import csv
import MySQLdb

db = MySQLdb.connect(host="localhost", user="kilburnuser", password="123",database="kilburn")
cursor = db.cursor()

sql2 = "INSERT INTO Employee(employee_id, fullname, address,salary,dob, nin, department) " \
       "VALUES (%s, %s, %s, %s, %s, %s, %s)"
sql3 = "INSERT INTO Emergency_Contact(emergency_name, relationship, emergency_phone, employee_id) " \
       "VALUES (%s, %s, %s, %s)"

with open("employees.csv", encoding="latin-1") as f:
    reader = csv.reader(f)
    next(reader)

    for row in reader:
        salary = row[3][1:]
        salary = salary.replace(",", "")
        day, month, year = row[4].split("/")
        dob = year + "-" + month + "-" + day
        cursor.execute(sql2, (row[0], row[1], row[2], salary, dob, row[5], row[6]))
        cursor.execute(sql3, (row[7], row[8], row[9], row[0]))

    db.commit()

db.close()