<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <!--<div class="alert alert-info"><i class="fa fa-info-circle"> <?php //echo $dev_message; ?></i></div>-->
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-review" class="form-horizontal">
          <div class="well">
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-seller-code"><?php echo $entry_seller_code; ?></label>
                  <div class="col-sm-10">
                    <input type="text" name="seller_code" value="" placeholder="<?php echo $entry_seller_code; ?>" id="input-seller-code" class="form-control" />
                  </div>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-rating"><?php echo $entry_rating; ?></label>
                  <div class="col-sm-10">
                    <input type="text" name="rating" value="" placeholder="<?php echo $entry_rating; ?>" id="input-rating" class="form-control" />
                  </div>
                </div>
                <input type="button" value="<?php echo $text_add; ?>" class="btn btn-primary pull-right" id="add_review"/>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>
<script type="text/javascript">
  $('#add_review').click(function(){
    if(confirm('Are you sure to change rating for this seller ?')){
      $("#form-review").submit();
    }
  });
</script>