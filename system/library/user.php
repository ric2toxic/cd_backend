<?php
class User {

	private $user_id = 0;
	private $user_group_id = 0;
	private $username = '';
	private $firstname = '';
	private $lastname = '';
	private $email = '';
	private $access_token = '';
	private $branch_code = '';
	private $device_id = '';
	private $permission = array();

	private $route = '';
	private $controller = '';
	private $action = array();

	public function __construct($registry = null) {

		if (isset($registry)) {
			$this->db = $registry->get('db');
			$this->request = $registry->get('request');
			$this->session = $registry->get('session');

			if (isset($this->session->data['user_id'])) {
				$this->user_id = (int) $this->session->data['user_id'];
				$this->initializeUser();
			}
		}
	}

	/**
	 * To initialize user related details in the instance,
	 * if we have user_id available in the private variable,
	 * without actualling calling method login()
	 */
	private function initializeUser() {

		if (empty($this->user_id) || empty($this->db)) {
			return false;
		}

		$sql = "SELECT * FROM " . DB_PREFIX . "user
                WHERE user_id = '" . (int) $this->user_id . "'
                AND status = '1'";
		$user_query = $this->db->query($sql);

		if ($user_query->num_rows) {
			$this->user_id = $user_query->row['user_id'];
			$this->username = $user_query->row['username'];
			$this->user_group_id = $user_query->row['user_group_id'];
			$this->firstname = $user_query->row['firstname'];
			$this->lastname = $user_query->row['lastname'];
			$this->email = $user_query->row['email'];
			$this->access_token = $user_query->row['access_token'];
			$this->device_id = $user_query->row['device_id'];
			$this->branch_code = $user_query->row['branch_code'];

			$permissions = unserialize($user_query->row['permission']);

			if (is_array($permissions)) {
				foreach ($permissions as $key => $value) {
					$this->permission[$key] = $value;
				}
			}
		} else {
			$this->logout();
		}
	}

