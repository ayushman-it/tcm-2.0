-- Add Ayushman as admin user
-- Email: thecodemunk@gmail.com
-- Password: iijjzdxeamohxfwvt

INSERT INTO users (name, email, password_hash, role, status, email_verified, onboarded)
VALUES ('Ayushman', 'thecodemunk@gmail.com', '$2y$10$57fmQZJM3bVK1EUascl0buXag2f67wHQHecUkdVYsHqf6mmnpYTo.', 'admin', 'active', 1, 1)
ON DUPLICATE KEY UPDATE
    password_hash = VALUES(password_hash),
    role = 'admin',
    status = 'active',
    email_verified = 1,
    onboarded = 1;
