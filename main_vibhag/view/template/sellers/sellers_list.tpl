<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid seller_list_page">
            <div class="pull-right ">
                <fieldset>
                    <legend>Mark Exclusive Status</legend>
                        <select name="filter_mark_exclusive_status" id="select-mark-exclusive-status">
                            <option value="">-----SELECT-----</option>
                            <option value="normal">Normal</option>
                            <option value="exclusive">Exclusive</option>
                            <option value="both">Both</option>
                        </select>
                      <button type="button" data-toggle="tooltip" title="<?php echo 'Save'; ?>" class="btn btn-primary mark_exclusive_btn btn-xs"><i class="fa fa-save"></i></i></button>
                </fieldset>
                <button type="button" data-toggle="tooltip" title="<?php echo 'Vacation Mode'; ?>" class="btn btn-warning seller_vacation_mode_button">Change Vacation Mode</button>
                <a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
            </div>
            <h1><?php echo $heading_title; ?></h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="container-fluid">
        <?php if ($error_warning) { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <?php if (isset($success) && $success) { ?>
        <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
            </div>
            <div class="panel-body">
                <div class="well">
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="input-seller"><?php echo $entry_seller; ?></label>
                                <input type="text" name="filter_seller" value="<?php echo $filter_seller; ?>" placeholder="<?php echo $entry_seller; ?>" id="input-seller" class="form-control" />
                            </div>
                            <div class="form-group">
                              <label class="control-label" for="input-vacation">Vacation Mode</label>
                              <select name="filter_vacation" id="input-vacation" class="form-control">
                                <option value="*" selected ></option>
                                <option value="1" <?php if (isset($filter_vacation) and $filter_vacation == "1") echo "selected";?> >ON</option>
                                <option value="0" <?php if (isset($filter_vacation) and $filter_vacation == "0") echo "selected";?> >OFF</option>
                              </select>
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="input-product-prefix"><?php echo $entry_product_name_prefix; ?></label>
                                <input type="text" name="filter_product_name_prefix" value="<?php echo $filter_product_name_prefix; ?>" placeholder="<?php echo $entry_product_name_prefix; ?>" id="input-product-prefix" class="form-control" />
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="input-company"><?php echo $entry_company; ?></label>
                                <input type="text" name="filter_company" value="<?php echo $filter_company; ?>" placeholder="<?php echo $entry_company; ?>" id="input-company" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="input-vacation">Rating</label>
                                <select name="filter_rating" id="input-vacation" class="form-control">
                                    <option value="*" selected ></option>
                                    <option value="1" <?php if (isset($filter_rating) and $filter_rating == "1") echo "selected";?> >Rated</option>
                                    <option value="0" <?php if (isset($filter_rating) and $filter_rating == "0") echo "selected";?> >Un-Rated</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="input-city"><?php echo $entry_city; ?></label>
                                <input type="text" name="filter_city" value="<?php echo $filter_city; ?>" placeholder="<?php echo $entry_city; ?>" id="input-city" class="form-control" />
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="input-email"><?php echo $entry_email; ?></label>
                                <input type="text" name="filter_email" value="<?php echo $filter_email; ?>" placeholder="<?php echo $entry_email; ?>" id="input-email" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?php echo $entry_status; ?></label>
                                <?php $status_value = array('*'=>'--Select--', '1'=>'Active','2'=>'Inactive','3'=>'Disabled', '-2'=>'Pending verification');?>
                                <select class="form-control" name="filter_seller_status">
                                    <?php if($status_value){ ?>
                                        <?php foreach($status_value as $key => $values){ ?>
                                            <?php if($filter_seller_status == $key){ $selected = "selected";}else{ $selected = '';}?>
                                            <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $values?></option>
                                        <?php }?>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?php echo $entry_state; ?></label>
                                <select class="form-control" name="filter_state">
                                    <option value="">--Select--</option>
                                    <?php if($zone){ ?>
                                        <?php foreach($zone as $key => $values){ ?>
                                            <?php if($filter_state == $values['zone_id']){ $selected = "selected";}else{ $selected = '';}?>
                                            <option value="<?php echo $values['zone_id']; ?>" <?php echo $selected; ?>><?php echo $values['name']?></option>
                                        <?php }?>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>    
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="input-telephone"><?php echo $entry_telephone; ?></label>
                                <input type="text" name="filter_telephone" value="<?php echo $filter_telephone; ?>" placeholder="<?php echo $entry_telephone; ?>" id="input-email" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="input-telephone"><?php echo $entry_seller_id; ?></label>
                                <input type="text" name="filter_seller_id" value="<?php echo $filter_seller_id; ?>" placeholder="<?php echo $entry_seller_id; ?>" id="input-email" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="input-sale"><?php echo "Filter sellers on sale"; ?></label>
                                <?php if ($filter_sale == 1) { ?>
                                <input type="checkbox" name="filter_sale" checked="checked" class="filter_sale form-control" />
                                <?php } else { ?>
                                <input type="checkbox" name="filter_sale" class="filter_sale form-control" />
                                <?php } ?>
                            </div>

                            <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
                        </div>
                    </div>
                </div>
                <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-customer">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" style="text-align: center" id="list-sellers">
                            <thead>
                            <tr>
                                <td><input type="checkbox" onclick="$('input[name*=\'checked_selected\']').attr('checked', this.checked);" /></td>
                                <td class="text-left"><?php if ($sort == 'ms.nickname') { ?>
                                    <a href="<?php echo $sort_seller_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_seller; ?></a>
                                    <?php } else { ?>
                                    <a href="<?php echo $sort_seller_name; ?>"><?php echo $column_seller; ?></a>
                                    <?php } ?>
                                </td>
                                <td class="text-left"><?php if ($sort == 'ms.company') { ?>
                                    <a href="<?php echo $sort_company; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_company; ?></a>
                                    <?php } else { ?>
                                    <a href="<?php echo $sort_company; ?>"><?php echo $column_company; ?></a>
                                    <?php } ?></td>
                                <td class="text-left">
                                    <?php echo $column_product_name_prefix; ?>
                                </td>
                                <td class="text-left"><?php if ($sort == 'c.email') { ?>
                                    <a href="<?php echo $sort_email; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_email; ?></a>
                                    <?php } else { ?>
                                    <a href="<?php echo $sort_email; ?>"><?php echo $column_email; ?></a>
                                    <?php } ?></td>
                                <td class="text-left"><?php if ($sort == 'c.telephone') { ?>
                                    <a href="<?php echo $sort_telephone; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_telephone; ?></a>
                                    <?php } else { ?>
                                    <a href="<?php echo $sort_telephone; ?>"><?php echo $column_telephone; ?></a>
                                    <?php } ?></td>
                                <td class="text-left"><?php if ($sort == 'product_total') { ?>
                                    <a href="<?php echo $sort_product_no; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_product_no; ?></a>
                                    <?php } else { ?>
                                    <a href="<?php echo $sort_product_no; ?>"><?php echo $column_product_no; ?></a>
                                    <?php } ?></td>
                                <td class="text-left"><?php if ($sort == 'ms.seller_status') { ?>
                                    <a href="<?php echo $sort_status; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_status . ' (Exclusive Status)'; ?></a>
                                    <?php } else { ?>
                                    <a href="<?php echo $sort_status; ?>"><?php echo $column_status . ' (Exclusive Status)'; ?></a>
                                    <?php } ?></td>
                                <td class="text-left"><?php if ($sort == 'ms.date_created') { ?>
                                    <a href="<?php echo $sort_date_added; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_date_added; ?></a>
                                    <?php } else { ?>
                                    <a href="<?php echo $sort_date_added; ?>"><?php echo $column_date_added; ?></a>
                                    <?php } ?></td>
                                <td><?php echo $column_action; ?></td>
                            </tr>
                            </thead>
                            <tbody>
                                <?php if(isset($sellers)&& !empty($sellers)){ ?>
                                <?php foreach($sellers as $seller){ ?>
                                <tr>
                                    <td class="text-left"><?php if (in_array($seller['seller_id'], $checked_selected)) { ?>
                                        <input type="checkbox" name="checked_selected[]" value="<?php echo $seller['seller_id']; ?>" checked="checked"/>
                                        <?php } else { ?>
                                        <input type="checkbox" name="checked_selected[]" value="<?php echo $seller['seller_id']; ?>" data-vacation-mode="<?php if($seller['seller_vacation'] == 1){ echo '0'; }else{ echo '1'; };?>"/>
                                        <?php } ?></td>
                                    <td class="text-left order_list_comment">
                                        <?php if($seller['seller_total_rating'] > 0 ){ ?>
                                            <sup class="seller_total_rating"><i class="fa fa-star fa-stack-1x"></i></sup>&nbsp;
                                        <?php } ?>
                                        <?php echo $seller['name']; ?> (<?php echo $seller['nick_name']; ?>)</br>
                                        <?php if(!empty($seller['on_sale'])){ ?>
                                            <span style="background-color:red;color: white;font-weight: 600;"><?php echo $seller['on_sale']; ?></span><br>
                                        <?php } ?>
                                        <b>(<?php echo $seller['seller_id'] ;?>)</b>
                                    </td>
                                    <td class="text-left order_list_comment"><?php echo $seller['company']; ?>  <br/>
                                        <?php if($seller['seller_vacation'] == 1) { ?>
                                        <span class="seller_vacation_mode">Vacation ON</span>
                                        <?php } ?>
                                    </td>
                                    <td class="text-left">
                                        <?php if( !empty($seller['product_name_prefix']) ) { ?>
                                            <?php echo $seller['product_name_prefix']; ?>  <br/>
                                            <?php if($seller['prefix_mode'] == 1) { ?>
                                            <span>Mode: <b class="text-success">ON</b></span>
                                            <?php }else{ ?> 
                                            <span>Mode: <b class="text-danger">OFF</b></span>
                                            <?php } ?>
                                        <?php } ?>
                                    </td>
                                    <td class="text-left">
                                        <span class="click_to_see btn-primary btn-xs" data-field-type="email" data-field-value="<?php echo base64_encode($seller['email']); ?>" onClick="clickToSee(this)"> Click to see </span>
                                        <!-- <?php //echo $seller['email']; ?> -->
                                    </td>
                                    <td class="text-left order_list_comment">
                                    <span class="click_to_see btn-primary btn-xs" data-field-type="telephone" data-field-value="<?php echo base64_encode($seller['telephone']); ?>" onClick="clickToSee(this)"> Click to see </span>
                                        <!-- <?php //echo $seller['telephone']; ?> -->
                                    </td>
                                    <td class="text-center"><?php echo $seller['product_total']; ?></td>
                                    <td class="text-left">
                                        <?php $status = array('1'=>'Active','2'=>'Inactive','3'=>'Disabled');
                                            if($status){
                                                foreach($status as $key => $values){
                                                    if($key == $seller['status']){
                                                        echo $values;
                                                    }
                                                }
                                            }
                                            echo !empty($seller['exclusive']) ? ' ('.$seller['exclusive'] . ') ' : '';
                                        ?>
                                    </td>
                                    <td class="text-left"><?php echo date('d-m-Y',strtotime($seller['date_added'])); ?></td>
                                    <td class="text-left">
                                        <a href="<?php echo $seller['seller_login'];?>" data-toggle="tooltip" title="<?php echo $button_login; ?>" class="btn btn-info " target="_blank"><i class="fa fa-lock"></i></a>
                                        <a href="<?php echo $seller['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a>
                                        <?php if($seller['product_total'] <= 0 ){ ?>
                                            <a href="<?php echo $seller['deleteSeller']; ?>" data-toggle="tooltip" title="Remove as Seller" class="btn btn-danger" onclick="return confirm('Are you sure to Remove as Seller ?')"><i class="fa fa-close"></i></a>
                                        <?php } ?>

                                         <a href="javascript:;" data-toggle="modal" data-target="#sellerSor" data-sor_days="<?php echo $seller['sor_terms']['sor_days']; ?>" data-sor_type="<?php echo $seller['sor_terms']['sor_type']; ?>" onclick="seller_sor_open(<?php echo $seller['seller_id']; ?>);" title="SOR Seller" id="seller_sor_btn_<?php echo $seller['seller_id']; ?>" class="btn btn-warning"><i class="fa fa-money"></i></a>
                                        
                                    </td>
                                </tr>
                                <?php } ?>
                                <?php } else { ?>
                                <tr>
                                    <td class="text-center" colspan="8"><?php echo $text_no_results; ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </form>
                <div class="row">
                    <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
                    <div class="col-sm-6 text-right"><?php echo $results; ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="ajaXloader" ></div>
</div>


<div id="sellerSor" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">SOR Terms</h4>
      </div>
      <div class="modal-body">
        <form id="seller_sor_form" method="post">
         <input type="hidden" name="seller_id" id="seller_id" />
         <input type="hidden" name="product_update" value="0" />
                     <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label" for="input-seller">Sor Type</label>
                                 <select name="sor_type" id="sor_type" class="form-control">
                                   <option value="regular">Regular</option>
                                   <option value="wsb-credit">Wsb-Credit</option>
                                 </select>
                            </div>
                        </div>
                    </div> 

                     <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label" for="input-seller">Sor Days</label>
                                 <input type="text" name="sor_days" value="" placeholder="Sor Days" id="sor_days" class="form-control" />
                            </div>
                        </div>
                    </div>       
        </form>
      </div>
      <div class="modal-footer">
          <button type="button" id="button-sor-save" onclick="seller_sor_save()" class="btn btn-primary pull-right"></i> Save 
          </button>
      </div>
    </div>

  </div>
</div>
<?php echo $footer; ?>
<script type="text/javascript">
    $('#button-filter').click(function(){
        url = 'index.php?route=sellers/sellers&token=<?php echo $token; ?>';

        var filter_seller = $('input[name=\'filter_seller\']').val();

        if(filter_seller){
            url += '&filter_seller=' + encodeURIComponent(filter_seller);
        }

        var filter_company = $('input[name=\'filter_company\']').val();

        if(filter_company){
            url += '&filter_company=' + encodeURIComponent(filter_company);
        }

        var filter_product_name_prefix = $('input[name=\'filter_product_name_prefix\']').val();

        if(filter_product_name_prefix){
            url += '&filter_product_name_prefix=' + encodeURIComponent(filter_product_name_prefix);
        }

        var filter_email = $('input[name=\'filter_email\']').val();

        if(filter_email){
            url += '&filter_email=' + encodeURIComponent(filter_email);
        }


        if($('.filter_sale').is(':checked')){
            url += '&filter_sale=' + encodeURIComponent(1);
        }

        var filter_telephone = $('input[name=\'filter_telephone\']').val();

        if(filter_telephone){
            url += '&filter_telephone=' + encodeURIComponent(filter_telephone);
        }

        var filter_seller_id = $('input[name=\'filter_seller_id\']').val();

        if(filter_seller_id){
            url += '&filter_seller_id=' + encodeURIComponent(filter_seller_id);
        }        
        
        var filter_vacation = $('select[name=\'filter_vacation\']').val();

	    if (filter_vacation != '*') {
		    url += '&filter_vacation=' + encodeURIComponent(filter_vacation);
	    }

        var filter_rating = $('select[name=\'filter_rating\']').val();

        if (filter_rating != '*') {
            url += '&filter_rating=' + encodeURIComponent(filter_rating);
        }

        var filter_seller_status = $('select[name=\'filter_seller_status\']').val();

	    if (filter_seller_status != '*') {
		    url += '&filter_seller_status=' + encodeURIComponent(filter_seller_status);
	    }

        var filter_city = $('input[name=\'filter_city\']').val();

        if (filter_city != '*') {
            url += '&filter_city=' + encodeURIComponent(filter_city);
        }

        var filter_state = $('select[name=\'filter_state\']').val();

        if (filter_state != '*') {
            url += '&filter_state=' + encodeURIComponent(filter_state);
        }


        location = url;

    });
</script>
<script type="text/javascript">
    $(document).ready(function(){
        $('.mark_exclusive_btn').click(function(){
            var seller_arr = [];
            var exclusive_status = $('#select-mark-exclusive-status').val();
            $('input[name^=\'checked_selected\']:checked').each(function(index, element){
                seller_arr.push($(element).val());
            });
            if(seller_arr.length > 0 && exclusive_status != ''){
                var check_product_exclusive = 0;
                if(confirm("This action may change the Exclusive status for all the products of this seller, as per the newly assigned Seller's exclusive setting. If you wish to allow this behaviour, click OK, else Cancel (only seller status will change and future products will be by default set as per Sellr's setting).")) {
                    check_product_exclusive = 1;
                } else {
                    check_product_exclusive = 0;
                }
                
                $.ajax({
                        type: 'post',
                        url: 'index.php?route=sellers/sellers/updateSellerExclusiveStatus&token=<?php echo $token; ?>',
                        dataType: 'json',
                        data:{'seller_arr':seller_arr, 'exclusive_status': exclusive_status, 'check_product_exclusive': check_product_exclusive},
                        beforeSend: function () {
                            $('.ajaXloader').fadeIn(100);
                        },
                        complete: function () {
                            $('.ajaXloader').fadeOut(100);
                        },
                        success: function (json) {
                            $('.ajaXloader').fadeOut(100);
                            if(json['success']) {
                                alert(json['success']);
                                window.location.reload();
                            } 
                            if(json['error']) {
                                alert(json['error']);
                                return false;
                            }
                        }
                    });
            }else {
                alert('Please select seller and exclusive status first!');
            }
        });
    });

    $('input, select').on('keypress',function(e){
        if (e.keyCode == 13) {
            $('#button-filter').trigger('click');
        }
    });
    $('.seller_vacation_mode_button').click(function(){
        var selected_seller = [];
        var vacation_mode = [];
        $('input[name^=\'checked_selected\']:checked').each(function(index, element){
            selected_seller.push($(element).val());
            vacation_mode.push($(element).attr('data-vacation-mode'));
        });

        
        if(selected_seller ==''){
            alert('Please select a seller !');
        }else {
            if ((selected_seller.length) > 1) {
                alert('you can\'t select more than one seller !');
            } else {
                if (confirm('Are you sure currently seller on vacation mode ?')) {
                    $.ajax({
                        type: 'post',
                        url: 'index.php?route=sellers/sellers/updateSellerVacationMode&token=<?php echo $token; ?>',
                        data:{'selected_seller':selected_seller, 'vacation_mode': vacation_mode},
                        beforeSend: function () {
                            $('.seller_vacation_mode_button').button('loading');
                        },
                        complete: function () {
                            $('.seller_vacation_mode_button').button('reset');
                        },
                        success: function () {
                            window.location.reload();
                        }
                    });
                }

            }
        }

    });

 function seller_sor_open(seller_id)
 {
    var sor_days = $("#seller_sor_btn_"+seller_id).data('sor_days');
    var sor_type = $("#seller_sor_btn_"+seller_id).data('sor_type');
    $("#seller_sor_form input[name='seller_id']").val(seller_id);
    $("#seller_sor_form input[name='sor_days']").val(sor_days);
    $("#seller_sor_form select[name='sor_type']").val(sor_type);
 }

 function seller_sor_save()
 {
   if(confirm("Do you want to update existing products of this seller to SOR as well ?"))
   {
    $("#seller_sor_form input[name='product_update']").val(1);
     var form_data= $("#seller_sor_form").serialize();
     $.ajax({
     type: 'post',
     url: 'index.php?route=sellers/sellers/saveSorSeller&token=<?php echo $token; ?>',
     data: form_data,
      dataType: 'json',
      success: function (json) {
      if(json['success']) {
        alert(json['success']);
        $("#sellerSor").modal('hide');
      }
      if(json['error']) {
        alert(json['error']);
      }
     }
    });
  }
  else
  {
    $("#seller_sor_form input[name='product_update']").val(0);
    var form_data= $("#seller_sor_form").serialize();
     $.ajax({
     type: 'post',
     url: 'index.php?route=sellers/sellers/saveSorSeller&token=<?php echo $token; ?>',
     data: form_data,
      dataType: 'json',
      success: function (json) {
      if(json['success']) {
        alert(json['success']);
      }
      if(json['error']) {
        alert(json['error']);
      }
      $('#sellerSor').modal('hide');
     }
    }); 
  }
 }   

</script>
<style type="text/css">
    .seller_list_page fieldset {
        border: 1px groove #000;
        display: inline-block;
        padding: 0 5px 5px;
    }
    .seller_list_page fieldset legend {
    border: 0 none;
    font-size: 14px;
    padding: 0;
    width: auto;
    margin-bottom: 5px;
}
</style>