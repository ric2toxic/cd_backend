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

$(document).ready(function() {

	// for show exclusive collection using link click.
	$(".exclusive").click(function(){
		$(".text_exclusive").toggle();
	});
	$(".exclusive_voucher_code").keyup(function() {
		$(".invalid_exclusive_voucher_code").hide();
	});
	$("#check_exclusive_voucher_code").click(function(){
		var exclusive_voucher_code = $(".exclusive_voucher_code").val();
		if(exclusive_voucher_code != ''){
			$.ajax({
				type: "post",
				url: "index.php?route=common/footer/checkExclusiveVoucherCode",
				data: "exclusive_voucher_code="+exclusive_voucher_code,
				success: function(data){
					if(data['success'] == 0) {
						$(".invalid_exclusive_voucher_code").show();
					}else{
						window.location.href = data['location'];
					}
				}
			});
		}
	});
	// Adding the clear Fix
	cols1 = $('#column-right, #column-left').length;

	/*if (cols1 == 2) {
		$('#content .product-layout:nth-child(2n+2)').after('<div class="clearfix visible-md visible-sm"></div>');
	} else if (cols1 == 1) {
		$('#content .product-layout:nth-child(4n+4)').after('<div class="clearfix visible-lg"></div>');
	} else {
		$('#content .product-layout:nth-child(4n+4)').after('<div class="clearfix"></div>');
	}*/

	// Highlight any found errors
	$('.text-danger').each(function() {
		var element = $(this).parent().parent();

		if (element.hasClass('form-group')) {
			element.addClass('has-error');
		}
	});

	// Currency
	$('#currency .currency-select').on('click', function(e) {
		e.preventDefault();

		$('#currency input[name=\'code\']').attr('value', $(this).attr('name'));

		$('#currency').submit();
	});

	// Language
	$('#language a').on('click', function(e) {
		e.preventDefault();

		$('#language input[name=\'code\']').attr('value', $(this).attr('href'));

		$('#language').submit();
	});

	/* Search */
    function isEmpty(value) {
        return typeof value == 'string' && !value.trim() || typeof value == 'undefined' || value === null;
    }
	// $('#search input[name=\'search\']').parent().find('.search-button').on('click', function() {
	$('#search_magnifier').on('click', function() {
		url = 'index.php?route=product/search';

		var value = $('header input[name=\'search\']').val();
		var category = $('header #header-cat-dropdown').val();

		if (value) {
			url += '&search=' + encodeURIComponent(value);
		}
		if (!isEmpty(category)) {
			url += '&category_id=' + encodeURIComponent(category);
		}

		location = url;
	});

	$('#search1 input[name=\'search\']').on('keydown', function(e) {
		if (e.keyCode == 13) {
			$('#search_magnifier').trigger('click');
		}
	});

	// Menu
	$('#menu .dropdown-menu').each(function() {
		var menu = $('#menu').offset();
		var dropdown = $(this).parent().offset();

		var i = (dropdown.left + $(this).outerWidth()) - (menu.left + $('#menu').outerWidth());

		if (i > 0) {
			$(this).css('margin-left', '-' + (i + 5) + 'px');
		}
	});

	// Product List
	$('#list-view').click(function() {
		$('#content .product-layout > .clearfix').remove();

		//$('#content .product-layout').attr('class', 'product-layout product-list col-xs-12');
		/*$('#content .row > .product-layout').attr('class', 'product-layout product-list col-xs-12');

		localStorage.setItem('display', 'list');
		*/
		//added code block given below is to show always grid
		// What a shame bootstrap does not take into account dynamically loaded columns
		cols = $('#column-right, #column-left').length;

		if (cols == 2) {
			$('#content .product-layout').attr('class', 'product-layout product-grid col-lg-6 col-md-6 col-sm-12 col-xs-12');
		} else if (cols == 1) {
			$('#content .product-layout').attr('class', 'product-layout product-grid col-lg-4 col-md-4 col-sm-6 col-xs-12');
		} else {
			$('#content .product-layout').attr('class', 'product-layout product-grid col-lg-3 col-md-3 col-sm-6 col-xs-12');
		}

		localStorage.setItem('display', 'grid');
	});

	// Product Grid
	$('#grid-view').click(function() {
		$('#content .product-layout > .clearfix').remove();

		// What a shame bootstrap does not take into account dynamically loaded columns
		cols = $('#column-right, #column-left').length;

		if (cols == 2) {
			$('#content .product-layout').attr('class', 'product-layout product-grid col-lg-6 col-md-6 col-sm-12 col-xs-12');
		} else if (cols == 1) {
			$('#content .product-layout').attr('class', 'product-layout product-grid col-lg-4 col-md-4 col-sm-6 col-xs-12');
		} else {
			$('#content .product-layout').attr('class', 'product-layout product-grid col-lg-3 col-md-3 col-sm-6 col-xs-12');
		}

		 localStorage.setItem('display', 'grid');
	});

	if (localStorage.getItem('display') == 'list') {
		$('#list-view').trigger('click');
	} else {
		$('#grid-view').trigger('click');
	}

	// tooltips on hover
	$('[data-toggle=\'tooltip\']').tooltip({container: 'body'});

	// Makes tooltips work on ajax generated content
	$(document).ajaxStop(function() {
		$('[data-toggle=\'tooltip\']').tooltip({container: 'body'});
	});

    // product search on home page
/*
    $("div#search input.form-control").keyup(function(){
        //alert($(this).val());
        var text = $(this).val();
        if(text.length >= 2){
            var url = "index.php?route=product/product/getsearchproduct";

            $(this).autocomplete({
                source: function( request, response ) {
                    //alert("here");
                    $.ajax({
                        url : "index.php?route=product/searchproduct/getsearchproduct",
                        type: "post",
                        dataType: "json",
                        data: "name="+text,
                        success: function( data ) {

                            response( $.map( data, function( item ) {
                                //console.log(item);
                                var code = item.split("|");

                                return {label: highlight(item, text),
                                    value: item};

                            }));
                        }
                    });
                },
                autoFocus: true,
                minLength: 0,
                select: function( event, ui ) {
                    var names = event.value;
                    $('div#search input.form-control').val(names);
                }
            });
        }
    });
*/
});

