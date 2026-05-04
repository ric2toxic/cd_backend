<?php
class ModelReportCreditApplications extends Model {
	
	public function getActivateCasesReportData()
	{
		$data['activated_cases'] = $this->getCreditActivatedCases('activated_cases');
		$data['activated_total_limit'] = $this->getCreditActivatedCases('activated_total_limit');
		return $data;
	}

	protected function getCreditActivatedCases(string $type)
	{	
		$field = '1';
		if($type == 'activated_cases') {
			$field = '1';
		}else if($type == 'activated_total_limit'){
			$field = 'dt.credit_limit';
		}
		
		$sql = "  
				SELECT
					SUM( IF( last_activation_date = CURDATE(), ".$field.", 0) ) as `Today`,
					SUM( IF( last_activation_date = CURDATE() + INTERVAL -1 DAY, ".$field.", 0) ) AS `T-1`,
					SUM( IF( last_activation_date = CURDATE() + INTERVAL -2 DAY, ".$field.", 0) ) AS `T-2`,
					SUM( IF( last_activation_date = CURDATE() + INTERVAL -3 DAY, ".$field.", 0) ) AS `T-3`,
					SUM( IF( last_activation_date >= LAST_DAY(CURRENT_DATE) + INTERVAL 1 DAY - INTERVAL 1 MONTH
	  						AND 
	  						last_activation_date < LAST_DAY(CURRENT_DATE) + INTERVAL 1 DAY,
	  						".$field.", 
							0) 
						) AS `MTD`,	
					SUM( IF( last_activation_date >= LAST_DAY(CURRENT_DATE - INTERVAL 2 MONTH ) + INTERVAL 1 DAY 
	  						AND 
	  						last_activation_date < LAST_DAY(CURRENT_DATE - INTERVAL 1 MONTH),
	  						".$field.", 
							0) 
						) AS `LAST_MONTH`, ";
		
		if($type == 'activated_cases') {

			$sql .= " COUNT( customer_id ) as `TILL_DATE` "; 

		}else if($type == 'activated_total_limit'){

			$sql .= " SUM( dt.credit_limit ) as `TILL_DATE` "; 
		}
		$sql .= "			
					FROM ( 
						SELECT 
							cwc.customer_id,
							DATE(MIN(cwcl.date_added)) AS last_activation_date,
							cwc.credit_limit
						FROM 
							".DB_PREFIX."customer_wsb_credit AS cwc 

						INNER JOIN 
							".DB_PREFIX."customer_wsb_credit_log AS cwcl 
									ON cwcl.customer_id = cwc.customer_id 
										AND
									   cwcl.status = 'ENABLED'

						WHERE
							cwc.status = 'ENABLED'
						GROUP BY 
							cwcl.customer_id
				 ) as dt 
				";
		//echo $sql; die;
		$result = $this->db->query($sql);
		if($result->num_rows) {
			return $result->row;
		}
		return array();
	}

	public function getCreditApprovedCasesData()
	{
		$data['approved_cases'] 				= $this->getCreditApprovedCases('approved_document_received','cases');
		$data['approved_total_limit'] 			= $this->getCreditApprovedCases('approved_document_received','limit');
		$data['transit_documents_today'] 		= $this->getCreditApprovedCases('document_in_transit','cases');
		$data['approved_but_agreement_pending'] = $this->getCreditApprovedCases('approved_but_agreement_pending','cases');
		$data['approved_total_but_agreement_pending'] = $this->getCreditApprovedCases('approved_but_agreement_pending','limit');
		return $data;
	}
	protected function getCreditApprovedCases(string $document_status, string $type)
	{
		$field = ($type == 'cases') ? 1 : 'credit_limit';

		$sql = " 
				SELECT
					SUM( IF( last_updated_date = CURDATE(), ".$field.", 0 ) ) as `Today`,
					SUM( IF( last_updated_date = CURDATE() + INTERVAL -1 DAY, ".$field.", 0 ) ) AS `T-1`,
					SUM( IF( last_updated_date = CURDATE() + INTERVAL -2 DAY, ".$field.", 0 ) ) AS `T-2`,
					SUM( IF( last_updated_date = CURDATE() + INTERVAL -3 DAY, ".$field.", 0 ) ) AS `T-3`,
					SUM( IF( last_updated_date >= LAST_DAY(CURRENT_DATE) + INTERVAL 1 DAY - INTERVAL 1 MONTH
	  						AND 
	  						last_updated_date < LAST_DAY(CURRENT_DATE) + INTERVAL 1 DAY,
	  						".$field.", 0) 
						) AS `MTD`,
					SUM( IF( last_updated_date >= LAST_DAY(CURRENT_DATE - INTERVAL 2 MONTH ) + INTERVAL 1 DAY 
	  						AND 
	  						last_updated_date < LAST_DAY(CURRENT_DATE - INTERVAL 1 MONTH),
	  						".$field.", 0) 
						) AS `LAST_MONTH`, " ;

		
		if($type == 'cases') {

			$sql .= " COUNT( customer_id ) as `TILL_DATE` "; 

		}else if($type == 'limit'){

			$sql .= " SUM( credit_limit ) as `TILL_DATE` "; 
		}

		$sql .= "
				FROM ( SELECT 
							ca.customer_id,
							DATE(MAX(casr.date_added)) AS last_updated_date,
							casr.credit_limit  
						FROM 
							".DB_PREFIX."credit_application AS ca
						LEFT JOIN 
							".DB_PREFIX."credit_application_status_remarks AS casr 
										ON casr.credit_application_id = ca.id
						WHERE
							casr.status = '".$this->db->escape($document_status)."'
						GROUP BY 
							ca.customer_id 
					) as dt 
				 
				";
		//echo $sql; die;		
		$result = $this->db->query($sql);
		if($result->num_rows) {
			return $result->row;
		}
		return array();

	}
	

