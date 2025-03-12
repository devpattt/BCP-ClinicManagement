import mysql.connector

def save_predictions_to_db(fullname, predicted_disease):
    conn = mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="bcp_sms3_cms"
    )
    cursor = conn.cursor()
    cursor.execute("INSERT INTO bcp_sms3_predictions (fullname, predicted_disease) VALUES (%s, %s)",
                   (fullname, predicted_disease))
    conn.commit()
    conn.close()
