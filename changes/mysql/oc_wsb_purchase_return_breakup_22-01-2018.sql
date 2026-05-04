CREATE TABLE `oc_wsb_purchase_return_breakup` (
  `id` int(11) NOT NULL,
  `debit_note_id` int(11) NOT NULL DEFAULT '0',
  `purchase_id` int(11) NOT NULL DEFAULT '0',
  `purchase_firm_id` int(11) NOT NULL DEFAULT '0',
  `product_id` int(11) NOT NULL DEFAULT '0',
  `model` varchar(255) DEFAULT NULL,
  `sku` varchar(255) DEFAULT NULL,
  `hsn_code` varchar(25) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `user_name` varchar(255) DEFAULT NULL,
  `date_added` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


ALTER TABLE `oc_wsb_purchase_return_breakup`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `oc_wsb_purchase_return_breakup`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
