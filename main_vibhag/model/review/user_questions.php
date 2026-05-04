<?php
class ModelReviewUserQuestions extends Model {

	public function getQuestions($data  = array())
	{
		$sql = "SELECT wq.question_id,
					   wq.customer_id,
					   wq.customer_name,
					   wq.telephone,
					   wq.email as customer_email,
					   wq.question,
					   wq.is_answered,
					   wq.date_added,
					    p.product_id,
						p.model,
						p.image,
					   ms.company,
					   ms.mobile_no,
					   ms.email as seller_email
				FROM  oc_wsb_questions wq
				INNER JOIN oc_product p
					ON p.product_id = wq.product_id 
				INNER JOIN oc_ms_product mp
					ON mp.product_id = p.product_id 
				INNER JOIN oc_ms_seller ms
					ON ms.seller_id = mp.seller_id 
				WHERE ";

		$filter_answered = trim($data['filter_answered'] ?? '');
		if ( $filter_answered === '0' || $filter_answered === '1' ) {
		    $sql .= " wq.is_answered = " .(int)$filter_answered;
        } else {
		    $sql .= " wq.is_answered IN (0,1)";
        }

		$filter_seller_code = trim($data['filter_seller_code'] ?? '');
		if ( !empty($filter_seller_code) ) {
		    $sql .= " AND ms.nickname LIKE '" . $this->db->escape($filter_seller_code) . "'";
        }

        // Pagination related filter condition
        $p = $data['page'] ?? '';
        if ( $p !== 'FIRST' && (int)$p > 0 ) {
            $sql .= " AND wq.question_id < " . (int)$p;
        }

		$sql .= " ORDER BY wq.question_id DESC";

        if (isset($data['limit'])) {
            if ( (int)$data['limit'] < 1) {
                $data['limit'] = 10;
            }

            $sql .= " LIMIT " . (int) $data['limit'];
        }

		$query = $this->db->query($sql);
		return $query->rows;
	}

	public function getTotalUnansweredQuestions() : int {
		$sql = "SELECT COUNT(*) AS total
				FROM " . DB_PREFIX . "wsb_questions 
				WHERE is_answered = 0";
		$query = $this->db->query($sql);
		return (int)($query->row['total'] ?? 0);
	}

	public function markAnswered($answered, $qid) : void {
		$sql = "UPDATE oc_wsb_questions 
                SET is_answered = " . (int)$answered . " 
                WHERE question_id = " . (int)$qid;
		$this->db->query($sql);
	}
	public function updateQuestion($question_text, $qid) : void	{
	    $question_text = trim($question_text);
	    if ( !empty($question_text) ) {
            $sql = "UPDATE oc_wsb_questions 
                    SET question = '" . $this->db->escape($question_text) ."' 
                    WHERE question_id = " . (int)$qid;
            $this->db->query($sql);
        }
	}

	// deleting question
	public function deleteQuestion($id) : void {
		$this->db->query("DELETE FROM " . DB_PREFIX . "wsb_questions 
		                  WHERE question_id = " . (int)$id);
	}

}