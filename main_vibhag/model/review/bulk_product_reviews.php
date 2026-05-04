<?php
class ModelReviewBulkProductReviews extends Model {
	public function addReview($data) {
		$seller_code = $data['seller_code'];
		$rating = $data['rating'];

		$sql_product_id = "SELECT mp.product_id
                FROM " . DB_PREFIX. "ms_product mp
                INNER JOIN oc_ms_seller ms
                  ON (mp.seller_id = ms.seller_id)
                WHERE ms.nickname = '".$seller_code."'";
		$query = $this->db->query($sql_product_id);

		foreach($query->rows as $key=>$values){
			$get_review_product = "SELECT product_id FROM oc_review WHERE product_id = '".$values['product_id']."' AND author = 'Admin' ";
			$result_value = $this->db->query($get_review_product);
			if($result_value->num_rows > 0){
				$this->db->query("UPDATE oc_review
                        SET rating='".$rating."',
                            status = 1,
                            customer_id= 0,
                            date_modified = NOW()
                        WHERE product_id ='".$values['product_id']."'
                            AND author = 'Admin';
                        ");
			}else{
				$this->db->query("INSERT INTO oc_review
                        SET product_id ='".$values['product_id']."',
                            customer_id= 0,
                            author='Admin',
                            rating='".$rating."',
                            status = 1,
                            date_added = NOW();
                        ");
			}
		}
	}

}