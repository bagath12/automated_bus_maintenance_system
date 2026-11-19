CREATE DATABASE IF NOT EXISTS bus_maintenance CHARACTER SET utf8mb4;
USE bus_maintenance;
CREATE TABLE admins(id INT AUTO_INCREMENT PRIMARY KEY, username VARCHAR(50) UNIQUE NOT NULL, password_hash VARCHAR(255) NOT NULL);
CREATE TABLE buses(id INT AUTO_INCREMENT PRIMARY KEY, bus_number VARCHAR(30) UNIQUE NOT NULL, route VARCHAR(100), tago_token VARCHAR(100), current_km DECIMAL(10,1) DEFAULT 0, last_sync DATETIME NULL);
CREATE TABLE items(id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(80) UNIQUE NOT NULL, default_km INT NULL, default_days INT NULL);
CREATE TABLE schedules(id INT AUTO_INCREMENT PRIMARY KEY, bus_id INT NOT NULL, item_id INT NOT NULL,
 last_km DECIMAL(10,1) DEFAULT 0, last_date DATE NOT NULL, interval_km INT NULL, interval_days INT NULL,
 next_km DECIMAL(10,1) NULL, next_date DATE NULL, status ENUM('Pending','Due','Completed') DEFAULT 'Pending',
 reminder_sent TINYINT DEFAULT 0, UNIQUE(bus_id,item_id),
 FOREIGN KEY(bus_id) REFERENCES buses(id) ON DELETE CASCADE, FOREIGN KEY(item_id) REFERENCES items(id) ON DELETE CASCADE);
CREATE TABLE history(id INT AUTO_INCREMENT PRIMARY KEY, bus_id INT NOT NULL, item_id INT NOT NULL, service_km DECIMAL(10,1), service_date DATE, notes VARCHAR(255),
 FOREIGN KEY(bus_id) REFERENCES buses(id) ON DELETE CASCADE, FOREIGN KEY(item_id) REFERENCES items(id) ON DELETE CASCADE);
CREATE TABLE reminders(id INT AUTO_INCREMENT PRIMARY KEY, schedule_id INT NOT NULL, message VARCHAR(255), emailed TINYINT DEFAULT 0, created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(schedule_id) REFERENCES schedules(id) ON DELETE CASCADE);
INSERT INTO items(name,default_km,default_days) VALUES('Engine oil',10000,180),('Coolant',40000,365),('Oil filter',10000,180),('Fuel filter',20000,365),('Brake inspection',15000,90);