function highlight(value, term) { //console.log(value);console.log(term);
    return value.replace(new RegExp("("+term+")", "gi"),'<b>$1</b>');
}

// Cart add remove functions
var cart = {
	'add': function(product_id, quantity) {
	 if(getCookie("customer_mobile") == '')
        {
        	$("input[name=redirect_cart]").val(product_id+'-'+quantity);
            $('#login_verify_popup').modal('show');
        }
    else if(getCookie("register_user") == 1 && getCookie("customer_id") == '')
       {
       	 $("input[name=redirect_cart]").val(product_id+'-'+quantity);
         $('#login_popup').modal('show');
       }  
    else
        {
		$.ajax({
			url: 'index.php?route=checkout/cart/add',
			type: 'post',
			data: 'product_id=' + product_id + '&quantity=' + (typeof(quantity) != 'undefined' ? quantity : 1),
			dataType: 'json',
			beforeSend: function() {
				$('#cart > button').button('loading');
			},
			complete: function() {
				$('#cart > button').button('reset');
			},
			success: function(json) {
				$('.alert, .text-danger').remove();

				if (json['redirect']) {
					location = json['redirect'];
				}

				if (json['success']) {
					$('#content').parent().before('<div class="alert alert-success addedincart"><i class="fa fa-check-circle"></i> ' + json['success'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');

					// Need to set timeout otherwise it wont update the total
					setTimeout(function () {

						var mq = window.matchMedia( "(max-width: 500px)" );
						if (mq.matches) {
							// window width is at least 500px
							$('#cart > button').html('<i class="fa fa-shopping-cart"></i><span id="cart-total">' + json['total_in_cart'] + '</span>');

							//$('span#cart-total-desktop').hide();

						}
						else {

							// window width is less than 500px
							//$('#cart-total').hide();
							$('#cart > button').html('<i class="fa fa-shopping-cart"></i><span id="cart-total-desktop"> Cart <span class="cart_number">' + json['total_in_cart'] + '</span></span>');
						}
					}, 100);


					//$('html, body').animate({ scrollTop: 0 }, 'slow');
					setTimeout(function() {
						$('.addedincart').fadeOut('slow');
					}, 3000); // <-- time in milliseconds

					$('#cart > ul').load('index.php?route=common/cart/info ul li');
				}
			}
		});
    }

	},
	'update': function(key, quantity) {
		$.ajax({
			url: 'index.php?route=checkout/cart/edit',
			type: 'post',
			data: 'key=' + key + '&quantity=' + (typeof(quantity) != 'undefined' ? quantity : 1),
			dataType: 'json',
			beforeSend: function() {
				$('#cart > button').button('loading');
			},
			complete: function() {
				$('#cart > button').button('reset');
			},
			success: function(json) {
				// Need to set timeout otherwise it wont update the total
				setTimeout(function () {
					$('#cart > button').html('<span id="cart-total"><i class="fa fa-shopping-cart"></i> ' + json['total'] + '</span>');
				}, 100);

				if (getURLVar('route') == 'checkout/cart' || getURLVar('route') == 'checkout/checkout') {
					location = 'index.php?route=checkout/cart';
				} else {
					$('#cart > ul').load('index.php?route=common/cart/info ul li');
				}
			}
		});
	},
	'remove': function(key) { 
		$.ajax({
			url: 'index.php?route=checkout/cart/remove',
			type: 'post',
			data: 'key=' + key,
			dataType: 'json',
			beforeSend: function() {
				$('#cart > button').button('loading');
			},
			complete: function() {
				$('#cart > button').button('reset');
			},
			success: function(json) {
				// Need to set timeout otherwise it wont update the total
				setTimeout(function () {
					$('#cart > button').html('<span id="cart-total"><i class="fa fa-shopping-cart"></i> ' + json['total'] + '</span>');
				}, 100);
					location = 'cart';
			}
		});
	}
}

var voucher = {
	'add': function() {

	},
	'remove': function(key) {
		$.ajax({
			url: 'index.php?route=checkout/cart/remove',
			type: 'post',
			data: 'key=' + key,
			dataType: 'json',
			beforeSend: function() {
				$('#cart > button').button('loading');
			},
			complete: function() {
				$('#cart > button').button('reset');
			},
			success: function(json) {
				// Need to set timeout otherwise it wont update the total
				setTimeout(function () {
					$('#cart > button').html('<span id="cart-total"><i class="fa fa-shopping-cart"></i> ' + json['total'] + '</span>');
				}, 100);

				if (getURLVar('route') == 'checkout/cart' || getURLVar('route') == 'checkout/checkout') {
					location = 'index.php?route=checkout/cart';
				} else {
					$('#cart > ul').load('index.php?route=common/cart/info ul li');
				}
			}
		});
	}
}

var wishlist = {
	'add': function(product_id,e) {

		if ($(".ctoken").get(0)) {
			var ctoken = $(".ctoken").val();
		} else {
			var ctoken = 0;
		}
		$.ajax({
			url: 'index.php?route=account/wishlist/add&ctoken='+ctoken,
			type: 'post',
			data: 'product_id=' + product_id,
			dataType: 'json',
			success: function(json) {
				$('.alert').remove();

				if (json['success']) {

                    $(document.body).append('<div class="alert alert-success" style="position:absolute; top:'+yoffset+'px; left:20%; margin:15px; padding:15px; z-index:10000; width:70%;"><i class="fa fa-check-circle"></i> ' + json['success'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				}

				if (json['info']){
                   	if($('body div:first').attr('id') == "header-mobile")
                    	var yoffset = e.pageY;
                   	else
                        var yoffset = e.pageY - 100;
                    $(document.body).append('<div class="alert alert-info" style="position:absolute; top:'+yoffset+'px; left:20%; margin:15px; padding:15px; z-index:10000; width:70%;"><i class="fa fa-info-circle"></i> ' + json['info'] + '&nbsp;<button type="button" class="close pull-right" data-dismiss="alert">&times;</button></div>');
				}

				$('#wishlist-total span').html(json['total']);
				$('#wishlist-total').attr('title', json['total']);

				//$('html, body').animate({ scrollTop: 0 }, 'slow');
				//$('html, body').animate({ scrollTop: 0 }, 'slow');
				setTimeout(function() {
					$('.alert-info').fadeOut('slow');
				}, 3000); // <-- time in milliseconds
			}
		});
	},
	'remove': function() {

	}
}

var compare = {
	'add': function(product_id) {
		$.ajax({
			url: 'index.php?route=product/compare/add',
			type: 'post',
			data: 'product_id=' + product_id,
			dataType: 'json',
			success: function(json) {
				$('.alert').remove();

				if (json['success']) {
					$('#content').parent().before('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');

					$('#compare-total').html(json['total']);

					$('html, body').animate({ scrollTop: 0 }, 'slow');
				}
			}
		});
	},
	'remove': function() {

	}
}

/* Agree to Terms */
$(document).delegate('.agree', 'click', function(e) {
	e.preventDefault();

	$('#modal-agree').remove();

	var element = this;

	$.ajax({
		url: $(element).attr('href'),
		type: 'get',
		dataType: 'html',
		success: function(data) {
			html  = '<div id="modal-agree" class="modal">';
			html += '  <div class="modal-dialog">';
			html += '    <div class="modal-content">';
			html += '      <div class="modal-header">';
			html += '        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
			html += '        <h4 class="modal-title">' + $(element).text() + '</h4>';
			html += '      </div>';
			html += '      <div class="modal-body">' + data + '</div>';
			html += '    </div';
			html += '  </div>';
			html += '</div>';

			$('body').append(html);

			$('#modal-agree').modal('show');
		}
	});
});



function createCookie(name, value, days) {

	var expires;
	if (days) {
		var date = new Date();
		date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
		expires = "; expires=" + date.toGMTString();
	}
	else {
		expires = "";
	}
	document.cookie = name + "=" + value + expires + "; path=/";
}

function getCookie(c_name) {
	if (document.cookie.length > 0) {
		c_start = document.cookie.indexOf(c_name + "=");
		if (c_start != -1) {
			c_start = c_start + c_name.length + 1;
			c_end = document.cookie.indexOf(";", c_start);
			if (c_end == -1) {
				c_end = document.cookie.length;
			}
			return unescape(document.cookie.substring(c_start, c_end));
		}
	}
	return "";
}
/***Search.tpl JS Code desktop */
$(document).ready(function(){
	$(".dropdown-menu li a").click(function () {
		$(this).parents(".dropdown").find('.btn').html($(this).text() + ' <span class="caret"></span>');
		$(this).parents(".dropdown").find('.btn').val($(this).attr('data-cat'));
	});
});
$(document).ready(function() {
	$("#mainsearch").autocomplete({
		source: 'index.php?route=common/search/autoComplete',
		dataType: "json",
		success: function (data) {
			response($.map(data, function (item) {

				return {
					label: item['label'],
					value: item['value']
				}
			}));
		},
		autoFocus: false,
		select: function (event, ui) {
			$('input[name=\'search\']').val(ui.item.label);
		},
		open: function (event, ui) {
			$(".ui-autocomplete").addClass('dropdown-menu');
			$(".ui-autocomplete").css("z-index", 1000);
		},
		create: function () {
			$(this).data('ui-autocomplete')._renderItem = function (ul, item) {
				return $('<li>')
						.append( "<a>" + item.label + "</a>" )
						.appendTo(ul);
			};
		}
	})
});
/** End search.tpl desktop */
/** Header.tpl desktop */
$(document).ready(function() {
	$(window).scroll(function () {
		if ($(this).scrollTop() > 1) {
			$('#top').slideUp('fast');
			//$('#recentSlideBtn').slideUp('fast');
			$('#top_nav').slideUp('fast');
			$('#header-desktop .sticky_new').css({"position":"fixed","width":"100%","margin-top":"20px"});
			$('#header-desktop header').css({"position":"fixed","height":"80px","z-index":"999","width":"100%","top":"0px"});
			$('#header-desktop header').slideDown(1000);
			$('#header-desktop .sticky_new').slideDown(1000);
		} else {
			$('#top').slideDown('fast');
			$('#recentSlideBtn').slideDown('fast');
			//$('#top_nav').slideDown('fast');
			$('#header-desktop .sticky_new').css({"position":"","width":"","margin-top":""});
			$('#header-desktop header').css({"position":"","height":"","z-index":"","width":""});			
			$('#header-desktop .sticky_new').slideDown(1000);
			$('#header-desktop header').slideDown(1000);
		}
	});

	$('#store_switch').on('click', function (argument) {
		$.ajax({
			url: 'index.php?route=common/header/getStoreSwitchNew',
			dataType: 'json',

			beforeSend: function () {
				$('body').removeClass('loaded').addClass('loading');
			},

			success: function (json) {
				location.reload();

			},
			error: function (xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}

		});
	});
});
/** Hedaer.tpl desktop */
/** Home.tpl */
$(document).ready(function() {
	$("div.tab-menu>div.tab_list>a").click(function(e) {
		e.preventDefault();
		$(this).siblings('a.active').removeClass("active");
		$(this).addClass("active");
		var index = $(this).index();
		$("div.bhoechie-tab>div.tab-text").removeClass("active");
		$("div.bhoechie-tab>div.tab-text").eq(index).addClass("active");
	});

/**
 * Product hover code
* */

$(document).ready(function() {
	$('.addtowishlist').click(function () {
		$('#wishlist_heart_' + $(this).attr('data-product-id')).removeClass("fa fa-heart-o").addClass("fa fa-heart custom_heart");
	});
});

	$(document).ready(function(){
		$('.product-thumb, .hover_description').mouseover(function(){
			//alert($( this ).attr('data-product-id'));
			$('#hover_description_'+$(this).attr('data-product-id')).show();
		}).mouseleave(function(){
			//alert($( this ).attr('data-product-id'));
			$('#hover_description_'+$(this).attr('data-product-id')).hide();
		});

		$('.button_add_wishlist').click(function(){
			$('#wishlist_heart_'+$(this).attr('data-product-id')).removeClass( "fa fa-heart-o" ).addClass( "fa fa-heart custom_heart" );
		});

		/*
		 $('#wishlist_'+<?php echo $product['product_id']; ?>).click(function(){
		 $('#wishlist_heart_'+<?php echo $product['product_id']; ?>).removeClass( "fa fa-heart-o" ).addClass( "fa fa-heart fa-3x custom_heart" );;
		 });
		 */
	});

	$(document).ready(function() {
		$('.product-thumb, .hover_description').mouseover(function () {
			//alert($( this ).attr('data-product-id'));
			$('#hover_description_' + $(this).attr('data-product-id')).show();
		}).mouseleave(function () {
			//alert($( this ).attr('data-product-id'));
			$('#hover_description_' + $(this).attr('data-product-id')).hide();
		});

		$('.button_add_wishlist').click(function () {
			$('#wishlist_heart_' + $(this).attr('data-product-id')).removeClass("fa fa-heart-o").addClass("fa fa-heart custom_heart");
		});
	});

});
function cart_add_animate(imgtodrag) {
 var cart = $('#cart > button#cart_btn');
 // var imgtodrag = $(this).parent('.item').find("img").eq(0);
 if (imgtodrag) {
     var imgclone = imgtodrag.clone()
         .offset({
         top: imgtodrag.offset().top,
         left: imgtodrag.offset().left
     })
         .css({
         'opacity': '0.5',
             'position': 'absolute',
             'height': '150px',
             'width': '150px',
             'z-index': '10000'
     })
         .appendTo($('body'))
         .animate({
         'top': cart.offset().top + 10,
             'left': cart.offset().left + 10,
             'width': 75,
             'height': 75
     }, 1000, 'easeInOutExpo');
     
     setTimeout(function () {
         cart.effect("shake", {
             times: 2
         }, 200);
     }, 1500);

     imgclone.animate({
         'width': 0,
             'height': 0
     }, function () {
         $(this).detach()
     });
 }
}
$(document).ready(function() {
	$('.post_code').keyup(function () {
		var char_count = $(this).val().length;
		if (char_count > 5) {
			var parent_class = $(this).attr('data-parent_class');
			if ($("#ctoken").get(0)) {
				var ctoken = $("#ctoken").val();
			} else {
				var ctoken = 0;
			}

			var pincode = $('.' + parent_class + ' .post_code').val();
			$.ajax({
				url: "index.php?route=account/address/autoPopulateAddress&ctoken=" + ctoken,
				type: "post",
				dataType: "json",
				data: "pincode=" + pincode,
				success: function (json) {

					$('.' + parent_class + ' .city').val(json['city']);

					if(parent_class == "shipping"){
                        //if same address is checked and change the shipping address
                        if($("#input-payment-postcode").val() != $("#input-shipping-postcode").val()){
                            $('input[name=\'same_address_shipping\']').prop('checked',false);
                        }

						$("." + parent_class + " .zone option").prop('selected', false).filter(function() {
							return $(this).text() == json['state'];
						}).prop('selected', true);
                        $("." + parent_class + " .zone").trigger('change');

					} else if(parent_class == "payment") {

						$("." + parent_class + " .zone option").prop('selected', false).filter(function() {
							return $(this).text() == json['state'];
						}).prop('selected', true);


						//update shipping address if same address is checked
						if($('input[name=\'same_address_shipping\']').is(':checked')){
							if(json['state'].length > 0 && json['city'].length > 0)
                            	$('input[name=\'same_address_shipping\']').prop('checked',false).trigger('click');
							else
                                $('input[name=\'same_address_shipping\']').prop('checked',false);
                        }

					} else {
						// do nothing
					}
				}
			});
		}
	});
});
