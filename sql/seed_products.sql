-- Seed data for products table (products: id, name, description, price, image_path, created_at)
-- Run in your MySQL / phpMyAdmin connected to database `brasbionique`.

INSERT INTO products (name, description, price, image_path) VALUES
('Bras Bionique BLX-01', 'Prothèse de bras motorisée, contrôle myoélectrique, 20 programmes personnalisés.', 12999.00, 'images/products/bras_blx01.jpg'),
('Main Prothétique HND-10', 'Main prothétique avec prise fine et retour haptique optionnel, idéale pour la dextérité.', 4999.00, 'images/products/main_hnd10.jpg'),
('Prothèse de Jambe PGR-3', 'Prothèse de jambe ultra-légère, adaptative à la marche et à la course.', 8999.00, 'images/products/jambe_pgr3.jpg'),
('Exosquelette d’assistance EXO-Walker', 'Exosquelette médical pour rééducation et assistance de la marche, réglages cliniques.', 29999.00, 'images/products/exo_walker.jpg'),
('Capteur myoélectrique MYS-2', 'Capteur EMG compact pour interface prothétique, faible latence, connexion Bluetooth.', 249.00, 'images/products/mys2_sensor.jpg'),
('Batterie haute capacité BAT-5000', 'Batterie lith-ion dédiée aux prothèses — autonomie longue durée, sécurité renforcée.', 189.00, 'images/products/bat5000.jpg'),
('Module de contrôle MCU-ONE', 'Module de contrôle central pour prothèses et exosquelettes — API ouverte pour intégration.', 699.00, 'images/products/mcu_one.jpg'),
('Doigt prothétique DIGI-4 (pack 5)', 'Pack de doigts prothétiques interchangeables pour griffe et préhension fine.', 399.00, 'images/products/digi4_pack5.jpg'),
('Attelle orthopédique ORTHO-Soft', 'Attelle de maintien pour rééducation post-opératoire ou support de membre.', 129.00, 'images/products/ortho_soft.jpg'),
('Capteur de pression plantar FOOTPAD', 'Capteur de pression pour semelle prothétique / rééducation, transmet en temps réel.', 149.00, 'images/products/footpad.jpg'),
('Kit d’entretien prothèse CARE-KIT', 'Kit maintenance pour prothèses: outils, lubrifiants, sangles de remplacement.', 49.00, 'images/products/carekit.jpg'),
('Capteur de position IMU-Pro', 'IMU 9-axe pour détection de mouvement et calibration prothétique avancée.', 199.00, 'images/products/imu_pro.jpg');

-- Optional: if you want to change created_at timestamps to now, you can run:
-- UPDATE products SET created_at = NOW() WHERE created_at IS NULL;

-- End of seed
