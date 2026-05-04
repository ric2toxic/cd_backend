DELIMITER $$
DROP TRIGGER IF EXISTS
  after_insert_ms_seller_table_update_oc_customer_table $$
CREATE DEFINER=`root`@`localhost` TRIGGER `after_insert_ms_seller_table_update_oc_customer_table` AFTER
INSERT ON
  `oc_ms_seller` FOR EACH ROW
BEGIN
SET
  @rowcount = 0 ;
(
  SELECT
    COUNT(oc.customer_id)
  INTO
    @rowcount
  FROM
    oc_customer oc
  WHERE
    oc.gst_number = NEW.gst_provisional_id AND oc.customer_id != NEW.seller_id
  GROUP BY
    oc.customer_id
) ;
IF @rowcount < 1 
THEN
UPDATE
  oc_customer oc
SET
  oc.gst_number = NEW.gst_provisional_id
WHERE
  oc.customer_id = NEW.seller_id ;
END IF ;
END $$
DELIMITER ;
