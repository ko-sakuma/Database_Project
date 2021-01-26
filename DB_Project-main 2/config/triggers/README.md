# Notification Triggers
These are a series of triggers that are implemented before and after inserts on the bid table.
These triggers send notification to the previous highest bidders (if they exists) that they have 
been outbid and the second trigger notifies watchers of a specific item that there is bidding 
activity on an item that they watch.

## IMPORTANT PLEASE READ AND FOLLOW EVERY STEP CAREFULLY.
To correctly add the trig_notify_outbid.sql trigger you must follow these steps:
1. Click on the bid table in phpMyAdmin.
2. Click on Triggers > click on Add trigger > set the trigger name to
anything you like > make sure the time is set to "BEFORE" and the Event
is set to "INSERT" > copy the entire contents of this file into the 
definition box > finally press go (no errors should occur).

If successful the trigger will run upon every insert into the bid table.
The following will select if it exists the user_id of the old bid where the
product_id matches the product_id of the incoming insert. If the incoming 
user_id is the same as the old one the seller has not been outbid; take no 
extra action. If there was no previous user_id found then this is the first
bid; take no extra action. If the incoming user_id is different to the 
previous user_id insert in the notifications table where user_id is the 
previous a message that they have been outbid.


To correctly add the trig_notify_watchers.sql you must follow these steps:
1. Click on the bid table in phpMyAdmin.
2. Click on Triggers > click on Add trigger > set the trigger name to
   anything you like > make sure the time is set to "AFTER" and the Event
   is set to "INSERT" > copy the entire contents of this file into the 
   definition box > finally press go (no errors should occur).

If successful the trigger will run after every insert into the bid table.
The following will select all user_id's that are watching a specific product
where the product_id that matches the just inserted product_id (exluding the
user_id from the user that just placed a bid) and insert into the 
notifications table that a user has just placed a bid on an item that they 
are watching.
 
__Addendum__:     
MySQL does not have "pass" or "continue" constructs like traditional
programming languages, it also lacks the concept of "NULL". A placeholder
value is used to explicitly do nothing in required branches of the IF
statement.
