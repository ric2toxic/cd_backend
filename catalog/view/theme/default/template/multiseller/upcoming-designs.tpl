<?php echo $header; ?>
    <div class="container ud-container">
        <div class="row">
            <div class="ud_top_bar">
                <div class="col-sm-4 ud_header_text"><i class="fa fa-list"></i> Upcoming Designs</div>
                <div class="col-sm-3 pull-right ud_add_text"><a href="<?php echo $ud_form; ?>"><i class="fa fa-plus"></i> Add upcoming Designs</a></div>
            </div>
            <div class="content">
                <table border="1" class="list table table-bordered">
                    <tr>
                        <th class="ud-alignment">S. No.</th>
                        <th class="ud-alignment">Image</th>
                        <th class="ud-alignment">Name</th>
                        <th class="ud-alignment">SKU</th>
                        <th class="ud-alignment">Price</th>
                    </tr>
                        <?php if(isset($sellerdata) && !empty($sellerdata)){
                        $sr_no = (($page_wholesale-1) * $limit) + 1;
                        foreach($sellerdata as $product){ ?>
                    <tr>
                        <td class="ud-alignment"><?php echo $sr_no++; ?></td>
                        <td class="ud-alignment"><img src="<?php echo HTTP_SERVER.'image/'.$product['p.image']; ?>" width="100px" /> </td>
                        <td class="ud-alignment"><?php echo $product['pd.name']; ?></td>
                        <td class="ud-alignment"><?php echo $product['p.sku']; ?></td>
                        <td class="ud-alignment"><span>Wholesale Box Price = Rs. <?php echo $product['p.price']; ?></span>
                            </br>
                            <?php foreach($product['store_info'] as $info){ ?>
                            <?php if($info['store_price'] != '' && $info['store_price'] != null){ ?>
                            <span><?php echo $info['name']; ?> Price = Rs. <?php echo $info['store_price']; ?> </span> </br>
                            <?php } } ?>
                        </td>
                    </tr>
                        <?php } } ?>

                </table>
                <div class="breadcrumb row custom-pagination-class">
                    <div class="col-sm-6 text-left"><?php echo $pagination_wholesale; ?></div>
                    <div class="col-sm-6 text-right"><?php echo $results_wholesale; ?></div>
                </div>
            </div>
        </div>
    </div>
<?php echo $footer; ?>
