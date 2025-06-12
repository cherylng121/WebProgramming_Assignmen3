-- Database setup for Assignment 3 - Personal Portfolio with PHP + MySQL
-- Group BlaBlaBla

-- Create database (if not exists)
CREATE DATABASE IF NOT EXISTS portfolio_db;
USE portfolio_db;

-- Table for contact form submissions
CREATE TABLE IF NOT EXISTS contact_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('new', 'read', 'replied') DEFAULT 'new'
);

-- Table for guestbook entries
CREATE TABLE IF NOT EXISTS guestbook (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    post_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    approved BOOLEAN DEFAULT TRUE,
    ip_address VARCHAR(45) DEFAULT NULL
);

-- Insert sample data for testing
INSERT INTO guestbook (name, message, post_date) VALUES
('Alice Johnson', 'Great website! Love the design and functionality.', '2024-11-01 10:30:00'),
('Bob Smith', 'Very impressive work from the group. Keep it up!', '2024-11-02 14:15:00'),
('Charlie Brown', 'The contact form works perfectly. Nice job!', '2024-11-03 09:45:00'),
('Diana Prince', 'Amazing portfolio! The responsive design is excellent.', '2024-11-04 16:20:00'),
('Ethan Hunt', 'Professional looking site with great functionality.', '2024-11-05 11:10:00');

INSERT INTO contact_submissions (name, email, subject, message, submission_date) VALUES
('John Doe', 'john.doe@example.com', 'Website Inquiry', 'I am interested in learning more about your web development skills.', '2024-11-01 12:00:00'),
('Jane Smith', 'jane.smith@example.com', 'Collaboration Request', 'Would love to collaborate on a project with your team.', '