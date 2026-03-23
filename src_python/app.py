from flask import Flask, request, jsonify
from flask_cors import CORS

app = Flask(__name__)
CORS(app)

# Knowledge Base: Ideal conditions for crops in India
# Dictionary mapping crop name to optimal (min_pH, max_pH, min_temp, max_temp, min_rain, max_rain, min_N, max_N)
CROP_KNOWLEDGE = {
    "Rice": {"ph": (5.5, 7.0), "temp": (20, 35), "rain": (150, 300), "n": (80, 120), "season": ["Kharif"]},
    "Wheat": {"ph": (6.0, 7.5), "temp": (15, 25), "rain": (50, 100), "n": (60, 90), "season": ["Rabi"]},
    "Cotton": {"ph": (6.0, 8.0), "temp": (21, 30), "rain": (50, 100), "n": (70, 110), "season": ["Kharif"]},
    "Sugarcane": {"ph": (6.5, 7.5), "temp": (21, 27), "rain": (150, 250), "n": (100, 150), "season": ["Kharif", "Zaid"]},
    "Maize": {"ph": (5.5, 7.5), "temp": (21, 27), "rain": (50, 100), "n": (60, 100), "season": ["Kharif", "Rabi"]},
    "Mustard": {"ph": (6.0, 7.5), "temp": (15, 25), "rain": (30, 80), "n": (40, 70), "season": ["Rabi"]},
    "Soybean": {"ph": (6.0, 7.5), "temp": (20, 30), "rain": (60, 100), "n": (20, 40), "season": ["Kharif"]}, 
    "Groundnut": {"ph": (6.0, 6.5), "temp": (25, 30), "rain": (50, 125), "n": (15, 30), "season": ["Kharif", "Zaid"]},
    "Jute": {"ph": (6.0, 7.5), "temp": (24, 35), "rain": (150, 250), "n": (80, 120), "season": ["Kharif"]},
    "Apple": {"ph": (5.8, 7.0), "temp": (15, 24), "rain": (100, 125), "n": (50, 80), "season": ["Rabi"]},
    "Banana": {"ph": (6.5, 7.5), "temp": (26, 30), "rain": (150, 250), "n": (100, 140), "season": ["Kharif", "Zaid"]},
    "Grapes": {"ph": (6.5, 7.5), "temp": (15, 40), "rain": (50, 90), "n": (80, 100), "season": ["Zaid"]},
    "Watermelon": {"ph": (6.0, 7.0), "temp": (25, 35), "rain": (40, 60), "n": (50, 70), "season": ["Zaid"]},
    "Mango": {"ph": (5.5, 7.5), "temp": (24, 27), "rain": (100, 250), "n": (80, 120), "season": ["Zaid"]},
    "Potato": {"ph": (5.0, 6.5), "temp": (15, 20), "rain": (50, 100), "n": (80, 120), "season": ["Rabi"]},
    "Tomato": {"ph": (6.0, 7.0), "temp": (20, 30), "rain": (40, 60), "n": (60, 90), "season": ["Rabi", "Kharif"]},
    "Onion": {"ph": (6.0, 7.0), "temp": (15, 30), "rain": (60, 80), "n": (40, 60), "season": ["Rabi", "Kharif", "Zaid"]},
    "Coffee": {"ph": (5.5, 6.5), "temp": (15, 28), "rain": (150, 250), "n": (80, 100), "season": ["Kharif"]},
    "Tea": {"ph": (4.5, 5.5), "temp": (20, 30), "rain": (150, 300), "n": (80, 120), "season": ["Kharif"]},
    "Tobacco": {"ph": (5.5, 6.5), "temp": (20, 30), "rain": (50, 100), "n": (80, 100), "season": ["Rabi", "Kharif"]}
}

def calculate_suitability(metric_val, ideal_min, ideal_max):
    if metric_val is None:
        return 100 # Ignore if not provided
        
    if ideal_min <= metric_val <= ideal_max:
        return 100
        
    # Calculate penalty
    if metric_val < ideal_min:
        diff = ideal_min - metric_val
        percent_off = (diff / ideal_min) * 100 if ideal_min != 0 else 100
    else:
        diff = metric_val - ideal_max
        percent_off = (diff / ideal_max) * 100 if ideal_max != 0 else 100
        
    # Subtract penalty from 100
    score = max(0, 100 - (percent_off * 2)) # Multiplier to punish deviations more
    return score

@app.route('/api/recommend', methods=['POST'])
def recommend_crop():
    data = request.json
    
    if not data:
        return jsonify({"status": "error", "message": "No input provided"}), 400
        
    # Extract inputs (support both PHP naming conventions and User prompt concepts)
    state = data.get('state', '')
    
    # Soil parameters
    ph = data.get('soil_ph', data.get('soil_type', None))
    # if soil_type is a string like "Clay", ph will be fallback. We prioritize numerical if available.
    try:
        ph = float(ph) if ph is not None else None
    except ValueError:
        ph = None
        
    nitrogen = data.get('nitrogen', None)
    
    # Water parameters
    rainfall = data.get('rainfall', data.get('water_availability', None))
    try:
        rainfall = float(rainfall) if rainfall is not None else None
    except ValueError:
        rainfall = None
        
    # Temperature/Season parameters
    temp = data.get('temperature', None)
    season_input = data.get('season', None) # E.g., 'Kharif', 'Rabi', 'Zaid'
    
    # Calculate scores for all crops
    results = []
    
    for crop, conditions in CROP_KNOWLEDGE.items():
        # Check season compatibility if season input provided
        if season_input and isinstance(season_input, str):
            if season_input.capitalize() not in conditions['season']:
                # Zero score if completely wrong season
                continue
                
        scores = []
        if ph is not None:
            scores.append(calculate_suitability(ph, conditions["ph"][0], conditions["ph"][1]))
        if temp is not None:
            scores.append(calculate_suitability(temp, conditions["temp"][0], conditions["temp"][1]))
        if rainfall is not None:
            scores.append(calculate_suitability(rainfall, conditions["rain"][0], conditions["rain"][1]))
        if nitrogen is not None:
            scores.append(calculate_suitability(nitrogen, conditions["n"][0], conditions["n"][1]))
            
        if not scores:
            final_score = 50.0 # No numerical data provided at all
        else:
            final_score = sum(scores) / len(scores)
            
        results.append({
            "crop": crop,
            "match_score": round(final_score, 1)
        })
        
    # Sort descending by score
    results.sort(key=lambda x: x["match_score"], reverse=True)
    
    # Return top 3 matches
    top_matches = results[:3]
    
    if len(top_matches) == 0:
        return jsonify({"status": "error", "message": "No suitable crops found for these parameters."}), 404
        
    return jsonify({
        "status": "success",
        "data": top_matches
    })

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=True)
