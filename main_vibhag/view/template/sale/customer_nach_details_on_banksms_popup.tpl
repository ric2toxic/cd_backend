<div class="credit-tab-content" id="credit">
  <?php if(!empty($customer_nach_details)){ ?>
  <div class="pull-left">
    <h4><b>All NACH account(s):</b></h4>
  </div>
  <div class="pull-right" style="padding-bottom: 2px;">
   <a href="<?php echo $edit_bank_details;?>" title="Edit NACH Account Details" target="_blank">
    <button type="button" class="btn btn-primary">Edit NACH Accounts</button>
  </a>
  </div>
  <table class="table table-bordered table-hover">
    <tr>
      <th>A/c Name</th>
      <th>A/c No.</th>
      <th>IFSC Code</th>
      <th>UMRN No.</th>
      <th>Status</th>
    </tr>
    <?php foreach($customer_nach_details as $value){ ?>
    <tr>
      <td>
        <?php echo $value['account_name']; ?>
        <input type="text" name="account_name_<?php echo $value['id']; ?>" placeholder="NACH A/c Name" id="account_name_<?php echo $value['id']; ?>" class="form-control"  value="<?php echo $value['account_name']; ?>" style="display: none" />
      </td>
      <td>
        <?php echo $value['account_no']; ?>
        <input type="text" name="account_no_<?php echo $value['id']; ?>" placeholder="NACH A/c Name" id="account_no_<?php echo $value['id']; ?>" class="form-control"  value="<?php echo $value['account_no']; ?>" style="display: none" />
      </td>
      <td>
        <?php echo $value['ifsc_code']; ?>
        <input type="text" name="ifsc_code_<?php echo $value['id']; ?>" placeholder="NACH A/c Name" id="ifsc_code_<?php echo $value['id']; ?>" class="form-control"  value="<?php echo $value['ifsc_code']; ?>" style="display: none" />
      </td>
      <td>
        <?php echo $value['umrn_no']; ?>
        <input type="text" name="umrn_no_<?php echo $value['id']; ?>" placeholder="NACH A/c Name" id="umrn_no_<?php echo $value['id']; ?>" class="form-control"  value="<?php echo $value['umrn_no']; ?>" style="display: none" />
      </td>
      <td>
        <?php if($value['active_status'] == 1 && $value['status'] == 1){ ?>
          Default
        <?php }else if($value['status'] == 1){ ?>
          Available
        <?php }else{ ?>
          Unavailable
        <?php } ?>
      </td>
    </tr>
    <?php } ?>
  </table><br>
  <?php }else{ ?>
  <table class="table table-bordered table-hover">
    <tr>
      <td align="left"><span style="color:RED;">NACH account not found</span></td>
    </tr>
  </table>
  <?php }?>
</div>

