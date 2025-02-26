import pandas as pd

# Load dataset
df = pd.read_csv("C:/xampp/htdocs/BCP-ClinicManagement/Forecasting_AI/dands.csv")


# Check for missing values
print("Missing values:\n", df.isnull().sum())

# Automatically detect all symptom-related columns
symptom_columns = [col for col in df.columns if col not in ['diseases']]  # Exclude non-symptom columns

# Apply get_dummies
df = pd.get_dummies(df, columns=symptom_columns)

# Check result
print(df.head())
