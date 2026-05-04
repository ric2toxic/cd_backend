<!-- ##################### Seller Replacement Notes Starts ###################  -->
<?php  
if(!empty($replacement_notes)){ 
  ?>
<table style="width:100%;" border="0" >
  <tr>
    <?php  
    foreach($replacement_notes as $rn_id => $replacement_note) { ?>
    <td style="width:11%; text-align:right;">
      <div>
        <span><b><?php echo $replacement_note['replacement_note_no']; ?></b></span>&nbsp
        <a href="<?php echo $replacement_note['replacement_note_dload']; ?>" class="btn btn-sm btn-warning" id="replacement_note_no_<?php echo $replacement_note['replacement_note_no']; ?>">
          <i class="fa fa-download"></i>
        </a>
      </div>
      <span><b>(<?php echo $replacement_note['date_added'];?>)</b></span>
    </td>
    <?php }  ?>
  </tr>
</table><br>
<table class="table table-bordered">
  <thead>
    <tr>
      <td class="text-left">Replacement Note Id</td>
      <td class="text-left">Replacement Note No.</td>
      <td class="text-left">Replacement Note Date</td>
      <td class="text-left"><?php echo $column_sku; ?></td>
      <td class="text-left"><?php echo $column_desc; ?></td>
      <td class="text-left">ReplacementNote Amount</td>
      <td class="text-right"><?php echo $column_piece_in_set; ?></td>
      <td class="text-right"><?php echo $column_ttl_rtn_pcs; ?></td>
      <td class="text-right"><?php echo $column_tax_per_pcs; ?></td>
      <td class="text-right">Transfer Price / Piece</td>
      <td class="text-right"><?php echo $column_transfer_price; ?></td>
      <td class="text-right"><?php echo $column_return_reason; ?></td>
      <td class="text-right"><?php echo $column_return_action; ?></td>
    </tr>
  </thead>
  <tbody>   
    <?php
    if(!empty($replacement_notes)){
      foreach($replacement_notes as $replacement_note_id => $replacement_note){
        $op_id = $replacement_note['order_product_id'];
    ?>
    <tr>
      <td><?php echo $replacement_note['replacement_note_id']; ?></td>
      <td><b><?php echo $replacement_note['replacement_note_prefix'].$replacement_note['replacement_note_no']; ?></b></td>
      <td>(<?php echo $replacement_note['date_added']; ?>)</td>
      <td class="text-left" style="width:5%">
        <?php echo !empty($products[$op_id]['seller_sku']) ? $products[$op_id]['seller_sku'] : ''; ?>
        <i><?php echo !empty($oop_options[$op_id]['name'])? 
          "<br>".$oop_options[$op_id]['name'].' : '.$oop_options[$op_id]['value'] :
          '' ?> 
        </i>
        <?php
          if($products[$op_id]['is_returnable'] == 0){
            echo '<span style="color: red;"><br>(Non-Returnable Product)</span>';
          }
        ?>
      </td>
      <td class="text-left" style="width:25%;">
        <b><i><?php echo !empty($products[$op_id]['model']) ? $products[$op_id]['model'] : ''; ?></i></b><br>
        <?php echo !empty($products[$op_id]['name']) ? $products[$op_id]['name'] : ''; ?>
      </td>
      <td>
        <?php echo @$replacement_note['replacement_note_amount'];?>
      </td>
      <td class="text-right">
        <?php echo !empty($products[$op_id]['piece_in_set']) ? $products[$op_id]['piece_in_set'] : ''; ?>
      </td>
      <td class="text-right">
        <?php echo !empty($replacement_note['quantity']) ? $replacement_note['quantity'] : ''; ?>
      </td>
      <td class="text-right">
        <?php echo (float)$products[$op_id]['seller_tax_per_piece'] ; ?>
      </td>
      <td class="text-right">
        <?php echo !empty($products[$op_id]['transfer_price_per_piece']) ? $products[$op_id]['transfer_price_per_piece'] : ''; ?>
      </td>
      <td class="text-right">
        <?php echo !empty($products[$op_id]['transfer_price_per_piece']) ? $products[$op_id]['transfer_price_per_piece']*$replacement_note['quantity'] : ''; ?>
      </td>
      <td class="text-right">
        <?php echo !empty($replacement_note['return_reason_name']) ? $replacement_note['return_reason_name'] : ''; ?>
      </td>
      <td class="text-right">
        <?php echo !empty($replacement_note['return_action_name']) ? $replacement_note['return_action_name'] : ''; ?>
      </td>
    </tr>
    <?php
      }
    }
    ?>
  </tbody>
</table>
<?php
  }
?>
<!-- ##################### Seller Replacement Notes Ends ###################  -->
