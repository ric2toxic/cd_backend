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
    <div class="shop_by_category">
      <div class="row">
      <h3>SHOP BY CATEGORY</h3>
          <div class="col-sm-2 preload">
            <a href="<?php echo $base; ?>women-kurtis">
              <img src="<?php echo $static_content_url; ?>new_home/wholesale-kurti.jpg">
              <div class="cagegory_name"><div class="category_text">KURTIS</div></div>
            </a>
          </div>
          <div class="col-sm-2 preload">
            <a href="<?php echo $base; ?>suits-catalog">
              <img src="<?php echo $static_content_url; ?>new_home/wholesale-suit catalog.jpg">
              <div class="cagegory_name"><div class="category_text">SUIT CATALOG</div></div>
            </a>
          </div>
          <div class="col-sm-2 preload">
            <a href="<?php echo $base; ?>bottoms">
              <img src="<?php echo $static_content_url; ?>new_home/wholesale-patiyala.jpg">
              <div class="cagegory_name"><div class="category_text">BOTTOMS</div></div>
            </a>
          </div>
          <div class="col-sm-2 preload">
            <a href="<?php echo $base; ?>menswear">
              <img src="<?php echo $static_content_url; ?>new_home/wholesale-shirt.jpg">
              <div class="cagegory_name"><div class="category_text">MENS WEAR</div></div>
            </a>
          </div>
          <div class="col-sm-2 preload">
            <a href="<?php echo $base; ?>kids-wear">
              <img src="<?php echo $static_content_url; ?>new_home/wholesale-kidswear.jpg">
              <div class="cagegory_name"><div class="category_text">KIDS WEAR</div></div>
            </a>
          </div>
          <div class="col-sm-2 preload">
            <a href="<?php echo $base; ?>home-furnishing">
              <img src="<?php echo $static_content_url; ?>new_home/wholesale-bedsheet.jpg">
              <div class="cagegory_name"><div class="category_text">HOME FURNISHING</div></div>
            </a>
          </div>
          
      </div>
    </div>

  <div class="row"><?php //echo $column_left; ?>
    <div style="display: none"><input type="button" class="append_categories" name="append_categories" value="append categories" /></div>
    <div id="content" class="col-sm-12 homepage">
        <?php echo $content_bottom; ?>
        <div id="latest_data" class="latest_container"></div>
        <div id="trending_data" class="trending_container"></div>
        <?php foreach($category_ids as $cat_id):?>
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
      <div class="home_tab">
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 tab-menu">
              <div class="tab_list">
                <a href="#" class="list-group active text-center">
                  <h4><?php echo $text_tab_info_1; ?></h4>
                </a>
                <a href="#" class="list-group text-center">
                  <h4><?php echo $text_tab_info_2; ?></h4>
                </a>
                <a href="#" class="list-group text-center">
                  <h4><?php echo $text_tab_info_3; ?></h4>
                </a>
              </div>
            </div>
            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 bhoechie-tab">
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
    </div>

  <div class="container">
    <div class="stats col-sm-12">
      <div class="col-sm-4">
        <div class="wsb_stats"><h1><?php echo $text_latest_stats ?>
        <span><img src="<?php echo $static_content_url; ?>arrow_home.png"></span></h1></div>
      </div>
      <div class="col-sm-4">
        <div class="stats-1">
          <div class="col-sm-4"><span><img src="<?php echo $static_content_url; ?>kurti_icon.png" class="img_icon" ></span></div>
          <div class="col-sm-8 text"><?php echo $total_products ?>
          <div class="stats_text"><p><?php echo $text_designs ?></p></div></div>
        </div>
      </div>
      <div class="col-sm-4">
        <div class="stats-1">
          <div class="col-sm-4"><span><i class="fa fa-users"></i></span></div>
          <div class="col-sm-8 text"><?php echo $total_customers ?>
          <div class="stats_text"><p><?php echo $text_users ?></p></div></div>
        </div>
      </div>
    </div>
  </div>

  <section class="container-fluid home_width_adjustment">
    <div class="wsb-app-link back_image">
      <div class="container">
        <div class="col-sm-6"><img src="<?php echo $static_content_url; ?>wholesalebox-app.jpg"></div>
        <div class="col-sm-6 wsb-app-details">
          <div><h1>DOWNLOAD WHOLESALEBOX APP NOW</h1></div>
          <p>Fast, Simple & Delightful.<br/>
          All it takes is 30 seconds to Download.<br/>
          <?php echo $text_two_cashback;?>
          </p>
          <div class="app-btn">
            <a href="https://play.google.com/store/apps/details?id=in.wholesalebox">
                    <img src="image/android_app_image.png" alt="Android app on google play">
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>

<?php echo $footer; ?>