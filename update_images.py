import json

with open('public/assets/data/crops.json', 'r', encoding='utf-8') as f:
    crops = json.load(f)

sql_statements = []

for crop in crops:
    name = crop['name'].replace("'", "''")
    image_url = crop.get('image_url', 'assets/images/crops/default.jpg').replace("'", "''")
    
    query = f"UPDATE crops SET image_url = '{image_url}' WHERE crop_name = '{name}';"
    sql_statements.append(query)

with open('update_images.sql', 'w', encoding='utf-8') as f:
    f.write("\n".join(sql_statements))
    
print("SQL update file generated successfully.")
