<?php echo $header; ?>
<div id="content">
    <div class="page-header">
        <div class="container">
            <div class="pull-right">
                <button type="submit" form="form-product" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-success"><i class="fa fa-save"></i></button>
                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-info" ><i class="fa fa-reply"></i></a>
            </div>
            <h1><?php echo $heading_title; ?></h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="container">
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
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-product" class="form-horizontal">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab-general" data-toggle="tab"><?php echo $tab_general; ?></a></li>
                        <li><a href="#tab-category" data-toggle="tab"><span style="color: red; font-size: 14px;">*</span>Category<?php // echo 'Category'; //$tab_links; ?></a></li>
                        <li><a href="#tab-image" data-toggle="tab"><?php echo $tab_image; ?></a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab-general">
                            <div class="tab-content">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="input-model"><span style="color: red; font-size: 16px;">*</span>&nbsp;Product Name</label>
                                    <?php if(!empty($product_description)){ ?>
                                    <?php foreach($product_description as $product_name){ ?>
                                    <div class="col-sm-10">
                                        <input type="text" name="product_name" value="<?php echo $product_name['name'] ?>" placeholder="Product Name" id="input-model" class="form-control" />
                                        <?php if($error_product_name != ''){ ?>
                                        <?php echo $error_product_name; ?>
                                        <?php } ?>
                                    </div>
                                    <?php } }else{ ?>
                                    <div class="col-sm-10">
                                        <input type="text" name="product_name" value="<?php echo $product_name; ?>" placeholder="Product Name" id="input-model" class="form-control" />
                                        <?php if($error_product_name != ''){ ?>
                                        <span style="color: rgb(169,68,69);"><?php echo $error_product_name; ?></span>
                                        <?php } ?>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-image"><?php echo $entry_image; ?></label>
                                <div class="col-sm-10">
                                    <a href="" id="thumb-image" data-toggle="image_seller" class="img-thumbnail"><img src="<?php echo $thumb; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a>
                                    <input type="hidden" name="image" value="<?php echo $image; ?>" id="input-image" aria-describedby="helpBlock"/>
                                    <span class="help-block alert-warning" id="helpBlock"><?php echo $text_compress_image; ?></span>
                                </div>
                            </div>
                            <div class="form-group required">
                                <label class="col-sm-2 control-label" for="input-sku"><span data-toggle="tooltip" title="<?php echo $help_sku; ?>"><?php echo $entry_sku; ?></span></label>
                                <div class="col-sm-10">
                                    <input type="text" name="sku" value="<?php echo $sku; ?>" placeholder="<?php echo $entry_sku; ?>" id="input-sku" class="form-control" />
                                    <?php if ($error_sku) { ?>
                                    <div class="text-danger"><?php echo $error_sku; ?></div>
                                    <?php } ?>
                                </div>

                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label"><?php echo $entry_store; ?></label>
                                <div class="col-sm-10">
                                    <div class="well well-sm" style="height: 150px; overflow: auto;">
                                        <div class="checkbox">
                                            <label>
                                                <?php if (in_array(0, $product_store)) { ?>
                                                <input type="checkbox" name="product_store[]"  value="0" checked="checked" />
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
                                                <input type="checkbox" class="store_check" name="product_store[]" value="<?php echo $store['store_id']; ?>" checked="checked" />
                                                <?php echo $store['name']; ?>
                                                <?php } else { ?>
                                                <input type="checkbox" class="store_check" name="product_store[]" value="<?php echo $store['store_id']; ?>" />
                                                <?php echo $store['name']; ?>
                                                <?php } ?>
                                            </label>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>

                            <fieldset class="well">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="input-price">
                                        <span data-toggle="tooltip" title="<?php echo $help_price; ?>"><?php echo $entry_price; ?></span>
                                    </label>
                                    <div class="col-sm-10">
                                        <input type="text" name="price" value="<?php echo $price; ?>" placeholder="<?php echo $entry_price; ?>" id="input-price" class="form-control" />
                                        <?php if ($error_price) { ?>
                                        <div class="text-danger"><?php echo $error_price; ?></div>
                                        <?php } ?>
                                    </div>
                                </div>



                                <?php foreach ($stores as $store) { ?>

                                <div class="form-group">
                                    <label for="store-<?php echo $store['store_id']; ?>" class="col-sm-2 control-label">
                                        <?php echo $store['name']; ?>&nbspPrice
                                    </label>

                                    <div class="col-sm-10"><input type="text" class="form-control" id="input-price<?php  echo $store['store_id']?>" value="<?php echo $store['store_price']; ?>" name="store_price[<?php echo $store['store_id']; ?>][]"/>
                                        <?php if ($error_store_price) { ?>
                                        <div class="text-danger"><?php echo $error_store_price; ?></div>
                                        <?php } ?>
                                    </div>

                                </div>
                                <?php } ?>

                            </fieldset>

                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-date-available"><?php echo $entry_date_available; ?></label>
                                <div class="col-sm-3">
                                    <div class="input-group date">
                                        <input type="text" name="date_available" value="<?php echo $date_available; ?>" placeholder="<?php echo $entry_date_available; ?>" data-date-format="YYYY-MM-DD" id="input-date-available" class="form-control" />
                    <span class="input-group-btn">
                    <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
                    </span></div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane" id="tab-category">
                            <div class="form-group">
                                <?php if ($error_product_category) { ?>
                                <div class="text-danger" style="text-align:center;"><?php echo $error_product_category; ?></div>
                                <?php } ?>
                                <label class="col-sm-12"><h3><?php echo $entry_category; ?></h3></label>
                                <div class="col-sm-12">
                                    <?php
                if(isset($category) && !empty($category)){
                   $inc = 5; foreach($category as $value){
                    if(isset($seller_category) && !empty($seller_category)){
                      foreach($seller_category as $seller_cat){
                        if($value['category_id'] == $seller_cat['category_id']){
                          $is_checked = "checked='checked'";
                          break;
                        }else{
                          $is_checked = "";
                        }
                      } }else{
                          $is_checked = "";
                        }?>
                                    <div class="col-sm-6 seller_product_border">
                                        <ul class="expectation_list">
                                            <li class="col-sm-12">
                                                <div class="checkbox">
                                                    <input id="checkbox-7-<?php echo $inc; ?>" type="checkbox" value="<?php echo $value['category_id']?>" name="product_category[]" <?php echo $is_checked;?> />
                                                    <label for="checkbox-7-<?php echo $inc; ?>"><span><?php echo $value['name']; ?></span></label>
                                                </div>
                                            </li>
                                            <?php if(isset($value['sub-category']) && !empty($value['sub-category'])){
                       $inc++; foreach($value['sub-category'] as $key){
                          if(isset($seller_category) && !empty($seller_category)){
                            foreach($seller_category as $seller_cat){
                              if($key['category_id'] == $seller_cat['category_id']){
                                $checked = "checked='checked'";
                                break;
                              }else{
                                $checked = "";
                              }
                            }
                          }else{
                              $checked = "";
                          }?>
                                            <li class="col-sm-1"></li>
                                            <li class="col-sm-11">
                                                <div class="checkbox">
                                                    <input id="checkbox-7-<?php echo $inc; ?>" type="checkbox" value="<?php echo $key['category_id']?>" name="product_category[]" <?php echo $checked;?> />
                                                    <label for="checkbox-7-<?php echo $inc; ?>"><span><?php echo $value['name']; ?> > <?php echo $key['name']; ?></span></label>
                                                </div>
                                            </li>
                                            <?php $inc++; } } ?>
                                        </ul>
                                    </div>
                                    <?php }
                }  ?>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="tab-image">
                            <div class="table-responsive">
                                <span class="help-block alert-warning"><?php echo $text_compress_image; ?></span>
                                <table id="images" class="table table-striped table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <td class="text-left"><?php echo $entry_image; ?></td>
                                        <td class="text-right"><?php echo $entry_sort_order; ?></td>
                                        <td></td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $image_row = 0; ?>
                                    <?php foreach ($product_images as $product_image) { ?>
                                    <tr id="image-row<?php echo $image_row; ?>">
                                        <td class="text-left"><a href="" id="thumb-image<?php echo $image_row; ?>" data-toggle="image_seller" class="img-thumbnail"><img src="<?php echo $product_image['thumb']; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a><input type="hidden" name="product_image[<?php echo $image_row; ?>][image]" value="<?php echo $product_image['image']; ?>" id="input-image<?php echo $image_row; ?>" /></td>
                                        <td class="text-right"><input type="text" name="product_image[<?php echo $image_row; ?>][sort_order]" value="<?php echo $product_image['sort_order']; ?>" placeholder="<?php echo $entry_sort_order; ?>" class="form-control" /></td>
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
                        </div>
                        <div class="tab-pane" id="tab-store-data">
                            <div class="row">
                                <div class="col-sm-2">
                                    <ul class="nav nav-pills nav-stacked" id="product_store_info">
                                        <?php $store_row = 0 ?>
                                        <?php foreach ($stores as $store_info_a) { ?>
                                        <li><a href="#tab-store-data<?php echo $store_row; ?>" data-toggle="tab"><i class="fa fa-minus-circle" onclick="$('a[href=\'#tab-store-data<?php echo $store_row; ?>\']').parent().remove(); $('#tab-store-data<?php echo $store_row; ?>').remove(); $('#product_store_info a:first').tab('show');"></i> <?php echo $store_info_a['name']; ?></a></li>
                                        <?php $store_row++; ?>
                                        <?php } ?>
                                    </ul>
                                </div>
                                <div class="col-sm-10">
                                    <div class="tab-content">
                                        <?php $store_row = 0 ?>
                                        <?php foreach ($product_store_info as $store_info) { ?>
                                        <div class="tab-pane" id="tab-store-data<?php echo $store_row; ?>">
                                            <input type="hidden" name="product_store_info[<?php echo $store_row; ?>][store_id]" value="<?php echo $store_info['store_id']; ?>" />
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label" for="input-language"><?php echo $entry_language; ?></label>
                                                <div class="col-sm-10">
                                                    <input type="text" name="product_store_info[<?php echo $store_row; ?>][language]" placeholder="<?php echo $entry_language; ?>" value="<?php if(isset($store_info['language'])){
                            echo $data['language'];
                            }else{
                              echo '';
                              }  ?>" id="input-language" class="form-control" />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label" for="input-meta_title"><?php echo $entry_meta_title; ?></label>
                                                <div class="col-sm-10">
                                                    <input type="text" name="product_store_info[<?php echo $store_row; ?>][meta_title]" placeholder="<?php echo $entry_meta_title; ?>" value="<?php if(isset($store_info['meta_title'])){
                            echo $store_info['meta_title'];
                            }else{
                              echo '';
                              }  ?>" id="input-meta_title" class="form-control" />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label" for="input-meta_keyword"><?php echo $entry_meta_keyword; ?></label>
                                                <div class="col-sm-10">
                                                    <input type="text" name="product_store_info[<?php echo $store_row; ?>][meta_keywords]" placeholder="<?php echo $entry_meta_keyword; ?>" value="<?php if(isset($store_info['meta_keywords'])){
                            echo $store_info['meta_keywords'];
                            }else{
                              echo '';
                              }  ?>" id="input-meta_keyword" class="form-control" />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label" for="input-meta_description"><?php echo $entry_meta_description; ?></label>
                                                <div class="col-sm-10">
                                                    <input type="text" name="product_store_info[<?php echo $store_row; ?>][meta_description]" value="<?php if(isset($store_info['meta_description'] )){
                            echo $store_info['meta_description'] ;
                            }else{
                              echo '';
                              }  ?>" placeholder="<?php echo $entry_meta_description; ?>" id="input-meta_description" class="form-control" />
                                                </div>
                                            </div>
                                        </div>
                                        <?php $store_row++; ?>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                </form>
            </div>
        </div>
    </div>
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

        //--></script>

    <script type="text/javascript"><!--
        var image_row = <?php echo $image_row; ?>;

        function addImage() {
            html  = '<tr id="image-row' + image_row + '">';
            html += '  <td class="text-left"><a href="" id="thumb-image' + image_row + '"data-toggle="image_seller" class="img-thumbnail"><img src="<?php echo $placeholder; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /><input type="hidden" name="product_image[' + image_row + '][image]" value="" id="input-image' + image_row + '" /></td>';
            html += '  <td class="text-right"><input type="text" name="product_image[' + image_row + '][sort_order]" value="" placeholder="<?php echo $entry_sort_order; ?>" class="form-control" /></td>';
            html += '  <td class="text-left"><button type="button" onclick="$(\'#image-row' + image_row  + '\').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
            html += '</tr>';

            $('#images tbody').append(html);

            image_row++;
        }
        //--></script>
    <script type="text/javascript"><!--
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
        //--></script>
    <script type="text/javascript"><!--
        $('#language a:first').tab('show');
        $('#option a:first').tab('show');
        //--></script></div>
