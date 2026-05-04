<?php echo $header_seller; ?>
<div class="container">
    <div id="piece_in_set-pop-<?php echo $product_id; ?>" class="inventory_popup">
        <div class="row">
            <div>
                <label class="control-label col-sm-12 display_none error_pis"><?php echo $err_enter_all_field; ?></label>
            </div>
            
            <div class="form-group col-sm-12">
                <label class="control-label col-sm-3"> <?php echo $text_set_type; ?> </label>
                <div class="col-sm-9">
                    <select name="set_type" class="form-control set_type" data-id="<?php echo $product_id; ?>" id="set_type_<?php echo $product_id; ?>">
                        <option value="sizes">
                            <?php echo $text_size_set; ?>
                        </option>
                        <option value="color">
                            <?php echo $text_color_set; ?>
                        </option>
                        <option value="free">
                            <?php echo $text_free_size; ?>
                        </option>
                    </select>
                </div>
            </div>

            <div class="form-group col-sm-12">
                <label class="control-label col-sm-3"> <?php echo $text_pis; ?> </label>
                <div class="col-sm-9">
                    <input type="text" class="form-control pis_text_val" html-data="<?php echo $product_id; ?>" name="pieces_in_Set" id="pis_val_<?php echo $product_id; ?>" value="<?php echo $piece_in_set; ?>" />
                </div>
            </div>
            
            <div class="form-group col-sm-12">
                <label id="insert_set_label-<?php echo $product_id; ?>" class="control-label col-sm-3"><?php echo $text_individual_size; ?></label>
                <div id="insert_set-<?php echo $product_id; ?>" class="col-sm-9">
                    <?php if(isset($piece_in_set) && !empty($piece_in_set)){ 
                            for($count= 0 ; $count< $piece_in_set; $count++ ) { ?>
                                <div class="col-sm-7 form-group"><input class="form-control" id ="eys_<?php echo $product_id; ?>_<?php echo $count; ?>"></input></div>
                    <?php   }  
                        } ?>
                </div>
            </div>
            <div>
                <label class="control-label col-sm-12 error_pis error_size"> <?php echo $text_sizes_like; ?> </label>
            </div>
        </div>
        &nbsp;
        <div class="col-xs-4 pull-right">
            <a><span class="btn btn-primary form-control save_pis" html-data="<?php echo $product_id; ?>" data-sor-id="<?php echo $sor_product_id; ?>" class="ud-alignment"><?php echo $text_submit; ?></span></a>
        </div>
    </div>
</div>

