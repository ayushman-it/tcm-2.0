-- =====================================================================
-- The Code Munk — Full Setup + Seed (Run this in phpMyAdmin)
-- Adds seats_filled column + all seed data with ON DUPLICATE KEY
-- =====================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Add seats_filled to courses if it doesn't exist
ALTER TABLE courses
  ADD COLUMN IF NOT EXISTS seats_filled INT NOT NULL DEFAULT 0 AFTER seats_left;

-- Update seats_filled from existing seats_left data
UPDATE courses SET seats_filled = GREATEST(0, total_seats - seats_left)
WHERE seats_filled = 0 AND total_seats > 0;

-- ── Users ──────────────────────────────────────────────────────
INSERT INTO users (id, name, email, password_hash, role, status, email_verified, onboarded) VALUES
(1, 'TCM Admin', 'admin@thecodemunk.com',
 '$2y$12$iCPmgLf8zY/sA/Yo4eEfeugvqcj1nHcE3Ht6qK1GPS/AkIY6AVT4u',
 'admin', 'active', 1, 1),
(2, 'Aarav Sharma', 'student@thecodemunk.com',
 '$2y$12$tw.5NtxIsosbAzP/fMkLzOT0zHkFUIAzjNyOv4LKo0OSJvt7pmDUO',
 'student', 'active', 1, 1)
ON DUPLICATE KEY UPDATE
  name  = VALUES(name),
  role  = VALUES(role),
  status = VALUES(status);

INSERT INTO student_profiles (user_id, headline, bio, location, college, graduation_year,
  experience_level, goal, github_url, linkedin_url) VALUES
(2, 'Aspiring Full Stack Developer',
 'College student passionate about building web apps and learning new tech.',
 'Lucknow, India', 'Integral University', 2027, 'intermediate',
 'Land a frontend developer internship',
 'https://github.com/aarav', 'https://linkedin.com/in/aarav')
ON DUPLICATE KEY UPDATE
  headline = VALUES(headline), bio = VALUES(bio),
  location = VALUES(location), college = VALUES(college);

-- ── Categories ─────────────────────────────────────────────────
INSERT INTO categories (id, name, slug, description, icon, audience, sort_order) VALUES
(1, 'College Special',       'college-special',    'Job-ready programs built for college students', 'bi-mortarboard', 'college',    1),
(2, 'Beginners & Non-IT',    'beginners-non-it',   'Start from scratch, no prior experience needed','bi-laptop',     'beginners',  2),
(3, 'Working Professionals', 'working-professionals','Upskill and transition into tech',             'bi-briefcase',  'working',    3)
ON DUPLICATE KEY UPDATE
  name = VALUES(name), audience = VALUES(audience), sort_order = VALUES(sort_order);

-- ── Courses ─────────────────────────────────────────────────────
INSERT INTO courses (id, category_id, instructor_id, title, slug, subtitle, description,
  icon, level, duration, price, original_price, rating, reviews_count, students_count,
  total_seats, seats_left, seats_filled, certificate, schedule, starts_at,
  is_featured, is_bestseller, status) VALUES
(1,  1, 1, 'Full Stack Development',   'full-stack-development',
 'Frontend, Backend and Deployment with projects',
 'Master frontend, backend and deployment through live sessions, real projects and expert mentorship. Built specifically for college students who want job-ready skills.',
 'bi-code-slash', 'beginner', '3 Months', 4999.00, 7999.00, 4.9, 238, 1200, 24, 6, 18, 1, '7:00 PM - 8:30 PM', '2026-07-01', 1, 1, 'published'),
(2,  1, 1, 'Frontend Development',     'frontend-development',
 'HTML, CSS, JavaScript and React fundamentals',
 'Build beautiful, responsive interfaces with HTML, CSS, JavaScript and React.',
 'bi-window', 'beginner', '2 Months', 3999.00, 5999.00, 4.8, 156, 820, 24, 12, 12, 1, '8:00 PM - 9:30 PM', '2026-07-05', 1, 0, 'published'),
(3,  1, 1, 'Python Development',       'python-development',
 'Programming, automation and real projects',
 'Learn Python programming, automation and build real-world projects.',
 'bi-filetype-py', 'beginner', '2 Months', 3499.00, 4999.00, 4.7, 98, 540, 24, 18, 6, 1, '6:00 PM - 7:30 PM', '2026-07-10', 0, 0, 'published'),
