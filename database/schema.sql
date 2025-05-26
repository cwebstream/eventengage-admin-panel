-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS eventmanager;
USE eventmanager;

-- Create events table
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    welcome_message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add indexes
CREATE INDEX idx_events_created_at ON events(created_at);
CREATE INDEX idx_events_title ON events(title);

-- Insert some sample data
INSERT INTO events (title, description, welcome_message) VALUES
('Annual Tech Conference 2024', 'Join us for our annual technology conference featuring industry leaders and innovative workshops.', 'Welcome to the biggest tech event of the year!'),
('Web Development Workshop', 'Learn the latest web development technologies and best practices.', 'Ready to become a web development expert?'),
('Digital Marketing Summit', 'Explore the future of digital marketing with industry experts.', 'Welcome to the Digital Marketing Summit!');
