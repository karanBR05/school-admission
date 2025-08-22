-- Create database
CREATE DATABASE IF NOT EXISTS school_admission;
USE school_admission;

-- Create users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'verifier') NOT NULL,
    email VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create admissions table
CREATE TABLE admissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    serial_no VARCHAR(20) UNIQUE NOT NULL,
    student_name VARCHAR(100) NOT NULL,
    father_name VARCHAR(100) NOT NULL,
    mother_name VARCHAR(100) NOT NULL,
    dob DATE NOT NULL,
    parent_mobile VARCHAR(10) NOT NULL,
    parent_email VARCHAR(100) NOT NULL,
    student_photo VARCHAR(255) NOT NULL,
    residence_certificate VARCHAR(255) NOT NULL,
    father_aadhar_front VARCHAR(255) NOT NULL,
    father_aadhar_back VARCHAR(255) NOT NULL,
    mother_aadhar_front VARCHAR(255) NOT NULL,
    mother_aadhar_back VARCHAR(255) NOT NULL,
    status ENUM('pending', 'verified', 'rejected', 'approved') DEFAULT 'pending',
    remarks TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    verified_at TIMESTAMP NULL,
    approved_at TIMESTAMP NULL
);

-- Create password_resets table
CREATE TABLE password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) UNIQUE NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert default admin user (password: admin123)
INSERT INTO users (username, password, role, email) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'admin@school.com');

-- Insert sample verifier user (password: verifier123)
INSERT INTO users (username, password, role, email) VALUES 
('verifier', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'verifier', 'verifier@school.com');

-- Insert sample admission data
INSERT INTO admissions (serial_no, student_name, father_name, mother_name, dob, parent_mobile, parent_email, student_photo, residence_certificate, father_aadhar_front, father_aadhar_back, mother_aadhar_front, mother_aadhar_back, status, verified_at, approved_at) VALUES
('SCH/2023/0001', 'Rahul Sharma', 'Rajesh Sharma', 'Sunita Sharma', '2015-05-15', '9876543210', 'rajesh@example.com', 'uploads/photos/photo1.jpg', 'uploads/residence/residence1.pdf', 'uploads/father_aadhar/front1.jpg', 'uploads/father_aadhar/back1.jpg', 'uploads/mother_aadhar/front1.jpg', 'uploads/mother_aadhar/back1.jpg', 'approved', NOW(), NOW()),
('SCH/2023/0002', 'Priya Patel', 'Sanjay Patel', 'Meera Patel', '2016-02-20', '8765432109', 'sanjay@example.com', 'uploads/photos/photo2.jpg', 'uploads/residence/residence2.jpg', 'uploads/father_aadhar/front2.jpg', 'uploads/father_aadhar/back2.jpg', 'uploads/mother_aadhar/front2.jpg', 'uploads/mother_aadhar/back2.jpg', 'verified', NOW(), NULL),
('SCH/2023/0003', 'Amit Kumar', 'Vikash Kumar', 'Neeta Kumar', '2015-11-10', '7654321098', 'vikash@example.com', 'uploads/photos/photo3.jpg', 'uploads/residence/residence3.pdf', 'uploads/father_aadhar/front3.jpg', 'uploads/father_aadhar/back3.jpg', 'uploads/mother_aadhar/front3.jpg', 'uploads/mother_aadhar/back3.jpg', 'pending', NULL, NULL),
('SCH/2023/0004', 'Sneha Gupta', 'Anil Gupta', 'Pooja Gupta', '2016-07-25', '6543210987', 'anil@example.com', 'uploads/photos/photo4.jpg', 'uploads/residence/residence4.jpg', 'uploads/father_aadhar/front4.jpg', 'uploads/father_aadhar/back4.jpg', 'uploads/mother_aadhar/front4.jpg', 'uploads/mother_aadhar/back4.jpg', 'rejected', NOW(), NULL);

-- Create indexes for better performance
CREATE INDEX idx_admissions_status ON admissions(status);
CREATE INDEX idx_admissions_serial ON admissions(serial_no);
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_password_resets_token ON password_resets(token);
CREATE INDEX idx_password_resets_expires ON password_resets(expires_at);