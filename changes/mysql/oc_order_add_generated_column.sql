ALTER TABLE 
	oc_order 
ADD COLUMN 
	order_no_last_five_digits varchar(5) GENERATED ALWAYS AS (RIGHT(order_no, 5)) 
    VIRTUAL COMMENT "this columns contains the last five digits of order no";

ALTER TABLE `oc_order` ADD INDEX(`order_no_last_five_digits`);
