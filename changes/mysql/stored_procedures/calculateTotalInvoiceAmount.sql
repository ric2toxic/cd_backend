DELIMITER $$
DROP PROCEDURE IF EXISTS `calculateTotalInvoiceAmount`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `calculateTotalInvoiceAmount`(IN `in_suborder_id` VARCHAR(20),
                                                                         IN `in_edit_type_flags` INT(1),
                                                                         INOUT `total_invoice_amount` DECIMAL(13,2))
BEGIN
  DECLARE shipping_charge DECIMAL(13,2) DEFAULT 0.0;
  DECLARE shipping_tax DECIMAL(13,2) DEFAULT 0.0;
  DECLARE total_amount_exc_ship DECIMAL(13,2) DEFAULT 0.0;

  IF in_edit_type_flags = 0 THEN
     (SELECT
        SUM(((oop.price_per_piece + oop.discount_per_piece) * oop.quantity * oop.piece_in_set) * (1 + oop.output_tax_rates/100) ),
        MAX(oop.output_tax_rates) INTO
          total_amount_exc_ship,
          shipping_tax
      FROM oc_order_product oop
      WHERE oop.suborder_id = in_suborder_id AND
            oop.edit_type in ('YES', 'SELLER_LATER_DISPATCH','SELLER_APPROVED', 'SELLER_PARTIAL', 'DAMAGE_BY_COURIER_COMPANY')
      GROUP BY oop.suborder_id
      LIMIT 1);

  ELSE
     (SELECT
        SUM(((oop.price_per_piece + oop.discount_per_piece) * oop.quantity * oop.piece_in_set) * (1 + oop.output_tax_rates/100) ),
        MAX(oop.output_tax_rates) INTO
          total_amount_exc_ship,
          shipping_tax
      FROM oc_order_product oop INNER JOIN
          oc_suborder os ON oop.buyer_invoice_id = os.buyer_invoice_id
      WHERE oop.suborder_id = in_suborder_id AND
            oop.buyer_invoice_id > 0 AND
            oop.seller_invoice_id > 0
      GROUP BY oop.suborder_id
      LIMIT 1);

  END IF;

  SELECT os.shipping_charge INTO shipping_charge
  FROM oc_suborder os
  WHERE os.suborder_id = in_suborder_id
  LIMIT 1;

  IF total_amount_exc_ship IS NULL THEN
      SET total_amount_exc_ship = 0.00;
  END IF;

  IF shipping_charge IS NULL THEN
      SET shipping_charge = 0.00;
  END IF;

  IF shipping_tax IS NULL THEN
      SET shipping_tax = 0.00;
  END IF;

  IF total_amount_exc_ship > 0 THEN
      SET total_invoice_amount = ROUND( total_amount_exc_ship + (shipping_charge * (1 + shipping_tax/100) ) , 2);
  ELSE
      SET total_invoice_amount = 0;
  END IF;

END$$
DELIMITER ;