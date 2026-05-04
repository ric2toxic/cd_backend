<?php
echo $header;
echo $column_left;
$sor_value = '';

if (!$sor_enable) {

  $sor_value = 'disabled="disabled"';
}

?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="button" id="form-button" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
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
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?></h3>
        <div class="product_form_model_sku_header">
          <b>Model : <span><?php echo $model; ?></span></b>
          <b>SKU : <span><?php echo $sku; ?></span></b>
        </div>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-product" class="form-horizontal">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-general" data-toggle="tab"><?php echo $tab_general; ?></a></li>
            <li><a href="#tab-data" data-toggle="tab"><?php echo $tab_data; ?></a></li>
            <li><a href="#tab-links" data-toggle="tab"><?php echo $tab_links; ?></a></li>
            <li><a href="#tab-attribute" data-toggle="tab"><?php echo $tab_attribute; ?></a></li>
            <?php if ($show_option_tab == 1) { ?>
            <li><a href="#tab-option" data-toggle="tab"><?php echo $tab_option; ?></a></li>
            <?php } ?>
            <li><a href="#tab-recurring" data-toggle="tab"><?php echo $tab_recurring; ?></a></li>
            <li><a href="#tab-discount" data-toggle="tab"><?php echo $tab_discount; ?></a></li>
            <li><a href="#tab-special" data-toggle="tab"><?php echo $tab_special; ?></a></li>
            <li><a href="#tab-image" data-toggle="tab"><?php echo $tab_image; ?></a></li>
            <li><a href="#tab-design" data-toggle="tab"><?php echo $tab_design; ?></a></li>
            <li><a href="#tab-store_data" data-toggle="tab"><?php echo $tab_store_data; ?></a></li>
            <?php if ($show_associate_tab == 1) { ?>
            <li><a href="#tab-associate_products" data-toggle="tab"><?php echo $tab_associate_products; ?></a></li>
            <?php } ?>
          </ul>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="tab-general">
              <ul class="nav nav-tabs" id="language">
                <?php foreach ($languages as $language) { ?>
                <li><a href="#language<?php echo $language['language_id']; ?>" data-toggle="tab"><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></a></li>
                <?php } ?>
              </ul>
              <div class="tab-content">
                <?php foreach ($languages as $language) { ?>
                <div class="tab-pane" id="language<?php echo $language['language_id']; ?>">
                  <div class="form-group required">
                    <label class="col-sm-2 control-label" for="input-name<?php echo $language['language_id']; ?>"><?php echo $entry_name; ?></label>
                    <div class="col-sm-10">
                      <input type="text" name="product_description[<?php echo $language['language_id']; ?>][name]" value="<?php echo isset($product_description[$language['language_id']]) ? $product_description[$language['language_id']]['name'] : ''; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name<?php echo $language['language_id']; ?>" class="form-control edit_track" data-change="false" data-old-value="<?php echo isset($product_description[$language['language_id']]) ? $product_description[$language['language_id']]['name'] : ''; ?>" />
                      <?php if (isset($error_name[$language['language_id']])) { ?>
                      <div class="text-danger"><?php echo $error_name[$language['language_id']]; ?></div>
                      <?php } ?>
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-set-description<?php echo $language['language_id']; ?>"><?php echo $entry_set_description; ?></label>
                    <div class="col-sm-10">
                      <input type="text" name="product_description[<?php echo $language['language_id']; ?>][set_description]" value="<?php echo isset($product_description[$language['language_id']]) ? htmlentities($product_description[$language['language_id']]['set_description']) : ''; ?>" placeholder="<?php echo $entry_set_description; ?>" id="input-set-description<?php echo $language['language_id']; ?>" class="form-control edit_track" data-change="false" data-old-value="<?php echo isset($product_description[$language['language_id']]) ? htmlentities($product_description[$language['language_id']]['set_description']) : ''; ?>" />
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-description<?php echo $language['language_id']; ?>"><?php echo $entry_description; ?></label>
                    <div class="col-sm-10">
                      <textarea name="product_description[<?php echo $language['language_id']; ?>][description]" placeholder="<?php echo $entry_description; ?>" id="input-description<?php echo $language['language_id']; ?>" class="edit_track description" data-change="false" data-old-value="<?php echo isset($product_description[$language['language_id']]) ? $product_description[$language['language_id']]['description'] : ''; ?>"><?php echo isset($product_description[$language['language_id']]) ? $product_description[$language['language_id']]['description'] : ''; ?></textarea>
                    </div>
                  </div>

                  <div class="form-group required">
                    <label class="col-sm-2 control-label" for="input-meta-title<?php echo $language['language_id']; ?>"><?php echo $entry_meta_title; ?></label>
                    <div class="col-sm-10">
                      <input type="text" name="product_description[<?php echo $language['language_id']; ?>][meta_title]" value="<?php echo isset($product_description[$language['language_id']]) ? $product_description[$language['language_id']]['meta_title'] : ''; ?>" placeholder="<?php echo $entry_meta_title; ?>" id="input-meta-title<?php echo $language['language_id']; ?>" class="form-control" />
                      <?php if (isset($error_meta_title[$language['language_id']])) { ?>
                      <div class="text-danger"><?php echo $error_meta_title[$language['language_id']]; ?></div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group required">
                    <label class="col-sm-2 control-label" for="input-meta-description<?php echo $language['language_id']; ?>"><?php echo $entry_meta_description; ?></label>
                    <div class="col-sm-10">
                      <textarea name="product_description[<?php echo $language['language_id']; ?>][meta_description]" rows="5" placeholder="<?php echo $entry_meta_description; ?>" id="input-meta-description<?php echo $language['language_id']; ?>" class="form-control"><?php echo isset($product_description[$language['language_id']]) ? $product_description[$language['language_id']]['meta_description'] : ''; ?></textarea>
                      <?php if (isset($error_meta_description[$language['language_id']])) { ?>
                      <div class="text-danger"><?php echo $error_meta_description[$language['language_id']]; ?></div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group required">
                    <label class="col-sm-2 control-label" for="input-meta-keyword<?php echo $language['language_id']; ?>"><?php echo $entry_meta_keyword; ?></label>
                    <div class="col-sm-10">
                      <textarea name="product_description[<?php echo $language['language_id']; ?>][meta_keyword]" rows="5" placeholder="<?php echo $entry_meta_keyword; ?>" id="input-meta-keyword<?php echo $language['language_id']; ?>" class="form-control"><?php echo isset($product_description[$language['language_id']]) ? $product_description[$language['language_id']]['meta_keyword'] : ''; ?></textarea>
                      <?php if (isset($error_meta_keyword[$language['language_id']])) { ?>
                      <div class="text-danger"><?php echo $error_meta_keyword[$language['language_id']]; ?></div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-tag<?php echo $language['language_id']; ?>"><span data-toggle="tooltip" title="<?php echo $help_tag; ?>"><?php echo $entry_tag; ?></span></label>
                    <div class="col-sm-10">
                      <input type="text" name="product_description[<?php echo $language['language_id']; ?>][tag]" value="<?php echo isset($product_description[$language['language_id']]) ? $product_description[$language['language_id']]['tag'] : ''; ?>" placeholder="<?php echo $entry_tag; ?>" id="input-tag<?php echo $language['language_id']; ?>" class="form-control" />
                    </div>
                  </div>
                </div>
                <?php } ?>
              </div>
            </div>
            <div class="tab-pane" id="tab-data">
              <!--<div class="form-group">
                <label class="col-sm-2 control-label" for="input-image"><?php echo $entry_image; ?></label>
                <div class="col-sm-10">
                  <a href="" id="thumb-image" data-toggle="image" class="img-thumbnail"><img src="<?php echo $thumb; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a>
                  <input type="hidden" name="image" value="<?php echo $image; ?>" id="input-image" />
                </div>
              </div>-->
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-model"><?php echo $entry_model; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="model" value="<?php echo $model; ?>" placeholder="<?php echo $entry_model; ?>" id="input-model" class="form-control edit_track" data-change="false" data-old-value="<?php echo $model; ?>" />
                  <?php if ($error_model) { ?>
                  <div class="text-danger"><?php echo $error_model; ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-sku"><span data-toggle="tooltip" title="<?php echo $help_sku; ?>"><?php echo $entry_sku; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="sku" value="<?php echo $sku; ?>" placeholder="<?php echo $entry_sku; ?>" id="input-sku" class="form-control edit_track" data-change="false" data-old-value="<?php echo $sku; ?>" />
                </div>
              </div>
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-hsn_code"><span data-toggle="tooltip" title="<?php echo $help_hsn_code; ?>"><?php echo $entry_hsn_code; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="hsn_code" value="<?php echo $hsn_code; ?>" placeholder="<?php echo $entry_hsn_code; ?>" id="input-hsn_code" class="form-control edit_track" data-change="false" data-old-value="<?php echo $hsn_code; ?>" />
                  <?php if ($error_hsn_code) { ?>
                  <div class="text-danger"><?php echo $error_hsn_code; ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-price">
                  <span data-toggle="tooltip" title="<?php echo $help_price; ?>"><?php echo $entry_price; ?></span>
                </label>
                <div class="col-sm-10">
                  <input type="text" name="price" value="<?php echo $price; ?>" placeholder="<?php echo $entry_price; ?>" id="input-price" class="form-control edit_track" data-change="false" data-old-value="<?php echo $price; ?>" />
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-mrp-price">
                  <span data-toggle="tooltip" title="<?php //echo $help_price; ?>"><?php echo $entry_mrp; ?></span>
                </label>
                <div class="col-sm-10">
                  <input type="text" name="mrp" value="<?php echo $mrp; ?>" placeholder="<?php echo $entry_mrp; ?>" id="input-mrp-price" class="form-control edit_track" data-change="false" data-old-value="<?php echo $mrp; ?>"/>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-piece-in-set">
                  <span data-toggle="tooltip" title="<?php echo $help_piece_in_set; ?>"><?php echo $entry_piece_in_set; ?></span>
                </label>
                <div class="col-sm-10">
                  <input <?php echo $sor_value;?> type="text" name="piece_in_set" value="<?php echo $piece_in_set; ?>" placeholder="<?php echo $entry_piece_in_set; ?>" id="input-piece-in-set" class="form-control edit_track" data-change="false" data-old-value="<?php echo $piece_in_set; ?>" />
                  <?php
                  if ($sor_value!='') {
                  ?>
                   <input type="hidden" name="piece_in_set" value="<?php echo $piece_in_set; ?>" placeholder="<?php echo $entry_piece_in_set; ?>" id="input-piece-in-set" class="form-control edit_track" data-change="false" data-old-value="<?php echo $piece_in_set; ?>" />
                  <?php
                  }
                  ?>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-commission">
                  <span data-toggle="tooltip" title="<?php echo $help_commission; ?>"><?php echo $entry_commission; ?></span>
                </label>
                <div class="col-sm-10">
                  <input type="text" name="commission" value="<?php echo $commission; ?>" placeholder="<?php echo $entry_commission; ?>" id="input-commission" class="form-control edit_track" data-change="false" data-old-value="<?php echo $commission; ?>"/>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-quantity"><?php echo $entry_quantity; ?></label>
                <div class="col-sm-10">
                  <input <?php echo $sor_value;?> type="text" name="quantity" value="<?php echo $quantity; ?>" placeholder="<?php echo $entry_quantity; ?>" id="input-quantity" class="form-control edit_track" data-change="false" data-old-value="<?php echo $quantity; ?>" />
                  <?php
                  if ($sor_value!='') {
                  ?>
                   <input type="hidden" name="quantity" value="<?php echo $quantity; ?>" placeholder="<?php echo $entry_quantity; ?>" id="input-quantity" class="form-control edit_track" data-change="false" data-old-value="<?php echo $quantity; ?>" />
                  <?php
                  }
                  ?>
                </div>
              </div>

              <!-- unit_id dropdown -->
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-unit_id"><?php echo $entry_unit_id; ?></label>
                <div class="col-sm-10">
                  <select name="unit_id" id="input-unit_id" class="form-control">
                                        <option value="0"><?php echo $entry_unit_id; ?></option>
                                          <?php if(!empty($units_array)) { foreach ($units_array as $unit) { ?>
                                                <?php if (isset($unit['unit_name']) && $unit['unit_id'] == $unit_id && !empty($unit_id)) { ?>
                                                        <option value="<?php echo $unit['unit_id']; ?>" selected="selected"><?php echo $unit['unit_name']; ?></option>
                        <?php } else { ?>
                                                        <option value="<?php echo $unit['unit_id']; ?>"><?php echo $unit['unit_name']; ?></option>
                          <?php } ?>
                      <?php } ?>
                    <?php } ?>
                  </select>
                    <?php /* if ($error_unit_id) { ?>
                        <div class="text-danger"><?php echo $error_unit_id; ?></div>
                    <?php } */?>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-single"><?php echo $entry_single; ?></label>
                <div class="col-sm-10">
                  <select name="is_single" id="input-single" class="form-control edit_track" data-change="false" data-old-value="<?php echo $is_single; ?>">
                    <?php if ($is_single) { ?>
                    <option value="1" selected="selected"><?php echo $text_yes; ?></option>
                    <option value="0"><?php echo $text_no; ?></option>
                    <?php } else { ?>
                    <option value="1"><?php echo $text_yes; ?></option>
                    <option value="0" selected="selected"><?php echo $text_no; ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-minimum"><span data-toggle="tooltip" title="<?php echo $help_minimum; ?>"><?php echo $entry_minimum; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="minimum" value="<?php echo $minimum; ?>" placeholder="<?php echo $entry_minimum; ?>" id="input-minimum" class="form-control edit_track" data-change="false" data-old-value="<?php echo $minimum; ?>" />
                  <?php if ($error_minimum) { ?>
                  <div class="text-danger"><?php echo $error_minimum; ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-subtract"><?php echo $entry_subtract; ?></label>
                <div class="col-sm-10">
                  <select name="subtract" id="input-subtract" class="form-control edit_track" data-change="false" data-old-value="<?php echo $subtract; ?>">
                    <?php if ($subtract) { ?>
                    <option value="1" selected="selected"><?php echo $text_yes; ?></option>
                    <option value="0"><?php echo $text_no; ?></option>
                    <?php } else { ?>
                    <option value="1"><?php echo $text_yes; ?></option>
                    <option value="0" selected="selected"><?php echo $text_no; ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-stock-status"><span data-toggle="tooltip" title="<?php echo $help_stock_status; ?>"><?php echo $entry_stock_status; ?></span></label>
                <div class="col-sm-10">
                  <select name="stock_status_id" id="input-stock-status" class="form-control edit_track" data-change="false" data-old-value="<?php echo $stock_status_id; ?>">
                    <?php foreach ($stock_statuses as $stock_status) { ?>
                    <?php if ($stock_status['stock_status_id'] == $stock_status_id) { ?>
                    <option value="<?php echo $stock_status['stock_status_id']; ?>" selected="selected"><?php echo $stock_status['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $stock_status['stock_status_id']; ?>"><?php echo $stock_status['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-product-rating"><?php echo $entry_product_rating; ?></label>
                <div class="col-sm-10">
                  <select name="rating" id="input-product-rating" class="form-control edit_track" data-change="false" data-old-value="<?php echo $product_rating; ?>">
                    <option value="">--Select--</option>
                    <?php foreach (PRODUCT_RATING_CONFIG as $rating_key => $rating_value) { ?>
                    <?php if ($product_rating == $rating_key) { ?>
                    <option value="<?php echo $rating_key; ?>" selected="selected"><?php echo $rating_value; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $rating_key; ?>"><?php echo $rating_value; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_shipping; ?></label>
                <div class="col-sm-10">
                  <label class="radio-inline">
                    <?php if ($shipping) { ?>
                    <input type="radio" name="shipping" value="1" checked="checked"/>
                    <?php echo $text_yes; ?>
                    <?php } else { ?>
                    <input type="radio" name="shipping" value="1" class="edit_track" data-change="false" data-old-value="<?php echo $shipping; ?>"/>
                    <?php echo $text_yes; ?>
                    <?php } ?>
                  </label>
                  <label class="radio-inline">
                    <?php if (!$shipping) { ?>
                    <input type="radio" name="shipping" value="0" checked="checked"/>
                    <?php echo $text_no; ?>
                    <?php } else { ?>
                    <input type="radio" name="shipping" value="0" class="edit_track" data-change="false" data-old-value="<?php echo $shipping; ?>"/>
                    <?php echo $text_no; ?>
                    <?php } ?>
                  </label>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-keyword"><span data-toggle="tooltip" title="<?php echo $help_keyword; ?>"><?php echo $entry_keyword; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="keyword" value="<?php echo $keyword; ?>" placeholder="<?php echo $entry_keyword; ?>" id="input-keyword" class="form-control" readonly/>
                  <?php if ($error_keyword) { ?>
                  <div class="text-danger"><?php echo $error_keyword; ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-exp-dispatch-date"><?php echo $entry_exp_dispatch_date; ?></label>
                <div class="col-sm-4">
                  <input type="text" name="expected_dispatch_date" value="<?php echo $expected_dispatch_date; ?>" placeholder="<?php echo $entry_exp_dispatch_date; ?>" data-date-format="YYYY-MM-DD" id="input-exp-dispatch-date" class="form-control edit_track" data-change="false" data-old-value="<?php echo $expected_dispatch_date; ?>" />
                  <!--<div class="input-group date">
                    <input type="text" name="expected_dispatch_date" value="<?php echo $expected_dispatch_date; ?>" placeholder="<?php echo $entry_exp_dispatch_date; ?>" data-date-format="YYYY-MM-DD" id="input-exp-dispatch-date" class="form-control edit_track" data-change="false" data-old-value="<?php echo $expected_dispatch_date; ?>" />
                    <span class="input-group-btn">
                    <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
                    </span>
                    </div>-->
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-date-available"><?php echo $entry_date_available; ?></label>
                <div class="col-sm-4">
                  <input type="text" name="date_available" value="<?php echo $date_available; ?>" placeholder="<?php echo $entry_date_available; ?>" data-date-format="YYYY-MM-DD" id="input-date-available" class="form-control edit_track" data-change="false" data-old-value="<?php echo $date_available; ?>" />
                  <!--<div class="input-group date">
                    <input type="text" name="date_available" value="<?php echo $date_available; ?>" placeholder="<?php echo $entry_date_available; ?>" data-date-format="YYYY-MM-DD" id="input-date-available" class="form-control edit_track" data-change="false" data-old-value="<?php echo $date_available; ?>" />
                    <span class="input-group-btn">
                    <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
                    </span>
                    </div>-->
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_cod_available; ?></label>
                <div class="col-sm-4">
                  <input type="checkbox" name="cod_available"  value="1" class="form-control edit_track" data-block-name="profile" id="cod_available" data-change="false" data-old-value="<?php echo $cod_available;?>" <?php if( $cod_available ){ ?> checked <?php } ?> />
                  <input type="hidden" name="cod_available"  value="0"  id="cod_available_hidden" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_non_returnable; ?></label>
                <div class="col-sm-4">
                  <input type="checkbox" name="non_returnable"  value="1" class="form-control edit_track" data-block-name="profile" id="non_returnable" data-change="false" data-old-value="<?php echo $non_returnable;?>" <?php if( $non_returnable ){ ?> checked <?php } ?> />
                  <input type="hidden" name="non_returnable"  value="0"  id="non_returnable_hidden" />
                </div>
              </div>

              <?php if ($show_associate_checkbox == 1) { ?>
                <div class="form-group">
                  <label class="col-sm-2 control-label"><?php echo $entry_is_associate; ?></label>
                  <div class="col-sm-4">
                    <input type="checkbox" name="is_associate"  value="1" class="form-control edit_track" data-block-name="profile" id="is_associate" data-change="false" data-old-value="<?php echo $is_associate;?>" <?php if( $is_associate ){ ?> checked <?php } ?> />
                    <input type="hidden" name="is_associate"  value="0"  id="is_associate_hidden" />
                  </div>
                </div>
              <?php } ?>

              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_sor; ?></label>
                <div class="col-sm-4">
                  <input type="checkbox" name="sor_product_terms"  value="1" class="form-control edit_track" data-block-name="profile" id="sor_product_terms" data-change="false" data-old-value="<?php echo $sor_product_terms;?>" <?php if( $sor_product_terms ){ ?> checked <?php } ?> />
                </div>
              </div>

              <div class="form-group" id="sor_product_terms_area" <?php if(!$sor_product_terms){ ?> style="display: none;" <?php } ?>>
                <label class="col-sm-2 control-label" for="input-length"><?php echo $entry_sor_type; ?></label>
                    <div class="col-sm-4">
                      <select name="sor_type" id="input-length-class" class="form-control">
                       <option value="regular" <?php if($sor_type == 'regular') { echo "selected"; } ?>>Regular</option>
                        <option value="wsb-credit" <?php if($sor_type == 'wsb-credit') { echo "selected"; } ?>>Wsb-Credit</option>
                  </select>
                   </div>

                   <label class="col-sm-2 control-label" for="input-length"><?php echo $entry_sor_limit_days; ?></label>
                    <div class="col-sm-4">
                     <input type="text" name="sor_days" value="<?php echo $sor_days; ?>" placeholder="<?php echo $entry_sor_limit_days; ?>" id="input-length" class="form-control" />
                  </select>
                   </div>
              </div>

              <!-- <div class="form-group">
                <label class="col-sm-2 control-label" for="input-length"><?php echo $entry_dimension; ?></label>
                <div class="col-sm-10">
                  <div class="row">
                    <div class="col-sm-4">
                      <input type="text" name="length" value="<?php echo $length; ?>" placeholder="<?php echo $entry_length; ?>" id="input-length" class="form-control" />
                    </div>
                    <div class="col-sm-4">
                      <input type="text" name="width" value="<?php echo $width; ?>" placeholder="<?php echo $entry_width; ?>" id="input-width" class="form-control" />
                    </div>
                    <div class="col-sm-4">
                      <input type="text" name="height" value="<?php echo $height; ?>" placeholder="<?php echo $entry_height; ?>" id="input-height" class="form-control" />
                    </div>
                  </div>
                </div>
              </div> -->
              <!-- <div class="form-group">
                <label class="col-sm-2 control-label" for="input-length-class"><?php echo $entry_length_class; ?></label>
                <div class="col-sm-10">
                  <select name="length_class_id" id="input-length-class" class="form-control">
                    <?php foreach ($length_classes as $length_class) { ?>
                    <?php if ($length_class['length_class_id'] == $length_class_id) { ?>
                    <option value="<?php echo $length_class['length_class_id']; ?>" selected="selected"><?php echo $length_class['title']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $length_class['length_class_id']; ?>"><?php echo $length_class['title']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div> -->
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-weight"><?php echo $entry_weight; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="weight" value="<?php echo $weight; ?>" placeholder="<?php echo $entry_weight; ?>" id="input-weight" class="form-control edit_track" data-change="false" data-old-value="<?php echo $weight; ?>" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-weight-class"><?php echo $entry_weight_class; ?></label>
                <div class="col-sm-10">
                  <select name="weight_class_id" id="input-weight-class" class="form-control">
                    <?php foreach ($weight_classes as $weight_class) { ?>
                    <?php if ($weight_class['weight_class_id'] == $weight_class_id) { ?>
                    <option value="<?php echo $weight_class['weight_class_id']; ?>" selected="selected"><?php echo $weight_class['title']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $weight_class['weight_class_id']; ?>"><?php echo $weight_class['title']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
                <div class="col-sm-10">
                  <select name="status" id="input-status" class="form-control edit_track" data-change="false" data-old-value="<?php echo $status; ?>">
                    <?php
                      if(isset($product_status_list)){
                        foreach($product_status_list as $product_status){
                          if($product_status['product_status_id'] == $status){
                            $selected_status = 'selected';
                          }else{
                            $selected_status = '';
                          }
                  ?>
                    <option value="<?php echo $product_status['product_status_id']?>" <?php echo $selected_status;?> ><?php echo $product_status['name']; ?></option>
                    <?php } } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_sort_order; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="sort_order" class="edit_track form-control" data-old-value="<?php echo $sort_order; ?>" data-change="false" value="<?php echo $sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-only-for-search"><?php echo $entry_only_for_search; ?></label>
                <div class="col-sm-10">
                  <textarea name="only_for_search" rows="5" class="edit_track form-control" data-old-value="<?php echo $only_for_search; ?>" data-change="false" placeholder="<?php echo $entry_only_for_search; ?>" id="input-only-for-search" class="form-control"><?php echo $only_for_search; ?></textarea>
                </div>
              </div>
            </div>
            <div class="tab-pane" id="tab-links">
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-manufacturer"><span data-toggle="tooltip" title="<?php echo $help_manufacturer; ?>"><?php echo $entry_manufacturer; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="manufacturer" value="<?php echo $manufacturer ?>" placeholder="<?php echo $entry_manufacturer; ?>" id="input-manufacturer" class="form-control" />
                  <input type="hidden" name="manufacturer_id" value="<?php echo $manufacturer_id; ?>" />
                </div>
              </div>
              <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-category"><span data-toggle="tooltip" title="<?php echo $help_category; ?>"><?php echo $entry_category; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="category" value="" placeholder="<?php echo $entry_category; ?>" id="input-category" class="form-control" />
                  <div id="product-category" class="well well-sm" style="height: 150px; overflow: auto;">
                    <?php foreach ($product_categories as $product_category) { ?>
                    <div id="product-category<?php echo $product_category['category_id']; ?>"><i class="fa fa-minus-circle"></i> <?php echo $product_category['name']; ?>
                      <input type="hidden" name="product_category[]" value="<?php echo $product_category['category_id']; ?>" />
                    </div>
                    <?php } ?>
                  </div>
                </div>
              </div>
              <div class="form-group" style="display: none;" >
                    <?php foreach ($product_categories as $product_category) { ?>
                    <div id="old_product-category<?php echo $product_category['category_id']; ?>"><i class="fa fa-minus-circle"></i> <?php echo $product_category['name']; ?>
                      <input type="hidden" name="old_product_category[]" value="<?php echo $product_category['category_id']; ?>" />
                    </div>
                    <?php } ?>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-filter"><span data-toggle="tooltip" title="<?php echo $help_filter; ?>"><?php echo $entry_filter; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="filter" value="" placeholder="<?php echo $entry_filter; ?>" id="input-filter" class="form-control" />
                  <div id="product-filter" class="well well-sm" style="height: 150px; overflow: auto;">
                    <?php foreach ($product_filters as $product_filter) { ?>
                    <div id="product-filter<?php echo $product_filter['filter_id']; ?>"><i class="fa fa-minus-circle"></i> <?php echo $product_filter['name']; ?>
                      <input type="hidden" name="product_filter[]" value="<?php echo $product_filter['filter_id']; ?>" />
                    </div>
                    <?php } ?>
                  </div>
                </div>
              </div>
              <div class="form-group" style="display: none;" >
                    <?php foreach ($product_filters as $product_filter) { ?>
                    <div id="old_product-filter<?php echo $product_filter['filter_id']; ?>"><i class="fa fa-minus-circle"></i> <?php echo $product_filter['name']; ?>
                      <input type="hidden" name="old_product_filter[]" value="<?php echo $product_filter['filter_id']; ?>" />
                    </div>
                    <?php } ?>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_store; ?></label>
                <div class="col-sm-10">
                  <div class="well well-sm" style="height: 150px; overflow: auto;">
                    <div class="checkbox">
                      <label>
                        <?php if (in_array(0, $product_store)) { ?>
                        <input type="checkbox" name="product_store[]" value="0" checked="checked" />
                        <?php echo $text_default; ?>
                        <?php } else { ?>
                        <input type="checkbox" name="product_store[]" value="0" />
                        <?php echo $text_default; ?>
                        <?php } ?>
                      </label>
                    </div>
                    <?php foreach ($stores as $store) { ?>
                    <div class="checkbox">
                      <label>
                        <?php if (in_array($store['store_id'], $product_store)) { ?>
                        <input type="checkbox" name="product_store[]" value="<?php echo $store['store_id']; ?>" checked="checked" />
                        <?php echo $store['name']; ?>
                        <?php } else { ?>
                        <input type="checkbox" name="product_store[]" value="<?php echo $store['store_id']; ?>" />
                        <?php echo $store['name']; ?>
                        <?php } ?>
                      </label>
                    </div>
                    <?php } ?>
                  </div>
                </div>
              </div>
              <div class="form-group" style="display: none;">
                    <?php if (in_array(0, $product_store)) { ?>
                        <input type="checkbox" name="old_product_store[]" value="0" checked="checked" />
                        <?php echo $text_default; ?>
                    <?php } ?>
                    <?php foreach ($stores as $store) { ?>
                    <div class="checkbox">
                      <label>
                        <?php if (in_array($store['store_id'], $product_store)) { ?>
                        <input type="checkbox" name="old_product_store[]" value="<?php echo $store['store_id']; ?>" checked="checked" />
                        <?php echo $store['name']; ?>
                        <?php } ?>
                      </label>
                    </div>
                    <?php } ?>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-download"><span data-toggle="tooltip" title="<?php echo $help_download; ?>"><?php echo $entry_download; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="download" value="" placeholder="<?php echo $entry_download; ?>" id="input-download" class="form-control" />
                  <div id="product-download" class="well well-sm" style="height: 150px; overflow: auto;">
                    <?php foreach ($product_downloads as $product_download) { ?>
                    <div id="product-download<?php echo $product_download['download_id']; ?>"><i class="fa fa-minus-circle"></i> <?php echo $product_download['name']; ?>
                      <input type="hidden" name="product_download[]" value="<?php echo $product_download['download_id']; ?>" />
                    </div>
                    <?php } ?>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-related"><span data-toggle="tooltip" title="<?php echo $help_related; ?>"><?php echo $entry_related; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="related" value="" placeholder="<?php echo $entry_related; ?> Search By WSB Product Code OR Product Name" id="input-related" class="form-control" />
                  <div id="product-related" class="well well-sm" style="height: 150px; overflow: auto;">
                    <?php foreach ($product_relateds as $product_related) { ?>
                    <div id="product-related<?php echo $product_related['product_id']; ?>"><i class="fa fa-minus-circle"></i> <?php echo $product_related['name']; ?>
                      <input type="hidden" name="product_related[]" value="<?php echo $product_related['product_id']; ?>" />
                    </div>
                    <?php } ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="tab-pane" id="tab-attribute">
              <div class="table-responsive">
                <table id="attribute" class="table table-striped table-bordered table-hover">
                  <thead>
                    <tr>
                      <td class="text-left"><?php echo $entry_attribute; ?></td>
                      <td class="text-left"><?php echo $entry_text; ?></td>
                      <td></td>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $attribute_row = 0; ?>
                    <?php foreach ($product_attributes as $product_attribute) { ?>
                    <tr id="attribute-row<?php echo $attribute_row; ?>">
                      <td class="text-left" style="width: 40%;"><input type="text" name="product_attribute[<?php echo $attribute_row; ?>][name]" value="<?php echo $product_attribute['name']; ?>" placeholder="<?php echo $entry_attribute; ?>" class="form-control" />
                        <input type="hidden" name="product_attribute[<?php echo $attribute_row; ?>][attribute_id]" value="<?php echo $product_attribute['attribute_id']; ?>" /></td>
                      <td class="text-left"><?php foreach ($languages as $language) { ?>
                        <div class="input-group"><span class="input-group-addon"><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /></span>
                          <textarea name="product_attribute[<?php echo $attribute_row; ?>][product_attribute_description][<?php echo $language['language_id']; ?>][text]" rows="5" placeholder="<?php echo $entry_text; ?>" class="form-control"><?php echo isset($product_attribute['product_attribute_description'][$language['language_id']]) ? $product_attribute['product_attribute_description'][$language['language_id']]['text'] : ''; ?></textarea>
                        </div>
                        <?php } ?></td>
                      <td class="text-left"><button type="button" onclick="$('#attribute-row<?php echo $attribute_row; ?>').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
                    </tr>
                    <?php $attribute_row++; ?>
                    <?php } ?>
                  </tbody>
                  <tfoot>
                    <tr>
                      <td colspan="2"></td>
                      <td class="text-left"><button type="button" onclick="addAttribute();" data-toggle="tooltip" title="<?php echo $button_attribute_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
            <div class="tab-pane" id="tab-option">
              <div class="row">
                <div class="col-sm-2">
                  <ul class="nav nav-pills nav-stacked" id="option">
                    <?php $option_row = 0; ?>
                    <?php foreach ($product_options as $product_option) { ?>
                    <li><a href="#tab-option<?php echo $option_row; ?>" data-toggle="tab"><i class="fa fa-minus-circle" onclick="$('a[href=\'#tab-option<?php echo $option_row; ?>\']').parent().remove(); $('#tab-option<?php echo $option_row; ?>').remove(); $('#option a:first').tab('show');"></i> <?php echo $product_option['name']; ?></a></li>
                    <?php $option_row++; ?>
                    <?php } ?>
                    <li>
                      <input type="text" name="option" value="" placeholder="<?php echo $entry_option; ?>" id="input-option" class="form-control" />
                    </li>
                  </ul>
                </div>
                <div class="col-sm-10">
                  <div class="tab-content">
                    <?php $option_row = 0; ?>
                    <?php $option_value_row = 0; ?>
                    <?php foreach ($product_options as $product_option) { ?>
                    <div class="tab-pane" id="tab-option<?php echo $option_row; ?>">
                      <input type="hidden" name="product_option[<?php echo $option_row; ?>][product_option_id]" value="<?php echo $product_option['product_option_id']; ?>" />
                      <input type="hidden" name="product_option[<?php echo $option_row; ?>][name]" value="<?php echo $product_option['name']; ?>" />
                      <input type="hidden" name="product_option[<?php echo $option_row; ?>][option_id]" value="<?php echo $product_option['option_id']; ?>" />
                      <input type="hidden" name="product_option[<?php echo $option_row; ?>][type]" value="<?php echo $product_option['type']; ?>" />
                      <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-required<?php echo $option_row; ?>"><?php echo $entry_required; ?></label>
                        <div class="col-sm-10">
                          <select name="product_option[<?php echo $option_row; ?>][required]" id="input-required<?php echo $option_row; ?>" class="form-control">
                            <?php if ($product_option['required']) { ?>
                            <option value="1" selected="selected"><?php echo $text_yes; ?></option>
                            <option value="0"><?php echo $text_no; ?></option>
                            <?php } else { ?>
                            <option value="1"><?php echo $text_yes; ?></option>
                            <option value="0" selected="selected"><?php echo $text_no; ?></option>
                            <?php } ?>
                          </select>
                        </div>
                      </div>
                      <?php if ($product_option['type'] == 'text') { ?>
                      <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-value<?php echo $option_row; ?>"><?php echo $entry_option_value; ?></label>
                        <div class="col-sm-10">
                          <input type="text" name="product_option[<?php echo $option_row; ?>][value]" value="<?php echo $product_option['value']; ?>" placeholder="<?php echo $entry_option_value; ?>" id="input-value<?php echo $option_row; ?>" class="form-control" />
                        </div>
                      </div>
                      <?php } ?>
                      <?php if ($product_option['type'] == 'textarea') { ?>
                      <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-value<?php echo $option_row; ?>"><?php echo $entry_option_value; ?></label>
                        <div class="col-sm-10">
                          <textarea name="product_option[<?php echo $option_row; ?>][value]" rows="5" placeholder="<?php echo $entry_option_value; ?>" id="input-value<?php echo $option_row; ?>" class="form-control"><?php echo $product_option['value']; ?></textarea>
                        </div>
                      </div>
                      <?php } ?>
                      <?php if ($product_option['type'] == 'file') { ?>
                      <div class="form-group" style="display: none;">
                        <label class="col-sm-2 control-label" for="input-value<?php echo $option_row; ?>"><?php echo $entry_option_value; ?></label>
                        <div class="col-sm-10">
                          <input type="text" name="product_option[<?php echo $option_row; ?>][value]" value="<?php echo $product_option['value']; ?>" placeholder="<?php echo $entry_option_value; ?>" id="input-value<?php echo $option_row; ?>" class="form-control" />
                        </div>
                      </div>
                      <?php } ?>
                      <?php if ($product_option['type'] == 'date') { ?>
                      <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-value<?php echo $option_row; ?>"><?php echo $entry_option_value; ?></label>
                        <div class="col-sm-3">
                          <div class="input-group date">
                            <input type="text" name="product_option[<?php echo $option_row; ?>][value]" value="<?php echo $product_option['value']; ?>" placeholder="<?php echo $entry_option_value; ?>" data-date-format="YYYY-MM-DD" id="input-value<?php echo $option_row; ?>" class="form-control" />
                            <span class="input-group-btn">
                            <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
                            </span></div>
                        </div>
                      </div>
                      <?php } ?>
                      <?php if ($product_option['type'] == 'time') { ?>
                      <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-value<?php echo $option_row; ?>"><?php echo $entry_option_value; ?></label>
                        <div class="col-sm-10">
                          <div class="input-group time">
                            <input type="text" name="product_option[<?php echo $option_row; ?>][value]" value="<?php echo $product_option['value']; ?>" placeholder="<?php echo $entry_option_value; ?>" data-date-format="HH:mm" id="input-value<?php echo $option_row; ?>" class="form-control" />
                            <span class="input-group-btn">
                            <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                            </span></div>
                        </div>
                      </div>
                      <?php } ?>
                      <?php if ($product_option['type'] == 'datetime') { ?>
                      <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-value<?php echo $option_row; ?>"><?php echo $entry_option_value; ?></label>
                        <div class="col-sm-10">
                          <div class="input-group datetime">
                            <input type="text" name="product_option[<?php echo $option_row; ?>][value]" value="<?php echo $product_option['value']; ?>" placeholder="<?php echo $entry_option_value; ?>" data-date-format="YYYY-MM-DD HH:mm" id="input-value<?php echo $option_row; ?>" class="form-control" />
                            <span class="input-group-btn">
                            <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                            </span></div>
                        </div>
                      </div>
                      <?php } ?>
                      <?php if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') { ?>
                      <div class="table-responsive">
                        <table id="option-value<?php echo $option_row; ?>" class="table table-striped table-bordered table-hover">
                          <thead>
                            <tr>
                              <td class="text-left"><?php echo $entry_option_value; ?></td>
                              <td class="text-right"><?php echo $entry_quantity; ?></td>
                              <td class="text-left"><?php echo $entry_subtract; ?></td>
                              <td class="text-left"><?php echo "Image"; ?></td>
                              <td class="text-right"><?php echo $entry_price; ?></td>
                              <td class="text-right"><?php echo $entry_option_points; ?></td>
                              <td class="text-right"><?php echo $entry_weight; ?></td>
                              <td class="text-right"><?php echo $entry_option_code; ?></td>
                              <td></td>
                            </tr>
                          </thead>
                          <tbody>
                            <?php foreach ($product_option['product_option_value'] as $product_option_value) { ?>
                            <tr id="option-value-row<?php echo $option_value_row; ?>">
                              <td class="text-left"><select name="product_option[<?php echo $option_row; ?>][product_option_value][<?php echo $option_value_row; ?>][option_value_id]" class="form-control">
                                  <?php if (isset($option_values[$product_option['option_id']])) { ?>
                                  <?php foreach ($option_values[$product_option['option_id']] as $option_value) { ?>
                                  <?php if ($option_value['option_value_id'] == $product_option_value['option_value_id']) { ?>
                                  <option value="<?php echo $option_value['option_value_id']; ?>" selected="selected"><?php echo $option_value['name']; ?></option>
                                  <?php } else { ?>
                                  <option value="<?php echo $option_value['option_value_id']; ?>"><?php echo $option_value['name']; ?></option>
                                  <?php } ?>
                                  <?php } ?>
                                  <?php } ?>
                                </select>
                                <input type="hidden" name="product_option[<?php echo $option_row; ?>][product_option_value][<?php echo $option_value_row; ?>][product_option_value_id]" value="<?php echo $product_option_value['product_option_value_id']; ?>" /></td>
                              <td class="text-right"><input type="text" size="2" name="product_option[<?php echo $option_row; ?>][product_option_value][<?php echo $option_value_row; ?>][quantity]" value="<?php echo $product_option_value['quantity']; ?>" placeholder="<?php echo $entry_quantity; ?>" class="form-control edit_track" data-change="false" data-old-value="<?php echo $product_option_value['quantity']; ?>" /></td>
                              <td class="text-left"><select name="product_option[<?php echo $option_row; ?>][product_option_value][<?php echo $option_value_row; ?>][subtract]" class="form-control">
                                  <?php if ($product_option_value['subtract']) { ?>
                                  <option value="1" selected="selected"><?php echo $text_yes; ?></option>
                                  <option value="0"><?php echo $text_no; ?></option>
                                  <?php } else { ?>
                                  <option value="1"><?php echo $text_yes; ?></option>
                                  <option value="0" selected="selected"><?php echo $text_no; ?></option>
                                  <?php } ?>
                                </select></td>
                                <td class="text-left"><a href="" id="option-image-<?php echo $option_value_row; ?>" data-directory="<?php echo $directory; ?>" data-toggle="image" class="img-thumbnail"><img src="<?php echo $product_option_value['image_thumb']?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /><input type="hidden" name="product_option[<?php echo $option_row; ?>][product_option_value][<?php echo $option_value_row; ?>][option_image]" value="<?php echo $product_option_value['option_image']?>" id="option-img<?php echo $option_value_row; ?>" /></td>
                              <td class="text-right"><select name="product_option[<?php echo $option_row; ?>][product_option_value][<?php echo $option_value_row; ?>][price_prefix]" class="form-control">
                                  <?php if ($product_option_value['price_prefix'] == '+') { ?>
                                  <option value="+" selected="selected">+</option>
                                  <?php } else { ?>
                                  <option value="+">+</option>
                                  <?php } ?>
                                  <?php if ($product_option_value['price_prefix'] == '-') { ?>
                                  <option value="-" selected="selected">-</option>
                                  <?php } else { ?>
                                  <option value="-">-</option>
                                  <?php } ?>
                                </select>
                                <input type="text" size="5" name="product_option[<?php echo $option_row; ?>][product_option_value][<?php echo $option_value_row; ?>][price]" value="<?php echo $product_option_value['price']; ?>" placeholder="<?php echo $entry_price; ?>" class="form-control" /></td>
                              <td class="text-right"><select name="product_option[<?php echo $option_row; ?>][product_option_value][<?php echo $option_value_row; ?>][points_prefix]" class="form-control">
                                  <?php if ($product_option_value['points_prefix'] == '+') { ?>
                                  <option value="+" selected="selected">+</option>
                                  <?php } else { ?>
                                  <option value="+">+</option>
                                  <?php } ?>
                                  <?php if ($product_option_value['points_prefix'] == '-') { ?>
                                  <option value="-" selected="selected">-</option>
                                  <?php } else { ?>
                                  <option value="-">-</option>
                                  <?php } ?>
                                </select>
                                <input type="text" size="5" name="product_option[<?php echo $option_row; ?>][product_option_value][<?php echo $option_value_row; ?>][points]" value="<?php echo $product_option_value['points']; ?>" placeholder="<?php echo $entry_points; ?>" class="form-control" /></td>
                              <td class="text-right"><select name="product_option[<?php echo $option_row; ?>][product_option_value][<?php echo $option_value_row; ?>][weight_prefix]" class="form-control">
                                  <?php if ($product_option_value['weight_prefix'] == '+') { ?>
                                  <option value="+" selected="selected">+</option>
                                  <?php } else { ?>
                                  <option value="+">+</option>
                                  <?php } ?>
                                  <?php if ($product_option_value['weight_prefix'] == '-') { ?>
                                  <option value="-" selected="selected">-</option>
                                  <?php } else { ?>
                                  <option value="-">-</option>
                                  <?php } ?>
                                </select>
                                <input type="text" size="5" name="product_option[<?php echo $option_row; ?>][product_option_value][<?php echo $option_value_row; ?>][weight]" value="<?php echo $product_option_value['weight']; ?>" placeholder="<?php echo $entry_weight; ?>" class="form-control" /></td>
                              <td class="text-right">
                                  <input type="text" size="5" name="product_option[<?php echo $option_row; ?>][product_option_value][<?php echo $option_value_row; ?>][option_code]" value="<?php echo $product_option_value['option_code']; ?>" placeholder="<?php echo 'Option Code'; ?>" class="form-control" />
                                  </td>
                              <td class="text-left"><button type="button" onclick="$(this).tooltip('destroy');$('#option-value-row<?php echo $option_value_row; ?>').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
                            </tr>
                            <?php $option_value_row++; ?>
                            <?php } ?>
                          </tbody>
                          <tfoot>
                            <tr>
                              <td colspan="8"></td>
                              <td class="text-left"><button type="button" onclick="addOptionValue('<?php echo $option_row; ?>');" data-toggle="tooltip" title="<?php echo $button_option_value_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
                            </tr>
                          </tfoot>
                        </table>
                      </div>
                      <select id="option-values<?php echo $option_row; ?>" style="display: none;">
                        <?php if (isset($option_values[$product_option['option_id']])) { ?>
                        <?php foreach ($option_values[$product_option['option_id']] as $option_value) { ?>
                        <option value="<?php echo $option_value['option_value_id']; ?>"><?php echo $option_value['name']; ?></option>
                        <?php } ?>
                        <?php } ?>
                      </select>
                      <?php } ?>
                    </div>
                    <?php $option_row++; ?>
                    <?php } ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="tab-pane" id="tab-recurring">
              <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover">
                  <thead>
                    <tr>
                      <td class="text-left"><?php echo $entry_recurring; ?></td>
                      <td class="text-left"></td>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $recurring_row = 0; ?>
                    <?php foreach ($product_recurrings as $product_recurring) { ?>

                    <tr id="recurring-row<?php echo $recurring_row; ?>">
                      <td class="text-left"><select name="product_recurring[<?php echo $recurring_row; ?>][recurring_id]" class="form-control">
                          <?php foreach ($recurrings as $recurring) { ?>
                          <?php if ($recurring['recurring_id'] == $product_recurring['recurring_id']) { ?>
                          <option value="<?php echo $recurring['recurring_id']; ?>" selected="selected"><?php echo $recurring['name']; ?></option>
                          <?php } else { ?>
                          <option value="<?php echo $recurring['recurring_id']; ?>"><?php echo $recurring['name']; ?></option>
                          <?php } ?>
                          <?php } ?>
                        </select></td>
                      <td class="text-left"><button type="button" onclick="$('#recurring-row<?php echo $recurring_row; ?>').remove()" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
                    </tr>
                    <?php $recurring_row++; ?>
                    <?php } ?>
                  </tbody>
                  <tfoot>
                    <tr>
                      <td colspan="1"></td>
                      <td class="text-left"><button type="button" onclick="addRecurring()" data-toggle="tooltip" title="<?php echo $button_recurring_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
            <div class="tab-pane" id="tab-discount">
              <div class="table-responsive">
                <table id="discount" class="table table-striped table-bordered table-hover">
                  <thead>
                    <tr>
                      <td class="text-right"><?php echo $entry_quantity; ?></td>
                      <td class="text-right"><?php echo $entry_priority; ?></td>
                      <td class="text-right"><?php echo $entry_price; ?></td>
                      <td class="text-left"><?php echo $entry_date_start; ?></td>
                      <td class="text-left"><?php echo $entry_date_end; ?></td>
                      <td></td>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $discount_row = 0; ?>
                    <?php foreach ($product_discounts as $product_discount) { ?>
                    <tr id="discount-row<?php echo $discount_row; ?>">
                        <td class="text-right hidden"><input type="text" name="product_discount[<?php echo $discount_row; ?>][store_id]" value="0" class="form-control" /></td>
                      <td class="text-right"><input type="text" name="product_discount[<?php echo $discount_row; ?>][quantity]" value="<?php echo $product_discount['quantity']; ?>" placeholder="<?php echo $entry_quantity; ?>" class="form-control" /></td>
                      <td class="text-right"><input type="text" name="product_discount[<?php echo $discount_row; ?>][priority]" value="<?php echo $product_discount['priority']; ?>" placeholder="<?php echo $entry_priority; ?>" class="form-control" /></td>
                      <td class="text-right"><input type="text" name="product_discount[<?php echo $discount_row; ?>][price]" value="<?php echo $product_discount['price']; ?>" placeholder="<?php echo $entry_price; ?>" class="form-control" /></td>
                      <td class="text-left" style="width: 20%;"><div class="input-group date">
                          <input type="text" name="product_discount[<?php echo $discount_row; ?>][date_start]" value="<?php echo $product_discount['date_start']; ?>" placeholder="<?php echo $entry_date_start; ?>" data-date-format="YYYY-MM-DD" class="form-control" />
                          <span class="input-group-btn">
                          <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
                          </span></div></td>
                      <td class="text-left" style="width: 20%;"><div class="input-group date">
                          <input type="text" name="product_discount[<?php echo $discount_row; ?>][date_end]" value="<?php echo $product_discount['date_end']; ?>" placeholder="<?php echo $entry_date_end; ?>" data-date-format="YYYY-MM-DD" class="form-control" />
                          <span class="input-group-btn">
                          <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
                          </span></div></td>
                      <td class="text-left"><button type="button" onclick="$('#discount-row<?php echo $discount_row; ?>').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
                    </tr>
                    <?php $discount_row++; ?>
                    <?php } ?>
                  </tbody>
                  <tfoot>
                    <tr>
                      <td colspan="5"></td>
                      <td class="text-left"><button type="button" onclick="addDiscount();" data-toggle="tooltip" title="<?php echo $button_discount_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
            <div class="tab-pane" id="tab-special">
              <div class="table-responsive">
                <table id="special" class="table table-striped table-bordered table-hover">
                  <thead>
                    <tr>
                      <td class="text-right"><?php echo $entry_priority; ?></td>
                      <td class="text-right"><?php echo $entry_price; ?></td>
                      <td class="text-left"><?php echo $entry_date_start; ?></td>
                      <td class="text-left"><?php echo $entry_date_end; ?></td>
                      <td></td>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $special_row = 0; ?>
                    <?php foreach ($product_specials as $product_special) { ?>
                    <tr id="special-row<?php echo $special_row; ?>">
                      <td class="text-right"><input type="text" name="product_special[<?php echo $special_row; ?>][priority]" value="<?php echo $product_special['priority']; ?>" placeholder="<?php echo $entry_quantity; ?>" class="form-control" /></td>
                      <td class="text-right"><input type="text" name="product_special[<?php echo $special_row; ?>][price]" value="<?php echo $product_special['price']; ?>" placeholder="<?php echo $entry_price; ?>" class="form-control" /></td>
                      <td class="text-left" style="width: 20%;"><div class="input-group date">
                          <input type="text" name="product_special[<?php echo $special_row; ?>][date_start]" value="<?php echo $product_special['date_start']; ?>" placeholder="<?php echo $entry_date_start; ?>" data-date-format="YYYY-MM-DD" class="form-control" />
                          <span class="input-group-btn">
                          <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
                          </span></div></td>
                      <td class="text-left" style="width: 20%;"><div class="input-group date">
                          <input type="text" name="product_special[<?php echo $special_row; ?>][date_end]" value="<?php echo $product_special['date_end']; ?>" placeholder="<?php echo $entry_date_end; ?>" data-date-format="YYYY-MM-DD" class="form-control" />
                          <span class="input-group-btn">
                          <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
                          </span></div></td>
                      <td class="text-left"><button type="button" onclick="$('#special-row<?php echo $special_row; ?>').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
                    </tr>
                    <?php $special_row++; ?>
                    <?php } ?>
                  </tbody>
                  <tfoot>
                    <tr>
                      <td colspan="4"></td>
                      <td class="text-left"><button type="button" onclick="addSpecial();" data-toggle="tooltip" title="<?php echo $button_special_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
            <div class="tab-pane" id="tab-image">
              <div class="table-responsive">
                <table id="images" class="table table-striped table-bordered table-hover">
                  <thead>
                    <tr>
                      <td class="text-left"><?php echo $entry_image; ?></td>
                      <td class="text-right"><?php echo $entry_sort_order; ?></td>
                      <td></td>
                    </tr>
                  </thead>
                  <tbody id="multiple_image" data-count-row="<?php echo count($product_images);?>" data-image-directory="<?php echo $directory?>">
                    <?php $image_row = 0; ?>
                    <?php foreach ($product_images as $product_image) { ?>
                    <tr id="image-row<?php echo $image_row; ?>">
                      <td class="text-left">
                        <a href="" id="thumb-image<?php echo $image_row; ?>" data-directory="<?php echo $product_image['directory'];?>"  data-toggle="image" class="img-thumbnail">
                          <img src="<?php echo $product_image['thumb']; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" />
                        </a>
                        <input type="hidden" name="product_image[<?php echo $image_row; ?>][image]" value="<?php echo $product_image['image']; ?>" id="input-image<?php echo $image_row; ?>" />
                        <input type="hidden" name="product_image[<?php echo $image_row; ?>][width]" value="<?php echo $product_image['width']; ?>" id="input-image<?php echo $image_row; ?>-width" />
                        <input type="hidden" name="product_image[<?php echo $image_row; ?>][height]" value="<?php echo $product_image['height']; ?>" id="input-image<?php echo $image_row; ?>-height" />
                      </td>
                      <td class="text-right"><input type="text" name="product_image[<?php echo $image_row; ?>][sort_order]" value="<?php if($image_row == 0){echo "1";}else{echo $product_image['sort_order'];} ?>" placeholder="<?php echo $entry_sort_order; ?>" class="form-control" /></td>
                      <td class="text-left"><button type="button" onclick="$('#image-row<?php echo $image_row; ?>').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
                    </tr>
                    <?php $image_row++; ?>
                    <?php } ?>
                  </tbody>
                  <tfoot>
                    <tr>
                      <td colspan="2"></td>
                      <td class="text-left"><button type="button" onclick="addImage();" data-toggle="tooltip" title="<?php echo $button_image_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
                    </tr>
                  </tfoot>
                </table>
              </div>
              <div class="multiple_image_button">
                <button class="multi_img_button btn btn-primary" type="button"> <?php echo $text_add_multiple_image; ?></button>
              </div>
            </div>
            <div class="tab-pane" id="tab-design">
              <div class="table-responsive">
                <table class="table table-bordered table-hover">
                  <thead>
                    <tr>
                      <td class="text-left"><?php echo $entry_store; ?></td>
                      <td class="text-left"><?php echo $entry_layout; ?></td>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td class="text-left"><?php echo $text_default; ?></td>
                      <td class="text-left"><select name="product_layout[0]" class="form-control">
                          <option value=""></option>
                          <?php foreach ($layouts as $layout) { ?>
                          <?php if (isset($product_layout[0]) && $product_layout[0] == $layout['layout_id']) { ?>
                          <option value="<?php echo $layout['layout_id']; ?>" selected="selected"><?php echo $layout['name']; ?></option>
                          <?php } else { ?>
                          <option value="<?php echo $layout['layout_id']; ?>"><?php echo $layout['name']; ?></option>
                          <?php } ?>
                          <?php } ?>
                        </select></td>
                    </tr>
                    <?php foreach ($stores as $store) { ?>
                    <tr>
                      <td class="text-left"><?php echo $store['name']; ?></td>
                      <td class="text-left"><select name="product_layout[<?php echo $store['store_id']; ?>]" class="form-control">
                          <option value=""></option>
                          <?php foreach ($layouts as $layout) { ?>
                          <?php if (isset($product_layout[$store['store_id']]) && $product_layout[$store['store_id']] == $layout['layout_id']) { ?>
                          <option value="<?php echo $layout['layout_id']; ?>" selected="selected"><?php echo $layout['name']; ?></option>
                          <?php } else { ?>
                          <option value="<?php echo $layout['layout_id']; ?>"><?php echo $layout['name']; ?></option>
                          <?php } ?>
                          <?php } ?>
                        </select></td>
                    </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="tab-pane" id="tab-store_data">
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-store_date"><?php echo $entry_store; ?></label>
                <div class="col-sm-10">
                  <select name="store_id" data-product-id="<?php echo $data['p_id']; ?>" class="form-control store_id">
                        <?php
                        foreach($data['store_data'] as $value){ ?>
                          <option value=" <?php echo $value['store_id']; ?> "><?php echo $value['name']; ?></option>
                        <?php } ?>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-store_date">Store Sales</label>
                <div class="col-sm-10">
                  <select <?php echo $sor_value;?> name="store_sales"
                          data-old-value="<?php echo $store_sales; ?>"
                          data-product-id="<?php echo $data['p_id']; ?>"
                          class="form-control edit_track store_sales"
                          data-change="false" >
                        <?php
                        foreach($data['store_sales_options'] as $option){ ?>
                          <option value="<?php echo $option; ?>"
                                 <?php echo $store_sales == $option ? 'selected' : ''; ?> >
                                  <?php echo $option; ?></option>
                        <?php } ?>
                  </select>
                  <?php
                  if ($sor_value!='') {
                  ?>
                  <input type="hidden" name="store_sales" value="<?php echo $store_sales; ?>" />
                  <?php
                  }
                  ?>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-exclusive"><?php echo 'Exclusive'; ?></label>
                <div class="col-sm-10">
                  <select name="exclusive" data-product-id="<?php echo $data['p_id']; ?>" class="form-control exclusive edit_track" data-change="false" data-old-value="<?php echo $exclusive; ?>">
                        <?php
                        foreach($data['exclusive_data'] as $value){ ?>
                          <option value="<?php echo $value; ?>"
                                  <?php echo $exclusive == $value ? 'selected' : ''; ?> >
                                  <?php echo ucfirst($value) ; ?></option>
                        <?php } ?>
                  </select>
                </div>
              </div>


              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-language"><?php echo $entry_language; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="<?php echo $entry_language; ?>" placeholder="<?php echo $entry_language; ?>" value="<?php if(isset($data['language'])){
                    echo $data['language'];
                    }else{
                      echo '';
                      }  ?>" id="input-language" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-meta_title"><?php echo $entry_meta_title; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="<?php echo $entry_meta_title; ?>" placeholder="<?php echo $entry_meta_title; ?>" value="<?php if(isset($data['meta_title'])){
                    echo $data['meta_title'];
                    }else{
                      echo '';
                      }  ?>" id="input-meta_title" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-meta_keyword"><?php echo $entry_meta_keyword; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="<?php echo $entry_meta_keyword; ?>" placeholder="<?php echo $entry_meta_keyword; ?>" value="<?php if(isset($data['meta_keywords'])){
                    echo $data['meta_keywords'];
                    }else{
                      echo '';
                      }  ?>" id="input-meta_keyword" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-meta_description"><?php echo $entry_meta_description; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="<?php echo $entry_meta_description; ?>" value="<?php if(isset($data['meta_description'] )){
                    echo $data['meta_description'] ;
                    }else{
                      echo '';
                      }  ?>" placeholder="<?php echo $entry_meta_description; ?>" id="input-meta_description" class="form-control" />
                </div>
              </div>
              <?php if ($show_sor_dropdown) {
              ?>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-exclusive"><?php echo 'Is SOR Product'; ?></label>
                <div class="col-sm-10">

                  <select <?php echo $sor_value;?> name="sor_product" id="sor_product" class="form-control exclusive edit_track">
                        <?php
                        foreach($data['sor_data'] as $key => $value){ ?>
                          <option value="<?php echo $key; ?>"
                                  <?php echo $sor_product == $key ? 'selected' : ''; ?> >
                                  <?php echo ucfirst($value) ; ?></option>
                        <?php } ?>
                  </select>
                </div>
              </div>
              <?php
            }
            ?>
            <?php
            if ($sor_value!='') {
            ?>
            <input type="hidden" name="sor_product" value="<?php echo $sor_product; ?>" />
            <?php
            }
            ?>
            </div>

            <?php if ($show_associate_tab == 1) { ?>
              <div class="tab-pane" id="tab-associate_products">
                <?php echo $associate_products; ?>
              </div>
            <?php } ?>
          </div>
          <input type="hidden" id="changes_data" name="changes_data" value="<?php echo $changes_data; ?>" />
        </form>
      </div>
    </div>
  </div>



    <button style="display: none" id="popup_model" type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal">Open Modal</button>
    <!-- Modal -->
    <div id="myModal" class="modal fade" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">

          <div class="modal-header">
            <!--<button type="button" class="close" data-dismiss="modal">&times;</button>-->
            <h4 class="modal-title"><?php echo $entry_select_unit; ?></h4>
          </div>
          <div class="modal-body">
              <p><input id="pro_unit" type="radio" name="pro_unit" checked value="product_unit"><?php echo $entry_product_unit; ?> <span id="pro_unit_text" class="unit_text"></span></p>
              <p><input id="cat_unit" type="radio" name="pro_unit" value="category_unit"><?php echo $entry_category_unit; ?> <span id="cat_unit_text" class="unit_text"></span>
                  <br/><span style="font-size: 9px;">(<?php echo $text_replace_unit; ?>)</span>
              </p>
          </div>
          <div class="modal-footer">
            <button onclick="change_unit()" type="button" class="btn btn-default" data-dismiss="modal"><?php echo $entry_submit; ?></button>
            <!--<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>-->
          </div>
        </div>

      </div>
    </div>




  <script type="text/javascript">

  var summernote = [];<!--
<?php foreach ($languages as $language) { ?>
   summernote[<?php echo $language['language_id']; ?>] = $('#input-description<?php echo $language['language_id']; ?>').summernote({
    height: 300,
    onpaste: function (e) {
        var bufferText = ((e.originalEvent || e).clipboardData || window.clipboardData).getData('Text');
        e.preventDefault();
        document.execCommand('insertText', false, bufferText);
    }
  });
<?php } ?>
//--></script>
  <script type="text/javascript"><!--
// Manufacturer

$('input[name=\'manufacturer\']').autocomplete({
  'source': function(request, response) {
    $.ajax({
      url: 'index.php?route=catalog/manufacturer/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
      dataType: 'json',
      success: function(json) {
        json.unshift({
          manufacturer_id: 0,
          name: '<?php echo $text_none; ?>'
        });

        response($.map(json, function(item) {
          return {
            label: item['name'],
            value: item['manufacturer_id']
          }
        }));
      }
    });
  },
  'select': function(item) {
    $('input[name=\'manufacturer\']').val(item['label']);
    $('input[name=\'manufacturer_id\']').val(item['value']);
  }
});

    //check unit
    //get list current categories_ids
    var categories = [];
    var category = $('input[name="product_category[]"]').each(function(){
        var value = $(this).val()
        categories.push(value);
    });

// Category
    $('input[name=\'category\']').on('keyup', function() {
      var check_length = $(this).val().trim().length;
      if(check_length!= '' && check_length % 3 == 0){
        $('input[name=\'category\']').autocomplete({
          'source': function(request, response) {
            $.ajax({
              url: 'index.php?route=catalog/category/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
              dataType: 'json',
              success: function(json) {
                response($.map(json, function(item) {
                  return {
                    label: item['name'],
                    value: item['category_id']
                  }
                }));
              }
            });
          },
          'select': function(item) {
            $('input[name=\'category\']').val('');

            $('#product-category' + item['value']).remove();

            $('#product-category').append('<div id="product-category' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" class="pro_cat" name="product_category[]" value="' + item['value'] + '" /></div>');

            //add category_id if not exit in categories array
            if($.inArray(item['value'], categories)<0){
                categories.push(item['value']);
            }
          }
        });
      }else{
        $('input[name=\'category\']').autocomplete({
          'source': function(){ }
        });
      }


    });

    //get list of category_ids
    <?php
    if( isset($this->request->get['product_id']) ){
        $product_id = $this->request->get['product_id'];
    }else{
        $product_id = '0';
    }
    ?>

    $('input[name=\'category\']').focusout(function(){
        //console.log(categories);

        var product_unit_id = $('#input-unit_id').val();
        //alert(product_unit_id);
        $.ajax({
          url: 'index.php?route=catalog/product/setUnitByCategory&token=<?php echo $token; ?>',
          dataType: 'json',
          type: "POST",
          data: {category : categories, product_id : <?php echo $product_id ?>, product_unit_id : product_unit_id},
          success: function(data) {
                if(data['is_popup'] == 1){
                  $('#pro_unit').val(data['product_unit_id']);
                  $('#pro_unit_text').text(data['product_unit_text']);
                  $('#cat_unit').val(data['category_unit_id']);
                  $('#cat_unit_text').text(data['category_unit_text']);
                  $('#popup_model').click();
                }
            }
        });
    });

    $('#product-category').delegate('.fa-minus-circle', 'click', function() {
        //remove category_id in categories array
        var cat_value = $(this).next('input[name="product_category[]"]').val();
        categories = jQuery.grep(categories, function( n, i ) {
          return ( n !== cat_value );
        });
        //console.log(categories);

        var product_unit_id = $('#input-unit_id').val();
        //alert(product_unit_id);

        $.ajax({
          url: 'index.php?route=catalog/product/setUnitByCategory&token=<?php echo $token; ?>',
          dataType: 'json',
          type: "POST",
          data: {category : categories, product_id : <?php echo $product_id?>, product_unit_id : product_unit_id},
          success: function(data) {
                if(data['is_popup'] == 1){
                  $('#pro_unit').val(data['product_unit_id']);
                  $('#pro_unit_text').text(data['product_unit_text']);
                  $('#cat_unit').val(data['category_unit_id']);
                  $('#cat_unit_text').text(data['category_unit_text']);
                  $('#popup_model').click();
                }
            }
        });
        $(this).parent().remove();
    });

    function change_unit(){
        var unit_id = $("input[name=pro_unit]:checked").val();
        //alert(val);
        $('#input-unit_id').val(unit_id).trigger('change');
    }



// Filter
    $('input[name=\'filter\']').on('keyup', function() {
      var check_length = $(this).val().trim().length;
      if (check_length != '' && check_length % 2 == 0) {
        $('input[name=\'filter\']').autocomplete({
          'source': function(request, response) {
            $.ajax({
              url: 'index.php?route=catalog/filter/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
              dataType: 'json',
              success: function(json) {
                response($.map(json, function(item) {
                  return {
                    label: item['name'],
                    value: item['filter_id']
                  }
                }));
              }
            });
          },
          'select': function(item) {
            $('input[name=\'filter\']').val('');

            $('#product-filter' + item['value']).remove();

            $('#product-filter').append('<div id="product-filter' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="product_filter[]" value="' + item['value'] + '" /></div>');
          }
        });
      }else{
        $('input[name=\'filter\']').autocomplete({
          'source': function(){ }
        });
      }
    });


$('#product-filter').delegate('.fa-minus-circle', 'click', function() {
  $(this).parent().remove();
});

// Downloads
$('input[name=\'download\']').autocomplete({
  'source': function(request, response) {
    $.ajax({
      url: 'index.php?route=catalog/download/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
      dataType: 'json',
      success: function(json) {
        response($.map(json, function(item) {
          return {
            label: item['name'],
            value: item['download_id']
          }
        }));
      }
    });
  },
  'select': function(item) {
    $('input[name=\'download\']').val('');

    $('#product-download' + item['value']).remove();

    $('#product-download').append('<div id="product-download' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="product_download[]" value="' + item['value'] + '" /></div>');
  }
});

$('#product-download').delegate('.fa-minus-circle', 'click', function() {
  $(this).parent().remove();
});


// Product Related
$('input[name=\'related\']').autocomplete({
  'source': function(request, response) {
    $.ajax({
      url: 'index.php?route=catalog/product/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
      dataType: 'json',
      success: function(json) {
        response($.map(json, function(item) {
          return {
            label: item['name'],
            value: item['product_id']
          }
        }));
      }
    });
  },
  'select': function(item) {
    $('input[name=\'related\']').val('');

    $('#product-related' + item['value']).remove();

    $('#product-related').append('<div id="product-related' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="product_related[]" value="' + item['value'] + '" /></div>');
  }
});
$('#product-related').delegate('.fa-minus-circle', 'click', function() {
  $(this).parent().remove();
});
//--></script>
  <script type="text/javascript"><!--
var attribute_row = <?php echo $attribute_row; ?>;

function addAttribute() {
    html  = '<tr id="attribute-row' + attribute_row + '">';
  html += '  <td class="text-left" style="width: 20%;"><input type="text" name="product_attribute[' + attribute_row + '][name]" value="" placeholder="<?php echo $entry_attribute; ?>" class="form-control" /><input type="hidden" name="product_attribute[' + attribute_row + '][attribute_id]" value="" /></td>';
  html += '  <td class="text-left">';
  <?php foreach ($languages as $language) { ?>
  html += '<div class="input-group"><span class="input-group-addon"><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /></span><textarea name="product_attribute[' + attribute_row + '][product_attribute_description][<?php echo $language['language_id']; ?>][text]" rows="5" placeholder="<?php echo $entry_text; ?>" class="form-control"></textarea></div>';
    <?php } ?>
  html += '  </td>';
  html += '  <td class="text-left"><button type="button" onclick="$(\'#attribute-row' + attribute_row + '\').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
    html += '</tr>';

  $('#attribute tbody').append(html);

  attributeautocomplete(attribute_row);

  attribute_row++;
}

function attributeautocomplete(attribute_row) {
  $('input[name=\'product_attribute[' + attribute_row + '][name]\']').autocomplete({
    'source': function(request, response) {
      $.ajax({
        url: 'index.php?route=catalog/attribute/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
        dataType: 'json',
        success: function(json) {
          response($.map(json, function(item) {
            return {
              category: item.attribute_group,
              label: item.name,
              value: item.attribute_id
            }
          }));
        }
      });
    },
    'select': function(item) {
      $('input[name=\'product_attribute[' + attribute_row + '][name]\']').val(item['label']);
      $('input[name=\'product_attribute[' + attribute_row + '][attribute_id]\']').val(item['value']);
    }
  });
}

$('#attribute tbody tr').each(function(index, element) {
  attributeautocomplete(index);
});
//--></script>
  <script type="text/javascript"><!--
var option_row = <?php echo $option_row; ?>;

$('input[name=\'option\']').autocomplete({
  'source': function(request, response) {
    $.ajax({
      url: 'index.php?route=catalog/option/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
      dataType: 'json',
      success: function(json) {
        response($.map(json, function(item) {
          return {
            category: item['category'],
            label: item['name'],
            value: item['option_id'],
            type: item['type'],
            option_value: item['option_value']
          }
        }));
      }
    });
  },
  'select': function(item) {
    html  = '<div class="tab-pane" id="tab-option' + option_row + '">';
    html += ' <input type="hidden" name="product_option[' + option_row + '][product_option_id]" value="" />';
    html += ' <input type="hidden" name="product_option[' + option_row + '][name]" value="' + item['label'] + '" />';
    html += ' <input type="hidden" name="product_option[' + option_row + '][option_id]" value="' + item['value'] + '" />';
    html += ' <input type="hidden" name="product_option[' + option_row + '][type]" value="' + item['type'] + '" />';

    html += ' <div class="form-group">';
    html += '   <label class="col-sm-2 control-label" for="input-required' + option_row + '"><?php echo $entry_required; ?></label>';
    html += '   <div class="col-sm-10"><select name="product_option[' + option_row + '][required]" id="input-required' + option_row + '" class="form-control">';
    html += '       <option value="1"><?php echo $text_yes; ?></option>';
    html += '       <option value="0"><?php echo $text_no; ?></option>';
    html += '   </select></div>';
    html += ' </div>';

    if (item['type'] == 'text') {
      html += ' <div class="form-group">';
      html += '   <label class="col-sm-2 control-label" for="input-value' + option_row + '"><?php echo $entry_option_value; ?></label>';
      html += '   <div class="col-sm-10"><input type="text" name="product_option[' + option_row + '][value]" value="" placeholder="<?php echo $entry_option_value; ?>" id="input-value' + option_row + '" class="form-control" /></div>';
      html += ' </div>';
    }

    if (item['type'] == 'textarea') {
      html += ' <div class="form-group">';
      html += '   <label class="col-sm-2 control-label" for="input-value' + option_row + '"><?php echo $entry_option_value; ?></label>';
      html += '   <div class="col-sm-10"><textarea name="product_option[' + option_row + '][value]" rows="5" placeholder="<?php echo $entry_option_value; ?>" id="input-value' + option_row + '" class="form-control"></textarea></div>';
      html += ' </div>';
    }

    if (item['type'] == 'file') {
      html += ' <div class="form-group" style="display: none;">';
      html += '   <label class="col-sm-2 control-label" for="input-value' + option_row + '"><?php echo $entry_option_value; ?></label>';
      html += '   <div class="col-sm-10"><input type="text" name="product_option[' + option_row + '][value]" value="" placeholder="<?php echo $entry_option_value; ?>" id="input-value' + option_row + '" class="form-control" /></div>';
      html += ' </div>';
    }

    if (item['type'] == 'date') {
      html += ' <div class="form-group">';
      html += '   <label class="col-sm-2 control-label" for="input-value' + option_row + '"><?php echo $entry_option_value; ?></label>';
      html += '   <div class="col-sm-3"><div class="input-group date"><input type="text" name="product_option[' + option_row + '][value]" value="" placeholder="<?php echo $entry_option_value; ?>" data-date-format="YYYY-MM-DD" id="input-value' + option_row + '" class="form-control" /><span class="input-group-btn"><button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button></span></div></div>';
      html += ' </div>';
    }

    if (item['type'] == 'time') {
      html += ' <div class="form-group">';
      html += '   <label class="col-sm-2 control-label" for="input-value' + option_row + '"><?php echo $entry_option_value; ?></label>';
      html += '   <div class="col-sm-10"><div class="input-group time"><input type="text" name="product_option[' + option_row + '][value]" value="" placeholder="<?php echo $entry_option_value; ?>" data-date-format="HH:mm" id="input-value' + option_row + '" class="form-control" /><span class="input-group-btn"><button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button></span></div></div>';
      html += ' </div>';
    }

    if (item['type'] == 'datetime') {
      html += ' <div class="form-group">';
      html += '   <label class="col-sm-2 control-label" for="input-value' + option_row + '"><?php echo $entry_option_value; ?></label>';
      html += '   <div class="col-sm-10"><div class="input-group datetime"><input type="text" name="product_option[' + option_row + '][value]" value="" placeholder="<?php echo $entry_option_value; ?>" data-date-format="YYYY-MM-DD HH:mm" id="input-value' + option_row + '" class="form-control" /><span class="input-group-btn"><button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button></span></div></div>';
      html += ' </div>';
    }

    if (item['type'] == 'select' || item['type'] == 'radio' || item['type'] == 'checkbox' || item['type'] == 'image') {
      html += '<div class="table-responsive">';
      html += '  <table id="option-value' + option_row + '" class="table table-striped table-bordered table-hover">';
      html += '    <thead>';
      html += '      <tr>';
      html += '        <td class="text-left"><?php echo $entry_option_value; ?></td>';
      html += '        <td class="text-right"><?php echo $entry_quantity; ?></td>';
      html += '        <td class="text-left"><?php echo $entry_subtract; ?></td>';
			html += '        <td class="text-left"><?php echo "Image"; ?></td>';
			html += '        <td class="text-right"><?php echo $entry_price; ?></td>';
			html += '        <td class="text-right"><?php echo $entry_option_points; ?></td>';
			html += '        <td class="text-right"><?php echo $entry_weight; ?></td>';
			html += '        <td class="text-right"><?php echo $entry_option_code; ?></td>';
			html += '        <td></td>';
			html += '      </tr>';
			html += '  	 </thead>';
			html += '  	 <tbody>';
			html += '    </tbody>';
			html += '    <tfoot>';
			html += '      <tr>';
			html += '        <td colspan="8"></td>';
			html += '        <td class="text-left"><button type="button" onclick="addOptionValue(' + option_row + ');" data-toggle="tooltip" title="<?php echo $button_option_value_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>';
			html += '      </tr>';
			html += '    </tfoot>';
			html += '  </table>';
			html += '</div>';

            html += '  <select id="option-values' + option_row + '" style="display: none;">';

            for (i = 0; i < item['option_value'].length; i++) {
        html += '  <option value="' + item['option_value'][i]['option_value_id'] + '">' + item['option_value'][i]['name'] + '</option>';
            }

            html += '  </select>';
      html += '</div>';
    }

    $('#tab-option .tab-content').append(html);

    $('#option > li:last-child').before('<li><a href="#tab-option' + option_row + '" data-toggle="tab"><i class="fa fa-minus-circle" onclick="$(\'a[href=\\\'#tab-option' + option_row + '\\\']\').parent().remove(); $(\'#tab-option' + option_row + '\').remove(); $(\'#option a:first\').tab(\'show\')"></i> ' + item['label'] + '</li>');

    $('#option a[href=\'#tab-option' + option_row + '\']').tab('show');

    $('.date').datetimepicker({
      pickTime: false
    });

    $('.time').datetimepicker({
      pickDate: false
    });

    $('.datetime').datetimepicker({
      pickDate: true,
      pickTime: true
    });

    option_row++;
  }
});
//--></script>
  <script type="text/javascript"><!--
var option_value_row = <?php echo $option_value_row; ?>;

function addOptionValue(option_row) {
  html  = '<tr id="option-value-row' + option_value_row + '">';
  html += '  <td class="text-left"><select name="product_option[' + option_row + '][product_option_value][' + option_value_row + '][option_value_id]" class="form-control">';
  html += $('#option-values' + option_row).html();
  html += '  </select><input type="hidden" name="product_option[' + option_row + '][product_option_value][' + option_value_row + '][product_option_value_id]" value="" /></td>';
  html += '  <td class="text-right"><input type="text" name="product_option[' + option_row + '][product_option_value][' + option_value_row + '][quantity]" value="" placeholder="<?php echo $entry_quantity; ?>" class="form-control" /></td>';
  html += '  <td class="text-left"><select name="product_option[' + option_row + '][product_option_value][' + option_value_row + '][subtract]" class="form-control">';
  html += '    <option value="1"><?php echo $text_yes; ?></option>';
  html += '    <option value="0"><?php echo $text_no; ?></option>';
  html += '  </select></td>';
  html += '  <td class="text-left"><a href="" id="option-image-' + option_row + option_value_row+'" data-directory="<?php echo $directory; ?>" data-toggle="image" class="img-thumbnail"><img src="<?php echo $placeholder; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /><input type="hidden" name="product_option[' + option_row + '][product_option_value][' + option_value_row + '][option_image]" value="" id="option-img' + option_row +option_value_row +'" /></td>';
	html += '  <td class="text-right"><select name="product_option[' + option_row + '][product_option_value][' + option_value_row + '][price_prefix]" class="form-control">';
	html += '    <option value="+">+</option>';
	html += '    <option value="-">-</option>';
	html += '  </select>';
	html += '  <input type="text" name="product_option[' + option_row + '][product_option_value][' + option_value_row + '][price]" value="" placeholder="<?php echo $entry_price; ?>" class="form-control" /></td>';
	html += '  <td class="text-right"><select name="product_option[' + option_row + '][product_option_value][' + option_value_row + '][points_prefix]" class="form-control">';
	html += '    <option value="+">+</option>';
	html += '    <option value="-">-</option>';
	html += '  </select>';
	html += '  <input type="text" name="product_option[' + option_row + '][product_option_value][' + option_value_row + '][points]" value="" placeholder="<?php echo $entry_points; ?>" class="form-control" /></td>';
	html += '  <td class="text-right"><select name="product_option[' + option_row + '][product_option_value][' + option_value_row + '][weight_prefix]" class="form-control">';
	html += '    <option value="+">+</option>';
	html += '    <option value="-">-</option>';
	html += '  </select>';
	html += '  <input type="text" name="product_option[' + option_row + '][product_option_value][' + option_value_row + '][weight]" value="" placeholder="<?php echo $entry_weight; ?>" class="form-control" /></td>';
  html += '  <td class="text-right">';
	html += '  <input type="text" name="product_option[' + option_row + '][product_option_value][' + option_value_row + '][option_code]" value="" placeholder="<?php echo $entry_option_code; ?>" class="form-control" /></td>';
  html += '  <td class="text-left"><button type="button" onclick="$(this).tooltip(\'destroy\');$(\'#option-value-row' + option_value_row + '\').remove();" data-toggle="tooltip" rel="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
	html += '</tr>';

	$('#option-value' + option_row + ' tbody').append(html);
        $('[rel=tooltip]').tooltip();

  option_value_row++;
}
//--></script>
  <script type="text/javascript"><!--
var discount_row = <?php echo $discount_row; ?>;

function addDiscount() {
  html  = '<tr id="discount-row' + discount_row + '">';
    html += '  <td class="text-right"><input type="text" name="product_discount[' + discount_row + '][quantity]" value="" placeholder="<?php echo $entry_quantity; ?>" class="form-control" /></td>';
    html += '  <td class="text-right"><input type="text" name="product_discount[' + discount_row + '][priority]" value="" placeholder="<?php echo $entry_priority; ?>" class="form-control" /></td>';
  html += '  <td class="text-right"><input type="text" name="product_discount[' + discount_row + '][price]" value="" placeholder="<?php echo $entry_price; ?>" class="form-control" /></td>';
  html += '  <td class="text-right hidden"><input type="text" name="product_discount[' + discount_row + '][store_id]" value="0" class="form-control" /></td>';
    html += '  <td class="text-left"><div class="input-group date"><input type="text" name="product_discount[' + discount_row + '][date_start]" value="" placeholder="<?php echo $entry_date_start; ?>" data-date-format="YYYY-MM-DD" class="form-control" /><span class="input-group-btn"><button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button></span></div></td>';
  html += '  <td class="text-left"><div class="input-group date"><input type="text" name="product_discount[' + discount_row + '][date_end]" value="" placeholder="<?php echo $entry_date_end; ?>" data-date-format="YYYY-MM-DD" class="form-control" /><span class="input-group-btn"><button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button></span></div></td>';
  html += '  <td class="text-left"><button type="button" onclick="$(\'#discount-row' + discount_row + '\').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
  html += '</tr>';

  $('#discount tbody').append(html);

  $('.date').datetimepicker({
    pickTime: false
  });

  discount_row++;
}
//--></script>
  <script type="text/javascript"><!--
var special_row = <?php echo $special_row; ?>;

function addSpecial() {
  html  = '<tr id="special-row' + special_row + '">';
    html += '  <td class="text-right"><input type="text" name="product_special[' + special_row + '][priority]" value="" placeholder="<?php echo $entry_priority; ?>" class="form-control" /></td>';
  html += '  <td class="text-right"><input type="text" name="product_special[' + special_row + '][price]" value="" placeholder="<?php echo $entry_price; ?>" class="form-control" /></td>';
    html += '  <td class="text-left" style="width: 20%;"><div class="input-group date"><input type="text" name="product_special[' + special_row + '][date_start]" value="" placeholder="<?php echo $entry_date_start; ?>" data-date-format="YYYY-MM-DD" class="form-control" /><span class="input-group-btn"><button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button></span></div></td>';
  html += '  <td class="text-left" style="width: 20%;"><div class="input-group date"><input type="text" name="product_special[' + special_row + '][date_end]" value="" placeholder="<?php echo $entry_date_end; ?>" data-date-format="YYYY-MM-DD" class="form-control" /><span class="input-group-btn"><button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button></span></div></td>';
  html += '  <td class="text-left"><button type="button" onclick="$(\'#special-row' + special_row + '\').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
  html += '</tr>';

  $('#special tbody').append(html);

  $('.date').datetimepicker({
    pickTime: false
  });

  special_row++;
}
//--></script>
  <script type="text/javascript"><!--
var image_row = <?php echo $image_row; ?>;

function addImage() {
  html  = '<tr id="image-row' + image_row + '">';
  html += '  <td class="text-left"><a href="" id="thumb-image' + image_row + '" data-directory="<?php echo $directory; ?>" data-toggle="image" class="img-thumbnail"><img src="<?php echo $placeholder; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" />' +
        '<input type="hidden" name="product_image[' + image_row + '][image]" value="" id="input-image' + image_row + '" />' +
        '<input type="hidden" name="product_image[' + image_row + '][width]" value="" id="input-image' + image_row + '-width" />' +
        '<input type="hidden" name="product_image[' + image_row + '][height]" value="" id="input-image' + image_row + '-height" />' +
        '</td>';
  html += '  <td class="text-right"><input type="text" name="product_image[' + image_row + '][sort_order]" value="" placeholder="<?php echo $entry_sort_order; ?>" class="form-control" /></td>';
  html += '  <td class="text-left"><button type="button" onclick="$(\'#image-row' + image_row  + '\').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
  html += '</tr>';

  $('#images tbody').append(html);

  image_row++;
}
//--></script>
  <script type="text/javascript"><!--
var recurring_row = <?php echo $recurring_row; ?>;

function addRecurring() {
  recurring_row++;

  html  = '';
  html += '<tr id="recurring-row' + recurring_row + '">';
  html += '  <td class="left">';
  html += '    <select name="product_recurring[' + recurring_row + '][recurring_id]" class="form-control">>';
  <?php foreach ($recurrings as $recurring) { ?>
  html += '      <option value="<?php echo $recurring['recurring_id']; ?>"><?php echo $recurring['name']; ?></option>';
  <?php } ?>
  html += '    </select>';
  html += '  </td>';
  html += '  <td class="left">';
  html += '    <a onclick="$(\'#recurring-row' + recurring_row + '\').remove()" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></a>';
  html += '  </td>';
  html += '</tr>';

  $('#tab-recurring table tbody').append(html);
}
//--></script>
  <script type="text/javascript"><!--
$('.date').datetimepicker({
  pickTime: false
});

$('#input-date-available').datetimepicker({
  pickTime: false,
});

$('#input-exp-dispatch-date').datetimepicker({
  pickTime: false
});

$('.time').datetimepicker({
  pickDate: false
});

$('.datetime').datetimepicker({
  pickDate: true,
  pickTime: true
});
//--></script>
  <script type="text/javascript"><!--
$('#language a:first').tab('show');
$('#option a:first').tab('show');
//--></script></div>
//<script type="text/javascript"><!--
$('.store_id').change(function(event) {
  var store_id = $('.store_id').val();
  var product_id = $(this).attr('data-product-id');
    $.ajax({
      url: 'index.php?route=catalog/product/store_data&token=<?php echo $token; ?>',
      dataType: 'json',
      data: '&store_id='+ store_id+ '&product_id='+ product_id,
      success: function(json) {
           $('#input-language').val(json['language']);
           $('#input-meta_title').val(json['meta_title']);
           $('#input-meta_keyword').val(json['meta_keywords']);
           $('#input-meta_description').val(json['meta_description']);
      }
    });
});
</script>
<?php echo $footer; ?>

<script type="text/javascript">
  // multiple images add in products.
  $('.multi_img_button').click(function(){
    $.ajax({
      url: 'index.php?route=common/filemanager&token=<?php echo $token; ?>&data_directory=' + $('#multiple_image').attr('data-image-directory'),
      dataType: 'html',
      beforeSend: function() {
        $('#button-image i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
        $('#button-image').prop('disabled', true);
      },
      complete: function() {
        $('#button-image i').replaceWith('<i class="fa fa-pencil"></i>');
        $('#button-image').prop('disabled', false);
      },
      success: function(html) {
        $('body').append('<div id="modal-image" class="modal">' + html + '</div>');

        $('#modal-image').modal('show');
      }
    });
  });

  $('.edit_track').on('change',function(){
    trackChange(this);
  });

  $('#form-button').on('click',function(){
      edit_track();
      if(document.getElementById('cod_available').checked){
          document.getElementById('cod_available_hidden').disabled = true;
      }
      if(document.getElementById('non_returnable').checked){
          document.getElementById('non_returnable_hidden').disabled = true;
      }
      if(document.getElementById('is_associate') !=  null && document.getElementById('is_associate').checked){
          document.getElementById('is_associate_hidden').disabled = true;
      }
      $('#form-product').submit();

  });

  function trackChange(obj){
    if($(obj).val().trim() != $(obj).attr('data-old-value').trim()){
      $(obj).attr('data-change','true');
    }else{
      $(obj).attr('data-change','false');
    }
  }

  function edit_track(){
    var old_data_format = {};
    summernote.forEach(function(element){
      var textarea = $('#'+$(element)[0].id);
      textarea.val($(element).code());
      if( $(element).code() != '<p><br></p>'){
        trackChange(textarea);
      }
    });

    $('.edit_track').each(function(){
      var data_change = $(this).data('change');

        var old_value = $(this).data('old-value');
        var new_value = $(this).val();
        var name = $(this).attr('name');

        if(name=='expected_dispatch_date'){
            if(old_value != new_value){
                data_change = $(this).attr('data-change');
            }
        }

        if(name=='date_available'){
            if(old_value != new_value){
                data_change = $(this).attr('data-change');
            }
        }

        if(name=='cod_available'){
            if($(this).prop('checked') == true)
                new_value = 1;
            else
                new_value = 0;
            if(old_value != new_value){
                data_change = $(this).attr('data-change');
            }
        }

        if(name=='non_returnable'){
            if($(this).prop('checked') == true)
                new_value = 1;
            else
                new_value = 0;
            if(old_value != new_value){
                data_change = $(this).attr('data-change');
            }
        }

        if(name=='is_associate'){
            if($(this).prop('checked') == true)
                new_value = 1;
            else
                new_value = 0;
            if(old_value != new_value){
                data_change = $(this).attr('data-change');
            }
        }

        if(name=='product_description[1][name]'){
          name = 'title';
        }

        if(name=='product_description[1][set_description]'){
          name = 'set_description';
        }

        if(name=='product_description[1][description]'){
          name = 'description';
        }

        if(typeof old_value === 'string'){
            old_value = old_value.replace(/\r?\n|\r/g,'');
            old_value = old_value.replace(/"/g, '\\"');
        }

        if(typeof new_value === 'string'){
            new_value = new_value.replace(/\r?\n|\r/g,'');
            new_value = new_value.replace(/"/g, '\\"');
        }

      if( data_change ) {
        old_data_format[name] = $.parseJSON('{"old_value":"'+  old_value + '","new_value":"' + new_value + '"}');
      }
    });

    if( $('#changes_data').val().length > 0 ){
        old_data_format = $.parseJSON($('#changes_data').val());
    }

    $('#changes_data').val( JSON.stringify(old_data_format) );
  }


  $('#sor_product_terms').on('click',function(){
    if($(this).prop("checked"))
    {
      $("#sor_product_terms_area").show();
      $("#non_returnable").prop("checked", false);
    }
    else
    {
      $("#sor_product_terms_area").hide();
    }

  });


  $('#non_returnable').on('click',function(){
    if($(this).prop("checked"))
    {
      $("#sor_product_terms_area").hide();
      $("#sor_product_terms").prop("checked", false);
    }
  });
</script>
