<div class="row tabs hidden edit_gst_number">
    <form action="<?php echo $save; ?>" method="post">
        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>" />
        <input type="hidden" name="suborder_id" value="<?php echo $suborder_id; ?>" />

        <div class="col-lg-6">
            Current GST No. In Order: <label style="vertical-align:top; font-size: 14px;"> <?php echo !empty($order['gst_number']) ? $order['gst_number'] : ''; ?></label>
            
            <input type="text" name="gst_number" id='gst_number' placeholder="GST Number" class="form-control" onkeyup="gst_number_valid();" required="required"/>
            <input type="hidden" name="customer_id" value="<?php echo $order['customer_id']; ?>"/>
            <input type="hidden" name="old_gst_number" value="<?php echo !empty($order['gst_number']) ? $order['gst_number'] : ''; ?>"/>
            <div class="text-danger"><?php echo ""; ?></div>
            <br>
            <div class="submit">
                <button type="subimt" class="btn btn-primary pull-right clearfix" id="gst_submit">
                    <i class="fa fa-save"></i>  Save
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    function gst_number_valid()
    {
      var gst_number = $("#gst_number").val();
       gst_number = gst_number.toUpperCase();
       $("#gst_number").val(gst_number);

    }
    $('#gst_number').on('blur', function() {
    var gst_number = $("#gst_number").val();
    if(gst_number != "" && !(gstin_validatation(gst_number))) {
        $("#gst_submit").prop("disabled", true);
        return false;
    } else {
        $("#gst_submit").prop("disabled", false);
    }
});
</script>