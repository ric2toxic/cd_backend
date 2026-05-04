<?php if($heading_title){ ?>
<h3 class="aligncenter"><?php echo $heading_title; ?></h3>
<?php } ?>

<div class="row" id="testimonials">

    <?php foreach ($reviews as $review) { ?>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="horizontal-sreview transition">
            <div class="caption review-caption">

                <p><?php echo $review['text']; ?></p>
                <span class="review-author"><?php echo $review['author']; ?></span>
                <?php /* ?>
                <span class="review-date-added"><?php echo $review['date_added']; ?></span>
                <div class="rating">
                    <?php for ($i = 1; $i <= 5; $i++) { ?>
                    <?php if ($review['rating'] < $i) { ?>
                <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"
                                             style='color: #FC0;'></i></span>
                    <?php } else { ?>
                <span class="fa fa-stack"><i class="fa fa-star fa-stack-2x"
                                             style='color: #FC0;'></i><i
                            class="fa fa-star-o fa-stack-2x"
                            style='color: #E69500;'></i></span>
                    <?php } ?>
                    <?php } ?>
                </div>
                <?php */ ?>
            </div>
        </div>
    </div>
    <?php } ?>
    <?php if($button_all){ ?>
    <div class="horizontal-sreview-all"><a href="<?php echo $keyword; ?>"><?php echo $button_all_text; ?></a></div>
    <?php } ?>
</div>