(4,  1, 1, 'Database Systems',         'database-systems',
 'SQL, MySQL and database design concepts',
 'Understand relational databases, SQL and design scalable schemas.',
 'bi-database', 'beginner', '1 Month', 2999.00, 4999.00, 4.6, 64, 310, 24, 20, 4, 1, '7:30 PM - 9:00 PM', '2026-07-12', 0, 0, 'published'),
(5,  1, 1, 'DSA & Problem Solving',    'dsa-problem-solving',
 'Coding logic and interview preparation',
 'Crack coding interviews with data structures, algorithms and problem solving.',
 'bi-cpu', 'intermediate', '3 Months', 4499.00, 6999.00, 4.9, 142, 700, 24, 9, 15, 1, '9:00 AM - 10:30 AM', '2026-07-15', 0, 1, 'published'),
(6,  1, 1, 'Cloud Fundamentals',       'cloud-fundamentals',
 'Hosting, deployment and cloud basics',
 'Learn hosting, deployment and the fundamentals of cloud computing.',
 'bi-cloud', 'beginner', '1 Month', 2999.00, 4999.00, 4.5, 40, 210, 24, 22, 2, 1, '8:00 PM - 9:00 PM', '2026-07-18', 0, 0, 'published')
ON DUPLICATE KEY UPDATE
  title = VALUES(title), seats_filled = VALUES(seats_filled), status = VALUES(status),
  price = VALUES(price), seats_left = VALUES(seats_left);

INSERT INTO courses (id, category_id, instructor_id, title, slug, subtitle, description,
  icon, level, duration, price, original_price, rating, reviews_count, students_count,
  total_seats, seats_left, seats_filled, certificate, schedule, starts_at,
  is_featured, is_bestseller, status) VALUES
(7,  2, 1, 'Computer Basics',          'computer-basics',
 'Start your digital journey from scratch',
 'Everything you need to become comfortable with computers and the web.',
 'bi-laptop', 'beginner', '1 Month', 1999.00, 3999.00, 4.4, 30, 180, 30, 25, 5, 1, '5:00 PM - 6:00 PM', '2026-07-20', 0, 0, 'published'),
(8,  2, 1, 'Web Development Basics',   'web-development-basics',
 'Learn website creation without confusion',
 'Create your first websites step by step, no confusion.',
 'bi-globe', 'beginner', '6 Weeks', 2499.00, 4999.00, 4.6, 52, 260, 30, 19, 11, 1, '6:00 PM - 7:00 PM', '2026-07-22', 0, 0, 'published'),
(9,  2, 1, 'AI Tools Mastery',         'ai-tools-mastery',
 'Use AI tools for productivity and work',
 'Boost productivity using modern AI tools at work and study.',
 'bi-robot', 'beginner', '4 Weeks', 2999.00, 5999.00, 4.7, 71, 410, 40, 31, 9, 1, '7:00 PM - 8:00 PM', '2026-07-25', 1, 0, 'published'),
(10, 3, 1, 'Career Transition Program','career-transition-program',
 'Move into tech with structured guidance',
 'A structured path for professionals switching into a tech career.',
 'bi-briefcase', 'intermediate', '4 Months', 5999.00, 9999.00, 4.8, 88, 320, 24, 7, 17, 1, '8:30 PM - 10:00 PM', '2026-08-01', 1, 1, 'published'),
(11, 3, 1, 'AI & Automation',          'ai-automation',
 'Automate repetitive work using AI',
 'Use AI and scripting to automate repetitive workflows.',
 'bi-lightning', 'intermediate', '6 Weeks', 4999.00, 7999.00, 4.7, 60, 240, 24, 14, 10, 1, '9:00 PM - 10:30 PM', '2026-08-03', 0, 0, 'published'),
(12, 3, 1, 'Data Analytics',           'data-analytics',
 'Learn reporting, dashboards and insights',
 'Turn raw data into reports, dashboards and actionable insights.',
 'bi-graph-up', 'intermediate', '3 Months', 4499.00, 6999.00, 4.6, 75, 300, 24, 11, 13, 1, '8:00 PM - 9:30 PM', '2026-08-05', 0, 0, 'published')
ON DUPLICATE KEY UPDATE
  title = VALUES(title), seats_filled = VALUES(seats_filled), status = VALUES(status),
  price = VALUES(price), seats_left = VALUES(seats_left);

