<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <h1><?php echo 'Account Panel'//$page_title; ?></h1>
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
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo 'Ledger Creation'; ?></h3>
      </div>
      <div class="panel-body">
        <div class="well">
           <!--form action="" method="get" id="filter_form"-->
               <input type="hidden" name="route" value="<?php echo $route; ?>">
               <input type="hidden" name="token" value="<?php echo $token; ?>">
               <input type="hidden" name="ledgerA"  class="ledgerA" value="" required> </span> <!-- it used only in Ajax to update in ledger table-->
                <div class="row">

                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label" for="nickname">Ledger Name</label>
                            <!--input type="text" class="form-control" id="ledger" placeholder="Ledger Name" name="ledger" /-->
                            <input type="text" class="form-control ledger" placeholder="Ledger Name" name="ledger" />
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                          <label class="control-label" for="input-store_date">Group Name</label>

                          <select name="group_id" id="group_id">
                              <option value="">--Select Group--</option>
                              <?php
        
                                if(isset( $groups ) ) {
                                  foreach($groups as $group)
                                  {
                                  ?>
                                      <option value="<?php echo $group['group_id'] ?>"><?php echo $group['group_name']?></option>
                                 <?php  
                                  }
                                }
                                ?>
                          </select>           
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label" for="nickname">Opening Balance</label>

                            <input type="text" class="form-control" id="opening_balance" placeholder="Opening Balance" name="opening_balance" />
                            <select name="drcr" id="drcr">
                                <option value="">--Select--</option>
                                <option value="Dr">Dr</option>
                                <option value="Cr">Cr</option>
                            </select>                            
                        </div>
                       
                    </div>                    
                    <div class="col-sm-3">
                        <div class="form-group">
                            <br><br><br><br>
                            <button type="button" form="filter_form" id="button-filter" class="addLedger"> <?php echo 'Save';//echo $button_filter; ?></button>
                        </div>
                    </div>
                </div>
            <!--/form-->
        </div>

        <h3>Filter</h3>
        <div class="well">
          <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                  <label class="control-label" for="input-name"><?php echo "Ledger Name"; ?></label>
                  <input type="text" name="filter_ledger_name" value="<?php echo $filter_ledger_name; ?>" placeholder="<?php echo $filter_ledger_name; ?>" id="input-name" class="form-control" />
                </div>
            </div>

            <div class="col-sm-4">
                <div class="form-group">

                      <button type="button" id="button-filter" class="btn btn-primary pull-right button-filter"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
                </div>
            </div>
          </div>
        </div>

      <div class="well">
      <div id="page-wrapper">
        <div class="table-responsive">
          <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead>
              <tr>
                  <th>Sr.No.</th>
                  <th>Ledger</th>
                  <th>Group Name</th>
                  <th>Opening Balance</th>
                  <!--th>Status</th-->
                  <!--th>Action</th-->
              </tr>
            </thead>         
            <tbody>
              <?php
              $i = 1;
                if(isset( $ledgers ) ) {                
                foreach( $ledgers as $key => $ledger ) {

                    $status = $ledger['status'];
                    
                    $status_text = "";
                    if ($status == 1 ) {
                      $status_text  = "Enabled";
                    }
                    if ($status == 0 ) {
                      
                      $status_text = "Disabled";
                    }
                
                $ledger_id = $ledger['ledger_id'] ;
                $ledger_name = $ledger['ledger_name'];
                $group_id = $ledger['group_id'];
                $group_name = $ledger['group_name'];
                $opening_balance = $ledger['opening_balance'];
                $drcr = $ledger['drcr'];
                $op_bal = $ledger['op_bal'];
                $user_id = $ledger['user_id'];
                $date_created = $ledger['date_created'];
                $date_modified = $ledger['date_modified'];
                $status = $ledger['status'];
              ?>
              <tr>
                <td><?php echo $i ?></td>

                <td><?php echo $ledger_name ?></td>
                <td><?php echo $group_name ?></td>
                <td><?php echo $op_bal ?></td>
                <!--td><?php //echo $status_text ?></td-->
                <!--td>
                  <button class="btn btn-warning btnShowLedger" name="" value="<?php //echo $ledger['ledger_id'];?>" group_id="<?php //echo $ledger['group_id'];?>" type="button">Edit</button>
                  <button class="btn btn-danger btnDeleteLedger" name="" value="<?php //echo $ledger['ledger_id'];?>" group_id="<?php //echo $ledger['group_id'];?>" type="button">Delete</button>
                  <!--a href="<?php //echo $ledger['delete_url']; ?>" class="btn btn-danger">Delete </a-->
                </td-->

              </tr>
              <?php
              $i++;
                }
                }
              ?>
            </tbody>
          </table>
        </div>
        <div>
          <?php //echo $pagination ?>
        </div>
      </div>
    <!-- /.container-fluid -->
