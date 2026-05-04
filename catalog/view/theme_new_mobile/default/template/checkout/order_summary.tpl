  <?php if (!isset($redirect)) { ?>

  <div class="row0">
      <div class="col-sm-4 order-summary-table">
          <table class="table table-bordered">
              <?php foreach ($totals as $total) { ?>
              <?php /* ?>
              <tr>
                  <td colspan="7" class="text-right"><strong><?php echo $total['title']; ?>:</strong></td>
                  <td class="text-right"><?php echo $total['text']; ?></td>
              </tr>
              <?php */ ?>

              <?php if ($total['code'] != 'tax') { ?>
              <tr>
                  <td colspan="7" class="text-right"><strong><?php echo $total['title']; ?>:</strong></td>
                  <?php if ($total['code'] == 'total') { ?>
                  <td class="text-left" style="font-size:14px;"><strong><?php echo $total['text']; ?></strong></td>
                  <?php } else { ?>
                  <td class="text-left"><?php echo $total['text']; ?></td>
                  <?php } ?>
              </tr>
              <?php } else {

                        if($cform_submit == 1 ){
             ?>
              <tr>
                  <td colspan="7" class="text-right"><strong><?php echo $text_cst; ?>:</strong></td>
                  <td class="text-left"><?php echo $cst; ?></td>
              </tr>
              <tr>
                  <td colspan="7" class="text-right"><strong><?php echo $text_tax_refund; ?>:</strong></td>
                  <td class="text-left"><?php echo $tax_refund; ?></td>
              </tr>
              <?php } else{
            ?>
              <tr>
                  <td colspan="7" class="text-right"><strong><?php echo $total['title']; ?>:</strong></td>
                  <td class="text-left"><?php echo $total['text']; ?></td>
              </tr>
              <?php
            } ?>
              <?php } ?>
              <?php } ?>
          </table>
      </div>


  </div>
  <?php } else { ?>
  <script type="text/javascript"><!--
      location = '<?php echo $redirect; ?>';
      //--></script>
  <?php } ?>