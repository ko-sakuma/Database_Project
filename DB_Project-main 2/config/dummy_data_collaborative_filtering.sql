-- this file only works if they are simply based on dummy_data.sql
-- if more data (e.g. users, bids, products etc.) this file might not show the
-- impact of the collaborative filtering as shown in the table in
-- our design report properly and accurate.

START TRANSACTION;

-- these users can be used to test the recommendation feature
INSERT INTO `users` (`user_id`, `user_name`, `user_email`, `user_password`, `user_role_id`) VALUES
(5, 'buyer3', 'buyer3@email.com', '6d613a1ee01eec4c0f8ca66df0db71dca0c6e1cf', 0); /* Password = database */

INSERT INTO `users` (`user_id`, `user_name`, `user_email`, `user_password`, `user_role_id`) VALUES
(6, 'buyer4', 'buyer4@email.com', '6d613a1ee01eec4c0f8ca66df0db71dca0c6e1cf', 0); /* Password = database */

INSERT INTO `users` (`user_id`, `user_name`, `user_email`, `user_password`, `user_role_id`) VALUES
(7, 'buyer5', 'buyer5@email.com', '6d613a1ee01eec4c0f8ca66df0db71dca0c6e1cf', 0); /* Password = database */

-- bids to test the recommendation feature
INSERT INTO `bid` (`bid_id`, `product_id`, `user_id`, `bid_amount`, `bid_creation_date`) VALUES
(31, 7, 5, '260.00', '2020-11-28 14:01:11'),
(32, 9, 5, '160.00', '2020-11-28 14:01:12'),
(33, 10, 6, '205.00', '2020-11-28 14:01:13'),
(34, 17, 6, '105.00', '2020-11-28 14:01:14'),
(35, 19, 7, '350.00', '2020-11-28 14:01:15'),
(36, 20, 7, '900.00', '2020-11-28 14:01:16');
-- until here, there are no recommendations shown for any user
-- then please bid according to table in design report

COMMIT;