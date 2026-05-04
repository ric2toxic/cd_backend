<?php echo $header; ?>
<div id="page-wrapper">
    <!-- page title-->
    <div class="col-sm-12">
        <h1 class="page_title">Orders</h1>
    </div>
    <!-- page Contant-->
    <!-- profile_tabs -->
    <div class="col-sm-12">
        <ul class="nav nav-tabs profile_tebination">
            <li class="active">
                <a href="<?php echo $pickup_requested_link; ?>">
                    <?php echo $tab_pickup_requested; ?>
                </a>
            </li>
            <li>
                <a href="#pickup_done" data-toggle="tab" aria-expanded="false">
                    <?php echo $tab_pickup_done; ?>
                </a>
            </li>
            <li>
                <a href="#tentative_orders" data-toggle="tab" aria-expanded="false">
                    <?php echo $tab_tentative_orders; ?>
                </a>
            </li>
        </ul>

        <div class="col-sm-12 profile_detail panel-group">
            <div class="tab-content">
                <!-- Pickup Requested section(start) -->
                <div id="pickup_requested" class="tab-pane fade in active">
                    <?php echo $pickup_order_request; ?>                    
                </div> 
                <!-- Pickup Done section(start) -->
                <div id="pickup_done" class="tab-pane fade">
                    <?php echo $pickup_order_done; ?>  
                </div>
                <!-- Tentative Orders section(start) -->
                <div id="tentative_orders" class="tab-pane fade">
                    <?php echo $tentative_order; ?>  
                </div>
            </div>
        </div>
    </div>
</div>    
<?php echo $footer; ?>

<script type="text/javascript">
    $(document).ready(function(){
        if(location.hash !=''){
            $(window).scrollTop(0);
        }
    });
</script>