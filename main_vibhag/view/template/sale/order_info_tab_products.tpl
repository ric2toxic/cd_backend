<?php if(!empty($text_immediate_pickup)){ ?>
    <div class="alert alert-warning alert-dismissible" role="alert" style="color: #8a6d3b;letter-spacing: 0.5px;">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <strong>Note: </strong>For the highlighted items in blue color, Immediate Delivery done from Store.
    </div><br>
<?php } ?>
<table class="table table-bordered">
  <tbody>
  <?php echo $product_info; ?>
  </tbody>
</table>
