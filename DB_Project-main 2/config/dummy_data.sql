START TRANSACTION;

-- Dummy data for table `users` (added a seller and a buyer)

INSERT INTO `users` (`user_id`, `user_name`, `user_email`, `user_password`, `user_role_id`) VALUES
(1, 'seller1', 'seller1@email.com', '6d613a1ee01eec4c0f8ca66df0db71dca0c6e1cf', 1); /* Password = database */

INSERT INTO `users` (`user_id`, `user_name`, `user_email`, `user_password`, `user_role_id`) VALUES
(2, 'buyer1', 'buyer1@email.com', '6d613a1ee01eec4c0f8ca66df0db71dca0c6e1cf', 0); /* Password = database */

INSERT INTO `users` (`user_id`, `user_name`, `user_email`, `user_password`, `user_role_id`) VALUES
(3, 'seller2', 'seller2@email.com', '6d613a1ee01eec4c0f8ca66df0db71dca0c6e1cf', 1); /* Password = database */

INSERT INTO `users` (`user_id`, `user_name`, `user_email`, `user_password`, `user_role_id`) VALUES
(4, 'buyer2', 'buyer2@email.com', '6d613a1ee01eec4c0f8ca66df0db71dca0c6e1cf', 0); /* Password = database */

-- Dummy data for table `product` 

-- Red Wine(0) x 5

INSERT INTO `product` (`product_id`, `user_id`, `product_status_id`, `product_title`, `product_year`, `product_starting_price`, `product_reserve_price`, `product_image_url`, `product_details`, `product_condition_id`, `product_category_id`, `product_end_date`, `product_creation_date`) VALUES
(1, 1, 0, '2017 CHATEAU BOSWELL', 2017, 99.99, 200, 'https://produits.bienmanger.com/28387-0w600h600_Red_Wine_Shiraz_Bin_Kalimna_Penfolds.jpg', 'We have sought to capture California’s purest representation of terroir from Napa Valley’s storied Beckstoffer To Kalon Vineyard', 0, 0, '2021-11-30 08:00:26', '2020-08-08  07:00:26');

INSERT INTO `product` (`product_id`, `user_id`, `product_status_id`, `product_title`, `product_year`, `product_starting_price`, `product_reserve_price`, `product_image_url`, `product_details`, `product_condition_id`, `product_category_id`, `product_end_date`, `product_creation_date`) VALUES
(2, 1, 0, '2016 BODEGA NORTON PRIVADA', 2016, 3.34, 10.50, 'https://produits.bienmanger.com/28387-0w600h600_Red_Wine_Shiraz_Bin_Kalimna_Penfolds.jpg', 'Concentrated dark fruit and chocolate mousse flavors are framed by firm tannins and acidity in this inky, muscular style.', 1, 0, '2022-02-07 12:00:26', '2019-08-08  08:10:12');

INSERT INTO `product` (`product_id`, `user_id`, `product_status_id`, `product_title`, `product_year`, `product_starting_price`, `product_reserve_price`, `product_image_url`, `product_details`, `product_condition_id`, `product_category_id`, `product_end_date`, `product_creation_date`) VALUES
(3, 1, 1, '1999 MERLOT DELUX BOX', 1999, 150, 180, 'https://produits.bienmanger.com/28387-0w600h600_Red_Wine_Shiraz_Bin_Kalimna_Penfolds.jpg', 'You should not be able to see this description. The bid has ended', 2, 0, '2019-08-08  08:00:26', '2016-08-08  08:00:26');

INSERT INTO `product` (`product_id`, `user_id`, `product_status_id`, `product_title`, `product_year`, `product_starting_price`, `product_reserve_price`, `product_image_url`, `product_details`, `product_condition_id`, `product_category_id`, `product_end_date`, `product_creation_date`) VALUES
(4, 3, 0, '2015 RIPASSO PREMIER BOX', 2015, 60, 600, 'https://produits.bienmanger.com/28387-0w600h600_Red_Wine_Shiraz_Bin_Kalimna_Penfolds.jpg', 'An intense, full-bodied yet smooth and velvety red wine with a bouquet of ripe red berries and hints of spice. A generous wine - as Ripasso should be.', 1, 0, '2022-08-08 08:00:26', '2020-08-08  08:00:26');

