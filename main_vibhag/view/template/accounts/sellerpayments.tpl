<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <h1><?php echo $heading_sellerpayment; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>

  <div class="panel panel-default">
    <div class="container-fluid">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $heading_sellerpayment_list; ?></h3>
        </div>
        
    </div>
  </div>
</div>
<?php echo $footer; ?> 