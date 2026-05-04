<?php if (count($languages) > 1) { ?>
<div class="col-xs-12 languages_in_mobile">
  <span class="white_color change_lang"><i class="fa fa-language change_lang" aria-hidden="true"></i> <?php echo $text_change_language; ?></span>
  <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="language">
    <div class="dropdown-menu-language">
    <?php foreach ($languages as $language) { ?>
    <?php if($code == $language['code']){
          $class = "selected_language";
      }else{
          $class = "unselected_language";
      } ?>
    <div class="col-xs-6 nopadding language_text"><a id="topbar-<?php echo $language['code'];?>-select" class="white_color <?php echo $class; ?>" href="<?php echo $language['code']; ?>"><?php echo ucfirst($language['name']); ?></a></div>
      <?php } ?>
    </div>
    <input type="hidden" name="code" value="" />
    <input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
  </form>
</div>
<?php } ?>