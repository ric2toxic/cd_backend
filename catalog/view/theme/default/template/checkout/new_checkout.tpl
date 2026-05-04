<?php echo $header; ?>
<script  src="catalog/view/javascript/jquery/datetimepicker/moment.js" type="text/javascript"></script>
<script  src="catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script  src="catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css" type="text/javascript"></script>
<div class="container">
    <!--<ul class="breadcrumb">
        <?php /* ?>
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
        <?php */ ?>
    </ul> -->
    <div class="col-sm-12 checkout_steps">
        <div class="checkout_step1" id="checkout_step1"></div>
        <div class="checkout_step2" id="checkout_step2"></div>
        <div class="checkout_step3" id="checkout_step3"></div>
    </div>
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="row"><?php echo $column_left; ?>
        <?php if ($column_left && $column_right) { ?>
        <?php $class = 'col-sm-6'; ?>
        <?php } elseif ($column_left || $column_right) { ?>
        <?php } else { ?>
        <?php $class = 'col-sm-12'; ?>
        <?php } ?>
        <div id="content" class="<?php echo $class; ?>"><?php echo $content_top; ?>
            <?php /* ?><!--<h1><?php echo $heading_title; ?></h1>  --> <?php */ ?>
            <div class="col-sm-8 checkout-pages-block">
                <div class="panel-group" id="accordion">
                    <?php if (!$logged && $account != 'guest') { ?>
                    <div class="panel panel-default">
                        <div class="checkout-login-page"></div>
                        <!--<div class="panel-heading">
                        <h4 class="panel-title"><?php echo $text_checkout_option; ?></h4>
                    </div>
                    <div class="panel-collapse collapse" id="collapse-checkout-option">
                        <div class="panel-body"></div>
                    </div> -->
                    </div>
                    <?php } ?>
                    <?php if (!$logged && $account != 'guest') { ?>
                    <div class="panel panel-default account_billing_detail">
                        <div class="panel-heading">
                            <h4 class="panel-title"><?php echo $text_checkout_account; ?></h4>
                        </div>
                        <div class="panel-collapse collapse" id="collapse-payment-address">
                            <div class="panel-body"></div>
                        </div>
                    </div>
                    <?php } else { ?>
                    <div class="panel panel-default checkout_payment_address">
                        <div class="panel-heading col-sm-12">
                            <h4 class="panel-title col-sm-8"><?php echo $text_checkout_payment_address; ?></h4>
                            <div class="col-sm-4">
                                <div class="continue back_collapse back_payment_address">
                                    <?php echo $button_back; ?>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="collapse-payment-address" id="collapse-payment-address">
                            <?php if (isset($default_address_delete_error) && $default_address_delete_error) { ?>
                                <div class="col-sm-12 warning_default">
                                    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $default_address_delete_error; ?>
                                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="panel-body"></div>
                        </div>
                    </div>
                    <?php } ?>
                    <?php if ($shipping_required) { ?>
                    <div class="panel panel-default new_shipping_address">
                        <div class="panel-heading col-sm-12">
                            <h4 class="panel-title col-sm-8"><?php echo $text_checkout_shipping_address; ?></h4>
                            <div class="col-sm-4">
                                <div class="continue back_shipment_collapse">
                                        <?php echo $button_back; ?>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="collapse-shipping-address" id="collapse-shipping-address">
                            <div class="panel-body"></div>
                        </div>
                    </div>
                    <div class="panel panel-default shipping-method-box">
                        <div class="panel-heading">
                            <h4 class="panel-title"><?php echo $text_checkout_shipping_method; ?></h4>
                        </div>
                        <div class="panel-collapse collapse" id="collapse-shipping-method">
                            <div class="panel-body"></div>
                        </div>
                    </div>
                    <?php } ?>
                    <div class="panel panel-default checkout_payment_method">
                        <div class="panel-heading col-sm-12">
                            <h4 class="panel-title col-sm-8"><?php echo $text_checkout_payment_method; ?></h4>
                            <div class="col-sm-4">
                                <div class="continue back_collapse back-payment-method">
                                    <?php echo $button_back; ?>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="collapse-payment-method" id="collapse-payment-method">
                            <div class="panel-body"></div>
                        </div>
                    </div>
                    <div class="panel panel-default checkout_confirm">
                        <!--<div class="panel-heading">
                            <h4 class="panel-title"><?php echo $text_checkout_confirm; ?></h4>
                        </div>-->
                        <div class="collapse-checkout-confirm" id="collapse-checkout-confirm">
                            <div class="panel-body"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 order-summary-block">
                <div class="order-summary-show">
                    <div class="order-summary">
                        <h3 class="orderr-summary-title"><?php echo $order_summary; ?></h3>
                    </div>
                    <div class="order-list"></div>
                </div>
            </div>
            <?php echo $content_bottom; ?></div>
        <?php echo $column_right; ?></div>
</div>
<script type="text/javascript"><!--
    $(document).on('change', 'input[name=\'account\']', function() {
        if ($('#collapse-payment-address').parent().find('.panel-heading .panel-title > *').is('a')) {
            if (this.value == 'register') {
                $('#collapse-payment-address').parent().find('.panel-heading .panel-title').html('<a href="#collapse-payment-address" data-toggle="collapse" data-parent="#accordion" class="accordion-toggle"><?php echo $text_checkout_account; ?> <i class="fa fa-caret-down"></i></a>');
            } else {
                $('#collapse-payment-address').parent().find('.panel-heading .panel-title').html('<a href="#collapse-payment-address" data-toggle="collapse" data-parent="#accordion" class="accordion-toggle"><?php echo $text_checkout_payment_address; ?> <i class="fa fa-caret-down"></i></a>');
            }
        } else {
            if (this.value == 'register') {
                $('#collapse-payment-address').parent().find('.panel-heading .panel-title').html('<?php echo $text_checkout_account; ?>');
            } else {
                $('#collapse-payment-address').parent().find('.panel-heading .panel-title').html('<?php echo $text_checkout_payment_address; ?>');
            }
        }
    });

    <?php if (!$logged) { ?>
        $(document).ready(function() {
            $.ajax({
                url: 'index.php?route=checkout/login',
                dataType: 'html',
                success: function(html) {
                    $('.checkout-login-page').html(html);
                    //$('#collapse-checkout-option').parent().find('.panel-heading .panel-title').html('<a href="#collapse-checkout-option" data-toggle="collapse" data-parent="#accordion" class="accordion-toggle"><?php echo $text_checkout_option; ?> <i class="fa fa-caret-down"></i></a>');

                    //$('a[href=\'.checkout-login-page\']').trigger('click');
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
        });
    <?php } else { ?>
        $(document).ready(function() {
            $.ajax({
                url: 'index.php?route=checkout/payment_address',
                dataType: 'html',
                success: function(html) {
                    $('#collapse-payment-address .panel-body').html(html);

                    $('#collapse-payment-address').parent().find('.panel-heading .panel-title').html('<p class="accordion-toggle"><?php echo $text_checkout_payment_address; ?></p>');

                    $('a[href=\'#collapse-payment-address\']').trigger('click');

                    $('.checkout_step1').show();
                    $('.checkout_step2').hide();
                    $('.checkout_step3').hide();
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
        });
    <?php } ?>

    // Checkout
    $(document).delegate('#button-account', 'click', function() {
        $.ajax({
            url: 'index.php?route=checkout/' + $('input[name=\'account\']:checked').val(),
            dataType: 'html',
            beforeSend: function() {
                //$('#button-account').button('loading');
                $('body').removeClass('loading').addClass('loading');
            },
            complete: function() {
                //$('#button-account').button('reset');
                setTimeout(function(){
                    $('body').removeClass('loading');
                },1000);
            },
            success: function(html) {
                $('.alert, .text-danger').remove();

                $('#collapse-payment-address .panel-body').html(html);

                if ($('input[name=\'account\']:checked').val() == 'register') {
                    $('#collapse-payment-address').parent().find('.panel-heading .panel-title').html('<a href="#collapse-payment-address" data-toggle="collapse" data-parent="#accordion" class="accordion-toggle"><?php echo $text_checkout_account; ?> <i class="fa fa-caret-down"></i></a>');
                } else {
                    $('#collapse-payment-address').parent().find('.panel-heading .panel-title').html('<a href="#collapse-payment-address" data-toggle="collapse" data-parent="#accordion" class="accordion-toggle"><?php echo $text_checkout_payment_address; ?> <i class="fa fa-caret-down"></i></a>');
                }

                $('a[href=\'#collapse-payment-address\']').trigger('click');
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });

    // Login
    $(document).delegate('#button-login', 'click', function() {
        $.ajax({
            url: 'index.php?route=checkout/login/save',
            type: 'post',
            data: $('#collapse-checkout-option :input'),
            dataType: 'json',
            success: function(json) {
                $('.alert, .text-danger').remove();
                $('.form-group').removeClass('has-error');

                if (json['redirect']) {
                    location = json['redirect'];
                } else if (json['error']) {
                    $('#sign_in #collapse-checkout-option').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error']['warning'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');

                    // Highlight any found errors
                    $('#sign_in input[name=\'email\']').parent().addClass('has-error');
                    $('#sign_in input[name=\'password\']').parent().addClass('has-error');
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });

    // Register
    $(document).delegate('#button-register', 'click', function() {
        $.ajax({
            url: 'index.php?route=checkout/register/save',
            type: 'post',
            data: $('#sign_up #collapse-checkout-option input[type=\'text\'], #sign_up #collapse-checkout-option input[type=\'password\'], #sign_up #collapse-checkout-option input[type=\'hidden\'], #sign_up #collapse-checkout-option input[type=\'checkbox\']:checked'),
            dataType: 'json',
            success: function(json) {
                $('.alert, .text-danger').remove();
                $('.form-group').removeClass('has-error');

                if (json['redirect']) {
                    location = json['redirect'];
                } else if (json['error']) {
                    if (json['error']['warning']) {
                        $('#sign_up #collapse-checkout-option').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error']['warning'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                    }

                    for (i in json['error']) {
                        var element = $('#input-payment-' + i.replace('_', '-'));

                        if ($(element).parent().hasClass('input-group')) {
                            $(element).parent().after('<div class="text-danger">' + json['error'][i] + '</div>');
                        } else {
                            $(element).after('<div class="text-danger">' + json['error'][i] + '</div>');
                        }
                    }

                    // Highlight any found errors
                    $('.text-danger').parent().addClass('has-error');
                } else {
                <?php if ($shipping_required) { ?>
                        var shipping_address = $('#payment-address input[name=\'shipping_address\']:checked').prop('value');

                        if (shipping_address) {
                            $.ajax({
                                url: 'index.php?route=checkout/shipping_method',
                                dataType: 'html',
                                success: function(html) {
                                    // Add the shipping address
                                    $.ajax({
                                        url: 'index.php?route=checkout/shipping_address',
                                        dataType: 'html',
                                        success: function(html) {
                                            $('#collapse-shipping-address .panel-body').html(html);

                                            $('#collapse-shipping-address').parent().find('.panel-heading .panel-title').html('<a href="#collapse-shipping-address" data-toggle="collapse" data-parent="#accordion" class="accordion-toggle"><?php echo $text_checkout_shipping_address; ?> <i class="fa fa-caret-down"></i></a>');
                                        },
                                        error: function(xhr, ajaxOptions, thrownError) {
                                            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                                        }
                                    });

                                    $('#collapse-shipping-method .panel-body').html(html);

                                    $('#collapse-shipping-method').parent().find('.panel-heading .panel-title').html('<a href="#collapse-shipping-method" data-toggle="collapse" data-parent="#accordion" class="accordion-toggle"><?php echo $text_checkout_shipping_method; ?> <i class="fa fa-caret-down"></i></a>');

                                    $('a[href=\'#collapse-shipping-method\']').trigger('click');

                                    $('#collapse-shipping-method').parent().find('.panel-heading .panel-title').html('<?php echo $text_checkout_shipping_method; ?>');
                                    $('#collapse-payment-method').parent().find('.panel-heading .panel-title').html('<?php echo $text_checkout_payment_method; ?>');
                                    $('#collapse-checkout-confirm').parent().find('.panel-heading .panel-title').html('<?php echo $text_checkout_confirm; ?>');
                                },
                                error: function(xhr, ajaxOptions, thrownError) {
                                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                                }
                            });
                        } else {
                            $.ajax({
                                url: 'index.php?route=checkout/shipping_address',
                                dataType: 'html',
                                success: function(html) {
                                    $('#collapse-shipping-address .panel-body').html(html);

                                    $('#collapse-shipping-address').parent().find('.panel-heading .panel-title').html('<a href="#collapse-shipping-address" data-toggle="collapse" data-parent="#accordion" class="accordion-toggle"><?php echo $text_checkout_shipping_address; ?> <i class="fa fa-caret-down"></i></a>');

                                    $('a[href=\'#collapse-shipping-address\']').trigger('click');

                                    $('#collapse-shipping-method').parent().find('.panel-heading .panel-title').html('<?php echo $text_checkout_shipping_method; ?>');
                                    $('#collapse-payment-method').parent().find('.panel-heading .panel-title').html('<?php echo $text_checkout_payment_method; ?>');
                                    $('#collapse-checkout-confirm').parent().find('.panel-heading .panel-title').html('<?php echo $text_checkout_confirm; ?>');
                                },
                                error: function(xhr, ajaxOptions, thrownError) {
                                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                                }
                            });
                        }
                    <?php } else { ?>
                        $.ajax({
                            url: 'index.php?route=checkout/payment_method',
                            dataType: 'html',
                            success: function(html) {
                                $('#collapse-payment-method .panel-body').html(html);

                                $('#collapse-payment-method').parent().find('.panel-heading .panel-title').html('<a href="#collapse-payment-method" data-toggle="collapse" data-parent="#accordion" class="accordion-toggle"><?php echo $text_checkout_payment_method; ?> <i class="fa fa-caret-down"></i></a>');

                                $('a[href=\'#collapse-payment-method\']').trigger('click');

                                $('#collapse-checkout-confirm').parent().find('.panel-heading .panel-title').html('<?php echo $text_checkout_confirm; ?>');
                            },
                            error: function(xhr, ajaxOptions, thrownError) {
                                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                            }
                        });
                    <?php } ?>

                    $.ajax({
                        url: 'index.php?route=checkout/payment_address',
                        dataType: 'html',
                        success: function(html) {
                            $('#collapse-payment-address .panel-body').html(html);

                            $('#collapse-payment-address').parent().find('.panel-heading .panel-title').html('<a href="#collapse-payment-address" data-toggle="collapse" data-parent="#accordion" class="accordion-toggle"><?php echo $text_checkout_payment_address; ?> <i class="fa fa-caret-down"></i></a>');
                        },
                        error: function(xhr, ajaxOptions, thrownError) {
                            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                        }
                    });
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });

    // Payment Address
    $(document).delegate('#button-payment-address', 'click', function() {
        $("body").scrollTop(0);
        var existing_check_ship_same_value = 0;
        var payment_check_ship_same_value = 0;

        if(!$('#payment-existing #checkbox_same_ship_address').is(':checked')){
            existing_check_ship_same_value = $('#payment-existing #checkbox_same_ship_address').val();
        }else{
            existing_check_ship_same_value = $('#payment-existing #checkbox_same_ship_address').val();
        }

        if(!$('#payment-new #checkbox_same_ship_address').is(':checked')){
            payment_check_ship_same_value = $('#payment-new #checkbox_same_ship_address').val();
            if(typeof existing_check_ship_same_value=='undefined'){
                existing_check_ship_same_value = 0;
            }
        }else{
            payment_check_ship_same_value = $('#payment-new #checkbox_same_ship_address').val();
            if(typeof existing_check_ship_same_value=='undefined'){
                existing_check_ship_same_value = 1;
            }
        }

        $.ajax({
            url: 'index.php?route=checkout/payment_address/save',
            type: 'post',
            data: $('#collapse-payment-address input[type=\'text\'], #collapse-payment-address input[type=\'date\'], #collapse-payment-address input[type=\'datetime-local\'], #collapse-payment-address input[type=\'time\'], #collapse-payment-address input[type=\'password\'], #collapse-payment-address input[type=\'checkbox\'], #collapse-payment-address input[type=\'radio\']:checked, #collapse-payment-address input[type=\'hidden\'], #collapse-payment-address textarea, #collapse-payment-address select'),
            dataType: 'json',
            beforeSend: function() {
                //$('#button-payment-address').button('loading');
                $('body').removeClass('loading').addClass('loading');
            },
            complete: function() {
                //$('#button-payment-address').button('reset');
                setTimeout(function(){
                    $('body').removeClass('loading');
                }, 1000);
            },
            success: function(json) {

                $('.alert, .text-danger').remove();

                if (json['redirect']) {
                    location = json['redirect'];
                } else if (json['error']) {
                    if (json['error']['warning']) {
                        $('#collapse-payment-address .panel-body').prepend('<div class="alert alert-warning">' + json['error']['warning'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                    }

                    for (i in json['error']) {
                        var element = $('#input-payment-' + i.replace('_', '-'));

                        if ($(element).parent().hasClass('input-group')) {
                            $(element).parent().after('<div class="text-danger">' + json['error'][i] + '</div>');
                        } else {
                            $(element).after('<div class="text-danger">' + json['error'][i] + '</div>');
                        }
                    }

                    // Highlight any found errors
                    $('.text-danger').parent().parent().addClass('has-error');
                } else {

                <?php if ($shipping_required) { ?>
                        if((existing_check_ship_same_value==0) || payment_check_ship_same_value==0) {
                            $('.account_billing_detail').hide();
                            $('.new_shipping_address').show();
                            $('.checkout_payment_address').hide();
                            $('.checkout_payment_method').hide();
                            $('.checkout_confirm').hide();

                            $.ajax({
                                url: 'index.php?route=checkout/shipping_address',
                                dataType: 'html',
                                success: function(html) {

                                    $('.checkout_step1').show();
                                    $('.checkout_step2').hide();
                                    $('.checkout_step3').hide();
                                    $('#collapse-shipping-address .panel-body').html(html);

                                    $('#collapse-shipping-address').parent().find('.panel-heading .panel-title').html('<p class="accordion-toggle"><?php echo $text_checkout_shipping_address; ?> </p>');

                                    $('a[href=\'#collapse-shipping-address\']').trigger('click');

                                    $('#collapse-shipping-method').parent().find('.panel-heading .panel-title').html('<?php echo $text_checkout_shipping_method; ?>');
                                    $('#collapse-payment-method').parent().find('.panel-heading .panel-title').html('<?php echo $text_checkout_payment_method; ?>');
                                    $('#collapse-checkout-confirm').parent().find('.panel-heading .panel-title').html('<?php echo $text_checkout_confirm; ?>');
                                },
                                error: function(xhr, ajaxOptions, thrownError) {
                                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                                }
                            });
                        }else{
                            $('.account_billing_detail').hide();
                            $('.new_shipping_address').hide();
                            $('.checkout_payment_address').hide();
                            $('.checkout_payment_method').show();
                            $('.checkout_confirm').hide();

                            $('.checkout_step1').hide();
                            $('.checkout_step2').show();
                            $('.checkout_step3').hide();

                            $.ajax({
                                url: 'index.php?route=checkout/shipping_method',
                                dataType: 'html',
                                success: function(html) {
                                    $('#collapse-shipping-method .panel-body').html(html);

                                    //$('#collapse-shipping-method').parent().find('.panel-heading .panel-title').html('<a href="#collapse-shipping-method" data-toggle="collapse" data-parent="#accordion" class="accordion-toggle"><?php echo $text_checkout_shipping_method; ?> <i class="fa fa-caret-down"></i></a>');

                                    //$('a[href=\'#collapse-shipping-method\']').trigger('click');

                                    $('#button-shipping-method').trigger('click');
                                    $('#collapse-payment-method').parent().find('.panel-heading .panel-title').html('<?php echo $text_checkout_payment_method; ?>');
                                    $('#collapse-checkout-confirm').parent().find('.panel-heading .panel-title').html('<?php echo $text_checkout_confirm; ?>');

                                    $.ajax({
                                        url: 'index.php?route=checkout/shipping_address',
                                        dataType: 'html',
                                        success: function(html) {
                                            $('#collapse-shipping-address .panel-body').html(html);
                                        },
                                        error: function(xhr, ajaxOptions, thrownError) {
                                            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                                        }
                                    });
                                },
                                error: function(xhr, ajaxOptions, thrownError) {
                                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                                }


                            });
                        }

                    <?php } else { ?>
                        $.ajax({
                            url: 'index.php?route=checkout/payment_method',
                            dataType: 'html',
                            success: function(html) {
                                $('#collapse-payment-method .panel-body').html(html);

                                //$('#collapse-payment-method').parent().find('.panel-heading .panel-title').html('<a href="#collapse-payment-method" data-toggle="collapse" data-parent="#accordion" class="accordion-toggle"><?php echo $text_checkout_payment_method; ?> <i class="fa fa-caret-down"></i></a>');

                                $('a[href=\'#collapse-payment-method\']').trigger('click');

                                $('#collapse-checkout-confirm').parent().find('.panel-heading .panel-title').html('<?php echo $text_checkout_confirm; ?>');
                            },
                            error: function(xhr, ajaxOptions, thrownError) {
                                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                            }
                        });
                    <?php } ?>

                    /*$.ajax({
                        url: 'index.php?route=checkout/payment_address',
                        data:{existing_check_ship_same_value,payment_check_ship_same_value},
                        dataType: 'html',
                        success: function(html) {
                            $('#collapse-payment-address .panel-body').html(html);
                        },
                        error: function(xhr, ajaxOptions, thrownError) {
                            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                        }
                    });*/
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });

    // Shipping Address
    $(document).delegate('#button-shipping-address', 'click', function() {
        $("body").scrollTop(0);
        var existing_check_ship_same_value = '';
        if(!$('#payment-existing #checkbox_same_ship_address').is(':checked')){
            existing_check_ship_same_value = $('#payment-existing #checkbox_same_ship_address').val();
        }else{
            existing_check_ship_same_value = $('#payment-existing #checkbox_same_ship_address').val();
        }
        var payment_check_ship_same_value = '';
        if(!$('#payment-new #checkbox_same_ship_address').is(':checked')){
            payment_check_ship_same_value = $('#payment-new #checkbox_same_ship_address').val();
        }else{
            payment_check_ship_same_value = $('#payment-new #checkbox_same_ship_address').val();
        }

        $.ajax({
            url: 'index.php?route=checkout/shipping_address/save',
            type: 'post',
            data: $('#collapse-shipping-address input[type=\'text\'], #collapse-shipping-address input[type=\'hidden\'], #collapse-shipping-address input[type=\'date\'], #collapse-shipping-address input[type=\'datetime-local\'], #collapse-shipping-address input[type=\'time\'], #collapse-shipping-address input[type=\'password\'], /*#collapse-shipping-address input[type=\'checkbox\']:checked,*/ #collapse-shipping-address input[type=\'radio\']:checked, #collapse-shipping-address textarea, #collapse-shipping-address select'),
            dataType: 'json',
            beforeSend: function() {
                //$('#button-shipping-address').button('loading');
                $('body').removeClass('loading').addClass('loading');
            },
            complete: function() {
                //$('#button-shipping-address').button('reset');
                setTimeout(function(){
                    $('body').removeClass('loading');
                }, 1000);
            },
            success: function(json) {
                $('.alert, .text-danger').remove();

                if (json['redirect']) {
                    location = json['redirect'];
                } else if (json['error']) {
                    if (json['error']['warning']) {
                        $('#collapse-shipping-address .panel-body').prepend('<div class="alert alert-warning">' + json['error']['warning'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                    }

                    for (i in json['error']) {
                        var element = $('#input-shipping-' + i.replace('_', '-'));

                        if ($(element).parent().hasClass('input-group')) {
                            $(element).parent().after('<div class="text-danger">' + json['error'][i] + '</div>');
                        } else {
                            $(element).after('<div class="text-danger">' + json['error'][i] + '</div>');
                        }
                    }

                    // Highlight any found errors
                    $('.text-danger').parent().parent().addClass('has-error');
                } else {
                    $('.account_billing_detail').hide();
                    $('.new_shipping_address').hide();
                    $('.checkout_payment_method').hide();
                    $('.checkout_payment_method').show();
                    $('.checkout_confirm').hide();


                    $('.checkout_step1').hide();
                    $('.checkout_step2').show();
                    $('.checkout_step3').hide();
                    $.ajax({
                        url: 'index.php?route=checkout/shipping_method',
                        dataType: 'html',
                        success: function(html) {
                            $('#collapse-shipping-method .panel-body').html(html);

                            //$('#collapse-shipping-method').parent().find('.panel-heading .panel-title').html('<a href="#collapse-shipping-method" data-toggle="collapse" data-parent="#accordion" class="accordion-toggle"><?php echo $text_checkout_shipping_method; ?> <i class="fa fa-caret-down"></i></a>');

                            //$('a[href=\'#collapse-shipping-method\']').trigger('click');

                            $('#button-shipping-method').trigger('click');
                            $('#collapse-payment-method').parent().find('.panel-heading .panel-title').html('<?php echo $text_checkout_payment_method; ?>');
                            $('#collapse-checkout-confirm').parent().find('.panel-heading .panel-title').html('<?php echo $text_checkout_confirm; ?>');

                            /*$.ajax({
                                url: 'index.php?route=checkout/shipping_address',
                                dataType: 'html',
                                success: function(html) {
                                    $('#collapse-shipping-address .panel-body').html(html);
                                },
                                error: function(xhr, ajaxOptions, thrownError) {
                                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                                }
                            });*/
                        },
                        error: function(xhr, ajaxOptions, thrownError) {
                            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                        }


                    });

                    /*$.ajax({
                        url: 'index.php?route=checkout/payment_address',
                        data:{existing_check_ship_same_value,payment_check_ship_same_value},
                        dataType: 'html',
                        success: function(html) {
                            $('#collapse-payment-address .panel-body').html(html);
                        },
                        error: function(xhr, ajaxOptions, thrownError) {
                            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                        }
                    });*/
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });

    // Guest
    $(document).delegate('#button-guest', 'click', function() {
        $.ajax({
            url: 'index.php?route=checkout/guest/save',
            type: 'post',
            data: $('#collapse-payment-address input[type=\'text\'], #collapse-payment-address input[type=\'date\'], #collapse-payment-address input[type=\'datetime-local\'], #collapse-payment-address input[type=\'time\'], #collapse-payment-address input[type=\'checkbox\']:checked, #collapse-payment-address input[type=\'radio\']:checked, #collapse-payment-address input[type=\'hidden\'], #collapse-payment-address textarea, #collapse-payment-address select'),
            dataType: 'json',
            beforeSend: function() {
                //$('#button-guest').button('loading');
                $('body').removeClass('loading').addClass('loading');
            },
            complete: function() {
                //$('#button-guest').button('reset');
                setTimeout(function(){
                    $('body').removeClass('loading');
                }, 1000);
            },
            success: function(json) {
                $('.alert, .text-danger').remove();

                if (json['redirect']) {
                    location = json['redirect'];
                } else if (json['error']) {
                    if (json['error']['warning']) {
                        $('#collapse-payment-address .panel-body').prepend('<div class="alert alert-warning">' + json['error']['warning'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                    }

                    for (i in json['error']) {
                        var element = $('#input-payment-' + i.replace('_', '-'));

                        if ($(element).parent().hasClass('input-group')) {
                            $(element).parent().after('<div class="text-danger">' + json['error'][i] + '</div>');
                        } else {
                            $(element).after('<div class="text-danger">' + json['error'][i] + '</div>');
                        }
                    }

                    // Highlight any found errors
                    $('.text-danger').parent().addClass('has-error');
                } else {
                <?php if ($shipping_required) { ?>
                        var shipping_address = $('#collapse-payment-address input[name=\'shipping_address\']:checked').prop('value');

                        if (shipping_address) {
                            $.ajax({
                                url: 'index.php?route=checkout/shipping_method',
                                dataType: 'html',
                                success: function(html) {
                                    // Add the shipping address
                                    $.ajax({
                                        url: 'index.php?route=checkout/guest_shipping',
                                        dataType: 'html',
                                        success: function(html) {
                                            $('#collapse-shipping-address .panel-body').html(html);

                                            $('#collapse-shipping-address').parent().find('.panel-heading .panel-title').html('<a href="#collapse-shipping-address" data-toggle="collapse" data-parent="#accordion" class="accordion-toggle"><?php echo $text_checkout_shipping_address; ?> <i class="fa fa-caret-down"></i></a>');
                                        },
                                        error: function(xhr, ajaxOptions, thrownError) {
                                            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                                        }
                                    });

                                    $('#collapse-shipping-method .panel-body').html(html);

                                    $('#collapse-shipping-method').parent().find('.panel-heading .panel-title').html('<a href="#collapse-shipping-method" data-toggle="collapse" data-parent="#accordion" class="accordion-toggle"><?php echo $text_checkout_shipping_method; ?> <i class="fa fa-caret-down"></i></a>');

                                    $('a[href=\'#collapse-shipping-method\']').trigger('click');

                                    $('#collapse-payment-method').parent().find('.panel-heading .panel-title').html('<?php echo $text_checkout_payment_method; ?>');
                                    $('#collapse-checkout-confirm').parent().find('.panel-heading .panel-title').html('<?php echo $text_checkout_confirm; ?>');
                                },
                                error: function(xhr, ajaxOptions, thrownError) {
                                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                                }
                            });
                        } else {
                            $.ajax({
                                url: 'index.php?route=checkout/guest_shipping',
                                dataType: 'html',
                                success: function(html) {
                                    $('#collapse-shipping-address .panel-body').html(html);

                                    $('#collapse-shipping-address').parent().find('.panel-heading .panel-title').html('<a href="#collapse-shipping-address" data-toggle="collapse" data-parent="#accordion" class="accordion-toggle"><?php echo $text_checkout_shipping_address; ?> <i class="fa fa-caret-down"></i></a>');

                                    $('a[href=\'#collapse-shipping-address\']').trigger('click');

                                    $('#collapse-shipping-method').parent().find('.panel-heading .panel-title').html('<?php echo $text_checkout_shipping_method; ?>');
                                    $('#collapse-payment-method').parent().find('.panel-heading .panel-title').html('<?php echo $text_checkout_payment_method; ?>');
                                    $('#collapse-checkout-confirm').parent().find('.panel-heading .panel-title').html('<?php echo $text_checkout_confirm; ?>');
                                },
                                error: function(xhr, ajaxOptions, thrownError) {
                                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                                }
                            });
                        }
                    <?php } else { ?>
                        $.ajax({
                            url: 'index.php?route=checkout/payment_method',
                            dataType: 'html',
                            success: function(html) {
                                $('#collapse-payment-method .panel-body').html(html);

                                $('#collapse-payment-method').parent().find('.panel-heading .panel-title').html('<a href="#collapse-payment-method" data-toggle="collapse" data-parent="#accordion" class="accordion-toggle"><?php echo $text_checkout_payment_method; ?> <i class="fa fa-caret-down"></i></a>');

                                $('a[href=\'#collapse-payment-method\']').trigger('click');

                                $('#collapse-checkout-confirm').parent().find('.panel-heading .panel-title').html('<?php echo $text_checkout_confirm; ?>');
                            },
                            error: function(xhr, ajaxOptions, thrownError) {
                                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                            }
                        });
                    <?php } ?>
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });

    // Guest Shipping
    $(document).delegate('#button-guest-shipping', 'click', function() {
        $.ajax({
            url: 'index.php?route=checkout/guest_shipping/save',
            type: 'post',
            data: $('#collapse-shipping-address input[type=\'text\'], #collapse-shipping-address input[type=\'date\'], #collapse-shipping-address input[type=\'datetime-local\'], #collapse-shipping-address input[type=\'time\'], #collapse-shipping-address input[type=\'password\'], #collapse-shipping-address input[type=\'checkbox\']:checked, #collapse-shipping-address input[type=\'radio\']:checked, #collapse-shipping-address textarea, #collapse-shipping-address select'),
            dataType: 'json',
            beforeSend: function() {
                //$('#button-guest-shipping').button('loading');
                $('body').removeClass('loading').addClass('loading');
            },
            complete: function() {
                //$('#button-guest-shipping').button('reset');
                setTimeout(function(){
                    $('body').removeClass('loading');
                }, 1000);
            },
            success: function(json) {
                $('.alert, .text-danger').remove();

                if (json['redirect']) {
                    location = json['redirect'];
                } else if (json['error']) {
                    if (json['error']['warning']) {
                        $('#collapse-shipping-address .panel-body').prepend('<div class="alert alert-warning">' + json['error']['warning'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                    }

                    for (i in json['error']) {
                        var element = $('#input-shipping-' + i.replace('_', '-'));

                        if ($(element).parent().hasClass('input-group')) {
                            $(element).parent().after('<div class="text-danger">' + json['error'][i] + '</div>');
                        } else {
                            $(element).after('<div class="text-danger">' + json['error'][i] + '</div>');
                        }
                    }

                    // Highlight any found errors
                    $('.text-danger').parent().addClass('has-error');
                } else {
                    $.ajax({
                        url: 'index.php?route=checkout/shipping_method',
                        dataType: 'html',
                        success: function(html) {
                            $('#collapse-shipping-method .panel-body').html(html);

                            $('#collapse-shipping-method').parent().find('.panel-heading .panel-title').html('<a href="#collapse-shipping-method" data-toggle="collapse" data-parent="#accordion" class="accordion-toggle"><?php echo $text_checkout_shipping_method; ?> <i class="fa fa-caret-down"></i>');

                            $('a[href=\'#collapse-shipping-method\']').trigger('click');

                            $('#collapse-payment-method').parent().find('.panel-heading .panel-title').html('<?php echo $text_checkout_payment_method; ?>');
                            $('#collapse-checkout-confirm').parent().find('.panel-heading .panel-title').html('<?php echo $text_checkout_confirm; ?>');
                        },
                        error: function(xhr, ajaxOptions, thrownError) {
                            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                        }
                    });
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });

    // Shipping method
    $(document).delegate('#button-shipping-method', 'click', function() {
        $("body").scrollTop(0);
        $.ajax({
            url: 'index.php?route=checkout/shipping_method/save',
            type: 'post',
            data: $('#collapse-shipping-method input[type=\'radio\']:checked, #collapse-shipping-method textarea'),
            dataType: 'json',
            beforeSend: function() {
                //$('#button-shipping-method').button('loading');
                $('body').removeClass('loading').addClass('loading');
            },
            complete: function() {
                //$('#button-shipping-method').button('reset');
                setTimeout(function(){
                    $('body').removeClass('loading');
                }, 1000);
            },
            success: function(json) {
                $('.alert, .text-danger').remove();

                if (json['redirect']) {
                    location = json['redirect'];
                } else if (json['error']) {
                    if (json['error']['warning']) {
                        $('#collapse-shipping-method .panel-body').prepend('<div class="alert alert-warning">' + json['error']['warning'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                    }
                } else {
                    $.ajax({
                        url: 'index.php?route=checkout/payment_method',
                        dataType: 'html',
                        success: function(html) {
                            $('#collapse-payment-method .panel-body').html(html);

                            //$('#collapse-payment-method').parent().find('.panel-heading .panel-title').html('<a href="#collapse-payment-method" data-toggle="collapse" data-parent="#accordion" class="accordion-toggle"><?php echo $text_checkout_payment_method; ?> <i class="fa fa-caret-down"></i></a>');

                            $('a[href=\'#collapse-payment-method\']').trigger('click');

                            $('#collapse-checkout-confirm').parent().find('.panel-heading .panel-title').html('<?php echo $text_checkout_confirm; ?>');
                        },
                        error: function(xhr, ajaxOptions, thrownError) {
                            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                        }
                    });
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });

    // Payment Method
    $(document).delegate('#button-payment-method', 'click', function() {
        $("body").scrollTop(0);
        $.ajax({
            url: 'index.php?route=checkout/payment_method/save',
            type: 'post',
            data: $('#collapse-payment-method input[type=\'radio\']:checked, #collapse-payment-method input[type=\'checkbox\']:checked, #collapse-payment-method textarea, #collapse-payment-method input[type=\'hidden\']'),
            dataType: 'json',
            beforeSend: function() {
                //$('#button-payment-method').button('loading');
                $('body').removeClass('loading').addClass('loading');
            },
            complete: function() {
                //$('#button-payment-method').button('reset');
                setTimeout(function(){
                    $('body').removeClass('loading');
                }, 1000);
            },
            success: function(json) {
                $('.alert, .text-danger').remove();

                if (json['redirect']) {
                    location = json['redirect'];
                } else if (json['error']) {
                    if (json['error']['warning']) {
                        $('#collapse-payment-method .panel-body').prepend('<div class="alert alert-warning">' + json['error']['warning'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                    }
                } else {
                    $('.checkout_step1').hide();
                    $('.checkout_step2').hide();
                    $('.checkout_step3').show();
                    $.ajax({
                        url: 'index.php?route=checkout/confirm',
                        dataType: 'html',
                        success: function(html) {
                            $('#collapse-checkout-confirm .panel-body').html(html);
                            //$('#collapse-checkout-confirm').parent().find('.panel-heading .panel-title').html('<a href="#collapse-checkout-confirm" data-toggle="collapse" data-parent="#accordion" class="accordion-toggle"><?php echo $text_checkout_confirm; ?> <i class="fa fa-caret-down"></i></a>');

                            $('a[href=\'#collapse-checkout-confirm\']').trigger('click');
                        },
                        error: function(xhr, ajaxOptions, thrownError) {
                            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                        }
                    });
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });
    $(document).ready(function(){
        parent.$.fancybox.close();
    });

    //--></script>

<script>
    $(document).ready(function(){
        $.ajax({
            url: 'index.php?route=checkout/order_summary&ajax=true',
            dataType: 'html',
            success: function(html) {
                $('.order-list').html(html);
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });
</script>
<script>
    $(document).ready(function(){
        $('.account_billing_detail').hide();
        $('.new_shipping_address').hide();
        $('.checkout_payment_method').hide();
        $('.checkout_confirm').hide();
    });
</script>
<?php echo $footer; ?>