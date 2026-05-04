DELIMITER $$
DROP PROCEDURE IF EXISTS `updateTotalInvoiceAmount`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `updateTotalInvoiceAmount`(IN `suborder_id` VARCHAR(20))
BEGIN
    DECLARE gst_flag TINYINT(1);
    DECLARE edit_type_flags INT(11);
    DECLARE order_id INT(11);
    SET @suborder_id = suborder_id;
    SET @total_invoice_amount = 0;
    SET @order_total = 0;

    (SELECT os.invoice_no, os.gst, os.order_id INTO @edit_type_flags, @gst_flag, @order_id FROM oc_suborder os WHERE os.suborder_id = @suborder_id);

    IF @gst_flag=1 THEN
        CALL calculateTotalInvoiceAmount(@suborder_id, @edit_type_flags, @total_invoice_amount); 
        UPDATE oc_suborder osu SET osu.total  = @total_invoice_amount WHERE osu.suborder_id = @suborder_id;

        (SELECT ROUND(SUM(osa.total), 2) INTO @order_total FROM oc_suborder osa WHERE osa.order_status_id>0 AND osa.order_status_id!=2 AND osa.order_id=@order_id GROUP BY osa.order_id);
        UPDATE oc_order oo SET oo.total  = @order_total WHERE oo.order_id = @order_id;

    END IF;
   
END$$
DELIMITER ;