-- ── Course Modules (Full Stack) ─────────────────────────────────
INSERT INTO course_modules (id, course_id, title, summary, position) VALUES
(1, 1, 'Introduction to Web Development',  '6 sessions · 9 hours · 1 project',   1),
(2, 1, 'Advanced CSS & Responsive Design',  '8 sessions · 12 hours · 1 project',  2),
(3, 1, 'JavaScript Fundamentals',           '10 sessions · 15 hours · 1 project', 3),
(4, 1, 'React.js – Modern Frontend',        '12 sessions · 18 hours · 2 projects',4),
(5, 1, 'Node.js & Express – Backend',       '10 sessions · 15 hours · 1 project', 5),
(6, 1, 'Databases – MySQL & MongoDB',        '8 sessions · 12 hours',             6)
ON DUPLICATE KEY UPDATE title = VALUES(title), summary = VALUES(summary);

-- ── Course Lessons ───────────────────────────────────────────────
INSERT IGNORE INTO course_lessons (module_id, title, type, position, is_preview) VALUES
(1, 'How the web works – Browsers, Servers, HTTP',  'live',    1, 1),
(1, 'Setting up your dev environment (VS Code)',     'live',    2, 0),
(1, 'Introduction to HTML – Structure & Semantics', 'live',    3, 0),
(1, 'HTML Forms, Tables & Media',                   'live',    4, 0),
(1, 'Introduction to CSS – Selectors, Box Model',   'live',    5, 0),
(1, 'Project: Build a Personal Profile Page',       'project', 6, 0),
(2, 'Flexbox – Layout & Alignment',                 'live',    1, 0),
(2, 'CSS Grid – Complex Layouts',                   'live',    2, 0),
(2, 'Responsive Design & Media Queries',            'live',    3, 0),
(2, 'CSS Animations & Transitions',                 'live',    4, 0),
(2, 'Bootstrap 5 – Grid, Components, Utilities',    'live',    5, 0),
(2, 'Project: Clone a Real Website Layout',         'project', 6, 0),
(3, 'Variables, Data Types & Operators',            'live',    1, 1),
(3, 'Control Flow – if/else, loops, switch',        'live',    2, 0),
(3, 'Functions, Scope & Closures',                  'live',    3, 0),
(3, 'Arrays & Array Methods',                       'live',    4, 0),
(3, 'DOM Manipulation & Events',                    'live',    5, 0),
(3, 'Async JS – Promises, Async/Await',             'live',    6, 0),
(3, 'Project: Interactive Quiz App',                'project', 7, 0),
(4, 'Introduction to React & JSX',                  'live',    1, 0),
(4, 'Components, Props & State',                    'live',    2, 0),
(4, 'React Hooks – useState, useEffect',            'live',    3, 0),
(4, 'React Router – Navigation & Pages',            'live',    4, 0),
(4, 'API Integration in React',                     'live',    5, 0),
(4, 'Project: E-Commerce Product Listing App',      'project', 6, 0),
(5, 'Introduction to Node.js & NPM',                'live',    1, 0),
(5, 'Building a Server with Express.js',            'live',    2, 0),
(5, 'REST API Design – Routes, Controllers',        'live',    3, 0),
(5, 'Authentication with JWT',                      'live',    4, 0),
(5, 'Project: REST API for a Task Manager',         'project', 5, 0),
(6, 'MySQL – Tables, Queries, Joins',               'live',    1, 0),
(6, 'Node.js + MySQL Integration',                  'live',    2, 0),
(6, 'MongoDB – Collections, Documents, CRUD',       'live',    3, 0),
(6, 'Mongoose ORM & Schema Design',                 'live',    4, 0);

-- ── Events ──────────────────────────────────────────────────────
INSERT INTO events (id, instructor_id, title, slug, description, category, type, status,
  price, event_date, event_time, mode, total_seats, seats_filled, recording_url) VALUES
(1, 1, 'Frontend Development Bootcamp',  'frontend-development-bootcamp',
 'Learn HTML, CSS and JavaScript through practical projects and expert mentorship.',
 'frontend', 'free', 'ongoing', 0.00, '2026-06-15', '19:00:00', 'online', 16, 12, NULL),
(2, 1, 'React Masterclass',              'react-masterclass',
 'Build modern applications using React and industry best practices.',
 'frontend', 'paid', 'ongoing', 499.00, '2026-06-18', '20:00:00', 'online', 16, 10, NULL),
(3, 1, 'Python Fundamentals Workshop',   'python-fundamentals-workshop',
 'Learn Python from basics to automation through practical examples.',
 'python', 'free', 'upcoming', 0.00, '2026-06-22', '18:00:00', 'online', 16, 6, NULL),
(4, 1, 'Node.js & Express Backend Bootcamp', 'nodejs-express-backend-bootcamp',
 'Build RESTful APIs, connect databases and handle auth with Node.js.',
 'backend', 'paid', 'upcoming', 699.00, '2026-06-25', '19:30:00', 'online', 16, 4, NULL),
