ALTER TABLE oc_customer_cart ADD COLUMN last_cart_modified_from enum('ANDROID','IOS','DWEB','MWEB') default 'DWEB'; 
ALTER TABLE oc_customer_cart ADD COLUMN  cart_modified_once_from text;
