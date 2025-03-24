import mysql.connector
import pandas as pd
import json

# 1️⃣ Connect to MySQL
db = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="bcp_sms3_cms"
)
cursor = db.cursor(dictionary=True)

# 2️⃣ Fetch latest student symptoms
cursor.execute("SELECT fullname, conditions FROM bcp_sms3_patients ORDER BY created_at DESC LIMIT 5")
students = cursor.fetchall()

# 3️⃣ Load illness dataset
df = pd.read_excel(r"C:\xampp\htdocs\BCP-ClinicManagement\CIPP.xlsx", engine="openpyxl")

# 4️⃣ Define prediction function
def predict_illness(symptoms_list):
    # Convert symptom string to list (comma + space separated)
    symptoms = symptoms_list.split(", ")
    
    # Keep only symptoms that exist in Excel columns
    valid_symptoms = [symptom for symptom in symptoms if symptom in df.columns]

    if not valid_symptoms:
        return ["No illness detected."]  # No matching symptoms

    # Find rows where at least one valid symptom is present
    matches = df[df[valid_symptoms].sum(axis=1) > 0]

    # Return unique illness names or fallback
    return list(set(matches["Illness Name"].tolist())) if not matches.empty else ["No illness detected."]

# 5️⃣ Process predictions for each student
results = []
for idx, student in enumerate(students, start=1):
    illnesses = predict_illness(student["conditions"])
    results.append({
        "patient_id": idx,
        "name": student["fullname"],
        "predicted_illness": ", ".join(illnesses)  # Join multiple illnesses into one string
    })

# 6️⃣ Print as JSON
print(json.dumps(results, ensure_ascii=False))

# 7️⃣ Close DB connection
cursor.close()
db.close()
