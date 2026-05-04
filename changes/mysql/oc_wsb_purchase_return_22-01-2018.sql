CREATE TABLE `oc_wsb_purchase_return` (
  `debit_note_id` int(11) NOT NULL,
  `purchase_id` int(11) NOT NULL DEFAULT '0',
  `purchase_firm_id` int(11) NOT NULL DEFAULT '0',
  `debit_note_prefix` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
  `debit_note_no` int(11) NOT NULL DEFAULT '0',
  `debit_note_status` tinyint(1) NOT NULL DEFAULT '1',
  `debit_note_amount` float(10,2) NOT NULL DEFAULT '0.00',
  `uesr_id` int(11) NOT NULL DEFAULT '0',
  `user_name` varchar(255) DEFAULT NULL,
  `date_added` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


ALTER TABLE `oc_wsb_purchase_return`
  ADD PRIMARY KEY (`debit_note_id`);


ALTER TABLE `oc_wsb_purchase_return`
  MODIFY `debit_note_id` int(11) NOT NULL AUTO_INCREMENT;
