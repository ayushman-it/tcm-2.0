/*
 * The Code Munk — front-end integration for the static marketing site.
 * Adds backend functionality WITHOUT changing the existing design:
 *   - auth-aware "Join Now" (opens the designer's modal, or links to the
 *     dashboard when already signed in)
 *   - wires the auth modal's password Login/Register to the real PHP backend
 *   - contact form submission to the API
 *   - WhatsApp click-to-chat on any [data-wa] element
 *   - live hydration of course/event listings from the API, reusing the
 *     original card markup (containers marked with data-tcm-list)
 * Every hook is defensive and no-ops if its target isn't present.
 */
(function () {
    "use strict";

    var BASE = (window.TCM_BASE || "").replace(/\/$/, "");
    var api = function (p) { return BASE + p; };
    var ME = { authenticated: false, dashboard_url: null, whatsapp_number: "", csrf_token: "" };

    function ready(fn) {
        if (document.readyState !== "loading") fn();
        else document.addEventListener("DOMContentLoaded", fn);
    }
    function $(id) { return document.getElementById(id); }
    function esc(s) {
        return String(s == null ? "" : s).replace(/[&<>"']/g, function (c) {
            return { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c];
        });
    }
    function getJSON(url) {
        return fetch(url, { headers: { Accept: "application/json" }, credentials: "same-origin" })
            .then(function (r) { return r.json(); });
    }
    function postJSON(url, data) {
        return fetch(url, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
                "X-CSRF-Token": ME.csrf_token || "",
            },
            credentials: "same-origin",
            body: JSON.stringify(data),
        }).then(function (r) { return r.json().catch(function () { return { success: false, message: "Unexpected response." }; }); });
    }

    /* ---- formatting helpers ------------------------------------------- */
    var MONTHS = ["January","February","March","April","May","June","July","August","September","October","November","December"];
    function fmtDate(d) {
        if (!d) return "TBA";
        var p = d.split("-"); if (p.length !== 3) return d;
        return parseInt(p[2], 10) + " " + MONTHS[parseInt(p[1], 10) - 1] + " " + p[0];
    }
    function fmtTime(t) {
        if (!t) return "";
        var p = t.split(":"); var h = parseInt(p[0], 10); var m = p[1] || "00";
        var ap = h >= 12 ? "PM" : "AM"; var hh = h % 12 || 12;
        return (hh < 10 ? "0" + hh : hh) + ":" + m + " " + ap;
    }
    function money(n) { return "\u20B9" + Number(n || 0).toLocaleString("en-IN"); }
    var CAT = { frontend: "Frontend", backend: "Backend", python: "Python", dsa: "DSA", career: "Career", general: "General" };
    function catLabel(c) { return CAT[c] || (c ? c.charAt(0).toUpperCase() + c.slice(1) : ""); }

    /* ================= AUTH STATE + NAV ================================ */
    function applyAuthState() {
        if (!ME.authenticated || !ME.dashboard_url) return;
        // When signed in, the "Join Now" button becomes a Dashboard link.
        document.querySelectorAll(".join-btn").forEach(function (el) {
            var clone = el.cloneNode(true);          // strips the modal click handler
            clone.textContent = "Dashboard";
            clone.setAttribute("href", api(ME.dashboard_url));
            if (el.parentNode) el.parentNode.replaceChild(clone, el);
        });
    }

    /* ================= AUTH MODAL -> REAL BACKEND ====================== */
    function modalNotice(msg, ok) {
        var view = document.querySelector(".auth-view.active") || document.querySelector(".auth-modal-body");
        if (!view) { alert(msg); return; }
        var box = view.querySelector(".tcm-auth-msg");
        if (!box) {
            box = document.createElement("div");
            box.className = "tcm-auth-msg";
            box.style.cssText = "margin:10px 0;padding:10px 12px;border-radius:10px;font-size:.85rem;";
            view.insertBefore(box, view.firstChild);
        }
        box.style.background = ok ? "#e7faf4" : "#ffeaea";
        box.style.color = ok ? "#00a884" : "#e23";
        box.textContent = msg;
    }
    function busy(btn, on) {
        if (!btn) return;
        btn.disabled = on;
        btn.classList.toggle("loading", on);
    }
    // Replace a node to drop listeners added by auth.js, returning the fresh node.
    function rebind(id) {
        var el = $(id);
        if (!el) return null;
        var fresh = el.cloneNode(true);
        el.parentNode.replaceChild(fresh, el);
        return fresh;
    }
    function wireAuthModal() {
        if (!document.getElementById("authOverlay")) return; // modal not on page

        var loginBtn = rebind("loginSubmitBtn");
        if (loginBtn) {
            loginBtn.addEventListener("click", function (e) {
                e.preventDefault();
                var email = (($("landing-pw-email") || {}).value || ($("regEmail") || {}).value || "").trim();
                var pw = (($("loginPassword") || {}).value || "");
                if (!pw) { modalNotice("Please enter your password.", false); return; }
                busy(loginBtn, true);
                postJSON(api("/auth/login"), { email: email, password: pw })
                    .then(function (res) {
                        if (res.success && res.data && res.data.redirect) { window.location = res.data.redirect; }
                        else { modalNotice(res.message || "Login failed.", false); busy(loginBtn, false); }
                    })
                    .catch(function () { modalNotice("Network error. Try again.", false); busy(loginBtn, false); });
            });
        }

        var regBtn = rebind("registerBtn");
        if (regBtn) {
            regBtn.addEventListener("click", function (e) {
                e.preventDefault();
                var name = (($("regName") || {}).value || "").trim();
                var email = (($("regEmail") || {}).value || "").trim();
                var pw = (($("regPassword") || {}).value || "");
                if (!name) { modalNotice("Please enter your name.", false); return; }
                if (pw.length < 8) { modalNotice("Password must be at least 8 characters.", false); return; }
                busy(regBtn, true);
                postJSON(api("/auth/register"), { name: name, email: email, password: pw, password_confirmation: pw })
                    .then(function (res) {
                        if (res.success && res.data && res.data.redirect) { window.location = res.data.redirect; }
                        else {
                            var msg = res.message || "Registration failed.";
                            if (res.errors) { var k = Object.keys(res.errors)[0]; if (k) msg = res.errors[k][0]; }
                            modalNotice(msg, false); busy(regBtn, false);
                        }
                    })
                    .catch(function () { modalNotice("Network error. Try again.", false); busy(regBtn, false); });
            });
        }
    }

    // Mimic auth.js view switching so we can drive flows after rebinding.
    function showAuthView(name) {
        document.querySelectorAll(".auth-view").forEach(function (v) { v.classList.remove("active"); });
        var el = $("view-" + name);
        if (el) el.classList.add("active");
    }
    function collectOtp(scopeSel) {
        var inputs = document.querySelectorAll(scopeSel + " input");
        return Array.prototype.map.call(inputs, function (i) { return (i.value || "").trim(); }).join("");
    }

    function wireAuthExtras() {
        if (!$("authOverlay")) return;

        // OTP login: send code (let auth.js handle the view switch), then verify for real.
        var sendOtp = $("landingSendOtp");
        if (sendOtp) {
            sendOtp.addEventListener("click", function () {
                var email = (($("landing-email") || {}).value || "").trim();
                if (!email) return;
                postJSON(api("/auth/otp/request"), { email: email })
                    .then(function (res) { if (!res.success) modalNotice(res.message || "Could not send code.", false); })
                    .catch(function () {});
            });
        }
        var verifyBtn = rebind("verifyOtpBtn");
        if (verifyBtn) {
            verifyBtn.addEventListener("click", function (e) {
                e.preventDefault();
                var email = (($("landing-email") || {}).value || "").trim();
                var code = collectOtp("#view-email-otp .auth-otp-wrap") || collectOtp("#otpInputs");
                if (code.length < 6) { modalNotice("Enter the 6-digit code.", false); return; }
                busy(verifyBtn, true);
                postJSON(api("/auth/otp/verify"), { email: email, otp: code })
                    .then(function (res) {
                        if (res.success && res.data && res.data.redirect) { window.location = res.data.redirect; }
                        else { modalNotice(res.message || "Invalid code.", false); busy(verifyBtn, false); }
                    })
                    .catch(function () { modalNotice("Network error.", false); busy(verifyBtn, false); });
            });
        }

        // Forgot password: send reset code (auth.js switches view), then reset for real.
        var forgotSend = $("forgotSendOtp");
        if (forgotSend) {
            forgotSend.addEventListener("click", function () {
                var email = (($("forgotEmail") || {}).value || "").trim();
                if (!email) return;
                postJSON(api("/auth/password/request"), { email: email }).catch(function () {});
            });
        }
        var resetBtn = rebind("resetPasswordBtn");
        if (resetBtn) {
            resetBtn.addEventListener("click", function (e) {
                e.preventDefault();
                var email = (($("forgotEmail") || {}).value || "").trim();
                var code = collectOtp("#view-forgot-otp .auth-otp-wrap") || collectOtp("#forgotOtpInputs");
                var pw = (($("newPassword") || {}).value || "");
                var conf = (($("confirmPassword") || {}).value || "");
                if (code.length < 6) { modalNotice("Enter the 6-digit code.", false); return; }
                if (pw.length < 8) { modalNotice("Password must be at least 8 characters.", false); return; }
                if (pw !== conf) { modalNotice("Passwords do not match.", false); return; }
                busy(resetBtn, true);
                postJSON(api("/auth/password/reset"), { email: email, otp: code, password: pw })
                    .then(function (res) {
                        if (res.success && res.data && res.data.redirect) { window.location = res.data.redirect; }
                        else { modalNotice(res.message || "Could not reset password.", false); busy(resetBtn, false); }
                    })
                    .catch(function () { modalNotice("Network error.", false); busy(resetBtn, false); });
            });
        }
    }

    /* ================= WHATSAPP ======================================== */
    function waLink(message) {
        var num = (ME.whatsapp_number || "").replace(/\D+/g, "");
        return (num ? "https://wa.me/" + num : "https://wa.me/") + "?text=" + encodeURIComponent(message || "Hi The Code Munk!");
    }
    function wireWhatsApp() {
        document.querySelectorAll("[data-wa]").forEach(function (el) {
            el.addEventListener("click", function (e) { e.preventDefault(); window.open(waLink(el.getAttribute("data-wa")), "_blank"); });
        });
    }

    /* ================= CONTACT FORM ==================================== */
    function wireContactForm() {
        var form = $("contactForm");
        if (!form) return;
        form.addEventListener("submit", function (e) {
            e.preventDefault();
            var val = function (id) { var el = $(id); return el ? el.value.trim() : ""; };
            var payload = {
                name: (val("firstName") + " " + val("lastName")).trim() || val("name"),
                email: val("email"),
                phone: val("phone"),
                subject: val("topic") || val("subject"),
                message: val("message"),
            };
            var btn = form.querySelector('button[type="submit"], button');
            var label = btn ? btn.textContent : "";
            if (btn) { btn.disabled = true; btn.textContent = "Sending..."; }
            postJSON(api("/api/contact"), payload)
                .then(function (res) {
                    contactNotice(form, res.success, res.message || (res.success ? "Sent!" : "Something went wrong."));
                    if (res.success) form.reset();
                })
                .catch(function () { contactNotice(form, false, "Network error. Please try again."); })
                .finally(function () { if (btn) { btn.disabled = false; btn.textContent = label; } });
        });
    }
    function contactNotice(form, ok, msg) {
        var box = form.querySelector(".tcm-notice");
        if (!box) {
            box = document.createElement("div");
            box.className = "tcm-notice";
            box.style.cssText = "margin:14px 0;padding:12px 16px;border-radius:10px;font-size:.92rem;";
            form.prepend(box);
        }
        box.style.background = ok ? "#e7faf4" : "#ffeaea";
        box.style.color = ok ? "#00a884" : "#e23";
        box.textContent = msg;
    }

    /* ================= LISTING HYDRATION =============================== */
    // index.html course card
    function courseCardHome(c) {
        var price = '<div class="course-price">' + money(c.price) +
            (c.original_price ? " <span>" + money(c.original_price) + "</span>" : "") + "</div>";
        return '<div class="course-card">' +
            '<div class="course-icon"><i class="bi ' + esc(c.icon || "bi-journal-code") + '"></i></div>' +
            price +
            "<h3>" + esc(c.title) + "</h3>" +
            "<p>" + esc(c.subtitle || "") + "</p>" +
            '<a href="course-details.html?course=' + encodeURIComponent(c.slug) + '">Explore Program <i class="bi bi-arrow-right"></i></a>' +
            "</div>";
    }
    // index.html event card (with 16-dot seat visual)
    function eventCardHome(ev) {
        var total = parseInt(ev.total_seats, 10) || 16;
        var filled = parseInt(ev.seats_filled, 10) || 0;
        var dots = "";
        for (var i = 0; i < 16; i++) {
            var on = i < Math.round((filled / total) * 16);
            dots += on ? '<span class="filled"></span>' : "<span></span>";
        }
        var indicator = ev.status === "ongoing"
            ? '<div class="live-indicator"><span class="live-dot"></span> Live Now</div>' : "";
        return '<div class="event-card">' + indicator +
            "<h3>" + esc(ev.title) + "</h3>" +
            "<p>" + esc(ev.description || "") + "</p>" +
            '<div class="seat-visual">' + dots + "</div>" +
            '<div class="seat-count">' + filled + " / " + total + " Seats Filled</div>" +
            '<div class="event-meta">' +
                '<span><i class="bi bi-calendar-event"></i> ' + esc(fmtDate(ev.event_date)) + "</span>" +
                (ev.event_time ? '<span><i class="bi bi-clock"></i> ' + esc(fmtTime(ev.event_time)) + "</span>" : "") +
            "</div>" +
            '<a href="event-details.html?event=' + encodeURIComponent(ev.slug) + '" class="event-link">View Event \u2192</a>' +
            "</div>";
    }
    // programs.html event card
    function eventCardPg(ev) {
        var total = parseInt(ev.total_seats, 10) || 16;
        var filled = parseInt(ev.seats_filled, 10) || 0;
        var pct = total ? Math.round((filled / total) * 100) : 0;
        var statusBadge = ev.status === "ongoing"
            ? '<div class="pg-status-badge ongoing"><span class="pg-live-dot"></span> Live Now</div>'
            : ev.status === "upcoming"
                ? '<div class="pg-status-badge upcoming">Upcoming</div>'
                : '<div class="pg-status-badge past">Completed</div>';
        var seats = ev.status === "past"
            ? '<div class="pg-seats-wrap past-seats"><span><i class="bi bi-check-circle-fill"></i> Event Completed</span></div>'
            : '<div class="pg-seats-wrap"><div class="pg-seats-bar"><div class="pg-seats-fill" style="width:' + pct + '%"></div></div>' +
              "<span>" + filled + " / " + total + " seats filled</span></div>";
        var btn = ev.status === "past"
            ? '<a href="event-details.html?event=' + encodeURIComponent(ev.slug) + '" class="pg-card-btn secondary">View Recording <i class="bi bi-play-circle"></i></a>'
            : '<a href="event-details.html?event=' + encodeURIComponent(ev.slug) + '" class="pg-card-btn">View Event <i class="bi bi-arrow-right"></i></a>';
        return '<div class="pg-event-card" data-status="' + esc(ev.status) + '" data-type="' + esc(ev.type) +
            '" data-cat="' + esc(ev.category) + '" data-title="' + esc((ev.title || "").toLowerCase()) + '">' +
            '<div class="pg-card-top">' + statusBadge +
                '<div class="pg-type-badge ' + esc(ev.type) + '">' + (ev.type === "free" ? "Free" : "Paid") + "</div></div>" +
            '<div class="pg-card-cat">' + esc(catLabel(ev.category)) + "</div>" +
            "<h3>" + esc(ev.title) + "</h3>" +
            "<p>" + esc(ev.description || "") + "</p>" +
            '<div class="pg-card-meta">' +
                '<span><i class="bi bi-calendar-event"></i> ' + esc(fmtDate(ev.event_date)) + "</span>" +
                (ev.event_time ? '<span><i class="bi bi-clock"></i> ' + esc(fmtTime(ev.event_time)) + "</span>" : "") +
                '<span><i class="bi bi-laptop"></i> ' + esc((ev.mode || "online").charAt(0).toUpperCase() + (ev.mode || "online").slice(1)) + "</span>" +
            "</div>" + seats + btn + "</div>";
    }

    function hydrateOne(container) {
        var type = container.getAttribute("data-tcm-list");
        var tpl = container.getAttribute("data-tcm-tpl") || "home";
        var qs = [];
        if (container.getAttribute("data-audience")) qs.push("audience=" + encodeURIComponent(container.getAttribute("data-audience")));
        if (container.getAttribute("data-status")) qs.push("status=" + encodeURIComponent(container.getAttribute("data-status")));
        var endpoint = type === "courses" ? "/api/courses" : type === "programs" ? "/api/programs" : "/api/events";
        var url = api(endpoint + (qs.length ? "?" + qs.join("&") : ""));

        getJSON(url).then(function (res) {
            if (!res || !res.success || !Array.isArray(res.data)) return;
            var items = res.data;
            if (!items.length) return; // keep the designer's sample cards if API empty
            var html = items.map(function (item) {
                if (type === "courses") return courseCardHome(item);
                return tpl === "pg" ? eventCardPg(item) : eventCardHome(item);
            }).join("");
            container.innerHTML = html;

            // Refresh the programs.html results counter if present.
            var counter = document.getElementById("resultsCount");
            if (counter && tpl === "pg") {
                counter.textContent = items.length + " event" + (items.length !== 1 ? "s" : "") + " found";
            }
        }).catch(function () {});
    }
    function hydrateLists() {
        document.querySelectorAll("[data-tcm-list]").forEach(hydrateOne);
    }

    /* ================= DETAIL PAGE HYDRATION =========================== */
    function qs(sel, ctx) { return (ctx || document).querySelector(sel); }
    function qsa(sel, ctx) { return (ctx || document).querySelectorAll(sel); }

    function setTxt(sel, val, ctx) {
        var el = qs(sel, ctx);
        if (el) el.textContent = val;
    }

    /* ---------- course-details.html ---------- */
    function hydrateCourseDetail() {
        var params = new URLSearchParams(window.location.search);
        var slug = params.get("course");
        if (!slug) return;

        getJSON(api("/api/courses/" + encodeURIComponent(slug))).then(function (res) {
            if (!res || !res.success || !res.data) return;
            var c = res.data;

            // document title
            document.title = (c.title || "Course") + " \u2013 The Code Munk";

            // breadcrumb
            var bc = qs(".cd-breadcrumb span:last-child");
            if (bc) bc.textContent = c.title || "";

            // hero h1
            var h1 = qs(".cd-hero-content h1");
            if (h1) h1.textContent = c.title || "";

            // hero desc
            setTxt(".cd-hero-desc", c.description || "");

            // price block
            setTxt(".cd-price", c.price != null ? money(c.price) : "");
            setTxt(".cd-price-original", c.original_price ? money(c.original_price) : "");
            var disc = c.discount_percent != null
                ? c.discount_percent
                : (c.original_price && c.price && c.original_price > 0
                    ? Math.round((1 - c.price / c.original_price) * 100)
                    : 0);
            setTxt(".cd-discount-badge", disc ? disc + "% Off" : "");

            // seats bar
            var total = parseInt(c.total_seats, 10) || 0;
            var filled = parseInt(c.seats_filled, 10) || 0;
            var left = Math.max(0, total - filled);
            var pct = total > 0 ? ((filled / total) * 100).toFixed(0) : 0;
            var fill = qs(".cd-seats-fill");
            if (fill) fill.style.width = pct + "%";
            var seatP = qs(".cd-seats p");
            if (seatP) seatP.innerHTML = "<strong>" + left + " seats left</strong> out of " + total;

            // stats row — rating, students_count, duration, "Live"
            var stats = qsa(".cd-stat strong");
            if (stats[0]) stats[0].textContent = c.rating != null ? c.rating : "—";
            if (stats[1]) stats[1].textContent = c.students_count != null ? Number(c.students_count).toLocaleString("en-IN") + "+" : "—";
            if (stats[2]) stats[2].textContent = c.duration || "—";
            if (stats[3]) stats[3].textContent = "Live";

            // meta row divs
            var metas = qsa(".cd-meta-row div");
            if (metas[0]) metas[0].innerHTML = '<i class="bi bi-calendar-event"></i> Starts ' + (c.starts_at ? fmtDate(c.starts_at.split(" ")[0]) : "TBA");
            if (metas[1]) metas[1].innerHTML = '<i class="bi bi-clock"></i> ' + (c.schedule || "Schedule TBA");
            if (metas[2]) metas[2].innerHTML = '<i class="bi bi-translate"></i> ' + (c.language || "English");
            if (metas[3]) metas[3].innerHTML = '<i class="bi bi-patch-check-fill"></i> ' + ((parseInt(c.certificate, 10)) ? "Certificate Included" : "No Certificate");

            // category badge
            var badge = qs(".cd-badge-tag");
            if (badge && c.category_name) badge.innerHTML = '<i class="bi bi-mortarboard-fill"></i> ' + esc(c.category_name);

            // bestseller badge
            var pop = qs(".cd-badge-popular");
            if (pop) pop.style.display = (parseInt(c.is_bestseller, 10) === 1) ? "" : "none";

            // enroll buttons
            var enrollUrl = (c.slug ? BASE + "/student/courses/" + encodeURIComponent(c.slug) : "#");
            qsa(".cd-enroll-btn").forEach(function (btn) { btn.setAttribute("href", enrollUrl); });
            // also wire quick-action "Enroll" link
            qsa(".action-btn").forEach(function (btn) {
                if ((btn.textContent || "").trim().toLowerCase() === "enroll") {
                    btn.setAttribute("href", enrollUrl);
                }
            });

            // curriculum
            if (Array.isArray(c.curriculum) && c.curriculum.length) {
                var wrapper = qs(".cd-curriculum-wrapper");
                if (wrapper) {
                    var html = c.curriculum.map(function (mod, idx) {
                        var num = String(idx + 1).padStart(2, "0");
                        var lessons = Array.isArray(mod.lessons) ? mod.lessons : [];
                        var lessonHtml = lessons.map(function (l) {
                            var isProject = (l.type === "project");
                            return '<div class="cd-lesson' + (isProject ? " cd-project-lesson" : "") + '">' +
                                '<i class="bi ' + (isProject ? "bi-folder2-open" : "bi-play-circle") + '"></i> ' +
                                '<span>' + esc(l.title) + '</span>' +
                                '<small' + (isProject ? ' class="project-tag"' : "") + '>' + esc(l.type || "Live") + '</small>' +
                                '</div>';
                        }).join("");
                        return '<details class="cd-module"' + (idx === 0 ? " open" : "") + '>' +
                            '<summary class="cd-module-header">' +
                            '<div class="cd-module-left">' +
                            '<div class="cd-module-num">' + num + '</div>' +
                            '<div><h4>' + esc(mod.title) + '</h4>' +
                            '<span>' + lessons.length + ' sessions</span>' +
                            '</div></div>' +
                            '<i class="bi bi-chevron-down cd-module-arrow"></i>' +
                            '</summary>' +
                            '<div class="cd-module-body">' + lessonHtml + '</div>' +
                            '</details>';
                    }).join("");
                    wrapper.innerHTML = html;
                }
            }
        }).catch(function () {});
    }

    /* ---------- event-details.html ---------- */
    function hydrateEventDetail() {
        var params = new URLSearchParams(window.location.search);
        var slug = params.get("event");
        if (!slug) return;

        getJSON(api("/api/events")).then(function (res) {
            if (!res || !res.success || !Array.isArray(res.data)) return;
            var ev = null;
            for (var i = 0; i < res.data.length; i++) {
                if (res.data[i].slug === slug) { ev = res.data[i]; break; }
            }
            if (!ev) return;

            // document title
            document.title = (ev.title || "Event") + " \u2013 The Code Munk";

            // hero content
            var heroContent = qs(".hero-content");
            if (heroContent) {
                var heroH1 = qs("h1", heroContent);
                if (heroH1) heroH1.textContent = ev.title || "";
                var heroP = qs("p", heroContent);
                if (heroP) heroP.textContent = ev.description || "";
            }

            // hero meta divs
            var metaDivs = qsa(".hero-meta div");
            if (metaDivs[0]) metaDivs[0].innerHTML = '<i class="bi bi-calendar-event"></i> ' + fmtDate(ev.event_date);
            if (metaDivs[1]) metaDivs[1].innerHTML = '<i class="bi bi-clock"></i> ' + (ev.event_time ? fmtTime(ev.event_time) : "TBA");
            if (metaDivs[2]) metaDivs[2].innerHTML = '<i class="bi bi-laptop"></i> ' + (ev.mode ? ev.mode.charAt(0).toUpperCase() + ev.mode.slice(1) : "Online");

            // availability card
            var total = parseInt(ev.total_seats, 10) || 16;
            var filled = parseInt(ev.seats_filled, 10) || 0;
            var left = Math.max(0, total - filled);
            var pct = total > 0 ? Math.round((filled / total) * 100) : 0;

            var avH3 = qs(".availability-card h3");
            if (avH3) avH3.textContent = pct + "% Filled";

            // rebuild dots grid
            var grid = qs(".availability-grid");
            if (grid) {
                var gridDots = "";
                var dotTotal = Math.min(total, 32); // cap visual at 32
                var dotFilled = Math.round((filled / total) * dotTotal);
                for (var d = 0; d < dotTotal; d++) {
                    gridDots += '<span class="dot' + (d < dotFilled ? " filled" : "") + '"></span>';
                }
                grid.innerHTML = gridDots;
            }

            // stats strongs
            var avStats = qsa(".availability-stats strong");
            if (avStats[0]) avStats[0].textContent = filled;
            if (avStats[1]) avStats[1].textContent = left;

            // status badge
            var isFull = left <= 0 && ev.status !== "past";
            var statusTxt = ev.status === "past"
                ? "Event Completed"
                : (isFull ? "Event Full" : "Registration Open");
            var statusBadge = qs(".status-badge");
            if (statusBadge) {
                statusBadge.textContent = statusTxt;
                statusBadge.className = "status-badge" +
                    (ev.status === "past" ? " past" : (isFull ? " full" : ""));
            }

            // register buttons — all .btn-dark links
            qsa(".btn-dark").forEach(function (btn) {
                if (ev.status === "past") {
                    btn.textContent = "Event Ended";
                    btn.removeAttribute("href");
                    btn.style.opacity = "0.5";
                    btn.style.cursor = "default";
                } else if (isFull) {
                    btn.textContent = "Event Full";
                    btn.removeAttribute("href");
                    btn.style.opacity = "0.5";
                    btn.style.cursor = "default";
                } else if (ev.type === "free") {
                    btn.textContent = "Register Free";
                    btn.setAttribute("href", BASE + "/student/events/" + encodeURIComponent(ev.id) + "/join");
                } else {
                    var waMsg = "Hi! I want to register for the event: " + (ev.title || slug);
                    btn.textContent = "Register \u2013 " + money(ev.price || 0);
                    btn.setAttribute("href", waLink(waMsg));
                    btn.setAttribute("target", "_blank");
                }
            });

            // also wire action-card "Register" button
            qsa(".action-btn").forEach(function (btn) {
                var txt = (btn.textContent || "").trim().toLowerCase();
                if (txt === "register") {
                    if (ev.status === "past" || isFull) {
                        btn.style.opacity = "0.5";
                        btn.removeAttribute("href");
                    } else if (ev.type === "free") {
                        btn.setAttribute("href", BASE + "/student/events/" + encodeURIComponent(ev.id) + "/join");
                    } else {
                        btn.setAttribute("href", waLink("Hi! I want to register for: " + (ev.title || slug)));
                        btn.setAttribute("target", "_blank");
                    }
                }
            });
        }).catch(function () {});
    }

    function hydrateDetailPage() {
        // Detect which detail page we're on by URL path or page elements
        var path = window.location.pathname;
        var isCoursePage = path.indexOf("course-details") !== -1
            || document.querySelector("[data-course-detail]") !== null
            || (new URLSearchParams(window.location.search)).get("course") !== null;
        var isEventPage = path.indexOf("event-details") !== -1
            || document.querySelector("[data-event-detail]") !== null
            || (new URLSearchParams(window.location.search)).get("event") !== null;

        if (isCoursePage && !isEventPage) hydrateCourseDetail();
        if (isEventPage && !isCoursePage) hydrateEventDetail();
    }

    /* ================= BOOT ============================================ */
    ready(function () {
        wireContactForm();
        wireAuthModal();
        wireAuthExtras();
        getJSON(api("/api/me"))
            .then(function (res) { if (res && res.data) ME = Object.assign(ME, res.data); })
            .catch(function () {})
            .finally(function () {
                applyAuthState();
                wireWhatsApp();
                hydrateLists();
                hydrateDetailPage();
            });
    });
})();
