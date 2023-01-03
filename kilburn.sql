CREATE DATABASE IF NOT EXISTS kilburn;
USE kilburn;

CREATE TABLE Area (
	area_name varchar(30) NOT NULL,
	city varchar(30) NOT NULL,
	PRIMARY KEY(area_name)
);

CREATE TABLE Office (
	office_id int unsigned  NOT NULL,
	office_name varchar(30) NOT NULL,
	area_name varchar(30) NOT NULL,
	PRIMARY KEY(office_id),
	FOREIGN KEY(area_name) REFERENCES Area(area_name)
);

CREATE TABLE Department (
	department_id varchar(10)  NOT NULL,
	head_office int unsigned,
	dept_manager varchar(10),
	PRIMARY KEY(department_id),
	FOREIGN KEY(head_office) REFERENCES Office(office_id) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE Employee (
	employee_id varchar(10)  NOT NULL,
	fullname varchar(30) NOT NULL,
	address varchar(30) NOT NULL,
	salary decimal(10,2) NOT NULL,
	dob date NOT NULL,
	nin varchar(30) NOT NULL,
	direct_manager varchar(10),
	department varchar(10) NOT NULL,
	PRIMARY KEY(employee_id),
	FOREIGN KEY(direct_manager) REFERENCES Employee(employee_id) ON DELETE SET NULL ON UPDATE CASCADE,
	FOREIGN KEY(department) REFERENCES Department(department_id) 
);

ALTER TABLE Department ADD FOREIGN KEY(dept_manager) REFERENCES Employee(employee_id) ON DELETE SET NULL ON UPDATE CASCADE;

CREATE TABLE Emergency_Contact (
	emergency_name varchar(30) NOT NULL,
	relationship varchar(30) NOT NULL,
	emergency_phone varchar(30) NOT NULL,
	employee_id varchar(10) NOT NULL,
	PRIMARY KEY(employee_id),
	FOREIGN KEY(employee_id) REFERENCES Employee(employee_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE Audit_Log (
  employee_id varchar(10) NOT NULL,
  employee_name varchar(30) NOT NULL,
  deleted_time datetime NOT NULL,
  operator_id varchar(10),
  PRIMARY KEY(employee_id)
);

CREATE TABLE Warehouse (
  warehouse_id int unsigned NOT NULL,
  location_address varchar(30) NOT NULL,
  size decimal(10,2) NOT NULL,
  purpose varchar(50),
  area_name varchar(30) NOT NULL,
  PRIMARY KEY(warehouse_id),
  FOREIGN KEY(area_name) REFERENCES Area(area_name)
);

CREATE TABLE Staff_Office (
  office_id int unsigned,
  employee_id varchar(10),
  PRIMARY KEY(office_id, employee_id),
  FOREIGN KEY(employee_id) REFERENCES Employee(employee_id) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY(office_id) REFERENCES Office(office_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE Staff_Warehouse (
  warehouse_id int unsigned,
  employee_id varchar(10),
  PRIMARY KEY(warehouse_id, employee_id),
  FOREIGN KEY(employee_id) REFERENCES Employee(employee_id) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY(warehouse_id) REFERENCES Warehouse(warehouse_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE Vehicle (
  vehicle_id int unsigned NOT NULL,
  vehicle_name varchar(30) NOT NULL,
  area_name varchar(30),
  PRIMARY KEY(vehicle_id)
);

CREATE TABLE Route (
  route_name varchar(30) NOT NULL,
  starting_location varchar(30) NOT NULL,
  starting_time time NOT NULL,
  ending_location varchar(30) NOT NULL,
  ending_time time NOT NULL,
  PRIMARY KEY(route_name)
);

CREATE TABLE Stops (
  stops_name varchar(30) NOT NULL,
  arrival_time time NOT NULL,
  route_name varchar(30) NOT NULL,
  PRIMARY KEY(stops_name, route_name),
  FOREIGN KEY(route_name) REFERENCES Route(route_name) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE Shipping_Work (
  work_id int unsigned NOT NULL AUTO_INCREMENT,
  shipping_date date NOT NULL,
  driver_id varchar(10) NOT NULL,
  vehicle_id int unsigned NOT NULL,
  route_name varchar(30) NOT NULL,
  PRIMARY KEY(work_id),
  FOREIGN KEY(driver_id) REFERENCES Employee(employee_id) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY(vehicle_id) REFERENCES Vehicle(vehicle_id),
  FOREIGN KEY(route_name) REFERENCES Route(route_name)
);

CREATE TABLE Product (
  product_id int unsigned NOT NULL,
  product_name varchar(30) NOT NULL,
  description varchar(100),
  price decimal(10,2) NOT NULL,
  quantity int unsigned NOT NULL,
  PRIMARY KEY(product_id)
);

CREATE TABLE Customer (
  customer_id int unsigned NOT NULL,
  fullname varchar(30) NOT NULL,
  email varchar(30),
  postal_location varchar(30),
  PRIMARY KEY(customer_id)
);

CREATE TABLE Customer_Order (
  order_id int unsigned NOT NULL AUTO_INCREMENT,
  purchase_date date NOT NULL,
  shipping_address varchar(30) NOT NULL,
  shipping_date date NOT NULL,
  customer_id int unsigned NOT NULL,
  PRIMARY KEY(order_id),
  FOREIGN KEY(customer_id) REFERENCES Customer(customer_id)
);

CREATE TABLE Ordered_Products (
  order_id int unsigned NOT NULL,
  product_id int unsigned NOT NULL,
  quantity int unsigned NOT NULL default 1,
  PRIMARY KEY(order_id, product_id),
  FOREIGN KEY(order_id) REFERENCES Customer_Order(order_id),
  FOREIGN KEY(product_id) REFERENCES Product(product_id)
);

CREATE TABLE Complaint (
  complaint_number int unsigned NOT NULL AUTO_INCREMENT,
  complaint_date date NOT NULL,
  passenger_name varchar(30) NOT NULL,
  complaint_reason varchar(200) NOT NULL,
  status int unsigned NOT NULL default 0,
  staff_id varchar(10),
  PRIMARY KEY(complaint_number),
  FOREIGN KEY(staff_id) REFERENCES Employee(employee_id) ON DELETE SET NULL ON UPDATE CASCADE
);

