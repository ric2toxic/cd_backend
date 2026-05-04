<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right"><a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
        <button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-geo-zone').submit() : false;"><i class="fa fa-trash-o"></i></button>
      </div>
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
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div style="height:60px; padding-top:12px; background-color:rgb(252,252,252); border-bottom: 1px solid rgb(232,232,232);">
        <h3 class="panel-title col-sm-4" style="font-size:16px; font-weight:500; margin-top:8px;"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
        <div class="col-sm-6 pull-right">
          <div class="col-sm-10" style="padding: 0;"><input style="border-radius:0; border-right: none;" placeholder="Search" class="form-control" id="search" name="search" value="<?php echo $search; ?>" type="text"></div>
          <div class="col-sm-1" style="height:35px; border:1px solid #ccc; background-color:white; border-left:none;"><a href="<?php echo $action; ?>" class="search"><i class="fa fa-search" style="color:black; font-size:22px; margin-top:5px; margin-left:-4px;"></i></a></div>
        </div>
      </div>
      <div class="panel-body">
        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-geo-zone">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
                  <td class="text-left"><?php if ($sort == 'name') { ?>
                    <a href="<?php echo $sort_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_name; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_name; ?>"><?php echo $column_name; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'description') { ?>
                    <a href="<?php echo $sort_description; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_description; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_description; ?>"><?php echo $column_description; ?></a>
                    <?php } ?></td>
                  <td class="text-right"><?php echo $column_action; ?></td>
                </tr>
              </thead>
              <tbody>
                <?php if ($geo_zones) { ?>
                <?php foreach ($geo_zones as $geo_zone) { ?>
                <tr>
                  <td class="text-center"><?php if (in_array($geo_zone['geo_zone_id'], $selected)) { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $geo_zone['geo_zone_id']; ?>" checked="checked" />
                    <?php } else { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $geo_zone['geo_zone_id']; ?>" />
                    <?php } ?></td>
                  <td class="text-left"><?php echo $geo_zone['name']; ?></td>
                  <td class="text-left"><?php echo $geo_zone['description']; ?></td>
                  <td class="text-right"><a href="<?php echo $geo_zone['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a></td>
                </tr>
                <?php } ?>
                <?php } else { ?>
                <tr>
                  <td class="text-center" colspan="4"><?php echo $text_no_results; ?></td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </form>
        <div class="row">
          <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-6 text-right"><?php echo $results; ?></div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  $(document).delegate('.search' , 'click', function () {
    $(this).attr('href', function() {
      return this.href + '&search='+$('#search').val();
    });
  });
</script>

<?php echo $footer; ?>