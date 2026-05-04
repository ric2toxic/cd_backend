<table class="table table-bordered">
  <tr>
    <td><?php echo $text_firstname; ?></td>
    <td><?php echo $payment_firstname; ?></td>
  </tr>
  <tr>
    <td><?php echo $text_lastname; ?></td>
    <td><?php echo $payment_lastname; ?></td>
  </tr>
  <?php if ($payment_company) { ?>
  <tr>
    <td><?php echo $text_company; ?></td>
    <td><?php echo $payment_company; ?></td>
  </tr>
  <?php } ?>
  <tr>
    <td><?php echo $text_address; ?></td>
    <td>
      <?php echo $payment_address_1; ?> <br>
      <?php echo $payment_address_2; ?>
    </td>
  </tr>
  <tr>
    <td><?php echo $text_city; ?></td>
    <td><?php echo $payment_city; ?></td>
  </tr>
  <?php if ($payment_postcode) { ?>
  <tr>
    <td><?php echo $text_postcode; ?></td>
    <td><?php echo $payment_postcode; ?></td>
  </tr>
  <?php } ?>
  <tr>
    <td><?php echo $text_zone; ?></td>
    <td><?php echo $payment_zone; ?></td>
  </tr>
  <tr>
    <td><?php echo $text_country; ?></td>
    <td><?php echo $payment_country; ?></td>
  </tr>
  <?php foreach ($payment_custom_fields as $custom_field) { ?>
  <!-- <tr data-sort="<?php // echo $custom_field['sort_order'] + 1; ?>"> -->
  <tr>
    <td><?php echo $custom_field['name']; ?>:</td>
    <td><?php echo $custom_field['value']; ?></td>
  </tr>
  <?php } ?>
  <tr>
    <td>GSTIN / UIN:</td>
    <td><?php echo $gst_number; ?></td>
  </tr>
  <tr>
    <td><?php echo $text_payment_method; ?></td>
    <td>
      <?php echo $payment_method; ?>
      <?php if(isset($neo_growth_status) && isset($neo_growth_message)) { ?>
        <br/>
        <div style="display: block;font-size: 12px;text-align: left;">
          <strong>Status:</strong><?php if($neo_growth_status == 'success') { ?><div style="color: green;"><?php } else { ?><div style="color: red;"><?php } echo $neo_growth_status; ?> <br/> <?php echo $neo_growth_message; ?></div>
        </div>
      <?php } ?>
    </td>
  </tr>
</table>

<script type="text/javascript">
// Sort the custom fields
  $('#tab-payment tr[data-sort]').detach().each(function() {
      if ($(this).attr('data-sort') >= 0 && $(this).attr('data-sort') <= $('#tab-payment tr').length) {
        $('#tab-payment tr').eq($(this).attr('data-sort')).before(this);
      }

      if ($(this).attr('data-sort') > $('#tab-payment tr').length) {
        $('#tab-payment tr:last').after(this);
      }

      if ($(this).attr('data-sort') < -$('#tab-payment tr').length) {
        $('#tab-payment tr:first').before(this);
      }
    });
</script>