DELIMITER $$
DROP TRIGGER IF EXISTS oc_order_total_update_trigger_after_change_order_status $$

CREATE DEFINER=`root`@`localhost` TRIGGER `oc_order_total_update_trigger_after_change_order_status` AFTER UPDATE ON `oc_suborder`
FOR EACH ROW
BEGIN
    DECLARE order_total DECIMAL(15,4);
    DECLARE advance_voucher_amount DECIMAL(15,2);
    DECLARE total_payment_amount DECIMAL(15,2);
    DECLARE total_remaining_advance_amount DECIMAL(15,2);
    SET @advance_voucher_count = 0;
    SET @MIN_VAL = 0;
  
    IF NEW.order_status_id != OLD.order_status_id THEN

        (SELECT CEIL(SUM(osa.total)) INTO @order_total FROM oc_suborder osa WHERE osa.order_status_id>0 AND osa.order_status_id!=2 AND osa.order_id=OLD.order_id GROUP BY osa.order_id);
        UPDATE oc_order oo SET oo.total  = @order_total WHERE oo.order_id = OLD.order_id;
      
    END IF;
  
    IF ((NEW.total <> OLD.total OR
         NEW.shipping_charge <> OLD.shipping_charge) AND
       NEW.invoice_no > 0 AND
       NEW.order_status_id > 0 AND
       NEW.order_status_id !=2) THEN

        (SELECT COUNT(oav.advance_voucher_id) INTO @advance_voucher_count
        FROM oc_advance_voucher oav
        WHERE oav.suborder_id = NEW.suborder_id AND
              oav.status = 1
        GROUP BY oav.suborder_id);
       
      
        IF @advance_voucher_count = 1 THEN
         (SELECT SUM(oav.value) INTO @advance_voucher_amount
        FROM oc_advance_voucher oav
        WHERE oav.order_id = NEW.order_id AND
              oav.suborder_id != NEW.suborder_id AND
              oav.status = 1
        GROUP BY oav.order_id);
           
        (SELECT SUM(oopt.amount) INTO @total_payment_amount
        FROM oc_order_payment oopt
        WHERE oopt.order_id = NEW.order_id AND
              oopt.successfull=1
        GROUP BY oopt.order_id);
        SET @total_remaining_advance_amount = (@total_payment_amount - @advance_voucher_amount);
          
        UPDATE oc_advance_voucher oav SET oav.value = LEAST(@total_remaining_advance_amount, NEW.total) WHERE oav.suborder_id = NEW.suborder_id;
END IF;
    END IF;

      
END$$
DELIMITER ;