<?php echo $footer_seller; ?>
<script type="text/javascript">
    $(".save_pis").click(function(){
        var pid = $(this).attr('html-data');
        var pis_val = $("input#pis_val_"+pid).val();
        var set_type = $("#set_type_"+pid).val();
        var sor_product_id = $(this).attr('data-sor-id');
        var sets,total_sets ='';
        var temp = 0;
        for(var i = 0; i<pis_val; i++){
            var last_pis = pis_val-1;
            var sets = $("input#eys_"+pid+'_'+i).val();
            if(i == last_pis){
                if(sets != ''){
                    total_sets += sets;
                }else{
                    temp = 1;
                    $('.error_pis').show();
                }
            }else{
                if(sets != ''){
                    total_sets += sets+', ';
                }else{
                    temp =1;
                    $('.error_pis').show();
                }
            }
            if(set_type == 'sizes'){                
                $("input#eys_"+pid+'_'+i).next('span').remove();
                if(sets == ''){
                    temp = 1;
                    $("input#eys_"+pid+'_'+i).after('<span class="text-danger">Please enter the value !</span>');
                } else if(!sets.match(/^[0-9]\d{0,9}(\.\d{1,3})?%?$/)){
                    temp = 1;
                    $("input#eys_"+pid+'_'+i).after('<span class="text-danger">Please enter only numeric sizes, eg: 36, 38 etc; letter sizes (S, M, XL etc) are not allowed.</span>');
                }
            }
            if(set_type == 'color'){
                var eys_color = $("#eys_color_"+pid).val();
                if(!eys_color.match(/^\d+$/) || eys_color == '' ) {
                    temp =1;
                    $('.error_pis').show();   
                }
            }else{
                $('.error_pis').hide();
            }

        }
        if(temp == 0){
            $.ajax({
                url : "index.php?route=seller/manage-inventory/UpdatePieceInSet",
                type: "post",
                dataType: "html",
                data: "pis="+pis_val+"&product_id="+pid+"&sor_product_id="+sor_product_id,
                success: function( data ) {
                    parent.location.reload(true);
                }
            });
            if(set_type == 'sizes'){
                var set_des = '1 Set = Total '+ pis_val + ' pieces; 1 each of '+ set_type + ' = ' + total_sets;
                $.ajax({
                    url : "index.php?route=seller/manage-inventory/RemoveColorFilter",
                    type: "post",
                    dataType: "html",
                    data: "product_id="+pid+"&sor_product_id="+sor_product_id,
                    success: function( data ) {
                        parent.location.reload(true);
                    }
                });
            }else if(set_type == 'color'){
                var set_color_size = $("#eys_color_"+pid).val();
                var set_des = '1 Set = Total '+ pis_val + ' pieces; 1 each of '+ set_type + ' = ' + total_sets + '; Size = '+ set_color_size;
                $.ajax({
                    url : "index.php?route=seller/manage-inventory/UpdateColorSize",
                    type: "post",
                    dataType: "html",
                    data: "set_color_size="+set_color_size+"&product_id="+pid+"&sor_product_id="+sor_product_id,
                    success: function( data ) {
                        parent.location.reload(true);
                    }
                });
            }else{
                var set_des = '1 Set = Total '+ pis_val + ' pieces';
            }
            
            $("#piece_in_set-pop-"+pid).hide();    
            var field = 'set_description';
            $.ajax({
                url : "index.php?route=seller/manage-inventory/UpdatePDField",
                type: "post",
                dataType: "html",
                data: "field_val="+set_des+"&product_id="+pid+"&field="+field+"&sor_product_id="+sor_product_id,
                success: function( data ) {
                    parent.$.fancybox.close();
                }
            });
        }
        $('span#des_text_'+pid).html(set_des);
        $('span#pis_text_'+pid).html(pis_val);
    });
    $('.set_type').change(function(){
        var pid = $(this).attr('data-id');
        var set = $('#pis_val_'+pid).val();
        var set_type = $('#set_type_'+pid).val();
        var html = '';
        if(set_type != 'free'){
            $('.error_size').show();
            $('.set_info').remove();
            $('#insert_set_label-'+pid).show();
            var set_type_name = 'Enter individual '+ set_type;
            $('#insert_set_label-'+pid).html(set_type_name);
                if(set_type == 'color'){
                    html+= '<div class="col-sm-5 set_info pull-right form-group"><label class="control-label">Enter Size</label><input class="form-control" id ="eys_color_'+pid+'"></input></div>';
                    for(var i = 0; i < set; i++){
                        html+= '<div class="col-sm-7 set_info pull-left form-group"><input class="form-control" id ="eys_'+pid+'_'+i+'"></input></div>';
                    }
                }else{
                    for(var i = 0; i < set; i++){
                        html+= '<div class="col-sm-7 set_info form-group"><input class="form-control" id ="eys_'+pid+'_'+i+'"></input></div>';
                    }
                }
        }else{
            $('#insert_set_label-'+pid).hide();
            $('.error_size').hide();
        }
        $('#insert_set-'+pid).html(html);
    });
    $('input[name=\'pieces_in_Set\']').keyup(function(){

            var pid = $(this).attr('html-data');
            var set = $(this).val();
            var set_type = $('#set_type_'+pid).val();
            var html ='';
            if(set_type != 'free'){
                if(set_type == 'color'){
                    html+= '<div class="col-sm-5 set_info pull-right form-group">Enter Size: <input class="form-control" id ="eys_color_'+pid+'"></input></div>';
                    for(var i = 0; i < set; i++){
                        html+= '<div class="col-sm-7 set_info pull-left form-group"><input class="form-control" id ="eys_'+pid+'_'+i+'"></input></div>';
                    }
                }else{
                    for(var i = 0; i < set; i++){
                        html+= '<div class="col-sm-7 set_info form-group"><input class="form-control" id ="eys_'+pid+'_'+i+'"></input></div>';
                    }
                }
            }
            $('#insert_set-'+pid).html(html);

    });
</script>