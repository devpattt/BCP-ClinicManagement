import pandas as pd

df = pd.read_csv("C:/xampp/htdocs/BCP-ClinicManagement/Forecasting_AI/dands.csv")

symptom_columns = df.columns.tolist()
print("All symptoms and target column:\n", symptom_columns)

symptom_columns_only = [col for col in df.columns if col != 'diseases']
print("All symptoms:\n", symptom_columns_only)

print("Missing values:\n", df.isnull().sum())

df = pd.get_dummies(df, columns=symptom_columns_only)

print(df.head())