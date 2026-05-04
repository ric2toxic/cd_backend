<?php echo $header; ?><?php echo $column_left; ?>

<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <h1><?php echo $heading_sor_report; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-list"></i><?php echo $heading_sor_report; ?></h3>
      </div>
      <div class="panel-body">
        <div class="well">
          <form action="<?php echo $action; ?>" method="post">
            <div class="row">
              <div class="col-sm-3">
                <div class="form-group">
                  <label class="control-label" for="input-seller"><?php echo $store_name; ?></label>                  
                  <select name="store_name" class="form-control" value="<?php echo $filter_store_name; ?>" placeholder="<?php echo $store_name; ?>" id="store_name">
                    <option value="">-Select Store-</option>
                    <?php
                    foreach ($stors as $key => $value) {
                    ?>  
                    <option <?php if($filter_store_name==$value['pickup_city_code']){echo 'selected="selected"';}?> ><?php echo $value['pickup_city_code'];?></option>
                    <?php  
                    }
                    ?>
                  </select>
                </div>
              </div>
              <div class="col-sm-3">
                <div class="form-group required" style="position:relative">
                  <label class="control-label" for="seller_id"><?php echo $seller_name_title; ?></label>
                  <input autocomplete="off" required type="text" required class="form-control" id="seller_id" placeholder="Seller" value="<?php echo  $filter_seller_id; ?>" />
                  <!-- <input type="hidden" class="form-control"  value="<?php //echo $seller_id; ?>" name="seller_id" placeholder="Seller" /> -->
                  <button id="dLabel11" type="button" class="hidden  dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      <span class="caret pull-right" style="margin-top:5px"></span>
                  </button>
                  <ul class="dropdown-menu" style="height:200px;overflow-y:auto;" aria-labelledby="dLabel11">
                      <?php if(!empty($seller_id)){ ?>
                          <li>
                              <a data-value="<?php echo $seller_id; ?>">
                                  <label for="seller_<?php echo $seller_id; ?>">
                                  <?php echo  $filter_seller_name; ?>
                                  </label>
                              </a>

                              <input type="radio"
                                     class="hidden purchase_firm"
                                     id="seller_id_<?php echo $filter_seller_id; ?>"
                                     name="seller_id"
                                     value="<?php echo $filter_seller_id; ?>"
                                     checked  />
                          </li>
                      <?php } ?>
                  </ul>
                </div>
              </div>
              <div class="col-sm-3">
                <div class="form-group date">
                  <label class="control-label" for="input-order-no"><?php echo $date_to; ?></label>
                  <input type="text" style="width: 80%; float: left;" name="date_to" value="<?php echo $filter_date_to; ?>" data-date-format="YYYY-MM-DD" placeholder="<?php echo $date_to; ?>" id="date_to" class="form-control" />
                  <button type="button" style="float: left; margin-left: -3px;" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                </div>
              </div>                
              <div class="col-sm-3">             
                <div class="form-group">
                  <button type="button" style="margin-top: 22px;" id="clear_date" class="btn btn-primary pull-left" name="Clear Date">Clear Date</button>
                  <button type="button" style="margin-top: 22px;" id="button-download-csv" class="btn btn-primary pull-right">
                      <i class="fa"></i> <?php echo $button_download_csv; ?>
                  </button>
                </div>
              </div>
            </div>
          </form>  
        </div>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>

<script type="text/javascript">
 $(document).ready(function(){
    
    selectThis('input[type="radio"].purchase_firm:checked','.prcse-btn');

    $('#button-download-csv').on('click', function() {
      var base_url = 'index.php?route=accounts/sorstockreports&token=<?php echo $token; ?>'+'&download=csv';
      var url = base_url;
      
      var store_name = $('select[name=\'store_name\']').val();
      if (store_name) {
          url += '&store_name=' + encodeURIComponent(store_name);
      }
      var seller_id = $('input[name=\'seller_id\']').val();
      if (seller_id) {
          url += '&seller_id=' + encodeURIComponent(seller_id);
      }
      var date_to = $('input[name=\'date_to\']').val();
      if (date_to) {
          url += '&date_to=' + encodeURIComponent(date_to);
      }
      location = url;
    });
    ////////////////////////////////////////////
    $('#seller_id').on(' keyup ',function(){
          let request = $(this).val();
         if( request.length > 0){
             if(typeof request == 'string'){
                request = $(this).val().toLowerCase().trim();
             }
             $(this).siblings('.dropdown-menu').html('');
             let input = $(this);
             let sellers = <?php echo json_encode($sellers); ?>;
             $(sellers).each(function(ind,element){

                 let nickname = element['nickname'].toLowerCase();
                 let name = element['company'].toLowerCase();
                 if( nickname.search(request) >= 0 || element['seller_id']  == request || name.search(request) >= 0 ){
                     let li  = '<li>';
                         li += '<a data-value="'+ element.seller_id +'">';
                         li +=    '<label for="seller_'+ element.seller_id +'">';
                         li +=       element.company + '<br>' + element.address1 + '<br> ' + element.address2;
                         li +=       '<br>'+element.city;
                         li +=    '</label>';
                         li += '</a>';
                         li += '<input type="radio" onchange="selectThis(this,\'#seller_id\',\'' + (element.company).replace(/'/g, "\\'") + '\')" class="hidden seller_id" id="seller_' + element.seller_id +'" name="seller_id" value="' + element.seller_id + '" />';
                         li += '</li>';
                     input.siblings('.dropdown-menu').append(li);
                 }
             });
             $('#dLabel11').click();
         }
    });
    //////////////////////////////////////
    $('.date').datetimepicker({
       pickTime: false,
    });
    ////////////////////////////////////
    $('#clear_date').click(function(){
      $('#date_to').val('');
    });
});
function selectThis(obj,btn,val=''){
    let text = $(obj).siblings('a').find('label').text();
    if(btn=='#seller_id'){
        $(btn).val(val);
    }
    else{
        $(btn).html(text+'<span class="caret pull-right" style="margin-top:5px"></span>');
    }
} 
</script>

<style type="text/css">
.btn{margin-right: 10px;}
.block{border: 1px solid rgb(221, 221, 221);padding: 3px;margin-bottom: 2px;}
</style>
