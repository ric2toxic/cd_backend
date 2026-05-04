$(function() {
	$("#ms-submit-button").click(function(){
		$('.success').remove();
		var button = $(this);
		var id = $(this).attr('id');

        if (msGlobals.config_enable_rte == 1) {
            $('.ckeditor').each(function () {
                $(this).val($(this).code());
            });
        }
 
		$.ajax({
			type: "POST",
			dataType: "json",
			url: 'index.php?route=seller/account-profile/jxsavesellerinfo',
			data: $("form#ms-sellerinfo").serialize(),
			beforeSend: function() {
				//button.hide();				
                button.button('loading');
                $('p.show_error').remove();
                $('.warning.main').hide();
                $('.form-group').removeClass("has-error");
                
			},
			complete: function(jqXHR, textStatus) { 				
				
				if(jqXHR.responseText.indexOf("errors") == -1)
				 {
					$('a[href="#tab-bankdetails"]').trigger('click'); // navigate to third tab - Bank Details       
				} else {		
                    button.button('reset');
					$('#ms-submit-button').show().prev('span.wait').remove();   
                } 
                
                window.scrollTo(0,0);
                
			},
			success: function(jsonData, textStatus) {				

					button.button('reset').prev('span.wait').remove();	
					
					if (!jQuery.isEmptyObject(jsonData.errors)) { 
						for (error in jsonData.errors) { 
							if ($('[name="'+error+'"]').length > 0) { 
								$('[name="' + error + '"]').parents('div:first').append('<p class="show_error text-danger">' + jsonData.errors[error] + '</p>');
								$('[name="' + error + '"]').parents('.form-group').addClass('has-error');
							} 
						}
					}	
		
					//window.scrollTo(0,0);
				
				//window.location.reload();
	       	}
		});
	});

	$("#ms-bank-submit-button").click(function() {
		$('.success').remove();
		var button = $(this);
		var id = $(this).attr('id');

		// var ifsc_code = $('input[name=\'seller[ifsc_code]\']').val();
		// var filter = /^([A-Z]{4})*([0-9]{7})+$/;
		// if(filter.test(ifsc_code)){
		// 	console.log('dddd');

		// }else{
		// 	console.log('ffff');
		// }

		//return false;
		
		$.ajax({
			type: "POST",
			dataType: "json",
			url: 'index.php?route=seller/account-profile/jxsavesellerinfo',
			data: $("form#ms-bank-sellerinfo").serialize(),
			beforeSend: function() {
				button.button('loading');
                $('p.show_error').remove();
                $('.warning.main').hide();
                $('.form-group').removeClass("has-error");
			},
			complete: function(jqXHR, textStatus) {
								
				if(jqXHR.responseText.indexOf("errors") > 0)
				{
                    button.button('reset');
					$('#ms-submit-button').show().prev('span.wait').remove();
					$('.error-ms-bank').remove(); 
					window.scrollTo(0,0);
                }				
			},
			success: function(jsonData) {
				button.button('reset').prev('span.wait').remove();	
				console.log(jQuery.isEmptyObject(jsonData.errors)); 
				if (!jQuery.isEmptyObject(jsonData.errors)) { 
					for (error in jsonData.errors) { 
						if ($('[name="'+error+'"]').length > 0) { 
							$('[name="' + error + '"]').parents('div:first').append('<p class="show_error text-danger">' + jsonData.errors[error] + '</p>');
							$('[name="' + error + '"]').parents('.form-group').addClass('has-error');
						} 
					}
				}
				else
				{
					window.location = jsonData.redirect;
				}				
			}
		});
	});
	
	$("#sellerinfo_avatar_files, #sellerinfo_banner_files").delegate(".ms-remove", "click", function() {
		$(this).parent().remove();
	});	

	var uploader = new plupload.Uploader({
		runtimes : 'gears,html5,flash,silverlight',
		//runtimes : 'flash',
		multi_selection:false,
		browse_button: 'ms-file-selleravatar',
		url: 'index.php?route=seller/account-profile/jxUploadSellerAvatar',
		flash_swf_url: 'catalog/view/javascript/plupload/plupload.flash.swf',
		silverlight_xap_url : 'catalog/view/javascript/plupload/plupload.silverlight.xap',
		
	    multipart_params : {
			'timestamp' : msGlobals.timestamp,
			'token'     : msGlobals.token,
			'session_id': msGlobals.session_id
	    },
		
		filters : [
			//{title : "Image files", extensions : "png,jpg,jpeg"},
		],
		
		init : {
			FilesAdded: function(up, files) {
				$('#error_sellerinfo_avatar').html('');
				up.start();
			},
			
			FileUploaded: function(up, file, info) {
				try {
   					data = $.parseJSON(info.response);
				} catch(e) {
					data = []; data.errors = []; data.errors.push(msGlobals.uploadError);
				}

				if (!$.isEmptyObject(data.errors)) {
					var errorText = '';
					for (var i = 0; i < data.errors.length; i++) {
						errorText += data.errors[i] + '<br />';
					}
					$('#error_sellerinfo_avatar').append(errorText).hide().fadeIn(2000);
				}

				if (!$.isEmptyObject(data.files)) {
					for (var i = 0; i < data.files.length; i++) {
						$("#sellerinfo_avatar_files").html(
						'<div class="ms-image">' +
						'<input type="hidden" value="'+data.files[i].name+'" name="seller[avatar_name]" />' +
						'<img src="'+data.files[i].thumb+'" />' +
						'<span class="ms-remove"></span>' +
						'</div>').children(':last').hide().fadeIn(2000);
					}
				}
				
				up.stop();
			},
			
			Error: function(up, args) {
				$('#error_sellerinfo_avatar').append(msGlobals.uploadError).hide().fadeIn(2000);
				console.log('[error] ', args);
			}
		}
	}).init();

    var bannerUploader = new plupload.Uploader({
		runtimes : 'gears,html5,flash,silverlight',
		//runtimes : 'flash',
		multi_selection:false,
		browse_button: 'ms-file-sellerbanner',
		url: 'index.php?route=seller/account-profile/jxUploadSellerAvatar',
		flash_swf_url: 'catalog/view/javascript/plupload/plupload.flash.swf',
		silverlight_xap_url : 'catalog/view/javascript/plupload/plupload.silverlight.xap',

	    multipart_params : {
			'timestamp' : msGlobals.timestamp,
			'token'     : msGlobals.token,
			'session_id': msGlobals.session_id
	    },

		filters : [
			//{title : "Image files", extensions : "png,jpg,jpeg"},
		],

		init : {
			FilesAdded: function(up, files) {
				$('#error_sellerinfo_banner').html('');
				up.start();
			},

			FileUploaded: function(up, file, info) {
				try {
   					data = $.parseJSON(info.response);
				} catch(e) {
					data = []; data.errors = []; data.errors.push(msGlobals.uploadError);
				}

				if (!$.isEmptyObject(data.errors)) {
					var errorText = '';
					for (var i = 0; i < data.errors.length; i++) {
						errorText += data.errors[i] + '<br />';
					}
					$('#error_sellerinfo_banner').append(errorText).hide().fadeIn(2000);
				}

				if (!$.isEmptyObject(data.files)) {
					for (var i = 0; i < data.files.length; i++) {
						$("#sellerinfo_banner_files").html(
						'<div class="ms-image">' +
						'<input type="hidden" value="'+data.files[i].name+'" name="seller[banner_name]" />' +
						'<img src="'+data.files[i].thumb+'" />' +
						'<span class="ms-remove"></span>' +
						'</div>').children(':last').hide().fadeIn(2000);
					}
				}

				up.stop();
			},

			Error: function(up, args) {
				$('#error_sellerinfo_banner').append(msGlobals.uploadError).hide().fadeIn(2000);
				console.log('[error] ', args);
			}
		}
	}).init();

	if (msGlobals.config_enable_rte == 1) {
		$('.ckeditor').each(function () {
            $(this).summernote({
                height: 300
            });

            if(!$(this).val()) $(this).code('');
        });
	}

    $("select[name='seller[country]']").on('change', function() {
        $.ajax({
            url: 'index.php?route=account/account/country&country_id=' + this.value,
            dataType: 'json',
            beforeSend: function() {
               $("select[name='seller[country]']").after(' <i class="fa fa-circle-o-notch fa-spin"></i>');
            },
            complete: function() {
                $('.fa-spin').remove();
            },
            success: function(json) {
                html = '<option value="">' + msGlobals.zoneSelectError + '</option>';

                if (json['zone']) {
                    for (i = 0; i < json['zone'].length; i++) {
                        html += '<option value="' + json['zone'][i]['zone_id'] + '"';

                        if (json['zone'][i]['zone_id'] == msGlobals.zone_id) {
                            html += ' selected="selected"';
                        }

                    html += '>' + json['zone'][i]['name'] + '</option>';
                }
                } else {
                    html += '<option value="0" selected="selected">' + msGlobals.zoneNotSelectedError + '</option>';
                }

                $("select[name='seller[zone]']").html(html);
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    }).trigger('change');
});