</div>
</div>
                  
        
        <div class="row">
          <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-6 text-right"><?php echo $results; ?></div>
        </div>
      </div>
    </div>
  </div>
<a href="#" class="scrollToTop">Top</a>
</div>
<?php echo $footer; ?>
<style>
.scrollToTop{
  display: none;
  position: fixed;
  bottom: 20px;
  right: 30px;
  z-index: 99;
  border: none;
  outline: none;
  background-color: red;
  color: white;
  cursor: pointer;
  padding: 15px;
  border-radius: 10px;
}

.scrollToTop:hover{
  text-decoration:none;
}
</style>

<script>
$(document).ready(function(){
  
  //Check to see if the window is top if not then display button
  $(window).scroll(function(){
    if ($(this).scrollTop() > 100) {
      $('.scrollToTop').fadeIn();
    } else {
      $('.scrollToTop').fadeOut();
    }
  });
  
  //Click event to scroll to top
  $('.scrollToTop').click(function(){
    $('html, body').animate({scrollTop : 0},800);
    return false;
  });
  
});
</script>
<style>
.show_product_row + .child{
    display: table-row!important;
}
</style>
<script type="text/javascript">
$('.date').datetimepicker({
      pickTime: false
    });
$('.toggleclass').click(function(){
    if($(this).prop('checked')){
        $(this).parents('tr').addClass('show_product_row bg-success');
    }
    else{
        $(this).parents('tr').removeClass('show_product_row bg-success');
    }

});
$('#seller_id').on('keyup',function(){
    if($(this).val().trim().length == 0){
        $('input[name="filter_seller_id"]').val(0);
    }
});
    
  </script>



<script>
  jQuery('.addLedger').click( function(){
    jQuery('.ajaXloader').fadeIn(100);

    var ledgerA = $(".ledgerA").val();
    //var ledger = $("#ledger").val();
    var ledger = $(".ledger").val();
    var group_id = $("#group_id").val();
    var opening_balance = $("#opening_balance").val();
    var drcr = $("#drcr").val();

    jQuery.ajax(
    {
      url: 'index.php?route=account_panel/ledger/addLedgerAjax&token=<?php echo $token; ?>',
      type: "POST",
      data : {ledgerA:ledgerA, ledger:ledger, group_id:group_id, opening_balance:opening_balance, drcr:drcr},
      ContentType:"application/json",
      async:false,
      success: function(response){
    
        var data = $.parseJSON(response);
        var status = data.status ;
        //alert(status);exit();
        var msg;
        
        if( status == 1 ) {
          jQuery('.ajaXloader').fadeOut(100);
          msg = "Ledger Saved Successfully";
          alert(msg);
          location.href = 'index.php?route=account_panel/ledger/&token=<?php echo $token; ?>';
          //location.reload();
        }
        else if( status == 2 ) {
          
          msg = "Ledger Updated Successfully";
          alert(msg);
          location.reload();
        }
        else if( status == 22 ) {
          
          msg = "You can't update ledger";
          alert(msg);
          location.reload();
        }        
        else if( status == 3 ) {
          
          msg = "Please fill ledger Name";
          alert(msg);
        }
        else if( status == 4 ) {
          
          msg = "Please Select Group";
          alert(msg);
        }
        else if( status == 44 ) {
          
          msg = "Please enter positive value";
          alert(msg);
        }        
        else if( status == 5 ) {
          
          msg = "Please Select Dr Or Cr";
          alert(msg);
        }
        else if( status == 6 ) {
          
          msg = "Duplicate Ledger not allowed!";
          alert(msg);
        }
        else if( status == 7 ) {
          
          msg = "You can't change ledger name, as entries exist by this name in tables";
          alert(msg);
        }         
        else if( status == 99 ) {
          msg = "Session Expired! Login Again";
          alert(msg);
          //location.reload;
         location.reload();
        }
        else {
          msg = "Invalid Entry";
          alert(msg);
        } 
      },      
      error: function(jqXHR, textStatus, errorThrown)
      {
        alert( textStatus ); 
      }
    });
    jQuery('.ajaXloader').fadeOut(100);
  });
