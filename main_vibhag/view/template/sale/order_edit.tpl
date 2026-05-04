<?php echo $header; ?><?php echo $column_left; ?>

<div id="content">
    <div class="page-header">
        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>" />
        <div class="container-fluid order_detail_page">

            <div class="pull-left">
                <h1>Edit Order</h1>
                <ul class="breadcrumb">
                    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                    <?php } ?>
                </ul>
            </div>
        </div>
        <div class="container-fluid order_detail_page">    
            <div class="panel panel-default">
                <div class="container-fluid">
                    <br>
                    <div class="row">
                        <div class="col-lg-12">
                            <table class="table table-bordered">
                                <tr>
                                    <td><h4><b>Order No: </b><?php echo $order_no; ?></h4></td>
                                    <td><h4><b>Suborder No: </b><a href="<?php echo $suborder_info_link; ?>" target="_blank"><?php echo $suborder_id; ?></a></h4></td>

                                </tr>
                            </table>
                        </div>
                    </div>
                    <br>
                    <br>
                    <div class="row">
                        <?php
                        if (!empty($session_succ_msg)) {
                            ?>
                            <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $session_succ_msg; ?><button type="button" class="close" data-dismiss="alert">×</button></div>
                            <?php
                        } else if (!empty($session_err_msg)) {
                            ?>
                            <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $session_err_msg; ?><button type="button" class="close" data-dismiss="alert">×</button></div>    
                            <?php
                        }
                        ?>

                        <div class="col-lg-6">
                            <h3>What do you want to edit</h2>
                        </div>
                        <div class="col-lg-6">
                            <select class="form-control" onchange="toggleTabls(this)">
                                <option>SELECT EDIT TYPE</option>
                                <?php
                                foreach ($order_edit_function as $key => $value) {
                                    ?>
                                    <option value="<?php echo $key; ?>" ><?php echo $value['name']; ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <br>
                    <br>
                    <?php
                    foreach ($order_edit_function as $key => $value) {
                        echo $value['function_work']; 
                    }
                    ?>
                    <br>
                    <br>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade my_model" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Confirm Changes</h4>
            </div>
            <div class="modal-body">
                <div class="confirm_products">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <td>Sr No.</td>
                                <td>Model</td>
                                <td>Total Pieces</td>
                                <td>Set Description</td>
                                <td>Price Per Piece</td>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <p><b>Note: </b>If you want to see old values, Move the pointer on the <i class="fa fa-info-circle fa-lg"></i>.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" form="product_form" class="btn btn-primary">Save changes</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="mark_duplicate_popup" class="modal fade" role="dialog"><?php echo $duplicate_popup;?></div>
<style>
    #product_form tr{
        transition: background 2s;
        -webkit-transition: background 2s;
        -moz-transition: background 2s;
    }
    #product_form tr.delete_this_row{
        background: rgba(245, 107, 107, 0.39);
    }
    .error, .error:hover, .error:active{
        border: solid 1px rgba(242, 69, 69, 0.44);
    }
    table tr, td, th{
        width: auto!important;
    }