	public function getApplicationRecordsData(string $document_status)
	{
		$data['step_0'] = $this->getApplicationRecordsCases(0, $document_status);
		$data['step_1'] = $this->getApplicationRecordsCases(1, $document_status);
		$data['step_2'] = $this->getApplicationRecordsCases(2, $document_status);
		//$data['step_2_with_bank_statement'] = $this->getApplicationRecordsCases(2, $document_status, true);

		return $data;
	}

	protected function getApplicationRecordsCases(int $step, string $document_status, bool $with_bank_statment = false)
	{
		$where = array();

		$where[] = ' draft = '.$step;

		if(!empty($document_status)) {
			if($document_status == 'no_status') {
				$where[] = " ( document_status IS NULL OR document_status = '') ";
			}else{
				$where[] = " document_status = '".$this->db->escape($document_status)."' ";	
			}
		}

		if(!empty($where)) {
			$where = " WHERE " . implode(" AND ", $where);
		}

		$sql = "
				SELECT
					SUM( IF( first_updated_date = CURDATE() AND draft=".$step.", 1, 0 ) ) as `Today`,
					SUM( IF( first_updated_date = CURDATE() + INTERVAL -1 DAY AND draft=".$step.", 1, 0 ) ) AS `T-1` , 
					SUM( IF( first_updated_date = CURDATE() + INTERVAL -2 DAY AND draft=".$step.", 1, 0 ) ) AS `T-2` , 
					SUM( IF( first_updated_date = CURDATE() + INTERVAL -3 DAY AND draft=".$step.", 1, 0 ) ) AS `T-3` ,
					SUM( IF( first_updated_date >= LAST_DAY(CURRENT_DATE) + INTERVAL 1 DAY - INTERVAL 1 MONTH AND first_updated_date < LAST_DAY(CURRENT_DATE) + INTERVAL 1 DAY AND draft=".$step.", 1, 0 ) ) AS `MTD`, 
					SUM( IF( first_updated_date >= LAST_DAY(CURRENT_DATE - INTERVAL 2 MONTH ) + INTERVAL 1 DAY 
	  						AND 
	  						first_updated_date < LAST_DAY(CURRENT_DATE - INTERVAL 1 MONTH)
	  						AND 
	  						draft=".$step.",
	  						1, 
							0) 
							) AS `LAST_MONTH`, 
					COUNT( dt.id ) AS `TILL_DATE`

					FROM 
					( 
						SELECT 
							ca.id,
							ca.draft,
							ca.document_status, 
							COALESCE( DATE(MIN(casr.date_added)), DATE(ca.created_date)) AS first_updated_date 
						FROM 
							".DB_PREFIX."credit_application AS ca 
						
						LEFT JOIN 
							".DB_PREFIX."credit_application_status_remarks AS casr
								ON casr.credit_application_id = ca.id

						".$where."
						GROUP BY 
							ca.id
					) as dt 

				";
		//echo $sql; die;
		$result = $this->db->query($sql);
		if($result->num_rows) {
			return $result->row;
		}
		return array();
	}

	public function getUnderProcessApplicationsData()
	{
		$data['counts'] = array();

		$sql = "
				SELECT 
					SUM( IF( first_updated_date BETWEEN CURDATE() - INTERVAL 5 day AND CURDATE(), 1, 0 ) ) as `5 Days`,
					SUM( IF( first_updated_date BETWEEN CURDATE() - INTERVAL 10 day AND CURDATE() - INTERVAL 5 day, 1, 0 ) ) as `10 Days`,
					SUM( IF( first_updated_date BETWEEN CURDATE() - INTERVAL 20 day AND CURDATE() - INTERVAL 10 day, 1, 0 ) ) as `20 Days`,
					SUM( IF( first_updated_date BETWEEN CURDATE() - INTERVAL 31 day AND CURDATE() - INTERVAL 20 day, 1, 0 ) ) as `30 Days`
				FROM
					(
						SELECT
							ca.id, 
							DATE(MIN(casr.date_added)) AS first_updated_date 
						FROM 
							".DB_PREFIX."credit_application AS ca 
						
						LEFT JOIN 
							".DB_PREFIX."credit_application_status_remarks AS casr
								ON casr.credit_application_id = ca.id
								AND
								casr.status =  'approved_document_received'
						WHERE
							ca.document_status = 'approved_document_received'
						GROUP BY
							ca.id	
					) as dt
				";
		//echo $sql; die;
		$result = $this->db->query($sql);
		if($result->num_rows) {
			$data['counts'] = $result->row;
		}
		return $data;	
	}


	public function getUnderProcessApplicationsToProcess(string $days)
	{
		$days = trim(str_replace('Days', '', $days));
		$sql = "
				SELECT 
					GROUP_CONCAT(ca.id) as ids
				FROM 
					".DB_PREFIX."credit_application AS ca
				INNER JOIN 
					".DB_PREFIX."credit_application_status_remarks AS casr
					ON casr.credit_application_id = ca.id	
				WHERE
					casr.status = 'approved_document_received'
					AND
					casr.date_added > CURDATE() - INTERVAL ".$this->db->escape($days)." DAY
		";
		$result = $this->db->query($sql);
		if($result->num_rows) {
			return $result->row['ids'];
		}
		return array();

	}

}