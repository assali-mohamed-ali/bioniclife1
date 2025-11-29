Seed SQL for products table

Files created:
- sql/seed_products.sql   -> SQL insert statements to add a variety of bionic/medical products
- scripts/seed_products.php -> optional PHP script to execute the SQL via PDO (one-off)

How to run the SQL

1) Via phpMyAdmin (easy):
   - Open phpMyAdmin, select database `brasbionique`.
   - Open the sql/seed_products.sql file and paste/run the SQL (or use Import > choose file).

2) Via MySQL CLI (example):
   - Open terminal / command prompt and run:

   mysql -u root -p brasbionique < path/to/seed_products.sql

   (On Windows with XAMPP, ensure you use the correct mysql client path if not in PATH.)

3) Run the PHP helper (one-off):
   - From the project root, run (Windows PowerShell):

   php scripts\seed_products.php

Images

- The SQL uses image paths like images/products/*.jpg.
- Put your product images in the `images/products/` folder with the same filenames, or adjust the SQL `image_path` values.

Notes

- The products table should match schema: id, name, description, price, image_path, created_at.
  Your application already creates this table dynamically in boutique.php if it doesn't exist.
- This seed inserts multiple different bionic/medical items (prothèses, capteurs, batteries, exosquelettes, etc.).
- If you already seeded once and want to re-run, be careful to avoid duplicates (you can truncate the table first):

  TRUNCATE TABLE products;

