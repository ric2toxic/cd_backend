ALTER TABLE `oc_order` ADD FULLTEXT(firstname, lastname, email, telephone);
ALTER TABLE `oc_order` ADD FULLTEXT(shipping_city);
ALTER TABLE `oc_order` ADD FULLTEXT(shipping_company);
