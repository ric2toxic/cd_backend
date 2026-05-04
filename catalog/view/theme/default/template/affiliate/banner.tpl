<?php echo $header; ?>
<div class="container">
    <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
    </ul>

    <div class="row"><?php echo $column_left; ?>
        <?php if ($column_left && $column_right) { ?>
        <?php $class = 'col-sm-6'; ?>
        <?php } elseif ($column_left || $column_right) { ?>
        <?php $class = 'col-sm-9'; ?>
        <?php } else { ?>
        <?php $class = 'col-sm-12'; ?>
        <?php } ?>

        <div id="content" class="<?php echo $class; ?> content_banner"><?php echo $content_top; ?>

            <?php $i = 1; ?>
            <?php $c = 0; ?>

            <?php foreach($images as $image){ ?>
            <span style="font-size: large; color: #151515"> <?php echo $text_banner; ?> : <?php echo $i++; ?></span style="fo"><br/>
            <span><?php echo $text_size; ?> : <?php echo $size[$c]; ?></span><a id="preview" href="<?php echo HTTP_SERVER; ?>/image/affiliate_banner/<?php echo $image; ?>" data-toggle="tooltip" ><span>Preview</span></a>
            <?php $c++; ?>
            <br/>
<textarea rows="5" cols="70">
<a href="<?php echo HTTP_SERVER; ?>?tracking=<?php echo $affiliate_code; ?>" alt="Wholesale Ladies Ethnic Wears"
title="Wholesale Ladies Ethnic Wears"><img src="<?php echo HTTP_SERVER; ?>/image/affiliate_banner/<?php echo $image; ?>"></a>
</textarea> <br /><br />
            <?php }  ?>
            <?php echo $content_bottom; ?></div>
        <?php echo $column_right; ?> </div>
</div>

<script type="text/javascript">
    $("a#preview").fancybox();
</script>

<?php echo $footer; ?>
