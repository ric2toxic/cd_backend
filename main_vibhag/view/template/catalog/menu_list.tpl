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
    <div class="pull-right col-sm-4">
      <form id="ms-website_store_change" class="ms-form form-horizontal">
        <?php if(!empty($storeid) && $count > 1){ ?>
          <div class="control-label">
            <div class="col-sm-5"><h4>Select Store</h4></div>
            <div class="col-sm-7">
              <select class ="form-control seller_store col-sm-6">
                <?php foreach ($stores as $value) { ?>
                <option  value="<?php echo $value['store_id']; ?>"
                <?php if($value['store_id'] == $select_seller_store_id){ ?> selected
                <?php } ?>
                >
                  <a><label class="control-label"><?php echo $value['name']; ?>
                      </label></a>
                </option>
                <?php } ?>
              </select>
            </div>
          </div>
        <?php } ?>
      </form>
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
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-menu">
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
                  <td class="text-left"><?php if ($sort == 'status') { ?>
                    <a href="<?php echo $sort_status; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_status; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_status; ?>"><?php echo $column_status; ?></a>
                    <?php } ?></td>
                  <td class="text-right"><?php echo $column_action; ?></td>
                </tr>
              </thead>
              <tbody>
                <?php if ($menus) { ?>
                <?php foreach ($menus as $menu) { ?>
                <tr>
                  <td class="text-center"><?php if (in_array($menu['id'], $selected)) { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $menu['id']; ?>" checked="checked" />
                    <?php } else { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $menu['id']; ?>" />
                    <?php } ?></td>
                  <td class="text-left"><?php echo $menu['name']; ?></td>
                  <td class="text-left"><?php echo $menu['status']; ?></td>
                  <td class="text-right"><a href="<?php echo $menu['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a></td>
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
<?php echo $footer; ?>
<script type="text/javascript">
  $(".seller_store").change(function () {
    var store_id  = $(this).val();
    $.ajax({
      url: 'index.php?route=seller/website-settings/change_seller_store',
      type: 'POST',
      dataType: 'html',
      data: {store_id: store_id},
      success: function () {
          window.location.reload();
          }
    });
  });
</script>