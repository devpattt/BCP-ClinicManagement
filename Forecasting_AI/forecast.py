import mysql.connector
import pandas as pd
from flask import Flask, request, jsonify
from difflib import SequenceMatcher
from mysql.connector import pooling
import os

app = Flask(__name__)

# Database configuration
DB_CONFIG = {
    "host": "localhost",
    "port": 4306, 
    "user": os.getenv("DB_USER", "root"),  
    "password": os.getenv("DB_PASSWORD", ""),
    "database": "bcp_sms3_cms"
}

# Create a connection pool
db_pool = None
try:
    db_pool = pooling.MySQLConnectionPool(pool_name="mypool", pool_size=5, **DB_CONFIG)
except mysql.connector.Error as err:
    print(f"Error creating connection pool: {err}")

# Get database connection
def get_db_connection():
    if db_pool is None:
        print("Connection pool is not available")
        return None
    try:
        return db_pool.get_connection()
    except mysql.connector.Error as err:
        print(f"Error getting connection from pool: {err}")
        return None

# Fetch patient data
def get_patient_data():
    try:
        conn = get_db_connection()
        if conn is None:
            return []  # Return an empty list if no connection

        cursor = conn.cursor(dictionary=True)
        cursor.execute("SELECT fullname, conditions FROM bcp_sms3_symptoms")
        patients = cursor.fetchall()
        cursor.close()
        conn.close()
        return patients
    except Exception as e:
        print(f"Error fetching patient data: {e}")
        return []

# Load disease dataset (CIP.csv)
def load_disease_data():
    return pd.read_csv("CIP.csv")

# Normalize symptoms for better matching
def normalize_text(text):
    return text.lower().strip()

# Similarity function
def get_similarity(a, b):
    return SequenceMatcher(None, normalize_text(a), normalize_text(b)).ratio()

# Predict illness
def predict_disease(patient_symptoms):
    diseases = load_disease_data()
    best_match = None
    best_score = 0.0

    for _, row in diseases.iterrows():
        disease = row['disease']
        symptoms_list = [normalize_text(s) for s in row['symptoms'].split(',')]

        # Calculate similarity score
        match_score = sum(get_similarity(patient_symptoms, s) for s in symptoms_list) / len(symptoms_list)

        if match_score > best_score:
            best_score = match_score
            best_match = disease

    return best_match if best_score > 0.5 else "No clear illness detected"

# Store predictions in MySQL
def save_predictions_to_db(fullname, predicted_disease):
    try:
        conn = get_db_connection()
        if conn is None:
            return
        cursor = conn.cursor()
        cursor.execute("INSERT INTO bcp_sms3_predictions (fullname, predicted_disease) VALUES (%s, %s)",
                       (fullname, predicted_disease))
        conn.commit()
    except mysql.connector.Error as err:
        print(f"Database error: {err}")
    finally:
        if cursor:
            cursor.close()
        if conn:
            conn.close()

# API endpoint to get predictions
@app.route('/predict', methods=['GET'])
def predict():
    patients = get_patient_data()
    results = []

    for patient in patients:
        predicted_disease = predict_disease(patient['conditions'])
        save_predictions_to_db(patient['fullname'], predicted_disease)
        results.append({"fullname": patient['fullname'], "predicted_disease": predicted_disease})

    return jsonify(results)

if __name__ == '__main__':
    app.run(host="0.0.0.0", port=5000, debug=True)