<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <h1><?php echo $page_title; ?></h1>
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
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $heading_title; ?></h3>
      </div>
      <div class="panel-body">
        <div class="well">
           <form action="" method="get" id="filter_form">
               <input type="hidden" name="route" value="<?php echo $route; ?>">
               <input type="hidden" name="token" value="<?php echo $token; ?>">
                <div class="row">

                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="control-label" for="nickname">Seller Code</label>
                            <input type="text" class="form-control" id="nickname" placeholder="Nickname" name="nickname" value="<?php echo $nickname; ?>" />
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="seller_id">SKU</label>
                            <input type="text" class="form-control" id="sku" placeholder="SKU" name="sku" value="<?php echo $sku; ?>" />
                        </div>                        
                    </div>

                    <div class="col-sm-4">
                        <div class="form-group date">
                            <label class="control-label" for="seller_id">Purchase Date Added From</label>
                            <div class="input-group date">
                                <input type="text" class="form-control" data-date-format="YYYY-MM-DD" value="<?php echo $filter_date_added_from; ?>" name="filter_date_added_from" placeholder="Purchase Date Added From" />
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                                </span>
                            </div>
                        </div>
                      <div class="form-group date">
                          <label class="control-label" for="seller_id">Purchase Date Added To</label>
                          <div class="input-group date">
                              <input type="text" class="form-control" data-date-format="YYYY-MM-DD" value="<?php echo $filter_date_added_to; ?>" name="filter_date_added_to" placeholder="Purchase Date Added To" />
                              <span class="input-group-btn">
                                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                              </span>
                          </div>
                      </div>                        
                    </div>

                    
                    <div class="col-sm-4">
                        <div class="form-group">
                          <label class="control-label" for="input-store_date">Store Sales</label>
                          <div class="">
                            <select name="store_sales"
                                    data-old-value="<?php echo $store_sales; ?>"
                                    data-product-id="<?php echo $data['p_id']; ?>"
                                    class="form-control edit_track store_sales"
                                    data-change="false" >
                                     <option value=""></option>
                                  <?php
                                  foreach($data['store_sales_options'] as $option){ ?>
                                    <option value="<?php echo $option; ?>"
                                           <?php echo $store_sales == $option ? 'selected' : ''; ?> >
                                            <?php echo $option; ?></option>
                                  <?php } ?>
                            </select>
                          </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <br><br><br><br>
                            <button type="submit" form="filter_form" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
                        </div>
                    </div>
                </div>

          
            </form>
        </div>
        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-return">
          <div class="table-responsive analysis">
            <table class="table table-bordered analysis_table">
              <thead>
                <tr>
                  <td >Image</td>
                  <td >Product SKU</td>
                  <td >Store</td>
                  <td class="text-left">
                    <table >
                      <tr>
                          <td>Purchased date</td>
                          <td>Total Purchase</td>
                      </tr>
                    </table>
                  </td>                  
                  <td >Sales</td>
                  <td >Available Pieces</td>
                  <td >Total Value</td>
                </tr>
              </thead>
              <tbody>              
                <?php if ($wsb_purchase) { ?>
                <?php $i = 1; ?>
                <?php foreach ($wsb_purchase as $key => $wsb_purchase) { ?> 
  
                <tr class="parent" data-child="child_<?php echo $key; ?>" >
                  <td class="text-left"><img src="<?php echo HTTPS_CATALOG.'image/'.$wsb_purchase['image']; ?>" style="border:1px solid #E0E0E0;" width="<?php echo $wsb_purchase['img_width'];?>" height="<?php echo $wsb_purchase['img_height'];?>"></td>
                  <td class="text-left"><?php echo $wsb_purchase['sku']; ?></td>
                  <td class="text-left"><?php echo $wsb_purchase['store_sales']; ?></td>
                  <td class="text-left" class="innerTab">
                    <table >
                    <?php foreach ($wsb_purchase['purchase_data'] as $purdata) {?>
                      <tr>
                        <td><?php echo $purdata['invoice_date']?></td>
                        <td><?php echo $purdata['pieces']?></td>
                      </tr>
                    <?php
                    }
                    ?>                    

                    </table>
                  </td>

                  <td class="text-left" class="innerTab">
                    <table >
                      <tr>
                        <td>Current Sales :</td>
                        <td><?php echo $wsb_purchase['total_sales']; ?></td>
                      </tr>

                      <?php foreach ($wsb_purchase['purchase_data'] as $purdata) {?>
                        <tr>
                          <td>Age :</td>
                          <td><?php if (isset($purdata['age'])){echo $purdata['age'];}?></td>
                        </tr>
                        <tr>
                          <td>Age/Piece :</td>
                          <td><?php if (isset($purdata['age_per_piece'])) {
                                     if(is_array($purdata['age_per_piece'])) {
                                      echo $purdata['age_per_piece'][0];
                                  } else {
                                    echo $purdata['age_per_piece'];
                            }
                          }?></td>
                        </tr>                      
                      <?php
                        }
                      ?>
                    </table>
                  </td>

                  <!--td class="text-left"><?php //echo ($wsb_purchase['total_purchasesmy']-$wsb_purchase['total_sales']); ?></td-->
                  <td class="text-left"><?php echo $wsb_purchase['available_pieces']; ?></td>
                  <td class="text-left"><?php echo round($wsb_purchase['total_value'],0); ?></td>



                  <!--td class="text-left"><?php //echo round((($wsb_purchase['total_purchasesmy']-$wsb_purchase['total_sales']) * $wsb_purchase['price']),0); ?></td-->




                  <!--td class="text-left"><?php //echo $wsb_purchase['date_added']; ?></td-->
                  <!--td class="text-right">
                      <input type="checkbox" class="form-control toggleclass">
                  </td-->
                </tr>
                <tr class="child child_<?php echo $key; ?>" style="display:none;">
                    <td class="text-center"  colspan="12">
                   <?php if(!empty($wsb_purchase['products'])) { ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <td>SKU</td>
                                <td>Pieces</td>
                                <td>Price Per Piece</td>
                            </tr>
                        </thead>
                        <tbody>
                    <?php foreach( $wsb_purchase['products'] as $product ){ ?>
                        <tr>
                            <td><?php echo $product['sku']; ?></td>
                            <td><?php echo $product['pieces']; ?></td>
                            <td><?php echo $product['transfer_price_per_piece']; ?></td>
                        </tr>
                    <?php }  ?>
                       </tbody>
                    </table>
                <?php } else{
                                echo "No Products Found.";
                     } ?>
                    </td>
                  </tr>
                <?php } ?>
                <?php } else { ?>
                <tr>
                  <td class="text-center" colspan="10"><?php echo $text_no_results; ?></td>
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
</div>
<?php echo $footer; ?>
<style>
.show_product_row + .child{
    display: table-row!important;
}
</style>
<script type="text/javascript">
$('.date').datetimepicker({
      pickTime: false
    });
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
// $('#seller_id').autocomplete({
//       'source': function(request, response) {
//          let json = [];
//          if(request.length > 0){
//              if(typeof request == 'string'){
//                 request = request.toLowerCase();
//              }
//              let sellers = <?php echo json_encode($sellers); ?>;
//              $(sellers).each(function(ind,element){
//                  let nickname = element['nickname'].toLowerCase();
//                  let name = element['company'].toLowerCase();
//                  let data;
//                  if( nickname.search(request) >= 0 ){
//                      data = $.parseJSON('{"label":"' + element['nickname'] + '","value":"' + element['seller_id'] + '"}');
//                  }
//                  if( element['seller_id']  == request ){
//                      data = $.parseJSON('{"label":"' + element['company'] + '","value":"' + element['seller_id'] + '"}');
//                  }
//                  if( name.search(request) >= 0){
//                      data = $.parseJSON('{"label":"' + element['company'] + '","value":"' + element['seller_id'] + '"}');
//                  }
//                  if( data ){
//                      json.push(data);
//                  }
//              });
//          }
//          response(json);
//       },
//       'select': function(item) {
//         $('#seller_id').val(item['label']);
//         $('input[name="filter_seller_id"]').val(item['value']);
//       }
//     });
    
  </script>
