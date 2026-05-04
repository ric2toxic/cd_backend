<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
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
        <?php if (isset($success) && $success) { ?>
        <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
            </div>
            <div class="panel-body">
                <div class="well">
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="input-clause-type"><?php echo $entry_clause_type; ?></label>
                                <input type="text" name="filter_clause_type" value="<?php echo $filter_clause_type; ?>" placeholder="<?php echo $entry_clause_type; ?>" id="input-clause-type" class="form-control" />
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="input-clause-version"><?php echo $entry_clause_version; ?></label>

                                <input type="text" name="filter_clause_version" value="<?php echo $filter_clause_version; ?>" placeholder="<?php echo $entry_clause_version; ?>" id="input-clause-version" class="form-control" />
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="input-clause-status"><?php echo $entry_clause_status; ?></label>
                                <?php $status_value = array('-1'=>'--Select--', '1'=>'Active', '0'=>'Inactive');?>
                                <select class="form-control" name="filter_clause_status">
                                    <?php if($status_value){ ?>
                                        <?php foreach($status_value as $key => $values){ ?>
                                            <?php if($filter_clause_status == $key){ $selected = "selected";}else{ $selected = '';}?>
                                            <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $values?></option>
                                        <?php }?>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>    
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="input-date-added"><?php echo $entry_clause_date_added; ?></label>
                                <div class="input-group date">
                                    <input type="text" name="filter_clause_date_added" value="<?php echo $filter_clause_date_added; ?>"" placeholder="<?php echo $entry_clause_date_added; ?>" data-date-format="YYYY-MM-DD" id="input-date-added" class="form-control" />
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                                    </span>
                                </div>
                            </div>

                            <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
                        </div>
                    </div>
                </div>
                <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-customer">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" style="text-align: center" id="list-sellers">
                            <thead>
                            <tr>
                                <td class="text-left"> <?php echo $column_clause_type; ?></td>
                                <td class="text-center"><?php echo $column_clause_version; ?></td>
                                <td class="text-center"><?php echo $column_clause_status; ?></td>
                                <td class="text-left"><?php echo $column_clause_date_added; ?></td>
                                <td class="text-left"><?php echo $column_user; ?></td>
                                <td><?php echo $column_action; ?></td>
                            </tr>
                            </thead>
                            <tbody>
                                <?php if(isset($sellers_agreement)&& !empty($sellers_agreement)){ ?>
                                <?php foreach($sellers_agreement as $agreement_data){ ?>
                                <tr>
                                    <td class="text-left"><?php echo $agreement_data['clause_type']; ?></td>
                                    <td class="text-center"><?php echo $agreement_data['clause_version']; ?></td>
                                    <td class="text-center"><?php echo $agreement_data['status']; ?></td>
                                    <td class="text-left"><?php echo $agreement_data['date_added']; ?></td>
                                    <td class="text-left"><?php echo $agreement_data['user']; ?></td>
                                    <td class="text-left">
                                        <a href="<?php echo $agreement_data['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a>
                                    </td>
                                </tr>
                                <?php } ?>
                                <?php } else { ?>
                                <tr>
                                    <td class="text-center" colspan="8"><?php echo $text_no_results; ?></td>
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
<script src="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<link href="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css" type="text/css" rel="stylesheet" media="screen" />
<script type="text/javascript">
    <!--
    $('.date').datetimepicker({
        pickTime: false
    });
    //-->
</script>
<script type="text/javascript">
    $('#button-filter').click(function(){

        url = 'index.php?route=seller_agreement/seller_agreement&token=<?php echo $token; ?>';

        var filter_clause_type = $('input[name=\'filter_clause_type\']').val();

        if(filter_clause_type){
            url += '&filter_clause_type=' + encodeURIComponent(filter_clause_type);
        }

        var filter_clause_version = $('input[name=\'filter_clause_version\']').val();

        if(filter_clause_version){
            url += '&filter_clause_version=' + encodeURIComponent(filter_clause_version);
        }

        var filter_clause_status = $('select[name=\'filter_clause_status\']').val();

        //if(filter_clause_status != -1){
            url += '&filter_clause_status=' + encodeURIComponent(filter_clause_status);
        //}


        var filter_clause_date_added = $('input[name=\'filter_clause_date_added\']').val();

        if(filter_clause_date_added){
            url += '&filter_clause_date_added=' + encodeURIComponent(filter_clause_date_added);
        }
        
        location = url;

    });

    $('input, select').on('keypress',function(e){
        if (e.keyCode == 13) {
            $('#button-filter').trigger('click');
        }
    });
</script>