(5, 1, 'DSA Crash Course for Interviews','dsa-crash-course-interviews',
 'Arrays, strings, sorting and searching — everything you need to crack coding rounds.',
 'dsa', 'free', 'upcoming', 0.00, '2026-06-28', '09:00:00', 'online', 16, 2, NULL),
(6, 1, 'Resume & LinkedIn Masterclass',  'resume-linkedin-masterclass',
 'Build a resume that gets noticed and a LinkedIn profile that attracts recruiters.',
 'career', 'paid', 'upcoming', 299.00, '2026-07-01', '18:30:00', 'online', 16, 8, NULL),
(7, 1, 'HTML & CSS: Zero to Hero',       'html-css-zero-to-hero',
 'A 3-hour live session that took absolute beginners from nothing to a working webpage.',
 'frontend', 'free', 'past', 0.00, '2026-06-05', '19:00:00', 'online', 48, 48,
 'https://youtu.be/demo-recording'),
(8, 1, 'Python for Automation Bootcamp', 'python-for-automation-bootcamp',
 'Automate boring tasks using Python — files, emails, web scraping and more.',
 'python', 'paid', 'past', 599.00, '2026-06-01', '18:00:00', 'online', 32, 32,
 'https://youtu.be/demo-recording'),
(9, 1, 'MySQL Database Design',          'mysql-database-design',
 'Schema design, normalization, joins and queries — everything for backend devs.',
 'backend', 'paid', 'past', 499.00, '2026-05-20', '18:30:00', 'online', 27, 27,
 'https://youtu.be/demo-recording')
ON DUPLICATE KEY UPDATE
  title = VALUES(title), status = VALUES(status),
  seats_filled = VALUES(seats_filled), recording_url = VALUES(recording_url);

-- ── Programs ────────────────────────────────────────────────────
INSERT INTO programs (id, title, slug, subtitle, description, type, icon, level, mode,
  duration, schedule, price, original_price, total_seats, seats_left, highlights,
  start_date, end_date, is_featured, status) VALUES
(1, 'TCM Live Classes — Full Stack', 'tcm-live-classes-full-stack',
 'Daily live online classes with mentors',
 'Learn full stack development through daily live online classes, code-alongs and weekly project reviews.',
 'live_classes', 'bi-broadcast', 'beginner', 'online', '4 Months',
 'Mon–Fri, 7:00–8:30 PM', 14999.00, 24999.00, 40, 12,
 'Daily live sessions\n10+ real projects\n1:1 mentorship\nDoubt support on WhatsApp\nCompletion certificate',
 '2026-07-01', '2026-10-31', 1, 'published'),
(2, 'TCM Learning Track — Frontend', 'tcm-learning-track-frontend',
 'Self-paced track with live doubt sessions',
 'A structured self-paced learning track combining recorded courses with weekly live doubt-clearing sessions.',
 'learning_track', 'bi-mortarboard', 'beginner', 'hybrid', '3 Months',
 'Weekly live doubts (Sat)', 7999.00, 12999.00, 100, 64,
 'Recorded course library\nWeekly live doubts\nProject feedback\nCertificate',
 '2026-07-05', '2026-10-05', 1, 'published'),
(3, 'Summer Campus 2026', 'summer-campus-2026',
 '6-week intensive on-campus + online bootcamp',
 'An intensive summer program for college students: build, ship and present a capstone project in 6 weeks.',
 'summer_campus', 'bi-sun', 'beginner', 'hybrid', '6 Weeks',
 'Mon–Sat, 10 AM–1 PM', 9999.00, 15999.00, 60, 22,
 'Capstone project\nTeam-based learning\nIndustry guest talks\nCertificate + LOR',
 '2026-05-15', '2026-06-30', 1, 'published'),
(4, 'TCM Internship Program', 'tcm-internship-program',
 'Work on real client projects with Cuboidsoft',
 'A guided internship where you contribute to real projects in partnership with Cuboidsoft.',
 'internship', 'bi-briefcase', 'intermediate', 'online', '3 Months',
 'Flexible, 20 hrs/week', 0.00, NULL, 20, 5,
 'Real client projects\nMentor guidance\nExperience certificate\nLetter of recommendation\nStipend for top performers',
 '2026-07-15', '2026-10-15', 1, 'published')
ON DUPLICATE KEY UPDATE
  title = VALUES(title), status = VALUES(status), seats_left = VALUES(seats_left);

INSERT IGNORE INTO program_courses (program_id, course_id) VALUES
(1,1),(1,2),(1,4),(2,2),(2,3);

