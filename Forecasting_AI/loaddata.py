import pandas as pd

df = pd.read_csv("C:/xampp/htdocs/BCP-ClinicManagement/Forecasting_AI/dands.csv")

print("Missing values:\n", df.isnull().sum())

symptom_columns = [col for col in df.columns if col not in ['diseases']]  

df = pd.get_dummies(df, columns=symptom_columns)

print(df.head())
