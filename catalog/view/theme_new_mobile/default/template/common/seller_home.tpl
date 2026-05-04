<?php echo $header; ?>
<section>
    <div class="image_banner">
        <div class="text_manuf_con">
            <h2 class="text_manuf text_manuf1">Are you a manufacturer ?<?php // echo $text_areuamanu; ?></h2>
            <h2 class="text_manuf text_manuf2">A factory owner ?</h2>
            <h2 class="text_manuf text_manuf3">An importer  ?</h2>
            <div class="right_text">
            <div class="texr_authen">
                <i class="fa fa-check-circle li_fa font_size"></i>  No hassle. No Commission
            </div>
            <div class="texr_authen">
                <i class="fa fa-check-circle li_fa font_size"></i> Grow your business using our platform
            </div>
            <div class="texr_authen">
                <i class="fa fa-check-circle li_fa font_size"></i> Sell on wholesale basis on Wholesalebox
            </div>
            </div>
        </div>
    </div>

    <div class="benefit">
        <h3 class="text text_hts"><?php echo $benefit_mobile; ?></h3>
        <div class="arrow_ben"><i class="fa fa-hand-o-down d2"></i>
        </div>
        <?php $i = 1; ?>
        <ul>
        <?php foreach($benefits as $ben){ ?>
            <?php $i++; ?>
        <li class="list_bens"><?php echo $ben; ?></li>
        <?php } ?>
        </ul>
    </div>

    <div class="text_how_container">
        <h3 class="text_howtosell">How to Sell?</h3>
        <div class="arrow_ben"><i class="fa fa-hand-o-down d1"></i>
        </div>
        <ul>
    <?php foreach($how_to_sell as $hs){ ?>
            <li class="list_bens color_white_font"><?php echo $hs; ?></li>
    <?php } ?>
        </ul>
    </div>

</section>
<?php echo $footer; ?>