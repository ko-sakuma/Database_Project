BEGIN
    -- declare all potential required variables prior to use.
    DECLARE PLACEHOLDER int;
    DECLARE PREVIOUS_USER_ID int;
    DECLARE INCOMING_PRODUCT_ID int;
    DECLARE PROD_TITLE varchar(30);
    DECLARE OUTBID_MSG varchar(100);

    -- capture incoming product_id.
    SET INCOMING_PRODUCT_ID = NEW.product_id;

    -- capture the previous user_id that has the previous highest bid (if 
    -- nothing is found the PREVIOUS_USER_ID variable is set to 0)
    SET PREVIOUS_USER_ID = (SELECT IF( EXISTS(
                               SELECT b.user_id
                               FROM bid AS b
                                WHERE b.product_id = INCOMING_PRODUCT_ID
                                ORDER BY b.bid_creation_date DESC
                                LIMIT 1), (SELECT bi.user_id
                                            FROM bid AS bi
                                            WHERE bi.product_id = INCOMING_PRODUCT_ID
                                            ORDER BY bi.bid_creation_date DESC
                                            LIMIT 1), 0));

    -- set the product_title.
    SET PROD_TITLE = (SELECT p.product_title
                       FROM product AS p
                       WHERE p.product_id = INCOMING_PRODUCT_ID);

    -- prepare the message.
    SET OUTBID_MSG = CONCAT("You have been outbid on the item: ", PROD_TITLE);

    -- if previous user_id = 0, this is a new bid; do nothing.
    IF (PREVIOUS_USER_ID = 0) THEN
        SET PLACEHOLDER = 0;

    -- if previous user_id is the same as the incoming user_id, the seller has 
    -- not been outbid; do nothing.
    ELSEIF (PREVIOUS_USER_ID = NEW.user_id) THEN
        SET PLACEHOLDER = 0;

    -- the incoming bid has come from a different user than the previous max
    -- bid, notify the previous highest bidder that they have just been outbid 
    -- on the auction.
    ELSE
        INSERT INTO notification (notification_id, user_id, notification_message, notification_creation_date)
                VALUES (null, PREVIOUS_USER_ID, OUTBID_MSG, null);

    END IF;

END
