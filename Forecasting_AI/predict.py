from flask import Flask, jsonify

app = Flask(__name__)

@app.route('/predict', methods=['POST'])
def predict():
    patients = get_patient_data()  # Function to fetch patient data
    results = []

    for patient in patients:
        predicted_disease = predict_disease(patient['conditions'])  # AI prediction function
        save_predictions_to_db(patient['fullname'], predicted_disease)
        results.append({"fullname": patient['fullname'], "predicted_disease": predicted_disease})

    return jsonify(results)
