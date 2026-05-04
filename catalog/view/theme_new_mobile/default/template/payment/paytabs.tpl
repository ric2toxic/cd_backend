
        <form action="index.php?route=payment/paytabs/send" method="post" id="payment">
       
          <input type="hidden" name="hide_billing_details" value="true" />
          <div class="buttons">
            <div class="right">
              <input type="submit" value="<?php echo $button_confirm; ?>" class="button" />
            </div>
          </div>
        </form>
        <script type="text/javascript">
            document.getElementById('payment').submit();
        </script>