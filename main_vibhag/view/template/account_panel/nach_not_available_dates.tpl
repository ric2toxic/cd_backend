<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <h1><?php echo $heading;?></h1>
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
        <h2 class="panel-title"><i class="fa fa-list"></i> <?php echo $heading;?></h2>
      </div>
      <div class="panel-body">
      <div>
        <label class="control-label">Add Bank Holiday Date</label>
      </div>
      <div class="col-sm-12"> 
      <form name="add-form" id="add-form" method="post">
        <div class="add-not-available-dates well" style="padding: 10px;float: left;">
          <div class="">
            <div class="col-sm-4 no-padding not_available_dates">
              <div class="form-group">
                <div class="input-group add-date" id="not_available_date_div">
                    <input class="form-control"
                           name="holiday_date[]"
                           type="text"
                           value="<?php echo $filter_date_from; ?>"
                           placeholder="YYYY-MM-DD"
                           data-date-format="YYYY-MM-DD"
                           type="text" />
                    <span class="input-group-btn">
                      <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                    </span>
                </div>
              </div>
            </div>
            <div class="col-sm-2">
              <button type="button" class="btn btn-primary" id="add-btn" name="add-btn"><i class="fa fa-plus"></i></button>
            </div>
            <div class="col-sm-6">
            <div class="form-group required">
              <label class="control-label required" for="filter_date_from" >Remarks</label>
              <div class="input-group">
                  <textarea placeholder="Remarks" id="remark" name="remark" class="form-control" cols="30"></textarea>
              </div>
            </div>
            </div>
          </div>
          <div class="pull-right">
            <button type="button" id="save-btn" class="btn btn-primary" name="save-btn"><i class="fa fa-save"></i> Save</button>
          </div>
        </div>
      </form>
      </div><br>
      <div class="col-sm-12">
      <form method="post" name="tentative-nach-form" style="float: left" >
        <div class="well">
          <div class="row">
              <div class="col-sm-3">
                <div class="form-group">
                  <label class="control-label" for="filter_date_from"><?php echo $column_date_from; ?></label>
                  <div class="input-group date" id="filter_date_from_div">
                      <input class="form-control"
                             name="filter_date_from"
                             id="filter_date_from"
                             type="text"
                             value="<?php echo $filter_date_from; ?>"
                             placeholder="YYYY-MM-DD"
                             data-date-format="YYYY-MM-DD"
                             type="text" />
                      <span class="input-group-btn">
                        <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                      </span>
                  </div>
                </div>
              </div>
              <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="filter_date_to"><?php echo $column_date_to; ?></label>
                <div class="input-group date" id="filter_date_to_div">
                    <input class="form-control"
                           name="filter_date_to"
                           id="filter_date_to"
                           type="text"
                           value="<?php echo $filter_date_to; ?>"
                           placeholder="YYYY-MM-DD"
                           data-date-format="YYYY-MM-DD"
                           type="text" />
                    <span class="input-group-btn">
                      <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                    </span>
                </div>
              </div>
              </div>
              <div class="col-sm-3">
                <div class="form-group">
                  <label class="control-label" for="filter_month">Month</label>
                  <select name="filter_month" id="filter_month" class="form-control" aa>
                    <option value="">Select</option>
                    <?php foreach($months as $key => $month){ ?>
                    <?php $str = '';?>
                    <?php if($filter_month == $key) { $str = 'selected="selected"'; } ?>
                    <option value="<?php echo $key; ?>" <?php echo $str; ?> ><?php echo $month; ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="col-sm-3">
                <div class="form-group">
                  <label class="control-label" for="filter_year">Year</label>
                  <input class="form-control" name="filter_year" id="filter_year" type="text" value="<?php echo $filter_year; ?>" placeholder="Year" />
                </div>
              <button type="submit" id="button-filter" class="btn btn-primary pull-right" name="button-filter"><i class="fa fa-search"></i> <?php echo $entry_filter; ?></button>
            </div>
          </div>
        </div>
        </form>
      </div>
          <div class="col-sm-12" style="float: left;">
          <?php if (!empty($not_nach_dates) ) { ?>
            <table class="table table-bordered table-hover">
              <thead>
                <tr class="text-left">
                  <th>Holiday Date</th>
                  <th>Day Of Week</th>
                  <th>Remark</th>
                  <th>User Name</th>
                  <th>Date Added</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                  foreach ($not_nach_dates as $nach_date) { 
                ?>
                <tr class="text-left">
                  <td><?php echo $nach_date['holiday_date'] ?? ''; ?></td>
                  <td><?php echo date('l', strtotime($nach_date['holiday_date']) ); ?></td>
                  <td><?php echo $nach_date['remark'] ?? ''; ?></td>
                  <td><?php echo $nach_date['user_name'] ?? ''; ?></td>
                  <td><?php echo $nach_date['date_added'] ?? ''; ?></td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
            <?php } else { ?>
                <div><?php echo $text_no_results; ?></div>
            <?php } ?>
          </div>
        
        <div class="row">
          <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-6 text-right"><?php echo $results; ?></div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>
<style type="text/css">
td { white-space: nowrap; }
.add-not-available-dates {
    border: solid 1px;
    padding: 5px;
    background-color: #f2f4f7;
}
.save_btn
{
  float: left;
  margin-top: 10px
}

</style>
<script type="text/javascript">
  var token = '<?php echo $token; ?>';

  $('#add-btn').click(function() {
    var html = '<div class="form-group"> <div class="input-group add-date"><input class="form-control" name="holiday_date[]" type="text" placeholder="YYYY-MM-DD"  data-date-format="YYYY-MM-DD" type="text" /> <span class="input-group-btn"> <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button> </span> </div> </div>';
    $('.not_available_dates').append(html);

    $('.add-date').datetimepicker();
  });

  $(document).ready(function(){
    $('.add-date').datetimepicker({
      minDate: new Date(), // Current date i.e. today validation
      pickTime: false
    });
    $('.date').datetimepicker({
      pickTime: false
    });
  });

  $('#save-btn').click(function(){
    var remarks = $('#remark').val();
    if(remarks.length <= 0){
      alert('Remark must be entered.');
      return false;
    }
    $.ajax({
      type : 'POST',
      url : 'index.php?route=account_panel/nach_not_available_dates/addDates&token=<?php echo $token; ?>',
      data : $('#add-form').serialize(),
      success: function(json) {
        json = $.trim(json);
        if(json.length > 0){
          alert(json);
        }
        
        location.reload();
      }
    });
  });

</script>
