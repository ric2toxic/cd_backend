CREATE TABLE oc_customer_tokens (
 customer_id int(11) NOT NULL,
 wsb_access_token varchar(255) NOT NULL,
 device_id varchar(255) DEFAULT NULL,
 access_type enum('web','web-mobile','app') DEFAULT NULL,
 login_date datetime NOT NULL,
 device_info varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE oc_customer_tokens;
