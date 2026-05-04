<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
  	<div class="container-fluid">
      <div class="pull-right">
        <a href="javascript:;" data-toggle="tooltip" title="Add Application" class="btn btn-primary" id="add_application"><i class="fa fa-plus"></i></a>
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


    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
      </div>
      <div class="panel-body">
     
        <form id="form_filter">
         <div class="well">
            <div class="row">

            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-date-added">Lead Name</label>
                <input type="text" name="filter_lead_name" value="<?php echo $filter_lead_name; ?>" placeholder="Lead Name" id="input-name" class="form-control" />
                </div>

            </div>


             <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-date-added">Mobile Number</label>
                <input type="text" name="filter_mobile_number" value="<?php echo $filter_mobile_number; ?>" placeholder="Mobile Number" id="input-name" class="form-control" />
                </div>

            </div>
 
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-name">Sequence Number</label>
                <input type="text" name="filter_sequence_number" value="<?php echo $filter_sequence_number; ?>" placeholder="Sequence Number" id="input-name" class="form-control" />
              </div>
            </div>


            <div class="col-sm-11">
              <div class="form-group">
              <button type="reset" class="btn btn-default pull-right" style="display: block;margin-top: 22px;"><i class="fa fa-undo"></i> Reset</button>
              </div>
            </div>

              <div class="col-sm-1">
              <div class="form-group">
              <button type="button" id="button-filter" class="btn btn-primary pull-right" style="display: block;margin-top: 22px;"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
              </div>
            </div>

          </div>
        </div>
      </form>    
      </div>
      <!-- panel body closes -->
      <form action="" method="post" enctype="multipart/form-data" id="form-customer">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td class="text-left"><a>Sequence Number</a></td>
                   <td class="text-left"><a>Lead Name</a></td> 
                  <td class="text-left"><a>Business Name</a></td>
                  <td class="text-left"><a>Mobile Number</a></td>
                  <td class="text-left"><a>Following Date</a></td>
                   <td class="text-left"><a href="<?php echo $sort_created; ?>"  class="<?php echo strtolower($order); ?>">Created</a></td>
                  <td class="text-right"  style="width: 150px;"><a>Action</a></td>
                </tr>
              </thead>
              <tbody>
              <?php if ($dialer_list) { ?>
                <?php foreach ($dialer_list as $dialer) { ?>
                 <tr>
                  <td class="text-left"> <?php echo $dialer['sequence_number']; ?> </td>
                  <td class="text-left"> <?php echo $dialer['lead_name']; ?> </td>
                  <td class="text-left"> <?php echo $dialer['lead_business_name']; ?> </td>
                  <td class="text-left"> <?php echo $dialer['mobile_number']; ?> </td>
                  <td class="text-left"> <?php echo $dialer['lead_followup_date']; ?> </td>
                  <td class="text-left"> <?php echo $dialer['created']; ?> </td>
                  <td class="text-left"> 
                   <a href="<?php echo $dialer['credit_link']; ?>" target="_blank" class="btn btn-default">Credit Application</a>
                  </td>
                <?php } ?>
                <?php } else { ?>
                <tr>
                  <td class="text-center" colspan="9"><?php echo $text_no_results; ?></td>
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
   <!-- panel closed -->
  </div>
  <!-- container closed -->

<style>
.table-wrapper-scroll-y {
  display: block;
  max-height: 470px;
  overflow-y: auto;
  -ms-overflow-style: -ms-autohiding-scrollbar;
}
</style>

<script type="text/javascript"><!--


$('#button-filter').on('click', function() {
  url = 'index.php?route=sale/customer_credit_application/today_dialer_list&token=<?php echo $token; ?>';

  var filter_lead_id = $('input[name=\'filter_lead_id\']').val();


  var filter_mobile_number = $('input[name=\'filter_mobile_number\']').val();

  if (filter_mobile_number) {
    url += '&filter_mobile_number=' + encodeURIComponent(filter_mobile_number);
  }

  var filter_lead_name = $('input[name=\'filter_lead_name\']').val();

  if (filter_lead_name) {
    url += '&filter_lead_name=' + encodeURIComponent(filter_lead_name);
  }

  var filter_sequence_number = $('input[name=\'filter_sequence_number\']').val();

  if (filter_sequence_number) {
    url += '&filter_sequence_number=' + encodeURIComponent(filter_sequence_number);
  }


  location = url;
});
//--></script>

  <script type="text/javascript">
$('.date').datetimepicker({
  pickTime: false
});

$(document).ready(function(){

   $("#form_filter input, #form_filter select").keypress(function(e) {
    if(e.which == 13) {

        $("#button-filter").click();
    }
  });
});

</script></div>
<?php echo $footer; ?>

