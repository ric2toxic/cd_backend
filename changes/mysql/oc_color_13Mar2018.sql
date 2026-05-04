CREATE TABLE `oc_color` (
  `color_id` INT(11) NOT NULL AUTO_INCREMENT,
  `language_id` INT(11) NOT NULL,
  `name` VARCHAR(45) NOT NULL,
  `hex_code` VARCHAR(45) NULL,
  `synonyms` TEXT NULL,
  PRIMARY KEY (`color_id`));

