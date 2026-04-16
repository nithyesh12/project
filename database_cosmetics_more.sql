INSERT IGNORE INTO crops (crop_name, image_url) VALUES 
('Wheat', 'assets/images/crops/default.jpg'), ('Maize', 'assets/images/crops/default.jpg'), ('Pearl Millet', 'assets/images/crops/default.jpg'), ('Chickpeas', 'assets/images/crops/default.jpg'), ('Soybean', 'assets/images/crops/default.jpg'), ('Peanuts', 'assets/images/crops/default.jpg'), ('Mustard', 'assets/images/crops/default.jpg'), ('Sunflower', 'assets/images/crops/default.jpg'), ('Sesame', 'assets/images/crops/default.jpg'), ('Linseed', 'assets/images/crops/default.jpg'), ('Castor', 'assets/images/crops/default.jpg'), ('Sugarcane', 'assets/images/crops/default.jpg'), ('Tea', 'assets/images/crops/default.jpg'), ('Coffee', 'assets/images/crops/default.jpg'), ('Cashew', 'assets/images/crops/default.jpg'), ('Cardamom', 'assets/images/crops/default.jpg'), ('Clove', 'assets/images/crops/default.jpg'), ('Ginger', 'assets/images/crops/default.jpg'), ('Garlic', 'assets/images/crops/default.jpg'), ('Mango', 'assets/images/crops/default.jpg'), ('Banana', 'assets/images/crops/default.jpg'), ('Citrus', 'assets/images/crops/default.jpg'), ('Apple', 'assets/images/crops/default.jpg'), ('Grapes', 'assets/images/crops/default.jpg'), ('Potato', 'assets/images/crops/default.jpg'), ('Tomato', 'assets/images/crops/default.jpg'), ('Onion', 'assets/images/crops/default.jpg'), ('Cucumber', 'assets/images/crops/default.jpg'), ('Pumpkin', 'assets/images/crops/default.jpg'), ('Nutmeg', 'assets/images/crops/default.jpg'), ('Cinnamon', 'assets/images/crops/default.jpg'), ('Mint', 'assets/images/crops/default.jpg'), ('Almond', 'assets/images/crops/default.jpg'), ('Walnut', 'assets/images/crops/default.jpg'), ('Vanilla', 'assets/images/crops/default.jpg'), ('Saffron', 'assets/images/crops/default.jpg'), ('Watermelon', 'assets/images/crops/default.jpg');


    INSERT INTO crop_cosmetics (crop_id, category, benefits, usage_method)
    SELECT id, 'Skin Care', 'Contains wheat germ oil which is rich in Vitamin E, prevents aging and deeply nourishes skin.', 'Oil Extract, Flour mask'
    FROM crops WHERE crop_name = 'Wheat'
    AND NOT EXISTS (SELECT 1 FROM crop_cosmetics WHERE crop_id = crops.id AND category = 'Skin Care');
    

    INSERT INTO crop_cosmetics (crop_id, category, benefits, usage_method)
    SELECT id, 'Skin Care', 'Cornstarch absorbs excess oil and reduces acne. Corn oil hydrates the skin.', 'Powder (Cornstarch), Oil'
    FROM crops WHERE crop_name = 'Maize'
    AND NOT EXISTS (SELECT 1 FROM crop_cosmetics WHERE crop_id = crops.id AND category = 'Skin Care');
    

    INSERT INTO crop_cosmetics (crop_id, category, benefits, usage_method)
    SELECT id, 'Skin Care', 'Rich in antioxidants, helps prevent early aging and improves skin texture.', 'Exfoliating Scrub'
    FROM crops WHERE crop_name = 'Pearl Millet'
    AND NOT EXISTS (SELECT 1 FROM crop_cosmetics WHERE crop_id = crops.id AND category = 'Skin Care');
    

    INSERT INTO crop_cosmetics (crop_id, category, benefits, usage_method)
    SELECT id, 'Skin Care', 'Besan (Gram Flour) is a traditional cleanser that brightens skin and removes tan.', 'Besan Paste/Mask'
    FROM crops WHERE crop_name = 'Chickpeas'
    AND NOT EXISTS (SELECT 1 FROM crop_cosmetics WHERE crop_id = crops.id AND category = 'Skin Care');
    

    INSERT INTO crop_cosmetics (crop_id, category, benefits, usage_method)
    SELECT id, 'Skin Care', 'Contains aglycones which help to reduce wrinkles and improve skin elasticity.', 'Oil, Soy Milk Extract'
    FROM crops WHERE crop_name = 'Soybean'
    AND NOT EXISTS (SELECT 1 FROM crop_cosmetics WHERE crop_id = crops.id AND category = 'Skin Care');
    

    INSERT INTO crop_cosmetics (crop_id, category, benefits, usage_method)
    SELECT id, 'Skin Care', 'Peanut oil contains Vitamin E which protects the skin from free radicals.', 'Oil Massage'
    FROM crops WHERE crop_name = 'Peanuts'
    AND NOT EXISTS (SELECT 1 FROM crop_cosmetics WHERE crop_id = crops.id AND category = 'Skin Care');
    

    INSERT INTO crop_cosmetics (crop_id, category, benefits, usage_method)
    SELECT id, 'Hair Care', 'Mustard oil stimulates hair growth, prevents hair loss, and acts as a natural conditioner.', 'Hair Oil Mapping'
    FROM crops WHERE crop_name = 'Mustard'
    AND NOT EXISTS (SELECT 1 FROM crop_cosmetics WHERE crop_id = crops.id AND category = 'Hair Care');
    

    INSERT INTO crop_cosmetics (crop_id, category, benefits, usage_method)
    SELECT id, 'Skin Care', 'Sunflower seed oil is non-comedogenic and highly absorbent, excellent for acne-prone skin.', 'Seed Oil'
    FROM crops WHERE crop_name = 'Sunflower'
    AND NOT EXISTS (SELECT 1 FROM crop_cosmetics WHERE crop_id = crops.id AND category = 'Skin Care');
    

    INSERT INTO crop_cosmetics (crop_id, category, benefits, usage_method)
    SELECT id, 'Skin Care', 'Deeply penetrates the skin, providing warming moisture and healing rough patches.', 'Massage Oil'
    FROM crops WHERE crop_name = 'Sesame'
    AND NOT EXISTS (SELECT 1 FROM crop_cosmetics WHERE crop_id = crops.id AND category = 'Skin Care');
    

    INSERT INTO crop_cosmetics (crop_id, category, benefits, usage_method)
    SELECT id, 'Skin Care', 'Flaxseed (Linseed) gel provides Omega-3 fatty acids that soothe redness and irritation.', 'Gel, Seed Oil'
    FROM crops WHERE crop_name = 'Linseed'
    AND NOT EXISTS (SELECT 1 FROM crop_cosmetics WHERE crop_id = crops.id AND category = 'Skin Care');
    

    INSERT INTO crop_cosmetics (crop_id, category, benefits, usage_method)
    SELECT id, 'Hair Care', 'Castor oil is legendary for thickening hair, eyebrows, and eyelashes.', 'Thick Oil Application'
    FROM crops WHERE crop_name = 'Castor'
    AND NOT EXISTS (SELECT 1 FROM crop_cosmetics WHERE crop_id = crops.id AND category = 'Hair Care');
    

    INSERT INTO crop_cosmetics (crop_id, category, benefits, usage_method)
    SELECT id, 'Skin Care', 'Contains glycolic acid, a natural AHA that exfoliates dead skin cells.', 'Juice Extract, Sugar Scrub'
    FROM crops WHERE crop_name = 'Sugarcane'
    AND NOT EXISTS (SELECT 1 FROM crop_cosmetics WHERE crop_id = crops.id AND category = 'Skin Care');
    

    INSERT INTO crop_cosmetics (crop_id, category, benefits, usage_method)
    SELECT id, 'Skin Care', 'Green and black tea extracts reduce puffiness around eyes and neutralize free radicals.', 'Extract, Cold Bags'
    FROM crops WHERE crop_name = 'Tea'
    AND NOT EXISTS (SELECT 1 FROM crop_cosmetics WHERE crop_id = crops.id AND category = 'Skin Care');
    

    INSERT INTO crop_cosmetics (crop_id, category, benefits, usage_method)
    SELECT id, 'Skin Care', 'Caffeine improves blood flow, reduces cellulite, and exfoliates dead skin cells.', 'Coffee Grounds Scrub'
    FROM crops WHERE crop_name = 'Coffee'
    AND NOT EXISTS (SELECT 1 FROM crop_cosmetics WHERE crop_id = crops.id AND category = 'Skin Care');
    

    INSERT INTO crop_cosmetics (crop_id, category, benefits, usage_method)
    SELECT id, 'Skin Care', 'Cashew nut oil is used to treat fungal infections and cracked heels.', 'Oil'
    FROM crops WHERE crop_name = 'Cashew'
    AND NOT EXISTS (SELECT 1 FROM crop_cosmetics WHERE crop_id = crops.id AND category = 'Skin Care');
    

    INSERT INTO crop_cosmetics (crop_id, category, benefits, usage_method)
    SELECT id, 'Skin Care', 'Has antibacterial properties that help in healing breakouts and acts as a skin purifier.', 'Essential Oil'
    FROM crops WHERE crop_name = 'Cardamom'
    AND NOT EXISTS (SELECT 1 FROM crop_cosmetics WHERE crop_id = crops.id AND category = 'Skin Care');
    

    INSERT INTO crop_cosmetics (crop_id, category, benefits, usage_method)
    SELECT id, 'Medicinal', 'Clove oil is highly anti-microbial and is used as a spot treatment for cystic acne.', 'Diluted Essential Oil'
    FROM crops WHERE crop_name = 'Clove'
    AND NOT EXISTS (SELECT 1 FROM crop_cosmetics WHERE crop_id = crops.id AND category = 'Medicinal');
    

    INSERT INTO crop_cosmetics (crop_id, category, benefits, usage_method)
    SELECT id, 'Skin Care', 'Improves skin elasticity and evens skin tone through its antioxidant compound gingerol.', 'Juice Extract'
    FROM crops WHERE crop_name = 'Ginger'
    AND NOT EXISTS (SELECT 1 FROM crop_cosmetics WHERE crop_id = crops.id AND category = 'Skin Care');
    

    INSERT INTO crop_cosmetics (crop_id, category, benefits, usage_method)
    SELECT id, 'Medicinal', 'Strong antimicrobial used overnight to flatten severe pimples and reduce fungal infections.', 'Crushed Paste (Spot use)'
    FROM crops WHERE crop_name = 'Garlic'
    AND NOT EXISTS (SELECT 1 FROM crop_cosmetics WHERE crop_id = crops.id AND category = 'Medicinal');
    

    INSERT INTO crop_cosmetics (crop_id, category, benefits, usage_method)
    SELECT id, 'Skin Care', 'Mango butter is highly nourishing, providing deep hydration without clogging pores.', 'Fruit Butter'
    FROM crops WHERE crop_name = 'Mango'
    AND NOT EXISTS (SELECT 1 FROM crop_cosmetics WHERE crop_id = crops.id AND category = 'Skin Care');
    

    INSERT INTO crop_cosmetics (crop_id, category, benefits, usage_method)
    SELECT id, 'Hair Care', 'Banana mash makes an excellent conditioning hair mask, reducing frizz.', 'Mashed Fruit paste'
    FROM crops WHERE crop_name = 'Banana'
    AND NOT EXISTS (SELECT 1 FROM crop_cosmetics WHERE crop_id = crops.id AND category = 'Hair Care');
    

    INSERT INTO crop_cosmetics (crop_id, category, benefits, usage_method)
    SELECT id, 'Skin Care', 'Rich in Vitamin C, treats dark spots and naturally brightens skin tone.', 'Juice Extract, Peel Powder'
    FROM crops WHERE crop_name = 'Citrus'
    AND NOT EXISTS (SELECT 1 FROM crop_cosmetics WHERE crop_id = crops.id AND category = 'Skin Care');
    

    INSERT INTO crop_cosmetics (crop_id, category, benefits, usage_method)
    SELECT id, 'Skin Care', 'Apple Cider Vinegar is used as a natural skin toner to balance pH.', 'Vinegar, Extract'
    FROM crops WHERE crop_name = 'Apple'
    AND NOT EXISTS (SELECT 1 FROM crop_cosmetics WHERE crop_id = crops.id AND category = 'Skin Care');
    

    INSERT INTO crop_cosmetics (crop_id, category, benefits, usage_method)
    SELECT id, 'Skin Care', 'Grape seed oil is ultra-lightweight and fights acne while moisturizing.', 'Seed Oil'
    FROM crops WHERE crop_name = 'Grapes'
    AND NOT EXISTS (SELECT 1 FROM crop_cosmetics WHERE crop_id = crops.id AND category = 'Skin Care');
    

    INSERT INTO crop_cosmetics (crop_id, category, benefits, usage_method)
    SELECT id, 'Skin Care', 'Potato juice naturally bleaches dark spots, under-eye circles, and hyperpigmentation.', 'Raw Juice'
    FROM crops WHERE crop_name = 'Potato'
    AND NOT EXISTS (SELECT 1 FROM crop_cosmetics WHERE crop_id = crops.id AND category = 'Skin Care');
    

    INSERT INTO crop_cosmetics (crop_id, category, benefits, usage_method)
    SELECT id, 'Skin Care', 'Natural astringent with lycopene, helps shrink pores and treats sunburn.', 'Pulp/Juice'
    FROM crops WHERE crop_name = 'Tomato'
    AND NOT EXISTS (SELECT 1 FROM crop_cosmetics WHERE crop_id = crops.id AND category = 'Skin Care');
    

    INSERT INTO crop_cosmetics (crop_id, category, benefits, usage_method)
    SELECT id, 'Hair Care', 'Onion juice is extremely potent for reversing hair fall and promoting regrowth.', 'Juice extract'
    FROM crops WHERE crop_name = 'Onion'
    AND NOT EXISTS (SELECT 1 FROM crop_cosmetics WHERE crop_id = crops.id AND category = 'Hair Care');
    

    INSERT INTO crop_cosmetics (crop_id, category, benefits, usage_method)
    SELECT id, 'Skin Care', 'Instantly cools the skin, reduces eye puffiness, and tightens pores.', 'Slices, Juice'
    FROM crops WHERE crop_name = 'Cucumber'
    AND NOT EXISTS (SELECT 1 FROM crop_cosmetics WHERE crop_id = crops.id AND category = 'Skin Care');
    

    INSERT INTO crop_cosmetics (crop_id, category, benefits, usage_method)
    SELECT id, 'Skin Care', 'Contains fruit enzymes and AHAs that increase cell turnover for glowing skin.', 'Pureed Mask'
    FROM crops WHERE crop_name = 'Pumpkin'
    AND NOT EXISTS (SELECT 1 FROM crop_cosmetics WHERE crop_id = crops.id AND category = 'Skin Care');
    

    INSERT INTO crop_cosmetics (crop_id, category, benefits, usage_method)
    SELECT id, 'Skin Care', 'Reduces inflammation and gently exfoliates when mixed with honey.', 'Powder'
    FROM crops WHERE crop_name = 'Nutmeg'
    AND NOT EXISTS (SELECT 1 FROM crop_cosmetics WHERE crop_id = crops.id AND category = 'Skin Care');
    

    INSERT INTO crop_cosmetics (crop_id, category, benefits, usage_method)
    SELECT id, 'Skin Care', 'Plumps skin and lips by bringing blood to the surface. Strong antibacterial.', 'Powder Extract'
    FROM crops WHERE crop_name = 'Cinnamon'
    AND NOT EXISTS (SELECT 1 FROM crop_cosmetics WHERE crop_id = crops.id AND category = 'Skin Care');
    

    INSERT INTO crop_cosmetics (crop_id, category, benefits, usage_method)
    SELECT id, 'Skin Care', 'Menthol provides a cooling sensation that relieves itchy skin and tightens pores.', 'Crushed Leaves, Oil'
    FROM crops WHERE crop_name = 'Mint'
    AND NOT EXISTS (SELECT 1 FROM crop_cosmetics WHERE crop_id = crops.id AND category = 'Skin Care');
    

    INSERT INTO crop_cosmetics (crop_id, category, benefits, usage_method)
    SELECT id, 'Skin Care', 'Sweet almond oil gently dislodges debris from pores and retains moisture.', 'Oil, Crushed Paste'
    FROM crops WHERE crop_name = 'Almond'
    AND NOT EXISTS (SELECT 1 FROM crop_cosmetics WHERE crop_id = crops.id AND category = 'Skin Care');
    

    INSERT INTO crop_cosmetics (crop_id, category, benefits, usage_method)
    SELECT id, 'Skin Care', 'Finely crushed shells are used as a physical body exfoliator.', 'Crushed Shells, Oil'
    FROM crops WHERE crop_name = 'Walnut'
    AND NOT EXISTS (SELECT 1 FROM crop_cosmetics WHERE crop_id = crops.id AND category = 'Skin Care');
    

    INSERT INTO crop_cosmetics (crop_id, category, benefits, usage_method)
    SELECT id, 'Skin Care', 'B-vitamins help maintain healthy skin, and its scent acts as a natural relaxant.', 'Essential Oil Extract'
    FROM crops WHERE crop_name = 'Vanilla'
    AND NOT EXISTS (SELECT 1 FROM crop_cosmetics WHERE crop_id = crops.id AND category = 'Skin Care');
    

    INSERT INTO crop_cosmetics (crop_id, category, benefits, usage_method)
    SELECT id, 'Skin Care', 'A luxurious spice that deeply brightens the complexion and reduces pigmentation.', 'Milk infused paste'
    FROM crops WHERE crop_name = 'Saffron'
    AND NOT EXISTS (SELECT 1 FROM crop_cosmetics WHERE crop_id = crops.id AND category = 'Skin Care');
    

    INSERT INTO crop_cosmetics (crop_id, category, benefits, usage_method)
    SELECT id, 'Skin Care', 'Provides intense, refreshing hydration for oily and combination skin types.', 'Juice/Extract'
    FROM crops WHERE crop_name = 'Watermelon'
    AND NOT EXISTS (SELECT 1 FROM crop_cosmetics WHERE crop_id = crops.id AND category = 'Skin Care');
    