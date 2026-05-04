<div id="cart" class="btn-group btn-block desktop_only">
  <button type="button" 
          id="cart_btn_mobile" 
          class="hidden-sm hidden-md hidden-lg btn btn-inverse btn-block btn-lg">
    <i class="fa fa-shopping-cart-custom"></i> 
    <span id="cart-total"><?php echo $total_in_cart; ?></span>
  </button>
</div>

<script type="text/javascript">
  $(document).ready(function() {
    $('#cart > button').click(function(){
      window.location = "<?php echo $cart_url; ?>";
      });
  });
</script>
