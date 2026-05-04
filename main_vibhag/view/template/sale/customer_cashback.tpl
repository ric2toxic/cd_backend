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

<fieldset class="fieldset">
  <legend>Cashback Given History</legend>
    <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <td class="text-left"><?php echo $column_date_added; ?></td>
        <td class="text-left"><?php echo $column_description; ?></td>
        <td class="text-right"><?php echo $column_amount; ?></td>
        <td class="text-right"><?php echo $column_amount_utilized; ?></td>
        <td class="text-right"><?php echo $column_expired; ?></td>
      </tr>
    </thead>
    <tbody>
      <?php if ($cashbacks) { ?>
      <?php foreach ($cashbacks as $cashback) { ?>
      <tr>
        <td class="text-left"><?php echo $cashback['date_added']; ?></td>
        <td class="text-left"><?php echo $cashback['description']; ?></td>
        <td class="text-right"><?php echo $cashback['amount']; ?></td>
        <td class="text-right"><?php echo $cashback['amount_utilized']; ?></td>
        <td class="text-right">
          <span class="available_text_<?php echo $cashback['csh_bck_id'];?>">
            <?php echo $cashback['expired']; ?>
          </span>
          <?php if($cashback['expired'] !='Expired') { ?>
          <button type="button" id="expired_button_<?php echo $cashback['csh_bck_id'];?>" class="btn btn-danger btn-xs expired_button"  data-csh-bck-id ="<?php echo $cashback['csh_bck_id'];?>">&times;</button>
          <?php } ?>
        </td>
      </tr>
      <?php } ?>
      <?php } else { ?>
      <tr>
        <td class="text-center" colspan="5"><?php echo $text_no_results; ?></td>
      </tr>
      <?php } ?>
    </tbody>
    </table>
</fieldset>


<div class="row">
  <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
  <div class="col-sm-6 text-right"><?php echo $results; ?></div>
</div>

<script type="text/javascript">
  $('.expired_button').click(function(){
      var csh_bck_id = $(this).attr('data-csh-bck-id');
      if(confirm('Do you really want to Expire this Cashback ?')){
        $.ajax({
          type: 'POST',
          url: 'index.php?route=sale/customer/expireCashBack&token=<?php echo $token; ?>',
          data:{csh_bck_id},
          success:function(json){
            $('#expired_button_'+csh_bck_id).remove();
            $('.available_text_'+csh_bck_id).text('Expired');
          }
        });

      }

  });
</script>