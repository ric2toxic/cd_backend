<?php if (count($languages) > 1) { ?>
    <div class="pull-left" style="margin-bottom:-6px;">
        <div class="btn-group language_btn">
            <button class="btn btn-link dropdown-toggle" data-toggle="dropdown">
                <?php foreach ($languages as $language) { ?>
                    <?php if ($language['code'] == $code) { ?>
                        <img src="image/flags/<?php echo $language['image']; ?>" alt="<?php echo $language['name']; ?>" title="<?php echo $language['name']; ?>">
                    <?php } ?>
                <?php } ?>
                <span class="hidden-xs hidden-sm hidden-md"><?php echo $text_language; ?></span> <i class="fa fa-caret-down"></i></button>
            <ul class="dropdown-menu">
                <?php foreach ($languages as $language) { ?>
                    <li><a href="<?php echo $this->url->link('common/language/language', 'code='.$language['code'].'&redirect='.$redirect, 'SSL');?>" id="topbar-<?php echo $language['code'];?>-select"><img src="image/flags/<?php echo $language['image']; ?>" alt="<?php echo $language['name']; ?>" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
<?php } ?>