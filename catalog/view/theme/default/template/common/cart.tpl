<div id="cart" class="btn-group btn-block desktop_only1">
  <button type="button" 
          id="cart_btn" 
          data-toggle="dropdown" 
          data-loading-text="<?php echo $text_loading; ?>" 
          class="hidden-xs btn btn-inverse btn-block btn-lg dropdown-toggle">
    <i class="fa fa-shopping-cart"></i> 
    <span id="cart-total"><?php echo $text_items; ?></span>
  </button>
</div>

<script type="text/javascript">
  $(document).ready(function() {
    $('#cart > button').click(function(){
      window.location = "<?php echo $cart_url; ?>";
      });
  });
</script>
