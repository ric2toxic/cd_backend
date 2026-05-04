<?php echo $header; ?>
<div class="container">
  <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
  </ul>
  <?php if ($success) { ?>
  <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
    <button type="button" class="close" data-dismiss="alert">&times;</button>
  </div>
  <?php } ?>
  <div class="row">
    <div id="content" class="col-sm-12 category_page"><?php echo $content_top; ?>
      <div id="list_container" class="row default_store">
      <h2><?php echo $heading_title; ?></h2>
      <?php if ($products) { ?>
          <?php
              $i = 0;
            foreach ($products as $product) {
                $productInfo[$product['product_id']] = $product['row_data']; // store product info in json
                $i++;
                $css_class = 'left';
                if( $i == 2){
                  $i = 0;
                  $css_class = 'right';
                }
           ?>
            <div class="product-layout col-xs-6 col-sm-3 item <?php echo $css_class;?>">
              <div class="product-thumb product-wishlist">
                <div class="image">
                  <a href="<?php echo $product['href']; ?>" data-product-id="<?php echo $product['product_id']; ?>" class="fancybox productInfoAjax">
                    <img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-responsive" width="<?php echo $product['img_width']; ?>" height="<?php echo $product['img_height']; ?>"/>
                  </a>
                  <a href="<?php echo $product['remove']; ?>" class="button-remove">
                    <button type="button" class="btn btn-danger btn-xs" data-toggle="tooltip" title="<?php echo $button_remove; ?>"><i class="fa fa-times"></i></button>
                  </a>
                </div>
                <div>
                  <div class="caption">
                    <p class="name"><a href="<?php echo $product['href'];?>" data-product-id="<?php echo $product['product_id']; ?>" class="fancybox productInfoAjax"><?php echo $product['name']; ?></a></p>
                    <?php if($product['rating'] > 0){ ?>
                    <div class="rating">
                      <p>
                        <?php for ($i = 1; $i <= 5; $i++) { ?>
                        <?php if ($product['rating'] < $i) { ?>
                        <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-1x"></i></span>
                        <?php } else { ?>
                        <span class="fa fa-stack"><i class="fa fa-star fa-stack-1x"></i><i class="fa fa-star-o fa-stack-1x"></i></span>
                        <?php } ?>
                        <?php } ?><br/>
                      </p>
                    </div>
                    <?php } ?>
                    <p class="model"><?php echo $product['model']; ?></p>
                    <p class="stock"><?php echo $product['stock']; ?></p>
                    <p class="price">
                      <?php if ($product['price']) { ?>
                        <div class="price">
                          <?php if (!$product['special']) { ?>
                          <?php echo $product['price']; ?>
                          <?php } else { ?>
                          <b><?php echo $product['special']; ?></b> <s><?php echo $product['price']; ?></s>
                          <?php } ?>
                        </div>
                      <?php } ?>
                    </p>
                    <?php if(isset($product['cod_available']) && $product['cod_available']==0){ ?>
                    <span class="not_available_cod"><?php echo $text_cod_available; ?></span>
                    <?php }?>
                  </div>
                  <div class="button-group">
                    <button type="button" onclick="cart.add('<?php echo $product['product_id']; ?>');" data-toggle="tooltip" title="<?php echo $button_cart; ?>" class="btn btn-primary btn-xs"><i class="fa fa-shopping-cart"> Add To Cart</i></button>
                  </div>
                </div>
            </div>
            </div>
          <?php } ?>
      <?php } else { ?>
      <p><?php echo $text_empty; ?></p>
      <?php } ?>
      <div class="buttons clearfix">
        <div class="pull-right"><a href="<?php echo $continue; ?>" class="btn btn-primary"><?php echo $button_continue; ?></a></div>
      </div>
    </div>
      <?php echo $content_bottom; ?></div>
    <?php echo $column_right; ?></div>
</div>
<?php echo $footer; ?>
<!-- Start Script for hover description -->
<script type="text/javascript">
    $(document).ready(function(){

        var productInfoJson = $.parseJSON(JSON.stringify(<?php echo json_encode($productInfo); ?>));

        $(".productInfoAjax").each(function() {

            var hrefA = $(this).attr('href');
            var popup_string = '&';
            var find_query_string = hrefA.indexOf("?");
            if(find_query_string > 0) {
                popup_string = '&';
            }
            hrefA = hrefA+popup_string+'popup=true';

            var getProductId = $(this).attr('data-product-id');
            var getInfo = "";

            $.each(productInfoJson, function (key, data) {
                if(getProductId == key)
                    getInfo = data;
            });

            $(this).fancybox({
                type: "ajax",
                beforeLoad: function() {
                    this.ajax.data = getInfo;
                    this.ajax.type = 'POST';
                },
                maxWidth: 768,
                width:'90%',
                padding:0,
                helpers: {
                    overlay: {
                        locked: true,
                        closeClick: false
                    }
                },
                'hideOnContentClick': true,
                'href' : hrefA,
                centerOnScroll: true,
                autoScale: true,
                openEffect	: 'none',
                closeEffect	: 'none',
                afterShow: function() {
                    $('.zoomContainer').remove();

                    var owl = $("#main_img_container");
                    owl.owlCarousel({
                        itemsMobile : [768, 1],
                        autoPlay : false,
                        lazyLoad : true,
                        singleItem: true,
                        items: 1,
                    });
                },
                afterClose: function() {
                    $('.zoomContainer').remove();
                    $('.fancybox-overlay').remove();
                }
            });
        });


        $(document).on('click', '.productRelatedAjax', function(e){
            e.preventDefault();
            var postUrl = $(this).attr('href');
            var getRelatedProductId = $(this).attr('data-product-id');
            var getInfo = "";

            $.each(productRelatedJson, function (key, data) {
                if(getRelatedProductId == key)
                    getRelatedInfo = data;
            });

            $.fancybox({
                type: "ajax",
                beforeLoad: function() {
                    this.ajax.data = getRelatedInfo;
                    this.ajax.type = 'POST';
                },
                maxWidth: 768,
                width:'90%',
                padding:0,
                helpers: {
                    overlay: {
                        locked: true,
                        closeClick: false
                    }
                },
                'hideOnContentClick': true,
                'href' : postUrl,
                centerOnScroll: true,
                autoScale: true,
                openEffect	: 'none',
                closeEffect	: 'none',
                afterShow: function() {
                    $('.zoomContainer').remove();

                    var owl = $("#main_img_container");
                    owl.owlCarousel({
                        itemsMobile : [768, 1],
                        autoPlay : false,
                        lazyLoad : true,
                        singleItem: true,
                        items: 1,
                    });
                },
                afterClose: function() {
                    $('.zoomContainer').remove();
                    $('.fancybox-overlay').remove();
                }
            });

        });

    });

    function removeclearfix(){
        $("#content div.clearfix").removeClass("clearfix");
    };

</script>

<script type="text/javascript">
  <?php /* if (isset($international_store)) { ?>

var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/5a420d52bbdfe97b137fd4ba/default';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();

<?php } else { ?>

var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/55faa35605ceaf627695ea99/default';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();

<?php } */ ?>
</script>

<script  src="catalog/view/theme/default/javascript/touchspin/jquery.bootstrap-touchspin.js" type="text/javascript"></script>