INSERT INTO `product` (`product_id`, `user_id`, `product_status_id`, `product_title`, `product_year`, `product_starting_price`, `product_reserve_price`, `product_image_url`, `product_details`, `product_condition_id`, `product_category_id`, `product_end_date`, `product_creation_date`) VALUES
(5, 3, 0, '2013 AMARONE DELLA VALPOLIELLA', 2013, 90, 600, 'https://produits.bienmanger.com/28387-0w600h600_Red_Wine_Shiraz_Bin_Kalimna_Penfolds.jpg', 'Dark ruby. Full bodied with high tannins. Enchanting notes of cherries. Aromas of dark cherries, dried plums, leather, vanilla. Long finish. Amazing !', 2, 0, '2022-08-08 08:00:26', '2020-08-08  08:00:26');

-- White Wine(1) x 5
INSERT INTO `product` (`product_id`, `user_id`, `product_status_id`, `product_title`, `product_year`, `product_starting_price`, `product_reserve_price`, `product_image_url`, `product_details`, `product_condition_id`, `product_category_id`, `product_end_date`, `product_creation_date`) VALUES
(6, 1, 0, '2016 APRIORI ANTICA CHARDONNAY', 2016, 102.50, 250, 'https://assets.sainsburys-groceries.co.uk/gol/7468058/1/640x640.jpg', 'Made by legendary winemaker Phillipe Melka, this white is just as good as his reds!', 0, 1, '2021-12-31 11:00:26', '2013-08-08  09:00:12');

INSERT INTO `product` (`product_id`, `user_id`, `product_status_id`, `product_title`, `product_year`, `product_starting_price`, `product_reserve_price`, `product_image_url`, `product_details`, `product_condition_id`, `product_category_id`, `product_end_date`, `product_creation_date`) VALUES
(7, 1, 0, '2018 NOELIA RICCI BRO EMILIA ROMAGNA TREBBIANO', 2018, 200, 300, 'https://assets.sainsburys-groceries.co.uk/gol/7468058/1/640x640.jpg', 'This Trebbiano encapsulates elegance, despite being assertively acidic. It’s packed with minerality and citrus fruits which give it an unbelievably refreshing taste. You can imagine sharing this wine on a picnic blanket amongst friends - quite literally summer in a glass!', 2, 1, '2020-12-10 11:00:26', '2019-08-08  09:00:12');

INSERT INTO `product` (`product_id`, `user_id`, `product_status_id`, `product_title`, `product_year`, `product_starting_price`, `product_reserve_price`, `product_image_url`, `product_details`, `product_condition_id`, `product_category_id`, `product_end_date`, `product_creation_date`) VALUES
(8, 1, 1, '2017 CATENA ZAPATA WHITE BONES GUALTALLARY CHARDONNAY', 2017, 70, 100, 'https://assets.sainsburys-groceries.co.uk/gol/7468058/1/640x640.jpg', 'Catena Zapata’s ‘White Bones’ from hails from the Adrianna Vineyard – considered as South America’s Grand Cru. Laura Catena has described the discovery of the site as ‘finding gold’. It’s no surprise that these are some of the best wines Argentina has to offer.', 1, 1, '2020-11-10 11:01:26', '2019-02-08  09:00:12');

INSERT INTO `product` (`product_id`, `user_id`, `product_status_id`, `product_title`, `product_year`, `product_starting_price`, `product_reserve_price`, `product_image_url`, `product_details`, `product_condition_id`, `product_category_id`, `product_end_date`, `product_creation_date`) VALUES

(9, 3, 0, '2019 ZUCCARDI FOSIL SAN PABLO CHARDONNAY', 2019, 100, 300, 'https://assets.sainsburys-groceries.co.uk/gol/7468058/1/640x640.jpg', 'Complex and intriguing, the 2019 Fósil Zuccardi has a powerful texture in the mouth and sustained flavour with heightened but contained freshness that comes in with a kick at the end. This white, Sebastian Zuccardi’s best, is influenced by the wines of Burgundy', 0, 1, '2021-11-07 11:00:26', '2019-08-08  09:00:12');

