<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-product" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-product" class="form-horizontal">
          <div class="row">
            <div class="col-sm-12">
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-word"><?php echo $text_word; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="word" value="<?php echo $word; ?>" placeholder="<?php echo $text_word; ?>" id="input-title" class="form-control" />
                </div>
              </div>
            </div>
            <div class="col-sm-12">
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-synonyms"><?php echo $text_synonyms; ?></label>
                <div class="col-sm-10">
                  <textarea name="word_synonyms" placeholder="<?php echo $text_synonyms; ?>" id="input-word-synonyms" class="form-control"><?php echo $word_synonyms; ?></textarea>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php echo $footer; ?>