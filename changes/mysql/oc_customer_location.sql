CREATE TABLE `oc_customer_location` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `device_id` varchar(255) DEFAULT NULL,
  `location_lat` varchar(50) NOT NULL,
  `location_lng` varchar(50) NOT NULL,
  `location_timestamp` varchar(50) DEFAULT NULL,
  `day` varchar(100) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_customer_location`
--
ALTER TABLE `oc_customer_location`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oc_customer_location`
--
ALTER TABLE `oc_customer_location`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
