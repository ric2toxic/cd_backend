<?php echo $header; ?>
<section>
<div>
    <div class="image_banner"></div>

    <div class="container nopadding">
        <div class="row">
            <div class="col-sm-12">
                <div class="col-sm-4 float_right register_seller">
                    <h3><?php echo $text_reg_now; ?> </h3>

                <form id="myform" method="post" action="<?php echo $action ?>">
                    <fieldset>
                    <input type="text" name="name" id="name" class="in_style in_style_reg" placeholder="<?php echo $text_name; ?>" required="required"/><br /><br />
                    <input type="tel" name="email" id="email" class="in_style in_style_reg" placeholder="<?php echo $text_email; ?>" required="required"/><br /><br />
                    <input type="submit" name="submit" value="<?php echo $text_sign_up; ?>" class="submit_btn_reg">
                    </fieldset>
                </form>
            </div>
                <div class="col-sm-8 text_slogan">
                    <h1 class="text">
                        <?php echo $text_areuamanu; ?>
                    </h1>
                    <div class="col-sm-3 text_manufacture text_manufacture1">
                        <i class="fa fa-check-circle li_fa"></i>  <?php echo $text_important_1; ?>
                    </div>
                    <div class="col-sm-3 text_manufacture text_manufacture2">
                        <i class="fa fa-check-circle li_fa"></i> <?php echo $text_important_2; ?>
                    </div>
                    <div class="col-sm-3 text_manufacture text_manufacture3">
                        <i class="fa fa-check-circle li_fa"></i> <?php echo $text_important_3; ?>
                    </div>
                    <h2 class="color_white_font"><?php echo $text_important_4; ?> <br /> <?php echo $text_important_5; ?></h2>
                </div>
        </div>
            <div class="main_round" id="main_benefit">
                <h1 class="text text_hts"><?php echo $benefit; ?></h1>
                <?php $i = 1; ?>
                <?php foreach($benefits as $ben){ ?>
                <div class="col-sm-4 round_ben round_ben_<?php echo $i; ?>">
                    <?php $i++; ?>
                <div class="round_ben_in">
                <?php echo $ben; ?>
                </div>
            </div>
            <?php } ?>
             </div>

    </div>
</div>
    </div>
</section>
<section>
    <div>
    <div class="main_htosell" id="htosell">
        <h2 class="main_round text"><?php echo $text_hts; ?></h2>
        <div class="boxes_text container">
            <?php foreach($how_to_sell as $hs){ ?>
            <div class="col-sm-4 text_how"><?php echo $hs; ?></div>
            <?php } ?>
        </div>
    </div>

    </div>
</section>
<script>
    function manu() {
        //alert("adas");
        $( ".text_manufacture1" ).toggle( "bounce", { times: 3 }, 2000 );
        $( ".text_manufacture2" ).toggle( "bounce", { times: 5 }, 3000 );
        $( ".text_manufacture3" ).toggle( "bounce", { times: 8 }, 4000 );
        /* $('.text_manufacture1').fadeIn(3000);
         $('.text_manufacture2').fadeIn(4000);
         $('.text_manufacture3').fadeIn(5000);
         */
    }
    $(document).ready(function(){
        $(".text_manufacture").css({
            "min-width": "198px",
            "min-height": "100px"

        });
        manu();
    });
</script>
<script>
    $(document).ready(function(){
		var lbl_name = "<?php echo $text_name; ?>";
		var lbl_email = "<?php echo $text_email; ?>";
		
        $('#myform').validate({
            rules: {
                name: "required",
                email: { required: true, email: true }
            },
            messages: {
                name: "Please enter " + lbl_name,
                email: "Please enter " + lbl_email 
            },
            errorPlacement: function (error, element) {
                element.attr("placeholder", error.text());
            }
        });
    });
</script>


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
<?php echo $footer; ?>


