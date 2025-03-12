import mysql.connector

conn = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="bcp_sms3_cms"
)
cursor = conn.cursor(dictionary=True)
cursor.execute("SELECT fullname, conditions FROM bcp_sms3_symptoms")
patients = cursor.fetchall()
conn.close()

print(patients)  # Should print patient records
