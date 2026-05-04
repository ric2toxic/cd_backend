<table class="table table-bordered" border="1">
  <thead>
    <tr>
      <td class="text-left">Date</td>
      <td class="text-left">Status</td>
      <td class="text-left order_list_comment">Comment</td>
      <td class="text-left order_list_comment">Internal Notes</td>
      <td class="text-left">Updated By</td>
      <td class="text-left">Emailed Customer</td>
      <td class="text-left">SMSed Customer</td>
    </tr>
  </thead>
  <tbody>
    <?php if (!empty($histories)) { ?>
    <?php foreach ($histories as $history) { ?>
    <tr>
      <td class="text-left"><?php echo $history['date_added']; ?></td>
      <td class="text-left"><?php echo $history['status']; ?></td>
      <td class="text-left order_list_comment"><?php echo $history['comment']; ?></td>
      <td class="text-left order_list_comment"><?php echo $history['notes']; ?></td>
      <td class="text-left"><?php echo $history['user']; ?></td>
      <td class="text-left"><?php echo $history['notify_email']; ?></td>
      <td class="text-left"><?php echo $history['notify_sms']; ?></td>
    </tr>
    <?php } ?>
    <?php } else { ?>
    <tr>
      <td class="text-center" colspan="4"><?php echo (!empty($text_no_results) ? $text_no_results : "There is no history found"); ?></td>
    </tr>
    <?php } ?>
  </tbody>
</table>
<div class="col-sm-12">
  <div class="col-sm-6 text-left"><?php echo (!empty($pagination) ? $pagination : ""); ?></div>
  <div class="col-sm-6 text-right"><?php echo (!empty($results) ? $results : ""); ?></div>            
</div>