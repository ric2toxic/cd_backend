<link href="view/javascript/bootstrap/opencart/opencart.css" type="text/css" rel="stylesheet" />
<div style="display: block;">
  <label style="font-size: 16px;">Order No: <?php echo $order_no; ?></label>
</div>

<table class="table table-bordered">
  <thead>
      <tr>
        <th>Transaction Id</th>
        <th>Transaction Type</th>
        <th>Transaction Amount</th>
        <th>Date Added</th>
        <th>Date Modified</th>
        <th>Status</th>
        <th>Message</th>
      </tr>
  </thead>
  <tbody>
      <?php foreach( $neo_growth_transactions as $transaction ){ ?>
            <tr>
                <td><?php echo $transaction['transaction_id']; ?></td>
                <td><?php echo $transaction['request_type']; ?></td>
                <td><?php echo $transaction['transaction_amount']; ?></td>
                <td><?php echo $transaction['date_added']; ?></td>
                <td><?php echo $transaction['date_modified']; ?></td>
                <td><?php echo $transaction['status']; ?></td>
                <td><?php echo $transaction['message']; ?></td>
            </tr>
      <?php } ?>
  </tbody>
</table>

