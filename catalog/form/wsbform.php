<?php 

class wsbform  {

	protected $data          = array();
	protected $config        = array();
	protected $label         = '';
	protected $label_field   = '';
	protected $label_class   = 'control-label';
	protected $field         = '';
	protected $fieldName     = '';
	protected $fieldValue    = '';
	protected $fieldAttribute  = '';

	/**
	 * Constructor.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct($config = NULL) {
		if ( ! empty($config)) {
			$this->initialize($config);
		}
	}

	// ------------------------------------------------------------------------
	/**
	 * Initialize library.
	 *
	 * @access public
	 * @param mixed $data
	 * @return void
	 */
	public function initialize($config) {
		$this->config = $config;
	}

	// ------------------------------------------------------------------------
	/**
	 * set label name.
	 *
	 * @access private
	 * @param mixed $data
	 * @return void
	 */
   private function _initInputLabel()
   {
   	    if($this->label <> '')
   	    {
   	     $this->label_field = '<label class="'.$this->label_class.'" for="input-'.$this->fieldName.'">'.$this->label.'</label>';
   	    }
   	    else
   	    {
   	    	$this->label_field = '';
   	    }
     	
     	return $this;
   }

 	// ------------------------------------------------------------------------
	/**
	 * set attribute.
	 *
	 * @access private
	 * @param mixed $data
	 * @return void
	 */
   private function _initInputAttribute($attribute=array())
   {
   	   $fieldOption_arr = array();
       foreach($attribute as $key => $val)
       {
         $fieldOption_arr[] = $key.'="'.$val.'"';
       }

       $this->fieldAttribute = implode(" ", $fieldOption_arr);
       return $this;
   }  

  	// ------------------------------------------------------------------------
	/**
	 * set options.
	 *
	 * @access private
	 * @param mixed $data
	 * @return void
	 */
   private function _initInputOptions($options=array(), $selectvalue)
   {
   	   $fieldOption_arr = array();
   	   $selected = '';

       foreach($options as $key => $val)
       {
       	if($key == $selectvalue) { $selected = 'selected'; } else { $selected = ''; }
         $fieldOption_arr[] = '<option value="'.$key.'" '.$selected.'>'.$val.'</options>';      	
       }

       $this->fieldOptions = implode(" ", $fieldOption_arr);
       return $this;
   }

  	// ------------------------------------------------------------------------
	/**
	 * init text value.
	 *
	 * @access private
	 * @param mixed $data
	 * @return void
	 */
   private function _initInputvalue($value)
   {
   	   if(isset($_POST[$this->fieldName]))
   	   {
   	   	$this->fieldValue = $_POST[$this->fieldName];
   	   }
   	   else if(isset($_GET[$this->fieldName]))
   	   {
   	   	$this->fieldValue = $_GET[$this->fieldName];
   	   }
   	   else
   	   {
   	   	$this->fieldValue = $value;
   	   }   	   
      
      return $this;
   }       
   

	// ------------------------------------------------------------------------
	/**
	 * create a form.
	 *
	 * @access public
	 * @param mixed $data
	 * @return void
	 */
   public function form_create($formname, $action, $attribute=array())
   {     
		$this->_initInputAttribute($attribute);
		if(!empty($action)) { $action =  'action="'.$action.'"'; }
        $this->field = '<form name="'.$formname.'" '.$action.' '.$this->fieldAttribute.'>';

        return $this->field;
   }

	// ------------------------------------------------------------------------
	/**
	 * create a textbox.
	 *
	 * @access public
	 * @param mixed $data
	 * @return void
	 */
   public function input($fieldName, $value, $attribute=array())
   {     
		$this->fieldName = $fieldName;

		if(isset($attribute['label'])) { $this->label = $attribute['label']; } else { $this->label = ''; }
		
        $this->_initInputLabel();
		$this->_initInputAttribute($attribute);
		$this->_initInputvalue($value);
      
        $this->field = '<input type="text" name="'.$this->fieldName.'" value="'.$this->fieldValue.'" '.$this->fieldAttribute.' />';

        $return = $this->label_field.$this->field;
        //$this->assertEmpty($return,"Failiure");
        return $return;
   }

	// ------------------------------------------------------------------------
	/**
	 * create a hidden textbox.
	 *
	 * @access public
	 * @param mixed $data
	 * @return void
	 */
   public function hidden($fieldName, $value, $attribute=array())
   {
        
		$this->fieldName = $fieldName;

		if(isset($attribute['label'])) { $this->label = $attribute['label']; } else { $this->label = ''; }
		
        $this->_initInputLabel();
		$this->_initInputAttribute($attribute);
		$this->_initInputvalue($value);


        $this->field = '<input type="hidden" name="'.$this->fieldName.'" value="'.$this->fieldValue.'" '.$this->fieldAttribute.' />';

        $return = $this->label_field.$this->field;
        return $return;

   }

