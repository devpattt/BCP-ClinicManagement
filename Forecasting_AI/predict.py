import joblib
import numpy as np

model = joblib.load("health_risk_model.pkl")

new_patient = np.array([[1, 1, 0, 1]])  

prediction = model.predict(new_patient)

if prediction[0] == 1:
    print("⚠️ High risk detected! Advise medical check-up.")
else:
    print("✅ Low risk. No immediate concern.")
