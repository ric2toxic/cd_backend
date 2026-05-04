<div class="left_section">
          <div class="panel-group user_block">
          <div class="panel panel-default">
            <div class="panel-heading panel_block">
                <h4 class="panel-title">
                    <i class="fa fa-user red_color"></i> 
                  <span class="panel_title"> Hello</span> 
                  <span class="bold_content">Mahaveer</span>
                </h4>
            </div>
          </div>
      </div>
     
     <?php if($seller_login){ ?>
      <div class="panel-group user_block">
                <div class="panel panel-default">
                  <a href="<?php echo $manufacturer_dashboard_link; ?>">
                    <div class="panel-heading panel_block">
                      <h4 class="panel-title">
                          <i class="fa fa-dashboard red_color"></i> 
                          <span class="panel_title"> <?php echo $text_go_to_seller_dashboard; ?></span>
                        </h4>
                    </div>
                </a>    
                </div>
      </div>
    <?php } ?>

    <div>  
          <div class="panel-group">
            <div class="panel panel-default">
              <div class="panel-heading panel_block"  data-toggle="collapse" data-title-value="Orders" href="#collapseOrders">
                          <h4 class="panel-title"> 
                          <i class="blue_color fa fa-bars"> </i> 
                              <span class="panel_title"> <?php echo $text_my_orders ?> </span>
                              <span class="toggle_caret pull-right"><i class="fa fa-angle-right"></i></span>
                          </h4>
              </div>

              <div id="collapseOrders" class="panel-collapse collapse" aria-expanded="true">
                <ul class="list-group">
                  <li class="list-group-item "><a href="<?php echo $order; ?>"><?php echo $text_title_my_order; ?></a></li>
                  <li class="list-group-item "><a href="<?php echo $pending_order; ?>"><?php echo $text_title_payment_pending_order; ?></a></li>
                  <li class="list-group-item "><a href="<?php echo $delivered_order; ?>"><?php echo $text_title_delivered_order; ?></a></li>
                  <li class="list-group-item "><a href="<?php echo $return_order; ?>"><?php echo $text_title_return_order; ?></a></li>
                  <li class="list-group-item "><a href="<?php echo $cancel_order; ?>"><?php echo $text_title_cancel_order; ?></a></li>
                </ul>
              </div>

            </div>
          </div> 


          <div class="panel-group">
            <div class="panel panel-default">
              <div class="panel-heading panel_block" data-toggle="collapse" data-title-value="Profile" href="#collapseProfile" style="padding:9px 15px;">
                          <h4 class="panel-title"> 
                          <i class="blue_color fa fa-user"> </i> 
                              <span class="panel_title"> <?php echo $text_title_profile ?> </span>
                              <span class="toggle_caret pull-right"><i class="fa fa-angle-right"></i></span>
                          </h4>
              </div>

               <div id="collapseProfile" class="panel-collapse collapse" aria-expanded="true">
                <ul class="list-group">
                  <li class="list-group-item "><a href="<?php echo $edit; ?>"><?php echo $text_title_profile_info; ?></a></li>
                  <li class="list-group-item "><a href="<?php echo $address; ?>"><?php echo $text_title_manage_address; ?></a></li>
                  <li class="list-group-item "><a href="<?php echo $bank_details; ?>"><?php echo $text_title_save_bank_details; ?></a></li>
                  <li class="list-group-item "><a href="<?php echo $my_returns; ?>"><?php echo $text_return; ?></a></li>
                  <li class="list-group-item "><a href="<?php echo $wishlist; ?>"><?php echo $text_wishlist; ?></a></li>
                </ul>
              </div>

            </div>
          </div>
          
          <?php if($statement) { ?>
           <div class="panel-group">
            <div class="panel panel-default">
              <div class="panel-heading panel_block">
                      <a href="<?php echo $statement ?>">
                          <h4 class="panel-title"> 
                          <i class="blue_color fa fa-file-excel-o"> </i> 
                              <span class="panel_title"> <?php echo $text_account_statement ?> </span>
                          </h4>
                        </a>
                    </div>
            </div>
          </div>
          <?php } ?>

          <div class="panel-group">
            <div class="panel panel-default">
              <div class="panel-heading panel_block">
                      <a href="<?php echo $credit_application ?>">
                          <h4 class="panel-title"> 
                          <i class="blue_color fa fa-file"> </i> 
                              <span class="panel_title"> <?php echo $text_credit_apply ?> </span>
                          </h4>
                        </a>
                    </div>
            </div>
          </div>

          <div class="panel-group">
            <div class="panel panel-default">
              <div class="panel-heading panel_block <?php if($active == 'product_feed') {  echo "active"; } ?>">
                      <a href="<?php echo $product_feed ?>" <?php if($active == 'product_feed') { ?> style="color:#fff;" <?php } ?>>
                          <h4 class="panel-title"> 
                          <i class="fa fa-file-text-o" <?php if($active == 'product_feed') { ?> style="color:#fff;" <?php } ?>> </i> 
                              <span class="panel_title"> <?php echo $text_product_feed ?> </span>
                          </h4>
                        </a>
                    </div>
            </div>
          </div>

          <div class="panel-group">
            <div class="panel panel-default">
              <div class="panel-heading panel_block <?php if($active == 'credit_note') {  echo "active"; } ?> ">
                      <a href="<?php echo $credit_note ?>" <?php if($active == 'credit_note') { ?> style="color:#fff;" <?php } ?>>
                          <h4 class="panel-title"> 
                          <i class="blue_color fa fa-file" <?php if($active == 'credit_note') { ?> style="color:#fff;" <?php } ?>> </i> 
                              <span class="panel_title"> <?php echo $text_credit_note ?> </span>
                          </h4>
                        </a>
                    </div>
            </div>
          </div>

          <div class="panel-group">
            <div class="panel panel-default">
              <div class="panel-heading panel_block <?php if($active == 'helpdesk') {  echo "active"; } ?> ">
                      <a href="<?php echo $support ?>" <?php if($active == 'helpdesk') { ?> style="color:#fff;" <?php } ?>>
                          <h4 class="panel-title"> 
                          <i class="blue_color fa fa-headphones" <?php if($active == 'helpdesk') { ?> style="color:#fff;" <?php } ?>> </i> 
                              <span class="panel_title"> <?php echo $text_support ?> </span>
                          </h4>
                        </a>
                    </div>
            </div>
          </div>

          <div class="panel-group">
            <div class="panel panel-default">
              <div class="panel-heading panel_block">
                      <a href="javascript:;" onclick="logout('<?php echo $logout; ?>');">
                          <h4 class="panel-title"> 
                          <i class="blue_color fa fa-power-off"> </i> 
                              <span class="panel_title"> <?php echo $text_logout ?> </span>
                          </h4>
                        </a>
                    </div>
            </div>
          </div>

    </div>
</div>

<script type="text/javascript">
  
  function logout(url)
  {
    dataLayer.push({'event': 'we-custom-logout'});
    setTimeout(function(){ window.location.assign(url); }, 500);
  }

</script>