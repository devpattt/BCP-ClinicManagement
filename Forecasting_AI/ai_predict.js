import { trainNaiveBayes, predictIllness } from "./ai_model.js";

// Fetch data from PHP
fetch("SDforecastingai.php")
    .then(response => response.json())
    .then(data => {
        const { patients, illnesses } = data;

        if (!patients.length || !illnesses.length) {
            document.getElementById("predictionResult").innerText = "No patient or illness data available.";
            return;
        }

        // Convert illness data into a format AI can use
        let trainingData = illnesses.map(entry => ({
            illness_name: entry.illness_name,
            symptoms: entry.symptoms.toLowerCase()
        }));

        // Train AI Model
        const model = trainNaiveBayes(trainingData);

        // Predict illness for each patient
        let output = "<h3>Predicted Illnesses</h3><ul>";
        patients.forEach(patient => {
            let symptoms = patient.symptoms.toLowerCase().split(", ").map(symptom => symptom.trim());
            let predictedIllness = predictIllness(symptoms, model);

            output += `<li><b>${patient.fullname}</b>: ${predictedIllness}</li>`;
        });
        output += "</ul>";

        document.getElementById("predictionResult").innerHTML = output;
    })
    .catch(error => console.error("Error fetching data:", error));
