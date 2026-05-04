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
    <?php if (isset($error_warning)) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php }
      if(isset($success)) {
    ?>
    <div class="alert alert-success"><i class="fa fa-exclamation-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <i class="fa fa-list"></i>
        <?php echo $text_list; ?>
      </div>
      <div class="panel-body">
        <div class="row">
          <div class="container">
              <div class="col-md-12">
                  <div class="pull-left col-md-5">
                    <div>
                      <h3 class="upload-heading"><?php echo $text_upload_sheet; ?></h3>
                     </div>
                    <div>
                      <form action="" method="post" id="form-return" class="form-horizontal" enctype="multipart/form-data">
                        <input type="file" name="delhivery_upload" class="upload-form-input">
                        <button type="submit" form="form-return" name="submit" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
                       <form>
                     </div>
                  </div>
                  <div class="col-md-3 col-md-offset-2">
                    <span class="download_link" style="color: #1e91cf;"><?php echo $text_download_sample_file; ?></span>
                    <a href="<?php echo $download; ?>" data-toggle="tooltip" class="btn btn-default"><i class="fa fa-download" aria-hidden="true"></i></a>
                  </div>
                <div class="col-md-4 col-md-offset-1">
                  <?php if(isset($user["update_date"])) { ?>
                    <span><b><?php echo $text_uploaded_by; ?>: <?php echo $user["name"]; echo " on "; echo $user["update_date"] ?><b></span>
                  <?php } ?>
                </div>
              </div>
          </div>
        </div>
        <div class="clearfix">
          <div class="well filter-form">
            <div class="row">
              <div class="col-sm-12">
                <div class="form-group">
                  <form action="" method="post">
                    <label class="control-label" for="input-name"><?php echo $text_pincode?></label>
                    <input type="text" name="filter_pin_code"  placeholder="Pin Code" id="input-name" class="form-control" value="<?php echo isset($this->request->get['filter_pincode_no'])?$this->request->get['filter_pincode_no']:''?>">
                    <span style="font-size: 11px; color: #f56b6b">Use comma(,) to search muliple pincodes</span>
                  </form>
                </div>
                <button type="button" id="button-filter" class="btn btn-primary pull-right button-filter"><i class="fa fa-search"></i> <?php echo $text_filter; ?></button>
              </div>
            </div>
          </div>
        </div>
        <div class="table-responsive">
          <?php if(isset($filter_pincodes) && !empty($filter_pincodes)) { ?>
            <div class="pull-left" style="padding-bottom: 10px;">
              <button type="button" id="button-update-status" class="btn btn-primary" title="Toggle status"><i class="fa fa-edit"></i> Update Status</button>  
            </div>
          <?php } ?> 
          <table class="table table-bordered table-hover">
            <thead>
            <tr>
              <td class="text-left">
                <input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" />
              </td>
              <td class="text-left">
                <span style="color: #000;"><?php echo $text_pincode?></span>
              </td>
              <td class="text-left">
                <span style="color: #000;"><?php echo $text_prepaid?></span>
              </td>
              <td class="text-left">
                <span style="color: #000;"><?php echo $text_reverse_pickup?></span>
              </td>
              <td class="text-left">
                <span style="color: #000;"><?php echo $text_repl?></span>
              </td>
              <td class="text-left">
                <span style="color: #000;"><?php echo $text_cod?></span>
              </td>
              <td class="text-left">
                <span style="color: #000;"><?php echo $text_dispatch_center?></span>
              </td>
              <td class="text-left">
                <span style="color: #000;"><?php echo $text_city?></span>
              </td>
              <td class="text-left">
                <span style="color: #000;"><?php echo $text_state?></span>
              </td>
              <td class="text-left">
                <span style="color: #000;"><?php echo $text_state_code?></span>
              </td>
              <td class="text-left">
                <span style="color: #000;"><?php echo $text_sort_code?></span>
              </td>
              <td class="text-left">
                <span style="color: #000;"><?php echo $text_value_capping?></span>
              </td>
              <td class="text-left">
                <span style="color: #000;"><?php echo $text_status?></span>
              </td>
            </tr>
            </thead>
            <tbody>
            <?php if(isset($filter_pincodes) && !empty($filter_pincodes)) { ?>
              <?php foreach ($filter_pincodes as $filter_pincode) { ?>
              <tr>
                <td class="text-left">
                  <input class="selectedPincodes" type="checkbox" name="selected[]" value="<?php echo $filter_pincode['pincode']; ?>" />
                </td>
                <td class="text-left">
                  <span><?php echo $filter_pincode["pincode"] ?></span>
                </td>
                <td class="text-left">
                  <span><?php echo ($filter_pincode["prepaid"])?'Yes':'No' ?></span>
                </td>
                <td class="text-left">
                  <span><?php echo ($filter_pincode["pickup"])?'Yes':'No' ?></span>
                </td>
                <td class="text-left">
                  <span><?php echo ($filter_pincode["repl"])?'Yes':'No' ?></span>
                </td>
                <td class="text-left">
                  <span><?php echo ($filter_pincode["cod"])?'Yes':'No' ?></span>
                </td>
                <td class="text-left">
                  <span><?php echo $filter_pincode["dispatch_center"]?></span>
                </td>
                <td class="text-left">
                  <span><?php echo $filter_pincode["city"]?></span>
                </td>
                <td class="text-left">
                  <span><?php echo $filter_pincode["state"]?></span>
                </td>
                <td class="text-left">
                  <span><?php echo $filter_pincode["state_code"]?></span>
                </td>
                <td class="text-left">
                  <span><?php echo $filter_pincode["sort_code"]?></span>
                </td>
                <td class="text-left">
                  <span><?php echo $filter_pincode["value_capping"]?></span>
                </td>
                <td class="text-left">
                  <span><?php echo ($filter_pincode["status"]) ? 'Active' : 'Inactive' ?></span>
                </td>
              </tr>
            <?php
            }
              }
           ?>
            </tbody>
           </table>
        </div>
      </div>
    </div>
<?php echo $footer; ?>
<script type="text/javascript">

  $('#button-filter').on('click', function() {
    url = 'index.php?route=logistic/delhivery&token=<?php echo $token; ?>';

    var filter_pincode_no = $('input[name=\'filter_pin_code\']').val();

    if (filter_pincode_no) {
      url += '&filter_pincode_no=' + encodeURIComponent(filter_pincode_no);
    }
    location = url;
  });

  $("#button-update-status").on("click",function(){
    
    var selected_pincodes = [];
    $('input.selectedPincodes:checkbox:checked').each(function () {
        selected_pincodes.push($(this).val());
    });

    if(selected_pincodes.length > 0 ) {

      url = 'index.php?route=logistic/delhivery/update_status&token=<?php echo $token; ?>';

      var selected = selected_pincodes.join(",")

      url += '&selected=' + encodeURIComponent(selected);

      var filter_pincode_no = $('input[name=\'filter_pin_code\']').val();

      if (filter_pincode_no) {
        url += '&filter_pincode_no=' + encodeURIComponent(filter_pincode_no);
      }

      location = url;

    }

  })


</script>
