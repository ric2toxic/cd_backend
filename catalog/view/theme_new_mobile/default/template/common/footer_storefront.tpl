<footer>
    <div class="container">
        <div class="row">
            <?php if ($informations) { ?>
            <div class="col-sm-3">
                <h4><?php echo $text_information; ?></h4>
                <ul class="list-unstyled">
                <!--   <li><a href="http://www.wholesalebox.in/about-us">About us</a></li>-->           
                    <?php foreach ($informations as $information) { ?>
                    <li><a href="<?php echo $information['href']; ?>"><?php echo $information['title']; ?></a></li>
                    <?php } ?>
                    <?php if ($logged) { ?>
                    <?php if($is_dropshipper == 0){ ?>
                    <li><a href="<?php echo HTTP_SERVER; ?>dropshipper"><?php echo $text_dropshipper; ?></a></li>
                    <?php } ?>
                    <li><a href="<?php echo $logout; ?>"><?php echo $text_logout; ?></a></li>
                    <?php }else { ?>
                    <li><a href="<?php echo HTTP_SERVER; ?>dropshipper"><?php echo $text_dropshipper; ?></a></li>
                    <li><a href="<?php echo $register; ?>"><?php echo $text_register; ?></a></li>
                    <li><a href="<?php echo $login; ?>"><?php echo $text_login; ?></a></li>
                    <li><a href="<?php echo $careers; ?>"><?php echo $text_career;?></a></li>
                    <?php } ?>
                </ul>
            </div>
            <?php } ?>

            <div class="col-sm-2 hidden-xs">
                <h5><?php echo $text_account; ?></h5>
                <ul class="list-unstyled">
                    <li><a href="<?php echo $account; ?>"><?php echo $text_account; ?></a></li>
                    <li><a href="<?php echo $order; ?>"><?php echo $text_order; ?></a></li>
                    <li><a href="<?php echo $wishlist; ?>"><?php echo $text_wishlist; ?></a></li>

                </ul>
            </div>

            <div class="col-sm-4">
                <div class="footer4">
                    <h2>Helpline Number</h2><br />
                    <ul>
                        <li>

                            <a href="tel:+918890958318" class="phone"><i class="fa fa-phone change_color"></i>(+91) 8890958318 </a>
                            <br /><span class="ofc_time" style="padding:0px">(10am to 8pm)</span>

                        </li>

                        <li>
                            <a href="tel:+918890958318 " class="whatsapp"><i class="fa fa-whatsapp"></i>+918890958318 </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-sm-3">
                <div class="footer4">
                    <h3>Logistics Partners</h3>
                    <div class = "col-sm-12 col-xs-12" style="padding:0px;">
                        <img src="image/gatikwe.png" alt="Gati is our Logistics Partner">
                    </div>
                    <div class = "col-sm-12 col-xs-12" style="padding:0px;">
                        <img src="image/bluedart.png" alt="BlueDart is our Logistics Partner">
                    </div>
                </div>
            </div>

        </div>
        <br />
<!--         <div class="alexa">
            <a href="http://www.alexa.com/siteinfo/wholesalebox.in"><script type="text/javascript" src="http://xslt.alexa.com/site_stats/js/t/a?url=wholesalebox.in"></script></a>
        </div> -->

        <hr>


        <p><?php echo $powered; ?></p>
    </div>
</footer>


