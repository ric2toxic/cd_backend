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
                            <label class="control-label" for="input-query"><?php echo $entry_orignal_url; ?></label>
                            <input type="text" name="filter_query" value="<?php echo $filter_query; ?>" placeholder="<?php echo $entry_orignal_url; ?>" id="input-query" class="form-control" />
                          </div>

                        </div>
                        <div class="col-sm-4">
                          <div class="form-group">
                            <label class="control-label" for="input-orignal_url"><?php echo $entry_slug; ?></label>
                            <input type="text" name="filter_keyword" value="<?php echo $filter_keyword; ?>" placeholder="<?php echo $entry_slug; ?>" id="input-keyword" class="form-control" />
                          </div>


                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="input-meta_title"><?php echo $entry_meta_title; ?></label>
                                <input type="text" name="filter_meta_title" value="<?php echo $filter_meta_title; ?>" placeholder="<?php echo $entry_meta_title; ?>" id="input-meta-title" class="form-control" />
                            </div>
                        </div>  
                    </div>  
                    <div class="row">  
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
                                    <td class="text-left"><?php echo $text_column_orignal_url; ?></td>
                                    <td class="text-left"><?php echo $text_column_slug; ?></td>
                                    <td class="text-left"><?php echo $text_column_url_type; ?></td>
                                    <td class="text-left"><?php echo $text_column_meta_title; ?></td>
                                    <td class="text-left"><?php echo $text_column_action; ?></td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    if(count($custom_urls) > 0){
                                        $i= $page_start_index;   
                                        foreach($custom_urls as $row){ ?>
                                <tr>
                                    <td class="text-left"><?php echo $i++; ?></td>
                                    <td class="text-left" style="word-break: break-all"><?php echo $row['query']; ?></td>
                                    <td class="text-left" style="word-break: break-all"><?php echo $row['keyword']; ?></td>
                                    <td class="text-left" style="word-break: break-all"><?php echo $row['url_type']; ?></td>
                                    <td class="text-left"><?php echo $row['meta_title']; ?></td>
                                    <td class="text-left">
                                            <a href="<?php echo $row['edit']; ?>" data-toggle="tooltip" title="" class="btn btn-primary btn-sm" data-original-title="Edit"><i class="fa fa-pencil"></i></a> 
                                            <a href="<?php echo $row['delete']; ?>" onclick="return confirm('are you sure you want to delete this?');" data-toggle="tooltip" title="" class="btn btn-primary btn-sm" data-original-title="Delete"><i class="fa fa-trash-o"></i></a> 
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
                <?php //if($custom_url_total > $filter_page_limit){ ?>               
                    <div class="row">
                        <div class="col-sm-8 text-left"><?php echo $pagination; ?></div> 
                        <div class="col-sm-4 text-right"><?php echo $results; ?></div>
                    </div>
                <?php //} ?>
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
$('.button-filter').on('click', function() {
        
    
	var url = 'index.php?route=digital_marketing/custom_url&token=<?php echo $token; ?>'; 
        
	var filter_query = $('input[name=\'filter_query\']').val();

	if (filter_query) {
		url += '&filter_query=' + encodeURIComponent(filter_query);
	}
        
	var filter_keyword = $('input[name=\'filter_keyword\']').val();

	if (filter_keyword) {
		url += '&filter_keyword=' + encodeURIComponent(filter_keyword);
	}

	
    
        var filter_meta_title = $('input[name=\'filter_meta_title\']').val();

	if (filter_meta_title) {
		url += '&filter_meta_title=' + encodeURIComponent(filter_meta_title);
	}
        
	var filter_page_limit = $('select[name=\'filter_page_limit\']').val();

        if (filter_page_limit != '*') {
          url += '&filter_page_limit=' + encodeURIComponent(filter_page_limit);
        }
        
    	location = url;
        
        
        
});
//--></script>
