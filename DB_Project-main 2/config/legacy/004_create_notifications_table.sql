-- create notification table to pass messages to both buyer and sellers when a
-- specific condition is met:
CREATE TABLE IF NOT EXISTS notification (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    notification_message VARCHAR(255) NOT NULL,
    notification_creation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP()
);

-- add constraint that the user_id within this table refers to a buyer or seller
-- that exists within the system:
ALTER TABLE notification ADD CONSTRAINT fk_notification_user_id FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE;
