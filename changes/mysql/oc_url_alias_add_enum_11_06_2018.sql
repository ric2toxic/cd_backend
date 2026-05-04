ALTER TABLE `oc_url_alias` 
CHANGE COLUMN `url_type` `url_type` ENUM('category', 'product', 'manufacturer', 'seller', 'information', 'filter', 'category_search', 'multiple_query_string') NULL DEFAULT NULL ;

