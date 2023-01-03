<!DOCTYPE html>
<html>
<head>
  <title>Processing Results</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.staticfile.org/twitter-bootstrap/5.1.1/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.staticfile.org/twitter-bootstrap/5.1.1/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<?php
  $cmd = $_GET['cmd'];
  switch ($cmd) {
    case 'register':
      doRegister();
      break;
    case 'update':
      doUpdate();
      break;
    case 'delete':
      doDelete();
      break;
    case 'driver':
      doDisplayDrivers();
      break;
    case 'birthday':
      doDisplayBirthday();
      break;
    case 'logs':
      doDisplayLogs();
      break;
    default:
      echo "Error: " . $cmd . " not supported!";
      break;
  }

  function test_input($input_name){
    $data = $_POST[$input_name];
    $data = trim($data);
    if ($data == "")
      die("Invalid input : " . $input_name . " is empty.");

    return $data;
  }

  function test_employee_id($input_name){
    $data = test_input($input_name);
    if (!preg_match("/[0-9]{2}-[0-9]{3}/", $data))
      die("Invalid Input : " . $input_name . " is invalid employee id.");

    return $data;
  }


  function test_name($input_name){
    $data = test_input($input_name);
    if (!preg_match("/[A-Z][a-z]*(\s[A-Z][a-z]*)*/", $data))
      die("Invalid Input : " . $input_name . " is invalid name.");

    return $data;
  }

  function test_salary($input_name){
    $data = test_input($input_name);
    if (floatval($data) < 10)
      die("Invalid Input : " . $input_name . " should be greater than 10.");

    return $data;
  }

  function test_phone($input_name){
    $data = test_input($input_name);
    if (!preg_match("/[0-9]*(\s[0-9]*)*/", $data))
      die("Invalid Input : " . $input_name . " is invalid phone number.");

    return $data;
  }

  function doRegister(){
    $employee_id = test_employee_id('employee_id');
    $fullname = test_name('fullname');
    $address = test_input('address');
    $salary = test_salary('salary');
    $dob = test_input('dob');
    $nin = test_input('nin');
    $department = test_input('department');
    $emergency_name = test_name('emergency_name');
    $relationship = test_input('relationship');
    $emergency_phone = test_phone('emergency_phone');

    try{
      $pdo = new pdo("mysql:host=localhost;dbname=kilburn", "kilburnuser", "123");
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
      $pdo->beginTransaction();

      $sql = "SELECT fullname FROM Employee WHERE employee_id = :id";
      $stmt = $pdo->prepare($sql);

      $stmt->execute(['id' => $employee_id]);
      while ($row = $stmt->fetch()) {
        echo "<p>Failed! The employee ID : " . $employee_id . " already exists.</p>";
        return;
      }

      $sql1 = sprintf("INSERT INTO Employee (employee_id, fullname, address, salary, dob, nin, department)
                      VALUES ('%s', '%s','%s', '%s', '%s', '%s', '%s')",
                      $employee_id,
                      $fullname,
                      $address,
                      $salary,
                      $dob,
                      $nin,
                      $department);
      $sql2 = sprintf("INSERT INTO Emergency_Contact (employee_id, emergency_name, relationship, emergency_phone)
                      VALUES ('%s', '%s','%s', '%s')",
                      $employee_id,
                      $emergency_name,
                      $relationship,
                      $emergency_phone);
      $pdo->exec($sql1);
      $pdo->exec($sql2);
      $pdo->commit();
      echo "<p>Register OK! The new employee ID is : " . $_POST['employee_id'] . "</p>";
    }
    catch(PDOException $e){
      $pdo->rollBack();
      echo "<p>Failed: " . $e->getMessage() . "</p>";
    }
  }


  function doUpdate(){
    $salary = test_salary('salary');
    $emergency_phone = test_phone('emergency_phone');

    try{
      $pdo = new pdo("mysql:host=localhost;dbname=kilburn", "kilburnuser", "123");
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
      $pdo->beginTransaction();

      $id = $_POST['employee_id'];
      $sql = "SELECT fullname FROM Employee WHERE employee_id = :id";
      $stmt = $pdo->prepare($sql);

      $stmt->execute(['id' => $id]);
      $stmt->setFetchMode(PDO::FETCH_ASSOC);
      $name = NULL;
      while ($row = $stmt->fetch()) {
        $name = $row['fullname'];
      }
      if ($name == NULL){
        echo "<p>Failed! The employee ID : " . $id . " does not exist.</p>";
        return;
      }

      $sql2 = sprintf("UPDATE Employee SET salary = '%s' WHERE employee_id = '%s'",
                    $salary, $id);
      $sql3 = sprintf("UPDATE Emergency_Contact SET emergency_phone = '%s' WHERE employee_id = '%s'",
                    $emergency_phone, $id);
      $pdo->exec($sql2);
      $pdo->exec($sql3);
      $pdo->commit();
      echo "<p>Update OK! The updated employee ID is : " . $_POST['employee_id'] . "</p>";
    }
    catch(PDOException $e){
      $pdo->rollBack();
      echo "<p>Failed: " . $e->getMessage() . "</p>";
    }
  }


  function doDelete(){
    $your_id = $_POST['your_id'];
    $del_id = $_POST['del_employee_id'];

    try{
      $pdo = new pdo("mysql:host=localhost;dbname=kilburn", "kilburnuser", "123");
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
      $pdo->beginTransaction();

      $sql = "SELECT fullname FROM Employee WHERE employee_id = :id";
      $stmt = $pdo->prepare($sql);

      $stmt->execute(['id' => $your_id]);
      $stmt->setFetchMode(PDO::FETCH_ASSOC);
      $your_name = NULL;
      while ($row = $stmt->fetch()) {
        $your_name = $row['fullname'];
      }
      if ($your_name == NULL){
        echo "<p>Failed : Your employee ID " . $your_id . " does not exist.</p>";
        return;
      }

      $stmt->execute(['id' => $del_id]);
      $del_name = NULL;
      while ($row = $stmt->fetch()) {
        $del_name = $row['fullname'];
      }
      if ($del_name == NULL){
        echo "<p>Failed : The delete employee ID " . $del_id . " does not exist.</p>";
        return;
      }

      $sql2 = sprintf("DELETE FROM Employee WHERE employee_id = '%s'", $del_id);
      $sql3 = sprintf("UPDATE Audit_Log SET operator_id = '%s' WHERE employee_id = '%s'",
                    $your_id, $del_id);
      $pdo->exec($sql2);
      $pdo->exec($sql3);
      $pdo->commit();
      echo "<p>Delete OK! The deleted employee ID is : " . $del_id . "</p>";
    }
    catch (Exception $e) {
        $dbh->rollBack();
        echo "Failed: " . $e->getMessage();
    }
  }


  function doDisplayDrivers(){
    try{
      $pdo = new pdo("mysql:host=localhost;dbname=kilburn", "kilburnuser", "123");
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

      $sql = "SELECT a.employee_id, a.fullname, a.department, b.relationship, b.emergency_name
              FROM Employee a INNER JOIN Emergency_Contact b ON a.employee_id = b.employee_id
              WHERE a.department = 'Driver' AND b.relationship = 'Father'";
      $stmt = $pdo->prepare($sql);
      $stmt->execute();
      $stmt->setFetchMode(PDO::FETCH_ASSOC);

      echo "<div class=\"container mt-3\">
            <h5>All drivers whose emergency contact is their Father:</h5>
            <table class=\"table mt-3\">
            <thead><tr>
            <th>Employee ID</th>
            <th>Fullname</th>
            <th>Department</th>
            <th>Relationship</th>
            <th>Emergency name</th>
            </tr></thead><tbody>";
      while ($row = $stmt->fetch()) {
        echo "<tr>";
        echo "<td>" . $row['employee_id'] . "</td>";
        echo "<td>" . $row['fullname'] . "</td>";
        echo "<td>" . $row['department'] . "</td>";
        echo "<td>" . $row['relationship'] . "</td>";
        echo "<td>" . $row['emergency_name'] . "</td>";
        echo "</tr>";
      }
      echo "</tbody></table></div>";
    }
    catch (Exception $e) {
        echo "Failed: " . $e->getMessage();
    }
  }


  function doDisplayBirthday(){
    try{
      $pdo = new pdo("mysql:host=localhost;dbname=kilburn", "kilburnuser", "123");
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

      $sql = "call fetch_employee_with_birthday()";
      $stmt = $pdo->prepare($sql);
      $stmt->execute();
      $stmt->setFetchMode(PDO::FETCH_ASSOC);

      echo "<div class=\"container mt-3\">
            <h5>Employees whose birthday is in the current calendar month:</h5>
            <table class=\"table mt-3\"><thead><tr>
            <th>Employee ID</th>
            <th>Fullname</th>
            <th>DOB</th>
            </tr></thead><tbody>";
      while ($row = $stmt->fetch()) {
        echo "<tr>";
        echo "<td>" . $row['employee_id'] . "</td>";
        echo "<td>" . $row['fullname'] . "</td>";
        echo "<td>" . $row['dob'] . "</td>";
        echo "</tr>";
      }
      echo "</tbody></table></div>";
    }
    catch (Exception $e) {
        echo "Failed: " . $e->getMessage();
    }
  }


  function doDisplayLogs(){
    try{
      $pdo = new pdo("mysql:host=localhost;dbname=kilburn", "kilburnuser", "123");
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

      $sql = "SELECT employee_id, employee_name, deleted_time, operator_id FROM Audit_Log";
      $stmt = $pdo->prepare($sql);
      $stmt->execute();
      $stmt->setFetchMode(PDO::FETCH_ASSOC);

      echo "<div class=\"container mt-3\">
            <h5>Employee Delete Records:</h5>
            <table class=\"table mt-3\"><thead><tr>
            <th>Employee ID</th>
            <th>Fullname</th>
            <th>Deleted Time</th>
            <th>Operator ID</th>
            </tr></thead><tbody>";
      while ($row = $stmt->fetch()) {
        echo "<tr>";
        echo "<td>" . $row['employee_id'] . "</td>";
        echo "<td>" . $row['employee_name'] . "</td>";
        echo "<td>" . $row['deleted_time'] . "</td>";
        echo "<td>" . $row['operator_id'] . "</td>";
        echo "</tr>";
      }
      echo "</tbody></table></div>";
    }
    catch (Exception $e) {
        echo "Failed: " . $e->getMessage();
    }
  }

?>

</body>
</html>
