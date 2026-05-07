-- Create new table for cosmetic uses
CREATE TABLE IF NOT EXISTS crop_cosmetics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    crop_id INT NOT NULL,
    category VARCHAR(100) NOT NULL, -- e.g., Skin Care, Hair Care, Medicinal
    benefits TEXT NOT NULL,
    usage_method VARCHAR(255) NOT NULL, -- e.g., Paste, Oil, Juice, Powder
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (crop_id) REFERENCES crops(id) ON DELETE CASCADE
);

-- Safely ensure we have some cosmetic crops in the main crops table
INSERT IGNORE INTO crops (crop_name, image_url, short_desc) VALUES 
('Turmeric', 'assets/images/crops/turmeric.jpg', 'A bright yellow spice with powerful anti-inflammatory properties.'),
('Aloe Vera', 'assets/images/crops/aloe_vera.jpg', 'A succulent plant widely used in cosmetics and alternative medicine.'),
('Coconut', 'assets/images/crops/coconut.jpg', 'A versatile palm tree producing coconuts, widely used for its water, milk, and oil.'),
('Neem', 'assets/images/crops/neem.jpg', 'A fast-growing tree with renowned medicinal and skincare properties.'),
('Papaya', 'assets/images/crops/papaya.jpeg', 'A tropical fruit rich in enzymes and widely used in skin routines.'),
('Sandalwood', 'assets/images/crops/sandalwood.jpg', 'A fragrant wood prized for its essential oil and cosmetic benefits.'),
('Rose', 'assets/images/crops/rose.jpg', 'A beautiful flower famous for its essential oils and rose water.'),
('Rice', 'assets/images/crops/rice.jpg', 'A staple grain that also has remarkable skin-brightening properties.');

-- Insert cosmetic data by dynamically looking up the crop IDs
INSERT INTO crop_cosmetics (crop_id, category, benefits, usage_method)
SELECT id, 'Skin Care', 'Anti-inflammatory, clears acne, provides a bright glowing skin tone.', 'Paste, Powder, Oil Extract'
FROM crops WHERE crop_name = 'Turmeric'
AND NOT EXISTS (SELECT 1 FROM crop_cosmetics WHERE crop_id = crops.id AND category = 'Skin Care');

INSERT INTO crop_cosmetics (crop_id, category, benefits, usage_method)
SELECT id, 'Skin Care', 'Deeply moisturizes, soothes sunburns, and accelerates skin healing.', 'Gel, Juice, Direct Application'
FROM crops WHERE crop_name = 'Aloe Vera'
AND NOT EXISTS (SELECT 1 FROM crop_cosmetics WHERE crop_id = crops.id AND category = 'Skin Care');

INSERT INTO crop_cosmetics (crop_id, category, benefits, usage_method)
SELECT id, 'Hair Care', 'Penetrates hair shaft, reduces protein loss, acts as a deep conditioner and promotes hair growth.', 'Oil, Milk Extract'
FROM crops WHERE crop_name = 'Coconut'
AND NOT EXISTS (SELECT 1 FROM crop_cosmetics WHERE crop_id = crops.id AND category = 'Hair Care');

INSERT INTO crop_cosmetics (crop_id, category, benefits, usage_method)
SELECT id, 'Skin Care', 'Excellent natural moisturizer, acts as a gentle makeup remover and body lotion.', 'Cold-pressed Oil'
FROM crops WHERE crop_name = 'Coconut'
AND NOT EXISTS (SELECT 1 FROM crop_cosmetics WHERE crop_id = crops.id AND category = 'Skin Care');

INSERT INTO crop_cosmetics (crop_id, category, benefits, usage_method)
SELECT id, 'Medicinal', 'Antibacterial and antifungal properties, helps clear severe acne, treats dandruff.', 'Paste, Oil, Boiled Water Rinse'
FROM crops WHERE crop_name = 'Neem'
AND NOT EXISTS (SELECT 1 FROM crop_cosmetics WHERE crop_id = crops.id AND category = 'Medicinal');

INSERT INTO crop_cosmetics (crop_id, category, benefits, usage_method)
SELECT id, 'Skin Care', 'Contains papain enzymes that act as a natural exfoliator, removes dead skin cells.', 'Mashed Paste, Extract'
FROM crops WHERE crop_name = 'Papaya'
AND NOT EXISTS (SELECT 1 FROM crop_cosmetics WHERE crop_id = crops.id AND category = 'Skin Care');

INSERT INTO crop_cosmetics (crop_id, category, benefits, usage_method)
SELECT id, 'Skin Care', 'Soothes rashes, clears blemishes, and acts as a powerful anti-aging component.', 'Powder, Essential Oil'
FROM crops WHERE crop_name = 'Sandalwood'
AND NOT EXISTS (SELECT 1 FROM crop_cosmetics WHERE crop_id = crops.id AND category = 'Skin Care');

INSERT INTO crop_cosmetics (crop_id, category, benefits, usage_method)
SELECT id, 'Skin Care', 'Acts as a mild astringent, balances skin pH, reduces redness, and hydrates.', 'Water (Hydrosol), Essential Oil'
FROM crops WHERE crop_name = 'Rose'
AND NOT EXISTS (SELECT 1 FROM crop_cosmetics WHERE crop_id = crops.id AND category = 'Skin Care');

INSERT INTO crop_cosmetics (crop_id, category, benefits, usage_method)
SELECT id, 'Skin Care', 'Brightens dull skin, reduces pores, and acts as an excellent anti-aging agent (fermented rice water).', 'Rice Water Rinse, Fine Powder'
FROM crops WHERE crop_name = 'Rice'
AND NOT EXISTS (SELECT 1 FROM crop_cosmetics WHERE crop_id = crops.id AND category = 'Skin Care');
