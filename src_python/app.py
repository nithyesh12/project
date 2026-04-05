from flask import Flask, request, jsonify
from flask_cors import CORS
import mysql.connector

app = Flask(__name__)
CORS(app)

def fetch_crop_knowledge():
    knowledge = {}
    try:
        conn = mysql.connector.connect(
            host="localhost",
            user="root",
            password="",
            database="growyourcrops"
        )
        cursor = conn.cursor(dictionary=True)
        cursor.execute("SELECT * FROM crops")
        crops = cursor.fetchall()
        for c in crops:
            knowledge[c['crop_name']] = {
                "ph": (c['ph_min'] if c['ph_min'] is not None else 0.0, c['ph_max'] if c['ph_max'] is not None else 14.0),
                "temp": (c['temp_min'] if c['temp_min'] is not None else 0.0, c['temp_max'] if c['temp_max'] is not None else 50.0),
                "rain": (c['rain_min'] if c['rain_min'] is not None else 0.0, c['rain_max'] if c['rain_max'] is not None else 5000.0),
                "n": (c['n_min'] if c['n_min'] is not None else 0.0, c['n_max'] if c['n_max'] is not None else 500.0),
                "season": [s.strip() for s in c['seasons'].split(",")] if c['seasons'] else ["All"],
                "states": [s.strip() for s in c['states'].split(",")] if c['states'] else ["All"]
            }
        cursor.close()
        conn.close()
    except Exception as e:
        print("Database connection failed:", e)
    return knowledge

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
    
    CROP_KNOWLEDGE = fetch_crop_knowledge()
    
    for crop, conditions in CROP_KNOWLEDGE.items():
        # Check season compatibility if season input provided
        if season_input and isinstance(season_input, str):
            if season_input.capitalize() not in conditions['season']:
                # Zero score if completely wrong season
                continue
                
        # Evaluate strict state compatibility mapping
        if state and isinstance(state, str):
            allowed_states = conditions.get('states', ['All'])
            if "All" not in allowed_states and state not in allowed_states:
                # Highly incompatible regional geographic crop
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
            "season": season_input.capitalize() if season_input else "/".join(conditions["season"]),
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
