import mysql.connector
import pandas as pd
from flask import Flask, request, jsonify
from difflib import SequenceMatcher

app = Flask(__name__)

# Connect to MySQL database
def get_patient_data():
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
    return patients

# Load disease dataset (CIP.csv)
def load_disease_data():
    df = pd.read_csv("CIP.csv")
    return df

# Similarity function
def get_similarity(a, b):
    return SequenceMatcher(None, a, b).ratio()

# Predict illness
def predict_disease(patient_symptoms):
    diseases = load_disease_data()
    best_match = None
    best_score = 0.0

    for _, row in diseases.iterrows():
        disease = row['disease']
        symptoms_list = row['symptoms'].split(',')

        # Calculate similarity score
        match_score = sum(get_similarity(patient_symptoms, s) for s in symptoms_list) / len(symptoms_list)

        if match_score > best_score:
            best_score = match_score
            best_match = disease

    return best_match if best_score > 0.5 else "No clear illness detected"

# Store predictions in MySQL
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

# API endpoint to get predictions
@app.route('/predict', methods=['POST'])
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
