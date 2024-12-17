-- migrations/sql/20231210120001_create_default_admin.sql

-- Insert default admin user
INSERT INTO users (username, password_hash, role, status)
VALUES 
    ('admin', '$2y$10$Z4w0e80BkhjkxFi7uQHQNyltWqNcPdpOX5g5fgdl0Ubo6p1EqMUtY', 'admin', 'active');
