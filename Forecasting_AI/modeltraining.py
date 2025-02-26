import pandas as pd
import numpy as np
from sklearn.model_selection import train_test_split
from sklearn.ensemble import RandomForestClassifier
from sklearn.metrics import accuracy_score
import joblib

#Sample datasets
data = {
    "headache": [1, 0, 1, 1, 0, 0, 1, 1, 0, 1],
    "dizziness": [1, 0, 1, 0, 1, 0, 1, 1, 1, 0],
    "fatigue": [0, 1, 1, 0, 1, 1, 0, 1, 0, 1],
    "fever": [1, 0, 0, 1, 1, 0, 1, 0, 1, 1],
    "risk": [1, 0, 1, 0, 1, 0, 1, 1, 0, 1]  # 1 = High Risk, 0 = Low Risk
}

df = pd.DataFrame(data)

X = df.drop("risk", axis=1) 
y = df["risk"] 

X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

model = RandomForestClassifier(n_estimators=100, random_state=42)
model.fit(X_train, y_train)

y_pred = model.predict(X_test)
accuracy = accuracy_score(y_test, y_pred)

print(f"Model Accuracy: {accuracy:.2f}")

joblib.dump(model, "health_risk_model.pkl")
