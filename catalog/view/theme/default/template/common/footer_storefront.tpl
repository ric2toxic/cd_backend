<footer>
  <div class="container">
    <div class="row">
      <?php if ($informations) { ?>
      <div class="col-sm-3">
        <h5><?php echo $text_information; ?></h5>
        <ul class="list-unstyled">
          <li><a href="http://www.wholesalebox.in/about-us">About us</a></li>
            <li><a href="http://www.wholesalebox.in/contact-us">Contact us</a></li>
          <?php foreach ($informations as $information) { ?>
          <li><a href="<?php echo $information['href']; ?>"><?php echo $information['title']; ?></a></li>
          <?php } ?>
          <?php if ($logged) { ?>
            <?php if($is_dropshipper == 0){ ?>
              <li><a href="<?php echo $dropshipper; ?>"><?php echo $text_dropshipper; ?></a></li> 
            <?php } ?>      
            <li><a href="<?php echo $logout; ?>"><?php echo $text_logout; ?></a></li>
          <?php }else { ?>
          <li><a href="<?php echo $dropshipper; ?>"><?php echo $text_dropshipper; ?></a></li>
          <li><a href="<?php echo $affiliate; ?>"><?php echo $text_Affiliate; ?></a></li>
          <li><a href="<?php echo $register; ?>"><?php echo $text_register; ?></a></li>
          <li><a href="<?php echo $login; ?>"><?php echo $text_login; ?></a></li>
          <li><a href="<?php echo $careers; ?>"><?php echo $text_career;?></a></li>

          <?php } ?>
        </ul>
      </div>
      <?php } ?>

      <div class="col-sm-3">
       <div class="footer4">
         <h5>Helpline Number</h5>
         <ul>
           <li>

               <a href="tel:+91 8890958318" class="phone"><i class="fa fa-phone"></i>(+91) 8890958318</a>
               <br /><span class="ofc_time" style="padding:0px">(10am to 8pm)</span>

           </li>

           <li>
              <a href="tel:+918890958318" class="whatsapp"><i class="fa fa-whatsapp"></i>+918890958318</a>
           </li>
         </ul>
       </div>
      </div>
      
     <div class="col-sm-3">
       <div class="footer4">
           <h5>Logistics Partners</h5>
           <div class = "col-sm-12 col-xs-4" style="padding:0px;">
               <img src="image/gatikwe.png" alt="Gati is our Logistics Partner">
           </div>
           <div class = "col-sm-12 col-xs-8" style="padding:0px;">
               <img src="image/bluedart.png" alt="BlueDart is our Logistics Partner">
           </div>
       </div>
      </div>
<!--      <div class="col-sm-3">
        <div class="trust_seal">

        </div>
     </div> -->

    </div>
    <div class="row">
        <div class="col-sm-3 social_profiles">
            <ul>
                <li>
                    <a class="sf_facebook" href="https://web.facebook.com/wholesalebox1" target="_blank"></a>
                </li>
                <li><!-- google plus link -->
                    <a class="sf_googleplus" href="https://plus.google.com/115306833718886596322" target="_blank"></a>
                </li>
               <?php /* ?> <li>
                    <a class="sf_youtube" href="https://web.facebook.com/wholesalebox1" target="_blank"></a>
                </li>
                <?php */ ?>

            </ul>
        </div>
        <div class="col-sm-6 ">
            <div class="ic_payment_methods">

            </div>
        </div>
        <?php
        $alexa = 1;
        if($alexa == 1){
        ?>
<!--         <div class="col-sm-3 ">
            <div class="alexa">
                  <a href="http://www.alexa.com/siteinfo/wholesalebox.in"><script type="text/javascript" src="http://xslt.alexa.com/site_stats/js/t/a?url=wholesalebox.in"></script></a>
            </div>
        </div> -->
        <?php } ?>
    </div>
  </div>
  <div class="footer_copyright">
    <div class="container ">
        <div class="row">
            <div class="col-sm-12">
                <p><?php echo $powered; ?></p>
            </div>
        </div>
    </div>
  </div>

</footer>

<script type="text/javascript">


  /* highlight the top nav as scrolling occurs8*/
  $('body').scrollspy({ target: '#sticky' })

</script>
<script>
  $(document).ready(function () {
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

<?php if($is_home != 1) { ?>
      $('#category_dropdown').click(function(){
          $(this).children('i').toggleClass('fa-caret-down, fa-caret-up');
           $('ul.nav').toggle(1);
      });
<?php } ?>

  });
  $('a.dropdown-mobile').click(function(e){
      e.preventDefault();
      $('ul.dropdown-mobile-content').hide();
      $(this).next('ul').toggle('fast');

  });

</script>
<?php if($route == 'product/product'){ ?>
<script>
  $(document).ready(function () {

    var owl = $("#owl-related");
    owl.owlCarousel({
              loop:true,
              items : 5,
              autoPlay : 3000,
              itemsDesktop:  [1199,6],
              itemsMobile: [479, 1],
              itemsDesktopSmall:  [979,5],
              itemsTablet: [768,3],
              itemsScaleUp:true,
              goToFirstSpeed : 3000,
              transitionStyle:"slide",
              lazyLoad : true,
              rewindSpeed : 3000,
              pagination: false,



            });

    // Custom Navigation Events
    $(".owl-related-next").click(function(){
      owl.trigger('owl.next');
    })
    $(".owl-related-prev").click(function(){
      owl.trigger('owl.prev');
    })


  });


</script>

<?php } ?>

</body>
</html>
