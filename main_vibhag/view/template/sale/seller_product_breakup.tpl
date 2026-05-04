<!DOCTYPE html>
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>">
<head>
<meta charset="UTF-8" />
<title><?php echo $title; ?></title>
<base href="<?php echo $base; ?>" />
<link href="view/javascript/bootstrap/css/bootstrap.css" rel="stylesheet" media="all" />
<script type="text/javascript" src="view/javascript/jquery/jquery-2.1.1.min.js"></script>
</head>
<body>
<div class="container-fluid">
<div class="row">
    <div class="col-lg-12">
    <form action="<?php echo  $dwnld_link; ?>" method="post">
        <input type="hidden" name="html" id="html" />
        <br>
        <button type="submit" name="submit" aria-label="Download Pdf" style="" class="btn pull-right btn-warning" >Download Pdf</button>
    </form>
    </div>
</div>
</div>
<div id="content">
<?php if($show_store_sales_notice){ ?>
<div class="alert alert-warning alert-dismissible" role="alert" style="color: #8a6d3b;letter-spacing: 0.5px;">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <strong>Note: </strong>For the highlighted items in yellow color, NO pickup is required. These are sold from WholesaleBox stores directly.
</div><br>
<?php } ?>
<h1 align="center"><?php echo "#".$suborder_id; ?></h1>
 <?php foreach ($sellers as $seller) { ?>
     <h4 style="width: 97%; margin: auto;"><?php echo $seller_company[$seller]; ?></h4>
     <table border="1" class="table table-bordered" style="width: 97%; margin: auto;">
     <thead>
       <tr>
         <td class="text-left">S.No.</td>
         <td class="text-left"><?php echo $column_sku; ?></td>
         <td class="text-left"><?php echo $column_wsb_product_code; ?></td>
         <td class="text-left"><?php echo $column_set_desc; ?></td>
         <td class="text-right"><?php echo $column_sets; ?></td>
         <td class="text-right"><?php echo $column_total_pieces; ?></td>
       </tr>
     </thead>
     <tbody>
       <?php foreach ($seller_breakup[$seller] as $product) { ?>
       <tr style="<?php echo $product['store_sales'] != 'NO' ? 'background-color:#fff8c3' : ''; ?>">
         <td class="text-left"><?php echo $product['index']; ?>
         <td class="text-left"><?php echo $product['sku']; ?>
         <td class="text-left"><?php echo $product['model']; ?></td>
         <td class="text-left"><?php echo $product['set_description']; ?></td>
         <td class="text-right"><?php echo $product['quantity']; ?></td>
         <td class="text-right"><?php echo $product['total_pieces']; ?></td>
       </tr>
       <?php } ?>
     </tbody>
	</table>
<?php } ?>
</table>
</div>
<script>
    $(document).ready(function(){
        $('#html').val(btoa($('#content').html()));
    });
</script>
</body>
</html>
