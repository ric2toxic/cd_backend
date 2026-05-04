$(function() {
    $("#ms-submit-button").click(function() {
        var button = $(this);
        
        var mobileno = phonenumber($("input[name=\'seller[reg_telephone]\'").val());	
        if (mobileno == 1 ){
            $.ajax({
                type: "POST",
                dataType: "json",
                url: 'index.php?route=account/register-seller/jxsavesellerinfo',
                data: $("form#seller-form").serialize(),
                beforeSend: function() {
                    //button.hide();
                    button.button('loading');
                    $('p.show_error').remove();
                    $('.warning.main').hide();
                    $('.form-group').removeClass("has-error");
                },
                /*complete: function(jqXHR, textStatus) {
                 if (textStatus != 'success') {
                 //button.show();
                 button.button('reset');
                 $(".warning.main").text(msGlobals.formError).show();
                 window.scrollTo(0,0);
                 }
                 },*/
        	    success: function(jsonData){
        	    	if (!jQuery.isEmptyObject(jsonData.errors)) { 
                		for (error in jsonData.errors) { 
                            if ($('#error_' + error).length > 0) { 
                                $('#error_' + error).text(jsonData.errors[error]);
                                $('#error_' + error).parents('.form-group').addClass('has-error');
                            } else if ($('[name="'+error+'"]').length > 0) { 
                                $('[name="' + error + '"]').parents('div:first').append('<p class="show_error text-danger">' + jsonData.errors[error] + '</p>');
                			    $('[name="' + error + '"]').parents('.form-group').addClass('has-error');
                            } else { 
                			  $(".warning.main").append("<p class='show_error'>" + jsonData.errors[error] + "</p>").show();
                		    }
                        }
                	} else{
                        if (!jQuery.isEmptyObject(jsonData.redirect)){
                            window.location = jsonData.redirect;
                        }
                    }
                },
                complete: function(jsonData) { 			
    			
    			
    				 if(jsonData.responseText.indexOf("redirect") == -1)
    				 {
    					button.button('reset');
    					$('#ms-submit-button').show().prev('span.wait').remove();
    						   //$('.error').text('');
     
                       window.scrollTo(0,0);
    				} 
                    // else {		
                    //     window.location = $('base').attr('href') + 'index.php?route=seller/account-profile';
                    // }
                }
            });
        } // close if of mobile no.
        else{
            return false;
        }
    });
});


function containsAny(str, substrings) {
    for (var i = 0; i != substrings.length; i++) {
        var substring = substrings[i];
        if (str.indexOf(substring) != - 1) {
            return substring;
        }
    }
    return null; 
}

function phonenumber(inputtxt,inputname){
    var flag = 1;
    var phoneno = /^\d{10}$/;
    var res = inputtxt.charAt(0);
    var result = containsAny(res, ["9", "8", "7","6"]);
    
    if( !result ){
        flag = 0;
    }

    if( flag == 1 ){
        if( inputtxt.match(phoneno) ) {
            return 1;
        } else {
            alert('Please Enter Valid Mobile Number!');
            return 2;
        }
    } else {
       alert('Please Enter Valid Mobile Number!');
       return 2;
    }
}