	/**
	 * To check for valid user login credentials,
	 * First check by username and then match password using password_hash() algorithm
	 * @param: string username
	 * @param: string password
	 * @return: bool
	 * @author: MSA Feb 2019
	 */
	public function login(string $username, string $password): bool{
		$user_query = $this->db->query("SELECT user_id,
                                               username,
                                               user_group_id,
                                               firstname,
                                               lastname,
                                               email,
                                               access_token,
                                               device_id,
                                               password,
                                               old_password,
                                               old_password_active,
                                               branch_code,
                                               permission
                                        FROM " . DB_PREFIX . "user
                                        WHERE
                                            username = '" . $this->db->escape($username) . "'
                                            AND
                                            status = '1' "
		);

		if ($user_query->num_rows) {

			$old_password_hash = $user_query->row['old_password'];
			$password_hash = $user_query->row['password'];

			$is_password_verify = false;
			$is_login_with_old_password = false;

			if (!empty($user_query->row['old_password']) && $user_query->row['old_password_active']) {

				if (password_verify($password, $old_password_hash)) {

					$is_password_verify = true;
					$is_login_with_old_password = true;

				} else if (password_verify($password, $password_hash)) {

					$is_password_verify = true;
				}

			} else {

				$is_password_verify = password_verify($password, $password_hash);
			}

			if ($is_password_verify) {
				$this->session->data['user_id'] = $user_query->row['user_id'];
				$this->session->data['branch_code'] = $user_query->row['branch_code'];

				$this->user_id = $user_query->row['user_id'];
				$this->username = $user_query->row['username'];
				$this->user_group_id = $user_query->row['user_group_id'];
				$this->firstname = $user_query->row['firstname'];
				$this->lastname = $user_query->row['lastname'];
				$this->email = $user_query->row['email'];
				$this->access_token = $user_query->row['access_token'];
				$this->device_id = $user_query->row['device_id'];
				$this->branch_code = $user_query->row['branch_code'];

				$permissions = unserialize($user_query->row['permission']);

				if (is_array($permissions)) {
					foreach ($permissions as $key => $value) {
						$this->permission[$key] = $value;
					}
				}

				// If user loggedIn with Old password, show popup box message
				if ($is_login_with_old_password) {

					$this->session->data['old_password_status'] = $user_query->row['old_password_active'];
					$this->session->data['username'] = $user_query->row['username'];

					$data = $this->getRandomPasswordString();

					$this->session->data['new_password_string'] = $data['password'];

					$password = password_hash($data['password'], PASSWORD_DEFAULT);

					/*
						                    * Do not allow second time to loggedIn using old password
						                    * So update fields - old_password & old_password_active
					*/
					$this->db->query("UPDATE `" . DB_PREFIX . "user`
                                        SET
                                            password     = '" . $this->db->escape($password) . "',
                                            old_password = NULL,
                                            old_password_active = 0
                                        WHERE
                                            user_id = '" . (int) $user_query->row['user_id'] . "'
                                    ");
				}

				return true;

			} else {

				return false;
			}

		} else {

			return false;
		}
	}

	/**
	 * Method to update device id in user table
	 * @param: int
	 * @return: void
	 */
	public function update_device_id(int $user_id, int $device_id) {
		$sql = "UPDATE " . DB_PREFIX . "user
                            SET device_id = '" . trim($this->db->escape($device_id)) . "' WHERE user_id = " . (int) $user_id;
		$this->db->query($sql);
		$this->device_id = $device_id;
	}

	/**
	 * Method to logout khufiya user login session
	 * @param: void
	 * @return: void
	 */
	public function logout() {
		unset($this->session->data['user_id']);
		$this->user_id = 0;
		$this->username = '';
		$this->user_group_id = 0;
		$this->firstname = '';
		$this->lastname = '';
		$this->email = '';
		$this->access_token = '';
		$this->device_id = '';
		$this->branch_code = '';
		$this->permission = array();
	}

	/**
	 * Public methos to check is controller
	 * @param: String
	 * @return : Boolean
	 * @author: Nishu, Sept 2018
	 */
	function checkControllerExist(string $value): bool{

		$valid_url = false;
		//Empty Check
		if (!empty($value)) {

			$controller = explode('/', $value);
			$this->controller = $controller[0] . '/' . $controller[1] . '.php';

			//Check for controller file existance
			$file_path = DIR_APPLICATION . 'controller/' . $this->controller;

			if (file_exists($file_path)) {
				$valid_url = true;
			}
		}

		return $valid_url;
	}

	/**
	 * @info: Public Method to check method level permissions
	 * @param:
	$key - Check Permission for
	$value - URL for which , perssion checks
	$check_method - Check Custome permissions from method with function name, value will be true
	OR if permission is checking at URL level, value will be false
	$id_admin_check - ADMIN_IDS constant is applicable or not
	 * @return: Boolen
	 * @author- Nishu, Manoj Addha, March 2018
	 */
	public function hasPermission($key = 'access', $value, $check_method = false) {

		//Check for controler or file path existance
		$valid_url = $this->checkControllerExist($value);
		if (!$valid_url) {
			return $valid_url;
		}

		$key = ($key != 'access') ? 'access' : $key;
		$allowed_ids = explode(',', ADMIN_IDS);

		/* Nornal permission structure checking */
		if (isset($this->request->get['route'])) {
			$this->route = (string) $this->request->get['route'];
			$controller = explode('/', $this->route);
			if (count($controller) > 2) {
				$this->controller = $controller[0] . '/' . $controller[1];
				$this->action = $controller[2];
			} else {
				$this->controller = $this->route;
				$this->action = 'index';
			}
		}

		/*Allowed permission check for Admin user ids from config settings */
		if (in_array($this->user_id, $allowed_ids)) {
			// For Admin users(1,43), Allow List/Add/Edit/Delete User and thier permissions
			$allowed_controller = array('user/user', 'user/user_permission');
			$allowed_controller_methods = array('index', 'add', 'edit', 'delete');
			if (
				in_array($this->controller, $allowed_controller)
				&&
				in_array($this->action, $allowed_controller_methods)
			) {
				return true;
			}
		} // end for Admin user checkings

		/* Custom URL permission structure checking */
		if (isset($this->request->get['permission_id'])) {
			$query = $this->db->query("SELECT `permission` FROM " . DB_PREFIX . "menu_admin WHERE id = '" . (int) $this->request->get['permission_id'] . "'");
			$menu = $query->row;
			if (substr_count($menu['permission'], '/') > 1) {
				$permission = explode('/', $menu['permission']);
				$this->controller = $permission[0] . '/' . $permission[1];
				$this->action = isset($permission[2]) ? $permission[2] : 'index';
				if (!$check_method) {
					$value = $this->controller;
				}

			}
		} else {

			if (!$check_method) {
				$value = $this->controller;
			}

		}

		//Method level permission checking
		if ($this->is_ajax()) {
			/* Ajax call checking */

			$allowed_all_methods = isset($this->permission['access']['methods'])
			? $this->permission['access']['methods']
			: array();
			if (isset($this->permission[$key]) && isset($allowed_all_methods[$this->controller])) {
				$allowed_controller_methods = $allowed_all_methods[$this->controller];
				$allowed_controller_methods = array_map('strtolower', $this->permission['access']['methods'][$this->controller]);
				$allowed_controller_methods[] = 'is_ajax'; // explict added method to check for ajax request for all controller methods list
				$permission_status = (in_array($value, $this->permission[$key]) && in_array(strtolower($this->action), $allowed_controller_methods));
			} else {
				$permission_status = false;
			}

			if (!$permission_status) {
				$res_error['error'] = 'You do not have permission to access method (' . $this->action . '), please refer to your system administrator to allow required access permission for the user you logged-in.';
				echo json_encode($res_error);
				exit;
			} else {
				return $permission_status;
			}

		} else if ($check_method) {
			/* custome method checking for permission */

			if (isset($value) && $value != '') {
				$controller = explode('/', $value);
				if (count($controller) > 2) {
					$this->controller = $controller[0] . '/' . $controller[1];
					$this->action = $controller[2];
				} else {
					$this->controller = $controller[0] . '/' . $controller[1];
					$this->action = 'index';
				}
			}

			$allowed_all_methods = isset($this->permission['access']['methods'])
			? $this->permission['access']['methods']
			: array();

			if (isset($this->permission[$key]) && isset($allowed_all_methods[$this->controller])) {
				$allowed_controller_methods = $allowed_all_methods[$this->controller];
				$allowed_controller_methods = array_map('strtolower', $this->permission['access']['methods'][$this->controller]);
				$allowed_controller_methods[] = 'is_ajax';
				return (in_array($this->controller, $this->permission[$key]) && in_array(strtolower($this->action), $allowed_controller_methods));
			} else {
				return false;
			}

		} else {

			$allowed_all_methods = isset($this->permission['access']['methods'])
			? $this->permission['access']['methods']
			: array();

			if (isset($this->permission[$key]) && isset($allowed_all_methods[$this->controller])) {
				$allowed_controller_methods = $allowed_all_methods[$this->controller];
				$allowed_controller_methods = array_map('strtolower', $this->permission['access']['methods'][$this->controller]);
				$allowed_controller_methods[] = 'is_ajax'; // explict added method to check for ajax request for all controller methods list
				return (in_array($value, $this->permission[$key]) && in_array(strtolower($this->action), $allowed_controller_methods));
			} else {
				return false;
			}

		}

	}

	public function isLogged() {
		return $this->user_id;
	}

	/**
	 * For constructing user object, other than normal login.
	 * @note: setDb method should be called before this method.
	 * eg: unit testing
	 */
	public function setId($id) {
		$this->user_id = (int) $id;
		if (empty($this->db)) {
			throw new Exception('User::setDb($db) method must be invoked before User::setId($id)');
			exit();
		}
		$this->initializeUser();
	}

	/**
	 * For setting db instance
	 */
	public function setDb($db) {
		$this->db = $db;
	}

	public function getId() {
		return $this->user_id;
	}

	public function getFirstname() {
		return $this->firstname;
	}

	public function getLastname() {
		return $this->lastname;
	}

	public function getEmail() {
		return $this->email;
	}

	public function getAccessToken() {
		return $this->access_token;
	}

	public function getBranchCode() {
		return $this->branch_code;
	}

	public function getDeviceId() {
		return $this->device_id;
	}

	public function getCrmId($user_id = 0) {

		$user_id = $user_id ? $user_id : $this->user_id;

		$user_query = $this->db->query("SELECT crm_user_id FROM " . DB_PREFIX . "user
                                        WHERE user_id = '" . (int) $user_id . "'");
		return $user_query->row['crm_user_id'];
	}

	public function getUserName($user_id = 0) {

		$user_id = $user_id ? $user_id : $this->user_id;

		$user_query = $this->db->query("SELECT username, CONCAT(firstname, ' ', lastname) AS `name`, email
                                        FROM " . DB_PREFIX . "user
                                        WHERE user_id = '" . (int) $user_id . "'");
		return $user_query->row;
	}

	public function getGroupId() {
		return $this->user_group_id;
	}

	public function getGroupName() {
		$sql = 'SELECT name FROM `oc_user_group` where user_group_id = ' . $this->user_group_id;
		$query = $this->db->query($sql);

		return $query->row['name'];
	}
	public function is_ajax() {
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
	}

	/**
	 * @info: Public Method to set user profile password with auto generated password
	 * @return: array
	 * @author- MSA Feb 2019
	 */
	public function getRandomPasswordString() {
		$password = $this->generateStrongPassword(10);

		return array('password' => $password);
	}

	/* Generates a strong password of N length containing at least one lower case letter,
		    * one uppercase letter, one digit, and one special character. The remaining characters
		    * in the password are chosen at random from those four sets.
		    *
		    * The available characters in each set are user friendly - there are no ambiguous
		    * characters such as i, l, 1, o, 0, etc. This, coupled with the $add_dashes option,
		    * makes it much easier for users to manually type or speak their passwords.
		    *
		    * Note: the $add_dashes option will increase the length of the password by
		    * floor(sqrt(N)) characters.
	*/
	public function generateStrongPassword($length = 9, $add_dashes = false, $available_sets = 'luds') {
		$sets = array();
		if (strpos($available_sets, 'l') !== false) {
			$sets[] = 'abcdefghjkmnpqrstuvwxyz';
		}

		if (strpos($available_sets, 'u') !== false) {
			$sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
		}

		if (strpos($available_sets, 'd') !== false) {
			$sets[] = '23456789';
		}

		if (strpos($available_sets, 's') !== false) {
			$sets[] = '@#$';
		}

		$all = '';
		$password = '';
		foreach ($sets as $set) {
			$password .= $set[array_rand(str_split($set))];
			$all .= $set;
		}
		$all = str_split($all);
		for ($i = 0; $i < $length - count($sets); $i++) {
			$password .= $all[array_rand($all)];
		}

		$password = str_shuffle($password);
		if (!$add_dashes) {
			return $password;
		}

		$dash_len = floor(sqrt($length));
		$dash_str = '';
		while (strlen($password) > $dash_len) {
			$dash_str .= substr($password, 0, $dash_len) . '-';
			$password = substr($password, $dash_len);
		}
		$dash_str .= $password;
		return $dash_str;
	}

	public function getUserOldPasswordStatus() {

		$user_id = $this->user_id;
		$user_query = $this->db->query("SELECT old_password_active
                                        FROM " . DB_PREFIX . "user
                                        WHERE user_id = '" . (int) $user_id . "'");
		if ($user_query->num_rows) {
			return $user_query->row['old_password_active'];
		}
		return 0;
	}

	public function isUserHasDefaultLandingPage() {
		$landing_page = array();
		$user_query = $this->db->query("SELECT dont_show_dashboard,
                                               default_landing_page_url
                                        FROM " . DB_PREFIX . "user
                                        WHERE
                                                user_id = '" . (int) $this->user_id . "'"
		);
		if ($user_query->num_rows) {
			$landing_page = $user_query->row;
		}
		return $landing_page;
	}

}