INSERT INTO `product` (`product_id`, `user_id`, `product_status_id`, `product_title`, `product_year`, `product_starting_price`, `product_reserve_price`, `product_image_url`, `product_details`, `product_condition_id`, `product_category_id`, `product_end_date`, `product_creation_date`) VALUES
(10, 3, 0, '1999 MEURSAULT PREMIER CRU LES CRAS REMOISSENET PERE & FILS', 1999, 202.50, 350, 'https://assets.sainsburys-groceries.co.uk/gol/7468058/1/640x640.jpg', 'Made by legendary winemaker Phillipe Melka, this white is just as good as his reds!', 0, 1, '2020-12-07 11:00:26', '2013-08-08  09:00:12');

-- Rose (2) x 5
INSERT INTO `product` (`product_id`, `user_id`, `product_status_id`, `product_title`, `product_year`, `product_starting_price`, `product_reserve_price`, `product_image_url`, `product_details`, `product_condition_id`, `product_category_id`, `product_end_date`, `product_creation_date`) VALUES
(11, 1, 0, '2019 CHATEAU MIRAVAL COTES DE PROVENCE ROSE', 2019, 20, 50, 'https://digitalcontent.api.tesco.com/v2/media/ghs/870834ca-e990-45f4-8ebd-f99f1e747e69/snapshotimagehandler_1049111046.jpeg?h=540&w=540', 'The prettiest bottle houses this picture-perfect rose, known for more than just its star quality. Quaff it by the sea with friends, raise it at a lunch or simply treat yourself. It is what Brangelina would’ve done.', 0, 2, '2020-12-07 11:00:26', '2018-07-08  09:00:12');

INSERT INTO `product` (`product_id`, `user_id`, `product_status_id`, `product_title`, `product_year`, `product_starting_price`, `product_reserve_price`, `product_image_url`, `product_details`, `product_condition_id`, `product_category_id`, `product_end_date`, `product_creation_date`) VALUES
(12, 1, 0, '2019 DOMAINES OTT CLOS MIREILLE ROSE COTES DE PROVENCE', 2019, 35, 60, 'https://digitalcontent.api.tesco.com/v2/media/ghs/870834ca-e990-45f4-8ebd-f99f1e747e69/snapshotimagehandler_1049111046.jpeg?h=540&w=540', 'Farmed organically since the 1930s and following stringent manual cultivation techniques, including hand-harvesting and strict selective sorting as well as extremely delicate pressing, the Domaines Ott team focus on low yields of outstanding juice; delicately pale yet packed with intense aromas.', 1, 2, '2020-12-07 11:00:26', '2019-07-08  09:00:12');

INSERT INTO `product` (`product_id`, `user_id`, `product_status_id`, `product_title`, `product_year`, `product_starting_price`, `product_reserve_price`, `product_image_url`, `product_details`, `product_condition_id`, `product_category_id`, `product_end_date`, `product_creation_date`) VALUES
(13, 1, 1, '2018 CLOS CIBONNE CUVEE TRADITION ROSE COTES DE PROVENCE', 2018, 20, 55, 'https://digitalcontent.api.tesco.com/v2/media/ghs/870834ca-e990-45f4-8ebd-f99f1e747e69/snapshotimagehandler_1049111046.jpeg?h=540&w=540', 'The legacy of Roux is continued by his grandchildren, who have focused their resources on optimising the vineyard to fully certified organic status. They have maintained authenticity as well with their beautifully designed labels, which haven’t changed for almost a century.', 2, 2, '2019-12-24 11:00:26', '2019-07-10  09:00:12');

INSERT INTO `product` (`product_id`, `user_id`, `product_status_id`, `product_title`, `product_year`, `product_starting_price`, `product_reserve_price`, `product_image_url`, `product_details`, `product_condition_id`, `product_category_id`, `product_end_date`, `product_creation_date`) VALUES
(14, 3, 0, '2019 CHATEAU LABALLE BRUME ROSE', 2019, 10, 60, 'https://digitalcontent.api.tesco.com/v2/media/ghs/870834ca-e990-45f4-8ebd-f99f1e747e69/snapshotimagehandler_1049111046.jpeg?h=540&w=540', 'Eight generations later and Chateau Labelle is now under the leadership of Cyril Laudet, who took the reins in 2007 where he led the winery to full biodynamic status. With the help of his wife, they have expanded their wine production and even brought back some small-scale Armagnac production.', 0, 2, '2020-11-30 11:00:26', '2019-06-10  09:00:12');

