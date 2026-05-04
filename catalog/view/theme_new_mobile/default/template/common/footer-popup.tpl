<br />
<br />
<?php if($route == 'product/product'){ ?>
<script>
  $(document).ready(function () {

    var owl = $("#owl-related");
    owl.owlCarousel({
              autoPlay : 3000,
              itemsMobile: [479, 1],
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




</body></html>
