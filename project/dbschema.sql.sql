-- Create the database
CREATE DATABASE proj_system;

-- Use the created database
USE proj_system;

-- Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    role ENUM('Manager', 'Project Leader', 'Team Member') NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(15),
    address TEXT,
    qualification VARCHAR(50),
    skills TEXT,
    idNumber INT UNIQUE,
    dob DATE
);


-- Projects Table
CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id VARCHAR(20) UNIQUE NOT NULL,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    customer_name VARCHAR(100),
    budget DECIMAL(10, 2),
    start_date DATE,
    end_date DATE,
    leader_id INT DEFAULT NULL,
    FOREIGN KEY (leader_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Tasks Table
CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    task_id VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    project_id INT NOT NULL,
    leader_id INT NOT NULL,
    start_date DATE,
    end_date DATE,
    effort INT,
    status ENUM('Pending', 'In Progress', 'Completed') DEFAULT 'Pending',
    priority ENUM('Low', 'Medium', 'High') DEFAULT 'Medium',
    completion_percentage INT DEFAULT 0,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (leader_id) REFERENCES users(id) ON DELETE CASCADE
);


-- Team Members Table
CREATE TABLE team_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    task_id INT NOT NULL,
    user_id INT NOT NULL,
    role VARCHAR(50),
    contribution_percentage INT,
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


-- Create Project Documents Table
CREATE TABLE IF NOT EXISTS project_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id VARCHAR(20) NOT NULL,
    title VARCHAR(255) NOT NULL,
    path VARCHAR(255) NOT NULL,
    FOREIGN KEY (project_id) REFERENCES projects(project_id)
);


-- Insert Users
INSERT INTO users (username, password, name, role, email, phone, address, qualification, skills) VALUES
('Ahmad', 'managar1', 'Ahmad', 'Manager', 'Ahmad@gmail.com', '0562373826', '123 St.', 'MBA', 'Leadership, Organization'),
('Sadeel', 'leader1', 'Sadeel', 'Project Leader', 'Sadeel@gmail.com', '0592548245', '456 Rd.', 'PMP', 'Project Planning, Communication'),
('Bara', 'team1', 'Bara', 'Team Member', 'Bara@gmail.com', '0592436176', '789 St.', 'BSc CS', 'Development, Testing'),
('Sami', 'team2', 'Sami', 'Team Member', 'Sami@gmail.com', '0562453965', '101 Rd.', 'BSc CS', 'Design, UI/UX'),
('Sara', 'team3', 'Sara', 'Team Member', 'Sara@gmail.com', '0594337523', '202 St.', 'BSc CS', 'Support, Documentation');

INSERT INTO users (username, password, name, role, email, phone, address, qualification, skills) VALUES
('habib_hij', 'habib123456', 'Habib Hijazi', 'Team Member', 'habib@gmail.com', '0594337523', '202 St.', 'BSc CS', 'Support, Documentation');

-- Insert Projects
INSERT INTO projects (project_id, title, description, customer_name, budget, start_date, end_date, leader_id) VALUES
('PRJ-00001', 'Website Redesign', 'A project to redesign the company website.', 'Ministry of Education', 15000.00, '2024-01-01', '2024-06-30', 2),
('PRJ-00002', 'Mobile App Development', 'Develop a mobile application for e-commerce.', 'Beta Tech', 3000.00, '2024-02-01', '2024-08-31', NULL),
('PRJ-00003', 'Mobile App', 'Develop a mobile app.', 'Beta Tech', 3000.00, '2024-02-01', '2024-08-31', 2);
INSERT INTO projects (project_id, title, description, customer_name, budget, start_date, end_date, leader_id) VALUES
('PRJ-00008', 'Mobile App', 'Develop a mobile app.', 'Beta Tech', 3000.00, '2024-02-01', '2024-08-31', 7);


DELETE FROM projects where id='8' ;

INSERT INTO projects (project_id, title, description, customer_name, budget, start_date, end_date, leader_id) VALUES
('PRJ-00009', 'School Portal', 'Develop a School Portal.', 'Beta Tech', 3000.00, '2025-01-01', '2025-08-31', 7);

-- Insert Tasks
INSERT INTO tasks (task_id, name, description, project_id, leader_id, start_date, end_date, effort, status, priority) VALUES
('TASK-00001', 'Design Wireframes', 'Create wireframes for the new website.', 1, 2, '2024-01-10', '2024-02-15', 2, 'Pending', 'High'),
('TASK-00002', 'Develop Backend', 'Implement backend APIs.', 1, 2, '2024-02-16', '2024-04-15', 4, 'Pending', 'Medium'),
('TASK-00003', 'Test Website', 'Perform end-to-end testing.', 1, 2, '2024-04-16', '2024-06-15', 3, 'Pending', 'Low');

-- Insert Team Members
INSERT INTO team_members (task_id, user_id, role, contribution_percentage) VALUES
(1, 3, 'Designer', 30),
(1, 4, 'Developer', 70),
(2, 4, 'Developer', 100),
(3, 5, 'Tester', 100);


SELECT * FROM projects;

SELECT * FROM users WHERE role = 'Project Leader';