INSERT INTO `product` (`product_id`, `user_id`, `product_status_id`, `product_title`, `product_year`, `product_starting_price`, `product_reserve_price`, `product_image_url`, `product_details`, `product_condition_id`, `product_category_id`, `product_end_date`, `product_creation_date`) VALUES
(15, 3, 0, '2018 CLOS CIBONNE CUVEE TRADITION ROSE COTES DE PROVENCE', 2018, 10, 60, 'https://digitalcontent.api.tesco.com/v2/media/ghs/870834ca-e990-45f4-8ebd-f99f1e747e69/snapshotimagehandler_1049111046.jpeg?h=540&w=540', 'The legacy of Roux is continued by his grandchildren, who have focused their resources on optimising the vineyard to fully certified organic status. They have maintained authenticity as well with their beautifully designed labels, which haven’t changed for almost a century.', 0, 2, '2020-11-30 11:00:26', '2019-06-10  09:00:12');

-- Champagne(3) x5

INSERT INTO `product` (`product_id`, `user_id`, `product_status_id`, `product_title`, `product_year`, `product_starting_price`, `product_reserve_price`, `product_image_url`, `product_details`, `product_condition_id`, `product_category_id`, `product_end_date`, `product_creation_date`) VALUES
(16, 1, 0, '1998 FREIXENET CORDON NEGRO NEW', 1998, 300, 350.50, 'https://cdn.shoplightspeed.com/shops/607989/files/12474540/image.jpg', 'This is the best-selling imported sparkling wine in the world. -winery notes', 0, 3, '2021-01-03 12:00:26', '2013-08-08  08:10:12');

INSERT INTO `product` (`product_id`, `user_id`, `product_status_id`, `product_title`, `product_year`, `product_starting_price`, `product_reserve_price`, `product_image_url`, `product_details`, `product_condition_id`, `product_category_id`, `product_end_date`, `product_creation_date`) VALUES
(17, 1, 0, '1995 FREIXENET CORDON NEGRO NEW ', 2001, 100.07, 300.50, 'https://cdn.shoplightspeed.com/shops/607989/files/12474540/image.jpg', 'Altesinos 2015 Brunello di Montalcino Montosoli shows those celebrated characteristics of this special vineyard cru that are always portrayed with pronounced mineral intensity, with highlights of crushed granite and pencil shaving. Indeed', 1, 3, '2020-12-15 12:00:26', '2013-08-08  08:10:12');

INSERT INTO `product` (`product_id`, `user_id`, `product_status_id`, `product_title`, `product_year`, `product_starting_price`, `product_reserve_price`, `product_image_url`, `product_details`, `product_condition_id`, `product_category_id`, `product_end_date`, `product_creation_date`) VALUES
(18, 1, 1, '2012 BOLLINGER LA GRANDE ANNEE', 2012, 200, 400, 'https://cdn.shoplightspeed.com/shops/607989/files/12474540/image.jpg', 'Often described as the ‘Gentleman’s Champagne’, this wine is elegant, reserved, perfectly harmonious, and has great structure.', 0, 3, '2020-01-03 12:00:26', '2013-08-08  08:10:12');

INSERT INTO `product` (`product_id`, `user_id`, `product_status_id`, `product_title`, `product_year`, `product_starting_price`, `product_reserve_price`, `product_image_url`, `product_details`, `product_condition_id`, `product_category_id`, `product_end_date`, `product_creation_date`) VALUES
(19, 3, 0, '2007 TAITTINGER COMTES DE CHAMPAGNE ROSE MAGNUM', 2007, 300, 400, 'https://cdn.shoplightspeed.com/shops/607989/files/12474540/image.jpg', 'Taittinger is considered as one of the great Champagne Houses being part of the small circle of Grandes Marques. Yet, it maintains the rare distinction of being owned and managed by the family it is named after, with Pierre-Emmanuel Taittinger, his daughter Vitalie and son Clovis managing the business. Located in Reims, Taittinger has significant vineyard plantings spanning over 280 hectares (700 acres) which include several Grand Cru sites.', 2, 3, '2021-01-03 12:00:26', '2019-12-08  08:10:12');