	// ------------------------------------------------------------------------
	/**
	 * create a password textbox.
	 *
	 * @access public
	 * @param mixed $data
	 * @return void
	 */
   public function password($fieldName, $value, $attribute=array())
   {
        
		$this->fieldName = $fieldName;

		if(isset($attribute['label'])) { $this->label = $attribute['label']; } else { $this->label = ''; }
		
        $this->_initInputLabel();
		$this->_initInputAttribute($attribute);
		$this->_initInputvalue($value);

        $this->field = '<input type="password" name="'.$this->fieldName.'" value="'.$this->fieldValue.'" '.$this->fieldAttribute.' />';

        $return = $this->label_field.$this->field;
        return $return;

   }
	// ------------------------------------------------------------------------
	/**
	 * create a textarea.
	 *
	 * @access public
	 * @param mixed $data
	 * @return void
	 */
   public function textarea($fieldName, $value, $attribute=array())
   {
        
		$this->fieldName = $fieldName;

		if(isset($attribute['label'])) { $this->label = $attribute['label']; } else { $this->label = ''; }
		
        $this->_initInputLabel();
		$this->_initInputAttribute($attribute);
		$this->_initInputvalue($value);

        $this->field = '<textarea name="'.$this->fieldName.'" '.$this->fieldAttribute.'>'.$this->fieldValue.'</textarea>';

        $return = $this->label_field.$this->field;

        return $return;
   }
 

	// ------------------------------------------------------------------------
	/**
	 * create a file.
	 *
	 * @access public
	 * @param mixed $data
	 * @return void
	 */
   public function file($fieldName, $attribute=array())
   {
        
		$this->fieldName = $fieldName;

		if(isset($attribute['label'])) { $this->label = $attribute['label']; } else { $this->label = ''; }
		
        $this->_initInputLabel();
		$this->_initInputAttribute($attribute);

        $this->field = '<input type="file" name="'.$this->fieldName.'" '.$this->fieldAttribute.' />';
        $return = $this->label_field.$this->field;
        
        return $return;
   }

 	// ------------------------------------------------------------------------
	/**
	 * create a selectbox.
	 *
	 * @access public
	 * @param mixed $data
	 * @return void
	 */
   public function select($fieldName, $value, $options=array(), $attribute=array()) 
   {
        
		$this->fieldName = $fieldName;

		if(isset($attribute['label'])) { $this->label = $attribute['label']; } else { $this->label = ''; }
		
        $this->_initInputLabel();
		$this->_initInputAttribute($attribute);

        $this->_initInputOptions($options,$value);

        $this->field = '<select name="'.$this->fieldName.'" '.$this->fieldAttribute.'>'.$this->fieldOptions.'</select>';

        $return = $this->label_field.$this->field;
        return $return;

   }

  	// ------------------------------------------------------------------------
	/**
	 * create a radio input.
	 *
	 * @access public
	 * @param mixed $data
	 * @return void
	 */
   public function radio($fieldName, $value, $attribute=array(), $ltr='', $rtl='')
   {
        
		$this->fieldName = $fieldName;

		if(isset($attribute['label'])) { $this->label = $attribute['label']; } else { $this->label = ''; }
		
        $this->_initInputLabel();
		$this->_initInputAttribute($attribute);
        $this->_initInputvalue($value);

        $this->field = '<input type="radio" name="'.$this->fieldName.'" value="'.$this->fieldValue.'" '.$this->fieldAttribute.' />';

       if(!empty($ltr)) { $this->field = $ltr.' '.$this->field; } 
       if(!empty($rtl)) { $this->field = $this->field.' '.$rtl; } 
       
        $return = $this->label_field.$this->field;
        return $return;
   }
  	// ------------------------------------------------------------------------
	/**
	 * create a radio input.
	 *
	 * @access public
	 * @param mixed $data
	 * @return void
	 */
   public function checkbox($fieldName, $value, $attribute=array(), $ltr='', $rtl='')
   {
        
		$this->fieldName = $fieldName;

		if(isset($attribute['label'])) { $this->label = $attribute['label']; } else { $this->label = ''; }
		
        $this->_initInputLabel();
		$this->_initInputAttribute($attribute);
		$this->_initInputvalue($value);

        $this->field = '<input type="checkbox" name="'.$this->fieldName.'" value="'.$this->fieldValue.'" '.$this->fieldAttribute.' />';

       if(!empty($ltr)) { $this->field = $ltr.' '.$this->field; } 
       if(!empty($rtl)) { $this->field = $this->field.' '.$rtl; } 

        $return = $this->label_field.$this->field;
        return $return;
 
   } 

	// ------------------------------------------------------------------------
	/**
	 * create a submit button.
	 *
	 * @access public
	 * @param mixed $data
	 * @return void
	 */
   public function submit($fieldName, $value, $attribute=array())
   {
        
		$this->fieldName = $fieldName;

		$this->_initInputAttribute($attribute);

        $this->fieldValue = $value;

        $this->field = '<input type="submit" name="'.$this->fieldName.'" value="'.$this->fieldValue.'" '.$this->fieldAttribute.' />';

        $return = $this->field;
        return $return;

   }

 	// ------------------------------------------------------------------------
	/**
	 * create a submit button.
	 *
	 * @access public
	 * @param mixed $data
	 * @return void
	 */
   public function button($fieldName, $value, $attribute=array())
   {
		$this->fieldName = $fieldName;
		$this->_initInputAttribute($attribute);
        $this->fieldValue = $value;
        $this->field = '<button type="button" name="'.$this->fieldName.'" '.$this->fieldAttribute.'>'.$this->fieldValue.'</button>';
        $return = $this->field;
        return $return;
   }  
	// ------------------------------------------------------------------------
	/**
	 * end form.
	 *
	 * @access public
	 * @param mixed $data
	 * @return void
	 */
   public function form_end()
   {     
        $this->field = '</form>';
        $return = $this->field;
        //$this->assertEmpty($return,"Failiure");
        return $return;
   }


}