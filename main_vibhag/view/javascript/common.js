function htmlEncode(value){
  return $('<div/>').text(value).html();
}

function htmlDecode(value){
  return $('<div/>').html(value).text();
}

function getURLVar(key) {
	var value = [];

	var query = String(document.location).split('?');

	if (query[1]) {
		var part = query[1].split('&');

		for (i = 0; i < part.length; i++) {
			var data = part[i].split('=');

			if (data[0] && data[1]) {
				value[data[0]] = data[1];
			}
		}

		if (value[key]) {
			return value[key];
		} else {
			return '';
		}
	}
}


/* This function validates for GSTIN */
    function gstin_validatation(gst_number) {
        
        var reggstin = /^([0-9]){2}([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}([a-zA-Z0-9]){1}([Z]){1}([a-zA-Z0-9]){1}?$/;
        if (reggstin.test(gst_number) == false) {
            alert("Invalid GST Number !!!");
            return false;
        }
        
        var factor_even = 1;
        var factor_odd = 2;
        var sum = 0;
        var gst_number_array = gst_number.split("");
        var checksum_weight_array = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split("");
        var checksum_mod = checksum_weight_array.length;
        var factor = factor_even;
        
        if(gst_number_array.length == 15) {
            gst_number_array.pop();
        }
        
        for(index = 0; index < gst_number_array.length; ++index) {
            var current_letter_weight = checksum_weight_array.indexOf(gst_number_array[index]);
            var current_checksum_digit = 0;
            if(current_letter_weight != -1) {
                current_checksum_digit = current_letter_weight * factor;
                current_checksum_digit = parseInt((current_checksum_digit / checksum_mod) + (current_checksum_digit % checksum_mod));
                sum += current_checksum_digit;
            }
            factor = (factor == factor_even) ? factor_odd : factor_even;
        }
        
        var calculated_checksum_weight = (checksum_mod - (sum % checksum_mod)) % checksum_mod;
        var calculated_checksum_letter = (checksum_weight_array[calculated_checksum_weight])
                                        ? checksum_weight_array[calculated_checksum_weight] 
                                        : false;
        var entered_checksum_character = gst_number.substr(-1);
        if(entered_checksum_character != calculated_checksum_letter) {
            var correct_gst_number = gst_number.slice(0, -1) + calculated_checksum_letter;
            alert("Invalid GST Number! Do you mean " + correct_gst_number + " instead? Please check and enter correct GST number again!");
            return false;
        }
        return true;
    }

$(window).load(function(){
	if($('#column-left').hasClass('active')){
		$('#column-left').removeClass('active').addClass('active');
	}
});

$(document).ready(function() {
	//Form Submit for IE Browser
	$('button[type=\'submit\']').on('click', function() {
		$("form[id*='form-']").submit();
	});

	// Highlight any found errors
	$('.text-danger').each(function() {
		var element = $(this).parent().parent();

		if (element.hasClass('form-group')) {
			element.addClass('has-error');
		}
	});
	//sessionStorage.removeItem('menu');
	//localStorage.removeItem('column-left');
	// Set last page opened on the menu
	$('#menu a[href]').on('click', function() {
		sessionStorage.setItem('menu', $(this).attr('href'));
	});

	if (!sessionStorage.getItem('menu')) {
		$('#menu #dashboard').addClass('active');
	} else {
		// Sets active and open to selected page in the left column menu.
    $('#menu a[href=\'' + sessionStorage.getItem('menu') + '\']').parents('li').addClass('active open');
	}

	if (localStorage.getItem('column-left') == 'active') {
		$('#button-menu i').replaceWith('<i class="fa fa-dedent fa-lg"></i>');

				$('#column-left').addClass('active');

				// Slide Down Menu
				$('#menu li.active').has('ul').children('ul').addClass('collapse in');
				$('#menu li').not('.active').has('ul').children('ul').addClass('collapse');

	} else {
		$('#button-menu i').replaceWith('<i class="fa fa-indent fa-lg"></i>');

		$('#menu li li.active').has('ul').children('ul').addClass('collapse in');
		$('#menu li li').not('.active').has('ul').children('ul').addClass('collapse');
	}

	// Menu button
	$('#button-menu').on('click', function() {
		// Checks if the left column is active or not.
		if ($('#column-left').hasClass('active')) {
			localStorage.setItem('column-left', '');

			$('#button-menu i').replaceWith('<i class="fa fa-indent fa-lg"></i>');

			$('#column-left').removeClass('active');

			$('#menu > li > ul').removeClass('in collapse');
			$('#menu > li > ul').removeAttr('style');
		} else {
			localStorage.setItem('column-left', 'active');

			$('#button-menu i').replaceWith('<i class="fa fa-dedent fa-lg"></i>');

			$('#column-left').addClass('active');

			// Add the slide down to open menu items
			$('#menu li.open').has('ul').children('ul').addClass('collapse in');
			$('#menu li').not('.open').has('ul').children('ul').addClass('collapse');
		}
	});

	// Menu
	$('#menu').find('li').has('ul').children('a').on('click', function() {
		if ($('#column-left').hasClass('active')) {
			$(this).parent('li').toggleClass('open').children('ul').collapse('toggle');
			$(this).parent('li').siblings().removeClass('open').children('ul.in').collapse('hide');
		} else if (!$(this).parent().parent().is('#menu')) {
			$(this).parent('li').toggleClass('open').children('ul').collapse('toggle');
			$(this).parent('li').siblings().removeClass('open').children('ul.in').collapse('hide');
		}
	});

	// Override summernotes image manager
	$('button[data-event=\'showImageDialog\']').attr('data-toggle', 'image').removeAttr('data-event');

	$(document).delegate('button[data-toggle=\'image\']', 'click', function() {
		$('#modal-image').remove();

		$(this).parents('.note-editor').find('.note-editable').focus();

		$.ajax({
			url: 'index.php?route=common/filemanager&token=' + getURLVar('token'),
			dataType: 'html',
			beforeSend: function() {
				$('#button-image i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
				$('#button-image').prop('disabled', true);
			},
			complete: function() {
				$('#button-image i').replaceWith('<i class="fa fa-upload"></i>');
				$('#button-image').prop('disabled', false);
			},
			success: function(html) {
				$('body').append('<div id="modal-image" class="modal">' + html + '</div>');

				$('#modal-image').modal('show');
			}
		});
	});

	// Image Manager
	$(document).delegate('a[data-toggle=\'image\']', 'click', function(e) {
		e.preventDefault();

		$('.popover').popover('hide', function() {
			$('.popover').remove();
		});

		var element = this;

		$(element).popover({
			html: true,
			placement: 'right',
			trigger: 'manual',
			content: function() {
				return '<button type="button" id="button-image" class="btn btn-primary"><i class="fa fa-pencil"></i></button> <button type="button" id="button-clear" class="btn btn-danger"><i class="fa fa-trash-o"></i></button>';
			}
		});

		$(element).popover('show');

		$('#button-image').on('click', function() {
			$('#modal-image').remove();

			$.ajax({
				url: 'index.php?route=common/filemanager&token=' + getURLVar('token') + '&target=' + $(element).parent().find('input').attr('id') + '&thumb=' + $(element).attr('id')+ '&data_directory=' + $(element).attr('data-directory'),
				dataType: 'html',
				beforeSend: function() {
					$('#button-image i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
					$('#button-image').prop('disabled', true);
				},
				complete: function() {
					$('#button-image i').replaceWith('<i class="fa fa-pencil"></i>');
					$('#button-image').prop('disabled', false);
				},
				success: function(html) {
					$('body').append('<div id="modal-image" class="modal">' + html + '</div>');

					$('#modal-image').modal('show');
				}
			});

			$(element).popover('hide', function() {
				$('.popover').remove();
			});
		});

		$('#button-clear').on('click', function() {
			$(element).find('img').attr('src', $(element).find('img').attr('data-placeholder'));

			$(element).parent().find('input').attr('value', '');

			$(element).popover('hide', function() {
				$('.popover').remove();
			});
		});
	});

	// tooltips on hover
	$('[data-toggle=\'tooltip\']').tooltip({container: 'body', html: true});

	// Makes tooltips work on ajax generated content
	$(document).ajaxStop(function() {
		$('[data-toggle=\'tooltip\']').tooltip({container: 'body'});
	});

	// https://github.com/opencart/opencart/issues/2595
	$.event.special.remove = {
		remove: function(o) {
			if (o.handler) {
				o.handler.apply(this, arguments);
			}
		}
	}

	$('[data-toggle=\'tooltip\']').on('remove', function() {
		$(this).tooltip('destroy');
	});
});

// Autocomplete */
(function($) {
	$.fn.autocomplete = function(option) {
		return this.each(function() {
			this.timer = null;
			this.items = new Array();

			$.extend(this, option);

			$(this).attr('autocomplete', 'off');

			// Focus
			$(this).on('focus', function() {
				this.request();
			});

			// Blur
			$(this).on('blur', function() {
				setTimeout(function(object) {
					object.hide();
				}, 200, this);
			});

			// Keydown
			$(this).on('keydown', function(event) {
				switch(event.keyCode) {
					case 27: // escape
						this.hide();
						break;
					default:
						this.request();
						break;
				}
			});

			// Click
			this.click = function(event) {
				event.preventDefault();

				value = $(event.target).parent().attr('data-value');

				if (value && this.items[value]) {
					this.select(this.items[value]);
				}
			}

			// Show
			this.show = function() {
				var pos = $(this).position();

				$(this).siblings('ul.dropdown-menu').css({
					top: pos.top + $(this).outerHeight(),
					left: pos.left
				});

				$(this).siblings('ul.dropdown-menu').show();
			}

			// Hide
			this.hide = function() {
				$(this).siblings('ul.dropdown-menu').hide();
			}

			// Request
			this.request = function() {
				clearTimeout(this.timer);

				this.timer = setTimeout(function(object) {
					object.source($(object).val(), $.proxy(object.response, object));
				}, 200, this);
			}

			// Response
			this.response = function(json) {
				html = '';

				if (json.length) {
					for (i = 0; i < json.length; i++) {
						this.items[json[i]['value']] = json[i];
					}

					for (i = 0; i < json.length; i++) {
						if (!json[i]['category']) {
							html += '<li data-value="' + json[i]['value'] + '"><a href="#">' + json[i]['label'] + '</a></li>';
						}
					}

					// Get all the ones with a categories
					var category = new Array();

					for (i = 0; i < json.length; i++) {
						if (json[i]['category']) {
							if (!category[json[i]['category']]) {
								category[json[i]['category']] = new Array();
								category[json[i]['category']]['name'] = json[i]['category'];
								category[json[i]['category']]['item'] = new Array();
							}

							category[json[i]['category']]['item'].push(json[i]);
						}
					}

					for (i in category) {
						html += '<li class="dropdown-header">' + category[i]['name'] + '</li>';

						for (j = 0; j < category[i]['item'].length; j++) {
							html += '<li data-value="' + category[i]['item'][j]['value'] + '"><a href="#">&nbsp;&nbsp;&nbsp;' + category[i]['item'][j]['label'] + '</a></li>';
						}
					}
				}

				if (html) {
					this.show();
				} else {
					this.hide();
				}

				$(this).siblings('ul.dropdown-menu').html(html);
			}

			$(this).after('<ul class="dropdown-menu"></ul>');
			$(this).siblings('ul.dropdown-menu').delegate('a', 'click', $.proxy(this.click, this));

		});
	}
})(window.jQuery);

//phone nos and email display only on click in admin panel - log with limit

function clickToSee(obj){
	// $(obj).css({'font-size':'12px'});
	var field_type = $(obj).data('field-type');
	var field_value = $(obj).data('field-value');
	$.ajax({
	    url: 'index.php?route=common/dashboard/fieldValueOnClickToSee&token=' + getURLVar('token'),
	    type: 'POST',
	    data: {"field_name":field_type, "new_value":field_value,},
	    success: function(response){
	    	$(obj).text(response.value);
	    	$(obj).attr('data-field-value',response.value);
	    	$(obj).removeClass('click_to_see');
	    	$(obj).removeClass('btn-primary');
	    	$(obj).prop("onclick", null);
	    }
	});
}


$.urlParam = function(name){
    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
    return results[1] || 0;
}



var pickupIssueInterval = '';

function setPickupInterval(key)
{
  pickupIssueInterval = setInterval(function(){ pickup_issue_popup(key) }, 60*2000);
}

function clearPickupInterval()
{
  clearInterval(pickupIssueInterval);
}

function pickup_issue_popup(key)
{
    var body_data = JSON.parse(localStorage.getItem('pickup_issue.'+key));
       $("#pickupIssueModal .pickup_name").html(body_data.pickup);
       $("#pickupIssueModal .pickup_number").html(body_data.pickup_number);
       $("#pickupIssueModal .pickup_address").html(body_data.pickup_address);
       $("#pickupIssueModal .pickup_city").html(body_data.pickup_city);
       $("#pickupIssueModal .pickup_pincode").html(body_data.pickup_pincode);
       $("#pickupIssueModal .seller_id").html(body_data.seller_id);
       $("#pickupIssueModal .seller").html(body_data.seller);
       $("#pickupIssueModal .company").html(body_data.company);
       $("#pickupIssueModal .order_no").html(body_data.order_no);
       $("#pickupIssueModal .order_product_id").html(body_data.order_product_id);
       $("#pickupIssueModal .product_name").html(body_data.product_name);
       $("#pickupIssueModal .product_model").html(body_data.product_model);
       $("#pickupIssueModal .total_pieces").html(body_data.total_pieces);
       $("#pickupIssueModal .total_price").html(body_data.total_price);
       $("#pickupIssueModal .total_set").html(body_data.total_set);
       $("#pickupIssueModal .product_comment").html(body_data.product_comment);
       $("#pickup_issue_slove").attr("data-order_product_id", body_data.order_product_id);
       $("#pickupIssueModal").modal("show");
}

function check_pickup_issue()
{
  $(".pickup_issue_notice").removeClass("blink");
  $(".pickup_li").remove();
  clearPickupInterval();

  var issue = 0; 
  var issue_count = parseInt($('.pickup_issue_notice').children("span").attr("data-alerts"));
  var str = '<li class="pickup_li dropdown-header">Pickup Issue</li>';
  for (var key in localStorage)
    {
       var pickup_key = key.split(".");	 
       if(pickup_key[0] == 'pickup_issue')
       {
         var pickup_data = JSON.parse(localStorage.getItem(key));
         if(pickup_data.status == 0)
           {
           	issue = pickup_key[1];
           	issue_count++;
            str = str+'<li class="pickup_li"><a href="javascript:;" onclick="pickup_issue_popup('+pickup_key[1]+')"><span class="label label-warning pull-right">'+pickup_data.order_no+'</span>Order No.</a></li>'
           } 
       } 
    }
   str = str+'<li class="pickup_li divider"></li>'; 
   $('.pickup_issue_notice').children("span").html(issue_count);
   if(issue != 0)
   {
     $(".pickup_issue_notice").next("ul").prepend(str);
     $(".pickup_issue_notice").addClass("blink");
     setPickupInterval(issue);
   }
}


function slove_pickup_issue()
{
  key = $("#pickup_issue_slove").attr('data-order_product_id');
  var pickup_data = JSON.parse(localStorage.getItem('pickup_issue.'+key));
  pickup_data.status = 1;
  localStorage.setItem("pickup_issue."+key,  JSON.stringify(pickup_data));
  $("#pickupIssueModal").modal("hide");
  check_pickup_issue(); 
}

//General method to validate email-id
function email_validation(email){
	var expr = new RegExp(/^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/);
    return expr.test(email);
}


/**
 * This method will get the order stats for given customer id, and will render the response on provided div id.
 * Currently used on customer list page, order list page and credit application list page.
 * @param customer id
 * @param div id
 * @author Devendra, Sep 2019
 *  */ 
var order_stats = {};
function getOrderStats(customer_id, div_id) {
    $("#"+div_id).html('<i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw" style="font-size:16px"></i>');
    if(order_stats[customer_id]) {
        $("#"+div_id).html(order_stats[customer_id]);
    } else {
        $.ajax({
            url: 'index.php?route=report/customer/getOrderStats&token='+getURLVar('token')+'&customer_id='+customer_id,
            type:'GET',
            success: function(response) {
                order_stats[customer_id] = response;
                $("#"+div_id).html(response);
            },
            error: function(xhr, ajaxOptions, thrownError) {
              alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
          });
    }
}

/**
 * This method will get the sms details for given customer id, and will render the response on provided div id.
 * Currently used on customer list page, order list page and credit application list page.
 * @param customer id
 * @param div id
 * @author Devendra, Sep 2019
 *  */ 
var sms_details = {};
function getSMSDetails(customer_id, div_id) {
    $("#"+div_id).html('<i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw" style="font-size:16px"></i>');
    if(sms_details[customer_id]) {
        $("#"+div_id).html(sms_details[customer_id]);
    } else {
        $.ajax({
            url: 'index.php?route=sale/customer/getSMSDetails&token='+getURLVar('token')+'&customer_id='+customer_id,
            type:'GET',
            success: function(response) {
                sms_details[customer_id] = response;
                $("#"+div_id).html(response);
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    }
}