<div class="table-responsive">
  <table class="table table-bordered table-striped">
    <tr>
      <td class="text-right info"><b><?php echo $text_cashback_total; ?></b></td>
      <td class="text-right active"><b><?php echo $text_cashback_available; ?></b></td>
      <td class="text-right success"><b><?php echo $text_cashback_utilized; ?></b></td>
      <td class="text-right danger"><b><?php echo $text_cashback_expired; ?></b></td>
    </tr>
    <tr>
      <td class="text-right info"><b><?php echo $cashback_total; ?></b></td>
      <td class="text-right active"><b><?php echo $cashback_available; ?></b></td>
      <td class="text-right success"><b><?php echo $cashback_utilized; ?></b></td>
      <td class="text-right danger"><b><?php echo $cashback_expired; ?></b></td>
    </tr>
  </table>
</div>
<div class="table-responsive">
  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <td class="text-left"><?php echo $column_date_added; ?></td>
        <td class="text-left"><?php echo $column_description; ?></td>
        <td class="text-right"><?php echo $column_amount; ?></td>
      </tr>
    </thead>
    <tbody>
      <?php if ($cashback_usage) { ?>
      <?php foreach ($cashback_usage as $usage) { ?>
      <tr>
        <td class="text-left"><?php echo $usage['date_added']; ?></td>
        <td class="text-left"><?php echo $usage['description']; ?></td>
        <td class="text-right"><?php echo $usage['amount']; ?></td>
      </tr>
      <?php } ?>
      <?php } else { ?>
      <tr>
        <td class="text-center" colspan="5"><?php echo $text_no_results; ?></td>
      </tr>
      <?php } ?>
    </tbody>
  </table>
</div>

<div class="row">
  <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
  <div class="col-sm-6 text-right"><?php echo $results; ?></div>
</div>
