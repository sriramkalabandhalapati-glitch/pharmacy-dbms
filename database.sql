-- PHARMACIA : Pharmacy Database Management System
-- Import in phpMyAdmin (Import tab) or: mysql -u root < database.sql
DROP DATABASE IF EXISTS pharmacia;
CREATE DATABASE pharmacia CHARACTER SET utf8mb4;
USE pharmacia;

CREATE TABLE customer (
  customer_id INT AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(50) NOT NULL,
  last_name VARCHAR(50) NOT NULL,
  contact_no VARCHAR(15) NOT NULL UNIQUE,
  address VARCHAR(100),
  street_no VARCHAR(20)
);
CREATE TABLE insurance (
  insurance_id INT AUTO_INCREMENT PRIMARY KEY,
  company_name VARCHAR(100) NOT NULL,
  co_insurance VARCHAR(50),
  start_date DATE NOT NULL,
  end_date DATE NOT NULL,
  customer_id INT,
  FOREIGN KEY (customer_id) REFERENCES customer(customer_id) ON DELETE CASCADE
);
CREATE TABLE doctor (
  doctor_id INT AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(50) NOT NULL,
  last_name VARCHAR(50) NOT NULL,
  specialization VARCHAR(50),
  contact_no VARCHAR(15) UNIQUE,
  mail_id VARCHAR(100) UNIQUE
);
CREATE TABLE drug (
  drug_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  type VARCHAR(50),
  stock INT,
  cost_price DECIMAL(10,2) NOT NULL,
  mrp DECIMAL(10,2) NOT NULL,
  discount DECIMAL(5,2) DEFAULT 0,
  expiry_date DATE NOT NULL,
  location VARCHAR(50),
  CONSTRAINT chk_stock CHECK (stock >= 0)
);
CREATE TABLE employee (
  employee_id INT AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(50) NOT NULL,
  last_name VARCHAR(50) NOT NULL,
  contact_no VARCHAR(15) UNIQUE,
  qualification VARCHAR(50),
  designation VARCHAR(50),
  salary DECIMAL(10,2)
);
CREATE TABLE distributor (
  distributor_id INT AUTO_INCREMENT PRIMARY KEY,
  company_name VARCHAR(100) NOT NULL,
  contact_no VARCHAR(15) UNIQUE,
  license VARCHAR(50) NOT NULL,
  address VARCHAR(100),
  email VARCHAR(100)
);
CREATE TABLE drug_manufacture (
  company_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  fssai VARCHAR(50) NOT NULL UNIQUE
);
CREATE TABLE purchase (
  purchase_id INT AUTO_INCREMENT PRIMARY KEY,
  date DATE NOT NULL,
  quantity INT,
  cost_price DECIMAL(10,2),
  distributor_id INT,
  drug_id INT,
  CONSTRAINT chk_pq CHECK (quantity > 0),
  FOREIGN KEY (distributor_id) REFERENCES distributor(distributor_id),
  FOREIGN KEY (drug_id) REFERENCES drug(drug_id)
);
CREATE TABLE sale_transaction (
  sale_id INT AUTO_INCREMENT PRIMARY KEY,
  date DATE NOT NULL,
  quantity INT NOT NULL,
  customer_id INT,
  drug_id INT,
  employee_id INT,
  CONSTRAINT chk_sq CHECK (quantity > 0),
  FOREIGN KEY (customer_id) REFERENCES customer(customer_id),
  FOREIGN KEY (drug_id) REFERENCES drug(drug_id),
  FOREIGN KEY (employee_id) REFERENCES employee(employee_id)
);
CREATE TABLE prescribes (
  doctor_id INT, customer_id INT,
  PRIMARY KEY (doctor_id, customer_id),
  FOREIGN KEY (doctor_id) REFERENCES doctor(doctor_id),
  FOREIGN KEY (customer_id) REFERENCES customer(customer_id)
);
CREATE TABLE manufactures (
  company_id INT, drug_id INT,
  PRIMARY KEY (company_id, drug_id),
  FOREIGN KEY (company_id) REFERENCES drug_manufacture(company_id),
  FOREIGN KEY (drug_id) REFERENCES drug(drug_id)
);
CREATE TABLE users (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(30) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','pharmacist') NOT NULL,
  employee_id INT,
  FOREIGN KEY (employee_id) REFERENCES employee(employee_id)
);

