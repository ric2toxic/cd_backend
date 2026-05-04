<?php echo $header_seller; ?>
<div class="container">
    <div id="piece_in_set-pop-<?php echo $product_id; ?>" class="inventory_popup">
        <div class="row">
            <div>
                <label class="control-label col-sm-12 display_none error_pis error_all_field">Please Enter all the fields</label>
            </div>
            
            <div class="form-group col-sm-12">
                <label class="control-label col-sm-3"><?php echo $text_fabric_length; ?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control fabric_length" html-data="<?php echo $product_id; ?>" name="fabric_length" id="fabric_length_<?php echo $product_id; ?>" value="" placeholder="<?php echo $entry_fabric_length; ?>" />
                </div>
            </div>

            <div class="form-group col-sm-12">
                <label class="control-label col-sm-3"><?php echo $text_fabric_width; ?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control fabric_width" html-data="<?php echo $product_id; ?>" name="fabric_width" id="fabric_width_<?php echo $product_id; ?>" value="" placeholder="<?php echo $entry_fabric_width; ?>" />
                </div>
            </div>
            
            <div class="form-group col-sm-12">
                <label class="control-label col-sm-3"><?php echo $text_weight; ?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control weight" html-data="<?php echo $product_id; ?>" name="weight" id="weight_<?php echo $product_id; ?>" value="" placeholder="<?php echo $entry_weight; ?>" />
                </div>
            </div>

            <div class="form-group col-sm-12">
                <label class="control-label col-sm-3"><?php echo $text_transfer_price; ?></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control price" html-data="<?php echo $product_id; ?>" name="transfer_price" id="price_meter_<?php echo $product_id; ?>" value="" placeholder="<?php echo $entry_transfer_price; ?>" />
                </div>
            </div>

            <div>
                <label class="control-label col-sm-12 display_none error_pis error_numeric_value">Please Enter Numeric Value only.</label>
            </div>
        </div>
        &nbsp;
        <div class="col-xs-4 pull-right">
            <a><span class="btn btn-primary form-control save_pis" data-product-type="<?php echo $product_type; ?>" html-data="<?php echo $product_id; ?>" data-sor-id="<?php echo $sor_product_id; ?>" class="ud-alignment"><?php echo $text_submit; ?></span></a>
        </div>
    </div>
</div>
<?php echo $footer_seller; ?>
<script type="text/javascript">
    $(".save_pis").click(function(){
        var pid             =   $(this).attr('html-data');
        var sor_product_id  =   $(this).attr('data-sor-id');
        var product_type    =   $(this).attr('data-product-type');

        var fabric_length   =   $('#fabric_length_'+pid).val();
        var fabric_width    =   $('#fabric_width_'+pid).val();
        var fabric_weight   =   $('#weight_'+pid).val();
        var price_meter     =   $('#price_meter_'+pid).val();

        if((fabric_length != '') && (fabric_width != '') && (fabric_weight != '') && (price_meter != '')){

            if(fabric_length.match(/^\d+$/) && fabric_width.match(/^\d+$/) && fabric_weight.match(/^\d+$/) && price_meter.match(/^\d+$/) ){
                var set_des = '1 Set = ' + fabric_length + ' meters; Width: ' + fabric_width + ' inches';

                var info    = {fabric_length: fabric_length, fabric_width: fabric_width, fabric_weight: fabric_weight, price_meter: price_meter}; 

                // Update set description
                $.ajax({
                    url : "index.php?route=seller/category_set_description/updateSetDescription",
                    type: "post",
                    dataType: "html",
                    data: {product_id :pid, sor_product_id: sor_product_id, product_type: product_type, info: info},
                    success: function( data ) { 
                        parent.$.fancybox.close();
                    }
                });
                $('span#des_text_'+pid).html(set_des);
            }else{
                $('.error_numeric_value').show();
            }
        }else{
            $('.error_all_field').show();
        }
    });
</script>