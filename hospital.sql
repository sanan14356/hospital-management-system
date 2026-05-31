CREATE DATABASE IF NOT EXISTS hospital_db;
USE hospital_db;

DROP TABLE IF EXISTS tickets;
DROP TABLE IF EXISTS assignments;
DROP TABLE IF EXISTS patients;
DROP TABLE IF EXISTS doctors;
DROP TABLE IF EXISTS admins;

CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE doctors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    specialization VARCHAR(120) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    experience INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    age INT NOT NULL,
    gender VARCHAR(20) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(120) NOT NULL,
    address VARCHAR(255) NOT NULL,
    disease VARCHAR(120) NOT NULL,
    blood_group VARCHAR(10) NOT NULL,
    admission_date DATE NOT NULL,
    assigned_doctor_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (assigned_doctor_id) REFERENCES doctors(id) ON DELETE SET NULL
);

CREATE TABLE assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active TINYINT(1) DEFAULT 1,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
);

CREATE TABLE tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    ticket_date DATE NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
);

INSERT INTO admins (full_name, email, password) VALUES
('System Admin', 'admin@hospital.test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

INSERT INTO doctors (name, specialization, phone, email, experience) VALUES
('Dr. Aisha Rahman', 'Cardiology', '03005551234', 'aisha@hospital.test', 10),
('Dr. Omar Nasser', 'Dermatology', '03005553456', 'omar@hospital.test', 8),
('Dr. Lina Farid', 'Orthopedics', '03005555678', 'lina@hospital.test', 12),
('Dr. Sami Malik', 'General Medicine', '03005557890', 'sami@hospital.test', 7),
('Dr. Noor Ibrahim', 'Ophthalmology', '03005559012', 'noor@hospital.test', 9);

INSERT INTO patients (full_name, age, gender, phone, email, address, disease, blood_group, admission_date, assigned_doctor_id) VALUES
('Mariam Alvi', 29, 'Female', '03001234567', 'mariam@example.com', 'Gulshan Block A', 'Heart Problem', 'A+', '2026-05-10', 1),
('Hassan Iqbal', 41, 'Male', '03009876543', 'hassan@example.com', 'Model Town', 'Skin Disease', 'B+', '2026-05-08', 2),
('Sana Sheikh', 33, 'Female', '03002223344', 'sana@example.com', 'DHA Phase 2', 'Fever', 'O+', '2026-05-11', 4),
('Imran Qureshi', 51, 'Male', '03006667788', 'imran@example.com', 'Johar Town', 'Bone Problem', 'AB+', '2026-05-06', 3);

INSERT INTO assignments (patient_id, doctor_id, is_active) VALUES
(1, 1, 1),
(2, 2, 1),
(3, 4, 1),
(4, 3, 1);

INSERT INTO tickets (patient_id, doctor_id, ticket_date, price) VALUES
(1, 1, '2026-05-12', 150.00),
(2, 2, '2026-05-11', 95.00);