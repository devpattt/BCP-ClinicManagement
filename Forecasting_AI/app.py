def save_predictions_to_db(patient_id, symptoms, predicted_condition, confidence_score, status="New"):
    conn = mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="bcp_sms3_cms"
    )
    cursor = conn.cursor()
    sql = """INSERT INTO bcp_sms3_predictions 
             (patient_id, symptoms, predicted_condition, confidence_score, status) 
             VALUES (%s, %s, %s, %s, %s)"""
    values = (patient_id, symptoms, predicted_condition, confidence_score, status)
    cursor.execute(sql, values)
    conn.commit()
    conn.close()
