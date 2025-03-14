from flask import Flask, request, jsonify
import mysql.connector
import joblib  # For loading AI model
import numpy as np
import pandas as pd
from difflib import SequenceMatcher
import os

app = Flask(__name__)

# Database Configuration
db_config = {
    "host": "localhost",
    "user": "root",
    "password": "",
    "database": "bcp_sms3_cms"
}

def connect_db():
    return mysql.connector.connect(**db_config)

# Load AI Model
try:
    model = joblib.load("health_risk_model.pkl")  # Load trained model
    vectorizer = joblib.load("vectorizer.pkl")  # Load symptom vectorizer
except Exception as e:
    print(f"Error loading model: {e}")
    model = None
    vectorizer = None

# Normalize text
def normalize_text(text):
    return text.lower().strip() if isinstance(text, str) else ""

# Get similarity score
def get_similarity(a, b):
    return SequenceMatcher(None, normalize_text(a), normalize_text(b)).ratio()

# Fetch patients' symptoms
def get_patient_data():
    try:
        conn = connect_db()
        cursor = conn.cursor(dictionary=True)
        cursor.execute("SELECT id, fullname, conditions FROM bcp_sms3_patients")
        patients = cursor.fetchall()
        cursor.close()
        conn.close()
        return patients
    except mysql.connector.Error as e:
        print(f"Error fetching patient data: {e}")
        return []

# Load Disease Dataset
def load_disease_data():
    file_path = os.path.abspath("CIP.xlsx")
    if not os.path.exists(file_path):
        print(f"Error: File {file_path} not found.")
        return None
    
    try:
        data = pd.read_excel(file_path, engine='openpyxl')
        if 'Illness Name' not in data.columns or 'symptoms' not in data.columns:
            print("Error: Missing required columns in dataset.")
            return None
        data.rename(columns={'Illness Name': 'disease'}, inplace=True)
        return data
    except Exception as e:
        print(f"Error loading disease data: {e}")
        return None

# Predict Disease
def predict_disease(patient_symptoms):
    diseases = load_disease_data()
    if diseases is None or diseases.empty:
        return "Disease data unavailable"
    
    best_match = None
    best_score = 0.0
    
    for _, row in diseases.iterrows():
        disease = row['disease']
        symptoms_list = [normalize_text(s) for s in row['symptoms'].split(',') if s]
        
        if not symptoms_list:
            continue  
        
        match_score = sum(get_similarity(patient_symptoms, s) for s in symptoms_list) / len(symptoms_list)
        if match_score > best_score:
            best_score = match_score
            best_match = disease
    
    return best_match if best_score > 0.5 else "No clear illness detected"

# Save Predictions to MySQL
def save_predictions_to_db(patient_id, fullname, predicted_disease, confidence_score):
    try:
        conn = connect_db()
        cursor = conn.cursor()
        cursor.execute(
            "INSERT INTO bcp_sms3_predictions (patient_id, fullname, predicted_condition, confidence_score) VALUES (%s, %s, %s, %s)",
            (patient_id, fullname, predicted_disease, confidence_score)
        )
        conn.commit()
        cursor.close()
        conn.close()
    except mysql.connector.Error as err:
        print(f"Database error: {err}")

# API for Predictions
@app.route('/predict', methods=['GET'])
def predict():
    patients = get_patient_data()
    if not patients:
        return jsonify({"error": "No patient data found"}), 404

    results = []
    for patient in patients:
        predicted_disease = predict_disease(patient['conditions'])
        confidence_score = 0.85  # Placeholder confidence score
        save_predictions_to_db(patient['id'], patient['fullname'], predicted_disease, confidence_score)
        results.append({
            "fullname": patient['fullname'],
            "predicted_disease": predicted_disease,
            "confidence_score": confidence_score
        })

    return jsonify(results)

# Health Check Endpoint
@app.route('/health', methods=['GET'])
def health_check():
    return jsonify({"status": "API is running"}), 200

if __name__ == '__main__':
    app.run(host="0.0.0.0", port=5000, debug=True)
