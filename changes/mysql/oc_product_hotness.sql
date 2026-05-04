CREATE TABLE oc_product_hotness(
	product_id int(11) PRIMARY KEY,
	hotness_points float(11) NOT NULL,
	modified datetime default now()
);
