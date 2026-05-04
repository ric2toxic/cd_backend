<?php echo $header; ?>
<?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <h1>
                <?php echo $heading_title; ?>
            </h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li>
                    <a href="<?php echo $breadcrumb['href']; ?>">
                        <?php echo $breadcrumb['text']; ?>
                    </a>
                </li>
                <?php } ?>
            </ul>

             <a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary pull-right">Add Pickup Boy</a>

             <a href="<?php echo $all_pickup_list; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary pull-right" style="margin-right: 10px;">Pick Up List</a>

        </div>
    </div>
    <div class="container-fluid">
        <?php if ($error_warning != '') { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i>
            <?php echo $error_warning; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <?php if ($success) { ?>
        <div class="alert alert-success"><i class="fa fa-check-circle"></i>
            <?php echo $success; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <?php if ($delete_success) { ?>
        <div class="alert alert-success"><i class="fa fa-check-circle"></i>
            <?php echo $delete_success; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
      

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
            </div>

            <div class="panel-body pickup_list">
                
               <div class="tabs">
    <ul class="tab-links">
       <?php
        $i=0;
        foreach($picker_cities as $city) { ?>
        <li <?php if($i == 0) { ?> class="active" <?php } ?>><a href="#<?php echo $city['pickup_city_code'] ?>"><?php echo ucfirst($city['pickup_city']) ?></a></li>
       <?php $i++; } ?> 
    </ul>
 
    <div class="tab-content">
          <div class="row remove_margin">
                <div class="col-sm-4 pickup_title">Pickup Boy</div>
                <div class="col-sm-1"></div>
                <div class="col-sm-7 pickup_title">Assigned Seller</div>
               </div>

        <div class="row remove_margin"> 
        <div class="col-sm-4">      
      <?php $i=0; foreach($pickers as $key => $pickup) { ?>
        <div id="<?php echo $key; ?>" class="row tab <?php if($picker_cities[0]['pickup_city_code'] == $key) { echo "active"; } ?>">
             <?php $k=1; foreach($pickup as $pickup_boy) { ?>
               <div class="col-sm-12 pickup_data <?php if($k == 1) { echo "active"; } ?>">
               <a href="#seller_<?php echo $key; ?>_<?php echo $pickup_boy['id']; ?> ">
               <?php echo $k.'.'; ?> &nbsp;   
               <?php echo $pickup_boy['first_name']; ?> <?php echo $pickup_boy['last_name']; ?>
               </a>
               </div>
            
            <?php $k++; } ?>
        </div>
      <?php $i++; } ?>  
       </div>
        <div class="col-sm-1"></div>
        <div class="col-sm-7">
           <?php $i=0; foreach($pickers as $key => $pickup) { ?> 
             <?php $k=1; foreach($pickup as $pickup_boy) { ?>
             
              <div class="seller_list <?php if($picker_cities[0]['pickup_city_code'] == $key && $k == 1) { echo "active"; } ?>" id="seller_<?php echo $key; ?>_<?php echo $pickup_boy['id']; ?>">
               
               <?php
                     $assign_ids = array();
                     $assign_ids = array_column($pickup_boy['assign_seller'], 'seller_id');
                    
                ?>

                <?php $j=1; foreach($pickup_boy['seller_list'] as $seller_list) {  ?>
                <div class="row">
                 <div class="col-sm-6"> <?php echo $j; ?>.  <?php echo $seller_list['company']; ?> (<?php echo $seller_list['nickname']; ?>) </div>
                 <div class="col-sm-6" style="text-align: right;"> 

                  <?php if(!in_array($seller_list['seller_id'], $assign_ids))
                        { ?>
                 <a href="javascript:;" onClick="assign_seller(<?php echo $pickup_boy['id']; ?>, <?php echo $seller_list['seller_id']; ?>, '<?php echo $key; ?>');">
                 Assign
                 </a>
                 <?php } else { ?>
                  Assigned to <?php echo $pickup_boy['assign_seller'][$seller_list['seller_id']]['first_name']; ?> <?php echo $pickup_boy['assign_seller'][$seller_list['seller_id']]['last_name']; ?>
                 <?php } ?>

                  </div> 
                </div> 
                <?php $j++; } ?>

                 <?php  foreach($pickup_boy['self_assign_seller'] as $seller_list) {  ?>
                <div class="row">
                 <div class="col-sm-6"> <?php echo $j; ?>.  <?php echo $seller_list['company']; ?> (<?php echo $seller_list['nickname']; ?>) </div>
                 <div class="col-sm-6" style="text-align: right;"> Assign by <?php echo $seller_list['first_name']; ?> <?php echo $seller_list['last_name']; ?> </div> 
                </div> 
                <?php $j++; } ?>


              </div>

              <?php $k++; } ?>
            <?php $i++; } ?>  
        </div>
       </div>
    </div>
</div> 
                   
            </div>  
        </div>
    </div>
</div>

<!-- Modal -->
<div id="Modal-Assign" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
    <form method="post" enctype="multipart/form-data" id="form-assign_seller" class="form-horizontal">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Assign Following Sellers</h4>
      </div>
      <div class="modal-body">
        <p><img src="view/image/ajax-loader.gif" /> Please wait...</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="assign_seller_save();">Assign</button>
      </div>
      </form>
    </div>
  </div>
</div>



</script>
<style type="text/css">
    .suborder_table table tr td {
        padding: 4px !important;
    }
</style> 

<script type="text/javascript">
$(document).ready(function() {
    $('.tabs .tab-links a').on('click', function(e)  {
        var currentAttrValue = $(this).attr('href');
 
        $('.tab').hide();
        // Show/Hide Tabs
        $('.tabs ' + currentAttrValue).show();
        $(currentAttrValue).children(".pickup_data").children("a").first().click();
 
        // Change/remove current tab to active
        $(this).parent('li').addClass('active').siblings().removeClass('active');
 
        e.preventDefault();
    });
});

$(document).ready(function() {
    $('.pickup_data a').on('click', function(e)  {
        var currentAttrValue = $(this).attr('href');
 
        $('.seller_list').hide();
        $(".pickup_data").removeClass('active');
        // Show/Hide Tabs
        $(currentAttrValue).show();
 
        // Change/remove current tab to active
        $(this).parent('div').addClass('active');
 
        e.preventDefault();
    });
});  

   function assign_seller(pickup_id=0, seller_id=0, city_code='')
    {    
         $("#Modal-Assign .alert").remove(); 
         var sellers   = [];
         sellers.push(seller_id);

        if(sellers.length == 0)
        {
            alert("Please select atleast one seller");
        }
        else
        {
            $("#Modal-Assign").modal("show");
            var ajax_url = 'index.php?route=operations/pickup/assign_seller&city_code=' + city_code +'&sellers=' + sellers+'&pickup_id=' + pickup_id +'&token=<?php echo $this->session->data["token"]; ?>'

            $.ajax({
            url: ajax_url,
            success: function( data ){
                  $("#Modal-Assign .modal-body").html(data);
                  var dateToday = new Date(); 
                  $('.date').datetimepicker({
                   pickDate: true,
                   pickTime: false,
                   minDate: dateToday
                  });
                }
            });

        }
    }  

function assign_seller_save()
{        
          $("#Modal-Assign .alert").remove(); 
         var actionurl = 'index.php?route=operations/pickup/save_assign_seller&token=<?php echo $this->session->data["token"]; ?>';
         var form_data = $("#form-assign_seller").serialize();
         ajax = $.ajax({
                url: actionurl,
                type: 'post',
                dataType: 'json',
                data: form_data,
                success: function(data)
                {
                  if(data['error'] == 1)
                    {
                       $("#Modal-Assign .modal-body").prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Error: '+data['error_msg']+'</div>');
                    }
                  else 
                    {
                        $("#Modal-Assign .modal-body").prepend('<div class="alert alert-success"><i class="fa fa-exclamation-circle"></i> Success: '+data['success_msg']+'</div>');
                        location.reload();
                    }     
                }
         });
}


</script>