<script>
    $(document).ready(function () {

        /* highlight the top nav as scrolling occurs8*/
        $('body').scrollspy({ target: '#sticky' })

        $('#column-left').removeClass('hidden-xs')
        $('button.navbar-toggle').click(function() {
            $('.navmenu').offcanvas('toggle');
        });

        $( "#filter_icon, div.filter_click" ).click(function() {

            $( "div.filter_box" ).slideToggle( "slow" );
            $( "div.filter_box").removeClass('hidden-xs');

        });

        $("a#filter_option").fancybox({
            'hideOnContentClick': true
        });
        $("a#sortby").fancybox({
            'hideOnContentClick': true
        });
        $(".fancybox").click(function(e){
            e.preventDefault();
            var $href = $(this).attr('href');
           // history.replaceState(null, null, 'women-kurtis#!test');
           // location.href  = $href;
            var popup_string = '&';
            var find_query_string = $href.indexOf("?");
            if(find_query_string > 0) {
                popup_string = '&';
            }
            $href = $href+popup_string +'popup=true';

            //+'?popup=true';
            $.fancybox({

                hideOnContentClick: true,
                maxWidth: 768,
                width:'100%',
                padding:0,
                type: 'iframe',
                href:$href,
                centerOnScroll: true,
                autoScale: true,
                iframe: {
                    preload: false // fixes issue with iframe and IE
                },
                helpers: {
                    overlay: {
                        locked: true,
                        closeClick: false
                    }
                }
            });
        });


        <?php if($is_home != 1) { ?>
            $('#category_dropdown').click(function(){
                $(this).children('i').toggleClass('fa-caret-down, fa-caret-up');
                $('ul.nav').toggle(1);
            });
        <?php } ?>

        $('a.dropdown-mobile').click(function(e){
            e.preventDefault();
            $('ul.dropdown-mobile-content').hide('fast');
            $(this).next('ul.dropdown-mobile-content').toggle('fast');

        });

    });


</script>

<?php if($route == 'product/category' || $route == 'product/search'){ ?>

<script type="text/javascript">
    $(document).ready(function() {
        $(window).scroll(function() {
            if ($(this).scrollTop()) {
                $('#scroll_to_top:hidden').stop(true, true).fadeIn();
            } else {
                $('#scroll_to_top').stop(true, true).fadeOut();
            }
        });

        $("a[href='#top']").click(function () {
            $("html, body").animate({scrollTop: 0}, "slow");
            return false;
        });
    });
</script>
<a href="#top" id="scroll_to_top" style="display: none;">&nbsp;</a>

<?php } ?>


<script>
    $(document).ready(function(){
        $(".SearchEffect").click(function(){

            //$('#searchInput').toggle('fast');

            if($('#searchInput').is(":visible")){ 
				$(this).removeClass("material-search-cross-custom").addClass("material-search-custom");
                $('#searchInput').hide('fast');
                //$(this).removeClass('clear').addClass('search').html('search');
                $(this).removeClass('clear').addClass('search').html('');
                $("i.heart").show();
                $("img.logo").show();

            }

            if($('#searchInput').is(":hidden")){ 
				$(this).removeClass("material-search-custom").addClass("material-search-cross-custom");
				
                $('#searchInput').show('fast');
                //$(this).removeClass('search').addClass('clear').html('clear');
                $(this).removeClass('search').addClass('clear').html('');
                $("img.logo").hide();
            }

        });

        $('#store_switch_filter').on('click', function (argument) {
            $.ajax({
                url : 'index.php?route=common/header/getStoreSwitchNew',
                dataType: 'json',

                beforeSend: function () {
                    $('body').removeClass('loaded').addClass('loading');
                },
                success: function (json) {
                    location.reload();

                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }

            });
        });

    });

</script>
<div class="global-bg-layer"></div>
<div class="global-ajax-loader"></div>

<script  src="catalog/view/javascript/jquery/jquery-ui.min.js" type="text/javascript"></script>
<script  src="catalog/view/javascript/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script  src="catalog/view/theme_new_mobile/default/javascript/jasny-bootstrap/js/jasny-bootstrap.min.js" type="text/javascript"></script>
<script  src="catalog/view/theme_new_mobile/default/javascript/fancybox/jquery.fancybox.pack.js" type="text/javascript"></script>
<script  src="catalog/view/theme_new_mobile/default/javascript/expand-search/modernizr.custom.js" type="text/javascript"></script>
<script  src="catalog/view/theme_new_mobile/default/javascript/common.js" type="text/javascript"></script>
<?php foreach ($scripts as $script) { ?>
<script  src="<?php echo $script; ?>" type="text/javascript"></script>
<?php } ?>

<!-- Browser notifications
<script src="https://notifi.io/push/notifier/142/loader.js"></script> -->
<!-- Pixel -->
<script type="text/javascript" src="//js.m-bazaar.in/wholesalebox.min.js" ></script>

</body></html>
