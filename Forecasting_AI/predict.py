from flask import Flask, request, jsonify
import mysql.connector
import joblib  # For loading AI model
import numpy as np

app = Flask(__name__)

# ✅ Connect to MySQL
def connect_db():
    return mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="bcp_sms3_cms"
    )

# ✅ Fetch Patient Data from MySQL
def get_patient_data():
    conn = connect_db()
    cursor = conn.cursor(dictionary=True)
    cursor.execute("SELECT fullname, conditions FROM bcp_sms3_patients")
    patients = cursor.fetchall()
    cursor.close()
    conn.close()
    return patients

# ✅ Load AI Model (Make sure you trained & saved it first)
model = joblib.load("health_risk_model.pkl")  # Load trained model
vectorizer = joblib.load("vectorizer.pkl")  # Load symptom vectorizer

# ✅ Predict Disease Based on Symptoms
def predict_disease(symptoms):
    symptoms_vectorized = vectorizer.transform([symptoms])  # Convert text to numbers
    prediction = model.predict(symptoms_vectorized)  # Get AI prediction
    return prediction[0]  # Return predicted condition

# ✅ Save Prediction to MySQL
def save_predictions_to_db(fullname, predicted_disease):
    conn = connect_db()
    cursor = conn.cursor()
    sql = "INSERT INTO bcp_sms3_predictions (fullname, patient_id, predicted_condition) VALUES (%s, %s,  %s)"
    values = (fullname, predicted_disease)
    cursor.execute(sql, values)
    conn.commit()
    cursor.close()
    conn.close()

# ✅ API Route for Predictions
@app.route('/predict', methods=['GET'])
def predict():
    patients = get_patient_data()
    if not patients:
        return jsonify({"error": "No patient data found"}), 404

    results = []
    for patient in patients:
        predicted_disease = predict_disease(patient['conditions'])
        confidence_score = 0.8  # Replace with actual confidence calculation
        save_predictions_to_db(patient, predicted_disease, confidence_score)
        results.append({
            "fullname": patient['fullname'], 
            "predicted_disease": predicted_disease, 
            "confidence_score": confidence_score
        })

    return jsonify(results)
