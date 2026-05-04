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
                        <th class="ud-alignment">Edit</th>
                        <th class="ud-alignment">Delete</th>
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
                        <td class="ud-alignment"><a href="<?php echo $edit; ?>&product_id=<?php echo $product['product_id']; ?>"> <i class="fa fa-edit" style="font-size:25px;"></i> </a></td>
                        <td class="ud-alignment"><div class="delete_pro" html-data="<?php echo $product['product_id']; ?>"> <i class="fa fa-trash-o" style="font-size:25px;"></i></i></div></td>
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
<script>

    $(".delete_pro").click(function(){
      var delete_pro  = $(this).attr("html-data");

        $.ajax({
            url : "index.php?route=seller/add-upcoming-designs/deleteProduct",
            type: "post",
            dataType: "html",
            data: "pid="+delete_pro,
            success: function( data ) {
                window.location.reload();
            }
        });

    });
</script>
<?php echo $footer; ?>