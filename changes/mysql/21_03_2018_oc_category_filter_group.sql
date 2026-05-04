--
-- Table structure for table `oc_category_filter_group`
--

CREATE TABLE `oc_category_filter_group` (
  `category_id` int(11) NOT NULL,
  `filter_group_id` int(11) NOT NULL,
  `sort_order` int(11) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oc_category_filter_group`
--
ALTER TABLE `oc_category_filter_group`
  ADD PRIMARY KEY (`category_id`,`filter_group_id`);