</style>
<script type="text/javascript">

    $('.payment_code').change(function () {
        let old = $('input[name="payment_method[old]"]').val();

        if ((old == 'cod' || old == 'credit') && $(this).val() == 'bank_transfer') {
            $(this).parent().find('.checkbox').removeClass('hidden');
        } else {
            $(this).parent().find('.checkbox').addClass('hidden');
        }

        if (old != $(this).val() && $(this).val().trim() != '') {
            $(this).parent().find('.submit').removeClass('hidden');
        } else {
            $(this).parent().find('.submit').addClass('hidden');
        }
    });


    $('.stock-transfer').click(function () {

        if (confirm("Do you really want to Store Tranfer this order?")) {
            return true;
        } else {
            return false;
        }

    });

    $('.remove-btns').click(function () {
        var txt_data = $(this).attr('data-txt');
        var vali_data = $(this).attr('data-vali');
        var data_id_check = $(this).attr('data-id-check');
        var check_his = false;
        $('.remove_his').each(function (index, value) {
            if ($(value).is(':checked') == true) {
                check_his = true;
            }
        });

        if (check_his == true) {
            var removeComment = $('#remove-comment_'+data_id_check).val()
            if(removeComment.trim() == '') {
                alert("Please enter the comment.");
                return false;
            }
            if (confirm("Do you really want to edit this/these "+txt_data+"?")) {
                return true;
            } else {
                return false;
            }
        } else {
            alert("Please select alteast one "+vali_data+" for edit.");
            return false;
        }


    });


    $('.check-credits').click(function (e) {

        e.preventDefault(); //prevent form submit when button is clicked

        var order_id = $('#payment_method_order_id').val();
        var credit_check = '<?php echo $credit_check;?>';

        $.ajax({
                type: 'GET',
                url: 'index.php?route=sale/edit_order/check_payments&token=<?php echo $token; ?>&order_id='+order_id,
                async: false,
                dataType: 'json',
                success:function(response){
                  
                  if(response == 1) {
                    
                    alert('There are still some WSB Credit payment entries in the order. Please Cancel them first.');
                    
                    return false;

                  }else{

                    if(credit_check == 1) {
                        
                        if (confirm("Some of the suborder buyer invoices are already generated. Do you still want to edit this order?")) {
                            $("#change_payment_method").submit();
                            return true
                        } else {
                            return false;
                        }

                    }else{

                        $("#change_payment_method").submit();
                    }
                    
                  }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                 var string = xhr.responseText.replace(/<b>/g,'');
                     string = string.replace(/<\/b>/g,'');
                     alert("Error occure during request processing ::\n"+string);
                }
        });
        
    });

    $('.shipping_code').change(function () {
        let old = $('input[name="shipping_code[old]"]').val();
        $('input[name="shipping_method[new]"]').val($(".shipping_code option:selected").text());
        $('.btn_ess_cancel').addClass('hidden');
        $('.input_ess_buttons').addClass('hidden');
        $('.input_ess_charges').addClass('hidden');
        $('.click_ess_charges').removeClass('hidden');
        $('input[name=apply_ess_charges]').prop('checked', false);
        $('input[name=ess_charges]').val('');


        if (old != $(this).val() && $(this).val().trim() != '') {
            $(this).parent().find('.submit').removeClass('hidden');
            $(this).parent().find('.btn-refresh').addClass('hidden');
        } else {
            $(this).parent().find('.submit').addClass('hidden');
            $(this).parent().find('.btn-refresh').removeClass('hidden');
        }
    })
    function toggleTabls(obj) {
        var class_name = $(obj).val();
        $('.tabs').addClass('hidden');
        $('.' + class_name).removeClass('hidden');

        if (class_name == 'edit_shipping') {
            var data_arr = "zone_id=<?php echo $shipping_methods['zone_id']; ?>&country_id= <?php echo $shipping_methods['country_id']; ?>&postcode=<?php echo $shipping_methods['postcode']; ?>&is_dropshipper=<?php echo $shipping_methods['is_dropshipper']; ?>&cartlimitcross=<?php echo $shipping_methods['cartlimitcross']; ?>&weight_cart=<?php echo $shipping_methods['weight_cart']; ?>";

            $('.shipping_code option').remove();
            $('<option>', {
                value: '',
                text: 'SELECT SHIPPING METHOD'
            }).appendTo('.shipping_code');
            $('<option>', {
                value: 'topay',
                text: 'TO PAY COURIER (₹ 0)'
            }).appendTo('.shipping_code');
            $('<option>', {
                value: 'topay_with_shipping',
                text: 'TO PAY COURIER (₹ 200)'
            }).appendTo('.shipping_code');
            $('<option>', {
                value: 'store_pickup',
                text: 'STORE PICKUP (₹ 0)'
            }).appendTo('.shipping_code');
            $('<option>', {
                value: 'free_shipping',
                text: 'FREE SHIPPING (₹ 0)'
            }).appendTo('.shipping_code');
            $('<option>', {
                value: 'warehouse_pickup',
                text: 'SELF PICKUP FROM WAREHOUSE (₹ 0)'
            }).appendTo('.shipping_code');
            $('<option>', {
                value: 'udaan_shipping',
                text: 'UDAAN SHIPPING (₹ 0)'
            }).appendTo('.shipping_code');
            $.ajax({
                type: "POST",
                data: data_arr,
                dataType: 'json',
                url: '<?php echo HTTPS_CATALOG; ?>api/checkout/shippingMethod',
                success: function (json) {
                    //console.log(json['shipping_method']['weight']['quote']);
                    $.each(json['shipping_method']['weight']['quote'], function (key, value) {
                        //console.log(value);
                        $('<option>', {
                            value: value['code'],
                            text: value['title'].toUpperCase() + " (" + htmlDecode(value['text']) + ")"
                        }).appendTo('.shipping_code');
                    });
                    $('.shipping_code').val('<?php echo $shipping_code; ?>');
                    $('input[name="shipping_method[new]"]').val($(".shipping_code option:selected").text());
                }
            });
        }
        if (class_name == 'edit_address_ship_pay') {
            order_id = '<?php echo $order_id; ?>';
            suborder_id = '<?php echo $suborder_id; ?>';
            $.ajax({
                type: 'post',
                url: 'index.php?route=sale/edit_order/getInformationByOrderId&token=<?php echo $token; ?>',
                data: {'order_id': order_id, 'suborder_id': suborder_id},
                success: function (data) {
                    $('.edit_address_detail_popup').html(data);
                    $('.edit_address_detail_popup').show();
                }
            });
        }

        if (class_name == 'edit_order_customer') {
            order_id = '<?php echo $order_id; ?>';
            suborder_id = '<?php echo $suborder_id; ?>';

            $('#mark_duplicate_popup').modal('show');
            
            var customerid = $(this).data('customerid');               
            $('#set_marked_as_master').data('ordercustomerid',customerid);
        }
        
    }
    function deleteProduct(obj, id) {
        $(obj).parents('tr').find('.edit_type_td select').prop('disabled', false);
        let edit_type = $(obj).parents('tr').find('.edit_type').val();
        let comment_rqd_txt = $(obj).parents('tr').find('.comment-rqd').val();
        if (edit_type != '') {
            $(obj).parents('tr').find('.edit_type').removeClass('error');
            $(obj).parents('tr').find('.action_row > textarea').removeClass('error');
            let ack = confirm('Do you want to delete this item..??');
            if (ack) {
                $(id).prop('checked', true);
                $(obj).siblings('a').click();
                $(obj).parents('tr').find('.edit_type').prop('disabled', false);
                $(obj).parents('tr').find('.edit_type').val(edit_type);
                $(obj).parents('tr').find('.comment-rqd').val(comment_rqd_txt);
                $(obj).parents('tr').find('.action_row > textarea').removeClass('hidden');
                //$(obj).parents('tr').find('.edit_type').prop('disabled',true);
                $(obj).parents('tr').find('.reset_btn').removeClass('hidden');
                $(id).parents('tr').addClass('delete_this_row');
                $(obj).addClass('hidden');

            }
        } else {
            $(obj).parents('tr').find('.edit_type').addClass('error');
            $(obj).parents('tr').find('.action_row > textarea').removeClass('hidden');
            $(obj).parents('tr').find('.action_row > textarea').addClass('error');
            alert('Select Edit Type and delete it again.');
        }

    }

    function resetProduct(obj, id) {
        let editable = $(obj).parents('tr').find('.editable');
        $(editable).each(function (ind, elem) {
            $(elem).find('input').val($(elem).find('input').data('old'));
            $(elem).find('textarea').val($(elem).find('textarea').data('old'));
            $(elem).find('input,textarea').addClass('hidden');
            $(elem).find('span').removeClass('hidden');
        });
        $(obj).parents('tr').find('.action_row > textarea').val('');
        $(obj).parents('tr').removeClass('delete_this_row');
        $(obj).parents('tr').find('.action_row > textarea').addClass('hidden');
        $(obj).parents('tr').find('.edit_type_td select').prop('disabled', true);
        $(obj).parents('tr').find('.edit_type_td select').val('');
        $(obj).parents('tr').find('.edit_type').val('');
        $(obj).parents('tr').find('.edit_type').prop('disabled', true);
        $(obj).parents('tr').find('.delete_btn').removeClass('hidden');
        //$(this).parents('tr').find('.edit_type_td select option[value=""]').attr('selected',true);
        $(obj).addClass('hidden');
        $(obj).parents('tr').removeClass('open');
    }

    $('.editable').dblclick(function () {
        $(this).parents('tr').addClass('open');
        $(this).find('input,textarea').removeClass('hidden');
        $(this).find('input,textarea').attr('disabled', false);
        $(this).find('span').addClass('hidden');
        $(this).parents('tr').find('.edit_type_td select').prop('disabled', false);
        $(this).parents('tr').find('.action_row > textarea').removeClass('hidden');
        $(this).parents('tr').find('.action_row > span').addClass('hidden');
        $(this).parents('tr').find('.reset_btn').removeClass('hidden');
    });

    $('.editable input, .editable textarea').on('change', function () {
        if ($(this).data('old') != $(this).val()) {
            $(this).addClass('changed');
        } else {
            $(this).removeClass('changed');
        }
    });

    function validateChanges(form) {
        $(form).find('tr.open').each(function () {
            $(this).find('.required').each(function (i, e) {
                let val = $(this).val();
                if (val.trim().length == 0) {
                    $(this).addClass('error');
                } else {
                    $(this).removeClass('error');
                }
            });
        });
        if (($(form).find('.changed').length == 0 || $(form).find('tr.open').length == 0) && $(form).find('.delete_this_row').length == 0) {
            alert('No change found.');
            return false;
        }
        if ($(form).find('.error').length > 0) {
            alert('Fill the required Field.');
            return false;
        }
        $('.confirm_products tbody').html('');
        $(form).find('tr.open, .delete_this_row').each(function (ind, element) {
            let row = '<tr class="bg-danger">';
            row += '<td>' + (ind + 1) + '</td>';
            row += '<td>' + $(this).find('td').eq(1).text() + '</td>';
            row += '<td>';
            row += $(this).find('td').eq(2).find('input').val();
            row += '<i class="fa fa-info-circle pull-right fa-lg" data-toggle="tooltip" data-placement="top" title="Old Value: ' + $(this).find('td').eq(2).find('input').attr('data-old') + '"></i>';
            row += '</td>';
            row += '<td>';
            row += $(this).find('td').eq(4).find('textarea').val();
            row += '<i class="fa fa-info-circle pull-right fa-lg" data-toggle="tooltip" data-placement="top" title="Old Value: ' + $(this).find('td').eq(4).find('textarea').attr('data-old') + '"></i>';
            row += '</td>';
            row += '<td>' + $(this).find('td').eq(3).find('span').text() + '</td>';
            row += '</tr>';
            $('.confirm_products tbody').append(row);
        });
        $('.my_model').modal('show');
    }
