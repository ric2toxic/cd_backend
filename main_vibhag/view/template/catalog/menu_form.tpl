<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <!-- <form action="index.php?route=catalog/menu/getForm&token=<?php echo $this->session->data['token']; ?>&menu_id=<?php echo $this->request->get['menu_id']; ?>" method="Post" id="form-menu_1" class="form-horizontal">
    <div class="row">
      <div class="pull-right col-sm-3">
        <div class="control-label">
          <div class="col-sm-4"><h4>Status</h4></div>
          <div class="col-sm-8">
            <select class ="form-control menu_store_status col-sm-6">
              <option>Select Menu status</option>
              <option value="1">Enable</option>
              <option value="0">Disable</option>              
            </select>
          </div>
        </div>
      </div>  
      <div class="pull-right col-sm-3">
        <div class="control-label">
          <div class="col-sm-4"><h4>Language</h4></div>
          <div class="col-sm-8">
            <select class ="form-control menu_store_language col-sm-6" name="menu_store_language" html-menu-id="<?php echo $this->request->get['menu_id']; ?>">
            <option value="0">Select Menu Language</option>
              <?php foreach($get_language as $language){ ?>
                <?php if($menu_store_language == $language['language_id']){ ?>
                    <option value="<?php echo $language['language_id']; ?>" selected="selected" ><?php echo $language['name']; ?></option>
                <?php }else{ ?>
                    <option value="<?php echo $language['language_id']; ?>" ><?php echo $language['name']; ?></option>
                <?php } ?>
              <?php } ?>
            </select>
          </div>
        </div>
      </div>
    </div>
  </form> -->

  <form action="index.php?route=catalog/menu/edit&token=<?php echo $this->session->data['token']; ?>&menu_id=<?php echo $this->request->get['menu_id']; ?>" method="post" id="form-menu" class="form-horizontal" enctype="multipart/form-data">
    <!-- <div class="row">
      <div class="pull-right col-sm-4">
        <?php if(!empty($storeid) && $count > 1){ ?>
        <div class="control-label">
          <div class="col-sm-5"><h4>Select Store</h4></div>
          <div class="col-sm-7">
            <select class ="form-control seller_store col-sm-6">
              <?php foreach ($stores as $value) { ?>
              <option  value="<?php echo $value['store_id']; ?>"
              <?php if($value['store_id'] == $select_seller_store_id){ ?> selected
              <?php } ?>
              >
              <a><label class="control-label"><?php echo $value['name']; ?>
                </label></a>
              </option>
              <?php } ?>
            </select>
          </div>
        </div>
        <?php } ?>
      </div>
    </div> -->
    <input type="hidden" class="form-control seller_store" value="<?php echo $stores; ?>"/>
    <input type="hidden" name="menu_store_language" class="form-control menu_hid_store_language" value="<?php echo $menu_store_language; ?>"/>
    <div class="page-header">
      <div class="container-fluid">
        <div class="pull-right">
          <button type="button" form="form-menu" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary save_all_menu"><i class="fa fa-save"></i></button>
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
    
    <div class="container-fluid">
      <?php if ($error_warning) { ?>
      <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
      </div>
      <?php } ?>
      <div class="row">
        <div class="col-sm-12">
          <p class="menu_box_error error"></p>
        </div>
      </div>
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title"><i class="fa fa-pencil"></i> Edit <?php echo $menu_info[0]['link_title']; ?></h3>
        </div>
        <div class="panel-body">
          <table id="menus" class="table table-striped table-bordered table-hover">
            <thead>
            <tr>
              <td class="text-left" colspan="2">Menu Title</td>
              <td class="text-left">Item</td>
              <td class="text-left">Value</td>
              <!--<td class="text-left">Type</td>-->
              <td class="text-left">Sort order</td>
              <td class="text-left">Status</td>
              <td></td>
            </tr>
            </thead>
            <tbody class="main_tbody">
             <input type="hidden" id="total_sub_menu">   
            <?php $i = 1;
                  $sub_menu_id = 0; 
                if(isset($menu_info) && !empty($menu_info) ){
                foreach ($menu_info as $info) { ?>
                  <tr id="menu_row_<?php echo $i; ?>" class="table_row_<?php echo $info['parent_id'];?> parent_menu_row">
                    <td class="text-left" colspan="2">
                     <input type="hidden" class="form-control"  name="info[<?php echo $i; ?>][menu_db_id]" value="<?php echo $info['id']; ?>"/>
                      <input type="hidden" class="form-control"  name="info[<?php echo $i; ?>][store_id]" value="<?php echo $select_seller_store_id; ?>"/>
                      <input type="hidden" class="form-control"  name="info[<?php echo $i; ?>][menu_id]" value="<?php echo $this->request->get['menu_id']; ?>"/>
                      <input type="text" class="form-control"  name="info[<?php echo $i; ?>][link_title]" value="<?php echo $info['link_title']; ?>"/>
                    </td>
                    <td class="text-left">
                      <select class ="form-control select_item" html-data="<?php echo $i; ?>" id="select_item_<?php echo $i; ?>" name="info[<?php echo $i; ?>][link_type]">
                        <option value="category" <?php if($info['link_type'] == 'category'){ ?> selected <?php } ?> >Category</option>
                        <option value="page" <?php if($info['link_type'] == 'page'){ ?> selected <?php } ?>>Page</option>
                        <option value="others" <?php if($info['link_type'] == 'others'){ ?> selected <?php } ?>>Custom Link</option>
                      </select>
                    </td>
                    <td class="text-left">
                      <input type="text" id="category_value_<?php echo $i; ?>" data-hidden-id="category_value_hidden<?php echo $i; ?>" class="form-control category_value" html-data="<?php echo $i; ?>" name="name_info[<?php echo $i; ?>][category]" value="<?php echo $info['value_label']; ?>" />
                      <input type="hidden" id="category_value_hidden<?php echo $i; ?>" html-data="<?php echo $i; ?>" class="form-control value"  name="info[<?php echo $i; ?>][category]" value="<?php echo $info['value']; ?>" />
                    </td>
                    <!--<td class="text-left">
                      <select class ="form-control" name="info[<?php echo $i; ?>][type]">
                        <option value="parent" <?php if($info['type'] == 'parent'){ ?> selected <?php } ?> >Parent</option>
                        <option value="child" <?php if($info['type'] == 'child'){ ?> selected <?php } ?> >Child</option>
                      </select>
                    </td>-->

                    <?php if($info['type'] == 'parent'){ ?>
                      <input type="hidden" class ="form-control" name="info[<?php echo $i; ?>][type]" value="parent" />
                    <?php } ?>
                    <?php if($info['type'] == 'child'){ ?>
                      <input type="hidden" class ="form-control" name="info[<?php echo $i; ?>][type]" value="child" />
                    <?php } ?>
                    <td class="text-left">
                      <input type="text" class="form-control"  name="info[<?php echo $i; ?>][position]" value="<?php echo $info['position']; ?>" size="5"/>
                    </td>
                    <td class="text-left">
                      <select name="info[<?php echo $i; ?>][status]" id="input-status" class="form-control">
                        <?php if ($info['status'] == 1) { ?>
                        <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                        <option value="0"><?php echo $text_disabled; ?></option>
                        <?php } else { ?>
                        <option value="1"><?php echo $text_enabled; ?></option>
                        <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                        <?php } ?>
                      </select>
                    </td>
                    <td class="text-left"></td>
                  </tr>
                  <tr>
                  <tr>
                    <td colspan="7">
                      <?php if($info['parent_id'] == 0){ ?>
                            <button type="button" data-toggle="tooltip" data-id="<?php echo $i; ?>" count_sub_menu="<?php echo (isset($info['child']) ? count($info['child']) : 0);?>" total-child="0" data-db-id="<?php echo $info['id']; ?>" class="btn btn-primary addmenu"><i class="fa fa-plus-circle"></i>Add Child</button>
                            <table class="table table-striped table-bordered table-hover">
                              <tbody class="child_<?php echo $i; ?>"></tbody>
                            </table>
                      <?php } ?>
                    </td>
                  </tr>

                  <?php if(isset($info['child']) && count($info['child']) > 0){ ?>
                    <?php
                      $j = 0;
                      foreach($info['child'] as $child_menu) {  ?>
                      
                        <?php if($child_menu['link_type'] == 'megamenu'){ ?>
                          <tr id="child_menu_row_<?php echo $i.$j; ?>" class="table_row_<?php echo $child_menu['parent_id'];?>">
                            <td>
                              <input type="hidden" class="form-control"  name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][menu_db_id]" value="<?php echo $child_menu['id']; ?>"/>
                              <input type="hidden" class="form-control"  name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][store_id]" value="<?php echo $select_seller_store_id; ?>"/>
                              <input type="hidden" class="form-control"  name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][menu_id]" value="<?php echo $this->request->get['menu_id']; ?>"/>
                              <?php if($child_menu['type'] == 'child'){ ?>
                                <input type="hidden" class ="form-control" name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][type]" value="child" />
                              <?php } ?>
                              <input type="hidden" class="form-control"  name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][position]" value="<?php echo $child_menu['position']; ?>" size="5"/>
                              <input type="hidden" class="form-control select_item" html-data="<?php echo $j; ?>" id="select_item_<?php echo $i.$j; ?>" name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][link_type]" value="megamenu" />
                              <input type="hidden" name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][megamenu_type]" value="1">
                            </td>

                            <td class="text-left col-sm-9" colspan="4">
                              <textarea class="form-control megamenu" name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][megamenu]" /> <?php echo $child_menu['megamenu']; ?> </textarea>
                            </td>
                            <td class="text-left">
                              <select name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][status]" id="input-status" class="form-control">
                                <?php if ($child_menu['status'] == 1) { ?>
                                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                <option value="0"><?php echo $text_disabled; ?></option>
                                <?php } else { ?>
                                <option value="1"><?php echo $text_enabled; ?></option>
                                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                <?php } ?>
                              </select>
                            </td>
                            <td class="text-left"><button type="button"  data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger parent_menu" row-type ="<?php echo $child_menu['type'];?>" parent_menu_item_id="<?php echo $child_menu['id'];?>"><i class="fa fa-minus-circle"></i></button></td>
                          </tr>
                        <?php }else{ ?>
                          <tr id="child_menu_row_<?php echo $i.$j; ?>" class="table_row_<?php echo $child_menu['parent_id'];?>">
                           
                            <td class="text-left" colspan="2">
                              <input type="hidden" class="form-control"  name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][menu_db_id]" value="<?php echo $child_menu['id']; ?>"/>
                              <input type="hidden" class="form-control"  name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][store_id]" value="<?php echo $select_seller_store_id; ?>"/>
                              <input type="hidden" class="form-control"  name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][menu_id]" value="<?php echo $this->request->get['menu_id']; ?>"/>
                              <input type="hidden" name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][megamenu_type]" value="">
                              <input type="text" class="form-control"  name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][link_title]" value="<?php echo $child_menu['link_title']; ?>"/>
                            </td>
                            <td class="text-left">
                              <select class ="form-control select_item" html-data="<?php echo $i.$j; ?>" id="select_item_<?php echo $i.$j; ?>" name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][link_type]">
                                <option value="category" <?php if($child_menu['link_type'] == 'category'){ ?> selected <?php } ?> >Category</option>
                                <option value="page" <?php if($child_menu['link_type'] == 'page'){ ?> selected <?php } ?>>Page</option>
                                <option value="others" <?php if($child_menu['link_type'] == 'others'){ ?> selected <?php } ?>>Custom Link</option>
                              </select>
                            </td>
                            <td class="text-left">
                              <input type="text" id="category_value_<?php echo $i.$j; ?>" data-hidden-id="category_value_hidden<?php echo $i.$j; ?>" class="form-control category_value" html-data="<?php echo $i.$j; ?>"  name="name_info[<?php echo $i; ?>][child][<?php echo $j; ?>][category]" value="<?php echo $child_menu['value_label']; ?>" />
                              <input type="hidden" id="category_value_hidden<?php echo $i.$j; ?>" html-data="<?php echo $i.$j; ?>" class="form-control value"  name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][category]" value="<?php echo $child_menu['value']; ?>" />
                              <input type="hidden" class ="form-control" name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][type]" value="child" />
                            </td>
                            <!--<td class="text-left">
                              <select class ="form-control" name="info[<?php echo $i; ?>][type]">
                                <option value="parent" <?php if($info['type'] == 'parent'){ ?> selected <?php } ?> >Parent</option>
                                <option value="child" <?php if($info['type'] == 'child'){ ?> selected <?php } ?> >Child</option>
                              </select>
                            </td>-->
                          
                       
                            <td class="text-left">
                              <input type="text" class="form-control"  name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][position]" value="<?php echo $child_menu['position']; ?>" size="5"/>
                            </td>
                            <td class="text-left">
                              <select name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][status]" id="input-status" class="form-control">
                                <?php if ($child_menu['status'] == 1) { ?>
                                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                <option value="0"><?php echo $text_disabled; ?></option>
                                <?php } else { ?>
                                <option value="1"><?php echo $text_enabled; ?></option>
                                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                <?php } ?>
                              </select>
                            </td>
                            <td class="text-left"><button type="button"  data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger parent_menu" row-type ="<?php echo $child_menu['type'];?>" parent_menu_item_id="<?php echo $child_menu['id'];?>"><i class="fa fa-minus-circle"></i></button></td>
                          </tr>
                          <?php $hidden = 'hidden'; 
                                if($child_menu['link_type'] == 'category'){ 
                                  $hidden = '';
                                }
                          ?>
                           <tr class="promotions_row_<?php echo $i.$j; ?> <?php echo $hidden; ?>">
                            <td class="text-left" colspan="7">
                              <?php 
                                $child_promotion_val=array();
                                if(!empty($child_menu['promotion'])){
                                  $child_promotion_val=unserialize($child_menu['promotion']);
                                }
                              ?>
                               <ul class="promotions_ul">
                                <li><label>Promotion : </label></li>
                                 <li>
                                   <label>
                                     <input name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][promotion][]" 
                                            value="brand" 
                                            type="checkbox"
                                            <?php if (in_array("brand", $child_promotion_val)) {echo 'checked="checked"';} ?>
                                            /> Brand
                                   </label>
                                 </li>
                                 <li>
                                   <label>
                                     <input name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][promotion][]" 
                                            value="price_range" 
                                            type="checkbox"
                                            <?php if (in_array("price_range", $child_promotion_val)) {echo 'checked="checked"';} ?>
                                            /> Price Range
                                   </label>
                                 </li>
                               </ul>
                            </td>
                           </tr>
                           <tr id="sub_child_row_<?php echo $i.$j; ?>" class="row_<?php echo $i.$j; ?>" >
                            <td colspan="7">
                           
                           <?php if(isset($child_menu['child']) && count($child_menu['child']) > 0)
                           {
                             $child_count = count($child_menu['child']); 
                           }
                           else
                           {
                             $child_count = 0;
                           } 
                           ?>

                           <button type="button" style="float: right;" data-toggle="tooltip" onclick="addSubmenu(<?php echo $i.$j; ?>);" data-subchlid-id="0" row_submenu="<?php echo $i; ?>" total-sub-child="<?php echo $child_count; ?>" child_number="<?php echo $j; ?>" title="Sub Child Add" class="btn btn-primary subchild_<?php echo $i.$j; ?>" parent_data="<?php echo $i; ?>,<?php echo $j; ?>,<?php echo $child_count; ?>">
                           <i class="fa fa-plus-circle"></i>Add Sub Child</button>

                           <button type="button" style="float: right; margin-right: 10px;" data-toggle="tooltip" onclick="addmegamenu(<?php echo $i.$j; ?>);" data-subchlid-id="0" row_submenu="<?php echo $i; ?>" total-sub-child="<?php echo $child_count; ?>" child_number="<?php echo $j; ?>" title="Sub Child Add" class="btn btn-primary addmegamenu_<?php echo $i.$j; ?>" parent_data="<?php echo $i; ?>,<?php echo $j; ?>,<?php echo $child_count; ?>">
                           <i class="fa fa-plus-circle"></i>Add Sub Megamenu</button>
                          
                           </td></tr>

                  <?php } ?>

                  <?php if(isset($child_menu['child']) && count($child_menu['child']) > 0){ ?>
                    <?php
                      $k = 0;
                      foreach($child_menu['child'] as $sub_child_menu) { 
                        if($sub_child_menu['link_type'] == 'megamenu'){ ?>
                          <tr id="child_menu_row_<?php echo $i.$j.$k; ?>" class="table_row_<?php echo $sub_child_menu['parent_id'];?>">
                            <td>
                              <input type="hidden" class="form-control"  name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][child][<?php echo $k; ?>][menu_db_id]" value="<?php echo $sub_child_menu['id']; ?>"/>
                              <input type="hidden" class="form-control"  name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][child][<?php echo $k; ?>][store_id]" value="<?php echo $select_seller_store_id; ?>"/>
                              <input type="hidden" class="form-control"  name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][child][<?php echo $k; ?>][menu_id]" value="<?php echo $this->request->get['menu_id']; ?>"/>
                           
                                <input type="hidden" class ="form-control" name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][child][<?php echo $k; ?>][type]" value="child" />
                             
                              <input type="hidden" class="form-control"  name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][child][<?php echo $k; ?>][position]" value="<?php echo $sub_child_menu['position']; ?>" size="5"/>
                              <input type="hidden" class="form-control select_item" html-data="<?php echo $i.$j.$k; ?>" id="select_item_<?php echo $i.$j.$k; ?>" name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][child][<?php echo $k; ?>][link_type]" value="megamenu" />
                              <input type="hidden" name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][child][<?php echo $k; ?>][megamenu_type]" value="1">
                            </td>

                            <td class="text-left col-sm-9" colspan="4">
                              <textarea class="form-control megamenu" name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][child][<?php echo $k; ?>][megamenu]" /> <?php echo $sub_child_menu['megamenu']; ?> </textarea>
                            </td>
                            <td class="text-left">
                              <select name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][child][<?php echo $k; ?>][status]" id="input-status" class="form-control">
                                <?php if ($sub_child_menu['status'] == 1) { ?>
                                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                <option value="0"><?php echo $text_disabled; ?></option>
                                <?php } else { ?>
                                <option value="1"><?php echo $text_enabled; ?></option>
                                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                <?php } ?>
                              </select>
                            </td>
                            <td class="text-left"><button type="button"  data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger parent_menu" row-type ="<?php echo $sub_child_menu['type'];?>" parent_menu_item_id="<?php echo $sub_child_menu['id'];?>"><i class="fa fa-minus-circle"></i></button></td>
                          </tr>
                        <?php }else{ ?>
                          <tr id="child_menu_row_<?php echo $i.$j.$k; ?>" class="table_row_<?php echo $sub_child_menu['parent_id'];?>">
                            <td>
                              <input type="hidden" class="form-control"  name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][child][<?php echo $k; ?>][menu_db_id]" value="<?php echo $sub_child_menu['id']; ?>"/>
                              <input type="hidden" class="form-control"  name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][child][<?php echo $k; ?>][store_id]" value="<?php echo $select_seller_store_id; ?>"/>
                              <input type="hidden" class="form-control"  name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][child][<?php echo $k; ?>][menu_id]" value="<?php echo $this->request->get['menu_id']; ?>"/>
                              <input type="hidden" name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][child][<?php echo $k; ?>][megamenu_type]" value="">
                            </td>
                            <td class="text-left">
                              <input type="text" class="form-control"  name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][child][<?php echo $k; ?>][link_title]" value="<?php echo $sub_child_menu['link_title']; ?>"/>
                            </td>
                            <td class="text-left">
                              <select class ="form-control select_item" html-data="<?php echo $i.$j.$k; ?>" id="select_item_<?php echo $i.$j.$k; ?>" name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][child][<?php echo $k; ?>][link_type]">
                                <option value="category" <?php if($sub_child_menu['link_type'] == 'category'){ ?> selected <?php } ?> >Category</option>
                                <option value="page" <?php if($sub_child_menu['link_type'] == 'page'){ ?> selected <?php } ?>>Page</option>
                                <option value="others" <?php if($sub_child_menu['link_type'] == 'others'){ ?> selected <?php } ?>>Custom Link</option>
                              </select>
                            </td>
                            <td class="text-left">
                              <input type="text" id="category_value_<?php echo $i.$j.$k; ?>" data-hidden-id="category_value_hidden<?php echo $i.$j.$k; ?>" class="form-control category_value" html-data="<?php echo $i.$j.$k; ?>"  name="name_info[<?php echo $i; ?>][child][<?php echo $j; ?>][child][<?php echo $k; ?>][category]" value="<?php echo $sub_child_menu['value_label']; ?>" />

                              <input type="hidden" id="category_value_hidden<?php echo $i.$j.$k; ?>" html-data="<?php echo $i.$j.$k; ?>" class="form-control value"  name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][child][<?php echo $k; ?>][category]" value="<?php echo $sub_child_menu['value']; ?>" />
                            </td>
                            <!--<td class="text-left">
                              <select class ="form-control" name="info[<?php echo $i; ?>][type]">
                                <option value="parent" <?php if($info['type'] == 'parent'){ ?> selected <?php } ?> >Parent</option>
                                <option value="child" <?php if($info['type'] == 'child'){ ?> selected <?php } ?> >Child</option>
                              </select>
                            </td>-->
                            <input type="hidden" class ="form-control" name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][child][<?php echo $k; ?>][type]" value="child" />
                          
                            <td class="text-left">
                              <input type="text" class="form-control"  name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][child][<?php echo $k; ?>][position]" value="<?php echo $sub_child_menu['position']; ?>" size="5"/>
                            </td>
                            <td class="text-left">
                              <select name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][child][<?php echo $k; ?>][status]" id="input-status" class="form-control">
                                <?php if ($sub_child_menu['status'] == 1) { ?>
                                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                <option value="0"><?php echo $text_disabled; ?></option>
                                <?php } else { ?>
                                <option value="1"><?php echo $text_enabled; ?></option>
                                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                <?php } ?>
                              </select>
                            </td>
                            <td class="text-left"><button type="button"  data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger parent_menu" row-type ="<?php echo $sub_child_menu['type'];?>" parent_menu_item_id="<?php echo $sub_child_menu['id'];?>"><i class="fa fa-minus-circle"></i></button></td>
                          </tr>

                           <tr id="sub_child_row_<?php echo $i.$j.$k; ?>" class="row_<?php echo $i.$j.$k; ?>" >
                           <td>&nbsp;</td>
                           <td colspan="2">
                              <?php 
                                $child_hidden = 'hidden';
                                if($sub_child_menu['link_type'] == 'category'){ 
                                  $child_hidden = '';
                                }

                                $sub_child_promotion_val=array();
                                if(!empty($sub_child_menu['promotion'])){
                                  $sub_child_promotion_val=unserialize($sub_child_menu['promotion']);
                                }
                                //echo "<pre>"; print_r($sub_child_promotion_val);
                              ?>
                              <ul class="promotions_ul promotions_row_<?php echo $i.$j.$k; ?> <?php echo $child_hidden; ?>">
                               <li><label>Promotion : </label></li>
                               <li>
                                 <label>
                                   <input name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][child][<?php echo $k; ?>][promotion][]" 
                                          value="brand" 
                                          type="checkbox" 
                                          <?php if (in_array("brand", $sub_child_promotion_val)) {echo 'checked="checked"';}  ?>
                                          /> Brand
                                 </label>
                               </li>
                               <li>
                                 <label>
                                   <input name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][child][<?php echo $k; ?>][promotion][]" 
                                          value="price_range" 
                                          type="checkbox" 
                                          <?php if (in_array("price_range", $sub_child_promotion_val)) {echo 'checked="checked"';} ?>
                                          /> Price Range
                                 </label>
                               </li>
                             </ul>
                          </td>
                           <td colspan="5">
                           
                           <?php if(isset($sub_child_menu['child']) && count($sub_child_menu['child']) > 0)
                           {
                             $child_count = count($sub_child_menu['child']); 
                           }
                           else
                           {
                             $child_count = 0;
                           } 
                           ?>

                           <button type="button" style="float: right;" data-toggle="tooltip" onclick="addSubmenu(<?php echo $i.$j.$k; ?>);" data-subchlid-id="0" row_submenu="<?php echo $i; ?>" total-sub-child="<?php echo $child_count; ?>" child_number="<?php echo $j; ?>" last_child="<?php echo $k; ?>" title="Sub Child Add" class="btn btn-primary subchild_<?php echo $i.$j.$k; ?>" parent_data="<?php echo $i; ?>,<?php echo $j; ?>,<?php echo $k; ?>,<?php echo $child_count; ?>">
                           <i class="fa fa-plus-circle"></i>Add Sub Child</button>

                           <button type="button" style="float: right; margin-right: 10px;" data-toggle="tooltip" onclick="addmegamenu(<?php echo $i.$j.$k; ?>);" data-subchlid-id="0" row_submenu="<?php echo $i; ?>" total-sub-child="<?php echo $child_count; ?>" child_number="<?php echo $j; ?>" last_child="<?php echo $k; ?>" title="Sub Child Add" class="btn btn-primary addmegamenu_<?php echo $i.$j.$k; ?>" parent_data="<?php echo $i; ?>,<?php echo $j; ?>,<?php echo $k; ?>,<?php echo $child_count; ?>">
                           <i class="fa fa-plus-circle"></i>Add Sub Megamenu</button>
                          
                           </td></tr>


                        <?php } ?>


                  <?php if(isset($sub_child_menu['child']) && count($sub_child_menu['child']) > 0){ ?>
                    <?php
                      $p = 0;
                      foreach($sub_child_menu['child'] as $last_child_menu) { 
                        if($last_child_menu['link_type'] == 'megamenu'){ ?>
                          <tr id="child_menu_row_<?php echo $i.$j.$k.$p; ?>" class="table_row_<?php echo $sub_child_menu['parent_id'];?>">
                            <td>
                              <input type="hidden" class="form-control"  name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][child][<?php echo $k; ?>][child][<?php echo $p; ?>][menu_db_id]" value="<?php echo $last_child_menu['id']; ?>"/>
                              <input type="hidden" class="form-control"  name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][child][<?php echo $k; ?>][child][<?php echo $p; ?>][store_id]" value="<?php echo $select_seller_store_id; ?>"/>
                              <input type="hidden" class="form-control"  name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][child][<?php echo $k; ?>][child][<?php echo $p; ?>][menu_id]" value="<?php echo $this->request->get['menu_id']; ?>"/>
                             
                                <input type="hidden" class ="form-control" name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][child][<?php echo $k; ?>][child][<?php echo $p; ?>][type]" value="child" />
                              
                              <input type="hidden" class="form-control"  name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][child][<?php echo $k; ?>][child][<?php echo $p; ?>][position]" value="<?php echo $last_child_menu['position']; ?>" size="5"/>
                              <input type="hidden" class="form-control select_item" html-data="<?php echo $i.$j.$k.$p; ?>" id="select_item_<?php echo $i.$j.$k.$p; ?>" name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][child][<?php echo $k; ?>][child][<?php echo $p; ?>][link_type]" value="megamenu" />
                              <input type="hidden" name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][child][<?php echo $k; ?>][child][<?php echo $p; ?>][megamenu_type]" value="1">
                            </td>

                            <td class="text-left col-sm-9" colspan="4">
                              <textarea class="form-control megamenu" name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][child][<?php echo $k; ?>][child][<?php echo $p; ?>][megamenu]" /> <?php echo $last_child_menu['megamenu']; ?> </textarea>
                            </td>
                            <td class="text-left">
                              <select name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][child][<?php echo $k; ?>][child][<?php echo $p; ?>][status]" id="input-status" class="form-control">
                                <?php if ($last_child_menu['status'] == 1) { ?>
                                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                <option value="0"><?php echo $text_disabled; ?></option>
                                <?php } else { ?>
                                <option value="1"><?php echo $text_enabled; ?></option>
                                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                <?php } ?>
                              </select>
                            </td>
                            <td class="text-left"><button type="button"  data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger parent_menu" row-type ="<?php echo $last_child_menu['type'];?>" parent_menu_item_id="<?php echo $last_child_menu['id'];?>"><i class="fa fa-minus-circle"></i></button></td>
                          </tr>
                        <?php }else{ ?>
                          <tr id="child_menu_row_<?php echo $i.$j.$k.$p; ?>" class="table_row_<?php echo $last_child_menu['parent_id'];?>">
                            <td>
                              <input type="hidden" class="form-control"  name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][child][<?php echo $k; ?>][child][<?php echo $p; ?>][menu_db_id]" value="<?php echo $last_child_menu['id']; ?>"/>
                              <input type="hidden" class="form-control"  name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][child][<?php echo $k; ?>][child][<?php echo $p; ?>][store_id]" value="<?php echo $select_seller_store_id; ?>"/>
                              <input type="hidden" class="form-control"  name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][child][<?php echo $k; ?>][child][<?php echo $p; ?>][menu_id]" value="<?php echo $this->request->get['menu_id']; ?>"/>
                              <input type="hidden" name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][child][<?php echo $k; ?>][child][<?php echo $p; ?>][megamenu_type]" value="">
                            </td>
                            <td class="text-left">
                              <input type="text" class="form-control"  name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][child][<?php echo $k; ?>][child][<?php echo $p; ?>][link_title]" value="<?php echo $last_child_menu['link_title']; ?>"/>
                            </td>
                            <td class="text-left">
                              <select class ="form-control select_item" html-data="<?php echo $i.$j.$k.$p; ?>" id="select_item_<?php echo $i.$j.$k.$p; ?>" name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][child][<?php echo $k; ?>][child][<?php echo $p; ?>][link_type]">
                                <option value="category" <?php if($last_child_menu['link_type'] == 'category'){ ?> selected <?php } ?> >Category</option>
                                <option value="page" <?php if($last_child_menu['link_type'] == 'page'){ ?> selected <?php } ?>>Page</option>
                                <option value="others" <?php if($last_child_menu['link_type'] == 'others'){ ?> selected <?php } ?>>Custom Link</option>
                              </select>
                            </td>
                            <td class="text-left">
                              <input type="text" id="category_value_<?php echo $i.$j.$k.$p; ?>" data-hidden-id="category_value_hidden<?php echo $i.$j.$k.$p; ?>" class="form-control category_value" html-data="<?php echo $i.$j.$k.$p; ?>"  name="name_info[<?php echo $i; ?>][child][<?php echo $j; ?>][child][<?php echo $k; ?>][child][<?php echo $p; ?>][category]" value="<?php echo $last_child_menu['value_label']; ?>" />

                              <input type="hidden" id="category_value_hidden<?php echo $i.$j.$k.$p; ?>" html-data="<?php echo $i.$j.$k.$p; ?>" class="form-control value"  name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][child][<?php echo $k; ?>][child][<?php echo $p; ?>][category]" value="<?php echo $last_child_menu['value']; ?>" />
                            </td>
                            <!--<td class="text-left">
                              <select class ="form-control" name="info[<?php echo $i; ?>][type]">
                                <option value="parent" <?php if($info['type'] == 'parent'){ ?> selected <?php } ?> >Parent</option>
                                <option value="child" <?php if($info['type'] == 'child'){ ?> selected <?php } ?> >Child</option>
                              </select>
                            </td>-->
                            <input type="hidden" class ="form-control" name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][child][<?php echo $k; ?>][child][<?php echo $p; ?>][type]" value="child" />
                          
                            <td class="text-left">
                              <input type="text" class="form-control"  name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][child][<?php echo $k; ?>][child][<?php echo $p; ?>][position]" value="<?php echo $last_child_menu['position']; ?>" size="5"/>
                            </td>
                            <td class="text-left">
                              <select name="info[<?php echo $i; ?>][child][<?php echo $j; ?>][child][<?php echo $k; ?>][child][<?php echo $p; ?>][status]" id="input-status" class="form-control">
                                <?php if ($last_child_menu['status'] == 1) { ?>
                                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                <option value="0"><?php echo $text_disabled; ?></option>
                                <?php } else { ?>
                                <option value="1"><?php echo $text_enabled; ?></option>
                                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                <?php } ?>
                              </select>
                            </td>
                            <td class="text-left"><button type="button"  data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger parent_menu" row-type ="<?php echo $last_child_menu['type'];?>" parent_menu_item_id="<?php echo $last_child_menu['id'];?>"><i class="fa fa-minus-circle"></i></button></td>
                          </tr>

                        <?php } ?>


                      <?php $p++; $sub_menu_id++; } }  ?>

                      <?php $k++; $sub_menu_id++; } }  ?>
 

             <?php $j++; $sub_menu_id++; } }  ?>

            <?php $i++; $sub_menu_id++; } } ?>
            </tbody>
          
          </table>
        </div>
      </div>
    </div>
  </form>
