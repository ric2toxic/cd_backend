<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <a href="javascript:void(0);" data-toggle="tooltip" title="Generate shipping Label" class="btn btn-info" onclick="$('#form_shipment_info').submit();"><i class="fa fa-barcode"></i></a>
                <!--<button type="submit" form="form-custom-field" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>-->
                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
            </div>
            <h1><?php echo $heading_title; ?></h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="container-fluid shipment_label">
        <?php if ($error_warning) { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?></h3>
            </div>
            <div class="panel-body">
                <form action="<?php echo $shipping_label; ?>" method="post" enctype="multipart/form-data" id="form_shipment_info" class="form-horizontal">
                    <div class="row">
                        <div class="col-sm-6">
                            <fieldset>
                                <legend><?php echo $text_courier; ?></legend>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label" for="input-courier"><?php echo $entry_courier; ?></label>
                                    <div class="col-sm-9">
                                        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>" >
                                        <select name="courier" id="input-courier" class="form-control">
                                            <option value=""><?php echo $entry_select; ?></option>
                                            <?php
                                                if(isset($couriers)){
                                                    foreach($couriers as $courier){
                                                        if($courier['id'] == $data_courier){ $selected = 'selected';}else{ $selected = '';}
                                            ?>
                                                        <option value="<?php echo $courier['id']; ?>" <?php echo $selected; ?>><?php echo $courier['name']; ?></option>
                                            <?php
                                                    }
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label" for="input-docket"><?php echo $entry_docket; ?></label>
                                    <div class="col-sm-9">
                                        <input type="text" name="docket_no" id="input-docket" class="form-control" placeholder="<?php echo $entry_docket; ?>" value="<?php echo $data_docket_no; ?>" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label" for="input-weight"><?php echo $entry_weight; ?> </label>
                                    <div class="col-sm-9">
                                        <input type="text" name="weight" id="input-weight" class="form-control" placeholder="<?php echo $entry_weight; ?>" value="<?php echo $data_weight; ?>"/>
                                    </div>
                                </div>
                                <!--<button type="button" name="" class="btn btn-primary pull-right">Next</button>-->
                            </fieldset>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <fieldset>
                                <legend><?php echo $text_company_address; ?></legend>
                                <div class="form-group">
                                    <div class="col-sm-4">
                                        <input type="text" name="filter_warehouse_name" class="form-control" placeholder="<?php echo $entry_search_warehouse; ?>" value="<?php echo $search_warehouse; ?>" />
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text" name="filter_city" class="form-control" placeholder="<?php echo $entry_search_city; ?>" value="<?php echo $search_city; ?>"/>
                                    </div>
                                    <div class="col-sm-3">
                                        <button type="button" name="search" class="btn btn-primary" id="button-search"><?php echo $button_search; ?></button>
                                        <div class="pull-right">
                                            <button type="button" class="btn btn-primary address_save_button"><i class="fa fa-plus"></i> </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="address_form_list">
                                    <?php
                                        if(isset($company_addresses)){
                                            $i = 1;
                                            foreach($company_addresses as $address){
                                    ?>
                                    <div class="col-sm-3">
                                        <div class="addresses_list">
                                                <?php echo $address['warehouse_name']; ?>, <br/>
                                                <?php echo $address['address_1']; ?>, <?php echo $address['address_2']; ?>, <br/>
                                                <?php echo $address['city']; ?> - <?php echo $address['postcode']; ?> <br/>
                                                <?php echo $address['state']; ?>, <?php echo $address['country']; ?> <br/>
                                                <?php echo $address['telephone']; ?>
                                        </div>
                                        <div class="address_select">
                                            <input type="radio" id="input-address-<?php echo $address['warehouse_id'];?>" class="" name="select_address" value="<?php echo $address['warehouse_id']; ?>" />
                                            <label class="" for="input-address-<?php echo $address['warehouse_id'];?>" ><?php echo $address['state']; ?>, <?php echo $address['country']; ?></label>
                                        </div>
                                    </div>
                                    <?php $i++; } } ?>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </form>
                <form action="<?php echo $action_save_address; ?>" method="post" enctype="multipart/form-data" id="form-warehouse-address" class="form-horizontal">
                    <div class="address_form">
                        <div class="col-sm-12">
                            <button type="button" name="close" id="address_form_button" class="address_form_button"><i class="fa fa-times"></i></button>
                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="input-sort-order"><?php echo $entry_company_name;?></label>
                                <div class="col-sm-9">
                                    <input type="text" name="warehouse_name" value="" placeholder="<?php echo $entry_company_name; ?>" id="input-sort-order" class="form-control" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="input-sort-order"><?php echo $entry_address_1; ?></label>
                                <div class="col-sm-9">
                                    <input type="text" name="address_1" value="" placeholder="<?php echo $entry_address_1; ?>" id="input-sort-order" class="form-control" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="input-sort-order"><?php echo $entry_address_2; ?></label>
                                <div class="col-sm-9">
                                    <input type="text" name="address_2" value="" placeholder="<?php echo $entry_address_2; ?>" id="input-sort-order" class="form-control" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="input-sort-order"><?php echo $entry_city; ?></label>
                                <div class="col-sm-9">
                                    <input type="text" name="city" value="" placeholder="<?php echo $entry_city; ?>" id="input-sort-order" class="form-control" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="input-sort-order"><?php echo $entry_postcode; ?></label>
                                <div class="col-sm-9">
                                    <input type="text" name="postcode" value="" placeholder="<?php echo $entry_postcode; ?>" id="input-sort-order" class="form-control" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="input-sort-order"><?php echo $entry_country; ?></label>
                                <div class="col-sm-9">
                                    <select name="country" class="form-control" >
                                        <option value=""><?php echo $entry_select; ?></option>
                                        <?php
                                                            if(isset($countries)){
                                                                foreach($countries as $country){
                                                        ?>
                                        <option value="<?php echo $country['country_id']; ?>" ><?php echo $country['name'];?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="input-sort-order"><?php echo $entry_state; ?></label>
                                <div class="col-sm-9">
                                    <select name="state" class="form-control"></select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="input-sort-order"><?php echo $entry_phone_no; ?></label>
                                <div class="col-sm-9">
                                    <input type="text" name="telephone" value="" placeholder="<?php echo $entry_phone_no; ?>" id="input-sort-order" class="form-control" />
                                </div>
                            </div>
                            <div class="pull-right">
                                <input type="submit" name="save" value="<?php echo $button_address_save; ?>" >
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php echo $footer; ?>

<script type="text/javascript">
    $(document).ready(function(){
        $('#address_form_button').click(function(){
            $('.address_form').hide();
            window.location.reload();
        });

        $('.address_save_button').click(function(){
            $('.address_form').show();
        });

        // $('#button-search').click(function(){
        //    var url = 'index.php?route=sale/shipment_label_information&token=<?php echo $token;?>';
        //
        //     // order id is hidden value
        //     var order_id = $("input[name=\'order_id\']").val();
        //     if(order_id){
        //         url += '&order_id=' + encodeURIComponent(order_id);
        //     }
        //
        //     var filter_warehouse = $("input[name=\'filter_warehouse_name\']").val();
        //     if(filter_warehouse){
        //         url += '&filter_warehouse=' + encodeURIComponent(filter_warehouse);
        //     }
        //
        //     var filter_city = $("input[name=\'filter_city\']").val();
        //     if(filter_city){
        //         url += '&filter_city=' + encodeURIComponent(filter_city);
        //     }
        //
        //     location = url;
        // });

        $('#button-search').click(function () {
          $('.address_form_list').empty();
          $.ajax({
            url : 'index.php?route=sale/order/getSearchAddress&token=<?php echo $token;?>',
            type: 'POST',
            dataType: 'json',
            data: '&filter_warehouse=' + encodeURIComponent($('input[name=\'filter_warehouse_name\']').val()) +
                   '&filter_city=' + encodeURIComponent($('input[name=\'filter_city\']').val()),
            beforeSend: function() {
              $('#button-search').button('loading');
            },
            complete: function() {
              $('#button-search').button('reset');
            },
            success: function(json) {
              $.each(json, function(index,element){

                var warehouse_address1 = '';
                var warehouse_address2 = '';
                if(element.address_1 != ''){
                  warehouse_address1 = element.address_1 + '<br>';
                }
                if(element.address_2 != ''){
                  warehouse_address2 = element.address_2 + ' <br>';
                }

                $('.address_form_list').append(
                    '<div class="col-sm-3">' +
                        '<div class="addresses_list  warehouse_address_height_set" id="warehouse_id_'+element.warehouse_id+'" data-address-id="'+element.warehouse_id+'">'
                                              + element.warehouse_name +'<br>'
                                              + warehouse_address1
                                              + warehouse_address2
                                              + element.city +'-'
                                              + element.postcode +'<br>'
                                              + element.zone_name +'-'
                                              + element.country_name+'<br>'
                                              + element.telephone
                        +'</div>'
                        +'<div class="address_select">' +
                          '<input type="radio" id="input-address-'+element.warehouse_id+'" class="" name="select_warehouse_id" value="'+element.warehouse_id+'" />' +
                          '<label class="" for="input-address-'+element.warehouse_id+'" >'+element.zone_name+' , '+ element.country_name +'</label>' +
                          '<input type="hidden" id="input-gati-vendor-'+element.warehouse_id+'" class="" name="gati_vendor_code" value="'+element.gati_vendor_code+'" />' +
                        '</div>' +
                    '</div>'
                );
              });
            }
          });
        });

        $("select[name='country']").on('change', function() {
            $.ajax({
                url: 'index.php?route=sale/customer/country&token=<?php echo $token; ?>&country_id=' + $(this).val(),
                dataType: 'json',
                beforeSend: function() {
                    $("select[name='seller[country]']").after('<i class="fa fa-circle-o-notch fa-spin"></i>');
                },
                complete: function() {
                    $('.fa-spin').remove();
                },
                success: function(json) {
                    html = '<option value=""><?php echo $entry_select; ?></option>';

                    if (json['zone']) {
                        for (i = 0; i < json['zone'].length; i++) {
                            html += '<option value="' + json['zone'][i]['zone_id'] + '"';
                            html += '>' + json['zone'][i]['name'] + '</option>';
                        }
                    }
                    $("select[name='state']").html(html);
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }


            });
        }).trigger('change');
    });
</script>
<script type="text/javascript">
    $('input[name=\'filter_warehouse_name\'], input[name=\'filter_city\']').on('keypress',function(e){
        if (e.keyCode == 13) {
            $('#button-search').trigger('click');
        }
    });

    $('input[name=\'save\']').click(function(e){
        $('.error').hide();
        if($('input[name=\'warehouse_name\']').val() ==''){
            $('input[name=\'warehouse_name\']').after('<div class="error">Please enter your warehouse name !</div>');
            $('input[name=\'warehouse_name\']').focus();
            return false;
        }
        if($('input[name=\'address_1\']').val() ==''){
            $('input[name=\'address_1\']').after('<div class="error">Please enter warehouse address 1 !</div>');
            $('input[name=\'address_1\']').focus();
            return false;
        }
        if($('input[name=\'address_2\']').val() ==''){
            $('input[name=\'address_2\']').after('<div class="error"> Please enter warehouse address 2 ! </div>');
            $('input[name=\'address_2\']').focus();
            return false;
        }

        if($('input[name=\'city\']').val() ==''){
            $('input[name=\'city\']').after('<div class="error"> Please enter warehouse city ! </div>');
            $('input[name=\'city\']').focus();
            return false;
        }
        if($('input[name=\'postcode\']').val() ==''){
            $('input[name=\'postcode\']').after('<div class="error"> Please enter warehouse postcode ! </div>');
            $('input[name=\'postcode\']').focus();
            return false;
        }
        if($('select[name=\'country\']').val() ==''){
            $('select[name=\'country\']').after('<div class="error"> Please enter warehouse country ! </div>');
            $('select[name=\'country\']').focus();
            return false;
        }
        if($('select[name=\'state\']').val() ==''){
            $('select[name=\'state\']').after('<div class="error"> Please enter warehouse state ! </div>');
            $('select[name=\'state\']').focus();
            return false;
        }
        if($('input[name=\'telephone\']').val() ==''){
            $('input[name=\'telephone\']').after('<div class="error"> Please enter warehouse telephone ! </div>');
            $('input[name=\'telephone\']').focus();
            return false;
        }

    });

</script>
