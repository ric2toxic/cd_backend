<label ><?php echo $text_important_information; ?></label>
  <div class="portion_important_info">
    <button type="button" class="close_button"><i class="fa fa-close"></i></button>
    <table class="table table-bordered">
      <tr>
        <td colspan="4" class="text-center">
          <h3><?php echo $text_telephone; ?>
          <?php echo $telephone; ?></h3>
        </td>
      </tr>  
      <tr>
        <td><b><?php echo $text_payment_method;?></b></td>
        <td><?php echo $payment_method;?></td>
       </tr> 
      <tr>  
        <td><b><?php echo $text_shipping_method; ?></b></td>
        <td><?php echo $shipping_method; ?></td>
      </tr>
      <!--<tr>
        <td><b><?php //echo $text_courier_advisory; ?></b></td>
        <td>
          <?php //echo $text_weight; ?>
          <?php //echo $text_postcode;?><?php echo $shipping_postcode;?>
        </td>
      </tr>-->
      <tr>
        <td><b><?php echo $text_customer_comment; ?></b></td>
        <td colspan="3"><?php echo $comment; ?></td>
      </tr>
    </table>
    <div class="show_last_history">
      <div class="col-sm-12">
        <label>Last History</label>
      </div>
      <div class="col-sm-12">
        <table class="table table-bordered">
          <tr>
            <td><b><?php echo $column_date_added; ?> : </b> </td>
            <td>
            <?php 
                if(isset($last_update_history['date_added'])) {
                    echo date('d-M-Y', strtotime($last_update_history['date_added']));
                }
            ?>
            </td>
          </tr>
          <tr>
            <td><b><?php echo $column_status; ?> : </b></td>
            <td><?php echo $order_status;?></td>                    
          </tr>
          <tr>
            <td width="25%"><b><?php echo $column_comment; ?> : </b></td>
            <td>
            <?php 
             if(isset($last_update_history['comment'])) {
                echo $last_update_history['comment']; 
              }  
            ?>
            </td>
          </tr>
          <tr>
            <td><b><?php echo $column_notes; ?> : </b></td>
            <td>
            <?php 
                if(isset($last_update_history['notes'])) {
                    echo $last_update_history['notes'];
                }
            ?>
            </td>
          </tr>
        </table>
      </div>
    </div>
  </div>

<script type="text/javascript">
  $('.important_information').click(function(){
    $('.portion_important_info').show();
    $('.portion_courier_advisory').hide();
  });
  $('.close_button').click(function(e){
    e.stopPropagation();
    $(this).parent().hide();
  });
</script>