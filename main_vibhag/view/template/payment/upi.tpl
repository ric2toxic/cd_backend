<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
  <div class="container-fluid">
  <div class="pull-right">
        <button type="submit" form="form-upi" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
 
    <ul class="breadcrumb">
      <?php foreach ($breadcrumbs as $breadcrumb) { ?>
      <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
      <?php } ?>
    </ul>
  </div>
  </div>
  <div class="container-fluid">
  <?php if (!empty($error_warning)) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
  <?php } ?>
  <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $breadcrumb['text']; ?></h3>
  </div>
 <div class="box">
<div class="content"> 
       <div class="panel-body">
         <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-upi" class="form-horizontal">
              <div class="form-group">
              </div>
       
         <!--<label class="col-sm-2 control-label" for="input-total"></label> -->
       <div class="col-sm-10"> 
      
      
		 <div class="form-group">
            <label class="col-sm-2 control-label" for="input-total"><?php echo $entry_module; ?></label>
              <div class="col-sm-10">
                  <select name="upi_module"id="input-status" class="form-control">
                    <?php $cm=explode('|',$entry_module_id);foreach($cm as $m){?>
                    <?php if ($upi_module == $m) { ?>
                    <option value="<?php echo $m; ?>" selected><?php echo $m; ?></option>
                    <?php } else { ?>
                       <option value="<?php echo $m; ?>"><?php echo $m; ?></option>
                    <?php }} ?>
                  </select>

              <!--<?php if (empty($upi_module)) { ?>
                  <span class="error text-danger"><?php echo $error_module; ?></span>
              <?php } ?>-->
              </div>
         </div>    
                            
             
		   <div class="form-group">
               <label class="col-sm-2 control-label" for="input-total"><?php echo $entry_merchant_name; ?></label>
                  <div class="col-sm-10">
                      <input type="text" name="upi_merchant_name" value="<?php echo $upi_merchant_name; ?>" id="input-sort-order"class="form-control" />
                    <?php if (empty($upi_merchant_name)) { ?>
                       <span class="error text-danger"><?php echo $error_merchant_name; ?></span>
                    <?php } ?>
                 </div>
	       </div>

      <div class="form-group">
               <label class="col-sm-2 control-label" for="input-total"><?php echo $entry_merchant_code; ?></label>
                  <div class="col-sm-10">
                      <input type="text" name="upi_merchant_code" value="<?php echo $upi_merchant_code; ?>" id="input-sort-order"class="form-control" />
                    <?php if (empty($upi_merchant_code)) { ?>
                       <span class="error text-danger"><?php echo $error_merchant_code; ?></span>
                    <?php } ?>
                 </div>
         </div>
               	  
		  <div class="form-group">
                <label class="col-sm-2 control-label" for="input-total"><?php echo $entry_wsb_vpa; ?></label>
                   <div class="col-sm-10">
                      <input type="text" name="upi_wsb_vpa" value="<?php echo $upi_wsb_vpa; ?>"  id="input-sort-order"class="form-control" />
                   <?php if (empty($upi_wsb_vpa)) { ?>
                      <span class="error text-danger"><?php echo $error_wsb_vpa; ?></span>
                   <?php } ?>
                </div> 
          </div>

      <div class="form-group">
               <label class="col-sm-2 control-label" for="input-total"><?php echo $entry_web_app_id; ?></label>
                 <div class="col-sm-10">
                    <input type="text" name="upi_web_app_id" value="<?php echo $upi_web_app_id; ?>"  id="input-sort-order"class="form-control" />
                   <?php if (empty($upi_web_app_id)) { ?>
                     <span class="error text-danger"><?php echo $error_web_app_id; ?></span>
                   <?php } ?>
                 </div> 
          </div>

		  <div class="form-group">
               <label class="col-sm-2 control-label" for="input-total"><?php echo $entry_app_app_id; ?></label>
                 <div class="col-sm-10">
                    <input type="text" name="upi_app_app_id" value="<?php echo $upi_app_app_id; ?>"  id="input-sort-order"class="form-control" />
                   <?php if (empty($upi_app_app_id)) { ?>
                     <span class="error text-danger"><?php echo $error_app_app_id; ?></span>
                   <?php } ?>
                 </div> 
          </div>


      <div class="form-group">
               <label class="col-sm-2 control-label" for="input-total"><?php echo $entry_vpa_mobile; ?></label>
                 <div class="col-sm-10">
                    <input type="text" name="upi_vpa_mobile" value="<?php echo $upi_vpa_mobile; ?>"  id="input-sort-order"class="form-control" />
                   <?php if (empty($upi_vpa_mobile)) { ?>
                     <span class="error text-danger"><?php echo $error_vpa_mobile; ?></span>
                   <?php } ?>
                 </div> 
          </div>
          
          <!--order_status-->
          
          <div class="form-group">
             <label class="col-sm-2 control-label" for="input-total"><?php echo $entry_order_status; ?></label>
               <div class="col-sm-10">
                 <select name="upi_order_status_id" id="input-status" class="form-control">
                    <?php foreach ($order_statuses as $order_status) { ?>
                    <?php if ($order_status['order_status_id'] == $upi_order_status_id) { ?>
                      <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                    <?php } else { ?>
                      <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                 </select>
               </div> 
          </div>
          
          <!--order_status-->          
          
          <!--order_fail_status-->
          
          <div class="form-group">
             <label class="col-sm-2 control-label" for="input-total"><?php echo $entry_order_fail_status; ?></label>
               <div class="col-sm-10">
                 <select name="upi_order_fail_status_id" id="input-status" class="form-control">
                    <?php foreach ($order_statuses as $order_status) { ?>
                    <?php if ($order_status['order_status_id'] == $upi_order_fail_status_id) { ?>
                      <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                    <?php } else { ?>
                      <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                 </select>
               </div> 
          </div>
          
          <!--order_fail_status-->    
          
          <div class="form-group">
              <label class="col-sm-2 control-label" for="input-total"><?php echo $entry_status; ?></label>
                 <div class="col-sm-10">
                   <select name="upi_status" id="input-status" class="form-control">
                      <?php if ($upi_status) { ?>
                         <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                         <option value="0"><?php echo $text_disabled; ?></option>
                      <?php } else { ?>
                         <option value="1"><?php echo $text_enabled; ?></option>
                         <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                      <?php } ?>
                   </select>
                 </div> 
          </div>
          
          <div class="form-group">
               <label class="col-sm-2 control-label" for="input-total"><?php echo $entry_sort_order; ?></label>
               	 <div class="col-sm-10">
                      <input type="text" name="upi_sort_order" value="<?php echo $upi_sort_order; ?>"  id="input-sort-order"class="form-control"size="1" />
                 </div>
          </div>
      </form>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>