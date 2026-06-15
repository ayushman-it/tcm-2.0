/**
 * skeleton.js – Universal Skeleton Loader
 * The Code Munk
 *
 * Strategy:
 * 1. On DOMContentLoaded – inject skeleton HTML over real content
 * 2. On window load (or 800ms timeout, whichever first) – fade out skeletons, reveal real content
 */

(function () {
    'use strict';

    /* ─── helpers ─────────────────────────────────────── */
    const sk  = (cls, extra) => `<div class="sk ${cls} ${extra||''}"></div>`;
    const skH = (html) => html; // passthrough for composed blocks

    /* ─── skeleton templates ───────────────────────────── */
    const templates = {

        // Generic section header
        sectionHeader: () => `
            <div class="sk-section-header">
                ${sk('sk sk-badge sk-pill sk-sec-tag')}
                ${sk('sk sk-sec-h2')}
                ${sk('sk sk-sec-p')}
            </div>`,

        // Hero (index page)
        hero: () => `
            <div class="sk-hero-wrap">
                ${sk('sk sk-pill sk-hero-tag')}
                ${sk('sk sk-hero-h1-1')}
                ${sk('sk sk-hero-h1-2')}
                ${sk('sk sk-hero-p1')}
                ${sk('sk sk-hero-p2')}
                ${sk('sk sk-pill sk-hero-proof')}
                <div class="sk-hero-btns">
                    ${sk('sk sk-hero-btn-1')}
                    ${sk('sk sk-hero-btn-2')}
                </div>
                ${sk('sk sk-hero-stats')}
            </div>`,

        // Event card (index)
        eventCard: () => `
            <div class="sk-event-card">
                ${sk('sk sk-pill sk-event-badge')}
                ${sk('sk sk-event-title')}
                ${sk('sk sk-event-desc-1')}
                ${sk('sk sk-event-desc-2')}
                <div class="sk-event-seats">${Array(16).fill(sk('sk sk-event-seat')).join('')}</div>
                <div class="sk-event-meta">
                    ${sk('sk sk-event-meta-item')}
                    ${sk('sk sk-event-meta-item')}
                </div>
                ${sk('sk sk-event-link')}
            </div>`,

        // Course card (index)
        courseCard: () => `
            <div class="sk-course-card">
                ${sk('sk sk-course-icon')}
                ${sk('sk sk-course-price')}
                ${sk('sk sk-course-title')}
                ${sk('sk sk-course-desc')}
                ${sk('sk sk-course-link')}
            </div>`,

        // Review card (index / insights)
        reviewCard: () => `
            <div class="sk-review-card">
                ${sk('sk sk-pill sk-review-badge')}
                ${sk('sk sk-review-line-1')}
                ${sk('sk sk-review-line-2')}
                ${sk('sk sk-review-line-3')}
                <div class="sk-review-user">
                    ${sk('sk sk-circle sk-review-avatar')}
                    <div>
                        ${sk('sk sk-review-name')}
                        ${sk('sk sk-review-sub')}
                    </div>
                </div>
            </div>`,

        // Founder card (insights)
        founderCard: () => `
            <div class="sk-founder-card">
                <div class="sk-founder-banner">
                    ${sk('sk sk-circle sk-founder-avatar')}
                    ${sk('sk sk-pill sk-founder-badge')}
                </div>
                <div class="sk-founder-body">
                    ${sk('sk sk-founder-name')}
                    ${sk('sk sk-founder-title')}
                    ${sk('sk sk-founder-bio-1')}
                    ${sk('sk sk-founder-bio-2')}
                    ${sk('sk sk-founder-bio-3')}
                    <div class="sk-founder-skills">
                        ${Array(4).fill(sk('sk sk-founder-skill')).join('')}
                    </div>
                    <div class="sk-founder-links">
                        ${Array(3).fill(sk('sk sk-founder-link')).join('')}
                    </div>
                </div>
            </div>`,

        // Platform card (community)
        platformCard: () => `
            <div class="sk-platform-card">
                <div class="sk-platform-top">
                    ${sk('sk sk-platform-icon')}
                    ${sk('sk sk-pill sk-platform-badge')}
                </div>
                ${sk('sk sk-platform-title')}
                ${sk('sk sk-platform-line')}
                ${sk('sk sk-platform-line')}
                ${sk('sk sk-platform-perk')}
                ${sk('sk sk-platform-perk')}
                ${sk('sk sk-platform-perk')}
                ${sk('sk sk-pill sk-platform-btn')}
            </div>`,

        // Programs page event card
        pgEventCard: () => `
            <div class="sk-pg-event-card">
                <div class="sk-pg-top">
                    ${sk('sk sk-pill sk-pg-status')}
                    ${sk('sk sk-pill sk-pg-type')}
                </div>
                ${sk('sk sk-pg-cat')}
                ${sk('sk sk-pg-title')}
                ${sk('sk sk-pg-desc-1')}
                ${sk('sk sk-pg-desc-2')}
                <div class="sk-pg-meta">
                    ${sk('sk sk-pg-meta-i')}
                    ${sk('sk sk-pg-meta-i')}
                    ${sk('sk sk-pg-meta-i')}
                </div>
                ${sk('sk sk-pg-bar')}
                ${sk('sk sk-pg-btn')}
            </div>`,

        // Contact form
        contactForm: () => `
            <div class="sk-form-card">
                ${sk('sk sk-sec-tag')}
                ${sk('sk sk-founder-name')}
                ${sk('sk sk-founder-title')}
                <div class="sk-form-row">
                    <div style="display:flex;flex-direction:column;gap:8px">
                        ${sk('sk sk-form-label')} ${sk('sk sk-form-input')}
                    </div>
                    <div style="display:flex;flex-direction:column;gap:8px">
                        ${sk('sk sk-form-label')} ${sk('sk sk-form-input')}
                    </div>
                </div>
                <div style="display:flex;flex-direction:column;gap:8px">
                    ${sk('sk sk-form-label')} ${sk('sk sk-form-input')}
                </div>
                <div style="display:flex;flex-direction:column;gap:8px">
                    ${sk('sk sk-form-label')} ${sk('sk sk-form-input')}
                </div>
                ${sk('sk sk-form-submit')}
            </div>`,

        // Contact card (right column)
        contactCard: () => `
            <div class="sk-contact-card">
                ${sk('sk sk-cc-icon')}
                <div style="flex:1;display:flex;flex-direction:column;gap:0">
                    ${sk('sk sk-cc-title')}
                    ${sk('sk sk-cc-sub')}
                    ${sk('sk sk-cc-value')}
                </div>
            </div>`,

        // Timeline item (insights)
        timelineItem: () => `
            <div class="sk-tl-item">
                ${sk('sk sk-tl-year')}
                <div class="sk-tl-content">
                    ${sk('sk sk-tl-h4')}
                    ${sk('sk sk-tl-p1')}
                    ${sk('sk sk-tl-p2')}
                </div>
            </div>`,

        // Why card (event-details)
        whyCard: () => `
            <div class="sk-why-card">
                ${sk('sk sk-why-icon')}
                <div class="sk-why-content">
                    ${sk('sk sk-why-title')}
                    ${sk('sk sk-why-text-1')}
                    ${sk('sk sk-why-text-2')}
                </div>
            </div>`,
    };

    /* ─── page detector ────────────────────────────────── */
    const page = (() => {
        const p = location.pathname.split('/').pop().replace('.html','') || 'index';
        return p;
    })();

    /* ─── inject page-loader overlay ───────────────────── */
    function injectPageLoader() {
        const loader = document.createElement('div');
        loader.className = 'page-loader';
        loader.id = 'pageLoader';
        loader.innerHTML = `
            <div class="page-loader-inner">
                <img src="https://thecodemunk.in/tcm/tcm.png" alt="TCM" class="page-loader-logo">
                <div class="page-loader-bar-wrap">
                    <div class="page-loader-bar"></div>
                </div>
            </div>`;
        document.body.prepend(loader);
    }

    /* ─── hide page loader ──────────────────────────────── */
    function hideLoader() {
        const loader = document.getElementById('pageLoader');
        if (loader) loader.classList.add('hidden');
    }

    /* ─── replace element with skeleton ────────────────── */
    function skeletonize(el, templateFn, count) {
        if (!el) return;
        // store real content
        el.dataset.realHtml = el.innerHTML;
        el.dataset.realDisplay = el.style.display || '';
        // inject skeletons
        const html = Array.from({ length: count || 1 }, templateFn).join('');
        el.innerHTML = html;
        el.classList.add('sk-active');
    }

    /* ─── restore real content ──────────────────────────── */
    function restore(el) {
        if (!el || !el.dataset.realHtml) return;
        el.innerHTML = el.dataset.realHtml;
        el.classList.remove('sk-active');
    }

    /* ─── page-specific skeleton injection ─────────────── */
    function applySkeletons() {

        /* ---- INDEX ---- */
        if (page === 'index' || page === '') {
            const heroInner = document.querySelector('.hero .col-xl-8, .hero .col-lg-9');
            if (heroInner) skeletonize(heroInner, templates.hero);

            document.querySelectorAll('.event-card:not(.hidden-event)').forEach(c => skeletonize(c, templates.eventCard));
            document.querySelectorAll('.course-card').forEach((c, i) => { if (i < 6) skeletonize(c, templates.courseCard); });
        }

        /* ---- PROGRAMS ---- */
        if (page === 'programs') {
            document.querySelectorAll('.pg-event-card').forEach(c => skeletonize(c, templates.pgEventCard));
        }

        /* ---- EVENT DETAILS ---- */
        if (page === 'event-details') {
            document.querySelectorAll('.why-card').forEach(c => skeletonize(c, templates.whyCard));
        }

        /* ---- COMMUNITY ---- */
        if (page === 'community') {
            document.querySelectorAll('.cm-platform-card').forEach(c => skeletonize(c, templates.platformCard));
        }

        /* ---- INSIGHTS ---- */
        if (page === 'insights') {
            document.querySelectorAll('.ins-founder-card').forEach(c => skeletonize(c, templates.founderCard));
            document.querySelectorAll('.ins-tl-item').forEach(c => skeletonize(c, templates.timelineItem));
        }

        /* ---- CONTACT ---- */
        if (page === 'contact') {
            const formCol = document.querySelector('.ct-form-col');
            if (formCol) skeletonize(formCol, templates.contactForm);
            document.querySelectorAll('.ct-contact-card').forEach(c => skeletonize(c, templates.contactCard));
        }

        /* ---- COURSE DETAILS ---- */
        if (page === 'course-details') {
            // Already rendered on server, just show loader
        }

        /* ── Reviews marquee – all pages ── */
        const marqueeWrappers = document.querySelectorAll('.marquee-wrapper:not(.sk-done)');
        marqueeWrappers.forEach(wrap => {
            // Just show shimmer over first visible row
            const track = wrap.querySelector('.marquee-track');
            if (!track) return;
            track.style.visibility = 'hidden';
            wrap.classList.add('sk-done');
        });
    }

    /* ─── restore everything ────────────────────────────── */
    function restoreAll() {
        // Restore skeletonized elements
        document.querySelectorAll('.sk-active').forEach(el => restore(el));

        // Restore marquee tracks
        document.querySelectorAll('.marquee-track').forEach(t => {
            t.style.visibility = '';
        });

        // Hide page loader
        hideLoader();
    }

    /* ─── init ──────────────────────────────────────────── */
    document.addEventListener('DOMContentLoaded', () => {
        injectPageLoader();
        applySkeletons();
    });

    // Restore on full load, max 1200ms wait
    let restored = false;
    function doRestore() {
        if (restored) return;
        restored = true;
        restoreAll();
    }

    window.addEventListener('load', doRestore);
    setTimeout(doRestore, 1200);

})();
