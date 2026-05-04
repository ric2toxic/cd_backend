<table class="table table-bordered">
  <tr>
    <td><?php echo $text_firstname; ?></td>
    <td><?php echo $shipping_firstname; ?></td>
  </tr>
  <tr>
    <td><?php echo $text_lastname; ?></td>
    <td><?php echo $shipping_lastname; ?></td>
  </tr>
  <?php if ($shipping_company) { ?>
  <tr>
    <td><?php echo $text_company; ?></td>
    <td><?php echo $shipping_company; ?></td>
  </tr>
  <?php } ?>
  <tr>
    <td><?php echo $text_address; ?></td>
    <td>
      <?php echo $shipping_address_1; ?> <br>
      <?php echo $shipping_address_2; ?>
    </td>
  </tr>
<?php if(!empty($shipping_alternate_numbers)) {?>
  <tr>
    <td><?php echo $text_alternate_number; ?></td>
    <td>
      <?php echo implode('<br>',$shipping_alternate_numbers);?>
    </td>
  </tr>
   <?php } ?>
  <tr>
    <td><?php echo $text_city; ?></td>
    <td><?php echo $shipping_city; ?></td>
  </tr>
  <?php if ($shipping_postcode) { ?>
  <tr>
    <td><?php echo $text_postcode; ?></td>
    <td><?php echo $shipping_postcode; ?></td>
  </tr>
  <?php } ?>
  <tr>
    <td><?php echo $text_zone; ?></td>
    <td><?php echo $shipping_zone; ?></td>
  </tr>
  <tr>
    <td><?php echo $text_country; ?></td>
    <td><?php echo $shipping_country; ?></td>
  </tr>
  <?php foreach ($shipping_custom_fields as $custom_field) { ?>
  <!-- <tr data-sort="<?php // echo $custom_field['sort_order'] + 1; ?>"> -->
  <tr>
    <td><?php echo $custom_field['name']; ?>:</td>
    <td><?php echo $custom_field['value']; ?></td>
  </tr>
  <?php } ?>
  <?php if ($shipping_method) { ?>
  <tr>
    <td><?php echo $text_shipping_method; ?></td>
    <td><?php echo $shipping_method; ?></td>
  </tr>
  <?php } ?>
</table>

<script type="text/javascript">
  $('#tab-shipping tr[data-sort]').detach().each(function() {
    if ($(this).attr('data-sort') >= 0 && $(this).attr('data-sort') <= $('#tab-shipping tr').length) {
      $('#tab-shipping tr').eq($(this).attr('data-sort')).before(this);
    }

    if ($(this).attr('data-sort') > $('#tab-shipping tr').length) {
      $('#tab-shipping tr:last').after(this);
    }

    if ($(this).attr('data-sort') < -$('#tab-shipping tr').length) {
      $('#tab-shipping tr:first').before(this);
    }
  });
</script>