</div>
<input type="hidden" id="sub_menu_id" value="<?php echo $sub_menu_id; ?>" />
<style>
.parent_menu_row {
   border: solid 2px; 
}
.sub-child-border{
     border: solid 2px brown;
}
</style>

<script type="text/javascript">
  var menu_row = <?php echo $i; ?>;
  var childcount = menu_row;
  $(document).delegate( ".addmenu", "click", function() {
    var sub_menu_id = $("#sub_menu_id").val();
    
      sub_menu_id = parseInt(sub_menu_id) + 1;
      $("#sub_menu_id").val(sub_menu_id);
    
    if($(this).hasClass("subchild")){
       var sub_child = $(this).attr('data-subchlid-id');
       var sub_child_add = parseInt(sub_child) + 1; 
       var val = $(this).attr('total-sub-child');
       var total_sub_child = parseInt(val);

    }else{
        var id = $(this).attr('data-id');
        var val = $(this).attr('total-child');
        if($(this).hasClass("child")){
            var sub_menu = $("#total_sub_menu").val();            
            total_sub_child = parseInt(total_sub_child)+1;
            var sub_menu = 1;
        }
        var count_sub_menu = parseInt($(this).attr('count_sub_menu'));
        var total_child = parseInt(val);
    }
    
    if(id == 0){
      child = '['+menu_row+']';
      childcount = menu_row;
      parent_menu_row_css = 'parent_menu_row';
    }else{
      if(count_sub_menu > 0){
        total_child += count_sub_menu;
        child = '[' + id + '][child][' + total_child + ']';
        parent_menu_row_css = '';
      }else{
        child = '[' + id + '][child][' + total_child + ']';
        parent_menu_row_css = '';
      }
    }
    if(sub_child == 1 && sub_child > 1){
      sub_child_indx = '['+sub_child+']';
    }
        html  = '<tr id="menu_row_' + menu_row + '" class="'+parent_menu_row_css+'">';
        html += '  <td class="text-left" colspan="2">';
        html += '   <input type="hidden" class="form-control"  name="info'+ child +'[menu_db_id]" value="0"/>'
        html += '   <input type="hidden" class="form-control"  name="info'+ child+'[store_id]" value="<?php echo $select_seller_store_id; ?>"/>';
        html += '   <input type="hidden" class="form-control"  name="info'+ child +'[menu_id]" value="<?php echo $this->request->get['menu_id']; ?>"/>';
        html += '<input type="text" class="form-control menu_title"  name="info'+ child +'[link_title]" value="" placeholder=" Enter Your Menu Title"/>';
        html += '</td> <td class="text-left"><select class ="form-control select_item" html-data="'+ menu_row +'" id="select_item_'+menu_row+'" name="info'+ child +'[link_type]"><option  value="category">Category</option><option  value="page">Page</option><option  value="others">Custom Link</option></select></td>';
         html += '  <td class="text-left"><input type="text" class="form-control category_value" id="category_value_' + menu_row + '" html-data="'+menu_row+'"  name="name_info'+ child +'[category]" value="" placeholder=" Select Category" data-hidden-id="category_value_hidden'+menu_row+'" /><input type="hidden" id="category_value_hidden'+menu_row+'" class="form-control value"  name="info'+ child +'[category]" value="" /></td>';
        //html += '<td class="text-left"><select class ="form-control" name="info'+ child +'[type]">';
        if(id ==0){
            //html +='<option  value="parent">Parent</option>';
            html +='<input type="hidden" name="info'+ child +'[type]" value="parent">';
        }
        if(id !=0){
            //html += '<option  value="child">Child</option></select></td>';
            html += '<input type="hidden" name="info'+ child +'[type]" value="child">';
            html += '<input type="hidden" name="info'+ child +'[megamenu_type]" value="">';
        }
        html += '  <td class="text-left"><input type="text" class="form-control"  name="info'+ child +'[position]" value="" placeholder=" Enter Your Menu position" size="5"/> </td>';
        html += '<td class="text-left"><select name="info'+ child +'[status]" id="input-status" class="form-control"><option value="1" selected="selected"><?php echo $text_enabled; ?></option><option value="0"><?php echo $text_disabled; ?></option></select></td>';
 
        html += '  <td class="text-right"><button type="button" row_number= '+menu_row+'  data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger button_remove"><i class="fa fa-minus-circle"></i></button></td>';

        html += '</tr>';
   
    if(id == 0){
      html += '<tr id="child_row_' + menu_row + '"><td colspan="7">';
      html += '<button type="button" data-toggle="tooltip" data-id="'+ menu_row +'" total-child="0" data-db-id="'+ id +'" title="Add Child" class="btn btn-primary child addmenu"><i class="fa fa-plus-circle"></i>Add Child</button>';
      html += '<table class="table table-striped table-bordered table-hover"><tbody class="child_'+ menu_row+'"></tbody></table></td>';
    }else{
      html += '<tr id="sub_child_row_'+ sub_menu_id + '" class="row_'+ sub_menu_id +' row_'+menu_row+' " >';
      html += '  <td colspan="4">';
      html += '     <ul class="promotions_ul promotions_row_'+menu_row+'">';
      html += '       <li><label>Promotion : </label></li>';
      html += '       <li>';
      html += '         <label>';
      html += '           <input name="info[1][promotion][]" value="brand" type="checkbox">';
      html += '           Brand';
      html += '         </label>';  
      html += '       </li>';
      html += '       <li>';
      html += '         <label>';
      html += '           <input name="info[1][promotion][]" value="price_range" type="checkbox">';
      html += '           Price Range';
      html += '         </label>';
      html += '       </li>';
      html += '     </ul>';
      html += '  </td>';
      html += '  <td colspan="3">';
      html += '<button type="button" style="float: right;" data-toggle="tooltip" onclick="addSubmenu('+sub_menu_id+');" data-subchlid-id="0" row_submenu="'+ id +'" total-sub-child="0" child_number="'+ total_child +'" title="Sub Child Add" class="btn btn-primary subchild_'+sub_menu_id+'" parent_data="'+id+','+total_child+',0"><i class="fa fa-plus-circle"></i>Add Sub Child</button>  ';

      html += '<button type="button" style="float: right;margin-right: 10px;" onclick="addmegamenu('+sub_menu_id+');" data-toggle="tooltip" data-subchlid-id="0" total-sub-child="0" data-db-id="'+ id +'" class="btn btn-primary addmegamenu_'+sub_menu_id+'" parent_data="'+id+','+total_child+',0"><i class="fa fa-plus-circle"></i>Add Sub Megamenu</button> <br />';
    }
    html += '</tr>';

    if(id == 0){
      $('#menus tbody.main_tbody').append(html);
    }else{
      if(sub_child){
        $('#sub_child_row_'+sub_child).after(html);
      }
      $('#menus tbody.child_'+id).append(html);
    }

    menu_row++;
    total_child = total_child + 1;
    $(this).attr('total-child', total_child);
  });


  function addSubmenu(id){
    //var sub_menu_id = $("#sub_menu_id").val();
    //var upddate_submenu_id = parseInt(sub_menu_id)+1;
    //$("#sub_menu_id").val(upddate_submenu_id);
    var row_submenu = $('.subchild_'+id).attr('row_submenu');
    var child = $(".subchild_"+id).attr('child_number');
    var last_child = $(".subchild_"+id).attr('last_child');
    var val = $(".subchild_"+id).attr('total-sub-child');
    var sub_child_add =  parseInt(val)+1;
    $(".subchild_"+id).attr('total-sub-child',sub_child_add);
    var total_sub_child = parseInt(val);


    var parent_data = $(".subchild_"+id).attr('parent_data');
    var parent_data_arr = parent_data.split(',');
    var upddate_submenu_id = parent_data_arr[0]+''+parent_data_arr[1]+''+parent_data_arr[2];
    
    if(parent_data_arr[3])
    {
     upddate_submenu_id = upddate_submenu_id+''+parent_data_arr[3]; 
    }

    var last_element = parent_data_arr[parent_data_arr.length - 1];
    last_element = parseInt(last_element)+1;

    var sub_child_indx  = '';
    var new_parent_data = '';
    //var sub_child_indx = '['+row_submenu+'][child]['+child+'][child]['+total_sub_child+']';
    for(i=0; i<parent_data_arr.length;i++)
    {
      if(i == (parent_data_arr.length)-1)
      {
        sub_child_indx = sub_child_indx + '['+parent_data_arr[i]+']';
      }
      else
      {
       sub_child_indx = sub_child_indx + '['+parent_data_arr[i]+'][child]'; 
      }

     if(i < (parent_data_arr.length)-1)
      { 
        new_parent_data = new_parent_data+parent_data_arr[i]+',';
      } 
      
    }


    $(".subchild_"+id).attr('parent_data',new_parent_data+last_element);

        html  = '<tr id="sub_menu_row_' + upddate_submenu_id + '" row-number="'+upddate_submenu_id+'"">';
        html += '  <td class="text-left">';
        html += '   <input type="hidden" class="form-control"  name="info'+ sub_child_indx +'[menu_db_id]" value="0"/>'
        html += '   <input type="hidden" class="form-control"  name="info'+ sub_child_indx+'[store_id]" value="<?php echo $select_seller_store_id; ?>"/>';
        html += '   <input type="hidden" class="form-control"  name="info'+ sub_child_indx +'[menu_id]" value="<?php echo $this->request->get['menu_id']; ?>"/></td>';
        html += '<td class="text-left"><input type="text" class="form-control menu_title"  name="info'+ sub_child_indx +'[link_title]" value="" placeholder=" Enter Your Menu Title"/>';

        html += '</td> <td class="text-left"><select class ="form-control select_item" html-data="'+ upddate_submenu_id +'" id="select_item_'+upddate_submenu_id+'" name="info'+ sub_child_indx +'[link_type]"><option  value="category">Category</option><option  value="page">Page</option><option  value="others">Custom Link</option></select></td>';

        html += '  <td class="text-left"><input type="text" class="form-control category_value" id="category_value_' + upddate_submenu_id + '" html-data="'+upddate_submenu_id+'"  name="name_info'+ sub_child_indx +'[category]" value="" placeholder=" Select Category" data-hidden-id="category_value_hidden'+upddate_submenu_id+'" /><input type="hidden" id="category_value_hidden'+upddate_submenu_id+'" class="form-control value"  name="info'+ sub_child_indx +'[category]" value="" />';

        html += '<input type="hidden" name="info'+ sub_child_indx +'[type]" value="child">';
        html += '<input type="hidden" name="info'+ sub_child_indx +'[megamenu_type]" value="">';

        html += '</td>';
 
        html += '  <td class="text-left"><input type="text" class="form-control"  name="info'+ sub_child_indx +'[position]" value="" placeholder=" Enter Your Menu position" size="5"/> </td>';
        html += '<td class="text-left"><select name="info'+ sub_child_indx +'[status]" id="input-status" class="form-control"><option value="1" selected="selected"><?php echo $text_enabled; ?></option><option value="0"><?php echo $text_disabled; ?></option></select></td>';
        html += '  <td class="text-left"><button type="button" row_number= '+upddate_submenu_id+'  data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger button_remove"><i class="fa fa-minus-circle"></i></button></td>';
        html += '</tr>';


      if(parent_data_arr.length < 4)
      {
        html += '<tr id="sub_child_row_'+ upddate_submenu_id + '" class="row_'+ upddate_submenu_id +' row_'+upddate_submenu_id+' " >';
        html += '  <td>&nbsp;</td>';
        html += '  <td colspan="2">';
        html += '     <ul class="promotions_ul promotions_row_'+upddate_submenu_id+'">';
        html += '       <li><label>Promotion : </label></li>';
        html += '       <li>';
        html += '         <label>';
        html += '           <input name="info[1][promotion][]" value="brand" type="checkbox">';
        html += '           Brand';
        html += '         </label>';  
        html += '       </li>';
        html += '       <li>';
        html += '         <label>';
        html += '           <input name="info[1][promotion][]" value="price_range" type="checkbox">';
        html += '           Price Range';
        html += '         </label>';
        html += '       </li>';
        html += '     </ul>';
        html += '  </td>';
        html += '<td colspan="4">';
        html += '<button type="button" style="float: right;" onclick="addSubmenu('+upddate_submenu_id+')" data-toggle="tooltip" row_submenu= '+row_submenu+' child_number="'+child+'"  data-subchlid-id="'+ upddate_submenu_id +'" total-sub-child="0" title="Sub Child Add" class="btn btn-primary menu_sub_menu subchild_'+upddate_submenu_id+'" parent_data="'+parent_data+',0"><i class="fa fa-plus-circle"></i>Add Sub Child</button>  ';

        html += '<button type="button" style="float: right;margin-right: 10px;" onclick="addmegamenu('+upddate_submenu_id+')" data-toggle="tooltip" data-subchlid-id="'+ upddate_submenu_id +'" total-sub-child="0"  class="btn btn-primary addmegamenu_'+upddate_submenu_id+'" parent_data="'+parent_data+',0"><i class="fa fa-plus-circle"></i>Add Sub Megamenu</button> <br />';

        html += '</tr>';
      }

       $('#sub_child_row_'+id).after(html);
     
  }

