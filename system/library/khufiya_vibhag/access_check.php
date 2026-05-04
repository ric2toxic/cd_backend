<?php
declare(strict_types=1);

class KhufiyaAccessCheck {

	private $chaabee = '';
	private $remote_ip = '';
	private $session = null;

	private $ips_required_chaabee = array();
	private $ips_required_no_chabee = array();


    /**
     * KhufiyaAccessCheck constructor.
     * @param string $chaabee
     * @param string $remote_ip
     * @param Session $session
     */
    public function __construct(string  $chaabee,
                                string  $remote_ip,
                                Session $session) {
		$this->chaabee = $chaabee;
		$this->remote_ip = $remote_ip;
		$this->session = $session;

        //IP's - required valid chabee  
        $this->ips_required_chaabee = array_filter(ALLOWED_IP_ADDRESS);
        
        //IP's - required no chabee 
        $this->ips_required_no_chabee = array_diff_assoc(ALLOWED_IP_ADDRESS, 
                                                         $this->ips_required_chaabee
                                                        );
	}

	/**
	 * Method for chaabee based User access restriction
     * @return bool
	 * @author MSA, August 2018
	 */
	public function checkAccess(): bool {

	 	if( $this->isStaging() ){ 
	 		// for staging, chaabee  not required, direct access
	 		return true; 

	 	}elseif( $this->isChaabeeNotRequired() ) {
	 		// If user ip in white list with no chaabee required
			return true;

		}elseif( $this->isMasterChaabee() ) {
			// if user with master chaabee, no further checking required
	 		$this->session->data['chaabee'] = $this->chaabee;
	 		return true; 

	 	} elseif ( $this->isIpInRequiredListAndChaabeeMatched() ) {
	 		// If user ip found in white list and chaabee matched with ip in white list, no further checking required
			$this->session->data['chaabee'] = $this->chaabee;
			return true;

        } elseif ( $this->isChaabeeFoundInAllowedUsersList()
                   && empty($this->session->data['user_id']) ) {
            // If user ip found in white list and chaabee matched with ip in white list, no further checking required
            $this->session->data['chaabee'] = $this->chaabee;
            return true;

		} elseif( !empty($this->chaabee)
                  && !empty($this->session->data['user_id']) 
                 ) { 
            // check user level chaabee, no matter ip found in white list or not
			// first we will allow user to go for login, 
			// after login, we will matched user chaabee with user id 
			// if chaabee matched, allow to access khufiya vibhag other wise showing ERROR
            $user_id = (int)$this->session->data['user_id'];
            if ($this->checkAllowedUserChaabee($user_id)) {
                $this->session->data['chaabee'] = $this->chaabee;
                return true;    
            }
		}

        // We are here - it implies all authentication has failed
        return false;
	}

	/**
     * Private method to allow staging server without any ip checking
     * @return bool  true/flase
     * @author MSA, August 2018
     */
   	private function isStaging(): bool
    {
    	if( SITE_ENVIRONMENT === 'staging' ){
    		return true;
    	}
    	return false;
    }

    /**
     * Private method to allow ip addresses with master chaabee
     * @return bool  true/flase
     * @author MSA, August 2018
     */
	private function isMasterChaabee(): bool
    {
    	if( !empty( $this->chaabee ) 
    		&& 
    		$this->chaabee === MASTER_CHAABEE
    	) { 
    		return true;
    	}
    	return false;
    }

    /**
     * Private method to check ip address for chaabee not required
     * @return bool  true/flase
     * @author MSA, August 2018
     */
    private function isChaabeeNotRequired(): bool
    {
    	if( !empty($this->ips_required_no_chabee) 
			&& 
		   in_array( $this->remote_ip, array_keys( $this->ips_required_no_chabee ) ) 
		) {
			return true;
    	}
    	return false;
    }

    /**
     * Private method to check ip address for chaabee is required
     * @return bool  true/flase
     * @author MSA, August 2018
     */
    private function isIpInRequiredListAndChaabeeMatched(): bool
    {
    	if( !empty($this->ips_required_chaabee) 
			&&
			in_array( $this->remote_ip, array_keys( $this->ips_required_chaabee ) )
			&&
			$this->ips_required_chaabee[$this->remote_ip] === $this->chaabee
    	) {
    		return true;
    	} 

    	return false;
    } 

    /**
     * Private method to check chaabee in allowed users list
     * @return bool  true/flase
     * @author MSA, August 2018
     */
    private function isChaabeeFoundInAllowedUsersList(): bool
    {
        $allowed_user_chaabee = array_keys( array_flip ( USER_CHAABEE_ACCESS ) );

        if( !empty($this->chaabee)
            &&
            !empty($allowed_user_chaabee) 
            && 
            in_array( (string)$this->chaabee, $allowed_user_chaabee, true )
            ) { 
            // Found chaabee in allowed users list and now we allow user to 
            // go for login page, after login we will again match chaabee for logged-in user
            // If chaabee matched for loggedin user data, allow to access khufiya vibhag
            // otherwise show Error.
            return true;
        }
        return false;
    }


    /**
     * Private method to unset chaabee related session data
     * @return void
     * @author MSA, August 2018
     */
    private function clearChaabeeSessionData(): void
    {
		unset($this->session->data['chaabee']);
    }

    /**
     * Private method to show error message for unauthorized ip users
     * @return void
     * @author MSA, August 2018
     */
    public function showError(): void
    {
    	header('HTTP/1.1 403 Forbidden');

        $_html = '<p>';
        	$_html .= '<h1 style="text-align:center">';
        		$_html .= 'Shoo! Get lost !';	
        	$_html .= '</h1>';
        $_html .= '</p>';

        echo $_html; 

        exit;
    }

    /**
     * Private Method check valid chaabee for allowed users
     * @param int $user_id
     * @return bool true/false
     * @author MSA, August 2018
     */
    private function checkAllowedUserChaabee(int $user_id): bool
    { 
		if( !empty($this->chaabee) 
			&& 
            !empty(USER_CHAABEE_ACCESS[$user_id]) 
            && 
			USER_CHAABEE_ACCESS[$user_id] === $this->chaabee 
        ) {

    		return true;
    	}
    	
        $this->clearChaabeeSessionData();
        return false;
    }  

}  
