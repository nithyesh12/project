import json

# Load crops to ensure we match names
with open('public/assets/data/crops.json', 'r', encoding='utf-8') as f:
    crops = json.load(f)
    
crop_names = [c['name'] for c in crops]

cosmetic_knowledge = [
    # Already added: Turmeric, Aloe Vera, Coconut, Neem, Papaya, Sandalwood, Rose, Rice
    ("Wheat", "Skin Care", "Contains wheat germ oil which is rich in Vitamin E, prevents aging and deeply nourishes skin.", "Oil Extract, Flour mask"),
    ("Maize", "Skin Care", "Cornstarch absorbs excess oil and reduces acne. Corn oil hydrates the skin.", "Powder (Cornstarch), Oil"),
    ("Pearl Millet", "Skin Care", "Rich in antioxidants, helps prevent early aging and improves skin texture.", "Exfoliating Scrub"),
    ("Chickpeas", "Skin Care", "Besan (Gram Flour) is a traditional cleanser that brightens skin and removes tan.", "Besan Paste/Mask"),
    ("Soybean", "Skin Care", "Contains aglycones which help to reduce wrinkles and improve skin elasticity.", "Oil, Soy Milk Extract"),
    ("Peanuts", "Skin Care", "Peanut oil contains Vitamin E which protects the skin from free radicals.", "Oil Massage"),
    ("Mustard", "Hair Care", "Mustard oil stimulates hair growth, prevents hair loss, and acts as a natural conditioner.", "Hair Oil Mapping"),
    ("Sunflower", "Skin Care", "Sunflower seed oil is non-comedogenic and highly absorbent, excellent for acne-prone skin.", "Seed Oil"),
    ("Sesame", "Skin Care", "Deeply penetrates the skin, providing warming moisture and healing rough patches.", "Massage Oil"),
    ("Linseed", "Skin Care", "Flaxseed (Linseed) gel provides Omega-3 fatty acids that soothe redness and irritation.", "Gel, Seed Oil"),
    ("Castor", "Hair Care", "Castor oil is legendary for thickening hair, eyebrows, and eyelashes.", "Thick Oil Application"),
    ("Sugarcane", "Skin Care", "Contains glycolic acid, a natural AHA that exfoliates dead skin cells.", "Juice Extract, Sugar Scrub"),
    ("Tea", "Skin Care", "Green and black tea extracts reduce puffiness around eyes and neutralize free radicals.", "Extract, Cold Bags"),
    ("Coffee", "Skin Care", "Caffeine improves blood flow, reduces cellulite, and exfoliates dead skin cells.", "Coffee Grounds Scrub"),
    ("Cashew", "Skin Care", "Cashew nut oil is used to treat fungal infections and cracked heels.", "Oil"),
    ("Cardamom", "Skin Care", "Has antibacterial properties that help in healing breakouts and acts as a skin purifier.", "Essential Oil"),
    ("Clove", "Medicinal", "Clove oil is highly anti-microbial and is used as a spot treatment for cystic acne.", "Diluted Essential Oil"),
    ("Ginger", "Skin Care", "Improves skin elasticity and evens skin tone through its antioxidant compound gingerol.", "Juice Extract"),
    ("Garlic", "Medicinal", "Strong antimicrobial used overnight to flatten severe pimples and reduce fungal infections.", "Crushed Paste (Spot use)"),
    ("Mango", "Skin Care", "Mango butter is highly nourishing, providing deep hydration without clogging pores.", "Fruit Butter"),
    ("Banana", "Hair Care", "Banana mash makes an excellent conditioning hair mask, reducing frizz.", "Mashed Fruit paste"),
    ("Citrus", "Skin Care", "Rich in Vitamin C, treats dark spots and naturally brightens skin tone.", "Juice Extract, Peel Powder"),
    ("Apple", "Skin Care", "Apple Cider Vinegar is used as a natural skin toner to balance pH.", "Vinegar, Extract"),
    ("Grapes", "Skin Care", "Grape seed oil is ultra-lightweight and fights acne while moisturizing.", "Seed Oil"),
    ("Potato", "Skin Care", "Potato juice naturally bleaches dark spots, under-eye circles, and hyperpigmentation.", "Raw Juice"),
    ("Tomato", "Skin Care", "Natural astringent with lycopene, helps shrink pores and treats sunburn.", "Pulp/Juice"),
    ("Onion", "Hair Care", "Onion juice is extremely potent for reversing hair fall and promoting regrowth.", "Juice extract"),
    ("Cucumber", "Skin Care", "Instantly cools the skin, reduces eye puffiness, and tightens pores.", "Slices, Juice"),
    ("Pumpkin", "Skin Care", "Contains fruit enzymes and AHAs that increase cell turnover for glowing skin.", "Pureed Mask"),
    ("Nutmeg", "Skin Care", "Reduces inflammation and gently exfoliates when mixed with honey.", "Powder"),
    ("Cinnamon", "Skin Care", "Plumps skin and lips by bringing blood to the surface. Strong antibacterial.", "Powder Extract"),
    ("Mint", "Skin Care", "Menthol provides a cooling sensation that relieves itchy skin and tightens pores.", "Crushed Leaves, Oil"),
    ("Almond", "Skin Care", "Sweet almond oil gently dislodges debris from pores and retains moisture.", "Oil, Crushed Paste"),
    ("Walnut", "Skin Care", "Finely crushed shells are used as a physical body exfoliator.", "Crushed Shells, Oil"),
    ("Vanilla", "Skin Care", "B-vitamins help maintain healthy skin, and its scent acts as a natural relaxant.", "Essential Oil Extract"),
    ("Saffron", "Skin Care", "A luxurious spice that deeply brightens the complexion and reduces pigmentation.", "Milk infused paste"),
    ("Watermelon", "Skin Care", "Provides intense, refreshing hydration for oily and combination skin types.", "Juice/Extract")
]

sql_statements = []

sql_statements.append("INSERT IGNORE INTO crops (crop_name, image_url) VALUES ")
insert_crops = []
# Ensure crops are in the database first
for name, cat, ben, method in cosmetic_knowledge:
    insert_crops.append(f"('{name}', 'assets/images/crops/default.jpg')")

sql_statements.append(", ".join(insert_crops) + ";\n")

for name, cat, ben, method in cosmetic_knowledge:
    ben = ben.replace("'", "''")
    method = method.replace("'", "''")
    query = f"""
    INSERT INTO crop_cosmetics (crop_id, category, benefits, usage_method)
    SELECT id, '{cat}', '{ben}', '{method}'
    FROM crops WHERE crop_name = '{name}'
    AND NOT EXISTS (SELECT 1 FROM crop_cosmetics WHERE crop_id = crops.id AND category = '{cat}');
    """
    sql_statements.append(query)

with open('database_cosmetics_more.sql', 'w', encoding='utf-8') as f:
    f.write("\n".join(sql_statements))
    
print("SQL file generated successfully.")
