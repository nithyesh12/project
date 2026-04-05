-- Run this script in your MySQL client / phpMyAdmin

CREATE TABLE IF NOT EXISTS `admins` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
);

-- Default admin account. Password is 'admin123'
INSERT IGNORE INTO `admins` (`email`, `password_hash`) 
VALUES ('admin@growyourcrops.com', '$2y$10$wTfH.rW6YnB6Jm/9g1f3c.BfMpHX76b9uK/tY41D45HtbvO.S0e0m');

DROP TABLE IF EXISTS `crops`;

CREATE TABLE `crops` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `crop_name` VARCHAR(100) NOT NULL UNIQUE,
    `scientific_name` VARCHAR(150),
    `short_desc` TEXT,
    `image_url` VARCHAR(255),
    
    -- Ranges for matching
    `ph_min` FLOAT,
    `ph_max` FLOAT,
    `temp_min` FLOAT,
    `temp_max` FLOAT,
    `rain_min` FLOAT,
    `rain_max` FLOAT,
    `n_min` FLOAT,
    `n_max` FLOAT,
    
    `seasons` VARCHAR(255), -- Comma separated e.g., 'Kharif,Rabi'
    `states` TEXT, -- Comma separated e.g., 'All' or 'Punjab,Haryana'
    `water_req` VARCHAR(100), -- E.g. 'High', 'Low'

    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
);

-- Insert a few sample crops to match the existing Python Dictionary
INSERT INTO `crops` (`crop_name`, `ph_min`, `ph_max`, `temp_min`, `temp_max`, `rain_min`, `rain_max`, `n_min`, `n_max`, `seasons`, `states`) VALUES
('Rice', 5.5, 7.0, 20.0, 35.0, 150.0, 300.0, 80.0, 120.0, 'Kharif', 'All'),
('Wheat', 6.0, 7.5, 15.0, 25.0, 50.0, 100.0, 60.0, 90.0, 'Rabi', 'Punjab,Haryana,Uttar Pradesh,Madhya Pradesh,Rajasthan,Bihar,Gujarat'),
('Sugarcane', 6.5, 7.5, 21.0, 27.0, 150.0, 250.0, 100.0, 150.0, 'Kharif,Zaid', 'Uttar Pradesh,Maharashtra,Karnataka,Tamil Nadu,Andhra Pradesh,Gujarat,Bihar,Haryana,Punjab');
