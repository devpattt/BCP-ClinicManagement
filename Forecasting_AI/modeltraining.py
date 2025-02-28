import pandas as pd
import joblib
from sqlalchemy import create_engine

model = joblib.load("health_risk_model.pkl")

engine = create_engine('mysql+pymysql://root:@localhost:4306/bcp_sms3_cms')

query = "SELECT headache, dizziness, fatigue, fever FROM bcp_sms3_symptoms"
df = pd.read_sql(query, engine)

predictions = model.predict(df)

df['predicted_risk'] = predictions

risk_to_illness = {
    0: 'No illness',
    1: 'Common Cold',
    2: 'Flu',
    3: 'Migraine',
}

df['illness'] = df['predicted_risk'].map(risk_to_illness)

print("Predictions from Database:")
print(df)

csv_data = pd.read_csv("dands.csv")

csv_data = csv_data[["headache", "dizziness", "fatigue", "fever"]]

csv_predictions = model.predict(csv_data)

csv_data['predicted_risk'] = csv_predictions

csv_data['illness'] = csv_data['predicted_risk'].map(risk_to_illness)

print("Predictions from CSV:")
print(csv_data)