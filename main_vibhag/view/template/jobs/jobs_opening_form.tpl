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
                <label class="col-sm-2 control-label" for="input-sku"><?php echo $text_title; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="job_title" value="<?php echo $title; ?>" placeholder="<?php echo $text_title; ?>" id="input-title" class="form-control" />
                </div>
              </div>
            </div>
            <div class="col-sm-12">
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-sku"><?php echo $text_job_type; ?></label>
                <div class="col-sm-10">
                  <?php
                    $jobs_type = array(
                      'full-time'=>'Full Time','part-time'=>'Part Time'
                    );
                  ?>
                  <select name="job_type" id="input-jobs-type" class="form-control">
                    <option value="" ><?php echo $text_select; ?></option>
                    <?php
                      foreach($jobs_type as $key => $value){
                        if($key == $job_type){
                          $selected = 'selected';
                        }else{
                          $selected = '';
                        }
                    ?>
                        <option value="<?php echo $key; ?>" <?php echo $selected; ?> ><?php echo $value; ?></option>
                    <?php
                      }
                    ?>
                  </select>
                </div>
              </div>
            </div>
            <div class="col-sm-12">
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-sku"><?php echo $text_description; ?></label>
                <div class="col-sm-10">
                  <textarea name="job_description" placeholder="<?php echo $text_description; ?>" id="input-jobs-description" class="form-control"><?php echo $description; ?></textarea>
                </div>
              </div>
            </div>
            <div class="col-sm-12">
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-sku"><?php echo $text_start_date; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="job_start_date" value="<?php echo $start_date; ?>" placeholder="<?php echo $text_start_date; ?>" id="input-job-start-date" class="form-control" />
                </div>
              </div>
            </div>
            <div class="col-sm-12">
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-sku"><?php echo $text_close_date; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="job_close_date" value="<?php echo $end_date; ?>" placeholder="<?php echo $text_close_date; ?>" id="input-job-close-date" class="form-control" />
                </div>
              </div>
            </div>
            <div class="col-sm-12">
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-sku"><?php echo $text_location; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="job_location" value="<?php echo $location; ?>" placeholder="<?php echo $text_location; ?>" id="input-job-location" class="form-control" />
                </div>
              </div>
            </div>
            <div class="col-sm-12">
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-sku"><?php echo $text_email_to; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="job_email_to" value="<?php echo $email_to; ?>" placeholder="<?php echo $text_email_to; ?>" id="input-job-emai-to" class="form-control" />
                </div>
              </div>
            </div>
            <div class="col-sm-12">
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-sku"><?php echo $text_input_image; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="job_input_image" value="<?php echo $job_icon; ?>" placeholder="<?php echo $text_input_image; ?>" id="input-job-input-image" class="form-control" />
                </div>
              </div>
            </div>
            <div class="col-sm-12">
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-sku"><?php echo $text_status; ?></label>
                <div class="col-sm-10">
                  <select name="job_status"  id="input-job-status" class="form-control">
                    <option>--select--</option>
                    <?php
                      $status_value = array('Disable','Enable');
                        foreach($status_value as $key=>$value){
                          if($key==$status){
                              $selected = 'Selected';
                          }else{
                            $selected = '';
                          }
                    ?>
                            <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
                    <?php
                    }
                  ?>
                  </select>
                  <!--<input type="text" name="job_status" value="<?php echo $status; ?>" placeholder="<?php echo $text_status; ?>" id="input-job-status" class="form-control" />-->
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  </div>
<script type="text/javascript">
  $(document).ready(function () {
    $(document).delegate('#input-job-start-date', 'focus', function () {
      $(this).datetimepicker({
        format: 'YYYY-MM-DD hh:mm:ss A'

      });
    });
    $(document).delegate('#input-job-close-date', 'focus', function () {
      $(this).datetimepicker({
        format: 'YYYY-MM-DD hh:mm:ss A'
      });
    });
  });
</script>

<script>
  $('#input-jobs-description').summernote({height:300});
</script>
<?php echo $footer; ?>