<script type="text/javascript"><!--
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

    $(document).delegate('a[data-toggle=\'image_seller\']', 'click', function(e) {
        e.preventDefault();

        $('.popover').popover('hide', function() {
            $('.popover').remove();
        });

        var element = this;

        $(element).popover({
            html: true,
            placement: 'right',
            trigger: 'manual',
            content: function() {
                return '<button type="button" id="button-image" class="btn btn-primary"><i class="fa fa-pencil"></i></button> <button type="button" id="button-clear" class="btn btn-danger"><i class="fa fa-trash-o"></i></button>';
            }
        });

        $(element).popover('show');

        $('#button-image').on('click', function() {
            $('#modal-image').remove();

            $.ajax({
                url: 'index.php?route=common/filemanager&token=' + getURLVar('token') + '&target=' + $(element).parent().find('input').attr('id') + '&thumb=' + $(element).attr('id'),

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

            $(element).popover('hide', function() {
                $('.popover').remove();
            });
        });

        $('#button-clear').on('click', function() {
            $(element).find('img').attr('src', $(element).find('img').attr('data-placeholder'));

            $(element).parent().find('input').attr('value', '');

            $(element).popover('hide', function() {
                $('.popover').remove();
            });
        });
    });


</script>
<?php echo $footer; ?>