BEGIN
	-- declare all potential required variables prior to use.
	DECLARE prod_id int;
	DECLARE prod_title varchar(255);
	DECLARE winner_id int;
    DECLARE loser_id int;
    DECLARE loser_msg varchar(255);

    -- finished required for iteration flow control.
    DECLARE finished int default 0;

	-- create iteration cursor for users that entered but did not win auction.
	DECLARE user_id_iter CURSOR FOR SELECT DISTINCT user_id 
							FROM bid 
							WHERE product_id = prod_id
							AND user_id != winner_id
							AND bid_amount != (SELECT MAX(bid_amount)
												FROM bid
												WHERE product_id = prod_id);

    -- set termination condition for the loop (if no more items found set finished to 1).
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET finished = 1;

	-- call stored procedure to get product_id & title of recently ended auction.
	CALL inner_update_status(@prod_id, @prod_title, @winner_id);

	SET prod_id = @prod_id;
	SET winner_id = @winner_id;
	SET prod_title = @prod_title;

	-- open iterator.
	OPEN user_id_iter;

	-- begin loop.
	final_iterr: LOOP

		-- execute iterator statement into respective declared variables above.
		FETCH user_id_iter INTO loser_id;

		-- leave iteration when no more rows found in iterator.
		IF finished = 1 THEN
			LEAVE final_iterr;

		-- Notify losers of the auction that they did not win the item.
		ELSE
			SET loser_msg = CONCAT("Sorry! you did not win: ", prod_title);

			INSERT INTO notification (notification_id, user_id, notification_message, notification_creation_date)
					VALUES (null, loser_id, loser_msg, null);

		END IF;

	END LOOP final_iterr;

	CLOSE user_id_iter;

END