function addmegamenu(id){
    var sub_menu_id = $("#sub_menu_id").val();
    var upddate_submenu_id = parseInt(sub_menu_id)+1;
    $("#sub_menu_id").val(upddate_submenu_id);
    var row_submenu = $('.addmegamenu_'+id).attr('row_submenu');
    var child = $(".addmegamenu_"+id).attr('child_number');

    var val = $(".addmegamenu_"+id).attr('total-sub-child');
    var sub_child_add =  parseInt(val)+1;

    var parent_data = $(".addmegamenu_"+id).attr('parent_data');
    var parent_data_arr = parent_data.split(',');
    var last_element = parent_data_arr[parent_data_arr.length - 1];
    last_element = parseInt(last_element)+1;

    var sub_child_indx  = '';
    var new_parent_data = '';
    //var sub_child_indx = '['+row_submenu+'][child]['+child+'][child]['+total_sub_child+']';
    for(i=0; i<parent_data_arr.length;i++)
    {
      if(i == (parent_data_arr.length)-1)
      {
        sub_child_indx = sub_child_indx + '['+parent_data_arr[i]+']';
      }
      else
      {
       sub_child_indx = sub_child_indx + '['+parent_data_arr[i]+'][child]'; 
      }

     if(i < (parent_data_arr.length)-1)
      { 
        new_parent_data = new_parent_data+parent_data_arr[i]+',';
      } 
      
    }

    $(".subchild_"+id).attr('parent_data',new_parent_data+last_element);


    html  = '<tr id="sub_menu_row_'+ upddate_submenu_id + '" colspan="7">';
    html += ' <td class="text-left">';
    html += ' <td class="text-left col-sm-9" colspan="4">';
    html += '<input type="hidden" name="info'+ sub_child_indx +'[type]" value="megamenu">';
    html += '<input type="hidden" name="info'+ sub_child_indx +'[megamenu_type]" value="1">';
    html += '   <input type="hidden" class="form-control"  name="info'+ sub_child_indx +'[menu_db_id]" value="0"/>'
    html += '   <input type="hidden" class="form-control"  name="info'+ sub_child_indx+'[store_id]" value="<?php echo $select_seller_store_id; ?>"/>';
    html += '   <input type="hidden" class="form-control"  name="info'+ sub_child_indx +'[menu_id]" value="<?php echo $this->request->get['menu_id']; ?>"/>';  
    html += '   <textarea class="form-control megamenu" name="info'+ sub_child_indx +'[megamenu]" value=""/> </textarea> <br />';
    html += ' </td>';
    html += ' <td class="text-left"><select name="info'+ sub_child_indx +'[status]" id="input-status" class="form-control"><option value="1" selected="selected"><?php echo $text_enabled; ?></option><option value="0"><?php echo $text_disabled; ?></option></select></td>';
    html += ' <td class="text-right"><button type="button" onclick="deleteRow('+upddate_submenu_id+')" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger button_remove"><i class="fa fa-minus-circle"></i></button></td>';
    html += '</tr>';
    html += '<tr id="child_row_' + menu_row + '"><td colspan="7">';
    html += '</td></tr>';
   $('#sub_child_row_'+id).after(html);

 $('.megamenu').summernote({
    height: 300
  });
    
}


 function deleteRow(id){
   $("#sub_menu_row_"+id).remove();
   $("#sub_child_row_"+id).remove();
 }


  $('.megamenu').summernote({
    height: 300
  });

  $(document).delegate( ".addmegamenu", "click", function() {
    var id = $(this).attr('data-id');
    var val = $(this).attr('total-child');
    var count_sub_menu = parseInt($(this).attr('count_sub_menu'));
    var total_child = parseInt(val);
    if(id == 0){
      child = '['+menu_row+']';
      childcount = menu_row;
      parent_menu_row_css = 'parent_menu_row';
    }else{
      if(count_sub_menu > 0){
        total_child += count_sub_menu;
        child = '[' + id + '][child][' + total_child + ']';
        parent_menu_row_css = '';
      }else{
        child = '[' + id + '][child][' + total_child + ']';
        parent_menu_row_css = '';
      }
    }
    html  = '<tr id="menu_row_' + menu_row + '" colspan="7" class="'+parent_menu_row_css+'">';
    html += ' <td class="text-left">';
    html += '   <input type="hidden" class="form-control"  name="info'+ child +'[menu_db_id]" value="0"/>';
    html += '   <input type="hidden" name="info'+ child +'[type]" value="child">';
    html += '   <input type="hidden" class="form-control"  name="info'+ child+'[store_id]" value="<?php echo $select_seller_store_id; ?>"/>';
    html += '   <input type="hidden" name="info'+ child +'[megamenu_type]" value="1">';
    html += '   <input type="hidden" class="form-control"  name="info'+ child +'[menu_id]" value="<?php echo $this->request->get['menu_id']; ?>"/></td>';
    html += ' <td class="text-left col-sm-9" colspan="4">';
    html += '   <textarea class="form-control megamenu" name="info'+ child +'[megamenu]" value=""/> </textarea> <br />';
    html += ' </td>';
    html += ' <td class="text-left"><select name="info'+ child +'[status]" id="input-status" class="form-control"><option value="1" selected="selected"><?php echo $text_enabled; ?></option><option value="0"><?php echo $text_disabled; ?></option></select></td>';
    html += ' <td class="text-right "><button type="button" row_number= '+menu_row+'  data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger button_remove"><i class="fa fa-minus-circle"></i></button></td>';
    html += '</tr>';
    html += '<tr id="child_row_' + menu_row + '"><td colspan="7">';
    html += '</td></tr>';
    $('#menus tbody.child_'+id).append(html);
    menu_row++;
    total_child = total_child + 1;
    $(this).attr('total-child', total_child);
  });

