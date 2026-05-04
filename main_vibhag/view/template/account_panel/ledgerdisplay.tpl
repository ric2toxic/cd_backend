<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <h1><?php echo 'Ledger Display'//$page_title; ?></h1>
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
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo 'Ledger Display'//$heading_title; ?></h3>
      </div>
      <div class="panel-body">
        <div class="well">
           <form action="" method="get" id="filter_form">
               <input type="hidden" name="route" value="<?php echo $route; ?>">
               <input type="hidden" name="token" value="<?php echo $token; ?>">
                <div class="row">

                    <div class="col-sm-4">
                        <div class="form-group">
                          <label class="control-label" for="input-model"><?php echo "Ledger Name Auto";?></label>
                          <input type="text" name="filter_ledger_name" value="<?php echo $filter_ledger_name; ?>" placeholder="<?php //echo $entry_model; ?>" id="input-model" class="form-control" />
                          <input type="hidden" name="ledger_id" value="<?php echo $ledger_id; ?>">
                        </div>


                        <div class="form-group">
                          <label class="control-label" for="input-name"><?php echo "Reference"; ?></label>
                          <input type="text" name="filter_ref" value="<?php echo $filter_ref; ?>" placeholder="<?php echo $filter_ref; ?>" id="input-name" class="form-control" />
                        </div>

                        <div class="form-group">
                          <label class="control-label" for="input-name"><?php echo "Order No."; ?></label>
                          <input type="text" name="filter_order_no" value="<?php echo $filter_order_no; ?>" placeholder="<?php echo $filter_order_no; ?>" id="input-name" class="form-control" />
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group date">
                            <label class="control-label" for="seller_id">Date From</label>
                            <div class="input-group date">
                              <input type="text" class="form-control" data-date-format="YYYY-MM-DD" value="<?php echo $filter_date_from; ?>" name="filter_date_from" placeholder="Date From" />
                              <span class="input-group-btn">
                                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                              </span>
                            </div>
                        </div>
                        <div class="form-group date">
                            <label class="control-label" for="seller_id">Date To</label>
                            <div class="input-group date">
                                <input type="text" class="form-control" data-date-format="YYYY-MM-DD" value="<?php echo $filter_date_to; ?>" name="filter_date_to" placeholder="Date To" />
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                      <div class="form-group">
                        <label class="control-label" for="input-name"><?php echo "Select Order"; ?></label>
                        <select name="filter_order" class="form-control filter_order">
                            <option value="">Select Order</option>
                            <?php
                              $filter_orderArry = array( 'ASC'  => 'ASC' , 'DESC' => 'DESC');
                              foreach( $filter_orderArry as $key => $value ) {
                              
                              if( $key == $filter_order ) {
                            ?>
                            <option class="form-control" selected  value="<?php echo $key  ?>" > <?php echo $value ?> </option>
                            <?php
                              }
                              else {
                            ?>
                            <option class="form-control" value="<?php echo $key  ?>" > <?php echo  $value ?> </option>
                            <?php
                              }
                              }
                            ?>
                        </select>
                      </div>
                      <div class="form-group">
                        <label class="control-label" for="input-status"><?php echo 'Page Limit'; ?></label>
                        <select name="filter_page_limit" id="input-status" class="form-control">
                          <option value="*">--Select--</option>
                          
                          <?php if(isset($page_limit_array)){ ?>
                          <?php foreach($page_limit_array as $page_limit_value){ ?>
                          <?php
                                if($page_limit_value == $filter_page_limit){
                                  $page_selected = 'selected';
                                }else{
                                  $page_selected = '';
                                }
                               ?>
                          <option value="<?php echo $page_limit_value; ?>" <?php echo $page_selected; ?>><?php echo $page_limit_value; ?></option>
                          <?php } ?>
                          <?php } ?>
                        </select>
                      </div>
                    </div>

                    <button type="submit" form="filter_form" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
                </div>

            </form>
        </div>
        <div class="well">
          <span style="font-weight: bold;">
            <?php 
            if($opBal > 0)
            {
              echo  "Opening Balance DR - Rs. " . number_format($opBal,2) . ", ";
            }
            if($opBal < 0)
            {
              echo  "Opening Balance CR - Rs. " . number_format(abs($opBal),2) . ", ";
            }
            if($clBal > 0)
            {
              echo  "Closing Balance DR - Rs. " . number_format($clBal,2);
            }
            if($clBal < 0)
            {
              echo  "Closing Balance CR - Rs. " . number_format(abs($clBal),2);
            }
            ?>
          </span>
        </div>
        <form action="<?php //echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-return">
          <div class="table-responsive analysis">
            <table class="table table-bordered analysis_table">
              <thead>
                <tr>
                  <td>S.No.</td>
                  <td>Dated</td>
                  <td>Particulars</td>
                  <td>Reference</td>
                  <td>Order No.</td>
                  <td>Dr</td>                 
                  <td>Cr</td>
                  
                </tr>
              </thead>
              <tbody>

                <?php if ($clientLedgers) { ?>
                <?php $i = 1; ?>
                <?php foreach ($clientLedgers as $key => $clientLedger) {  ?>               
  
                <tr class="parent" data-child="child_<?php echo $key; ?>" >
                  <td class="text-left"><?php echo $i; ?></td>
                  <td class="text-left"><?php echo $newDate = date("d-m-Y", strtotime($clientLedger['dated'])); ?></td>
                  <td class="text-left"><?php echo $clientLedger['ledger_name']; ?></td>
                  <td class="text-left"><?php echo $clientLedger['reference']; ?></td>
                  <td class="text-left"><?php echo $clientLedger['order_no']; ?></td>

                  <td class="text-left">
                  <?php 
                    echo ($clientLedger['Dr'] > 0 ? "Rs. " . $clientLedger['Dr'] : ''); 
                  ?>
                  </td>


                  <td class="text-left">                  
                  <?php 
                    echo ($clientLedger['Cr'] > 0 ? "Rs. " . $clientLedger['Cr'] : ''); 
                  ?>
                  </td>

                                  
                </tr>

                <?php $i++;} ?>
                <?php } else { ?>
                <tr>
                  <td class="text-center" colspan="10"><?php echo $text_no_results; ?></td>
                </tr>
                <?php } ?>
                <tr style="font-weight:bold">
                    <td ></td>
                    <td ></td>
                    <td ></td>
                    <td ></td>
                    <td >Total Rs.</td>
                    <td > <?php echo "Rs. " . number_format($drTotal,2)  ?> </td>
                    <td > <?php echo "Rs. " . number_format($crTotal,2) ?> </td>
                </tr>

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
<a href="#" class="scrollToTop">Top</a>
</div>
<?php echo $footer; ?>
<style>
.scrollToTop{
  display: none;
  position: fixed;
  bottom: 20px;
  right: 30px;
  z-index: 99;
  border: none;
  outline: none;
  background-color: red;
  color: white;
  cursor: pointer;
  padding: 15px;
  border-radius: 10px;
}

