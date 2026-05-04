<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <a href="javascript:void(0);" title="" class="ms-button ms-button-mark" onclick="$('#form-product-to-moderate').attr('action','<?php echo $moderate_approve; ?>').submit();"></a>
                <a href="javascript:void(0);" title="" class="ms-button ms-button-delete" onclick="$('#form-product-to-moderate').attr('action','<?php echo $moderate_reject; ?>').submit();"></a>
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
        <?php if ($success) { ?>
        <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
            </div>

            <div class="panel-body">
                <form action="" method="post" enctype="multipart/form-data" id="form-product-to-moderate">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'product_id\']').prop('checked', this.checked);" /></td>
                            <td class="text-center"><?php echo $column_image; ?></td>
                            <td class="text-left"><?php echo $column_model; ?></td>
                            <td class="text-left"><?php echo $column_price; ?></td>
                            <td class="text-left"><?php echo $column_seller_tax; ?></td>
                            <td class="text-left"><?php echo $column_commission; ?></td>
                            <td class="text-left"><?php echo $column_piece_in_set; ?></td>
                            <td class="text-left"><?php echo $column_set_descripation; ?></td>
                            <td class="text-left"><?php echo $column_weight; ?></td>
                            <td class="text-center">Action</td>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if ($products) { ?>
                        <?php foreach ($products as $product_list) { ?>
                        <tr class="moderated_<?php echo $product_list['product_id'];?>">
                            <td class="text-center"><?php if(in_array($product_list['product_id'], $product_id)) { ?>
                                <input type="checkbox" name="product_id[]" value="<?php echo $product_list['product_id']; ?>" checked="checked" />
                                <?php } else { ?>
                                <input type="checkbox" name="product_id[]" value="<?php echo $product_list['product_id']; ?>" />
                                <?php } ?></td>
                            <td class="text-center"><?php if ($product_list['image']) { ?>
                                <img src="<?php echo $product_list['image']; ?>" alt="<?php echo $product_list['name']; ?>" class="img-thumbnail" />
                                <?php } else { ?>
                                <span class="img-thumbnail list"><i class="fa fa-camera fa-2x"></i></span>
                                <?php } ?>
                            </td>
                            <td class="text-center"><?php echo $product_list['model']; ?></td>
                            <td class="text-center"><?php echo $product_list['price']; ?></td>
                            <td class="text-center"><?php echo $product_list['seller_tax']; ?></td>
                            <td class="text-center"><?php echo $product_list['commission']; ?></td>
                            <td class="text-center"><?php echo $product_list['piece_in_set']; ?></td>
                            <td class="text-center order_list_comment"><?php echo $product_list['set_description']; ?></td>
                            <td class="text-center order_list_comment"><?php echo $product_list['weight']; ?></td>
                            <td class="text-center">
                                <a class='ms-button ms-button-edit' href="<?php echo $product_list['edit'];?>" title="" target="_blank"></a>
                                <a href="javascript:void(0);" html-data="<?php echo $product_list['product_id'];?>" title="" class="ms-button ms-button-mark moderate_tick" id="moderate_tick_<?php echo $product_list['product_id'];?>"></a>
                                <a href="javascript:void(0);" html-data="<?php echo $product_list['product_id'];?>" title="" class="ms-button ms-button-delete moderate_cross" id="moderate_cross_<?php echo $product_list['product_id'];?>"></a>
                            </td>
                        </tr>
                        <?php } ?>
                        <?php } else { ?>
                        <tr>
                            <td class="text-center" colspan="8">Data Not Found.<?php //echo $text_no_results; ?></td>
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
<script type="text/javascript"><!--

    $('.moderate_tick').click(function(){
        var product_id = $(this).attr('html-data');
        $.ajax({
            type:'post',
            url:'index.php?route=inventory/producttomoderate/productToModerateApprove&token=<?php echo $token; ?>',
            data:{product_id},
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
            url:'index.php?route=inventory/producttomoderate/productToModerateReject&token=<?php echo $token; ?>',
            data:{product_id},
            success:function(html){
                $('#moderate_cross_'+product_id).remove();
                $('.moderated_'+product_id).removeClass('product_to_moderate_tick').addClass('product_to_moderate_cross');
            }
        });
    });

    $('input,select').on('keypress',function(e){
        if(e.keyCode == 13){
            $('#button-filter').trigger('click');
        }
    });

//--></script>
<?php echo $footer; ?>