</script>
<script type="text/javascript">
  // $(".seller_store").change(function () {
  //   var store_id  = $(this).val();

  //   $.ajax({
  //     url: 'index.php?route=seller/website-settings/change_seller_store ',
  //     type: 'POST',
  //     dataType: 'html',
  //     data: {store_id: store_id},
  //     success: function () {
  //       window.location.reload();
  //     }
  //   });
  // });
  $(".menu_store_status").change(function () {
    var menu_status  = $(this).val();
    var menu_lang = $('.menu_hid_store_language').val();
    $.ajax({
      url: 'index.php?route=catalog/menu/menu_store_status&token=<?php echo $this->session->data['token']; ?> ',
      type: 'POST',
      dataType: 'html',
      data: {menu_status: menu_status, menu_lang: menu_lang},
      success: function () {
        window.location.reload();
      }
    });
  });
  $(".menu_store_language").change(function () {
    var menu_language = $(this).val();
    if(menu_language != 0){
      $('.menu_hid_store_language').attr('value', menu_language);
      $('#form-menu_1').submit();  
    }
  });

  $(document).delegate( ".select_item", "change", function() {
      var select_item = $(this).val();
      var data_id = $(this).attr('html-data');
      $('.promotions_row_'+data_id).hide();
      $('.promotions_row_'+data_id).addClass('hidden');
      if(select_item == "category"){
        $('#category_value_'+data_id).attr('placeholder',' Select Category');
        $('#category_value_'+data_id).attr('value','');
        $('#category_value_hidden'+data_id).attr('value','');
        $('.promotions_row_'+data_id).show();
        $('.promotions_row_'+data_id).removeClass('hidden');
      }else if(select_item == "page"){
        $('#category_value_'+data_id).attr('placeholder',' Select Page');
        $('#category_value_'+data_id).attr('value','');
        $('#category_value_hidden'+data_id).attr('value','');
      }else{
        $('#category_value_'+data_id).attr('placeholder',' http://www.google.com');
        $('#category_value_'+data_id).attr('value','');
        $('#category_value_hidden'+data_id).attr('value',0);
      }
  });

  $(document).delegate( ".category_value", "focusin", function() {
    var call_function = "";
    var select_item = $("select#select_item_"+$(this).attr("html-data")+" option:selected").val();
    var token = '<?php echo $this->session->data['token']; ?>';
    //alert(select_item)
    var flagt = 1;
    if(select_item == "category"){
      $(this).attr('placeholder',' Select Category');
      call_function = "autocompleteCategory";
      flagt = 0;
    }else if(select_item == "page"){
      $(this).attr('placeholder',' Select Page');
      call_function = "autoComplatePages";
      flagt = 0;
    }else{
      $("#category_value_hidden"+$(this).attr("html-data")).val(0);
      $(this).attr('placeholder',' http://www.google.com');
      var call_function = "";
      flagt = 0;
    }

    if(flagt == '0') {
      var store_id = ($('.seller_store').val()) ? $('.seller_store').val() : "<?php echo $select_seller_store_id ;?>";
      $(this).autocomplete({
        'source': function (request, response) {
            $.ajax({
              url: 'index.php?route=catalog/menu/' + call_function + '&store_id=' + store_id + '&filter_name=' + encodeURIComponent(request) + '&token=' + token,
              dataType: 'json',
              success: function (json) {
                response($.map(json, function (item) {
                  return {
                    label: item['name'],
                    value: item['category_id']
                  }
                }));
              }
            });
        },
        'select': function (item) {
            
            var full_label = item['label'];
            var arrow_position = full_label.lastIndexOf(">");
            var child_cat_lable = full_label.substring(arrow_position+1).trim();
          
            
          $(this).val(child_cat_lable);
          hidden_field_id = $(this).attr('data-hidden-id');
          //alert(hidden_field_id);
          //alert(item['value']);
          $('#' + hidden_field_id).val(item['value']);
        }
      });
    }
  });
