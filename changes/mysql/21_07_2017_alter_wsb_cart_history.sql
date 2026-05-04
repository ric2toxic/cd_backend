ALTER TABLE oc_wsb_cart_history ADD COLUMN last_cart_modified_from enum('ANDROID','IOS','DWEB','MWEB') default 'DWEB'; 