-- ---------- sample data ----------
INSERT INTO employee (first_name,last_name,contact_no,qualification,designation,salary) VALUES
('Vanishri','Elangovan','9967845123','B.Pharm','Pharmacist',26000),
('Anita','Shree','8546123456','D.Pharm','Pharmacist',45000),
('Harish','Raja','7854123694','B.Pharm','Pharmacist',21000),
('Amaya','Singh','7854332165','M.Pharm','Manager',32000);
INSERT INTO users (username,password_hash,role,employee_id) VALUES
('admin','$2y$10$xn3vevIyWVLINc5Z1sC1T.ZdI1mqH2cXs18RcJ8I1MNeNqdo/lQ1K','admin',4),
('pharmacist','$2y$10$CrlMpoLtWTdD7QJAMhFiKu/hqegn9Rf9MqfQOsbN9tVc8IfVciI4m','pharmacist',1);
INSERT INTO distributor (company_name,contact_no,license,address,email) VALUES
('XYZ Pharmaceuticals','8746832145','DL-2201','Chennai, Tamil Nadu','xyz@pharma.com'),
('ABC PharmaSupply','7894561235','DL-2202','Trichy','abc@pharmsupp.com'),
('Daily Pharma Ltd','7854699321','DL-2203','Hyderabad','daily@dpharma.com'),
('MediAid','9874583236','DL-2204','Chennai','mail@mediaid.com');
INSERT INTO drug_manufacture (name,fssai) VALUES ('Micro Labs','FS-1001'),('Cipla','FS-1002'),('Pfizer','FS-1003');
INSERT INTO drug (name,type,stock,cost_price,mrp,discount,expiry_date,location) VALUES
('Dolo 650 MG','Tablet',620,1.00,1.50,0,DATE_ADD(CURDATE(),INTERVAL 14 MONTH),'rack 5'),
('Paracetamol','Tablet',90,2.00,2.50,0,DATE_ADD(CURDATE(),INTERVAL 10 MONTH),'rack 6'),
('Livogen','Capsule',25,4.00,5.00,0,DATE_ADD(CURDATE(),INTERVAL 40 DAY),'rack 3'),
('Gelusil','Tablet',440,1.00,1.25,0,DATE_ADD(CURDATE(),INTERVAL 18 MONTH),'rack 4'),
('Cyclopam','Tablet',120,5.00,6.00,5,DATE_ADD(CURDATE(),INTERVAL 8 MONTH),'rack 2'),
('Benadryl 200 ML','Syrup',20,80.00,95.00,0,DATE_ADD(CURDATE(),INTERVAL 25 DAY),'rack 10'),
('Lopamide','Capsule',15,3.00,4.00,0,DATE_ADD(CURDATE(),INTERVAL 6 MONTH),'rack 7'),
('Vitamin C','Tablet',90,2.50,3.00,0,DATE_ADD(CURDATE(),INTERVAL 12 MONTH),'rack 8');
INSERT INTO manufactures VALUES (1,1),(1,2),(2,4),(3,5);
INSERT INTO customer (first_name,last_name,contact_no,address,street_no) VALUES
('Ravi','Kumar','9000011111','Chennai','12'),
('Meena','Devi','9000022222','Trichy','7'),
('Arjun','Nair','9000033333','Kattankulathur','21');
INSERT INTO doctor (first_name,last_name,specialization,contact_no,mail_id) VALUES
('Suresh','Babu','General Physician','8000011111','suresh@clinic.com'),
('Lakshmi','Priya','Pediatrics','8000022222','lakshmi@clinic.com');
INSERT INTO prescribes VALUES (1,1),(2,2);
INSERT INTO insurance (company_name,co_insurance,start_date,end_date,customer_id) VALUES ('Star Health','10%',CURDATE(),DATE_ADD(CURDATE(),INTERVAL 1 YEAR),1);

-- ---------- trigger, function, procedures ----------
DELIMITER //
CREATE TRIGGER After_Sale_Update_Stock
AFTER INSERT ON sale_transaction
FOR EACH ROW
BEGIN
  UPDATE drug SET stock = stock - NEW.quantity WHERE drug_id = NEW.drug_id;
END //

CREATE FUNCTION GetCustomerTotalSpent(p_id INT) RETURNS DECIMAL(10,2)
READS SQL DATA
BEGIN
  DECLARE total_spent DECIMAL(10,2);
  SELECT SUM(s.quantity * d.mrp * (1 - d.discount/100)) INTO total_spent
  FROM sale_transaction s INNER JOIN drug d ON s.drug_id = d.drug_id
  WHERE s.customer_id = p_id;
  RETURN COALESCE(total_spent, 0.00);
END //

CREATE PROCEDURE SafeInsertPurchase(IN p_date DATE, IN qty INT, IN d_id INT, IN dist_id INT, IN cost DECIMAL(10,2), OUT msg VARCHAR(120))
BEGIN
  DECLARE EXIT HANDLER FOR SQLEXCEPTION
  BEGIN
    ROLLBACK;
    SET msg = 'ERROR: Transaction failed. Check drug, distributor and quantity.';
  END;
  START TRANSACTION;
  INSERT INTO purchase (date, quantity, cost_price, distributor_id, drug_id) VALUES (p_date, qty, cost, dist_id, d_id);
  UPDATE drug SET stock = stock + qty WHERE drug_id = d_id;
  COMMIT;
  SET msg = 'OK';
END //

CREATE PROCEDURE GenerateExpiryReport()
BEGIN
  DECLARE d_name VARCHAR(100);
  DECLARE d_expiry DATE;
  DECLARE done INT DEFAULT FALSE;
  DECLARE cur CURSOR FOR SELECT name, expiry_date FROM drug WHERE expiry_date <= DATE_ADD(CURDATE(), INTERVAL 3 MONTH) ORDER BY expiry_date;
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
  CREATE TEMPORARY TABLE IF NOT EXISTS temp_alerts (msg TEXT);
  TRUNCATE temp_alerts;
  OPEN cur;
  read_loop: LOOP
    FETCH cur INTO d_name, d_expiry;
    IF done THEN LEAVE read_loop; END IF;
    INSERT INTO temp_alerts VALUES (CONCAT('ALERT: ', d_name, ' is expiring on ', d_expiry));
  END LOOP;
  CLOSE cur;
  SELECT * FROM temp_alerts;
END //
DELIMITER ;

INSERT INTO sale_transaction (date,quantity,customer_id,drug_id,employee_id) VALUES
(CURDATE(),10,1,1,1),(CURDATE(),5,2,5,1),(CURDATE(),2,3,6,2);
