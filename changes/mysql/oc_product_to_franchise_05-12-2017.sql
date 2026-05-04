CREATE TABLE `oc_product_to_franchise` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `franchise_id` int(11) NOT NULL,
  `new_product_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8
