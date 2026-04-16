from flask import Flask, request, jsonify
from flask_cors import CORS
import mysql.connector

app = Flask(__name__)
CORS(app)

import os
import json
import re

def fetch_crop_knowledge():
    knowledge = {}
    try:
        json_path = os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', 'public', 'assets', 'data', 'crops.json')
        with open(json_path, 'r', encoding='utf-8') as f:
            crops = json.load(f)
            
        for c in crops:
            # Helper to parse strings like "6.0 - 7.5"
            def parse_range(val_str, default_min, default_max):
                if not val_str: return (default_min, default_max)
                numbers = re.findall(r"[-+]?\d*\.\d+|\d+", str(val_str))
                if len(numbers) >= 2:
                    return (float(numbers[0]), float(numbers[1]))
                elif len(numbers) == 1:
                    return (float(numbers[0]), float(numbers[0]))
                return (default_min, default_max)

            knowledge[c.get('name', 'Unknown')] = {
                "ph": parse_range(c.get('ph_range'), 0.0, 14.0),
                "temp": parse_range(c.get('temp_range'), 0.0, 50.0),
                "rain": parse_range(c.get('rainfall_range'), 0.0, 5000.0),
                "n": (0.0, 500.0), # Default fallback as JSON doesn't track nitrogen
                "season": [s.strip() for s in c.get('season', 'All').split(",")] if c.get('season') else ["All"],
                "states": [s.strip() for s in c.get('suitable_states', 'All').split(",")] if c.get('suitable_states') else ["All"]
            }
    except Exception as e:
        print("Failed to load crop knowledge:", e)
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
        if season_input and isinstance(season_input, str) and season_input.lower() != 'all':
            # The frontend sends "Rabi (Winter: Oct - Mar)" instead of "Rabi"
            # We extract just the main season name: "Rabi"
            base_season = season_input.split(' ')[0].capitalize()
            # Also handle if the crop season in db is completely lowercase
            crop_seasons_normalized = [s.capitalize() for s in conditions['season']]
            if base_season not in crop_seasons_normalized and "All" not in crop_seasons_normalized:
                # Zero score if completely wrong season
                continue
                
        # Evaluate strict state compatibility mapping
        if state and isinstance(state, str) and state.lower() != 'all':
            allowed_states = conditions.get('states', ['All'])
            # Since the database has incomplete state data (many states like Goa are missing),
            # we will not strictly drop the crop. 
            # We can softly match it or simply allow the environmental parameters to dictate suitability.
            state_matched = any(state.lower() in s.lower() or s.lower() == 'all' for s in allowed_states)
            if not state_matched:
                # Apply a minor penalty instead of completely eliminating the crop
                # This ensures users in states not listed in the JSON still get recommendations
                scores = [80] # Start with a lower baseline score due to regional unlikelihood
            else:
                scores = []
        else:
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
