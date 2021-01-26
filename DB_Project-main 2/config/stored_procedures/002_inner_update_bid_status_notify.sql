BEGIN
    -- declare all potential required variables prior to use.
    DECLARE prod_id int;
    DECLARE prod_user_id int;
    DECLARE prod_title varchar(30);
    DECLARE prod_reserve_price decimal(65,2);

    DECLARE WINNER_USER_ID int;
    DECLARE SELLER_SOLD_MSG varchar(255);
    DECLARE SELLER_UNSOLD_MSG varchar(255);
    DECLARE BIDDER_WON_MSG varchar(255);

    -- finished required for iteration flow control.
    DECLARE finished int default 0;

    -- create iteration cursor that loops through ended bids where their status is still 'active'.
    DECLARE products_iter CURSOR FOR SELECT product_id, user_id, product_title, product_reserve_price 
                                    FROM product 
                                    WHERE product_end_date < CURRENT_TIME 
                                    AND product_status_id = 0;

    -- set termination condition for the loop (if no more items found set finished to 1).
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET finished = 1;

	-- open iterator.
	OPEN products_iter;

	-- begin loop.
	prod_info_iter: LOOP

		-- execute iterator statement into respective declared variables above.
		FETCH products_iter INTO prod_id, prod_user_id, prod_title, prod_reserve_price;

		-- leave iteration when no more rows found in iterator.
		IF finished = 1 THEN
			LEAVE prod_info_iter;

		ELSE
			-- if end price < reserve (item has not sold, notify seller only & update product status to Ended):
			IF ((SELECT (
						SELECT IFNULL(
							(SELECT MAX(bid_amount)
							FROM bid 
							WHERE product_id = prod_id),
							(SELECT product_starting_price
							FROM product
							WHERE product_id = prod_id)
						)
					) AS current_price
					FROM product
					WHERE product_id = prod_id) < prod_reserve_price) THEN

				SET SELLER_UNSOLD_MSG = CONCAT("Your item: ", prod_title, " went unsold.");

				INSERT INTO notification (notification_id, user_id, notification_message, notification_creation_date)
						VALUES (null, prod_user_id, SELLER_UNSOLD_MSG, null);
						
				UPDATE product SET product_status_id = 1 WHERE product_id = prod_id;

				-- set output variable of stored procedure to notify the 'losers' of the auction.
				SET prod_id_out = prod_id;
				SET prod_title_out = prod_title;
				SET winner_id_out = WINNER_USER_ID;
							
			-- end price > reserve (item has sold, notify seller and buyer and update product status to Sold):
			ELSE
				SET WINNER_USER_ID = (SELECT user_id FROM bid WHERE product_id = prod_id AND bid_amount = (SELECT MAX(bid_amount) FROM bid WHERE product_id = prod_id));

				SET SELLER_SOLD_MSG = CONCAT("Congrats! your item: ", prod_title, " sold!");

				SET BIDDER_WON_MSG = CONCAT("Hooray! you've won the item!: ", prod_title);

				INSERT INTO notification (notification_id, user_id, notification_message, notification_creation_date)
						VALUES (null, prod_user_id, SELLER_SOLD_MSG, null);
						
				INSERT INTO notification (notification_id, user_id, notification_message, notification_creation_date)
						VALUES (null, WINNER_USER_ID, BIDDER_WON_MSG, null);
						
				UPDATE product SET product_status_id = 2 WHERE product_id = prod_id;

				-- set output variable of stored procedure to notify the 'losers' of the auction.
				SET prod_id_out = prod_id;
				SET prod_title_out = prod_title;
				SET winner_id_out = WINNER_USER_ID;

			END IF;

		END IF;

	END LOOP prod_info_iter;

	CLOSE products_iter;
END
