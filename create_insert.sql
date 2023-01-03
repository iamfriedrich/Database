USE kilburn;

DELIMITER $$
CREATE PROCEDURE fetch_employee_with_birthday()
BEGIN
	SELECT employee_id, fullname, dob FROM Employee WHERE month(dob) = month(curdate());
END $$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER trig_del_employee AFTER DELETE ON Employee FOR EACH Row
BEGIN
	INSERT INTO Audit_Log (employee_id, employee_name, deleted_time) VALUES (OLD.employee_id, OLD.fullname, NOW());
END $$
DELIMITER ;

INSERT INTO Department (department_id) VALUES ("Driver"), ("Packager"), ("HR"), ("Manager");