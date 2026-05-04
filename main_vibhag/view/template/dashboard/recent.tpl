<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title"><i class="fa fa-shopping-cart"></i> <?php echo $heading_title; ?></h3>
  </div>
  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <td class="text-right"><?php echo $column_order_no; ?></td>
          <td><?php echo $column_customer; ?></td>
          <td><?php echo $column_company; ?></td>
          <td>
            <table class="table table-bordered">
              <tr>
                 <tr class="text-center">
                    <td>Suborder No</td>
                    <td>Total</td>
                    <td>Status</td>
                    <td>Action</td>
                </tr>
              </tr>  
            </table>
          </td>
          <td>Order Date</td>
          <td class="text-right"><?php echo $column_total; ?></td>
        </tr>
      </thead>
      <tbody>
        <?php if ($orders) { ?>
        <?php foreach ($orders as $order) { ?>
          <?php
              $order_info = $order['order'];
              $suborder_info = $order['suborder'];
          ?>
          <tr>
          <td class="text-right">
            <?php echo $order_info['order_no']; ?><br>
          </td>
          <td><?php echo $order_info['customer']; ?></td>
          <td>
            <?php echo $order_info['shipping_company']; ?> <br>
              <?php echo $order_info['shipping_city']; ?>
          </td>
          <td class="suborder_table">
            <table class="table table-bordered">
                      <?php if( !empty( $suborder_info) ) { ?>
                      <?php foreach($suborder_info as $key_suborder_id => $suborder_data){ ?>
                        <tr>
                          <td class="text-center"><?php echo $key_suborder_id; ?></td>
                          <td><?php echo $suborder_data['total']; ?></td>
                          <td>
                            <strong> <?php echo $suborder_data['status'];  ?> </strong>
                          </td>
                          <td class="">
                            <a href="<?php echo $suborder_data['view']; ?>" data-toggle="tooltip" title="<?php echo $button_view; ?>" class="btn btn-info btn-xs"><i class="fa fa-eye"></i></a>
                          </td>
                        </tr>
                        <?php } ?>
                        <?php } ?>
                      </table>
          </td>
          <td><?php echo $order_info['date_added']; ?></td>
          <td class="text-right"><?php echo $order_info['total']; ?></td>
        </tr>
        <?php } ?>
        <?php } else { ?>
        <tr>
          <td class="text-center" colspan="6"><?php echo $text_no_results; ?></td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</div>
<style type="text/css">
  .suborder_table table tr td {
    padding: 2px !important; 
  }
</style>