-- ── Live Sessions ───────────────────────────────────────────────
INSERT IGNORE INTO live_sessions (program_id, course_id, title, description, host,
  session_date, start_time, end_time, meeting_url, status) VALUES
(1, NULL, 'Kickoff: Web Dev Foundations', 'Orientation and how the web works.',
 'Ayushman', '2026-07-01', '19:00:00', '20:30:00', 'https://meet.google.com/demo-tcm', 'scheduled'),
(1, NULL, 'HTML & CSS Live Code-along', 'Build a landing page together.',
 'Ayushman', '2026-07-02', '19:00:00', '20:30:00', 'https://meet.google.com/demo-tcm', 'scheduled'),
(2, NULL, 'Weekly Doubt Session', 'Bring your blockers, we solve them live.',
 'TCM Mentor', '2026-07-11', '11:00:00', '12:00:00', 'https://meet.google.com/demo-tcm', 'scheduled');

-- ── Testimonials ────────────────────────────────────────────────
INSERT IGNORE INTO testimonials (name, role, content, rating, sort_order) VALUES
('Riya Verma', 'Frontend Developer @ Startup',
 'TCM helped me go from zero to building real projects. The live classes and mentorship made all the difference.',
 5, 1),
('Karan Singh', 'CS Student',
 'The DSA program got me interview-ready. I cleared two coding rounds within a month of finishing.',
 5, 2),
('Priya Nair', 'Career Switcher',
 'I moved from a non-IT job into tech thanks to the Career Transition Program. Highly recommend.',
 5, 3);

-- ── Blog Posts ──────────────────────────────────────────────────
INSERT IGNORE INTO posts (author_id, title, slug, excerpt, content, category, tags, status, published_at) VALUES
(1, 'How to Build Your First Full Stack Project', 'build-first-full-stack-project',
 'A practical roadmap to ship your first end-to-end web app.',
 'Building your first full stack project can feel overwhelming. Start small, pick a simple idea, and iterate.',
 'Tutorials', 'fullstack,beginners,projects', 'published', NOW()),
(1, '5 DSA Patterns That Show Up in Every Interview', 'dsa-patterns-interviews',
 'Master these patterns and most coding rounds become predictable.',
 'Sliding window, two pointers, fast & slow pointers, binary search and backtracking.',
 'Career', 'dsa,interview,career', 'published', NOW());

-- ── Settings ────────────────────────────────────────────────────
INSERT INTO settings (`key`, `value`) VALUES
('site_name',       'The Code Munk'),
('site_tagline',    'Learn Coding With Confidence'),
('contact_email',   'hello@thecodemunk.com'),
('contact_phone',   '+91-00000-00000'),
('students_count',  '1200'),
('courses_count',   '12'),
('currency',        'INR'),
('whatsapp_number', '919999999999'),
('whatsapp_message','Hi The Code Munk! I am interested in')
ON DUPLICATE KEY UPDATE `value` = VALUES(`value`);

SET FOREIGN_KEY_CHECKS = 1;
-- =====================================================================
-- Done! All seed data inserted.
-- =====================================================================

-- =====================================================================
-- Lead Forms (shareable capture pages)
-- =====================================================================
CREATE TABLE IF NOT EXISTS lead_forms (
    id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    title        VARCHAR(200)    NOT NULL,
    slug         VARCHAR(220)    NOT NULL,
    description  TEXT            DEFAULT NULL,
    -- context: event / course / general
    context_type ENUM('event','course','program','general') NOT NULL DEFAULT 'general',
    context_id   BIGINT UNSIGNED DEFAULT NULL,
    context_title VARCHAR(220)   DEFAULT NULL,
    -- JSON array of field configs: [{name,label,type,required,options}]
    fields_json  TEXT            DEFAULT NULL,
    -- CTA text on submit button
    cta_text     VARCHAR(100)    NOT NULL DEFAULT 'Submit',
    -- Where to route captured leads
    whatsapp_redirect TINYINT(1) NOT NULL DEFAULT 1,
    thank_you_message VARCHAR(255) DEFAULT 'Thank you! We will be in touch shortly.',
    status       ENUM('active','inactive') NOT NULL DEFAULT 'active',
    views        INT             NOT NULL DEFAULT 0,
    submissions  INT             NOT NULL DEFAULT 0,
    created_by   BIGINT UNSIGNED DEFAULT NULL,
    created_at   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_lead_form_slug (slug),
    KEY idx_lf_status (status),
    CONSTRAINT fk_lf_creator FOREIGN KEY (created_by) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
