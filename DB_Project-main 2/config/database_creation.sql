START TRANSACTION;

-- ============================================================================
--           TABLE CREATION WITH BASIC CHECKS & CONSTRAINTS
-- ============================================================================
CREATE TABLE IF NOT EXISTS users (
	user_id INT AUTO_INCREMENT PRIMARY KEY,
	user_name VARCHAR(30) NOT NULL UNIQUE,
	user_email VARCHAR(254) NOT NULL UNIQUE, -- RFC3696 Errata ID 1690.
	user_password VARCHAR(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, -- datatype for hashed password
	user_role_id INT NOT NULL
);

CREATE TABLE IF NOT EXISTS user_role (
	user_role_id INT NOT NULL PRIMARY KEY,
	user_role_name VARCHAR(16) NOT NULL
);

CREATE TABLE IF NOT EXISTS product (
	product_id INT AUTO_INCREMENT PRIMARY KEY,
	user_id INT NOT NULL,
	product_status_id INT NOT NULL,
	product_title VARCHAR(100) NOT NULL,
	product_year INT NOT NULL CHECK (product_year > 0 AND product_year <= 9999),
	product_starting_price DECIMAL(65,2) NOT NULL CHECK (product_starting_price > 0),
	product_reserve_price DECIMAL(65,2) NOT NULL CHECK (product_reserve_price > 0),
	product_category_id INT NOT NULL,
	product_image_url VARCHAR(255), -- nullable
	product_details VARCHAR(1000), -- nullable
	product_condition_id INT NOT NULL,
	product_end_date TIMESTAMP NOT NULL CHECK (product_end_date > CURRENT_TIMESTAMP()),
	product_creation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP()
);

CREATE TABLE IF NOT EXISTS product_status (
	product_status_id INT NOT NULL PRIMARY KEY,
	product_status_name VARCHAR(16) NOT NULL
);

CREATE TABLE IF NOT EXISTS product_category (
	product_category_id INT NOT NULL PRIMARY KEY,
	product_category_name VARCHAR(16) NOT NULL
);

CREATE TABLE IF NOT EXISTS product_condition (
	product_condition_id INT NOT NULL PRIMARY KEY,
	product_condition_name VARCHAR(50) NOT NULL
);

CREATE TABLE IF NOT EXISTS bid (
	bid_id INT AUTO_INCREMENT PRIMARY KEY,
	product_id INT NOT NULL,
	user_id INT NOT NULL,
	bid_amount DECIMAL(65,2) NOT NULL CHECK (bid_amount > 0),
	bid_creation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP()
);

CREATE TABLE IF NOT EXISTS watching (
	user_id INT NOT NULL,
	product_id INT NOT NULL,
	PRIMARY KEY (user_id, product_id),
	watching_creation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP()
);

CREATE TABLE IF NOT EXISTS notification (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    notification_message VARCHAR(255) NOT NULL,
    notification_creation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP()
);

-- ============================================================================
--           CONSTRAINTS IMPLEMENTATION AFTER TABLE CREATION
-- ============================================================================
-- user table constraints:
ALTER TABLE users ADD CONSTRAINT fk_users_role_id FOREIGN KEY (user_role_id) REFERENCES user_role(user_role_id) ON DELETE CASCADE;

-- product table constraints:
ALTER TABLE product ADD CONSTRAINT fk_product_user_id FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE;
ALTER TABLE product ADD CONSTRAINT fk_product_status_id FOREIGN KEY (product_status_id) REFERENCES product_status(product_status_id) ON DELETE CASCADE;
ALTER TABLE product ADD CONSTRAINT fk_product_category_id FOREIGN KEY (product_category_id) REFERENCES product_category(product_category_id) ON DELETE CASCADE;
ALTER TABLE product ADD CONSTRAINT fk_product_condition_id FOREIGN KEY (product_condition_id) REFERENCES product_condition(product_condition_id) ON DELETE CASCADE;

-- bid table constraints:
ALTER TABLE bid ADD CONSTRAINT fk_bid_product_id FOREIGN KEY (product_id) REFERENCES product(product_id) ON DELETE CASCADE;
ALTER TABLE bid ADD CONSTRAINT fk_bid_user_id FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE;

-- watching table constraints:
ALTER TABLE watching ADD CONSTRAINT fk_watching_user_id FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE;
ALTER TABLE watching ADD CONSTRAINT fk_watching_product_id FOREIGN KEY (product_id) REFERENCES product(product_id) ON DELETE CASCADE;

-- notification table constraints:
ALTER TABLE notification ADD CONSTRAINT fk_notification_user_id FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE;

-- ============================================================================
--                   INDEX CREATION FOR QUERY PERFORMANCE
-- ============================================================================
-- user table indexes (speed up lookup for user via username):
CREATE INDEX idx_users_username_lookup ON users (user_name);

-- product table indexes (speed up title & categorical searches based on user query):
CREATE INDEX idx_product_user_query_lookup ON product (product_title, product_status_id, product_category_id, product_condition_id);

-- ============================================================================
--                   TABLE SEEDING FOR LABELLED FIELDS
-- ============================================================================
-- seed user_role table
INSERT IGNORE INTO user_role VALUES (0, 'Buyer'), (1, 'Seller');

-- seed product_status table
INSERT IGNORE INTO product_status VALUES (0, 'Active'), (1, 'Ended'), (2, 'Sold');

-- seed product_category table (to be decided final category)
INSERT IGNORE INTO product_category VALUES (0, 'Red'), (1, 'White'), (2, 'Rosé'), (3, 'Champagne');

-- seed product_condition table
INSERT IGNORE INTO product_condition VALUES (0, 'Direct from winemaker'), (1, 'Preserved by merchant'), (2, 'Preserved at home');

COMMIT;
