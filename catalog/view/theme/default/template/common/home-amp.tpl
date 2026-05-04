<?php echo $header; ?>
<div class="full-width-banner">
    <?php echo $content_top; ?>
</div>


<div class="container-fluid home_width_adjustment">
    <?php if ($show_promotional_tabs == 1) {
    ?>
      <div class="col-sm-12 offer nopadding">
        <div class="col-sm-3 nopadding">
          <div class="left_padding">
            <div class="col-sm-4 nopadding">
              <div class="offer-2"></div>
            </div>
            <div class="col-sm-8 discount_text"><b>2% DISCOUNT</b>
              <p>Get 2% discount on every prepaid order</p>
            </div>
          </div>
          </div>
          <div class="col-sm-3 nopadding">
          <div class="padding_adjust">
          <a href="https://www.wholesalebox.in/sale">
            <div class="col-sm-4 nopadding">
              <div class="clearance-sale"></div>
            </div>
            <div class="col-sm-8 discount_text"><b>OFFER ZONE</b>
            <p>Buy stock at maximum possible discount</p>
            </div>
          </a>
          </div>
          </div>
          <div class="col-sm-3 nopadding">
          <div class="right_padding">
            <div class="col-sm-4 nopadding">
              <div class="offer-3"></div>
            </div>
            <div class="col-sm-8 discount_text"><b>3% DISCOUNT</b>
            <p>Get 3% discount on prepaid order of Rs. 25,000 & above</p>
            </div>
          </div>
          </div>
          <div class="col-sm-3 nopadding">
          <div class="right_padding">
            <div class="col-sm-4 nopadding">
              <div class="cashback"></div>
            </div>
            <div class="col-sm-8 discount_text"><b>2% CASHBACK</b>
            <p><?php echo $text_two_cashback;?></p>
            </div>
          </div>
          </div>
      </div>
     <?php }
?>
    
  <div class="row"><?php //echo $column_left; ?>
    <div style="display: none"><input type="button" class="append_categories" name="append_categories" value="append categories" /></div>
    <div id="content" class="col-sm-12 homepage">
        <div id="latest_data0" class="latest_container"></div>
        <div id="latest_data1" class="trending_container"></div>
        <?php echo $content_bottom; ?>
        <?php $i = 0;
              foreach($category_ids as $cat_id):
                if($i == 3){
        ?>        
          <!--mobile banner!-->
          <section class="mobile_banner">
            <h2>DOWNLOAD WHOLESALEBOX APP NOW</h2>
            <P>Fast, Simple & Delightful. All it takes is 30 seconds to Download.</P>
            <div class="google_app-btn">
              <a href="https://play.google.com/store/apps/details?id=in.wholesalebox" target="_blank">
              <img src="image/google-play-android-app.svg" alt="Android app on google play" width="140px" /></a>

              <a href="https://itunes.apple.com/us/app/wholesalebox/id1254820324?mt=8" target="_blank">
              <img src="image/ios_download.svg" alt="ios app on app store" width="140px"/></a>
            </div>
          </section>  
          <!--mobile banner end!-->
        <?php   }  $i++; ?>
        <div id="category_<?php echo $cat_id;?>"></div>
        <?php endforeach;?>
    </div>
</div>

</div> 
<div id="wait" class="col-sm-12" align="center" style="display:block; padding-left:0;"><img src='image/Preloader_2.gif' width="35" height="35" /></div>
<div class="keep_safe">
    <div class="container">
    <div class="step">
      <div class="col-sm-4">
      <img src="<?php echo $static_content_url; ?>new_home/seal.png">
        <h3>BUY  DIRECT  FROM  MANUFACTURER</h3>
        <div><p>Buy at Lowest Factory Price</p></div>
      </div>
      <div class="col-sm-4">
        <img src="<?php echo $static_content_url; ?>new_home/48HR.png">
        <h3>EASY 48 Hr RETURN POLICY</h3>
        <div><p>We offer you best return policy</p></div>
      </div>
      <div class="col-sm-4">
        <img src="<?php echo $static_content_url; ?>new_home/DOORDELIVERY.png">
        <h3>DOOR DELIVERY</h3>
        <div><p>Receive goods on your shop</p></div>
      </div>
      </div>
    </div>
    </div>
    <div class="container">
      <div class="home_tab col-sm-9">
            <div class="col-sm-12 tab-menu">
              <div class="tab_list">
                <a href="#" class="list-group_btn active">
                  <h4><?php echo $text_tab_info_1; ?></h4>
                  <div class="down"></div>
                </a>
                <a href="#" class="list-group_btn">
                  <h4><?php echo $text_tab_info_2; ?></h4>
                  <div class="down"></div>
                </a>
                <a href="#" class="list-group_btn">
                  <h4><?php echo $text_tab_info_3; ?></h4>
                  <div class="down"></div>
                </a>
              </div>
            </div>
            <div class="col-sm-12 bhoechie-tab">
              <div class="tab-text active">
                    <p>
                    <img src="<?php echo $static_content_url; ?>infographics_1.jpg" class="tab_infografics">
                      <?php echo $text_tabs_bottom_1; ?>
                    </p>
                    </div>
              <div class="tab-text">
                    <p>
                    <img src="<?php echo $static_content_url; ?>infographics_2.jpg" class="tab_infografics">
                        <?php echo $text_tabs_bottom_2; ?>
                    </p>
              </div>
              <div class="tab-text">
                    <p>
                    <img src="<?php echo $static_content_url; ?>infographics_3.jpg" class="tab_infografics">
                        <?php echo $text_tabs_bottom_3; ?>
                    </p>
              </div>
            </div>
      </div>

    <div class="stats col-sm-3">
      <div class="col-sm-12 wsb_stats"><?php echo $text_latest_stats ?></div>
      <div class="col-sm-12 stats_design_box">
      <span class="design_img_box col-sm-4 col-lg-4"><img src="image/kurti_icon_new.png" class="img_icon" ></span>
      <div class="stats_text col-sm-8 col-lg-8"><?php echo $text_designs ?><p><?php echo $total_products ?>+</p></div>
      <div class="clear"></div>
      </div>

      <div class="col-sm-12 stats_design_box">
      <span class="design_img_box col-sm-4 col-lg-4"><img src="image/shop_icon_new.png" class="img_icon" ></span>
      <div class="stats_text col-sm-8 col-lg-8"><?php echo $text_users ?><p><?php echo $total_customers ?>+</p></div>
      <div class="clear"></div>
      </div>
    </div>
    </div>

<?php echo $footer; ?>