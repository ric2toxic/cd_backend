<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-seller-agreement" id="ms-submit-button" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>  
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
        <form action="<?php echo $action; ?>" onsubmit="return strip_text()" method="post" enctype="multipart/form-data" id="form-seller-agreement" class="form-horizontal">
            <div class="tab-content">
                <div class="tab-pane active" id="tab-general">
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo $entry_clause_type; ?></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="clause_type" value="<?php echo $clause_type; ?>" placeholder="<?php echo $entry_clause_type; ?>" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo $entry_clause_version; ?></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="clause_version" value="<?php echo $clause_version; ?>" placeholder="<?php echo $entry_clause_version; ?>" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo $entry_clause_status; ?></label>
                        <div class="col-sm-10">
                            <select name="clause_status" class="form-control">
                                <?php $statuses = array('-1' => '--SELECT--','1'=>'Active','0'=>'Inactive');?>
                                <?php if( $statuses ){ ?>
                                    <?php foreach( $statuses as $key => $values){ ?>
                                        <?php if($clause_status == $key){ $selected = "selected";}else{ $selected = '';}?>
                                        <option value="<?php echo $key; ?>" <?php echo $selected; ?>> <?php echo $values; ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-description"><?php echo $entry_clause_content; ?></label>
                        <div class="col-sm-10">
                          <textarea name="clause_content" placeholder="<?php echo $entry_clause_content; ?>" id="input-description"><?php echo $clause_content; ?></textarea>
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
<script type="text/javascript">
    $('#input-description').summernote({
        height: 300,
        onpaste: function (e) {
            var bufferText = ((e.originalEvent || e).clipboardData || window.clipboardData).getData('Text');
            e.preventDefault();
            document.execCommand('insertText', false, bufferText);
        }
    });

    function strip_text(){
        var cnt = $.parseHTML($('#input-description').val());
        if($(cnt).text().length == 0){
            $('#input-description').val("");
        }
    }
</script>