</script>
<?php echo $footer; ?>

<script>
    $(document).ready(function () {
        $('input[name=apply_ess_charges]').click(function () {
            $('.click_ess_charges').addClass('hidden');
            $('.input_ess_charges').removeClass('hidden');
            $('.btn-refresh').addClass('hidden');
            $('.input_ess_buttons').removeClass('hidden');
            $('.btn_ess_cancel').removeClass('hidden');
        });

        $('.btn_ess_cancel').click(function () {
            $('.input_ess_buttons').addClass('hidden');
            $('.click_ess_charges').removeClass('hidden');
            $('.input_ess_charges').addClass('hidden');
            $('.btn-refresh').removeClass('hidden');
            $('input[name=apply_ess_charges]').prop('checked', false);
            //$('.input_ess_buttons').removeClass('hidden');
        });
    });

    //get duplicate customer record
    $("body").delegate("#get_duplicate_record", "click", function(){    
        
        $('#set_marked_as_master').hide();

        var customer_name       = $('input[name=\'customer_name\']').val();
        var customer_email      = $('input[name=\'customer_email\']').val();
        var customer_phone      = $('input[name=\'customer_phone\']').val();
        var customer_city       = $('input[name=\'customer_city\']').val();
        var company_name        = $('input[name=\'company_name\']').val();
        var customer_cid        = $('input[name=\'customer_cid\']').val();
        var customer_postcode   = $('input[name=\'customer_postcode\']').val();
        var customer_id          = $('#set_marked_as_master').data('ordercustomerid');
        var change_customer_flag = 1;
        var order_id = '<?php echo $order_id; ?>';
        var suborder_id = '<?php echo $suborder_id; ?>';
        
        if (customer_name=='' && customer_email=='' && customer_phone=='' && customer_city=='' && company_name=='' && customer_cid=='' && customer_postcode=='') {
            alert('Please fill atleast one value in search form.');
            return false;
        }

        $.ajax({
            url: 'index.php?route=sale/order/getCustomers&token=<?php echo $token; ?>&customer_name=' + customer_name +'&customer_email=' + customer_email + '&customer_phone=' + customer_phone + '&customer_city=' + customer_city + '&company_name=' + company_name + '&customer_cid=' + customer_cid + '&customer_postcode=' + customer_postcode + '&customer_id=' + customer_id + '&change_customer_flag=' + change_customer_flag + '&order_id=' + order_id + '&suborder_id=' + suborder_id,
            beforeSend: function(){
                $('#get_record_loading').removeClass('hide');
            },
            complete: function() {
                
                $('#get_record_loading').addClass('hide');  
            },
            success: function(json) {
                console.log(json);
                if (json['error']) {
                    alert(json['error']);
                }else{
                    $('#duplicate_customer_search_modal').html(''); 
                    if ($.trim(json)!='') {                        
                        $('#duplicate_customer_search_modal').html(json);                        
                    }else{
                        alert('Record not found, please change your filters');
                    }
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        }); 
    });
    $("body").delegate(".change_customer_edit_order", "click", function(){    
        var cur_customer_id = $(this).val();
        $('.change_customer_for_edit').addClass('hidden');
        $('.change_customer_for_edit input[type=checkbox]').prop('checked',false);
        $('.customer_edit_'+cur_customer_id).removeClass('hidden');
    });
    
</script>