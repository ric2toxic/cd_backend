<?php echo $header; ?>
<div class="container"> 	
	<div class="row">
      <div class="">
          <div class="dropshipper_heading">
              <h1 class="dropshipper_heading_title">What is Dropshipping?</h1>
              <p class="dropshipper_heading_list">Dropshipping is a practice of selling a product to your customers that you don’t have physically in stock. It’s a new trend of marketing and distribution of products.<br><br>We can make this possible by shipping the stock directly to your customer with your name
              </p>
          </div> 
      </div>
      <div class="dropshipper_image">
      </div>
      <hr>
      <div id="dropshipper_text_container">
          <h1>Benefits of Dropshipping:</h1>
          <ul  id="dropshipper_text_benefits">
              <li>Unlike traditional business, you do not have to source products in bulk before you sell anything. Therefore, you aren't facing the risk of having dead stock.</li><br />
              <li>No need of inventory management or maintaining your own warehouse.</li><br />
              <li>Normally, when you buy from wholesalers, you have to buy in large quantities. But with Wholesalebox.in, you get low wholesale prices even on small quantities.</li><br />
              <li>Another major benefit is that you can start your business without any need for start-up capital, as our company will be managing the stock for you. At Wholesalebox.in, we have everything in stock that we list online. Only once you get an order, you pay us for shipping it out.</li><br />
              <li>You don't have to pick, pack, and ship orders as we will do that on your behalf. So you can completely focus on your sales and just communicate the sales to us and we will take it from there. And as said, we have everything in stock, this means, your orders are dispatched within a working day.</li><br />
              <li>And the best part, the whole transaction is completely anonymous. Customer wouldn’t know that it came from us!</li><br />
             <br />
          </ul>

      </div>
        <div id="dropshipper_text_container">
          <h1>How do I become a dropshipper with Wholesalebox.in ?</h1>
          <ul id="dropshipper_text_benefits">
              
              <?php if ($logged) { ?>
                <?php if($is_dropshipper == 0){ ?>
                <li>Your dropshipper account activated when you click on become a dropshipper.</li><br />
                    <a id="popup" class="inline-cta__button1 marketing-button marketing-button--small dropshipper_red segment-button ">Become a Dropshipper</a><br /><br />
                <?php } ?>
              <?php } else { ?>
                  <li>To Register please do the following</li><br />
                  <li>Register an account with www.wholesalebox.in</li><br />

                    <a href="javascript:;" class="inline-cta__button marketing-button marketing-button--small dropshipper_green segment-button " data-toggle="modal" data-target="#login_verify_popup" id="dropshipper_login_link">Sign UP</a><br /><br />
              <?php } ?>
              <li>You will receive an e-mail confirmation when your dropship account is activated.</li><br />
          </ul>
        </div>

        <div id="dropshipper_text_container">
          <h1>More about dropshipping?</h1>
          <ul  id="dropshipper_text_benefits">
              <li>If you want to know anything else, please drop us an e-mail at 
              <a href="<?php echo HTTP_SERVER; ?>contact-us"> info@wholesalebox.in </a><br>
              </li>
          </ul>
        </div>
        
        <div id="dropshipper_text_container">
          <h4>Note:</h4>
          <ul  id="dropshipper_note">
              <li>Promotional offers, such as 2% discount, Free Shipping, etc., are not valid on dropshipping orders.</li>
              <li> Dropshipping orders will be sent only by Air couriers. If the weight of the package exceeds 5 kg, Surface courier option is also available.</li>
              <li>No returns will be entertained on dropshipping orders. Only in the case of manufacturing defects or product description mismatch, returns request will be entertained. <strong>Returns will be on the sole discretion of Wholesalebox.</strong></li>
          </ul>
        </div>
	</div>
</div>
<?php echo $footer; ?>
<script type="text/javascript">
    $(document).ready(function(){
        $("#popup").click(function(){
          $.fancybox(
          '<h2 clsss="dropshipper_popup">Thank you</h2><p>We would review your request shortly and would get back to you.</p>',
            {
              'autoDimensions'  : false,
              'width'           : 350,
              'height'          : 'auto',
              'transitionIn'    : 'none',
              'transitionOut'   : 'none'
            }
          );     
          $.ajax({
            url : "index.php?route=account/dropshipper/updateDropshipper",
            type: "POST",
            success: function( data ) {
            //  alert(data);
            }
          });   
    });
  });
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