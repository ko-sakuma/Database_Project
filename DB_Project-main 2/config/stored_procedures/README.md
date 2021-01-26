# Automatic Bid Manager
This is a combination of stored procedures and MySQL events that call the created stored procedure 
that manage the auction website. 

## IMPORTANT PLEASE READ AND FOLLOW EVERY STEP CAREFULLY.
To correctly have the bid manager working you must follow these steps:
1. Click on the database in phpMyAdmin > Click on the SQL tab at the 
top > Copy the entire contents of 001_turn_on_scheduler.sql into an SQL 
query box in phpMyAdmin and click go (no errors should occur).

2. Click on the database in phpMyAdmin > click on the Routines tab at the top > click on Add 
routine > change routine name to inner_update_status > add 3 parameters: 

	   	1. Direction: OUT, Name: prod_id_out, Type: INT, 
	   	2. Direction: OUT, Name: prod_title_out, Type: VARCHAR, Length/Values: 30,
	   	3. Direction: OUT, Name: winner_id_out, Type: INT

3. Copy the entire definition of 002_inner_update_bid_status_notify.sql into the definition 
box > change Security type to INVOKER > change SQL data access to MODIFIES SQL DATA > and finally 
press go (no errors should occur).
    
4. Click on Add routine again > change routing name to outer_update_status > drop all 
parameters > copy the entire contents of 003_outer_update_status_notify.sql into the definition 
box > change the Security type to DEFINER > change SQL data access to MODIFIES SQL DATA > and 
finally press go (no errors should occur).

5. Click on the database in phpMyAdmin > Click on the SQL tab at the top > copy the entire 
contents of 004_create_continuos_event.sql into an SQL query box in phpMyAdmin and click go 
(no errors should occur).

If successful the bid manager will sweep the auction every 1 second call the outer_update_status 
stored procedure that correctly manage the product_status and ended item. It is automatically 
updates the product status to either Sold or Unsold depending on whether the reserve was met and 
sends notifications to the seller, winner and or losers of the auction where applicable.
 
 --------------

## Logic Overview
The stored procedure code found in inner_update_status_notify.sql and 
outer_update_status_notify.sql will check the products table for bids where the end date is less
than the current date. There is branching logic depending on whether the bid has been successful
(reserve has been met) or not. If the reserve price has not been met the seller is notified that
their item went unsold. The product_status_id is set to 1 (Ended). If ther reserve price has been
met the seller is notified that their item has sold, the winner (higher bidder) is notified that
they have just won the item and the product_status_id is set to 2 (Sold). In both cases there is an
output value set for the inner_update_status stored procedure, these are sent to the
outer_update_status stored_procedure to notify bidders and non-winners that they have lost the 
auction.

### Stored Procedure Quirks:
Stored procedures are evaluated lazily (similar to a yield operation in python). It is required
that all potential variables in a stored procedure is declared prior to setting updating and
inserting variables down the line. Views are only created at it's point of use, the 'DECLARE' 
statements inform MySQL of the Selectiona and Projection of the view.

### Inner & Outer procedures:
Within a stored procedure you can only iterate through a single loop / declared view. Thus there is
a need for an inner and outer procedure. The inner procedure contains logic that notifies the
seller and potential winner about the results of the auction, an iteration of a view is required to
loop through ended bids where their status is still set to 'Active'. The outer procedure notifies 
all non-winners that they have lost, an iteration of a view is required to loop through all the 
losers of the auction and notify them.

### Assumptions:
The stored procedure sweep of once per second is enough and negligible in comparison to the
theoretical maximum number of queries per second that MySQL 5.7 is able to achieve. This 
once-per-second sweep has congruence with standard unit time, it is also assumed that sellers 
cannot set invalid end times for an auction i.e. a seller cannot list an auction for a time in the 
past (enforced both by PHP form restrictions and CHECK constraints within the product table upon 
insertion).
