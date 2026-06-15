-- =====================================================================
-- The Code Munk (TCM) - Ed-Tech Platform Database Schema
-- Engine: MySQL 8 / MariaDB 10.4+  |  Charset: utf8mb4
-- =====================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- Users & authentication
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name            VARCHAR(150)    NOT NULL,
    email           VARCHAR(190)    NOT NULL,
    password_hash   VARCHAR(255)    NOT NULL,
    role            ENUM('admin','student') NOT NULL DEFAULT 'student',
    phone           VARCHAR(20)     DEFAULT NULL,
    avatar          VARCHAR(255)    DEFAULT NULL,
    status          ENUM('active','suspended','pending') NOT NULL DEFAULT 'active',
    email_verified  TINYINT(1)      NOT NULL DEFAULT 0,
    onboarded       TINYINT(1)      NOT NULL DEFAULT 0,
    last_login_at   DATETIME        DEFAULT NULL,
    created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_users_email (email),
    KEY idx_users_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Extended profile information for students (1:1 with users)
CREATE TABLE IF NOT EXISTS student_profiles (
    user_id         BIGINT UNSIGNED NOT NULL,
    headline        VARCHAR(160)    DEFAULT NULL,
    bio             TEXT            DEFAULT NULL,
    location        VARCHAR(120)    DEFAULT NULL,
    college         VARCHAR(160)    DEFAULT NULL,
    graduation_year SMALLINT        DEFAULT NULL,
    experience_level ENUM('beginner','intermediate','advanced') NOT NULL DEFAULT 'beginner',
    goal            VARCHAR(190)    DEFAULT NULL,
    github_url      VARCHAR(255)    DEFAULT NULL,
    linkedin_url    VARCHAR(255)    DEFAULT NULL,
    website_url     VARCHAR(255)    DEFAULT NULL,
    twitter_url     VARCHAR(255)    DEFAULT NULL,
    created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id),
    CONSTRAINT fk_profile_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Course catalog (CMS managed by admin)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS categories (
    id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name        VARCHAR(120)    NOT NULL,
    slug        VARCHAR(140)    NOT NULL,
    description VARCHAR(255)    DEFAULT NULL,
    icon        VARCHAR(60)     DEFAULT NULL,   -- bootstrap-icon name
    audience    ENUM('college','beginners','working','general') NOT NULL DEFAULT 'general',
    sort_order  INT             NOT NULL DEFAULT 0,
    status      ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_categories_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS courses (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    category_id     BIGINT UNSIGNED DEFAULT NULL,
    instructor_id   BIGINT UNSIGNED DEFAULT NULL,
    title           VARCHAR(180)    NOT NULL,
    slug            VARCHAR(200)    NOT NULL,
    subtitle        VARCHAR(255)    DEFAULT NULL,
    description     TEXT            DEFAULT NULL,
    icon            VARCHAR(60)     DEFAULT NULL,
    thumbnail       VARCHAR(255)    DEFAULT NULL,
    level           ENUM('beginner','intermediate','advanced') NOT NULL DEFAULT 'beginner',
    language        VARCHAR(60)     DEFAULT 'Hindi + English',
    duration        VARCHAR(60)     DEFAULT NULL,    -- e.g. "3 Months"
    price           DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
    original_price  DECIMAL(10,2)   DEFAULT NULL,
    rating          DECIMAL(2,1)    NOT NULL DEFAULT 0.0,
    reviews_count   INT             NOT NULL DEFAULT 0,
    students_count  INT             NOT NULL DEFAULT 0,
    total_seats     INT             NOT NULL DEFAULT 0,
    seats_left      INT             NOT NULL DEFAULT 0,
    certificate     TINYINT(1)      NOT NULL DEFAULT 1,
    schedule        VARCHAR(120)    DEFAULT NULL,    -- e.g. "7:00 PM - 8:30 PM"
    starts_at       DATE            DEFAULT NULL,
    is_featured     TINYINT(1)      NOT NULL DEFAULT 0,
    is_bestseller   TINYINT(1)      NOT NULL DEFAULT 0,
    status          ENUM('draft','published','archived') NOT NULL DEFAULT 'draft',
    created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_courses_slug (slug),
    KEY idx_courses_category (category_id),
    KEY idx_courses_status (status),
    CONSTRAINT fk_course_category FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE SET NULL,
    CONSTRAINT fk_course_instructor FOREIGN KEY (instructor_id) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS course_modules (
    id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    course_id   BIGINT UNSIGNED NOT NULL,
    title       VARCHAR(180)    NOT NULL,
    summary     VARCHAR(255)    DEFAULT NULL,
    position    INT             NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    KEY idx_modules_course (course_id),
    CONSTRAINT fk_module_course FOREIGN KEY (course_id) REFERENCES courses (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS course_lessons (
    id               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    module_id        BIGINT UNSIGNED NOT NULL,
    title            VARCHAR(220)    NOT NULL,
    type             ENUM('live','video','project','reading','quiz') NOT NULL DEFAULT 'live',
    duration_minutes INT             DEFAULT NULL,
    position         INT             NOT NULL DEFAULT 0,
    is_preview       TINYINT(1)      NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    KEY idx_lessons_module (module_id),
    CONSTRAINT fk_lesson_module FOREIGN KEY (module_id) REFERENCES course_modules (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS course_reviews (
    id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    course_id   BIGINT UNSIGNED NOT NULL,
    user_id     BIGINT UNSIGNED NOT NULL,
    rating      TINYINT         NOT NULL DEFAULT 5,
    comment     TEXT            DEFAULT NULL,
    status      ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    created_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_reviews_course (course_id),
    CONSTRAINT fk_review_course FOREIGN KEY (course_id) REFERENCES courses (id) ON DELETE CASCADE,
    CONSTRAINT fk_review_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Events (workshops, bootcamps, live sessions)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS events (
    id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    instructor_id BIGINT UNSIGNED DEFAULT NULL,
    title         VARCHAR(180)    NOT NULL,
    slug          VARCHAR(200)    NOT NULL,
    description   TEXT            DEFAULT NULL,
    category      ENUM('frontend','backend','python','dsa','career','general') NOT NULL DEFAULT 'general',
    type          ENUM('free','paid') NOT NULL DEFAULT 'free',
    status        ENUM('upcoming','ongoing','past') NOT NULL DEFAULT 'upcoming',
    price         DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
    event_date    DATE            DEFAULT NULL,
    event_time    TIME            DEFAULT NULL,
    mode          ENUM('online','offline') NOT NULL DEFAULT 'online',
    location      VARCHAR(190)    DEFAULT NULL,
    banner        VARCHAR(255)    DEFAULT NULL,
    total_seats   INT             NOT NULL DEFAULT 16,
    seats_filled  INT             NOT NULL DEFAULT 0,
    recording_url VARCHAR(255)    DEFAULT NULL,
    created_at    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_events_slug (slug),
    KEY idx_events_status (status),
    KEY idx_events_category (category),
    CONSTRAINT fk_event_instructor FOREIGN KEY (instructor_id) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS event_registrations (
    id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    event_id      BIGINT UNSIGNED NOT NULL,
    user_id       BIGINT UNSIGNED NOT NULL,
    status        ENUM('registered','attended','cancelled') NOT NULL DEFAULT 'registered',
    registered_at DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_event_user (event_id, user_id),
    CONSTRAINT fk_reg_event FOREIGN KEY (event_id) REFERENCES events (id) ON DELETE CASCADE,
    CONSTRAINT fk_reg_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Commerce: orders, enrollments, progress
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS orders (
    id             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id        BIGINT UNSIGNED NOT NULL,
    order_number   VARCHAR(40)     NOT NULL,
    item_type      ENUM('course','event') NOT NULL,
    item_id        BIGINT UNSIGNED NOT NULL,
    item_title     VARCHAR(200)    DEFAULT NULL,
    amount         DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
    currency       VARCHAR(8)      NOT NULL DEFAULT 'INR',
    status         ENUM('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',
    payment_method VARCHAR(40)     DEFAULT NULL,
    payment_ref    VARCHAR(120)    DEFAULT NULL,
    created_at     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_orders_number (order_number),
    KEY idx_orders_user (user_id),
    CONSTRAINT fk_order_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS enrollments (
    id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id      BIGINT UNSIGNED NOT NULL,
    course_id    BIGINT UNSIGNED NOT NULL,
    order_id     BIGINT UNSIGNED DEFAULT NULL,
    status       ENUM('active','completed','cancelled') NOT NULL DEFAULT 'active',
    progress     TINYINT         NOT NULL DEFAULT 0,   -- 0-100 percent
    enrolled_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    completed_at DATETIME        DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_enroll_user_course (user_id, course_id),
    KEY idx_enroll_course (course_id),
    CONSTRAINT fk_enroll_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT fk_enroll_course FOREIGN KEY (course_id) REFERENCES courses (id) ON DELETE CASCADE,
    CONSTRAINT fk_enroll_order FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS lesson_progress (
    id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id      BIGINT UNSIGNED NOT NULL,
    lesson_id    BIGINT UNSIGNED NOT NULL,
    completed    TINYINT(1)      NOT NULL DEFAULT 0,
    completed_at DATETIME        DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_progress_user_lesson (user_id, lesson_id),
    CONSTRAINT fk_progress_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT fk_progress_lesson FOREIGN KEY (lesson_id) REFERENCES course_lessons (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Student portfolio (grow & showcase)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS portfolio_projects (
    id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id     BIGINT UNSIGNED NOT NULL,
    title       VARCHAR(180)    NOT NULL,
    description TEXT            DEFAULT NULL,
    tech_stack  VARCHAR(255)    DEFAULT NULL,   -- comma separated
    repo_url    VARCHAR(255)    DEFAULT NULL,
    live_url    VARCHAR(255)    DEFAULT NULL,
    image       VARCHAR(255)    DEFAULT NULL,
    is_featured TINYINT(1)      NOT NULL DEFAULT 0,
    sort_order  INT             NOT NULL DEFAULT 0,
    created_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_pproj_user (user_id),
    CONSTRAINT fk_pproj_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS portfolio_skills (
    id       BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id  BIGINT UNSIGNED NOT NULL,
    name     VARCHAR(80)     NOT NULL,
    level    TINYINT         NOT NULL DEFAULT 50,  -- 0-100 proficiency
    PRIMARY KEY (id),
    KEY idx_pskill_user (user_id),
    CONSTRAINT fk_pskill_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS portfolio_achievements (
    id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id     BIGINT UNSIGNED NOT NULL,
    title       VARCHAR(180)    NOT NULL,
    issuer      VARCHAR(160)    DEFAULT NULL,
    description VARCHAR(255)    DEFAULT NULL,
    url         VARCHAR(255)    DEFAULT NULL,
    achieved_on DATE            DEFAULT NULL,
    PRIMARY KEY (id),
    KEY idx_pach_user (user_id),
    CONSTRAINT fk_pach_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS certificates (
    id                 BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id            BIGINT UNSIGNED NOT NULL,
    course_id          BIGINT UNSIGNED DEFAULT NULL,
    certificate_number VARCHAR(60)     NOT NULL,
    title              VARCHAR(190)    NOT NULL,
    url                VARCHAR(255)    DEFAULT NULL,
    issued_at          DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_cert_number (certificate_number),
    KEY idx_cert_user (user_id),
    CONSTRAINT fk_cert_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT fk_cert_course FOREIGN KEY (course_id) REFERENCES courses (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Content: insights/blog, testimonials, contact, newsletter, settings
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS posts (
    id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    author_id    BIGINT UNSIGNED DEFAULT NULL,
    title        VARCHAR(200)    NOT NULL,
    slug         VARCHAR(220)    NOT NULL,
    excerpt      VARCHAR(300)    DEFAULT NULL,
    content      LONGTEXT        DEFAULT NULL,
    cover_image  VARCHAR(255)    DEFAULT NULL,
    category     VARCHAR(80)     DEFAULT 'General',
    tags         VARCHAR(255)    DEFAULT NULL,
    status       ENUM('draft','published') NOT NULL DEFAULT 'draft',
    views        INT             NOT NULL DEFAULT 0,
    published_at DATETIME        DEFAULT NULL,
    created_at   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_posts_slug (slug),
    KEY idx_posts_status (status),
    CONSTRAINT fk_post_author FOREIGN KEY (author_id) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS testimonials (
    id         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name       VARCHAR(120)    NOT NULL,
    role       VARCHAR(120)    DEFAULT NULL,
    content    TEXT            NOT NULL,
    rating     TINYINT         NOT NULL DEFAULT 5,
    image      VARCHAR(255)    DEFAULT NULL,
    status     ENUM('active','inactive') NOT NULL DEFAULT 'active',
    sort_order INT             NOT NULL DEFAULT 0,
    created_at DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS contact_messages (
    id         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name       VARCHAR(120)    NOT NULL,
    email      VARCHAR(190)    NOT NULL,
    subject    VARCHAR(190)    DEFAULT NULL,
    message    TEXT            NOT NULL,
    status     ENUM('new','read','replied') NOT NULL DEFAULT 'new',
    created_at DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS newsletter_subscribers (
    id         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    email      VARCHAR(190)    NOT NULL,
    created_at DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_news_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS settings (
    `key`      VARCHAR(120) NOT NULL,
    `value`    TEXT         DEFAULT NULL,
    PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================================
-- Extension: Programs, Live Classes, Leads (WhatsApp-driven enrolment)
-- =====================================================================
SET FOREIGN_KEY_CHECKS = 0;

-- Programs: higher-level offerings (live class tracks, internships,
-- summer campus, bootcamps, bundles that may group courses + live sessions)
CREATE TABLE IF NOT EXISTS programs (
    id             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    title          VARCHAR(180)    NOT NULL,
    slug           VARCHAR(200)    NOT NULL,
    subtitle       VARCHAR(255)    DEFAULT NULL,
    description    TEXT            DEFAULT NULL,
    type           ENUM('live_classes','learning_track','internship','summer_campus','bootcamp','bundle') NOT NULL DEFAULT 'live_classes',
    icon           VARCHAR(60)     DEFAULT 'bi-stack',
    thumbnail      VARCHAR(255)    DEFAULT NULL,
    level          ENUM('beginner','intermediate','advanced') NOT NULL DEFAULT 'beginner',
    mode           ENUM('online','offline','hybrid') NOT NULL DEFAULT 'online',
    duration       VARCHAR(60)     DEFAULT NULL,
    schedule       VARCHAR(160)    DEFAULT NULL,
    price          DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
    original_price DECIMAL(10,2)   DEFAULT NULL,
    total_seats    INT             NOT NULL DEFAULT 0,
    seats_left     INT             NOT NULL DEFAULT 0,
    highlights     TEXT            DEFAULT NULL,   -- newline separated bullet points
    start_date     DATE            DEFAULT NULL,
    end_date       DATE            DEFAULT NULL,
    is_featured    TINYINT(1)      NOT NULL DEFAULT 0,
    status         ENUM('draft','published','archived') NOT NULL DEFAULT 'draft',
    created_at     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_programs_slug (slug),
    KEY idx_programs_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pivot: programs may bundle existing courses
CREATE TABLE IF NOT EXISTS program_courses (
    program_id BIGINT UNSIGNED NOT NULL,
    course_id  BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (program_id, course_id),
    CONSTRAINT fk_pc_program FOREIGN KEY (program_id) REFERENCES programs (id) ON DELETE CASCADE,
    CONSTRAINT fk_pc_course FOREIGN KEY (course_id) REFERENCES courses (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Live class sessions (can belong to a program or a course, or stand alone)
CREATE TABLE IF NOT EXISTS live_sessions (
    id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    program_id    BIGINT UNSIGNED DEFAULT NULL,
    course_id     BIGINT UNSIGNED DEFAULT NULL,
    title         VARCHAR(190)    NOT NULL,
    description   VARCHAR(255)    DEFAULT NULL,
    host          VARCHAR(120)    DEFAULT NULL,
    session_date  DATE            DEFAULT NULL,
    start_time    TIME            DEFAULT NULL,
    end_time      TIME            DEFAULT NULL,
    meeting_url   VARCHAR(255)    DEFAULT NULL,
    recording_url VARCHAR(255)    DEFAULT NULL,
    status        ENUM('scheduled','live','completed','cancelled') NOT NULL DEFAULT 'scheduled',
    created_at    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_ls_program (program_id),
    KEY idx_ls_course (course_id),
    CONSTRAINT fk_ls_program FOREIGN KEY (program_id) REFERENCES programs (id) ON DELETE CASCADE,
    CONSTRAINT fk_ls_course FOREIGN KEY (course_id) REFERENCES courses (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Program enrolments (confirmed by admin after a lead converts)
CREATE TABLE IF NOT EXISTS program_enrollments (
    id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id     BIGINT UNSIGNED NOT NULL,
    program_id  BIGINT UNSIGNED NOT NULL,
    status      ENUM('pending','active','completed','cancelled') NOT NULL DEFAULT 'active',
    enrolled_at DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_pe_user_program (user_id, program_id),
    CONSTRAINT fk_pe_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT fk_pe_program FOREIGN KEY (program_id) REFERENCES programs (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Leads: every enquiry / enrol-intent captured for follow-up over WhatsApp
CREATE TABLE IF NOT EXISTS leads (
    id             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id        BIGINT UNSIGNED DEFAULT NULL,
    name           VARCHAR(150)    NOT NULL,
    email          VARCHAR(190)    DEFAULT NULL,
    phone          VARCHAR(20)     DEFAULT NULL,
    interest_type  ENUM('course','event','program','general','contact') NOT NULL DEFAULT 'general',
    interest_id    BIGINT UNSIGNED DEFAULT NULL,
    interest_title VARCHAR(200)    DEFAULT NULL,
    message        TEXT            DEFAULT NULL,
    source         VARCHAR(80)     DEFAULT 'website',
    status         ENUM('new','contacted','converted','lost') NOT NULL DEFAULT 'new',
    whatsapp_sent  TINYINT(1)      NOT NULL DEFAULT 0,
    created_at     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_leads_status (status),
    KEY idx_leads_user (user_id),
    CONSTRAINT fk_lead_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================================
-- Extension: Internship applications (with resume upload)
-- =====================================================================
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS internship_applications (
    id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id      BIGINT UNSIGNED NOT NULL,
    program_id   BIGINT UNSIGNED NOT NULL,
    full_name    VARCHAR(150)    NOT NULL,
    email        VARCHAR(190)    NOT NULL,
    phone        VARCHAR(20)     DEFAULT NULL,
    college      VARCHAR(160)    DEFAULT NULL,
    skills       VARCHAR(255)    DEFAULT NULL,   -- comma separated
    why          TEXT            DEFAULT NULL,    -- motivation / cover note
    portfolio_url VARCHAR(255)   DEFAULT NULL,
    resume_file  VARCHAR(255)    DEFAULT NULL,    -- stored filename in storage/resumes
    status       ENUM('submitted','under_review','shortlisted','selected','rejected') NOT NULL DEFAULT 'submitted',
    notes        TEXT            DEFAULT NULL,     -- internal admin notes
    reviewed_at  DATETIME        DEFAULT NULL,
    created_at   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_appl_user_program (user_id, program_id),
    KEY idx_appl_status (status),
    KEY idx_appl_program (program_id),
    CONSTRAINT fk_appl_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT fk_appl_program FOREIGN KEY (program_id) REFERENCES programs (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