</script>
<script>
jQuery('.btnShowLedger').click(function(){

  var ledger_id = jQuery(this).attr('value');
  jQuery.ajax(
  {
    url: 'index.php?route=account_panel/ledger/getLedgerRecordThroughAjax&token=<?php echo $token; ?>',
    type: "POST",
    data : {ledger_id:ledger_id},
    ContentType:"application/json",
    async:false,
    success: function(response){
        var data = $.parseJSON(response);
        var status = data.status ;

          var ledger_id = data.ledger_id;
          var ledger_name = data.ledger_name;
          var group_id = data.group_id;
          var opening_balance = data.opening_balance;
          var drcr = data.drcr;


          $(".ledgerA").val(ledger_id);
          //$("#ledger").val(ledger_name);
          $(".ledger").val(ledger_name);
          $("#group_id").val(group_id);
          $("#opening_balance").val(opening_balance);
          $("#drcr").val(drcr);
          $("#group_id").removeAttr("disabled");
          if( status == 2 ) {
            $("#group_id").prop('disabled', 'disabled');
          }

    },      
    error: function(jqXHR, textStatus, errorThrown)
    {
      alert( textStatus ); 
    }
  });
});
jQuery('.btnDeleteLedger').click(function(){

  var ledger_id = jQuery(this).attr('value');
  var group_id = jQuery(this).attr('group_id');

  jQuery.ajax(
  {
    url: 'index.php?route=account_panel/ledger/deleteLedger&token=<?php echo $token; ?>',
    type: "POST",
    data : {ledger_id:ledger_id,group_id:group_id},
    ContentType:"application/json",
    async:false,
    success: function(response){

        var data = $.parseJSON(response);
        var status = data.status ;
        var msg;
        
        if( status == 1 ) {
          msg = "You cannot delete this Ledger as it appears in entries";
          alert(msg);
        }
        else if( status == 2 ) {
          
          msg = "You can't delete some important ledgers";
          alert(msg);
          //location.reload();
        }
        else if( status == 51 ) {
          
          msg = "Ledger Deleted successfully";
          alert(msg);
          location.reload();
        }        
        
        else {
          msg = "Invalid Entry";
          alert(msg);
        }
    
    },      
    error: function(jqXHR, textStatus, errorThrown)
    {
      alert( textStatus );
    }
  });
});
</script>


<script type="text/javascript"><!--
$('.button-filter').on('click', function() {
  
  var url = 'index.php?route=account_panel/ledger&token=<?php echo $token; ?>';

  var filter_ledger_name = $('input[name=\'filter_ledger_name\']').val();
  if (filter_ledger_name) {
    url += '&filter_ledger_name=' + encodeURIComponent(filter_ledger_name);
  }

  location = url;

});
</script>

<script type="text/javascript">
    $('input[name=\'filter_ledger_name\']').on('keyup', function() {
      var check_length = $(this).val().trim().length;
        $('input[name=\'filter_ledger_name\']').autocomplete({
          'source': function (request, response) {
            $.ajax({
              url: 'index.php?route=account_panel/ledger/getLedger&token=<?php echo $token; ?>&filter_ledger_name=' + encodeURIComponent(request),
              dataType: 'json',
              success: function (json) {
                response($.map(json, function (item) {
                  //console.log(item['ledger_name']);
                  return {
                    //label: item['model'],
                    label: item['ledger_name'],
                    value: item['ledger_id']
                  }
                }));
              }
            });
          },
          'select': function (item) {
            //$('input[name=\'filter_ledger_name\']').val(item['text']);
            $('input[name=\'filter_ledger_name\']').val(item['label']);
            $('input[name=\'ledger_id\']').val(item['value']);
          }
        });
    });
</script>