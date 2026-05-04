<?php if (isset($error_warning)) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
<?php } ?>

<?php if(isset($shipping_label)) { ?>
<table class="table table-bordered">
    <thead>
    <tr>
        <td>Warehouse</td>
        <td>Courier Name</td>
        <td>Tracking No</td>
        <td>Weight</td>
        <td>File Name</td>
    </tr>
    </thead>
    <tbody>
    <?php foreach($shipping_label as $shipping_label){ ?>
    <tr class="shipping_label_table">
        <td><?php echo $shipping_label['warehouse']; ?></td>
        <td><?php echo $shipping_label['courier_name']; ?></td>
        <td><?php echo $shipping_label['tracking_no']; ?></td>
        <td><?php echo $shipping_label['weight']; ?></td>
        <td>
        
        <?php if(!empty($shipping_label['download_link'])) { ?>
            <a href="<?php echo $shipping_label['download_link']; ?>" class="btn btn-primary" data-toggle="tooltip" data-original-title="Download Generated Shipping Label"> <i class="fa fa-download"></i> Download </a>
            &nbsp;&nbsp;
        <?php  }?>

        <?php if(strtolower($shipping_label['courier_name'])!='fedex') { ?>
            <a href="index.php?route=sale/order/shippingLabelPdf&token=<?php echo $token;?>&shipping_label_id=<?php echo $shipping_label['shipping_label_id'];?>" class="btn btn-warning" data-toggle="tooltip" data-original-title="Re-Generate Shipping Label"><i class="fa fa-barcode"></i></a>
         <?php  }?>
            
        </td>
    </tr>
    <?php }  ?>
    </tbody>
</table>
<div class="col-sm-12">
    <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
    <div class="col-sm-6 text-right"><?php echo $results; ?></div>
</div>
<?php } ?>
