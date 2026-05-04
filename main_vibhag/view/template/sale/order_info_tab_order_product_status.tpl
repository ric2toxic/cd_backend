<?php if(!empty($send_out_of_stock_email_url)){ ?>

<div class="clearfix">
        <button type="button"
                class="btn btn-warning pull-right"
                id="send_out_of_stock_email"
                data-toggle="tooltip"
                title=""
                data-original-title="Send Out of Stock Email to Customer">
            <i class="fa fa-envelope"></i>
        </button>
</div>
<br>
<?php } ?>
<form id="out_of_stock_form">
<input type="hidden" name="order_id" value="<?php echo $order_id; ?>" />
<table class="table table-bordered">
  <thead>
      <tr>
        <td><input type="checkbox" onclick="$('.product_checkbox').prop('checked',$(this).prop('checked'))" /></td>
        <td>Sr. No.</td>
        <td style="width:30%">SKU</td>
        <td>Seller Nickname</td>
        <td>Piece In Sets</td>
        <td>Sets</td>
        <td>Stock Status</td>
        <td>Out of stock Marked By</td>
      </tr>
  </thead>
  <tbody>
      <?php $i = 1; ?>
      <?php foreach( $order_products_for_status['products'] as $product ){ ?>
            <tr class="<?php echo !empty($product['stock_status']) ? 'bg-warning':''; ?>">
                <td >
                    <?php if(!empty($product['stock_status'])){ ?>
                        <input type="checkbox"
                               name="order_product_id[]"
                               class="product_checkbox"
                               value="<?php echo $product['order_product_id']; ?>"/>
                    <?php } ?>
                </td>
                <td><?php echo $i; ?></td>
                <td>
                    <b><?php echo $product['model'];  ?></b>
                </td>
                <td><?php echo $order_products_for_status['sellers'][$product['seller_id']]['nickname'];  ?></td>
                <td><?php echo $product['piece_in_set']  ?></td>
                <td> Ordered: <?php echo $product['quantity'];  ?><br>
                     Current Stock: <?php echo $product['current_quantity'];  ?>
                </td>
                <td>
                    <?php echo !empty($product['stock_status']) ? $product['stock_status'] : 'In Stock'  ?>
                </td>
                <td><?php echo $product['updated_by']; ?></td>
            </tr>
            <?php $i++; ?>
      <?php } ?>
  </tbody>
</table>
</form>
<input type="hidden"
       id="out_of_stock_comment"
       value="<?php echo !empty($out_of_stock_item) ? implode(", ",$out_of_stock_item) : ''; ?>">
<script>
    $('#send_out_of_stock_email').click(function(){
        if( $('.product_checkbox:checked').length > 0 ){
            let form_data = new FormData($('#out_of_stock_form')[0]);
            $.ajax({
                url: '<?php echo !empty($send_out_of_stock_email_url) ? $send_out_of_stock_email_url : ''; ?>&<?php echo !empty($token) ? $token : ''; ?>',
                type: 'POST',
                data: form_data,
                dataType: 'json',
                contentType: false, // NEEDED, DON'T OMIT THIS (requires jQuery 1.6+)
                processData: false, // NEEDED, DON'T OMIT THIS
                success: function(data){
                    if(data['status']=='success'){
                        alert('Mail sent successfully.');
                    }
                    else{
                        alert(data['error']);
                    }
                }
            });
        }
        else{
            alert('Select atleast a product for sending mail.');
        }
    });
</script>
