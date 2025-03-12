@echo off
cd /d C:\xampp\htdocs\BCP-ClinicManagement\Forecasting_AI
python -m waitress --listen=0.0.0.0:5000 forecast:app
