BEGIN
    -- declare all potential required variables prior to use.
    DECLARE INCOMING_USER_ID int;
    DECLARE INCOMING_PRODUCT_ID int;
    DECLARE PROD_TITLE varchar(30);
    DECLARE ACTIVITY_MSG varchar(100);
	DECLARE watchers_user_id int;

    -- finished required for iteration flow control.
    DECLARE finished int default 0;


    -- create iteration cursor that loops through user_id in watching table that matches predicate.
    DECLARE user_iter CURSOR FOR SELECT user_id
                                    FROM watching 
                                    WHERE product_id = INCOMING_PRODUCT_ID
                                    AND user_id != INCOMING_USER_ID;

    -- set termination condition for the loop (if no more items found set finished to 1).
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET finished = 1;

    -- capture incoming product_id.
    SET INCOMING_PRODUCT_ID = NEW.product_id;

    -- capture incoming user_id.
    SET INCOMING_USER_ID = NEW.user_id;

    -- set the product_title.
    SET PROD_TITLE = (SELECT p.product_title
                       FROM product AS p
                       WHERE p.product_id = INCOMING_PRODUCT_ID);

	-- open iterator.
	OPEN user_iter;

	-- begin loop.
	user_id_iter: LOOP

		-- execute iterator statement into respective declared variables above.
		FETCH user_iter INTO watchers_user_id;

		-- leave iteration when no more rows found in iterator.
		IF finished = 1 THEN
			LEAVE user_id_iter;

		ELSE
			-- prepare the bid activity message.
			SET ACTIVITY_MSG = CONCAT("Someone just bidded on the item you were watching: ", PROD_TITLE);

			INSERT INTO notification (notification_id, user_id, notification_message, notification_creation_date)
					VALUES (null, watchers_user_id, ACTIVITY_MSG, null);

		END IF;

	END LOOP user_id_iter;

	CLOSE user_iter;

END
