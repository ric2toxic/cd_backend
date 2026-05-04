<link href="view/javascript/bootstrap/opencart/opencart.css" type="text/css" rel="stylesheet" />
<div style="display: block;">
  <label style="font-size: 16px;">Order No: <?php echo $order_no; ?></label>
</div>

<table class="table table-bordered">
  <thead>
      <tr>
        <th>Merchant Transaction Id</th>
        <th>Lazypay Transaction Id</th>
        <th>Transaction Type</th>
        <th>Transaction Amount</th>
        <th>Date Added</th>
        <th>Status</th>
        <th>Message</th>
      </tr>
  </thead>
  <tbody>
      <?php foreach( $lazypay_transactions as $transaction ){ ?>
            <tr>
                <td><?php echo $transaction['transaction_id']; ?></td>
                <td><?php echo $transaction['lpTxnId']; ?></td>
                <td>
                  <?php 
                    switch($transaction['request_type']) {
                      case 'EligibilityCheck'  :  
                            echo 'Lazypay Eligibility Check'; 
                            break;
                      case 'InitiatePreAuth'  :  
                            echo 'OTP sent'; 
                            break;
                      case 'AuthorizedPreAuth' :  
                            echo 'Verify OTP'; 
                            break;
                      case 'Purchased' :  
                            echo 'Transaction completed'; 
                            break;
                      default : 
                            echo $transaction['request_type'];
                        break;
                    }
                    ?>
                </td>
                <td><?php echo $transaction['transaction_amount']; ?></td>
                <td><?php echo $transaction['date_added']; ?></td>
                <td><?php echo $transaction['status']; ?></td>
                <td><?php echo $transaction['message']; ?></td>
            </tr>
      <?php } ?>
  </tbody>
</table>

