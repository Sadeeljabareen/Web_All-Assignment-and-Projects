
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255),
  email VARCHAR(255) UNIQUE,
  password VARCHAR(255),
  user_type ENUM('manager', 'customer', 'staff')
);

CREATE TABLE IF NOT EXISTS staff (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS tickets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  customer_id INT NOT NULL,
  description TEXT,
  submitted_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  status ENUM('Pending', 'Assigned', 'Completed') DEFAULT 'Pending',
  emergency_level ENUM('Low', 'Medium', 'High'),
  assigned_date TIMESTAMP NULL,
  assigned_staff_id INT NULL,
  ticket_image VARCHAR(255),
  location VARCHAR(255),
  FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (assigned_staff_id) REFERENCES staff(id) ON DELETE SET NULL
);


INSERT INTO tickets (customer_id, description, emergency_level, ticket_image, location) 
VALUES ((SELECT id FROM users WHERE email = 'cust@maktoob.com'), 
        'Broken air conditioner in the living room', 
        'High', 
        'ac_broken.jpg', 
        'Living Room');

UPDATE tickets 
SET assigned_staff_id = (SELECT id FROM staff WHERE user_id = (SELECT id FROM users WHERE email = 'staff1@maktoob.com')) 
WHERE id = 1;

INSERT INTO users (name, email, password, user_type) VALUES
('Adam', 'manager@maktoob.com', 'comp@334', 'manager'),
('Asmaa', 'manager2@maktoob.com', 'mang123', 'manager'),
('Borhan', 'manager3@maktoob.com', 'mang123', 'manager'),
('Basma', 'cust@maktoob.com', 'comp#334', 'customer'),
('Ameer', 'cust2@maktoob.com', 'cust123', 'customer'),
('Deyaa-eldeen', 'cust3@maktoob.com', 'cust123', 'customer'),
('Doha', 'cust4@maktoob.com', 'cust123', 'customer'),
('Ehab', 'cust5@maktoob.com', 'cust123', 'customer'),
('Ezz-eldeen', 'cust6@maktoob.com', 'cust123', 'customer'),
('Jehan', 'cust7@maktoob.com', 'cust123', 'customer'),
('Kawthar', 'cust8@maktoob.com', 'cust123', 'customer'),
('Leena', 'cust9@maktoob.com', 'cust123', 'customer'),
('Maamoon', 'cust10@maktoob.com', 'cust123', 'customer'),
('Maysaa', 'staff1@maktoob.com', 'staff123', 'staff'),
('Sami', 'staff2@maktoob.com', 'staff123', 'staff'),
('Nadeem', 'staff3@maktoob.com', 'staff123', 'staff'),
('Nooh', 'staff4@maktoob.com', 'staff123', 'staff'),
('Osamah', 'staff5@maktoob.com', 'staff123', 'staff'),
('Omniah', 'staff6@maktoob.com', 'staff123', 'staff'),
('Rania', 'staff7@maktoob.com', 'staff123', 'staff'),
('Reem', 'staff8@maktoob.com', 'staff123', 'staff'),
('Saly', 'staff9@maktoob.com', 'staff123', 'staff'),
('Walaa', 'staff10@maktoob.com', 'staff123', 'staff');


INSERT INTO staff (user_id)
SELECT id FROM users WHERE user_type = 'staff';
