ALTER TABLE `oc_location` DROP `fax`;

ALTER TABLE `oc_location` ADD `location_type` ENUM('office','store','franchise','') NOT NULL AFTER `name`;


ALTER TABLE `oc_location` ADD `city` VARCHAR(255) NOT NULL AFTER `address`, ADD `postcode` VARCHAR(10) NOT NULL AFTER `city`, ADD `zone_id` VARCHAR(255) NOT NULL AFTER `postcode`, ADD `country_id` VARCHAR(255) NOT NULL AFTER `zone_id`;

ALTER TABLE `oc_location` ADD `show_on` ENUM('all','storelocator','contact') NOT NULL AFTER `telephone`;

ALTER TABLE `oc_location` ADD `direction_url` VARCHAR(255) NOT NULL AFTER `geocode`;

ALTER TABLE `oc_location` ADD `sort_order` INT(11) NOT NULL AFTER `open`;  


ALTER TABLE `oc_location` CHANGE `telephone` `telephone` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;


ALTER TABLE `oc_location` CHANGE `zone_id` `state` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `country_id` `country` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `oc_location` CHANGE `open` `timing` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `oc_location` ADD `status` TINYINT(1) NOT NULL DEFAULT '1' AFTER `direction_url`;

ALTER TABLE `oc_location` ADD `date_added` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `comment`, ADD `date_modified` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `date_added`;

ALTER TABLE `oc_location` CHANGE `sort_order` `sort_order` INT(11) NULL DEFAULT NULL; 


INSERT INTO `oc_location` (`location_id`, `name`, `location_type`, `address`, `city`, `postcode`, `state`, `country`, `telephone`, `show_on`, `geocode`, `direction_url`, `status`, `image`, `timing`, `sort_order`, `comment`, `date_added`, `date_modified`) VALUES
(1, 'Head Office Jaipur', 'office', 'B-1, Crystal Mall, Banipark', 'Jaipur', '302016', 'Rajasthan', 'India', '(+91) 0141-4049163, (+91) 9116121383', 'all', '26.9207735,75.791692', 'https://goo.gl/maps/VF3YkpzumJU2', 1, 'catalog/wsbjaipur.png', '', 1, '', '2018-05-03 15:22:35', '2018-05-03 15:22:35'),
(3, 'Bangalore', 'store', 'Shop No 2118, 1st floor NKS Premier, \r\nNear MH Plaza, Mamulpet', 'Bangalore', '560053', 'Karnataka', 'india', '+91-8048527226, (+91) 9116121383', 'all', '12.972442,77.580643', 'https://goo.gl/maps/4q7VYZutYq62', 1, 'catalog/wsbbangalore.png', ' Mon. To Sat.(9:00 am to 7:00 pm)', 5, '', '2018-05-03 15:22:35', '2018-05-03 16:44:35'),
(4, 'Surat', 'store', 'A 2009/10, Millennium Textile Market, Ring Road', 'Surat', '395002', 'Gujarat', 'India', '(+91) 9116121383', 'all', '21.195,72.819444', 'https://goo.gl/maps/YVb5xBiWsK72', 1, 'catalog/wsbsurat.png', 'Mon. To Sat.(10:30 am to 8:00 pm)', 3, '', '2018-05-03 15:22:35', '2018-05-03 18:04:14'),
(5, 'Mumbai(Dadar)', 'store', '30, Bhoomi Plaza, Ground floor, Matkar marg,\r\nMasjid Gali, Dadar West', 'Mumbai', '400028', 'Maharashtra', 'India', '022 4973 1112, (+91) 9116121383', 'all', '18.9672614,72.8282709', 'https://goo.gl/Vp7mjt', 1, '', 'Mon. To Sat.(9:00 am to 7:00 pm)', 6, '', '2018-05-03 15:22:35', '2018-05-03 18:02:22'),
(6, 'Delhi', 'store', 'G-5 &amp; G 6, Ground Floor, Vardhman diamond \r\nplaza, DB Gupta Road, Motia Khan, Paharganj', 'Delhi', '110055', 'New Delhi', 'India', '(+91) 9116121383', 'all', '28.38,77.12', 'https://goo.gl/maps/UrrwjRFPkdm', 1, 'catalog/wsbdelhi.png', 'Mon. To Sat.(10:00 am to 7:00 pm) Sun.(9:00 am To 6:00 pm )', 4, '', '2018-05-03 15:22:35', '2018-05-03 17:59:08'),
(8, 'Kolkata', 'store', 'Shop No. NI-145, SS Hogg Market, New Market', 'Kolkata', '700087', 'West Bengal', 'India', '(+91) 9116121383', 'all', '22.6055046,88.4011', 'https://goo.gl/GrBW9E', 1, '', 'Mon. To Sat.(9:00 am to 7:00 pm)', 7, '', '2018-05-03 15:22:35', '2018-05-03 18:01:31'),
(9, 'Hyderabad', 'store', 'Shop No. 4-1-834/A, Sattar Plaza, Behind Big Bazar \r\n&amp; MPM Mall, Abibs Circle GPO', 'Hyderabad', '500001', 'Telangana', 'India', ' +91 9849857072 ', 'storelocator', '17.388732,78.47759489999999', 'https://goo.gl/tAQx22', 1, '', 'Mon. To Sat.(9:00 am to 7:00 pm)', 8, '', '2018-05-03 15:22:35', '2018-05-03 17:59:48'),
(10, 'Indore', 'store', 'Shop No. 69, Nai Basti, Bicholi Mardana', 'Indore', '452016', 'Madhya Pradesh', 'India', '+91 8956558622', 'storelocator', '22.7087726,75.9151283', 'https://goo.gl/maps/oNtHgzWLcGK2', 1, '', 'Mon. To Sat.(9:00 am to 7:00 pm)', 9, '', '2018-05-03 15:22:35', '2018-05-03 18:00:12'),
(11, 'Bhopal', 'store', '10, 11 Hare Krishna Complex 10 Number Market, \r\nE-3 Arera Colony', 'Bhopal', '462016', 'Madhya Pradesh', 'India', '91 8871939160 ', 'storelocator', '23.2140308,77.4363928', 'https://goo.gl/maps/6HPfdjZetWp', 1, '', 'Mon. To Sat.(9:00 am to 7:00 pm)', 10, '', '2018-05-03 15:22:35', '2018-05-03 17:58:45'),
(14, 'Jaipur', 'store', 'S-3 Main Chowk, Dhula House, Bapu Bazar', 'Jaipur', '302003', 'Rajasthan', 'India', '(+91) 7230982729', 'all', '26.9167428,75.820902', 'https://goo.gl/maps/Nc8sqrhWZq42', 1, 'catalog/wsbjaipur.png', '', 2, '', '2018-05-03 15:22:35', '2018-05-03 18:00:40'),
(17, 'Visakhapatnam', 'store', '31-30-37, Narayana Street, Daba Gardens', 'Visakhapatnam', '530020', 'Andhra Pradesh', 'India', '+91 7032748786 ', 'storelocator', '17.7153496,83.295898', 'https://goo.gl/maps/x1bcapEYkX22', 1, '', 'Mon. To Sat.(9:00 am to 7:00 pm)', 11, '', '2018-05-03 15:22:35', '2018-05-03 18:07:02');
