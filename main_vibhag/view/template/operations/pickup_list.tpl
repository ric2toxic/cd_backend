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
                <?php } ?>
            </ul>
           

            <a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary pull-right">Add Pickup Boy</a>

             <a href="<?php echo $all_pickup_list; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary pull-right" style="margin-right: 10px;">Pick Up List</a>
        </div>
    </div>
    <div class="container-fluid">
        <?php if ($error_warning != '') { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i>
            <?php echo $error_warning; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <?php if ($success) { ?>
        <div class="alert alert-success"><i class="fa fa-check-circle"></i>
            <?php echo $success; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <?php if ($delete_success) { ?>
        <div class="alert alert-success"><i class="fa fa-check-circle"></i>
            <?php echo $delete_success; ?>
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
                            <label class="control-label" for="input-name"><?php echo $entry_name; ?></label>
                            <input type="text" name="filter_name" value="<?php echo $filter_name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
                          </div>

                        </div>
                        <div class="col-sm-4">
                          <div class="form-group">
                            <label class="control-label" for="input-price"><?php echo $entry_pickup_city_code ?></label>
                            <input type="text" name="filter_pickup_city_code" value="<?php echo $filter_pickup_city_code; ?>" placeholder="<?php echo $entry_pickup_city_code; ?>" id="input-price" class="form-control" />
                          </div>


                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="input-pickup_city"><?php echo $entry_pickup_city; ?></label>
                                <input type="text" name="filter_pickup_city" value="<?php echo $filter_pickup_city; ?>" placeholder="<?php echo $entry_pickup_city; ?>" id="input-pickup_city" class="form-control" />
                            </div>
                        </div>  
                    </div>  
                    <div class="row">  
                        <div class="col-sm-4">
                              <div class="form-group">
                                <label class="control-label" for="input-status"><?php echo $entry_status; ?></label>
                                <select name="filter_status" id="input-status" class="form-control">   
                                    <option value="*" <?php if(!isset($filter_status)) echo "selected"; ?>><?php echo $entry_select_status; ?></option> 
                                    <option value="1" <?php if(isset($filter_status) && $filter_status == 1) echo "selected"; ?>>Active</option>
                                    <option value="0" <?php if(isset($filter_status) && $filter_status == 0) echo "selected"; ?>>Deactive</option>
                                </select>

                              </div>

                        </div>
                        
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="input-seller"><?php echo $entry_seller; ?></label> 
                                <select name="filter_seller" id="input-seller" class="form-control">   
                                    <option value="*" <?php if(!isset($filter_seller)) echo "selected"; ?>><?php echo $entry_select_seller; ?></option> 
                                    <?php foreach($seller_list as $seller){ ?>
                                    <option <?php if($filter_seller == $seller['seller_id']) echo "selected"; ?> value="<?php echo $seller['seller_id']; ?>"><?php echo $seller['nickname']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div> 
                        
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="input-status"><?php echo $text_page_limit; ?></label>

                                <select name="filter_page_limit" id="input-status" class="form-control">
                                  <option value="*">--Select--</option>
                                  <?php if(isset($page_limit_array)){ ?>
                                  <?php foreach($page_limit_array as $page_limit_value){ ?>
                                  <?php
                                        if($page_limit_value == $filter_page_limit){
                                          $page_selected = 'selected';
                                        }else{
                                          $page_selected = '';
                                        }
                                       ?>
                                  <option value="<?php echo $page_limit_value; ?>" <?php echo $page_selected; ?>><?php echo $page_limit_value; ?></option>
                                  <?php } ?>
                                  <?php } ?>
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
                    <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-product">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <td class="text-left"><?php echo $text_column_serial_no; ?></td>
                                        <td class="text-left"><?php echo $text_column_name; ?></td>
                                        <td class="text-left"><?php echo $text_column_phone_no; ?></td>
                                        <td class="text-left"><?php echo $text_column_email; ?></td>
                                        <td class="text-left"><?php echo $text_column_pickup_city; ?></td>
                                        <td class="text-left"><?php echo $text_column_pickup_city_code; ?></td> 
                                        <td class="text-left"><?php echo $text_column_status; ?></td>
                                        <td class="text-left"><?php echo $text_column_action; ?></td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        if(count($pickers) > 0){
                                            $i= $page_start_index;   
                                            foreach($pickers as $row){ ?>
                                    <tr>
                                        <td class="text-left"><?php echo $i++; ?></td>
                                        <td class="text-left"><?php echo $row['first_name'] . ' ' . $row['last_name']; ?></td>
                                        <td class="text-left"><?php echo $row['phone_no']; ?></td>
                                        <td class="text-left"><?php echo $row['email']; ?></td>
                                        <td class="text-left"><?php echo $row['pickup_city']; ?></td>
                                        <td class="text-left"><?php echo $row['pickup_city_code']; ?></td>
                                        <td class="text-left"><?php 
                                        if($row['status'] == 1) { echo "Active"; } 
                                        else { echo "Deactive"; } ?></td>
                                        <td class="text-left">
                                                <a href="<?php echo $row['edit']; ?>" data-toggle="tooltip" title="" class="btn btn-primary btn-sm" data-original-title="Edit"><i class="fa fa-pencil"></i></a> 

                                                 <a href="<?php echo $row['delete']; ?>" data-toggle="tooltip" title="" class="btn btn-primary btn-sm" data-original-title="Delete"><i class="fa fa-trash-o"></i></a> 
                                        </td>
                                    </tr>
                                    <?php   } 
                                        }else{ ?>
                                        <tr>
                                        <td class="text-left" colspan="8"><p><?php echo $no_records; ?></p></td> 
                                        </tr>
                                    <?php    }
                                    ?>
                                </body>

                          </table>
                        </div>
                    </form> 
                    <?php //if($piker_total > $filter_page_limit){ ?>               
                        <div class="row">
                            <div class="col-sm-8 text-left"><?php echo $pagination; ?></div> 
                            <div class="col-sm-4 text-right"><?php echo $results; ?></div>
                        </div>
                    <?php //} ?>
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
$('.button-filter').on('click', function() {
        
    
	var url = 'index.php?route=operations/pickup&token=<?php echo $token; ?>'; 
        
	var filter_name = $('input[name=\'filter_name\']').val();

	if (filter_name) {
		url += '&filter_name=' + encodeURIComponent(filter_name);
	}
        
	var filter_pickup_city_code = $('input[name=\'filter_pickup_city_code\']').val();

	if (filter_pickup_city_code) {
		url += '&filter_pickup_city_code=' + encodeURIComponent(filter_pickup_city_code);
	}

	
    
        var filter_pickup_city = $('input[name=\'filter_pickup_city\']').val();

	if (filter_pickup_city) {
		url += '&filter_pickup_city=' + encodeURIComponent(filter_pickup_city);
	}
        
	

	var filter_status = $('select[name=\'filter_status\']').val();

	if (filter_status != '*') {
		url += '&filter_status=' + encodeURIComponent(filter_status);
	}
    
        var filter_seller = $('select[name=\'filter_seller\']').val();

	if (filter_seller != '*') {
		url += '&filter_seller=' + encodeURIComponent(filter_seller);
	}
    
    
    var filter_page_limit = $('select[name=\'filter_page_limit\']').val();

    if (filter_page_limit != '*') {
      url += '&filter_page_limit=' + encodeURIComponent(filter_page_limit);
    }
        
    	location = url;
        
        
        
});
//--></script>