.scrollToTop:hover{
  text-decoration:none;
}
</style>
<script>
$('.date').datetimepicker({
      pickTime: false
    });
    
$(document).ready(function(){
  
  //Check to see if the window is top if not then display button
  $(window).scroll(function(){
    if ($(this).scrollTop() > 100) {
      $('.scrollToTop').fadeIn();
    } else {
      $('.scrollToTop').fadeOut();
    }
  });
  
  //Click event to scroll to top
  $('.scrollToTop').click(function(){
    $('html, body').animate({scrollTop : 0},800);
    return false;
  });
  
});
</script>
<style>
.show_product_row + .child{
    display: table-row!important;
}
</style>
<script type="text/javascript">
$('.toggleclass').click(function(){
    if($(this).prop('checked')){
        $(this).parents('tr').addClass('show_product_row bg-success');
    }
    else{
        $(this).parents('tr').removeClass('show_product_row bg-success');
    }

});
$('#seller_id').on('keyup',function(){
    if($(this).val().trim().length == 0){
        $('input[name="filter_seller_id"]').val(0);
    }
});

</script>

<script type="text/javascript">
    $('input[name=\'filter_ledger_name\']').on('keyup', function() {
      var check_length = $(this).val().trim().length;
        $('input[name=\'filter_ledger_name\']').autocomplete({
          'source': function (request, response) {
            $.ajax({
              url: 'index.php?route=account_panel/ledger/getLedger&token=<?php echo $token; ?>&filter_ledger_name=' + encodeURIComponent(request),
              dataType: 'json',
              success: function (json) {
                response($.map(json, function (item) {
                  //console.log(item['ledger_name']);
                  return {
                    //label: item['model'],
                    label: item['ledger_name'],
                    value: item['ledger_id']
                  }
                }));
              }
            });
          },
          'select': function (item) {
            //$('input[name=\'filter_ledger_name\']').val(item['text']);
            $('input[name=\'filter_ledger_name\']').val(item['label']);
            $('input[name=\'ledger_id\']').val(item['value']);
          }
        });
    });
</script>