</script>


<!-- remove menus -->
<script type="text/javascript">
  $(document).ready(function(){
    var token = '<?php echo $this->session->data['token']; ?>';
    $('.parent_menu').click(function() {
     if(confirm('Do you really want delete this menu item?'))
    {    
      var parent_menu_item_id = $(this).attr('parent_menu_item_id');
      var row_type = $(this).attr('row-type');
      var store_id = $('.seller_store').val();
      var child_row = $('.table_row_'+parent_menu_item_id).length;
      if(row_type == 'parent'){
        if(child_row > 0){
          ok = confirm('Are you sure do you want to that menu with sub menu');
          if(ok){
            $.ajax({
              type: 'POST',
              url: 'index.php?route=catalog/menu/deleteMenuAll&token=' + token,
              data: {parent_menu_item_id,store_id},
              success: function () {
                window.location.reload();
              }
            });
          }
        }else{
          $.ajax({
            type: 'POST',
            url: 'index.php?route=catalog/menu/deleteMenuAll&token=' + token,
            data: {parent_menu_item_id,store_id},
            success: function () {
              window.location.reload();
            }
          });
        }
      }else{
        $.ajax({
          type: 'POST',
          url: 'index.php?route=catalog/menu/deleteMenuAll&token=' + token,
          data: {parent_menu_item_id,store_id},
          success: function (html) {
            window.location.reload();
          }
        });
      }
     }
    });
  });
</script>
<!-- Remove blanks menus-->
<script type="text/javascript">
  $(document).delegate( ".button_remove", "click", function() {
      var row_number = $(this).attr('row_number');
      $("#sub_menu_row_"+row_number).remove();
      $('tr#menu_row_'+row_number).remove();
      $('tr#child_row_'+row_number).remove();
      $('.row_'+row_number).remove();
      //$('#sub_child_row_'+row_number).remove();
    });
</script>
<!-- validation for blank menus -->
<script type="text/javascript">
  $('.save_all_menu').click(function(){
    var submit_fail = 0;

    $('.category_value').each(function() {
      if($(this).val()==''){
        $('.menu_box_error').text('Please enter menu value').parent().css('text-align','center');
        submit_fail = 1;
        return false;
      }
    });
    $('.menu_title').each(function() {
      if($(this).val()==''){
        $('.menu_box_error').text('Please enter menu title').parent().css('text-align','center');
        submit_fail = 1;
        return false;
      }
    });
    if(submit_fail == '0') {
      $("form#form-menu").submit();
    }else{
      return false;
    }
  });
</script>
<?php echo $footer; ?>