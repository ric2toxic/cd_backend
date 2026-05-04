<?php echo $header; ?>
<?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <h1>
                <?php echo $heading_title; ?>
            </h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li>
                    <a href="<?php echo $breadcrumb['href']; ?>">
                        <?php echo $breadcrumb['text']; ?>
                    </a>
                </li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="container-fluid">
        <?php if ($error_warning) { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i>
            <?php echo $error_warning; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <?php if ($success) { ?>
        <div class="alert alert-success"><i class="fa fa-check-circle"></i>
            <?php echo $success; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
      </div>

        <div class="panel panel-default">
            <div class="panel-body">
                <ul class="nav nav-tabs">
                    <li class=" <?php echo ( $tab_order_pickup ) ? 'active' : '';  ?> ">
                        <a href="<?php echo $order_pickup;?>" > 
                            <h3> <?php echo $text_tab_order_pickup; ?> </h3>
                        </a>
                    </li>
                    <li class="<?php echo ( $tab_order_transit ) ? 'active' : '';  ?>">
                        <a href="<?php echo $order_transit; ?>">
                            <h3> <?php echo $text_tab_order_transit; ?> </h3>
                        </a>
                    </li>
                    <li class="<?php echo ( $tab_order_tantative ) ? 'active' : '';  ?>">
                        <a href="<?php echo $order_tantative; ?>" > 
                            <h3> <?php echo $text_tab_order_tantivie; ?> </h3>
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <td class="text-right">
                                        <?php if ($sort == 'o.order_no') { ?>
                                        <a href="<?php echo $sort_order; ?>" class="<?php echo strtolower($order); ?>">
                                            <?php echo $column_order_no; ?>
                                        </a>
                                        <?php } else { ?>
                                        <a href="<?php echo $sort_order; ?>">
                                            <?php echo $column_order_no; ?>
                                        </a>
                                        <?php } ?>
                                    </td>
                                    <td class="text-left">
                                        <?php if ($sort == 'customer') { ?>
                                        <a href="<?php echo $sort_customer; ?>" class="<?php echo strtolower($order); ?>">
                                            <?php echo $column_customer; ?>
                                        </a>
                                        <?php } else { ?>
                                        <a href="<?php echo $sort_customer; ?>">
                                            <?php echo $column_customer; ?>
                                        </a>
                                        <?php } ?>
                                    </td>
                                    <td class="text-left">
                                        <?php if ($sort == 'o.shipping_company') { ?>
                                        <a href="<?php echo $sort_company; ?>" class="<?php echo strtolower($order); ?>">
                                            <?php echo $column_company; ?>
                                        </a>
                                        <?php } else { ?>
                                        <a href="<?php echo $sort_company; ?>">
                                            <?php echo $column_company; ?>
                                        </a>
                                        <?php } ?>
                                    </td>
                                    <td class="text-left">
                                        <?php if ($sort == 'o.shipping_city') { ?>
                                        <a href="<?php echo $sort_city; ?>" class="<?php echo strtolower($order); ?>">
                                            <?php echo $column_city; ?>
                                        </a>
                                        <?php } else { ?>
                                        <a href="<?php echo $sort_city; ?>">
                                            <?php echo $column_city; ?>
                                        </a>
                                        <?php } ?>
                                    </td>
                                    <td class="text-left">
                                        <table class="table table-bordered">
                                            <tr>
                                                <td><?php echo $column_suborder_no; ?></td>
                                                <td><?php echo $column_total; ?></td>
                                                <td><?php echo $column_status; ?></td>
                                                <td><?php echo $column_action; ?></td>
                                                <td><?php echo $column_shipping_method; ?></td>
                                            </tr>
                                        </table>

                                    </td>
                                    <td class="text-left">
                                        <?php if ($sort == 'o.date_added') { ?>
                                        <a href="<?php echo $sort_date_added; ?>" class="<?php echo strtolower($order); ?>">
                                            Order Date
                                        </a>
                                        <?php } else { ?>
                                        <a href="<?php echo $sort_date_added; ?>">
                                            Order Date
                                        </a>
                                        <?php } ?>
                                    </td>
                                    <td class="text-cetner">
                                        Payment Action
                                    </td>
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
                                    <td class="text-right" width="10%">
                                        <?php if(!strcasecmp( $order_info['operations_status'], "good_to_process" )) { ?>
                                        <div class="alert alert-success">
                                            <i class="fa fa-check-circle"> </i>
                                            <?php echo $text_good_process; ?>
                                        </div>
                                        <?php } elseif(!strcasecmp( $order_info['operations_status'], "good_to_dispatch" )) { ?>
                                            <div class="alert alert-info">
                                                <i class="fa fa-check-circle"> </i>
                                                <?php echo $text_good_dispatch; ?>
                                            </div>
                                        <?php } ?>
                                        <strong>
                                            
                                            <?php echo $order_info['order_no']; ?>
                                          </strong> <br>
                                        <?php echo $order_info['store_name']; ?> <br/>
                                        <?php if( !strcasecmp( $order_info['operations_status'], "dont_dispatch" )  ){ ?>
                                        <span class="dont_disptach"><?php echo $text_dont_dispatch;?></span>
                                        <?php } ?>
                                      </td>
                                      <td class="text-left">
                                        <a href="<?php echo $order_info['customer_link']; ?>" target="_blank"><?php echo $order_info['customer'];?></a>
                                        <br/>
                                        <span style="color: #000;">
                                            <span class="click_to_see btn-primary btn-xs" data-field-type="telephone" data-field-value="<?php echo base64_encode($order_info['telephone']); ?>" onClick="clickToSee(this)"> Click to see </span>
                                            <!-- <?php //echo $order_info['telephone'];?> -->
                                        </span>
                                      </td>
                                      <td class="text-left">
                                        <strong><?php echo $order_info['shipping_company']; ?></strong> <br>
                                        <div class="order_list_comment">
                                            <?php echo $order_info['comment']; ?>
                                        </div>
                                    </td>
                                    <td class="text-left"><strong><?php echo $order_info['shipping_city']; ?></strong>
                                        <font color="blue">
                                            <?php if ( $order_info['gati_pincode_status'] ) { echo " - GATI " . strtoupper($order_info['gati_pincode_status']); } ?> </font> <br>
                                        <div class="order_list_comment">
                                        </div>
                                    </td>
                                    <td class="text-left suborder_table">
                                        <table class="table table-bordered">
                                          <?php if( !empty( $suborder_info ) ) { ?>
                                              <?php foreach($suborder_info as $key_suborder_id => $suborder_data){ ?>
                                                <tr <?php if ($suborder_data['red_flag']) {
                                                        echo('style="background-color:yellow; color:red;"');
                                                      } elseif ($suborder_data['dispute_flag']) {
                                                        echo('style="background-color:red; color:white;"');
                                                      } ?>>
                                                  <td class="text-center"><?php echo $key_suborder_id; ?></td>
                                                  <td><?php echo $suborder_data['total']; ?></td>
                                                  <td>
                                                    <strong> <?php echo $suborder_data['status'];  ?> </strong><br>
                                                    <div class="order_list_comment">
                                                        <?php
                                                            if ( !empty($suborder_data['order_history']) &&
                                                                 is_array($suborder_data['order_history']) ) {
                                                                $last_history_comment = end($suborder_data['order_history']);
                                                                echo $last_history_comment['comment'];
                                                            }
                                                         ?>
                                                    </div>
                                                  </td>
                                                  <td><?php echo $suborder_data['shipping_method'];?></td>
                                                  <td class="" width="75px;">
                                                      <a href="<?php echo $suborder_data['view']; ?>"
                                                         data-toggle="tooltip"
                                                         title="<?php echo $button_view; ?>"
                                                         class="btn btn-sm btn-info pull-left">
                                                              <i class="fa fa-eye"></i>
                                                      </a>
                                                  </td>
                                                </tr>
                                                <?php } ?>
                                            <?php } ?>
                                      </table>
                                      </td>
                                    <td class="text-left">
                                        <?php echo $order_info['date_added']; ?>
                                    </td>
                                    <td class="text-center">
                                        <strong><?php echo $order_info['payment_mode']; ?> </strong></br></br>
                                    </td>
                                </tr>
                                <?php } ?>
                              <?php } else { ?>
                                <tr>
                                  <td class="text-center" colspan="8"><?php echo $text_no_results; ?></td>
                                </tr>
                              <?php } ?>
                        </tbody>
                      </table>
                    </div>
                </div>
                <div class="row">
                  <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
                  <div class="col-sm-6 text-right"><?php echo $results; ?></div>
                </div>
            </div>
        </div>
    </div>
</div>
</script>
<style type="text/css">
    .suborder_table table tr td {
        padding: 4px !important;
    }
</style>
