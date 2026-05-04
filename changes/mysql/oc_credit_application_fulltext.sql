ALTER TABLE `oc_credit_application` ADD FULLTEXT(first_name, middle_name, last_name);
ALTER TABLE `oc_credit_application` ADD FULLTEXT(current_city, permanent_city);
