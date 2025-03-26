function trainNaiveBayes(data) {
    let illnessCounts = {};
    let symptomCounts = {};
    let totalIllnesses = data.length;

    data.forEach(entry => {
        let illness = entry.illness_name || "Unknown";
        illnessCounts[illness] = (illnessCounts[illness] || 0) + 1;

        let symptoms = entry.symptoms ? entry.symptoms.split(", ") : [];
        symptoms.forEach(symptom => {
            if (!symptomCounts[illness]) symptomCounts[illness] = {};
            symptomCounts[illness][symptom] = (symptomCounts[illness][symptom] || 0) + 1;
        });
    });

    return { illnessCounts, symptomCounts, totalIllnesses };
}

function predictIllness(symptoms, model) {
    let bestIllness = null;
    let bestProbability = 0;

    Object.keys(model.illnessCounts).forEach(illness => {
        let illnessProbability = model.illnessCounts[illness] / model.totalIllnesses;

        symptoms.forEach(symptom => {
            let symptomFrequency = (model.symptomCounts[illness]?.[symptom] || 0.01) / model.illnessCounts[illness];
            illnessProbability *= symptomFrequency;
        });

        if (illnessProbability > bestProbability) {
            bestProbability = illnessProbability;
            bestIllness = illness;
        }
    });

    return bestIllness || "Unknown Illness - Further Check Needed";
}

export { trainNaiveBayes, predictIllness };
