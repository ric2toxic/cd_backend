
<?php echo $header;  ?><?php echo $column_left; ?>
<div id="content">
  <div id="price-pop" style="display:none; position:fixed; width:30%; height:250px; border:thick solid red; z-index:1000; background-color:white; left:32%; top:25%;">

          <h3 class="text-center">ENTER PRICE MARKUP PERCENT</h3>

      <br>
      <form name="price-form-name" id="price_form" action="javascript:void(0);" method="post">
          <div class="col-sm-12">
            <div class="form-group">
              <label class="control-label">Transfer Price Markup</label>
              <input type="text" name="field-price-pop" id="input-price-pop" placeholder="ENTER THE VALUE IN PERCENT" class="form-control" value='10' />
            </div>
            <div class="form-group">
              <label class="control-label">Commission</label>
              <input type="text" name="field-comm-pop" placeholder="ENTER COMMISSION IN PERCENT" id="input-comm-pop" class="form-control" value='<?php echo(DEFAULT_SELLER_COMMISSION + 5); ?>'  />

            </div>

            <input type="text" name="single_or_sor" id="single_or_sor" value="" readonly />

            <button class="btn btn-primary text-center" id="price_popup_ajax">ENTER</button>
          </div>
      </form>
  </div>
  <div class="page-header">
    <div class="container-fluid catalog_products">
      <div class="col-sm-5">
        <h1><?php echo $heading_title; ?></h1>
        <ul class="breadcrumb">
          <?php foreach ($breadcrumbs as $breadcrumb) { ?>
          <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
          <?php } ?>
        </ul>
      </div>
      <div class="col-sm-12">
    <div class="pull-left">
      
       <legend style="font-size:10px; color : red">*Rows marked orange are archived product</legend>
    </div>
        <div class="pull-right">
          <fieldset>
            <legend><?php echo 'Bulk CSV Download'; ?></legend>
            <form id="form_bulk_csv_download" action="<?php echo $apply_bulk_product_csv_download;?>" method="post">
              <select name="bulk_csv_download[]" class="form-control bulk_csv_download" id="bulk_csv_download" multiple="multiple">
                <?php if(!empty($product_table_column_name)) { ?>
                  <?php foreach($product_table_column_name as $table_name_key => $table_column_array){ ?>
                    <optgroup label="<?php echo ucwords(str_replace('_',' ',substr($table_name_key, 3)));?>">
                      <?php foreach($table_column_array as $field_key => $field_name) { ?>
                        <option value="<?php echo $field_name; ?>"><?php echo $field_name; ?></option>
                      <?php } ?>
                    </optgroup>
                  <?php } ?>
                <?php } ?>
              </select>
              <button type="button" class="btn btn-info btn-xs"  title="CSV Upload" onclick="return applyBulkCsvDownload();"><i class="fa fa-download"></i></button>
            </form>
          </fieldset>
            
          <fieldset>
            <legend>Bulk Update</legend>
              <select title="bulk_update" name="filter_bulk_update" class="bulk_update">
                <option value="">--Select Bulk Update--</option>
                <option value="1">Set/Update Seller</option>
                <option value="2">Change Product Status</option>
                <option value="4">Change Quantity</option>
                <option value="5">Archive Inventory</option>
                <?php if (!(isset($this->request->get['filter_franchise_tab']) && $this->request->get['filter_franchise_tab'] == '1')) { ?>
                <option value="3">Assign Store Code</option>
                <option value="6">Assign Exclusive</option>
                <option value="7">Update Minimum Quantity</option>
                <option value="8">Product convert To Single</option>
                <option value="10">Bulk Commission Update</option>
                <?php } ?>
                <option value="9">Product sync to solr</option>
                <option value="12">SOR Product Terms Update</option>
                <option value="13">SOR Product Terms Remove</option>
              </select>

              <div style="display: inline">
                <select title="seller list" name="filter_seller_list_value" class="seller_listing part_of_bulk_update hidden" id="bulk_update_block_1">
                  <option value=""><?php echo '--Select Seller--';?></option>
                    <?php
                        if(isset($seller_list)){
                        foreach ($seller_list as $sellers) {
                    ?>
                      <option value="<?php echo $sellers['seller_id']; ?>" >&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $sellers['nickname'];?></option>
                    <?php } } ?>
                </select>
              </div>  

              <div class="part_of_bulk_update hidden" id="bulk_update_block_12">
                 <select name="sor_type" style="width: 100%;">
                   <option value="regular">Regular</option>
                   <option value="wsb-credit">Wsb-Credit</option>
                  </select>
                  <br />
                  <input type="text" name="sor_days" placeholder="Sor Days" style="width: 100%; font-size: 12px; height: 18px; margin-bottom: 5px;" />
                   <br />
               </div>   

              <select title="product status list" name="filter_product_list" class="product_status_list part_of_bulk_update hidden" id="bulk_update_block_2">
                <option value=""><?php echo '--Select Status--';?></option>
                <?php
                    if(isset($product_status_list)){
                    foreach ($product_status_list as $product_status) {
                ?>  
                <option value="<?php echo $product_status['product_status_id']?>" >&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $product_status['name']; ?></option>
                <?php } } ?>
              </select>

              <select title="Assign store code" name="filter_assign_store_code" class="assgin_store_code part_of_bulk_update hidden" id="bulk_update_block_3">
                <option value=""><?php echo '--Select Code--';?></option>
                <?php
                    if(isset($store_sales_options)){
                    foreach ($store_sales_options as $store_sales_opt) {
                ?>
                <option value="<?php echo $store_sales_opt;?>" >&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $store_sales_opt; ?></option>
                <?php } } ?>
              </select>

              <input  type="text" placeholder="integer > 0 " name="filter_product_quantity" class="product_quantity part_of_bulk_update hidden" id="bulk_update_block_4" size="8">

              <select title="archive" name="filter_archive_list_value" class="archive_listing part_of_bulk_update hidden" id="bulk_update_block_5">
                <option value=""><?php echo '--Select Archive--';?></option>
                <?php
                    if(isset($archive_inventory)){
                    foreach ($archive_inventory as $key=>$archive_status) {
                ?>
                <option value="<?php echo $key; ?>" >&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $archive_status;?></option>
                <?php } } ?>
              </select>

              <select title="exclusive" name="filter_exclusive_list_value" class="exclusive_listing part_of_bulk_update hidden" id="bulk_update_block_6">
                <option value=""><?php echo '--Select Exclusive--';?></option>
                <?php
                    if(isset($exclusive_options)){
                    foreach ($exclusive_options as $exclusive_option) {
                ?>
                <option value="<?php echo $exclusive_option; ?>" >&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $exclusive_option;?></option>
                <?php } }  ?>
              </select>
              <input  type="text" placeholder="integer >= 1 " name="update_minimum_quantity" class="minimum_quantity part_of_bulk_update hidden" id="bulk_update_block_7" size="8">
              <input  type="text" placeholder="integer >= 1 " name="bulk_commission_update" class="bulk_commission_update part_of_bulk_update hidden" id="bulk_update_block_10" size="8">
              <button type="button" data-toggle="tooltip" title="<?php echo 'Save'; ?>" class="btn btn-primary bulk_update_button btn-xs"><i class="fa fa-save"></i></i></button>
          </fieldset>
          <?php if (!(isset($this->request->get['filter_franchise_tab']) && $this->request->get['filter_franchise_tab'] == '1')) { ?>
          <button type="button" data-toggle="tooltip" title="<?php echo 'Product Rating'; ?>" class="btn product_rating"><span class="fa fa-stack"><i class="fa fa-star fa-stack-1x"></i><i class="fa fa-star-o"></i></span></button>
          <a href="javascript:void(0);" title="" class="btn ms-button ms-button-mark bulk-button-mark" onclick="/*$('#form-product').attr('action','<?php echo $moderate_approve; ?>').submit();*/"></a>
          <a href="javascript:void(0);" title="" class="btn ms-button ms-button-delete bulk-button-cross" onclick="/*$('#form-product').attr('action','<?php echo $moderate_reject; ?>').submit();*/"></a>
          <a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
          <button type="button" data-toggle="tooltip" title="<?php echo $button_copy; ?>" class="btn btn-default" onclick="$('#form-product').attr('action', '<?php echo $copy . '&external_copy=1'; ?>').submit()"><i class="fa fa-copy"></i></button>
          <button type="button" data-toggle="tooltip" id="copy_single" title="<?php echo $button_copy_single; ?>" class="btn btn-default"><i class="fa fa-cc"></i></button>
          <?php /* ?> <button type="button" data-toggle="tooltip" id="copy_sor" title="<?php echo $button_copy_sor; ?>" class="btn btn-default"><i class="fa fa-cc"></i></button> <?php */ ?>
          <?php } ?>
          <button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-product').submit() : false;"><i class="fa fa-trash-o"></i></button>
        </div>
      </div>
    </div>
  </div>
  
  <div class="container-fluid product_list">
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
       <div class="row">
         <?php if (!(isset($this->request->get['filter_franchise_tab']) && $this->request->get['filter_franchise_tab'] == '1')) { ?>
         <div class="col-sm-3">
           <div class="form-group product_filter_seller_list ">
             <label class="control-label" for="input-name"><?php echo 'Seller List'; ?></label>
             <select title="Stores" name="filter_seller_list" class="form-control select_store_list">
               <option value="all"><?php echo '--Select All--';?></option>
               <?php if(isset($filter_seller_list) && $filter_seller_list == 'null'){ $selected_id = 'selected'; } else { $selected_id = '';} ?>
               <option value="null" <?php echo $selected_id;?>>--UnAssigned--</option>
               <?php
                    if(isset($seller_list)){
                    foreach ($seller_list as $sellers) { ?>
               <?php
                      if($sellers['seller_id'] == $filter_seller_list){
                        $selected_id = 'selected';
                      }else{
                        $selected_id = '';
                      }
                  ?>
               <option value="<?php echo $sellers['seller_id']; ?>" <?php echo $selected_id;?>>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $sellers['nickname'];?></option>
               <?php } }?>
             </select>

           </div>
         </div>
         <?php } ?>
         <div class="col-sm-3">
           <div class="form-group product_filter_seller_list ">
             <label class="control-label" for="input-category"><?php echo 'Categories'; ?></label>
             <select name="filter_category" id="input-category" class="form-control">
               <option value="0">All Categories</option>
                <?php if(!empty($categories))  { ?>

                  <?php foreach ($categories as $category_1) { ?>

                      <?php if (!empty($category_1['category_id']) && (!empty($filter_category) && $category_1['category_id'] == $filter_category)) { ?>
                        <option value="<?php echo $category_1['category_id']; ?>" selected="selected"><?php echo $category_1['name']; ?></option>
                     <?php } else { ?>
                        <option value="<?php echo $category_1['category_id']; ?>"><?php echo $category_1['name']; ?></option>
                     <?php } ?>

                      <?php if(!empty($category_1['children'])) { ?>

                        <?php foreach ($category_1['children'] as $category_2) { ?>

                            <?php if (!empty($category_2['category_id']) &&  (!empty($filter_category) &&  $category_2['category_id'] == $filter_category)) { ?>
                              <option value="<?php echo $category_2['category_id']; ?>" selected="selected">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_2['name']; ?></option>
                           <?php } else { ?>
                              <option value="<?php echo $category_2['category_id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_2['name']; ?></option>
                           <?php } ?>

                              <?php if(!empty($category_2['children'])) { ?>

                                <?php foreach ($category_2['children'] as $category_3) { ?>

                                   <?php if (!empty($category_3 ['category_id']) &&  (!empty($filter_category) && $category_3['category_id'] == $filter_category)) { ?>
                                     <option value="<?php echo $category_3['category_id']; ?>" selected="selected">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_3['name']; ?></option>
                                   <?php } else { ?>
                                     <option value="<?php echo $category_3['category_id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_3['name']; ?></option>
                                   <?php } ?>

                                <?php } //category_2 loop ?> 

                            <?php } //category_2 empty check ?>

                        <?php } //category_1 loop ?>                        

                       <?php } //category_1 empty check ?>

                  <?php } //first loop ?>

                <?php } //outer empty check ?>
             </select>
           </div>
         </div>
         <div class="col-sm-3">
          <form id="form_bulk_csv_upload" action="<?php echo $apply_bulk_product_csv_upload;?>" method="post" enctype= "multipart/form-data">
            <div class="form-group product_filter_seller_list ">
              <label class="control-label" for="input-category"><?php echo 'Bulk CSV Upload'; ?></label>
              <input type="file" name="bulk_csv_upload" class="bulk_csv_upload" id="bulk_csv_upload">
              <button type="button" class="btn btn-warning btn-xs"  title="CSV Upload" onclick="return applyBulkCsvUpload(this);"><i class="fa fa-upload"></i></button>
            </div>
          </form>
         </div>
       </div>

      </div>

      <div class="panel-body">
        <div class="well">
          <div class="row">
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-name"><?php echo $entry_name; ?></label>
                <input type="text" name="filter_name" value="<?php echo $filter_name ?? ''; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
              </div>
              <div class="form-group row">
                <div class="col-sm-10">
                  <label class="control-label" for="input-model"><?php echo $entry_wsb_product_code; ?></label>
                  <input type="text" name="filter_model" value="<?php echo $filter_model ?? ''; ?>" placeholder="<?php echo $entry_model; ?>" id="input-model" class="form-control" />
                </div>
                <div class="col-sm-2">
                  <label class="control-label" for="input-model">&nbsp;</label>
                  <div class="btn btn-primary wsb_code_feature_btn"><i class="fa fa-plus"></i></div>
                </div>
                <?php $style=''; if(!empty($filter_model) &&  (!empty($filter_type_string) OR (!empty($filter_val_from) && !empty($filter_val_to)) )){ $style = "style='display:block;'";} ?>
                <div class="filter_feature_box well filter_feature_box_wsb_code" <?php echo $style; ?> >
                  <div class="model_filter_feature_box_close filter_feature_box_close"><i class="fa fa-times"></i></div>
                  <div class="form-group row">
                    <div class="col-sm-6">
                      <select name="filter_feature_box_operator" class="form-control">
                        <?php $operate = array('AND' => 'AND', 'OR' =>'OR');?>
                        <?php foreach($operate as $optor_key => $optor_val){ ?>
                          <option value="<?php echo $optor_key; ?>" <?php echo (!empty($filter_operator) && $optor_key == $filter_operator) ?  'selected': ''; ?>><?php echo $optor_val; ?></option>
                        <?php } ?>
                      </select>
                    </div>
                    <div class="col-sm-6">
                      <select name="filter_feature_box_type" class="form-control">
                        <?php $filter_type = array('string' => 'String', 'integer' =>'Integer');?>
                        <?php foreach($filter_type as $filter_type_key => $filter_type_val){ ?>
                          <option value="<?php echo $filter_type_key; ?>" <?php echo (!empty($filter_feature_box_type) && $filter_type_key == $filter_feature_box_type) ? 'selected' : ''; ?>><?php echo $filter_type_val; ?></option>
                        <?php } ?>
                      </select>
                    </div>
                    <!-- <div class="col-sm-2">
                      <span class="btn btn-primary"><i class="fa fa-plus"></i></span>
                    </div> -->                      
                  </div>
                  <div class="form-group row form_group_string">
                    <div class="col-sm-12">
                      <input type="text" name="filter_feature_box_string" value="<?php echo $filter_type_string ?? ''; ?>" placeholder="Ex: ABT;PTS" class="form-control">
                    </div>
                  </div>
                  <div class="form-group row form_group_integer hidden">
                    <div class="col-sm-6">
                      <input type="text" name="filter_feature_box_from" value="<?php echo $filter_val_from ?? '';?>" placeholder="From" class="form-control">
                    </div>
                    <div class="col-sm-6">  
                      <input type="text" name="filter_feature_box_to" value="<?php echo $filter_val_to ?? '';?>" placeholder="To" class="form-control">
                    </div>
                  </div>      
                </div>
              </div>
              <?php if (!(isset($this->request->get['filter_franchise_tab']) && $this->request->get['filter_franchise_tab'] == '1')) { ?>
              <div class="form-group pull-left">
                <label><input type="checkbox" name="filter_non_single" <?php if (!empty($filter_non_single) && $filter_non_single) { echo " checked";} ?> class="control-label"/></label>
                <label class="control-label">Products which are not singles</label>
                <br />
                <label><input type="checkbox" name="filter_non_sor" <?php if (!empty($filter_non_sor) && $filter_non_sor) { echo " checked";} ?> class="control-label"/></label>
                <label class="control-label">Products which are not SOR</label>
                <br />
                <label><input type="checkbox" name="filter_images" <?php if (!empty($filter_images) && $filter_images) { echo " checked";} ?> class="control-label"/></label>
                <label class="control-label">Products with No Images</label>
              </div>
              <?php } ?>
            </div>
            <div class="col-sm-3">
              <div class="form-group pull-left">
                <label class="control-label" for="input-price"><?php echo $entry_price; ?></label><br/>
                <div class="col-sm-7 row">
                  <input type="text" name="filter_price_from" value="<?php echo $filter_price_from ?? ''; ?>" placeholder="<?php echo $entry_price_from; ?>" id="input-price" class="form-control" />
                </div>
                <div class="col-sm-7 row">
                  <input type="text" name="filter_price_to" value="<?php echo $filter_price_to ?? ''; ?>" placeholder="<?php echo $entry_price_to; ?>" id="input-price" class="form-control" />  
                </div>
              </div>
              <div class="form-group row">
                <div class="col-sm-10">
                  <label class="control-label" for="input-quantity"><?php echo $entry_seller_sku; ?></label>
                  <input type="text" name="filter_seller_sku" value="<?php echo $filter_seller_sku ?? ''; ?>" placeholder="<?php echo 
                  $entry_seller_sku; ?>" id="input-quantity" class="form-control" />
                </div>
                <div class="col-sm-2">
                  <label class="control-label" for="input-model">&nbsp;</label>
                  <div class="btn btn-primary sllr_sku_feature_btn"><i class="fa fa-plus"></i></div>
                </div>
                <?php $style=''; if(!empty($filter_seller_sku) && (!empty($sllr_sku_filter_type_string) OR (!empty($sllr_sku_filter_val_from) && !empty($sllr_sku_filter_val_to))) ){ $style = "style='display:block;'";} ?>
                <div class="filter_feature_box well filter_feature_box_sllr_sku" <?php echo $style; ?> >
                  <div class="sllr_sku_filter_feature_box_close filter_feature_box_close"><i class="fa fa-times"></i></div>
                  <div class="form-group row">
                    <div class="col-sm-6">
                      <select name="sllr_sku_filter_feature_box_operator" class="form-control">
                        <?php $operate = array('AND' => 'AND', 'OR' =>'OR');?>
                        <?php foreach($operate as $optor_key => $optor_val){ ?>
                          <option value="<?php echo $optor_key; ?>" <?php echo (!empty($sllr_sku_filter_operator) && $optor_key == $sllr_sku_filter_operator) ?  'selected': ''; ?>><?php echo $optor_val; ?></option>
                        <?php } ?>
                      </select>
                    </div>
                    <div class="col-sm-6">
                      <select name="sllr_sku_filter_feature_box_type" class="form-control">
                        <?php $filter_type = array('string' => 'String', 'integer' =>'Integer');?>
                        <?php foreach($filter_type as $filter_type_key => $filter_type_val){ ?>
                          <option value="<?php echo $filter_type_key; ?>" <?php echo (!empty($sllr_sku_filter_feature_box_type) && $filter_type_key == $sllr_sku_filter_feature_box_type) ?  'selected': ''; ?>><?php echo $filter_type_val; ?></option>
                        <?php } ?>
                      </select>
                      </select>
                    </div>
                  </div>
                  <div class="form-group row sllr_sku_form_group_string">
                    <div class="col-sm-12">
                      <input type="text" name="sllr_sku_filter_feature_box_string" value="<?php echo $sllr_sku_filter_type_string ?? ''; ?>" placeholder="Ex: ABT;PTS" class="form-control">
                    </div>
                  </div>
                  <div class="form-group row sllr_sku_form_group_integer hidden">
                    <div class="col-sm-6">
                      <input type="text" name="sllr_sku_filter_feature_box_from" value="<?php echo $sllr_sku_filter_val_from ?? ''; ?>" placeholder="From" class="form-control">
                    </div>
                    <div class="col-sm-6">  
                      <input type="text" name="sllr_sku_filter_feature_box_to" value="<?php echo $sllr_sku_filter_val_to ?? ''; ?>" placeholder="To" class="form-control">
                    </div>
                  </div>      
                </div>
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
            <div class="col-sm-3">
              <div class="form-group pull-left row">
                <div class="col-sm-12">
                  <label class="control-label" for="input-status"><?php echo $entry_status;?></label>
                  <select name="filter_status" id="input-status" class="form-control">
                    <option value="*">---Select Status---</option>
                    <?php
                        if(isset($product_status_list)){
                          foreach($product_status_list as $product_status){
                            if(isset($filter_status) && $product_status['product_status_id'] == $filter_status){
                              $selected_status = 'selected';
                            }else{
                              $selected_status = '';
                            }
                    ?>
                      <option value="<?php echo $product_status['product_status_id']?>" <?php echo $selected_status;?> ><?php echo $product_status['name']; ?></option>
                    <?php } } ?>
                  </select>
                </div>
                <?php if (!(isset($this->request->get['filter_franchise_tab']) && $this->request->get['filter_franchise_tab'] == '1')) { ?>
                <div class="col-sm-12">
                  <label class="control-label" for="input-store-sales-code"><?php echo $entry_store_sales_code; ?></label>
                  <select name="filter_store_sales_code" id="input-store-sales-code" class="form-control">
                    <option value="*">--Select Sales Code--</option>
                    <?php
                        if(isset($store_sales_options)){
                          foreach($store_sales_options as $store_sale_code){
                            if(isset($filter_store_sales_code) && $store_sale_code == $filter_store_sales_code){
                              $selected_status = 'selected';
                            }else{
                              $selected_status = '';
                            }
                    ?>
                      <option value="<?php echo $store_sale_code?>" <?php echo $selected_status;?> ><?php echo $store_sale_code; ?></option>
                    <?php } } ?>
                  </select>
                </div>
                <?php } ?>
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-hsn-code"><?php echo $entry_hsn_code; ?></label><br/>
                <input type="text" name="filter_hsn_code" value="<?php echo $filter_hsn_code ?? ''; ?>" placeholder="<?php echo $entry_hsn_code; ?>" id="input-hsn-code" class="form-control" />
              </div>
            </div>

             <div class="col-sm-3">
              <div class="form-group pull-left">
                <label class="control-label" for="input-commission"><?php echo $entry_commission; ?></label><br/>
                <div class="col-sm-7 row">
                  <input type="text" name="filter_commission_from" value="<?php echo $filter_commission_from ?? ''; ?>" placeholder="<?php echo $entry_commission_form; ?>" id="input-commission-form" class="form-control box_50_per" />
                </div>
                <div class="col-sm-7 row">
                  <input type="text" name="filter_commission_to" value="<?php echo $filter_commission_to ?? ''; ?>" placeholder="<?php echo $entry_commission_to; ?>" id="input-commission-to" class="form-control box_50_per" />  
                </div>
              </div>
            </div>

            <?php if (isset($this->request->get['filter_franchise_tab']) && $this->request->get['filter_franchise_tab'] == '1') { ?>
            <div class="col-sm-4">
            <label class="control-label"><?php echo "Franchise List"; ?></label>
            <input type="hidden" name="filter_franchise_tab" id="filter_franchise_tab" value="<?php echo $filter_franchise_tab;?>">
                  <select name="filter_franchise_id" id="input_franchise_id" class="form-control">
                    <option value="">--Select Franchise--</option>
                    <?php
                        if(isset($franchise_lists)){
                          foreach($franchise_lists as $franchise_list){
                            if($franchise_list['customer_id'] == $filter_franchise_id){
                              $selected_status = 'selected';
                            }else{
                              $selected_status = '';
                            }
                    ?>
                      <option value="<?php echo $franchise_list['customer_id']; ?>" <?php echo $selected_status; ?> > <?php echo $franchise_list['franchise_name']; ?></option>
                    <?php } } ?>
                  </select>
            </div>
            <?php } ?>
            
             <div class="col-sm-2" style="padding-top:20px; float: right">
              <button type="button" id="button-filter" class="btn btn-primary pull-right button-filter" style="margin-right: 5px;"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
            </div>
          </div>
        </div>

        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-product">

          <div class="table-responsive">
            <table class="table table-bordered table-hover">
   <thead>
                <tr>
                  <td rowspan="2" style="width: 1px;" class="text-center">
                    <input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" id="group_checkbox" />
                  </td>
                  <td rowspan="2" class="text-center"><?php echo $column_image; ?></td>

                  <td rowspan="2" class="text-left"><?php echo $column_name; ?></td>
                  <td rowspan="2" class="text-left"><?php echo $column_wsb_product_code; ?></td>
                  <td rowspan="2" class="text-left"><?php if (!empty($sort) && $sort == 'p.sku') { ?>
                    <a href="<?php echo $sort_seller_sku; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_seller_sku; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_seller_sku; ?>"><?php echo $column_seller_sku; ?></a>
                    <?php } ?></td>
                  <td class="text-center" colspan="2"><?php if (!empty($sort) && $sort == 'p.price') { ?>
                    <a href="<?php echo $sort_price; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_price; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_price; ?>"><?php echo $column_price ; ?></a>
                    <?php } ?></td>
                  <td colspan="1" >Selling Price</td>
                  <td rowspan="2" class="text-right"><?php echo 'Set Description';?></td>
                  <td rowspan="2" class="text-right"><?php if (!empty($sort) && $sort == 'p.quantity') { ?>
                    <a href="<?php echo $sort_quantity; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_quantity; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_quantity; ?>"><?php echo $column_quantity; ?></a>
                    <?php } ?></td>
                   <td rowspan="2" class="text-left"><?php echo "Stock Status Info"; ?></td>
                  <td rowspan="2" class="text-left"><?php if (!empty($sort) && $sort == 'p.status') { ?>
                    <a href="<?php echo $sort_status; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_status; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_status; ?>"><?php echo $column_status; ?></a>
                    <?php } ?></td>
                  <td rowspan="2">Sort Order</td>  
                  <td rowspan="2" class="text-right"><?php echo $column_action; ?></td>
                </tr>

                <!-- this table row for Transfer price with divided 3 column -->
                <tr>
                  <td class="text-center">Input Seller Tax Rate</td>
                  <td rowspan="2" class="text-center">
                    <?php if (!empty($sort) && $sort == 'p.commission') { ?>
                    <a href="<?php echo $sort_commission; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_commission; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_commission; ?>"><?php echo $column_commission ; ?></a>
                    <?php } ?>
                    </td>
                  <td class="text-center">Output Tax Rate</td>
                </tr>
              </thead>
              <tbody>
                <?php if ($products) { ?>
                <?php foreach ($products as $product) { ?>
                <tr class="moderated_<?php echo $product['product_id'];?>" <?php 
          if($product['is_archived']) {?> style="background-color:#ffc966" <?php }?> > 
                  <td rowspan="2" class="text-center"><?php if (in_array($product['product_id'], $selected)) { ?>
                    <input id="checkbox_<?php echo $product['product_id']; ?>" data-sorname="<?php echo $product['sku']; ?>" data-sorproduct="<?php if($product_edit_enable!=1){echo $product['sor_product'];}?>" type="checkbox" name="selected[]" value="<?php echo $product['product_id']; ?>" product-status-id = "<?php echo $product['product_status_id'];?>" checked="checked" data-seller-id="<?php echo $product['seller_id']; ?>" data-product-rating="<?php echo $product['product_rating']; ?>"
                    data-franchise-id="<?php echo $product['franchise_id']; ?>" data-product-has-option="<?php echo $product['has_product_option']; ?>"
                    data-product-model="<?php echo $product['model']; ?>"/>
                    <?php } else { ?>
                    <input id="checkbox_<?php echo $product['product_id']; ?>" data-sorname="<?php echo $product['sku']; ?>"  data-sorproduct="<?php if($product_edit_enable!=1){echo $product['sor_product'];}?>" type="checkbox" name="selected[]" value="<?php echo $product['product_id']; ?>" product-status-id = "<?php echo $product['product_status_id'];?>" data-seller-id="<?php echo $product['seller_id']; ?>" data-product-rating="<?php echo $product['product_rating']; ?>" data-franchise-id="<?php echo $product['franchise_id']; ?>" data-product-has-option="<?php echo $product['has_product_option']; ?>" data-product-model="<?php echo $product['model']; ?>"/>
                    <?php } ?></td>
                  <td rowspan="2" class="text-center"><?php if ($product['image']) { ?>
                    <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="img-thumbnail" />
                    <?php } else { ?>
                    <span class="img-thumbnail list"><i class="fa fa-camera fa-2x"></i></span>
                    <?php } ?></td>
                  <td rowspan="2" class="text-left order_list_comment product_weight" data-product-id="<?php echo $product['product_id'];?>">
                    <span class="product_list_class" id="product_weight_<?php echo $product['product_id'];?>">
                      <?php echo 'Weight : '. number_format($product['weight'], 3, '.', ''). ' Kg';?>
                    </span>
                    <span id="prod_weight_<?php echo $product['product_id'];?>" style="display: none;">
                      <input type="text" name="product_weight_update" id="input_weight_<?php echo $product['product_id'];?>" data-product-id = "<?php echo $product['product_id'];?>" value="<?php echo $product['weight']?>" style="width: 80px;" data-old-value="<?php echo $product['weight']?>">
                      <span class="weight_close_input" data-product-id="<?php echo $product['product_id']; ?> "><i class="fa fa-ban"></i> </span>
                    </span>
                    <br>
                    <?php echo $product['name']; ?>
                     <?php if(!empty($product['product_option_name'])) { ?>
                      <br>
                      <span class="btn btn-success btn-xs"><?php echo $product['product_option_name'].' option'; ?></span>
                    <?php } ?>
                  </td>
                  <td rowspan="2" class="text-left">
                    <sup class="product_star">
                        <label><?php echo $product['product_rating']; ?></label>
                        <i class="fa fa-star fa-stack-1x"></i>
                    </sup>
                    <br/>
                    <?php echo $product['model']; ?> <br/>
                    <?php if( $product['store_sales'] ) { ?>
                      <span class="store_sales_span"> <?php echo $product['store_sales']; ?> </span>
                    <?php } ?>
                    <?php if( $product['hsn_code'] ) { ?>
          <span class="store_sales_span"> <?php echo $product['hsn_code']; ?> </span>
                    <?php } ?>
                  </td>
                  <td rowspan="2" class="text-left">
                    <?php echo $product['sku'];?> <br />
                    <b><?php echo $product['exclusive'];?></b> <br />
                    <?php if ( $product['nickname'] ) { ?>
                      <span class="store_sales_span">
                        <?php echo $product['nickname']; ?>
                      </span>
                    <?php } ?>

                  </td>
                  <td colspan="2"  class="text-center transfer_price"data-product-id="<?php echo $product['product_id'];?>">
                    <span id="transfer_price_<?php echo $product['product_id'];?>">
                      <?php if ($product['special']) { ?>
                      <span style="text-decoration: line-through;"><?php echo $product['price']; ?></span><br/>
                      <div class="text-danger"><?php echo $product['special']; ?></div>
                      <?php } else { ?>
                      <?php echo $product['price']; ?>
                      <?php } ?>
                    </span>
                    <span id="trans_price_<?php echo $product['product_id'];?>" style="display: none;">
                      <input type="text" name="transfer_price_update" id="input_price_<?php echo $product['product_id'];?>" data-product-id = "<?php echo $product['product_id'];?>" value="<?php echo $product['price']?>" style="width: 70px;" data-old-value="<?php echo $product['price']?>">
                      <span class="price_close_input" data-product-id="<?php echo $product['product_id']; ?>"><i class="fa fa-ban"></i> </span>
                    </span>
                  </td>
                  <td><?php echo $product['selling_price'];?></td>
                  <!--<td rowspan="2" class="text-right"><?php echo $product['commission']; ?></td>-->
                  <!--<td rowspan="2" class="text-right"><?php echo $product['commission']; ?></td>-->
                  <td rowspan="2" class="text-left order_list_comment">
                    <span class="product_list_class"><?php echo 'Piece in set : '. $product['piece_in_set'];?></span> <br>
                    <?php echo $product['set_description']; ?>
                  </td>
                  <?php
                    $sor_stock = '';
                  if ($product['seller_invoice_generate_status']==1 || $product_edit_enable) {
                    $sor_stock = 'product_quantity';
                  }
                  ?>
                  <td rowspan="2" class="text-right <?php echo $sor_stock;?> " data-product-id="<?php echo $product['product_id'];?>">
                    <span id="product_quantity_<?php echo $product['product_id'];?>">
                      <?php if ($product['quantity'] <= 0) { ?>
                      <span class="label label-warning"><?php echo $product['quantity']; ?></span>
                      <?php } elseif ($product['quantity'] <= 5) { ?>
                      <span class="label label-danger"><?php echo $product['quantity']; ?></span>
                      <?php } else { ?>
                      <span class="label label-success"><?php echo $product['quantity']; ?></span>
                      <?php } ?>
                    </span>
                    <span id="prod_quantity_<?php echo $product['product_id'];?>" style="display: none;">
                      <input type="text" name="product_quantity_update" id="input_quantity_<?php echo $product['product_id'];?>" data-product-id = "<?php echo $product['product_id'];?>" value="<?php echo $product['quantity']?>" style="width: 30px;"  data-old-value="<?php echo $product['quantity']?>">
                      <span class="quantity_close_input" data-product-id="<?php echo $product['product_id']; ?>"><i class="fa fa-ban"></i> </span>
                    </span>
                  </td>

                   <td rowspan="2" class="text-right" data-product-id="<?php echo $product['product_id'];?>">
                    <span <?php echo $product['product_id'];?>>
                      <?php if(!empty($product['stock_status_info']['stock'])) { ?>
              <span class="label label-success"><?php echo "in stock"; }?> </span>
              <span class="label label-danger"><?php  if(empty($product['stock_status_info']['stock'])) {
                echo "out of stock" ; echo "<br>";
                foreach($product['stock_status_info']['reason'] as $key=>$value) {
                echo "$value";
                echo "<br>";
              } }?></span>
            <span class="label label-danger"><?php ?></span>
                    </span>
                  </td>


                  <td rowspan="2" class="text-left"><?php echo $product['status']; ?></td>
                  <td rowspan="2"><?php echo $product['sort_order'];?></td>
                  <td rowspan="2" class="text-right">
                    <div class="main_content_div_<?php echo $product['product_id'];?>">
                      <?php if($product['product_status_id'] == 2){ ?>
                        <a class='ms-button ms-button-edit' href="<?php echo $product['edit'];?>" title="<?php echo $button_edit; ?>" target="_blank"></a>
                        <a href="javascript:void(0);" html-data="<?php echo $product['product_id'];?>" title="" class="ms-button ms-button-mark moderate_tick" id="moderate_tick_<?php echo $product['product_id'];?>"></a>
                        <a href="javascript:void(0);" html-data="<?php echo $product['product_id'];?>" title="" class="ms-button ms-button-delete moderate_cross" id="moderate_cross_<?php echo $product['product_id'];?>"></a>
                      <a href="<?php echo $product['products_order_list']; ?>" target="_blank" data-toggle="tooltip" title="Products Order" class="btn btn-info btn-sm"><i class="fa fa-bar-chart"></i></a>
                      <?php } else { ?>
                        <a href="<?php echo $product['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i></a>
                        <a href="<?php echo $product['products_order_list']; ?>" target="_blank" data-toggle="tooltip" title="Products Order" class="btn btn-info btn-sm"><i class="fa fa-bar-chart"></i></a>
                      <?php } ?>
                      <a href="<?php echo $product['merge_different_design_product']; ?>" target="_blank" data-toggle="tooltip" title="Separate Different Design in Products" class="btn btn-warning btn-sm"><i class="fa fa-picture-o"></i></a>
                    </div>
                    <div class="edit_selling_price_div_<?php echo $product['product_id'];?>">
                      <button type="button" id="edit_selling_<?php echo $product['product_id'];?>" data-toggle="tooltip" title="Edit Hidden Selling Price" data-product_id = "<?php echo $product['product_id'];?>" class="btn btn-info btn-sm edit_selling_price" ><i class="fa fa-eye-slash"></i></a></button>

                      <span id="edit_selling_price_span_<?php echo $product['product_id'];?>" style="display: inline;" class="hidden">
                        <input type="text" name="special_selling_price" id="input_edit_selling_price_<?php echo $product['product_id'];?>" data-product-id="<?php echo $product['product_id'];?>" value="<?php echo (int) $product['hidden_selling_price'];?>" style="width: 70px;" data-old-value="<?php echo (int) $product['hidden_selling_price'];?>">
                        <span class="price_edit_selling_input" data-product-id="<?php echo $product['product_id'];?>"><i class="fa fa-ban"></i> </span>
                    </span>
                    </div>
                  </td>
                </tr>
                <!-- this table row for Transfer price with divided 3 column -->
                <tr class="moderated_<?php echo $product['product_id'];?>" <?php 
          if($product['is_archived']) {?> style="background-color:#ffc966" <?php }?> > 
                  <td class="text-center"><?php echo $product['seller_tax'];?></td>
                  <td class="text-center product_commission" data-product-id="<?php echo $product['product_id'];?>">
                     <span id="product_commission_<?php echo $product['product_id'];?>">
                       <?php echo $product['commission'];?>
                    </span>
                    <span id="prod_comm_<?php echo $product['product_id'];?>" style="display: none;">
                      <input type="text" name="product_commission_update" id="input_commission_<?php echo $product['product_id'];?>" data-product-id = "<?php echo $product['product_id'];?>" value="<?php echo $product['commission']?>" style="width: 50px;" data-old-value="<?php echo $product['commission']?>">
                      <span class="commission_close_input" data-product-id="<?php echo $product['product_id']; ?>"><i class="fa fa-ban"></i> </span>
                    </span>
                  </td>
                  <td class="text-center"><?php echo $product['output_tax_rate'];?></td>
                </tr>
                <?php } ?>
                <?php } else { ?>
                <tr>
                  <td class="text-center" colspan="11"><?php echo $text_no_results; ?></td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
          <!-- rating for selected products  -->
          <div class="product_rating_popup">
            <button type="button" class="close new_close" data-dismiss="modal" aria-hidden="true">x</button>
            <div class="rating_popup_content">
              <div class="well">
                <div class="row">
                  <div class="col-sm-12">
                    <div class="form-group">
                      <label class="control-label" for="input-product-ratings"><?php echo $entry_product_rating; ?></label>
                      <select name="product_ratings" id="select-product-ratings" class="form-control">
                        <option value="">--Select--</option>
                        <?php foreach(PRODUCT_RATING_CONFIG as $rating_key => $rating_title) { ?>
                          <option value="<?php echo $rating_key;?>"><?php echo $rating_title;?></option>
                        <?php } ?>
                      </select>
                    </div>
                    <button type="button" data-toggle="tooltip" title="<?php echo $button_product_rating; ?>" class="btn btn-primary pull-right product_rating_button"><?php echo $button_product_rating;?></button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Trigger the modal with a button -->
          <button type="button" class="btn btn-info btn-lg hidden model_popup_button" data-toggle="modal" data-target="#myModal">Open Modal</button>

          <!-- Modal for product have contain  options-->
          <div id="myModal" class="modal fade prevent_quantity_update" role="dialog" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog">

              <!-- Modal content-->
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close product_list_popup_close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title">Below products have options, hence quantity cannot be updated.</h4>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-default product_list_popup_close" data-dismiss="modal">Close</button>
                  <button type="button" class="btn btn-primary product_list_popup_continue" data-dismiss="modal">Continue</button>
                  <button type="button" class="btn btn-default product_list_popup_ok hidden" data-dismiss="modal">OK</button>
                </div>
              </div>

            </div>
          </div>
          <!-- -->
        </form>
        <div class="row"><?php echo $pagination; ?></div>
        <!-- <div class="row">
          <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-6 text-right"><?php //echo $results; ?></div>
        </div> -->
      </div>
    </div>
  </div>
<script><!--
  $(document).ready(function(){
    $('#filter_solr_enable').click(function() {
      
      if(this.checked == true && $('select[name=\'filter_seller_list\']').val()!='null') {
        $(this).val(1);        
      } else {
        $(this).val(0);        
      }

      if($(this).val() == 1) {
        $('input[name=\'filter_search_like_web_enabled\']').val(0).prop('disabled', false);
      }else{
        $('input[name=\'filter_search_like_web_enabled\']').val(0).prop('checked',false);
        $('input[name=\'filter_search_like_web_enabled\']').val(0).not(':disabled').prop('disabled', true);
      }

    });
    //search like web
     $('#filter_search_like_web_enable').click(function() {
     
      if ($(this).hasClass('disabled')) {
        $(this).val('unchecked');
      }else{
        if(this.checked == true) {
           
          if ($('#filter_solr_enable').val()==1) {
            
            $(this).val(1);
          }else{
            $(this).val(0);  
          }
        } else {
          $(this).val(0);
        }
      }      
    });

  });
//--></script>
<script type="text/javascript"><!--
$('#button-download-csv').click(function(){
  var url = location.href;
  url += '&download_csv=1';
  location = url;
});
$('.button-filter').on('click', function() {

  if ($('#filter_franchise_tab').length > 0) {
    var url = 'index.php?route=catalog/product&filter_franchise_tab=1&token=<?php echo $token; ?>';
  } else {
    var url = 'index.php?route=catalog/product&token=<?php echo $token; ?>';
  }


  var filter_name = $('input[name=\'filter_name\']').val();

  if (filter_name) {
    url += '&filter_name=' + encodeURIComponent(filter_name);
  }

  var filter_model = $('input[name=\'filter_model\']').val();

  if (filter_model) {
    url += '&filter_model=' + encodeURIComponent(filter_model);
    
    var filter_operator = $('select[name=\'filter_feature_box_operator\']').val();
    var filter_type = $('select[name=\'filter_feature_box_type\']').val();
    url += '&filter_operator=' + encodeURIComponent(filter_operator);
    url += '&filter_feature_box_type=' + encodeURIComponent(filter_type);
    if(filter_type=='string'){
      var filter_box_val_string = $('input[name=\'filter_feature_box_string\']').val();
      url += '&filter_type_string=' + encodeURIComponent(filter_box_val_string);
    } else if(filter_type=='integer'){
      var filter_box_val_from = $('input[name=\'filter_feature_box_from\']').val();
      var filter_box_val_to = $('input[name=\'filter_feature_box_to\']').val();
      url += '&filter_val_from=' + encodeURIComponent(filter_box_val_from);
      url += '&filter_val_to=' + encodeURIComponent(filter_box_val_to);
    }
  }

  var filter_price_from = $('input[name=\'filter_price_from\']').val();

  if (filter_price_from) {
    url += '&filter_price_from=' + encodeURIComponent(filter_price_from);
  }

  var filter_price_to = $('input[name=\'filter_price_to\']').val();

  if (filter_price_to) {
    url += '&filter_price_to=' + encodeURIComponent(filter_price_to);
  }

  var filter_commission_from = $('input[name=\'filter_commission_from\']').val();

  if (filter_commission_from) {
    url += '&filter_commission_from=' + encodeURIComponent(filter_commission_from);
  }

  var filter_commission_to = $('input[name=\'filter_commission_to\']').val();

  if (filter_commission_to) {
    url += '&filter_commission_to=' + encodeURIComponent(filter_commission_to);
  }

  var filter_seller_sku = $('input[name=\'filter_seller_sku\']').val();

  if (filter_seller_sku) {
    url += '&filter_seller_sku=' + encodeURIComponent(filter_seller_sku);

    var sllr_sku_filter_operator = $('select[name=\'sllr_sku_filter_feature_box_operator\']').val();
    var sllr_sku_filter_type = $('select[name=\'sllr_sku_filter_feature_box_type\']').val();
    url += '&sllr_sku_filter_operator=' + encodeURIComponent(sllr_sku_filter_operator);
    url += '&sllr_sku_filter_feature_box_type=' + encodeURIComponent(sllr_sku_filter_type);
    if(sllr_sku_filter_type=='string'){
      var sllr_sku_filter_box_val_string = $('input[name=\'sllr_sku_filter_feature_box_string\']').val();
      url += '&sllr_sku_filter_type_string=' + encodeURIComponent(sllr_sku_filter_box_val_string);
    } else if(sllr_sku_filter_type=='integer'){
      var sllr_sku_filter_box_val_from = $('input[name=\'sllr_sku_filter_feature_box_from\']').val();
      var sllr_sku_filter_box_val_to = $('input[name=\'sllr_sku_filter_feature_box_to\']').val();
      url += '&sllr_sku_filter_val_from=' + encodeURIComponent(sllr_sku_filter_box_val_from);
      url += '&sllr_sku_filter_val_to=' + encodeURIComponent(sllr_sku_filter_box_val_to);
    }

  }

  var filter_status = $('select[name=\'filter_status\']').val();

  if (filter_status != '*') {
    url += '&filter_status=' + encodeURIComponent(filter_status);
  }

    var filter_seller_list = $('select[name=\'filter_seller_list\']').val();

  if (typeof filter_seller_list != 'undefined' && filter_seller_list != '*') {
    url += '&filter_seller_list=' + encodeURIComponent(filter_seller_list);
  }

    var filter_category = $('select[name=\'filter_category\']').val();

  if (filter_category != '*') {
    url += '&filter_category=' + encodeURIComponent(filter_category);
  }

    if( $('input[name=\'filter_non_single\']').is(":checked")){
        url += '&filter_non_single=1';
    } else {
        url += '&filter_non_single=0';
    }

    if( $('input[name=\'filter_non_sor\']').is(":checked")){
      url += '&filter_non_sor=1';
    } else {
      url += '&filter_non_sor=0';
    }

    if( $('input[name=\'filter_images\']').is(":checked")){
      url += '&filter_images=1';
    } else {
      url += '&filter_images=0';
    }

    var filter_page_limit = $('select[name=\'filter_page_limit\']').val();

    if (filter_page_limit != '*') {
      url += '&filter_page_limit=' + encodeURIComponent(filter_page_limit);
    }

    //var filter_solr_enabled = $('#filter_solr_enable').val();
    //url += '&filter_solr_enabled=' + filter_solr_enabled;

    //var filter_search_like_web_enabled = $('#filter_search_like_web_enable').val();
    //url += '&filter_search_like_web_enabled=' + filter_search_like_web_enabled;

  var filter_store_sales_code = $('select[name=\'filter_store_sales_code\']').val();

  if (typeof filter_store_sales_code != 'undefined' && filter_store_sales_code !='*') {
    url += '&filter_store_sales_code=' + encodeURIComponent(filter_store_sales_code);
  }

  var filter_hsn_code = $('input[name=\'filter_hsn_code\']').val();

  if ( filter_hsn_code ) {
    url += '&filter_hsn_code=' + encodeURIComponent(filter_hsn_code);
  }

  var filter_franchise_id = $('select[name=\'filter_franchise_id\']').val();

  if (filter_franchise_id) {
    url += '&filter_franchise_id=' + encodeURIComponent(filter_franchise_id);
  }

  location = url;
});
//--></script>
  <script type="text/javascript"><!--

$('#copy_single').click(function () {
  $('#single_or_sor').val('single');
  $('#price-pop').show();
});
$('#copy_sor').click(function () {
  $('#single_or_sor').val('sor');
  $('#price-pop').show();
});

$('#price_popup_ajax').on('click', function () {
  $.ajax({

    url: 'index.php?route=catalog/product/SetPriceMarkup&token=<?php echo $token; ?>&ajax_request=1&price_markup=' + $('#input-price-pop').val()+'&comm=' + $('#input-comm-pop').val(),
    dataType: 'json',

    success: function () {
      var single_or_sor = $('#single_or_sor').val();
      if(single_or_sor == 'single') {
        $('#form-product').attr('action', '<?php echo $copy_single; ?>&token=<?php echo $token ?>').submit();
      }else if(single_or_sor == 'sor'){
        $('#form-product').attr('action', '<?php echo $copy_sor; ?>&token=<?php echo $token ?>').submit();
      }
    },

  });
});

$('#price_popup_ajax_sor').on('click', function () {
  $.ajax({

    url: 'index.php?route=catalog/product/SetPriceMarkup&token=<?php echo $token; ?>&ajax_request=1&price_markup=' + $('#input-price-pop').val()+'&comm=' + $('#input-comm-pop').val(),
    dataType: 'json',

    success: function () {
      $('#form-product').attr('action', '<?php echo $copy_sor; ?>&token=<?php echo $token ?>').submit();
    },

  });
});


// update for transfer price
$('.transfer_price').dblclick(function(){
  var product_id = $(this).attr('data-product-id');
  $('#transfer_price_'+ product_id).hide();
  $('#trans_price_'+ product_id).show();
  $('input#input_price_'+product_id).focus();
});
$('.price_close_input').click(function(){
  var product_id = $(this).attr('data-product-id');
  $('#transfer_price_'+ product_id).show();
  $('#trans_price_'+ product_id).hide();
});

// Update Transfer Price
$('input[name=\'transfer_price_update\']').on("keypress", null, function(e) {
  if (e.keyCode == 13) {

    var transfer_price = $(this).val();
    var product_id = $(this).attr('data-product-id');

    // save change log data in admin product change log table
    var old_value = $(this).attr('data-old-value');
    var new_value = $(this).val();

    if( new_value != old_value ){
      $.ajax({
        url: 'index.php?route=catalog/product/updateProductList&token=<?php echo $token; ?>&product_id='+product_id+'&field_type=price'+'&field_value='+transfer_price+'&old_value='+old_value+'&new_value='+new_value,
        dataType: 'json',
        complete: function() {
          $('#button-invoice').button('reset');
        },
        success: function(json) {
          $('span#transfer_price_'+ product_id).text(json['price_value']);
          $('#transfer_price_'+ product_id).show();
          $('#trans_price_'+ product_id).hide();
          $('#input_price_'+product_id).val(new_value);
          $('#input_price_'+product_id).attr('data-old-value', new_value);
        },
        error: function(xhr, ajaxOptions, thrownError) {
          alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
      });
    } else {
      $('span#transfer_price_'+ product_id).text(old_value);
      $('#transfer_price_'+ product_id).show();
      $('#trans_price_'+ product_id).hide();
    }
  }
});


// update for commission
$('.product_commission').dblclick(function(){
  var product_id = $(this).attr('data-product-id');
  $('#product_commission_'+ product_id).hide();
  $('#prod_comm_'+ product_id).show();
  $('input#input_commission_'+product_id).focus();
});
$('.commission_close_input').click(function(){
  var product_id = $(this).attr('data-product-id');
  $('#product_commission_'+ product_id).show();
  $('#prod_comm_'+ product_id).hide();
});

// Update commission
$('input[name=\'product_commission_update\']').on("keypress", null, function(e) {
  if (e.keyCode == 13) {

    var product_commission = $(this).val();
    var product_id = $(this).attr('data-product-id');

    // save change log data in admin product change log table
    var old_value = $(this).attr('data-old-value');
    var new_value = $(this).val();

    if( new_value != old_value ){
      $.ajax({
        url: 'index.php?route=catalog/product/updateProductList&token=<?php echo $token; ?>&product_id='+product_id+'&field_type=commission'+'&field_value='+product_commission+'&old_value='+old_value+'&new_value='+new_value,
        dataType: 'json',
        complete: function() {
          $('#button-invoice').button('reset');
        },
        success: function(json) {
          $('span#product_commission_'+ product_id).text(json['price_value']);
          $('#product_commission_'+ product_id).show();
          $('#prod_comm_'+ product_id).hide();
          $('#input_commission_'+product_id).val(new_value);
          $('#input_commission_'+product_id).attr('data-old-value',new_value);
        },
        error: function(xhr, ajaxOptions, thrownError) {
          alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
      });
    } else {
      $('span#product_commission_'+ product_id).text(old_value);
      $('#product_commission_'+ product_id).show();
      $('#prod_comm_'+ product_id).hide();
    }
  }
});

// update for quantity
$('.product_quantity').dblclick(function(){
      var product_id = $(this).attr('data-product-id');
      $('#product_quantity_'+ product_id).hide();
      $('#prod_quantity_'+ product_id).show();
      $('input#input_quantity_'+product_id).focus();
    });
    $('.quantity_close_input').click(function(){
      var product_id = $(this).attr('data-product-id');
      $('#product_quantity_'+ product_id).show();
      $('#prod_quantity_'+ product_id).hide();
    });

    // Update quantity
    $('input[name=\'product_quantity_update\']').on("keypress", null, function(e) {
      if (e.keyCode == 13) {

        var product_quantity = $(this).val();
        var product_id = $(this).attr('data-product-id');

        // save change log data in admin product change log table
        var old_value = $(this).attr('data-old-value');
        var new_value = $(this).val();

        if( new_value != old_value ){
          $.ajax({
            url: 'index.php?route=catalog/product/updateProductList&token=<?php echo $token; ?>&product_id='+product_id+'&field_type=quantity'+'&field_value='+product_quantity+'&old_value='+old_value+'&new_value='+new_value,
            dataType: 'json',
            complete: function() {
              $('#button-invoice').button('reset');
            },
            success: function(json) {
              if( json['error'] ){
                alert(json['message']);
                $('#product_quantity_'+ product_id).show();
                $('#prod_quantity_'+ product_id).hide();
                $('#input_quantity_'+product_id).val(old_value);
                return false;
              } else {
                $('span#product_quantity_'+ product_id).text(json['price_value']);
                $('#product_quantity_'+ product_id).show();
                $('#prod_quantity_'+ product_id).hide();
                $('#input_quantity_'+product_id).val(new_value);
                $('#input_quantity_'+product_id).attr('data-old-value',new_value);
              }
            },
            error: function(xhr, ajaxOptions, thrownError) {
              alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
          });
        } else {
          $('span#product_quantity_'+ product_id).text(old_value);
          $('#product_quantity_'+ product_id).show();
          $('#prod_quantity_'+ product_id).hide();
        }
      }
    });

// update for weight
    $('.product_weight').dblclick(function(){
      var product_id = $(this).attr('data-product-id');
      $('#product_weight_'+ product_id).hide();
      $('#prod_weight_'+ product_id).show();
      $('input#input_weight_'+product_id).focus();
    });
    $('.weight_close_input').click(function(){
      var product_id = $(this).attr('data-product-id');
      $('#product_weight_'+ product_id).show();
      $('#prod_weight_'+ product_id).hide();
    });

    // Update weight
    $('input[name=\'product_weight_update\']').on("keypress", null, function(e) {
      if (e.keyCode == 13) {

        var product_weight = $(this).val();
        var product_id = $(this).attr('data-product-id');

        // save change log data in admin product change log table
        var old_value = $(this).attr('data-old-value');
        var new_value = $(this).val();

        if( new_value != old_value ){
          $.ajax({
            url: 'index.php?route=catalog/product/updateProductList&token=<?php echo $token; ?>&product_id='+product_id+'&field_type=weight'+'&field_value='+product_weight+'&old_value='+old_value+'&new_value='+new_value,
            dataType: 'json',
            complete: function() {
              $('#button-invoice').button('reset');
            },
            success: function(json) {
              //window.location.reload();
              $('span#product_weight_'+ product_id).text('Weight: '+json['price_value'] + ' kg');
              $('#product_weight_'+ product_id).show();
              $('#prod_weight_'+ product_id).hide();
              $('#input_weight_'+product_id).attr('value', new_value);
              $('#input_weight_'+product_id).attr('data-old-value', new_value);
            },
            error: function(xhr, ajaxOptions, thrownError) {
              alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
          });
        } else {
          $('span#product_weight_'+ product_id).text('Weight: '+old_value + ' kg');
          $('#product_weight_'+ product_id).show();
          $('#prod_weight_'+ product_id).hide();
        }


      }
    });

    $('.moderate_tick').click(function(){
      var product_id = $(this).attr('html-data');
      $.ajax({
        type:'post',
        url:'index.php?route=catalog/product/productToModerateApprove&token=<?php echo $token; ?>',
        data:{selected:product_id},
        success:function(html){
          $('#moderate_tick_'+product_id).remove();
          $('.moderated_'+product_id).removeClass('product_to_moderate_cross').addClass('product_to_moderate_tick');
        }
      });
    });

    $('.moderate_cross').click(function(){
      var product_id = $(this).attr('html-data');
      $.ajax({
        type:'post',
        url:'index.php?route=catalog/product/productToModerateReject&token=<?php echo $token; ?>',
        data:{selected:product_id},
        success:function(html){
          $('#moderate_cross_'+product_id).remove();
          $('.moderated_'+product_id).removeClass('product_to_moderate_tick').addClass('product_to_moderate_cross');
        }
      });
    });

    $('.bulk-button-mark').click(function(){
      var checkbox_selected_value = new Array();
      var count = $('input[name=\'selected[]\']:checked').length;
      if(count > 0) {
        $('input[name=\'selected[]\']:checked').each(function () {
          //console.log($(this).attr('product-status-id'),$(this).val());
          var product_status_id = $(this).attr('product-status-id');
          if (product_status_id == 2) {
            checkbox_selected_value.push($(this).val());
          }
        });

        if (checkbox_selected_value.length > 0) {
          $.ajax({
            type: 'post',
            url: 'index.php?route=catalog/product/productToModerateApprove&token=<?php echo $token; ?>',
            data:{selected:checkbox_selected_value},
            dataType: 'json',
            success: function (json) {
              if (json['success']) {
                for(i in checkbox_selected_value){
                  $('#moderate_tick_'+checkbox_selected_value[i]).remove();
                  $('.moderated_'+checkbox_selected_value[i]).removeClass('product_to_moderate_cross').addClass('product_to_moderate_tick');
                }
              }
            }
          });
        } else {
          alert('Invalid Product Selection ! At-least one of the products selected must be To Moderate.');
        }
      }else{
        alert('Please select at-least one product to moderate');
      }
    });

    $('.bulk-button-cross').click(function(){
      var checkbox_selected_value = new Array();
      var count = $('input[name=\'selected[]\']:checked').length;
      if(count > 0) {
        $('input[name=\'selected[]\']:checked').each(function () {
          //console.log($(this).attr('product-status-id'));
          var product_status_id = $(this).attr('product-status-id');
          if (product_status_id == 2) {
            checkbox_selected_value.push($(this).val());
          }
        });

        if (checkbox_selected_value.length > 0) {
          $.ajax({
            type: 'post',
            url: 'index.php?route=catalog/product/productToModerateReject&token=<?php echo $token; ?>',
            data:{selected:checkbox_selected_value},
            dataType: 'json',
            success: function (json) {
              if (json['success']) {
                for(i in checkbox_selected_value){
                  $('#moderate_cross_'+checkbox_selected_value[i]).remove();
                  $('.moderated_'+checkbox_selected_value[i]).removeClass('product_to_moderate_tick').addClass('product_to_moderate_cross');
                }
              }
            }
          });
        }else{
          alert('Invalid Product Selection ! At-least one of the products selected must be To Moderate.');
        }
      }else{
        alert('Please select at-least one product to moderate');
      }
    });


//--></script></div>
<?php echo $footer; ?>
<script type="text/javascript">
  $('.product_rating').click(function(){
    $('.product_rating_popup').show();
  });
  $('.product_rating_popup .new_close').click(function(){
    $('.product_rating_popup').hide();
  });

</script>


<script type="text/javascript">
  $('input[name*=\'filter_\'], select[name*=\'filter_\']').on('keypress',function(e){
    if (e.keyCode == 13) {
      $('.button-filter').trigger('click');
    }
  });
</script>

<script type="text/javascript">
  $(".tab_div_show").click(function(){
    $('.content_div_show').slideUp();
    var value = $(this).attr('data-number');
    $('.sub_main_div_'+value).slideToggle();
    $('.sub_main_div_'+value).scrollTop(0);
  });
</script>

<script>
  $(document).ready(function(){

    //Code added by nilesh to update dynamic selling price
    $('.edit_selling_price').click(function(){
      var product_id = $(this).attr('data-product_id');
      $('.main_content_div_' + product_id).addClass('hidden');
      $('#edit_selling_price_span_' + product_id).removeClass('hidden');
      $('#edit_selling_' + product_id).addClass('hidden');
    });

    $('.price_edit_selling_input').click(function(){
      var product_id = $(this).attr('data-product-id');
      $('.main_content_div_' + product_id).removeClass('hidden');
      $('#edit_selling_price_span_' + product_id).addClass('hidden');
      $('#edit_selling_' + product_id).removeClass('hidden');
    });

    // Update hidden selling Price
    $('input[name=\'special_selling_price\']').on("keypress", null, function(e) {
      if (e.keyCode == 13) {

        var hidden_selling_price = $(this).val();
        if(hidden_selling_price == 0) {
          alert("Hidden Selling Price must be greater than 0.");
          return false;
        }
        var product_id = $(this).attr('data-product-id');

        // save change log data in admin product change log table
        var old_value = $(this).attr('data-old-value');
        var new_value = $(this).val();

        if( new_value != old_value ){
          $.ajax({
            url: 'index.php?route=catalog/product/updateHiddenSellingPriceOfProduct&token=<?php echo $token; ?>&product_id='+product_id+'&old_value='+old_value+'&new_value='+new_value,
            dataType: 'json',
            complete: function() {
              $('#button-invoice').button('reset');
            },
            success: function(json) {
              console.log(json);
              if(json['error'] != '') {
                alert(json['error']);
              }
              $('.main_content_div_' + product_id).removeClass('hidden');
              $('#edit_selling_price_span_' + product_id).addClass('hidden');
              $('#edit_selling_' + product_id).removeClass('hidden');
              $('#input_edit_selling_price_' + product_id).val(hidden_selling_price);
              $('#input_edit_selling_price_' + product_id).attr('data-old-value', hidden_selling_price);
            },
            error: function(xhr, ajaxOptions, thrownError) {
              alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
          });
        }
      }
    });




    ///////////////////////////////////
    //////////////////////////////////
    /////////////////////////////////
    $('.wsb_code_feature_btn').click(function(){
      $('.filter_feature_box_wsb_code').toggle();
    });
    $('.model_filter_feature_box_close').click(function(){
      $('.filter_feature_box_wsb_code').hide();
    });

    $('.sllr_sku_feature_btn').click(function(){
      $('.filter_feature_box_sllr_sku').toggle();
    });
    $('.sllr_sku_filter_feature_box_close').click(function(){
      $('.filter_feature_box_sllr_sku').hide();
    });
    
    
    $('select[name=filter_feature_box_type]').change(function(){
      var selected_type = $(this).val();
      if( selected_type == 'integer'){
        $('.form_group_integer').removeClass('hidden');
        $('.form_group_string').addClass('hidden');
      } else if(selected_type == 'string') {
        $('.form_group_integer').addClass('hidden');
        $('.form_group_string').removeClass('hidden');
      }
    });

    if($('select[name=filter_feature_box_type]').val() =='integer'){
      $('.form_group_integer').removeClass('hidden');
      $('.form_group_string').addClass('hidden');
    } else if($('select[name=filter_feature_box_type]').val() == 'string'){
      $('.form_group_integer').addClass('hidden');
      $('.form_group_string').removeClass('hidden');
    }

    $('select[name=sllr_sku_filter_feature_box_type]').change(function(){
      var selected_type = $(this).val();
      if( selected_type == 'integer'){
        $('.sllr_sku_form_group_integer').removeClass('hidden');
        $('.sllr_sku_form_group_string').addClass('hidden');
      } else if(selected_type == 'string') {
        $('.sllr_sku_form_group_integer').addClass('hidden');
        $('.sllr_sku_form_group_string').removeClass('hidden');
      }
    });

    if($('select[name=sllr_sku_filter_feature_box_type]').val() =='integer'){
      $('.sllr_sku_form_group_integer').removeClass('hidden');
      $('.sllr_sku_form_group_string').addClass('hidden');
    } else if($('select[name=sllr_sku_filter_feature_box_type]').val() == 'string') {
      $('.sllr_sku_form_group_integer').addClass('hidden');
      $('.sllr_sku_form_group_string').removeClass('hidden');
    }

    ///////////////////////////////////
    //////////////////////////////////
    /////////////////////////////////

    $('select[name=\'filter_seller_list\']').on('change',function(){
      if( $(this).val() == 'null' ){
        $('input[name=\'filter_solr_enabled\']').val(0).prop('checked',false);
        $('input[name=\'filter_search_like_web_enabled\']').val(0).prop('checked',false);
        $('input[name=\'filter_search_like_web_enabled\']').val(0).not(':disabled').prop('disabled', true);
      } else {        
        $('input[name=\'filter_solr_enabled\']').val(1).prop('checked',true);
        
        if ($('input[name=\'filter_search_like_web_enabled\']').is(':checked') == true) {
         
          $('input[name=\'filter_search_like_web_enabled\']').val(1).prop('disabled', false);
        }else{
      
          $('input[name=\'filter_search_like_web_enabled\']').val(0).prop('disabled', false);
        }
        
      }
    });

    $('.product_rating_button').click(function(){
      var product_rating = $("select[name='product_ratings']").val();
      var product_ids = [];
      var changes_data = [];
      

      $('input[name=\'selected[]\']:checked').each(function(){
        product_ids.push($(this).val());

        var old_value           = {};
        old_value[$(this).val()]= $(this).data('product-rating');
        changes_data.push(old_value);

      });

      if(product_rating == ''){
        alert('Please enter product rating !');
      }else if(product_ids == ''){
        alert('Please select your product !');
      }else{
        $.ajax({
          type: 'post',
          url: 'index.php?route=catalog/product/setProductRatingByAjax&token=<?php echo $token; ?>',
          data: {"product_rating":product_rating,"product_ids":product_ids,"changes_data":changes_data},
          dataType: 'json',
          beforeSend: function() {
            $('.product_rating_button').button('loading');
          },
          complete: function() {
            $('.product_rating_button').button('reset');
          },
          success: function (json) {
            alert(json['success']);
            window.location.reload();
            $("select[name='product_ratings']").val('');
            $('#form-product input[type=\'checkbox\']').removeAttr('checked');
          }
        });
      }
    });

    
    //////////////////////////////////////////////////
    ///////// start bulk Update block ///////////////
    ////////////////////////////////////////////////

    $("select[name='filter_bulk_update']").on('change',function(){
      var selected_value = $(this).val();
      var checked_product_flag = 0;
      $('.part_of_bulk_update').addClass('hidden');
      if(selected_value !=''){
        
        $('#bulk_update_block_'+selected_value).removeClass('hidden');

        if(selected_value==1){
          $('.bulk_update_block_'+selected_value).removeClass('hidden');          
        } else {
          $('.bulk_update_block_'+selected_value).addClass('hidden');
        }
        
        if(selected_value==4){
          var ul_alignment = '';
          ul_alignment = '<ul class="list-group">';
          $('input[name=\'selected[]\']:checked').each(function(){
            if( $(this).attr('data-product-has-option') == 'true' ){
              checked_product_flag = 1;
              ul_alignment +='<li class="list-group-item">';
              ul_alignment += $(this).attr('data-product-model');
              ul_alignment +='</li>';
            }            
          });
          ul_alignment +='</ul>';
          if( checked_product_flag ){
            $('.model_popup_button').trigger('click');
            $('.prevent_quantity_update .modal-body').html(ul_alignment);
            $('.product_list_popup_close, .product_list_popup_continue').removeClass('hidden');
            $('.product_list_popup_ok').addClass('hidden');
          }
        }

      } else {
        $('.part_of_bulk_update').addClass('hidden');
      }
    });

    $("input[name='filter_product_quantity']").keyup(function(e) {
      if (/\D/g.test(this.value)) {
       var node = $(this);
       node.val(node.val().replace(/[^0-9]/g,'') );
      }
    });

    $("input[name='update_minimum_quantity']").keyup(function(e) {
      if (/\D/g.test(this.value)) {
       var node = $(this);
       node.val(node.val().replace(/[^0-9]/g,'') );
      }
    });

    $("input[name='bulk_commission_update']").keyup(function(e) {
      if (/\D/g.test(this.value)) {
       var node = $(this);
       node.val(node.val().replace(/[^0-9.]/g,'') );
      }
    });

    $('.bulk_update_button').click(function(){
      var product_ids = [];
      var sor_product = [];
      var select_bulk_update_value = $("select[name='filter_bulk_update']").val();
      var changes_data = [];

      $('input[name=\'selected[]\']:checked').each(function(){
        var old_value           = {};
        old_value[$(this).val()]= $(this).data('franchise-id');
        changes_data.push(old_value);
      });

      $('input[name=\'selected[]\']:checked').each(function(){
        product_ids.push($(this).val());
        if(select_bulk_update_value ==1){
          if ($(this).data('sorproduct')==1) {
            sor_product = sor_product+$(this).data('sorname')+'\n';
          }
        }
      });
      
      if(select_bulk_update_value == ''){
        alert('Please select bulk update !');
        return false;
      } else if( product_ids == ''){
        alert('Please select your product(s) !');
        $('select[name="filter_bulk_update"]').val('').trigger('change');
        return false;
      }

      if(select_bulk_update_value == 1){
        if (sor_product!='') {
          alert('These below products has SOR Status \n\n'+sor_product+'\nSo you can\'t change seller');
          return false;
        }
        var seller_id = $("select[name='filter_seller_list_value']").val();  
        if(seller_id==''){
          alert('Please select seller !');
          return false;
        } else {
          bulkUpdateProductAssignToSeller(seller_id, product_ids,'.bulk_update_button');
        }        
      } else if(select_bulk_update_value == 2){
        var product_status = $("select[name='filter_product_list']").val();  
        if(product_status==''){
          alert('Please select product status !');
          return false;
        } else {
          bulkUpdateChangeProductStatus(product_status, product_ids,'.bulk_update_button');
        } 
      } else if(select_bulk_update_value == 3){
        var store_code = $("select[name='filter_assign_store_code']").val();  
        if(store_code==''){
          alert('Please select store code !');
          return false;
        } else {
          bulkUpdateAssignStoreCode(store_code, product_ids,'.bulk_update_button');
        } 
      } else if(select_bulk_update_value == 4){
        var quantity = $("input[name='filter_product_quantity']").val();
        if(quantity==''){
          alert('Please enter quantity greater than 0 !');
          return false;
        } else {
          bulkUpdateChangeQuantity(quantity, product_ids,'.bulk_update_button');
        } 
      } else if(select_bulk_update_value == 5){
        var archive_status = $("select[name='filter_archive_list_value']").val();  
        if(archive_status==''){
          alert('Please select archive status !');
          return false;
        } else {
          archiveInventory(archive_status, product_ids,'.bulk_update_button');
        } 
      } else if(select_bulk_update_value == 6){
        var exclusive_status = $("select[name='filter_exclusive_list_value']").val();  
        if(exclusive_status == ''){
          alert('Please select exclusive status !');
          return false;
        } else {
          bulkUpdateAssignExclusive(exclusive_status, product_ids,'.bulk_update_button');
        } 
      } else if(select_bulk_update_value == 7){
        var update_minimum_quantity = $("input[name='update_minimum_quantity']").val();  
        if(update_minimum_quantity <= 0 ){
         alert('Please enter quantity greater than or equal to 1 !');
          return false;
        } else {
          bulkUpdateMinimumQuantity(update_minimum_quantity, product_ids,'.bulk_update_button');
        } 
      } else if(select_bulk_update_value == 8){
          bulkProductConvertToSingle(product_ids,'.bulk_update_button');
      } else if(select_bulk_update_value == 9){
          applyBulkProductSyncToSolr(product_ids,'.bulk_update_button');
      } else if(select_bulk_update_value == 10){
        var bulk_commission_update = $("input[name='bulk_commission_update']").val();  
        if(bulk_commission_update <= 0 ){
         alert('Please enter quantity greater than or equal to 1 !');
          return false;
        } else {
          applyBulkUpdateCommission(bulk_commission_update, product_ids,'.bulk_update_button');
        } 
      } else if(select_bulk_update_value == 12){
        var sor_days = $("input[name='sor_days']").val();
        var sor_type = $("select[name='sor_type']").val();  
        if(sor_days <= 0 ){
         alert('Please enter sor days greater than or equal to 1 !');
          return false;
        } else {
          applyBulkUpdateSor(sor_days, sor_type, product_ids,'.bulk_update_button');
        } 
      } else if(select_bulk_update_value == 13){
          applyBulkDeleteSor(product_ids,'.bulk_update_button');
       
      } else{
        return false;
      }
    });

    $('.product_list_popup_continue').click(function(){      
      $('input[name=\'selected[]\']:checked').each(function(){        
        if( $(this).attr('data-product-has-option') =='true' ){
          $('#form-product input[type=\'checkbox\']#checkbox_'+$(this).val()).removeAttr('checked');
        }
      });
      $('input[name="filter_product_quantity"]').focus();
      $('#group_checkbox').removeAttr('checked');
    });

    $('.product_list_popup_close').click(function(){
      $('select[name="filter_bulk_update"]').val('').trigger('change');
      $('#form-product input[type=\'checkbox\']').removeAttr('checked');
    });

    $('.product_list_popup_ok').click(function(){
      window.location.reload();
    });

    //////////////////////////////////////////////////
    ///////// End bulk Update block ///////////////
    ////////////////////////////////////////////////
  });

function bulkUpdateProductAssignToSeller(seller_id, product_ids, obj){
  var check_product_exclusive = 0;
  
  if(confirm("This action may change the Exclusive status for all the selected product(s), as per the newly assigned Seller's exclusive setting. If you wish to allow this behaviour, click OK, else Cancel (only seller assignment will happen).")) {
    check_product_exclusive = 1;
  } else {
    check_product_exclusive = 0;
  }    
  
  $.ajax({
    type: 'post',
    url: 'index.php?route=catalog/product/ProductAssignToSeller&token=<?php echo $token; ?>',
    data: {"seller_id":seller_id,"product_ids":product_ids, "check_product_exclusive": check_product_exclusive},
    dataType: 'json',
    beforeSend: function() {
      $(obj).button('loading');
    },
    complete: function() {
      $(obj).button('reset');
    },
    success: function (json) {
      if(json['success']) {
        alert(json['success']);
        window.location.reload();
      }
      if(json['error']) {
        alert(json['error']);
      }
      $('.seller_listing').val('');
      $('#form-product input[type=\'checkbox\']').removeAttr('checked');
    }
  });
}

function bulkUpdateChangeProductStatus(product_status_id, product_ids, obj){
  $.ajax({
    type: 'post',
    url: 'index.php?route=catalog/product/bulkUpdateChangeProductStatus&token=<?php echo $token; ?>',
    data: {"product_status_id":product_status_id,"product_ids":product_ids},
    dataType: 'json',
    beforeSend: function() {
      $(obj).button('loading');
    },
    complete: function() {
      $(obj).button('reset');
    },
    success: function (json) {
      if(json['success']) {
        alert(json['success']);
      }
      if(json['error']) {
        alert(json['error']);
      }
      $('.product_status_list').val('');
      $('#form-product input[type=\'checkbox\']').removeAttr('checked');
    }
  });
}

function bulkUpdateAssignStoreCode(store_code, product_ids, obj){
  $.ajax({
    type: 'post',
    url: 'index.php?route=catalog/product/bulkUpdateAssignStoreCode&token=<?php echo $token; ?>',
    data: {"store_code":store_code,"product_ids":product_ids},
    dataType: 'json',
    beforeSend: function() {
      $(obj).button('loading');
    },
    complete: function() {
      $(obj).button('reset');
    },
    success: function (json) {
      if(json['success']) {
        alert(json['success']);
      }
      if(json['error']) {
        alert(json['error']);
      }
      $('.assgin_store_code').val('');
      $('#form-product input[type=\'checkbox\']').removeAttr('checked');
    }
  });
}

function bulkUpdateChangeQuantity(quantity, product_ids, obj){
  $.ajax({
    type: 'post',
    url: 'index.php?route=catalog/product/bulkUpdateChangeQuantity&token=<?php echo $token; ?>',
    data: {"quantity":quantity,"product_ids":product_ids},
    dataType: 'json',
    beforeSend: function() {
      $(obj).button('loading');
    },
    complete: function() {
      $(obj).button('reset');
    },
    success: function (json) {
      if(json['success']) {
        alert(json['success']);
      }
      if(json['error']) {
        alert(json['error']);
        return false;
      }
      if( json['has_product_option'] !='' ){
        var again_ul_alignment = '';
        again_ul_alignment = '<ul class="list-group">';
        $.each(json['has_product_option'], function(index, value){
          again_ul_alignment +='<li class="list-group-item">';
          again_ul_alignment += $('#checkbox_'+value).attr('data-product-model');
          again_ul_alignment +='</li>';
        });
        again_ul_alignment +='</ul>';
        $('.prevent_quantity_update .modal-body').html(again_ul_alignment);
        $('.model_popup_button').trigger('click');
        $('.product_list_popup_close, .product_list_popup_continue').addClass('hidden');
        $('.product_list_popup_ok').removeClass('hidden');
      } else {
        window.location.reload();
      }
    }
  });
}

function archiveInventory(archive_status, product_ids, obj){
  $.ajax({
    type: 'post',
    url: 'index.php?route=catalog/product/productManageArchive&token=<?php echo $token; ?>',
    data:'archive_val=' + archive_status + '&product_ids='+product_ids,
    dataType: 'json',
    beforeSend: function() {
    $(obj).button('loading');
    },
    complete: function() {
    $(obj).button('reset');
    },
    success: function (json) {        
      if(json['success']) {
        alert(json['success']);
        $('.archive_listing').val('');
        $('#form-product input[type=\'checkbox\']').removeAttr('checked');
      }else if(json['error']) {
        alert(json['error']);
      }        
    }
  });
}

//Function to mark products exclusive in bulk
function bulkUpdateAssignExclusive(exclusive_status, product_ids, obj){
  $.ajax({
    type: 'post',
    url: 'index.php?route=catalog/product/bulkUpdateAssignExclusiveStatus&token=<?php echo $token; ?>',
    data: {"exclusive_status":exclusive_status,"product_ids":product_ids},
    dataType: 'json',
    beforeSend: function() {
      $(obj).button('loading');
    },
    complete: function() {
      $(obj).button('reset');
    },
    success: function (json) {
      if(json['success']) {
        alert(json['success']);
      }
      if(json['error']) {
        alert(json['error']);
      }
      $('.product_status_list').val('');
      $('#form-product input[type=\'checkbox\']').removeAttr('checked');
    }
  });
  location.reload();
}

//Function to mark products sor in bulk
function applyBulkUpdateSor(sor_days, sor_type, product_ids, obj){
  $.ajax({
    type: 'post',
    url: 'index.php?route=catalog/product/bulkUpdateSor&token=<?php echo $token; ?>',
    data: {"sor_days":sor_days,"sor_type":sor_type,"product_ids":product_ids},
    dataType: 'json',
    beforeSend: function() {
      $(obj).button('loading');
    },
    complete: function() {
      $(obj).button('reset');
    },
    success: function (json) {
      if(json['success']) {
        alert(json['success']);
      }
      if(json['error']) {
        alert(json['error']);
      }
      location.reload();
    }
  });
  
}

//Function to delete products sor in bulk
function applyBulkDeleteSor(product_ids, obj){
  $.ajax({
    type: 'post',
    url: 'index.php?route=catalog/product/bulkUpdateSor&token=<?php echo $token; ?>',
    data: {"product_ids":product_ids},
    dataType: 'json',
    beforeSend: function() {
      $(obj).button('loading');
    },
    complete: function() {
      $(obj).button('reset');
    },
    success: function (json) {
      if(json['success']) {
        alert(json['success']);
      }
      if(json['error']) {
        alert(json['error']);
      }
      location.reload();
    }
  });
  
}
//Function to Update Minimum Quantity in bulk
function bulkUpdateMinimumQuantity(update_minimum_quantity, product_ids, obj){
  $.ajax({
    type: 'post',
    url: 'index.php?route=catalog/product/applyBulkUpdateMinimumQuantity&token=<?php echo $token; ?>',
    data: {"update_minimum_quantity":update_minimum_quantity,"product_ids":product_ids},
    //data:'seller_id=' + seller_id + '&product_ids='+product_ids,
    dataType: 'json',
    beforeSend: function() {
      $(obj).button('loading');
    },
    complete: function() {
      $(obj).button('reset');
    },
    success: function (json) {
      if(json['success']) {
        alert(json['success']);
      }
      if(json['error']) {
        alert(json['error']);
      }
      $('.minimum_quantity').val('');
      $('#form-product input[type=\'checkbox\']').removeAttr('checked');
    }
  });
  location.reload();
}
//Function to product convert to single
function bulkProductConvertToSingle(product_ids, obj){
  $.ajax({
    type: 'post',
    url: 'index.php?route=catalog/product/applyBulkProductConvertToSingle&token=<?php echo $token; ?>',
    data: {"product_ids":product_ids},
    //data:'seller_id=' + seller_id + '&product_ids='+product_ids,
    dataType: 'json',
    beforeSend: function() {
      $(obj).button('loading');
    },
    complete: function() {
      $(obj).button('reset');
    },
    success: function (json) {
      if(json['success']) {
        alert(json['success']);
      }
      if(json['error']) {
        alert(json['error']);
      }
      $('#form-product input[type=\'checkbox\']').removeAttr('checked');
    }
  });
  location.reload();
}

//Function to mark products exclusive in bulk
function applyBulkProductSyncToSolr(product_ids, obj){
  $.ajax({
    type: 'post',
    url: 'index.php?route=catalog/product/applyBulkProductSyncToSolr&token=<?php echo $token; ?>',
    data: {"product_ids":product_ids},
    dataType: 'json',
    beforeSend: function() {
      $(obj).button('loading');
    },
    complete: function() {
      $(obj).button('reset');
    },
    success: function (json) {
      if(json['success']) {
        alert(json['success']);
      }
      if(json['error']) {
        alert(json['error']);
      }
      $('#form-product input[type=\'checkbox\']').removeAttr('checked');
    }
  });
  location.reload();
}

//Function to Bulk Update Commission
function applyBulkUpdateCommission(bulk_commission_update, product_ids, obj){
  $.ajax({
    type: 'post',
    url: 'index.php?route=catalog/product/applyBulkUpdateCommission&token=<?php echo $token; ?>',
    data: {"bulk_commission_update":bulk_commission_update,"product_ids":product_ids},
    dataType: 'json',
    beforeSend: function() {
      $(obj).button('loading');
    },
    complete: function() {
      $(obj).button('reset');
    },
    success: function (json) {
      if(json['success']) {
        alert(json['success']);
      }
      if(json['error']) {
        alert(json['error']);
      }
      $('.bulk_commission_update').val('');
      $('#form-product input[type=\'checkbox\']').removeAttr('checked');
    }
  });
  location.reload();
}

$('input[name="filter_images"]').click(function(){
      if($(this).is(':checked')){
        $(this).val(1);
        $('input[name="filter_solr_enabled"]').attr('disabled', true);
        $('input[name="filter_solr_enabled"]').removeAttr('checked',false);
        $('input[name="filter_solr_enabled"]').val(0);
      } else {
        $(this).val(0);
        $('input[name="filter_solr_enabled"]').attr('disabled',false);
      }
    });

function applyBulkCsvUpload(obj){
  var value = $(obj).prev().val();
  if(value){
    if(confirm('Do you really want to upload Bulk Update CSV ? Your product(s) will be updated. You shall be informed by email, once the update is successful !!!')){
      $('#form_bulk_csv_upload').submit();        
      return true;
    }
  } else {
    alert('Please select your csv report!');
    return false;
  }    
}

function applyBulkCsvDownload(){
  $('#form_bulk_csv_download').submit();
}

</script>