INSERT INTO `product` (`product_id`, `user_id`, `product_status_id`, `product_title`, `product_year`, `product_starting_price`, `product_reserve_price`, `product_image_url`, `product_details`, `product_condition_id`, `product_category_id`, `product_end_date`, `product_creation_date`) VALUES
(20, 3, 0, '2008 CHARLES HEIDSIECK BRUT MILLESIME', 2008, 800, 1000, 'https://cdn.shoplightspeed.com/shops/607989/files/12474540/image.jpg', 'Another sumptuous champagne from this seminal house, produced from one of the best vintages ever experienced in Champagne, drinkers will enjoy pecan and hazelnut overtones. Perfect to pop pretty much anywhere.', 1, 3, '2021-01-01 12:00:26', '2019-12-24  08:10:12');


-- Dummy data for table `watching` (added the buyer watching the two active items)

INSERT INTO `watching` (`user_id`, `product_id`, `watching_creation_date`) VALUES
(2, 1, '2016-10-08  08:00:26'),
(2, 2, '2020-10-10 13:28:46'),
(2, 4, '2020-10-28 14:05:06'),
(2, 11, '2020-10-30 14:04:47'),
(2, 15, '2020-11-22 14:04:30'),
(4, 1, '2020-11-23 14:06:41'),
(4, 4, '2020-11-23 14:05:39'),
(4, 7, '2020-11-24 14:05:47'),
(4, 11, '2020-11-25 14:05:26'),
(4, 16, '2020-11-25 14:05:59');

-- Dummy data for table `bid` (inserted 2 bids for the buyer under the same product to come up with "showing-only-the-recent-one" logic)

INSERT INTO `bid` (`bid_id`, `product_id`, `user_id`, `bid_amount`, `bid_creation_date`) VALUES
(1, 1, 2, 3350, '2019-08-09 08:00:26'), -- user bids near the start of creation date then bids again later below:
(2, 1, 2, '3400.00', '2020-11-25 13:28:46'),
(3, 2, 2, '10.00', '2020-11-25 13:50:22'),
(4, 2, 2, '30.50', '2020-11-25 13:50:45'),
(5, 11, 2, '32.50', '2020-11-25 13:50:56'),
(6, 11, 2, '33.00', '2020-11-25 13:51:22'),
(7, 2, 2, '40.00', '2020-11-25 13:51:36'),
(8, 11, 4, '50.00', '2020-11-25 13:52:12'),
(9, 11, 4, '51.00', '2020-11-25 13:52:27'),
(10, 11, 2, '55.00', '2020-11-25 13:52:48'),
(11, 11, 4, '60.00', '2020-11-25 13:53:16'),
(12, 11, 2, '61.20', '2020-11-25 13:54:01'),
(13, 15, 2, '11.00', '2020-11-25 13:54:09'),
(14, 4, 2, '100.00', '2020-11-25 13:54:24'),
(15, 15, 4, '15.00', '2020-11-25 13:54:50'),
(16, 12, 4, '40.00', '2020-11-25 13:55:02'),
(17, 5, 4, '100.50', '2020-11-25 13:55:30'),
(18, 14, 2, '11.00', '2020-11-25 13:55:46'),
(19, 15, 2, '20.00', '2020-11-25 13:56:04'),
(20, 12, 2, '50.00', '2020-11-25 13:56:16'),
(21, 12, 2, '55.00', '2020-11-25 13:56:39'),
(22, 12, 4, '65.00', '2020-11-25 13:57:10'),
(23, 12, 2, '70.00', '2020-11-25 13:57:32'),
(24, 4, 4, '200.00', '2020-11-25 13:58:14'),
(25, 2, 4, '50.00', '2020-11-25 13:58:34'),
(26, 1, 4, '3995.50', '2020-11-25 13:59:07'),
(27, 1, 4, '4000.00', '2020-11-25 13:59:38'),
(28, 16, 4, '330.00', '2020-11-25 13:59:57'),
(29, 6, 4, '130.20', '2020-11-25 14:00:33'),
(30, 15, 2, '22.00', '2020-11-25 14:01:11');

COMMIT;
