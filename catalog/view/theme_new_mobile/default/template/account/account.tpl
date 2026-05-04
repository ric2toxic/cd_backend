<?php echo $header; ?>
<div class="container">
  <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
  </ul>
  <?php if ($success) { ?>
  <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?></div>
  <?php } ?>
  <div class="row"><?php echo $column_left; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-sm-9'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>
    <div id="content" class="<?php echo $class; ?>"><?php echo $content_top; ?>
      <h1><?php echo $welcome; ?>: <?php echo $name; ?></h1>
      <ul class="list-unstyled">
        <li><a href="<?php echo $edit; ?>"><?php echo $text_edit; ?></a></li>
        <li><a href="<?php echo $password; ?>"><?php echo $text_password; ?></a></li>
        <li><a href="<?php echo $address; ?>"><?php echo $text_address; ?></a></li>
        <li><a href="<?php echo $wishlist; ?>"><?php echo $text_wishlist; ?></a></li>
        <li><a href="<?php echo $credit_application ?>">Credit Application</a></li>
      </ul>
      <div class="width_fifty">
        <h1><?php echo $text_credit; ?></h1>
        <span class="referral_text"><?php echo $text_credit_balance; ?> : <i class="fa fa-inr" style="font-size: 12px;"></i><?php echo $amount; ?></span><br />
        <ul class="list-unstyled">
          <li><a href="<?php echo $transaction; ?>"><?php echo $text_transaction; ?></a></li>
        </ul>
      </div>
      <h2><?php echo $text_my_orders; ?></h2>
      <ul class="list-unstyled">
        <li><a href="<?php echo $order; ?>"><?php echo $text_order; ?></a></li>
        <?php /* ?><li><a href="<?php echo $download; ?>"><?php echo $text_download; ?></a></li><?php */ ?>
        <?php if ($reward) { ?>
        <li><a href="<?php echo $reward; ?>"><?php echo $text_reward; ?></a></li>
        <?php } ?>
        <?php /* ?><li><a href="<?php echo $return; ?>"><?php echo $text_return; ?></a></li><?php */ ?>
        <li><a href="<?php echo $transaction; ?>"><?php echo $text_transaction; ?></a></li>
        <?php /* ?><li><a href="<?php echo $recurring; ?>"><?php echo $text_recurring; ?></a></li><?php */ ?>
      </ul>
      <div class="width_fifty">
        <span class="referral_text"><?php echo $text_referral ?></span>
        <span class="referral_code"> <?php echo $referral_code; ?> </span> <br />
        <span id="referral_copy" style="cursor: pointer; color: #133595;"><?php echo HTTP_SERVER; ?>index.php?route=account/register&ref=<?php echo $referral_code; ?></span>
      </div>
      <?php echo $content_bottom; ?></div>
    <?php echo $column_right; ?></div>
</div>
<?php echo $footer; ?>

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