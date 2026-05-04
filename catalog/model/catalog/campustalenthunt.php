<?php
class Modelcatalogcampustalenthunt extends Model{

	public function campustalenthunt($data_array){


	 $q = "INSERT INTO ".DB_PREFIX."campus_member
			  SET
			  member_name = '".$data_array['name']."',
			  member_contact_no = '".$data_array['contact']."',
			  member_email = '".$data_array['email']."',
			  internship_company = '".$data_array['internship_company']."',
			  grade_point = '".$data_array['grade_point']."',
			  internship_role = '".$data_array['internship_role']."',
			  desired_profile = '".$data_array['desired_profile']."',
			  team_id = ".$data_array['team_id'].",
			  team_member_id = '".$data_array['team_member_id']."',
			  member_city = '".$data_array['city']."',
			  campus_name = '".$data_array['campus']."'
			  ";

		$query = $this->db->query($q);
		
	}

	public function createTeam($team_name, $campus_id){
		$q = "INSERT INTO ".DB_PREFIX."campus_team
			  SET
			  campus_id = ". $campus_id .",
			  team_name = '".$team_name."'";

		if($this->db->query($q)) {

			return $this->db->getLastId();
		}
	}

	public function getCampuses(){

		$q = "SELECT * FROM ".DB_PREFIX."campus WHERE status = 1 ORDER BY campus_name";

		$query = $this->db->query($q);

		return $query->rows;
	}

	public function getCampusName($campus_id){

		$q = "SELECT campus_name FROM ".DB_PREFIX."campus WHERE campus_id = ".$campus_id;

		$query = $this->db->query($q);

		return $query->row['campus_name'];
	}

	public function getMemberByPhoneToValidate($phone){

		$q = "SELECT COUNT(*) AS total FROM ".DB_PREFIX."campus_member WHERE member_contact_no = '".$phone."'";

		$query = $this->db->query($q);

		return $query->row['total'];
	}

	public function getMemberByEmailToValidate($email){

		$q = "SELECT COUNT(*) AS total FROM ".DB_PREFIX."campus_member WHERE member_email = '".$email."'";

		$query = $this->db->query($q);

		return $query->row['total'];
	}
}