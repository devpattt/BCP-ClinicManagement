import unittest
import pandas as pd
import sys
import os

# Add the parent directory of Forecasting_AI to the Python path
sys.path.insert(0, os.path.abspath(os.path.join(os.path.dirname(__file__), '..')))

from Forecasting_AI.forecast import load_disease_data

class TestForecast(unittest.TestCase):
    def test_load_disease_data(self):
        # Load the disease data
        data = load_disease_data()
        
        # Check if the data is loaded correctly
        self.assertIsNotNone(data, "Data should not be None")
        
        # Check if the required columns are present
        required_columns = ['disease', 'symptoms']
        for column in required_columns:
            self.assertIn(column, data.columns, f"Missing required column: {column}")

if __name__ == '__main__':
    unittest.main()