/*!

 =========================================================
 * Bootstrap Wizard - v1.1.1
 =========================================================
 
 * Product Page: https://www.creative-tim.com/product/bootstrap-wizard
 * Copyright 2017 Creative Tim (http://www.creative-tim.com)
 * Licensed under MIT (https://github.com/creativetimofficial/bootstrap-wizard/blob/master/LICENSE.md)
 
 =========================================================
 
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 */

// Get Shit Done Kit Bootstrap Wizard Functions

searchVisible = 0;
transparent = true;

$(document).ready(function(){
    
    //onload set active tab
    if(draft == ''){
        draft = 0
    }
    
    draft = parseInt(draft) + 1
    if(validation_error != ''){
        draft = parseInt(draft) - 1
    }
    $('html, body').animate({
        'scrollTop' : 0
    });
       
    $(".wizard-navigation ul li:nth-child("+draft+") a").tab('show');

    /*  Activate the tooltips      */
    $('[rel="tooltip"]').tooltip();

    // Code for the Validator
    //Application detail validation  
    
    //add custom function
    jQuery.validator.addMethod("numberNotStartWithZero", function(value, element) { 
                return this.optional(element) || /^[1-9][0-9]+$/i.test(value); 
    }, "Please enter a valid number. (Do not start with zero)");

    jQuery.validator.addMethod("phoneValidate", function(value, element) { 
        var isSuccess = 0;
        var credit_application_id = $("input[name=credit_application_id]").val();
         $.ajax({ url: "index.php?route=account/credit_application/phoneOrEmailValidate&type=phone&value="+value+"&credit_application_id="+credit_application_id, 
            data: {}, 
            async: false, 
            success: 
                function(msg) { isSuccess = msg; }
          });
         return this.optional(element) || isSuccess == 1;
   
    }, "Phone number already register");

    jQuery.validator.addMethod("emailValidate", function(value, element) { 
        var isSuccess = 0;
        var credit_application_id = $("input[name=credit_application_id]").val();
         $.ajax({ url: "index.php?route=account/credit_application/phoneOrEmailValidate&type=email&value="+value+"&credit_application_id="+credit_application_id, 
            data: {}, 
            async: false, 
            success: 
                function(msg) { isSuccess = msg; }
          });
         return this.optional(element) || isSuccess == 1;
   
    }, "Email address already register");
    
    
    jQuery.validator.addMethod(
     "date",
     function( value, element ) {
            return this.optional(element) || /^(?:(?:31(\/|-|\.)(?:0?[13578]|1[02]))\1|(?:(?:29|30)(\/|-|\.)(?:0?[1,3-9]|1[0-2])\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})$|^(?:29(\/|-|\.)0?2\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:0?[1-9]|1\d|2[0-8])(\/|-|\.)(?:(?:0?[1-9])|(?:1[0-2]))\4(?:(?:1[6-9]|[2-9]\d)?\d{2})$/.test(value);
        },
     "Please enter a correct date"
    );
    
    jQuery.validator.addMethod(
     "panNo",
     function( value, element ) {
            return this.optional(element) || /[a-zA-z]{5}\d{4}[a-zA-Z]{1}/.test(value);
        },
     "Please enter a correct Pan No"
    );
 
     jQuery.validator.addMethod(
     "gstNumber",
     function( value, element ) {
            return this.optional(element) || /^([0-9]{2}[a-zA-Z]{4}([a-zA-Z]{1}|[0-9]{1})[0-9]{4}[a-zA-Z]{1}([a-zA-Z]|[0-9]){3}){0,15}$/.test(value);
        },
     "Please enter a correct Gst Number"
    );   


    jQuery.validator.addMethod("notEqual", function(value, element, param) {
      return this.optional(element) || value != param;
    });
    
    jQuery.validator.addMethod("filesize", function(value, element, param) {

      var files = element.files;
      if(files && files.length > 0){
      for (var i = 0; i < files.length; i++) {
          if(files[i].size > 2000000){
            return false;
          }
      }
        return true;
      }else{
         return true; 
      }
      return false;
    },
    'File Size cannot be more than 2 MB.');

    var $validator=$("#short_credit_application_form2").validate( {
        rules: {
            
            aadhaar_no: {
                required: {
                    depends: function() {
                        return $("#short_credit_application_form").hasClass('nokhufiya');
                    }
                  }, 
                normalizer: function( value) {
                    return $.trim( value);
                }
                , minlength: 12,
                  maxlength: 12,
                  number: true,
            },
            current_address: {
                required: {
                    depends: function() {
                        return $("#short_credit_application_form").hasClass('nokhufiya');
                    }
                  }, 
            },
            permanent_pincode: {
                required: {
                    depends: function() {
                        return $("#short_credit_application_form").hasClass('nokhufiya');
                    }
                  }, 
            },
            email: {
                required: {
                    depends: function() {
                        return $("#short_credit_application_form").hasClass('nokhufiya');
                    }
                  }, minlength: 3, emailValidate:true,
            },
            customer_email: {
                required: {
                    depends: function() {
                        return $("#short_credit_application_form").hasClass('nokhufiya');
                    }
                  }, minlength: 3, emailValidate:true,
            },
            company_name: {
                required: {
                    depends: function() {
                        return $("#short_credit_application_form").hasClass('nokhufiya');
                    }
                  }, minlength: 3
            },
            current_address: {
                required: {
                    depends: function() {
                        return $("#short_credit_application_form").hasClass('nokhufiya');
                    }
                  },
                normalizer: function( value ) {
                return $.trim( value );
                }
            },business_start_year: {
                required: {
                    depends: function() {
                        return $("#short_credit_application_form").hasClass('nokhufiya');
                    }
                  }, 
            }
           
           
        }
        
    }
    
    );

        var $validator=$("#short_credit_application_form").validate( {
        rules: {
            first_name: {
                required: {
                    depends: function() {
                        return $("#short_credit_application_form").hasClass('nokhufiya');
                    }
                  }, 

                normalizer: function( value) {
                    return $.trim( value);
                }
                , minlength: 3,
            }
            , last_name: {
                required: {
                    depends: function() {
                        return $("#short_credit_application_form").hasClass('nokhufiya');
                    }
                  }, normalizer: function( value) {
                    return $.trim( value);
                }
                , minlength: 3,
            }, father_name: {
                required: {
                    depends: function() {
                        return $("#short_credit_application_form").hasClass('nokhufiya');
                    }
                  }, normalizer: function( value) {
                    return $.trim( value);
                }
                , minlength: 3,
            }
            , phone_no: {
                required: {
                    depends: function() {
                        return $("#short_credit_application_form").hasClass('nokhufiya');
                    }
                  }, number: true, minlength: 10, numberNotStartWithZero: true, phoneValidate: true,
            }
            , email: {
                required: {
                    depends: function() {
                        return $("#short_credit_application_form").hasClass('nokhufiya');
                    }
                  }, minlength: 3, emailValidate:true,
            }
            , current_pincode: {
                required: {
                    depends: function() {
                        return $("#short_credit_application_form").hasClass('nokhufiya');
                    }
                  }, minlength: 6, maxlength: 6
            }
            , gst_number: {
                gstNumber: {
                    depends: function() {
                        return $("#short_credit_application_form").hasClass('nokhufiya');
                    }
                  },
            }
            , dob: {
                required: {
                    depends: function() {
                        return $("#short_credit_application_form").hasClass('nokhufiya');
                    }
                  },
            }
            ,company_name: {
                required: {
                    depends: function() {
                        return $("#short_credit_application_form").hasClass('nokhufiya');
                    }
                  }, minlength: 3
            }
            ,months_in_current_location: {
                number: {
                    depends: function() {
                        return $("#short_credit_application_form").hasClass('nokhufiya');
                    }
                  },
            },
            current_pincode: {
              required: {
                    depends: function() {
                        return $("#short_credit_application_form").hasClass('nokhufiya');
                    }
                  },
                          number: true,
                          minlength: 6,
                          maxlength: 6
            },
            permanent_pincode: {
              required: {
                    depends: function() {
                        return $("#short_credit_application_form").hasClass('nokhufiya');
                    }
                  },
                          number: true,
                          minlength: 6,
                          maxlength: 6
            },
            pan_no: {
                required: {
                    depends: function() {
                        return $("#short_credit_application_form").hasClass('nokhufiya');
                    }
                  }, 
                normalizer: function( value) {
                    return $.trim( value);
                }
                , minlength: 3,
                panNo: true
            },
            gst_number: {
                  required: {
                    depends: function() {
                      if($('input[name=gst_number_yes]:checked').val() == '1' && $("#short_credit_application_form").hasClass('nokhufiya')){
                        return true;
                      }else{
                        return false;
                      }
                    }
                  },
                gstNumber: true,
                },
            permanent_address: {
                required: {
                    depends: function() {
                        return $("#short_credit_application_form").hasClass('nokhufiya');
                    }
                  }, 
            },
        }
    }
    
    );

    var $validator = $(".wizard-card form[name='application_form']").validate({
		  rules: {
                      
                    business_entity_type: { 
		      required: true,
		    }, 
                    
		    first_name: {
		      required: true,
                      normalizer: function( value ) {
                        return $.trim( value );
                      },
		      minlength: 3,
		    },
                    last_name: {
		      required: true,
                      normalizer: function( value ) {
                        return $.trim( value );
                      },
		      minlength: 3,
		    },
                    
		    father_name: {
		      required: true,
                      normalizer: function( value ) {
                        return $.trim( value );
                      },
		      minlength: 3
		    },
        mother_name: {
          required: true,
                      normalizer: function( value ) {
                        return $.trim( value );
                      },
          minlength: 3
        },
         marital_status: {
          required: true
        },
                    pan_no: {
		      required: true,
                      normalizer: function( value ) {
                        return $.trim( value );
                      },
		      panNo: true
		    },
                    
                    aadhaar_no: {
		      required: true,
                      number: true,
		      minlength: 12, 
		    },
                    dob: {
		      required: true,
                      date: true
		    },
                    gender: {
		      required: true
		    },
                    education: {
		      required: true
		    },
        phone_no: {
		      required: true,
                      number: true,
                      minlength: 10,
                      numberNotStartWithZero: true,
                      phoneValidate:true,
		    },
                    email: {
		      required: true,
		      minlength: 3,
          emailValidate: true,
		    },
                    current_address: {
		      required: true,
                      normalizer: function( value ) {
                        return $.trim( value );
                      }
		    },
                    current_pincode: {
		      required: true,
                      number: true,
                      minlength: 6,
                      maxlength: 6
		    },
                    current_city: {
		      required: true,
                      minlength: 3,
                    
		    },
                    current_state: {
		      required: true,
                      minlength: 3,
		    },
                    current_landline_phone_no: {
                      required: true,
                      number:true,
                    }, 
                    current_resident_premises: {
		      required: true,
                    },
                    residing_date_month: {
		      required: true,
                    },
                    residing_date_year: {
          required: true,
                    },
                    permanent_address: {
		      required: true,
                      normalizer: function( value ) {
                        return $.trim( value );
                      }
                    },
                    permanent_pincode: {
		      required: true,
                      number: true,
                      minlength: 6,
                      maxlength: 6
		    },
                    permanent_city: {
		      required: true,
                      minlength: 3,
		    },
                    permanent_state: {
		      required: true,
                      minlength: 3,
		    },
                    permanent_landline_phone_no: {
                      required: true,
                      number:true,
                    },
                    permanent_resident_premises: {
		      required: true,
                    },
                    permanent_date_month: {
                      required: true,
                    },
                    permanent_date_year: {
                      required: true,
                    },
                    
            }
	});

        
    //Business detail validation
    var $validator = $(".wizard-card form[name='business_form']").validate({
            rules: {
               
                company_name: {
                  required: true,
                  normalizer: function( value ) {
                    return $.trim( value );
                  },
                  minlength: 3
                }, 
                // entity_name: {
                //   required: true,
                //   normalizer: function( value ) {
                //     return $.trim( value );
                //   },
                //   minlength: 3
                // }, 
                partners: { 
                  required: true,
                  number:true,
                  notEqual: "0"
                },
                shop_establishment_number: {
                  required: {
                    depends: function() {
                        return $('input[name=business_pan_no]').val() == '';
                    }
                  },
                  normalizer: function( value ) {
                        return $.trim( value );
                    },
                  minlength: 3
                }, 
                business_pan_no: { 
                  required: {
                    depends: function() {
                        return $('input[name=shop_establishment_number]').val() == '';
                    }
                  },
                  panNo: true
                },
                company_identification_number: {
                  // required: true,
                  normalizer: function( value ) {
                        return $.trim( value );
                    },
                  // minlength: 3
                },
                trading_name: {
                  required: true,
                  normalizer: function( value ) {
                        return $.trim( value );
                    },
                  minlength: 3
                },
                nature_of_business: {
                  required: true
                },
                business_segment: {
                  required: true
                },
                business_ownership:{
                  required: true
                },
                business_vintage: {
                  required: true
                },
                months_in_current_business: {
                  required: true,
                  normalizer: function( value ) {
                    return $.trim( value );
                  },
                },
                business_premises: {
                  required: true
                },
                occupied_since_month: {
                  required: true,
                },
                occupied_since_year: {
                  required: true,
                },
                business_address: {
                  required: true,
                  normalizer: function( value ) {
                    return $.trim( value );
                  }
                },
                business_pincode: {
                    required: true,
                    number: true,
                    minlength: 6,
                    maxlength: 6
                },
                business_city: {
                  required: true,
                  minlength: 3,
                },
                business_state: {
                  required: true,
                  minlength: 3,
                },
                reg_office_address: {
                  required: true,
                  normalizer: function( value ) {
                    return $.trim( value );
                  }
                },
                reg_office_pincode: {
                  required: true,
                  number: true,
                  minlength: 6,
                  maxlength: 6
                },
                reg_office_city: {
                  required: true,
                  minlength: 3,
                },
                reg_office_state: {
                  required: true,
                  minlength: 3,
                },
                other_business_entity_detail: {
                  required: {
                    depends: function() {
                        return $('input[name=has_litigation]:checked').val() == 'yes';
                    }
                  },
                  normalizer: function( value ) {
                    return $.trim( value );
                  },
                  minlength: 10

                },
                business_since_month: {
                  required: true,
                },
                business_since_year: {
                  required: true,
                },
                annual_turnover: {
                  required: true
                },
                litigation: {
                  required: {
                    depends: function() {
                        return $('input[name=has_other_entity]:checked').val() == 'yes';
                    }
                  },
                  normalizer: function( value ) {
                    return $.trim( value );
                  },
                  minlength: 10,
                },
                contact_person_first_name: {
                  required: true,
                  normalizer: function( value ) {
                    return $.trim( value );
                  },
                   minlength: 3
                },
                contact_person_last_name: {
                  required: true,
                  normalizer: function( value ) {
                    return $.trim( value );
                  },
                   minlength: 3
                },
                contact_person_designation: {
                  required: true,
                  normalizer: function( value ) {
                    return $.trim( value );
                  },
                   minlength: 3
                },
                contact_person_relation_with_borrower: {
                  required: true,
                  normalizer: function( value ) {
                    return $.trim( value );
                  },
                   minlength: 3
                },
                contact_person_email: {
                  required: true,
                  minlength: 3
                },
                contact_person_phone_no: {
                  required: true,
                  minlength: 10,
                  number:true,
                  numberNotStartWithZero: true
                },
                "declaration": { 
                        required: true, 
                        minlength: 1 
                } 
            },
            messages: {
                shop_establishment_number: {
                    required: "Either Shop Establishment Number or Business PAN is mandatory",
                },
                business_pan_no: {
                    required: "Either Shop Establishment Number or Business PAN is mandatory",
                }
                
            }
    });

    //Applicate document validation
    var $validator = $(".wizard-card form[name='application_document']").validate({
            rules: {
                "pancard[]": {
                  required: true,
                  extension: "jpg|jpeg|png|pdf",
                  required: {
                    depends: function() {
                        return $('input[name=required_pancard]').val() == '1';
                    }
                  },
                  filesize : true
                },
                "aadhaar_card[]": {
                  required: true,
                  extension: "jpg|jpeg|png|pdf",
                  required: {
                    depends: function() {
                        return $('input[name=required_aadhaar_card]').val() == '1';
                    }
                  },
                  filesize : true
                },
                "photo[]": {
                  required: true,
                  extension: "jpg|jpeg|png|pdf",
                  required: {
                    depends: function() {
                        return $('input[name=required_photo]').val() == '1';
                    }
                  },
                  filesize : true
                },
                "voter_id[]": {
                  required: {
                    depends: function() {
                        if($('#voter_id_number').val() == ''){
                          return false;
                        } else if ($('#voter_id_number').val() != '' && $('.voter_id_images > .document_image').children('.doc_image').length > 0){
                          return false;
                        } else {
                          return true;
                        }
                    }
                  },
                  filesize : true
                },
                "voter_id[number]": {
                  required: {
                    depends: function() {
                        if($('#voter_id').val() == '' && $('.voter_id_images > .document_image').children('.doc_image').length == 0){
                          return false;
                        } else{
                          return true;
                        }
                    }
                  }
                },
                "driving_license[]": {
                  required: {
                    depends: function() {
                        if($('#dl_number').val() == '' && $('#dl_expiry_date').val() == ''){
                          return false;
                        } else if (($('#dl_number').val() != '' || $('#dl_expiry_date').val() != '') && $('.driving_license_images > .document_image').children('.doc_image').length > 0){
                          return false;
                        } else{
                          return true;
                        }
                    }
                  },
                  filesize : true
                },
                "driving_license[number]": {
                  required: {
                    depends: function() {
                        if($('#driving_license').val() == '' && $('#dl_expiry_date').val() == ''){
                          return false;
                        } else{
                          return true;
                        }
                    }
                  }
                },
                "driving_license[expiry_date]": {
                  required: {
                    depends: function() {
                        if($('#dl_number').val() == '' && $('#driving_license').val() == ''){
                          return false;
                        } else{
                          return true;
                        }
                    }
                  }
                },
                "passport[]": {
                  required: {
                    depends: function() {
                        if($('#passport_number').val() == '' && $('#passport_expiry_date').val() == ''){
                          return false;
                        } else if (($('#passport_number').val() != '' || $('#passport_expiry_date').val() != '') && $('.passport_images > .document_image').children('.doc_image').length > 0){
                          return false;
                        } else{
                          return true;
                        }
                    }
                  },
                  filesize : true
                },
                "passport[number]": {
                  required: {
                    depends: function() {
                        if($('#passport_image').val() == '' && $('#passport_expiry_date').val() == ''){
                          return false;
                        } else{
                          return true;
                        }
                    }
                  }
                },
                "passport[expiry_date]": {
                  required: {
                    depends: function() {
                        if($('#passport_number').val() == '' && $('#passport_image').val() == ''){
                          return false;
                        } else{
                          return true;
                        }
                    }
                  }
                } 
            }
    });

    //Bussines document validation
    var $validator = $(".wizard-card form[name='bussines_document']").validate({
            rules: {
                
                "business_proof[]": {
                  required: {
                    depends: function() {
                        return $('input[name=required_mandatory_document]').val() == '1';
                    }
                  },
                  filesize : true
                }, 
                "electricity_bill[]": {
                  required: true,
                  extension: "jpg|jpeg|png|pdf",
                  required: {
                    depends: function() {
                        return $('input[name=required_mandatory_document]').val() == '1';
                    }
                  },
                  filesize : true
                },
                "phone_landline_bill[]": {
                  required: true,
                  extension: "jpg|jpeg|png|pdf",
                  required: {
                    depends: function() {
                        return $('input[name=required_mandatory_document]').val() == '1';
                    }
                  },
                  filesize : true
                },
                "registered_leave_license_agreement[]": {
                    required: true,
                    extension: "jpg|jpeg|png|pdf",
                  required: {
                    depends: function() {
                        return $('input[name=required_mandatory_document]').val() == '1';
                    }
                  },
                  filesize : true
                },
                "maintenance_receipt[]": {
                    required: true,
                    extension: "jpg|jpeg|png|pdf",
                    required: {
                        depends: function() {
                            return $('input[name=required_mandatory_document]').val() == '1';
                        }
                    },
                  filesize : true
                },
                "rental_agreement[]": {
                    required: true,
                    extension: "jpg|jpeg|png|pdf",
                    required: {
                        depends: function() {
                            return $('input[name=required_mandatory_document]').val() == '1';
                        }
                    },
                  filesize : true
                },
                "optional_document[]": {
                  required: {
                    depends: function() {
                        return $('input[name=required_optional_document]').val() == '1';
                    }
                  },
                  filesize : true
                }, 
                "six_months_bank_statement[]": {
                  required: true,
                  extension: "jpg|jpeg|png|pdf",
                  required: {
                        depends: function() {
                            return $('input[name=required_optional_document]').val() == '1';
                        }
                    },
                  filesize : true
                },
                "last_quarter_vat_transactions[]": {
                  required: true,
                  extension: "jpg|jpeg|png|pdf",
                  required: {
                        depends: function() {
                            return $('input[name=required_optional_document]').val() == '1';
                        }
                    },
                  filesize : true
                },
                "income_tax_returns[]": {
                  required: true,
                  extension: "jpg|jpeg|png|pdf",
                  required: {
                        depends: function() {
                            return $('input[name=required_optional_document]').val() == '1';
                        }
                    }
                },
                  filesize : true
            },
            messages: {
                "business_proof[]": "", 
                "electricity_bill[]": "",
                "phone_landline_bill[]": "",
                "registered_leave_license_agreement[]": "",
                "maintenance_receipt[]": "",
                "rental_agreement[]": "",
                "optional_document[]": "", 
                "six_months_bank_statement[]": "",
                "last_quarter_vat_transactions[]": "",
                "income_tax_returns[]": "",
                
            }
    });
       
    //submit form onclick finish(last tab)
    $( "#submit_form" ).click(function() {
      $("#submit_form").attr("disabled", "disabled");
        var $valid = $(".wizard-card form[name='bussines_document']").valid();
        if($valid == false) { 
            $validator.focusInvalid();
            $("#submit_form").removeAttr('disabled');
            return false;
        }else{
            $("form[name='bussines_document']").submit();
            $("#submit_form").removeAttr('disabled');
        }
    });
    
    // Wizard Initialization
  	$('.wizard-card').bootstrapWizard({
        'tabClass': 'nav nav-pills',
        'nextSelector': '.btn-next',
        'previousSelector': '.btn-previous',

        onNext: function(tab, navigation, index) {
        	var $valid = $('.wizard-card form#credit_application_form'+index).valid();
                
                if($valid == false) {
                    $validator.focusInvalid();
                    
                    var errorDiv = $('.error:visible').first();
                    var scrollPos = errorDiv.offset().top;
                    scrollPos = parseInt(scrollPos - 50);
                    $(window).scrollTop(scrollPos);
                    
                    return false;
                }else{
                    
                    //submit form 
                    $("form#credit_application_form"+index).submit();
                    // $('html, body').animate({
                    //   'scrollTop' : $(".wizard-navigation").position().top
                    // });
                }
        },

        onInit : function(tab, navigation, index){
            
          //check number of tabs and fill the entire row
          var $total = navigation.find('li').length;
          $width = 100/$total;
          var $wizard = navigation.closest('.wizard-card');

          $display_width = $(document).width();

          if($display_width < 600 && $total > 3){
              $width = 50;
          }
          
           navigation.find('li').css('width',$width-1 + '%');
           $first_li = navigation.find('li:first-child a').html();
           $moving_div = $('<div class="moving-tab">' + $first_li + '</div>');
           //$('.wizard-card .wizard-navigation').append($moving_div);
           refreshAnimation($wizard, index);
           $('.moving-tab').css('transition','transform 0s');
       },

        onTabClick : function(tab, navigation, index){
            
            var current_form_index = parseInt(index+1);
            //var $valid = true; 
            var $valid = $('.wizard-card #credit_application_form'+current_form_index).valid();
            
            if(!$valid){
                
                var errorDiv = $('.error:visible').first();
                var scrollPos = errorDiv.offset().top;
                scrollPos = parseInt(scrollPos - 50);
                $(window).scrollTop(scrollPos);
                
                return false;
            } else {
              // $('html, body').animate({
              //         'scrollTop' : $(".wizard-navigation").position().top
              //       });
                return true;
            }
        },

        onTabShow: function(tab, navigation, index) {
            var $total = navigation.find('li').length;
            var $current = index+1;

            var $wizard = navigation.closest('.wizard-card');

            // If it's the last tab then hide the last button and show the finish instead
            if($current >= $total) {
                $($wizard).find('.btn-next').hide();
                $($wizard).find('.btn-finish').show();
            } else {
                $($wizard).find('.btn-next').show();
                $($wizard).find('.btn-finish').hide();
            }

            button_text = navigation.find('li:nth-child(' + $current + ') a').html();

            setTimeout(function(){
                $('.moving-tab').text(button_text);
                }, 150);

            var checkbox = $('.footer-checkbox');

            if( !index == 0 ){
                $(checkbox).css({
                    'opacity':'0',
                    'visibility':'hidden',
                    'position':'absolute'
                });
            } else {
                $(checkbox).css({
                    'opacity':'1',
                    'visibility':'visible'
                });
            }

            refreshAnimation($wizard, index);
        }
  	});


    // Prepare the preview for profile picture
    $("#wizard-picture").change(function(){
        readURL(this);
    });

    $('[data-toggle="wizard-radio"]').click(function(){
        wizard = $(this).closest('.wizard-card');
        wizard.find('[data-toggle="wizard-radio"]').removeClass('active');
        $(this).addClass('active');
        $(wizard).find('[type="radio"]').removeAttr('checked');
        $(this).find('[type="radio"]').attr('checked','true');
    });

    $('[data-toggle="wizard-checkbox"]').click(function(){
        if( $(this).hasClass('active')){
            $(this).removeClass('active');
            $(this).find('[type="checkbox"]').removeAttr('checked');
        } else {
            $(this).addClass('active');
            $(this).find('[type="checkbox"]').attr('checked','true');
        }
    });

    $('.set-full-height').css('height', 'auto');

});



 //Function to show image before upload

function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('#wizardPicturePreview').attr('src', e.target.result).fadeIn('slow');
        }
        reader.readAsDataURL(input.files[0]);
    }
}

$(window).resize(function(){
    $('.wizard-card').each(function(){
        $wizard = $(this);
        index = $wizard.bootstrapWizard('currentIndex');
        refreshAnimation($wizard, index);

        $('.moving-tab').css({
            'transition': 'transform 0s'
        });
    });
});

function refreshAnimation($wizard, index){
    total_steps = $wizard.find('li').length;
    move_distance = $wizard.width() / total_steps;
    step_width = move_distance;
    move_distance *= index;

    $wizard.find('.moving-tab').css('width', step_width);
    $('.moving-tab').css({
        'transform':'translate3d(' + move_distance + 'px, 0, 0)',
        'transition': 'all 0.3s ease-out'

    });
}

function debounce(func, wait, immediate) {
	var timeout;
	return function() {
		var context = this, args = arguments;
		clearTimeout(timeout);
		timeout = setTimeout(function() {
			timeout = null;
			if (!immediate) func.apply(context, args);
		}, wait);
		if (immediate && !timeout) func.apply(context, args);
	};
};
