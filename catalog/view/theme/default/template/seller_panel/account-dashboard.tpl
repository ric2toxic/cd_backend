<?php echo $header_seller; ?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h2 class="page-header">Dashboard</h2>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-shopping-cart fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge"><?php echo $total_order; ?></div>
                            <div><?php  echo $text_total_order; ?></div>
                        </div>
                    </div>
                </div>
                <a href="<?php echo $view_orders; ?>">
                    <div class="panel-footer">
                        <span class="pull-left"><?php echo $text_view_more; ?></span>
                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="panel panel-green">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-credit-card fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge"><?php echo $total_sales; ?></div>
                            <div><?php  echo $text_total_sales; ?></div>
                        </div>
                    </div>
                </div>
                <a href="<?php echo $view_sales; ?>">
                    <div class="panel-footer">
                        <span class="pull-left"><?php echo $text_view_more; ?></span>
                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>

        <?php if (isset($total_customer)) { ?>
            <div class="col-lg-3 col-md-6">
                <div class="panel panel-red">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-3">
                                <i class="fa fa-user fa-5x"></i>
                            </div>
                            <div class="col-xs-9 text-right">
                                <div class="huge"><?php echo $total_customer; ?></div>
                                <div><?php  echo $text_total_customer; ?></div>
                            </div>
                        </div>
                    </div>
                    <a href="<?php echo $view_customer; ?>">
                        <div class="panel-footer">
                            <span class="pull-left"><?php echo $text_view_more; ?></span>
                            <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                            <div class="clearfix"></div>
                        </div>
                    </a>
                </div>
            </div>
        <?php } ?>    
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-8">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-bar-chart-o fa-fw"></i> <?php echo $text_latest_order; ?>
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <table class="list table table-bordered">
                        <thead>
                            <tr>
                                <td><?php echo $column_order_no; ?></td>
                                <td><?php echo $column_status; ?></td>
                                <td><?php echo $column_order_date; ?></td>
                                <td><?php echo $column_order_total; ?></td>
                            </tr>
                        </thead>

                        <tbody>
                        <?php if (isset($orders) && $orders) { ?>
                            <?php foreach ($orders as $order) { ?>
                            <tr>
                                <td><a href="<?php echo $this->url->link('seller/account-order/viewOrder', 'order_id=' . $order['order_id'] . '&suborder_id=' . $order['suborder_id']); ?>" class="" title="<?php echo $this->language->get('ms_view_modify') ?>"><?php echo $order['order_no']; ?></a></td>
                                <td><?php echo $order['status']; ?></td>
                                <td><?php echo $order['order_date']; ?></td>
                                <td><?php echo $order['total']; ?></td>
                            </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td class="text-center" colspan="7"><?php echo $no_records; ?></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-8 -->
        <div class="col-lg-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-bell fa-fw"></i> Notifications Panel
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="list-group">
                        <a href="#" class="list-group-item">
                            <i class="fa fa-comment fa-fw"></i> New Comment
                            <span class="pull-right text-muted small"><em>4 minutes ago</em>
                            </span>
                        </a>
                        <a href="#" class="list-group-item">
                            <i class="fa fa-twitter fa-fw"></i> 3 New Followers
                            <span class="pull-right text-muted small"><em>12 minutes ago</em>
                            </span>
                        </a>
                        <a href="#" class="list-group-item">
                            <i class="fa fa-envelope fa-fw"></i> Message Sent
                            <span class="pull-right text-muted small"><em>27 minutes ago</em>
                            </span>
                        </a>
                        <a href="#" class="list-group-item">
                            <i class="fa fa-tasks fa-fw"></i> New Task
                            <span class="pull-right text-muted small"><em>43 minutes ago</em>
                            </span>
                        </a>
                        <a href="#" class="list-group-item">
                            <i class="fa fa-upload fa-fw"></i> Server Rebooted
                            <span class="pull-right text-muted small"><em>11:32 AM</em>
                            </span>
                        </a>
                        <a href="#" class="list-group-item">
                            <i class="fa fa-bolt fa-fw"></i> Server Crashed!
                            <span class="pull-right text-muted small"><em>11:13 AM</em>
                            </span>
                        </a>
                        <a href="#" class="list-group-item">
                            <i class="fa fa-warning fa-fw"></i> Server Not Responding
                            <span class="pull-right text-muted small"><em>10:57 AM</em>
                            </span>
                        </a>
                        <a href="#" class="list-group-item">
                            <i class="fa fa-shopping-cart fa-fw"></i> New Order Placed
                            <span class="pull-right text-muted small"><em>9:49 AM</em>
                            </span>
                        </a>
                        <a href="#" class="list-group-item">
                            <i class="fa fa-money fa-fw"></i> Payment Received
                            <span class="pull-right text-muted small"><em>Yesterday</em>
                            </span>
                        </a>
                    </div>
                    <!-- /.list-group -->
                    <a href="#" class="btn btn-default btn-block">View All Alerts</a>
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel .chat-panel -->
        </div>
        <!-- /.col-lg-4 -->
    </div>
    <!-- /.row -->
</div>
<!-- /#page-wrapper -->

<?php echo $footer_seller; ?>