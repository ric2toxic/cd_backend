<?php if (!isset($redirect)) { ?>
<!--<div class="row">
    <div class="col-xs-12">
        <!--<divss="pull-left">
            <div class="panel-heading">
                <h4 class="panel-title"><?php echo $text_checkout_confirm; ?></h4>
            </div>
        </div> --
        <!--<div class="pull-right checkout-confirm-order-button">
            <div class="buttons">
                <div class="pull-left">
                    <div class="continue back_collapse back-cash-delivery"><?php echo $button_back; ?></div>
                </div>
            </div>
        </div>
    </div>
</div>-->
<!-- // count piece of product -->
<?php
       $total_piece = 0;
       foreach ($products as $product){
        $total_piece += $product['piece_in_set'];
       }
    ?>
    <div class="row">
        <div id="total_pay">
            <table class="table">
                <tr>
                    <td class="text-left"><strong class="text-left"><?php echo count($products);?> Sets = <?php echo $total_piece; ?> Pieces</strong></td>
                    <td class="text-right">
                        <?php foreach ($totals as $total) { ?>
                        <?php if ($total['code'] != 'tax') { ?>
                        <strong><?php //echo $total['title']; ?></strong>
                        <?php if ($total['code'] == 'total') { ?>
                        <strong class="text-right">Total : <?php echo $total['text']; ?></strong>
                        <?php } else { ?>
                        <?php //echo $total['text']; ?>
                        <?php } ?>
                        <?php } ?>
                        <?php } ?>
                    </td>
                </tr>
            </table>
        </div>
        <div id="content">
            <?php foreach ($products as $product) { ?>
            <div class="cart_table_container">
                <table class="cart_table" class="table-responsive" >
                    <tbody>
                    <?php
                        //$outofstock_css_class = 'instock';
                        //if (!$product['stock']) {
                         // $outofstock_css_class = "outofstock";
                         // $outofstock_flag = 1;
                        //} ?>
                    <tr>
                        <td>
                            <table class="table <?php //echo $outofstock_css_class;?>">
                                <tr>
                                    <td class="product_image">
                                        <?php if ($product['thumb']) { ?>
                                        <a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" style="float: left;" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-thumbnail" /></a>
                                        <?php } ?>
                                    </td>

                                    <td>
                                        <a href="<?php echo $product['href']; ?>" style="float: left;"><?php echo $product['name']; ?></a>
                                        <br/>
                                        <span style="font-size:10px;"><?php echo $product['model']; ?><?php //echo $text_product_code . $product['model']; ?></span>
                                        <br/>
                                        <span class="product_set_description"><?php echo $product['set_description'];?></span>
                                        <br/>
                                        <?php foreach ($product['option'] as $option) { ?>
                                        <br />
                                        &nbsp;<small> - <?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
                                        <?php } ?>
                                        <?php if($product['recurring']) { ?>
                                        <br />
                                        <span class="label label-info"><?php echo $text_recurring_item; ?></span> <small><?php echo $product['recurring']; ?></small>
                                        <?php } ?>
                                        <span class="product_price"><?php echo $product['price_per_piece'];?><?php echo $column_per_piece;?></span>
                                        <span class="fa fa-plus cart_more_detail" data-id="<?php echo $product['product_id'];?>"> More</span>
                                        <!--<span><a href="#cart_details" class="cart_more_detail"><i class="fa fa-plus"></i> More</a></span>-->
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr class="cart_details<?php echo $product['product_id'];?> cart_details_show">
                        <td>
                            <table class="table table-bordered">
                                <tbody>
                                <tr>
                                    <td  class="text-left"><?php echo $text_number_of_pieces; ?></td>
                                    <td class="text-right"><?php echo $product['piece_in_set']; ?></td>
                                </tr>
                                <tr>
                                    <td  class="text-left"><?php echo $text_price_per_piece;  ?></td>
                                    <td class="text-right" ><?php echo $product['price_per_piece']; ?></td>
                                </tr>
                                <tr>
                                    <td  class="text-left"><?php echo $text_price_per_set;  ?></td>
                                    <td class="text-right" ><?php echo $product['price']; ?></td>
                                </tr>
                                <tr>
                                    <td  class="text-left"><?php echo $text_total;  ?></td>
                                    <td class="text-right" ><?php echo $product['total']; ?></td>
                                </tr>
                                <tr>
                                    <td  class="text-left"><?php echo $text_tax;  ?></td>
                                    <td class="text-right" ><?php echo $product['tax']; ?></td>
                                </tr>
                                <?php if($product['set_description'] == ""){ ?>

                                <?php } else { ?>
                                <tr>
                                    <td  class="text-left"><?php echo $text_set_description;  ?></td>
                                    <td class="text-right" ><?php echo $product['set_description']; ?></td>
                                </tr>
                                <?php } ?>
                                </tr>
                                </tbody>
                            </table>
                        </td>

                    </tr>
                    </tbody>
                </table>
            </div>
            <?php } ?>
            <?php foreach ($vouchers as $vouchers) { ?>
            <table class="cart_table" class="table-responsive" style="width: 100%; margin-top: 10px;">
                <tbody>
                <tr>
                    <td class="text-left"><?php echo $voucher['description']; ?></td>
                    <td class="text-left"></td>
                    <td class="text-right">1</td>
                    <td class="text-right"><?php echo $voucher['amount']; ?></td>
                    <td class="text-right"><?php echo $voucher['amount']; ?></td>
                </tr>
                </tbody>
            </table>
            <?php } ?>
        </div>
    </div>
    <div class="row0">
        <table class="table table-bordered table_box_pad">
            <?php foreach ($totals as $total) { ?>
            <?php /* ?>
            <tr>
                <td colspan="7" class="text-right"><strong><?php echo $total['title']; ?>:</strong></td>
                <td class="text-right"><?php echo $total['text']; ?></td>
            </tr>
            <?php */ ?>

            <?php if ($total['code'] != 'tax') { ?>
            <tr>
                <td colspan="7" class="text-right"><strong><?php echo $total['title']; ?>:</strong></td>
                <?php if ($total['code'] == 'total') { ?>
                <td class="text-right" style="font-size:14px;"><strong><?php echo $total['text']; ?></strong></td>
                <?php } else { ?>
                <td class="text-right"><?php echo $total['text']; ?></td>
                <?php } ?>
            </tr>
            <?php } else {

                            if(isset($cform_submit) && $cform_submit == 1 ){
                 ?>
            <tr>
                <td colspan="7" class="text-right"><strong><?php echo $text_cst; ?>:</strong></td>
                <td class="text-right"><?php echo $cst; ?></td>
            </tr>
            <tr>
                <td colspan="7" class="text-right"><strong><?php echo $text_tax_refund; ?>:</strong></td>
                <td class="text-right"><?php echo $tax_refund; ?></td>
            </tr>
            <?php } else{
                ?>
            <tr>
                <td colspan="7" class="text-right"><strong><?php echo $total['title']; ?>:</strong></td>
                <td class="text-right"><?php echo $total['text']; ?></td>
            </tr>
            <?php
                } ?>
            <?php } ?>
            <?php } ?>
        </table>
    </div>
    <?php echo $payment; ?>
<?php } else { ?>
    <script type="text/javascript"><!--
    location = '<?php echo $redirect; ?>';
    //--></script>
<?php } ?>


<script>
    $(document).ready(function(){
        $('.order-summary-block').hide();
        $('.back-cash-delivery').click(function(){
            $('.account_billing_detail').hide();
            $('.new_shipping_address').hide();
            $('.checkout_payment_address').hide();
            $('.checkout_payment_method').show();
            $("panel-body").scrollTop(0);
            $('.checkout_confirm').hide();
            $('.order-summary-block').show();
            $("html, body").animate({scrollTop: 0});

//            $('.checkout-pages-block').removeClass('col-sm-12');
//            $('.order-summary-block').show();
//            $('.checkout-pages-block').addClass('col-sm-8');

            $('.checkout_step1').hide();
            $('.checkout_step2').show();
            $('.checkout_step3').hide();
            $('.checkout_step4').hide();
        });


        $(document).ready(function(){
            $('.cart_more_detail').click(function(e){
                var product_id = $(this).attr('data-id');
                $('.cart_details'+product_id).toggle("slow");
                $(this).toggleClass('fa-plus fa-minus');
                if($(this).text() == ' Less'){
                    $(this).text(' More');
                } else {
                    $(this).text(' Less');
                }
            });
        });
    });
</script>
