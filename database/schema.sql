CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  full_name VARCHAR(120) NOT NULL,
  role ENUM('admin','technician') NOT NULL,
  is_active TINYINT(1) DEFAULT 1,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE technicians (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  phone VARCHAR(30),
  specialization VARCHAR(120),
  FOREIGN KEY (user_id) REFERENCES users(id)
);
CREATE TABLE clients (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(120) NOT NULL, phone VARCHAR(30), email VARCHAR(120), created_at DATETIME DEFAULT CURRENT_TIMESTAMP);
CREATE TABLE vehicles (id INT AUTO_INCREMENT PRIMARY KEY, client_id INT NOT NULL, plate_no VARCHAR(20) NOT NULL, vin VARCHAR(40) NOT NULL UNIQUE, make VARCHAR(60), model VARCHAR(60), year SMALLINT, FOREIGN KEY (client_id) REFERENCES clients(id));
CREATE TABLE workstations (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(80) NOT NULL, is_active TINYINT(1) DEFAULT 1);
CREATE TABLE operation_templates (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(120) NOT NULL, description TEXT, estimated_minutes INT NOT NULL, is_active TINYINT(1) DEFAULT 1);
CREATE TABLE work_orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  client_id INT NOT NULL,
  vehicle_id INT NOT NULL,
  technician_id INT NOT NULL,
  workstation_id INT,
  priority ENUM('normal','urgent') DEFAULT 'normal',
  status ENUM('new','assigned','in_progress','pause','completed','cancelled') DEFAULT 'new',
  admin_notes TEXT,
  finish_reason TEXT,
  estimated_minutes INT DEFAULT 0,
  started_at DATETIME NULL,
  completed_at DATETIME NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (client_id) REFERENCES clients(id), FOREIGN KEY (vehicle_id) REFERENCES vehicles(id), FOREIGN KEY (technician_id) REFERENCES users(id), FOREIGN KEY (workstation_id) REFERENCES workstations(id)
);
CREATE TABLE work_order_operations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  work_order_id INT NOT NULL,
  operation_template_id INT NOT NULL,
  status ENUM('pending','done') DEFAULT 'pending',
  tech_note TEXT,
  FOREIGN KEY (work_order_id) REFERENCES work_orders(id) ON DELETE CASCADE,
  FOREIGN KEY (operation_template_id) REFERENCES operation_templates(id)
);
CREATE TABLE work_sessions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  work_order_id INT NOT NULL,
  technician_id INT NOT NULL,
  started_at DATETIME NULL,
  paused_at DATETIME NULL,
  resumed_at DATETIME NULL,
  ended_at DATETIME NULL,
  FOREIGN KEY (work_order_id) REFERENCES work_orders(id) ON DELETE CASCADE,
  FOREIGN KEY (technician_id) REFERENCES users(id)
);
CREATE TABLE audit_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  action VARCHAR(80) NOT NULL,
  entity VARCHAR(80) NOT NULL,
  entity_id INT NULL,
  payload JSON NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

INSERT INTO users(username,password_hash,full_name,role) VALUES
('admin','$2y$10$e0NR3M8W9q4P.Y56VfPf8eR0aNdb7jO2jhMKTl5h6LQvP8qN3v0CO','Admin Service','admin'),
('tech1','$2y$10$2Xkgk9feh2TuxqgkxmG5FOSzF4GXjSXZnQb7U/pqQqOUtiTlM.3i2','Ionut Pop','technician'),
('tech2','$2y$10$2Xkgk9feh2TuxqgkxmG5FOSzF4GXjSXZnQb7U/pqQqOUtiTlM.3i2','Mihai Radu','technician'),
('tech3','$2y$10$2Xkgk9feh2TuxqgkxmG5FOSzF4GXjSXZnQb7U/pqQqOUtiTlM.3i2','Alex Dumitru','technician');
INSERT INTO technicians(user_id,phone,specialization) VALUES (2,'0700000001','Mecanica'),(3,'0700000002','Diagnoza'),(4,'0700000003','Frane');
INSERT INTO workstations(name) VALUES ('Elevator 1'),('Elevator 2'),('Stand diagnoza');
INSERT INTO clients(name,phone,email) VALUES
('Client One','0711111111','c1@mail.com'),('Client Two','0722222222','c2@mail.com'),('Client Three','0733333333','c3@mail.com'),('Client Four','0744444444','c4@mail.com'),('Client Five','0755555555','c5@mail.com');
INSERT INTO vehicles(client_id,plate_no,vin,make,model,year) VALUES
(1,'B101AAA','VIN000000000000001','Dacia','Logan',2018),(2,'B102BBB','VIN000000000000002','VW','Golf',2019),(3,'B103CCC','VIN000000000000003','BMW','320',2017),(4,'B104DDD','VIN000000000000004','Ford','Focus',2020),(5,'B105EEE','VIN000000000000005','Audi','A4',2016);
INSERT INTO operation_templates(name,description,estimated_minutes) VALUES
('Schimb ulei','Inlocuire ulei motor',45),('Schimb filtru ulei','Inlocuire filtru ulei',20),('Schimb filtru aer','Inlocuire filtru aer',15),('Schimb filtru combustibil','Inlocuire filtru combustibil',25),('Schimb placute frana','Inlocuire placute',60),('Schimb discuri frana','Inlocuire discuri',90),('Diagnoza','Diagnoza electronica',30),('Revizie completa','Revizie generala',120),('Verificare suspensie','Inspectie suspensie',40),('Geometrie roti','Reglaj directie',50);
INSERT INTO work_orders(client_id,vehicle_id,technician_id,workstation_id,priority,status,admin_notes,estimated_minutes) VALUES
(1,1,2,1,'normal','assigned','Revizie standard',120),(2,2,3,3,'urgent','in_progress','Verificare engine light',60),(3,3,4,2,'normal','new','Frane fata',90),(4,4,2,1,'normal','pause','Asteapta piese',80),(5,5,3,3,'normal','completed','Diagnoza finalizata',30);
INSERT INTO work_order_operations(work_order_id,operation_template_id,status) VALUES
(1,1,'pending'),(1,2,'pending'),(1,3,'pending'),(2,7,'done'),(3,5,'pending'),(3,6,'pending'),(4,8,'pending'),(5,7,'done');
