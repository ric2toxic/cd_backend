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
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-bar-chart"></i> <?php echo $text_credit_application; ?></h3>
      </div>
      <div class="panel-body">
    
      <div class="row">
       <div class="col-sm-12">
        <h3>#<?php echo $text_activated_cases;?> (<span style="font-size: 13px;"><a href="javascript:void(0)" class="refresh" data-section="activated_cases_data" data-status-filter=""><?php echo $button_refresh?></a></span>)</h3>
          <div id="activated_cases_data">    
            <?php echo $credit_activated_cases; ?>
         </div>
        </div>
      </div>  

      <p>&nbsp;</p>
      
      <div class="row">
        <div class="col-sm-12">
          <h3>#<?php echo $text_approved_cases;?> (<span style="font-size: 13px;"><a href="javascript:void(0)" class="refresh" data-section="approved_cases_data" data-status-filter=""><?php echo $button_refresh?></a></span>)</h3>
            <div id="approved_cases_data">
              <?php echo $credit_approved_cases;?>
            </div>
          </div>
      </div>

      <p>&nbsp;</p>

      <div class="row">
        <div class="col-sm-12">
          <h3>#<?php echo $text_application_records;?> (<span style="font-size: 13px;"><a href="javascript:void(0)" class="refresh application_records_data" data-section="application_records_data" data-status-filter=""><?php echo $button_refresh?></a></span>)</h3>
            <div id="application_records_data">
              <?php echo $application_records_data;?>
            </div>
          </div>
      </div>

      <p>&nbsp;</p>

      <div class="row">
        <div class="col-sm-12">
          <h3>#<?php echo $text_unser_process_application;?> (<span style="font-size: 13px;"><a href="javascript:void(0)" class="refresh under_process_applications" data-section="under_process_applications" data-status-filter=""><?php echo $button_refresh?></a></span>)</h3>
            <div id="under_process_applications">
              <?php echo $under_process_applications;?>
            </div>
          </div>
      </div>

      <p>&nbsp;</p>

    	</div>
     </div>
    </div>
  </div>
  </div>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.js"></script>

<?php echo $footer; ?>

<script type="text/javascript">
$(document).ready(function(){

function reload_credit_data(section, document_status)
{
    var ajax_loader = '<img src="<?php echo $image_path?>">';
    url = 'index.php?route=report/credit_applications/reload_credit_data&section='+section+'&document_status='+document_status+'&token=<?php echo $token; ?>';
    $.ajax({
            type: 'GET',
            url: url,
            dataType: 'json',
            beforeSend: function() {
              $('#'+section).html(ajax_loader);
            },
            success: function(response) {
               $.each(response,function(index, item){
                  if(index == 'success') {
                    $('#'+section).html(item);
                  }else if(index == 'error') {
                    $('#'+section).html(item);
                  }
               })
            },
            error: function(xhr, ajaxOptions, thrownError) { 
            var string = xhr.responseText.replace(/<b>/g,'');
                string = string.replace(/<\/b>/g,'');
                alert(string);
            }
        });
}

// Refresh the page  
  $(document).on('click','.refresh',function(){
    var section = $(this).data('section');
    var status_filter = $(this).data('status-filter');
      reload_credit_data(section,'');
  })
 //document status filter 
  $(document).on('change','.document-status-filter',function(){
      reload_credit_data('application_records_data',$(this).val());
  })


})

</script>