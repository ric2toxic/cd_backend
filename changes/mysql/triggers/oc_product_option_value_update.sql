DELIMITER |

DROP TRIGGER IF EXISTS oc_product_option_value_update_trigger |

CREATE DEFINER=`root`@`localhost` TRIGGER oc_product_option_value_update_trigger 
AFTER UPDATE ON oc_product_option_value
FOR EACH ROW
	BEGIN
		DECLARE filter_id INT;
		DECLARE filter_name VARCHAR(64);
		DECLARE filter_name_hindi VARCHAR(64);
		DECLARE product_id INT;
		DECLARE filter_group_id INT;
		IF( ( NEW.option_id = 11 OR NEW.option_id = 5 ) AND OLD.quantity = 0 AND NEW.quantity > 0) THEN
			IF (NEW.option_id = 11) THEN
				SET filter_group_id = 9;
			ELSE
				SET filter_group_id = 8;
			END IF;
			SET filter_name = (SELECT o.name FROM oc_option_value_description o WHERE o.option_value_id = NEW.option_value_id AND o.language_id = 1 LIMIT 1);
			SET filter_id = (SELECT f.filter_id FROM oc_filter_description f WHERE f.name = filter_name AND f.language_id = 1 LIMIT 1);
			IF filter_id IS NULL THEN
				INSERT INTO oc_filter SET filter_group_id = filter_group_id, sort_order = 0;
				SET filter_id = (SELECT LAST_INSERT_ID());
				INSERT IGNORE INTO oc_filter_description SET filter_id = filter_id, language_id = 1, filter_group_id = filter_group_id, name = filter_name;
				SET filter_name_hindi = (SELECT o.name FROM oc_option_value_description o WHERE o.option_value_id = NEW.option_value_id AND o.language_id = 2 LIMIT 1);
				INSERT IGNORE INTO oc_filter_description SET filter_id = filter_id, language_id = 2, filter_group_id = filter_group_id, name = filter_name_hindi;
			END IF;
			INSERT IGNORE INTO oc_product_filter SET product_id = NEW.product_id, filter_id = filter_id;
		END IF;
		IF( ( NEW.option_id = 11 OR NEW.option_id = 5 ) AND NEW.quantity = 0) THEN
			SET filter_name = (SELECT o.name FROM oc_option_value_description o WHERE o.option_value_id = NEW.option_value_id AND o.language_id = 1 LIMIT 1);
			SET filter_id = (SELECT f.filter_id FROM oc_filter_description f WHERE f.name = filter_name AND f.language_id = 1 LIMIT 1);
			IF filter_id IS NOT NULL THEN
				DELETE FROM oc_product_filter WHERE (oc_product_filter.product_id = NEW.product_id AND oc_product_filter.filter_id = filter_id);
			END IF;
		END IF;
	END
|
DELIMITER ;
