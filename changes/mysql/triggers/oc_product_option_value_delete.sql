DELIMITER |

DROP TRIGGER IF EXISTS oc_product_option_value_delete_trigger |

CREATE DEFINER=`root`@`localhost` TRIGGER oc_product_option_value_delete_trigger 
AFTER DELETE ON oc_product_option_value
FOR EACH ROW
	BEGIN
		DECLARE filter_id INT;
		DECLARE filter_name VARCHAR(64);
		IF( OLD.option_id = 11 OR OLD.option_id = 5 ) THEN
			SET filter_name = (SELECT o.name FROM oc_option_value_description o WHERE o.option_value_id = OLD.option_value_id AND o.language_id = 1 LIMIT 1);
			SET filter_id = (SELECT f.filter_id FROM oc_filter_description f WHERE f.name = filter_name AND f.language_id = 1 LIMIT 1);
			IF filter_id IS NOT NULL THEN
				DELETE FROM oc_product_filter WHERE (oc_product_filter.product_id = OLD.product_id AND oc_product_filter.filter_id = filter_id);
			END IF;
		END IF;
	END
|
DELIMITER ;
