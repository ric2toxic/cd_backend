<?php echo $header; ?>
<?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <h1>
                <?php echo $heading_title; ?>
            </h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li>
                    <a href="<?php echo $breadcrumb['href']; ?>">
                        <?php echo $breadcrumb['text']; ?>
                    </a>
                </li>
                <?php }  ?> 
            </ul>
            <a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary pull-right"><i class="fa fa-plus"></i></a>
        </div>
    </div>
    <div class="container-fluid">
        <?php if ($error_warning != '') { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i>
            <?php echo $error_warning; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>

        <?php if ($success != '') { ?>
        <div class="alert alert-success"><i class="fa fa-check-circle"></i>
            <?php echo $success; ?>
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
                        <div class="col-sm-4">
                          <div class="form-group">
                            <label class="control-label" for="input-query"><?php echo $entry_cluster_name; ?></label>
                            <input type="text" name="filter_cluster" value="<?php echo $filter_cluster ?? ''; ?>" placeholder="<?php echo $entry_cluster_name; ?>" id="input-query" class="form-control" />
                          </div>
                        </div>
                        <div class="col-sm-4">
                          <div class="form-group">
                            <label class="control-label" for="input-query"><?php echo $entry_seller; ?></label>
                            <input type="text" name="filter_seller" value="<?php echo $filter_seller ?? ''; ?>" placeholder="<?php echo $entry_seller; ?>" id="input-query" class="form-control" />
                          </div>
                        </div>
                        <div class="col-sm-4">
                          <div class="form-group">
                            <label class="control-label" for="input-orignal_url"><?php echo $entry_category; ?></label>
                            <select name="filter_category" class="form-control"> 
                                <option value="0"><?php echo $entry_filter_category?></option> 
                                <?php 
                                    if(!empty($category_filter_list)){
                                        foreach($category_filter_list as $row){ ?>    
                                        <option <?php if(!empty($filter_category) && $filter_category == $row['category_id']) echo 'selected'; ?> value="<?php echo $row['category_id'] ?>"><?php echo $row['name'] ?></option>
                                <?php   }
                                    }
                                ?>    
                            </select>
                          </div>
                        </div>
                    </div>  
                    <div class="row">
                        <div class="col-sm-12">
                        <button type="button" id="button-filter" class="btn btn-primary pull-right button-filter"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
                        </div>
                    </div>
                </div>

                        <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <td class="text-left"><?php echo $column_id; ?></td>
                                    <td class="text-left"><?php echo $column_category; ?></td>
                                    <td class="text-left"><?php echo $column_cluster_name; ?></td>
                                    <td class="text-left"><?php echo $column_seller; ?></td>
                                    <td class="text-left"><?php echo $column_action; ?></td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    if(count($similar_sellers) > 0){
                                        foreach($similar_sellers as $row){ ?>
                                <tr>
                                    <td class="text-left" style="word-break: break-all; width:10%;"><?php echo $row['similar_seller_cluster_id']; ?></td>
                                    <td class="text-left" style="word-break: break-all; width:20%;"><?php echo $row['category_name']; ?></td>
                                    <td class="text-left" style="word-break: break-all; width:20%;"><?php echo $row['cluster_name']; ?></td>
                                    <td class="text-left" style="word-break: break-all; "><?php echo $row['seller_name']; ?></td>
                                    <td class="text-left" style="width:10%">
                                            <a href="<?php echo $row['edit']; ?>" data-toggle="tooltip" title="" class="btn btn-primary btn-sm" data-original-title="Edit"><i class="fa fa-pencil"></i></a> 
                                            <a href="<?php echo $row['delete']; ?>" onclick="return confirm('are you sure you want to delete this?');" data-toggle="tooltip" title="" class="btn btn-primary btn-sm" data-original-title="Delete"><i class="fa fa-trash-o"></i></a> 
                                    </td>
                                </tr>
                                <?php   } 
                                    }else{ ?>
                                    <tr>
                                    <td class="text-left" colspan="5"><p><?php echo $text_no_records; ?></p></td> 
                                    </tr>
                                <?php    }
                                ?>
                            </body>
                            
                      </table>
                    </div>

                    <div class="row">
                        <div class="col-sm-12"><?php echo $pagination; ?></div> 
                    </div>
           </div>  
                
            </div>
        </div>
    </div>
</div>
</script>
<style type="text/css">
    .suborder_table table tr td {
        padding: 4px !important;
    }
</style>
<script type="text/javascript">
    
    function reset_filter(){
            
        var url = 'index.php?route=sellers/similar_sellers&token=<?php echo $token; ?>'; 
        
        location = url;
    }
    
    
$('.button-filter').on('click', function() {
        
	var url = 'index.php?route=sellers/similar_sellers&token=<?php echo $token; ?>'; 
        
	var filter_seller = $('input[name=\'filter_seller\']').val();

	if (filter_seller.trim() != '') {
		url += '&filter_seller=' + encodeURIComponent(filter_seller.trim()); 
	}
        
	var filter_category = $('select[name=\'filter_category\']').val();
    if (filter_category > 0) {
		url += '&filter_category=' + encodeURIComponent(filter_category); 
	}

    var filter_cluster = $('input[name=\'filter_cluster\']').val();

	if (filter_cluster.trim() != '') {
		url += '&filter_cluster=' + encodeURIComponent(filter_cluster.trim());
	}
        
	location = url;
        
});

$(document).on('keypress',function(e) {
    $(".form-control").on('keyup', function (e) {
        if (e.keyCode === 13) {
            $('#button-filter').trigger('click');
        }
    });
});


//--></script>
