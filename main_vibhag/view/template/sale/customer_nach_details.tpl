<div class="credit-tab-content" id="credit">
  <fieldset>
    <legend>NACH Details: </legend>
    <div id="nach_details_div" class="">
      <div class="form-group">
        <label class="col-sm-2 control-label" for="nach_bank_type">NACH A/c Name</label>
        <div class="col-sm-8">
          <select class="form-control" name="nach_bank_type" id="nach_bank_type">
            <option value="STANC">STANC</option>
            <option value="YES_BANK">YES BANK</option>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-2 control-label" for="nach_lan_no">NACH LAN No.</label>
        <div class="col-sm-8">
          <input type="text" name="nach_lan_no" placeholder="NACH LAN No" id="nach_lan_no" class="form-control" />
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-2 control-label" for="nach_ac_name">NACH A/c Name</label>
        <div class="col-sm-8">
          <input type="text" name="nach_ac_name" placeholder="NACH A/c Name" id="nach_ac_name" class="form-control" value="" />
        </div>
      </div>

      <div class="form-group">
        <label class="col-sm-2 control-label" for="nach_ac_no">NACH A/c No.</label>
        <div class="col-sm-8">
          <input type="text" name="nach_ac_no" placeholder="NACH A/c No." id="nach_ac_no" class="form-control" value="" />
        </div>
      </div>

      <div class="form-group">
        <label class="col-sm-2 control-label" for="nach_umrn_no">UMRN No</label>
        <div class="col-sm-8">
          <input type="text" name="nach_umrn_no" placeholder="UMRN No" id="nach_umrn_no" class="form-control" value="" />
        </div>
      </div>

      <div class="form-group">
        <label class="col-sm-2 control-label" for="nach_ifsc_code">IFSC Code</label>
        <div class="col-sm-8">
          <input type="text" name="nach_ifsc_code" placeholder="IFSC Code." id="nach_ifsc_code" class="form-control" value="" />
          <span class="sample_ifsc_code">IFSC Code Ex:- KARB0000001 or BARB0DIGJAI</span>
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-2"></label>
        <div class="col-sm-10">
          <button type="button" id="ifsc_code_loading" class="btn btn-primary btn-xs hidden"></button>
          <div class="bank_details_block"></div>
        </div>
      </div>

      <button type="button" name="save_nach_details" id="save_nach_details" class="btn btn-primary">Save</button>
    </div>
  </fieldset><br><br>
  <?php if(!empty($customer_nach_details)){ ?>
  <div>
    <h4><b>All NACH account(s):</b></h4>
  </div>
  <table class="table table-bordered table-hover">
    <tr>
      <th>A/c Name</th>
      <th>A/c No.</th>
      <th>IFSC Code</th>
      <th>UMRN No.</th>
      <th>Bank Type</th>
      <th>LAN No.</th>
      <th>status</th>
      <th>Action</th>
    </tr>
    <?php foreach($customer_nach_details as $value){ ?>
    <tr>
      <td>
        <?php echo $value['account_name']; ?>
        <input type="text" name="account_name_<?php echo $value['id']; ?>" placeholder="NACH A/c Name" id="account_name_<?php echo $value['id']; ?>" class="form-control"  value="<?php echo $value['account_name']; ?>" style="display: none" />
      </td>
      <td>
        <?php echo $value['account_no']; ?>
        <input type="text" name="account_no_<?php echo $value['id']; ?>" placeholder="NACH A/c Name" id="account_no_<?php echo $value['id']; ?>" class="form-control"  value="<?php echo $value['account_no']; ?>" style="display: none" />
      </td>
      <td>
        <?php echo $value['ifsc_code']; ?>
        <input type="text" name="ifsc_code_<?php echo $value['id']; ?>" placeholder="NACH A/c Name" id="ifsc_code_<?php echo $value['id']; ?>" class="form-control"  value="<?php echo $value['ifsc_code']; ?>" style="display: none" />
      </td>
      <td>
        <?php echo $value['umrn_no']; ?>
        <input type="text" name="umrn_no_<?php echo $value['id']; ?>" placeholder="NACH A/c Name" id="umrn_no_<?php echo $value['id']; ?>" class="form-control"  value="<?php echo $value['umrn_no']; ?>" style="display: none" />
      </td>
      <td>
        <?php echo $value['bank_type']; ?>

        <select class="form-control" name="bank_type_<?php echo $value['id']; ?>" id="bank_type_<?php echo $value['id']; ?>" style="display: none">
          <option value="STANC" <?php echo $value['bank_type']== 'STANC' ? 'selected' : ''; ?> >STANC</option>
          <option value="YES_BANK" <?php echo $value['bank_type']== 'YES_BANK' ? 'selected' : ''; ?> >YES BANK</option>
        </select>
        <input type="text" name="bank_type_<?php echo $value['id']; ?>" id="bank_type_<?php echo $value['id']; ?>" class="form-control"  value="<?php echo $value['bank_type']; ?>" style="display: none" />
      </td>
      <td>
        <?php echo $value['lan_no']; ?>
        <input type="text" name="lan_no_<?php echo $value['id']; ?>" id="lan_no_<?php echo $value['id']; ?>" class="form-control"  value="<?php echo $value['lan_no']; ?>" style="display: none" />
      </td>
      <td>
        <?php if($value['active_status'] == 1 && $value['status'] == 1){ ?>
          <button type="button" class="btn btn-success">Default</button>
        <?php }else if($value['status'] == 1){ ?>
          <button type="button" class="btn btn-primary">Available</button>
        <?php }else{ ?>
          <button type="button" class="btn btn-danger">Unavailable</button>
        <?php } ?>
      </td>
      <td>
        <?php if($value['active_status'] != 1 && $value['status'] == 1){ ?>
        <span style="cursor: pointer;color: #003399;" class="mark_default_nach" data-id="<?php echo $value['id']; ?>"><i>Mark Default</i></span> | <br>
        <?php } ?> 
        <?php if($value['active_status'] != 1){ ?>
        <span style="cursor: pointer;color: #003399;" class="change_availability" data-id="<?php echo $value['id']; ?>"><i>Change Availability</i></span> | <br>
        <?php } ?> 
        <?php if($value['status'] == 1){ ?>
          <span style="cursor: pointer;color: #003399;" data-id="<?php echo $value['id']; ?>" data-toggle="modal" data-target="#move_nach_popup" class="move_nach_action"><i>Move NACH A/c</i></span> | <br>
        <?php }?>
        <span style="cursor: pointer;color: #003399;" class="edit_nach_ac" id="edit_nach_ac_<?php echo $value['id']; ?>" data-id="<?php echo $value['id']; ?>"><i class="fa fa-edit">Edit</i></span>
        <button type="button" name="save_nach_details_action" id="save_nach_details_action_<?php echo $value['id']; ?>" data-id="<?php echo $value['id']; ?>" class="btn btn-primary save_nach_details_action" style="display: none;float: left;margin-right: 2px;"><i class="fa fa-save"></i></button> 
        <button type="reset" name="cancel_nach_details_action_" id="cancel_nach_details_action_<?php echo $value['id']; ?>" data-id="<?php echo $value['id']; ?>" class="btn btn-default cancel_nach_details_action" style="display: none;float: left;"><i class="fa fa-undo"></i></button> 
      </td>
    </tr>
    <?php } ?>
  </table><br>
  <?php } ?>
</div>
<!-- Popup to take customer Id for moving NACH A/c details to that customer  -->
<div id="move_nach_popup" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <input type="hidden" name="hddn_nach_id" id="hddn_nach_id" />
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Move NACH A/c</h4>
      </div>
      <div class="modal-body" >
        <div class="form-group">
          <div class="col-sm-2"></div>
          <label for="to_customer_id"  class="col-sm-3" >To Customer Id: </label>
          <div class="col-sm-6">
            <input type="text" name="to_customer_id" id="to_customer_id" class="form-control" />
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary move_nach_account" name="move_nach_account" >Move NACH</button>
      </div>
    </div>

  </div>
</div>

<script type="text/javascript">
  $('#nach_ifsc_code').on('keyup',function(e){
      var filter = /^([a-zA-Z0-9]){1}$/;

      if(($(this).val().length==11) && filter.test(e.key)){
        getIfscCodeDetails(this,'.bank_details_block', '#ifsc_code_loading');
      } else {
        $('.bank_details_block').hide();
      }

  });

  function getIfscCodeDetails(obj, class_ctn, id_loading){
    var ifsc_code = $(obj).val().toUpperCase();
    $(obj).val(ifsc_code);
    if(ifsc_code.length == 11){
      flag = false;
      $.ajax({
        url : 'index.php?route=sellers/sellers/bankDetails&token=<?php echo $token;?>&ifsc_code='+ifsc_code ,
        dataType : 'json',
        async: false,
        beforeSend: function() {
            setTimeout(function(){
                $(id_loading).button('loading');
                $(id_loading).removeClass('hidden');
            },0);
        },
        complete: function() {
            setTimeout(function(){
                $(id_loading).button('reset');
                if(id_loading == '#ifsc_code_loading'){
                    $(id_loading).addClass('hidden');
                }
            },1000);
        },

        success:function(json){
            $('.customer-ifsc').hide();
            if(typeof(json) == 'object'){ 
                $(".customer-ifsc").remove();
                html = '<table class="table table-responsive table-bordered">';
                html += '<caption align="center"><h4>Bank Detail</h4></caption>';
                html +=     '<tr>';
                html +=         '<td><b>Bank Name: </b></td>';
                html +=         '<td>'+json['BANK']+'</td>';
                html +=     '</tr>';
                html +=     '<tr>';
                html +=         '<td><b>Branch: </b></td>';
                html +=         '<td>'+json['BRANCH']+'</td>';
                html +=     '</tr>';
                html +=     '<tr>';
                html +=         '<td><b>Address: </b></td>';
                html +=         '<td>'+json['ADDRESS']+'</td>';
                html +=     '</tr>';
                html +=     '<tr>';
                html +=         '<td><b>Place: </b></td>';
                html +=         '<td>'+json['DISTRICT']+'<br> '+json['CITY']+', '+json['STATE']+'</td>';
                html +=     '</tr>';
                html += '</table>';

                $(class_ctn).html(html);
                $(class_ctn).show();
                flag = true;
            }else{
                 
                $(obj).parent().find('.sample_ifsc_code').after('<span class="alert-danger customer-ifsc">'+ json +'</span>');
                $(class_ctn).html('');
                flag = false;
            }
          },
      });
      return flag;
    }else if(ifsc_code.length == 0){
        $(class_ctn).html(''); 
        return false;
                   
    }else{
        $(class_ctn).html('');
        return false;
    }
  }

  function validateNachForm(form_type, nach_ac_name, nach_ac_no, nach_umrn_no, nach_ifsc_code, id = 0){

    var err = 0;
    if(form_type == 'add' && $('#nach_bank_type').val().trim() == 'YES_BANK' && $('#nach_lan_no').val().trim().length == 0 ){
      err = 1;
      alert('LAN No. must be entered for Bank Type "YES BANK" !');
    }else if(form_type == 'update' && $('#bank_type_'+id).val().trim() == 'YES_BANK' && $('#lan_no_'+id).val().trim().length == 0 ){
      err = 1;
      alert('LAN No. must be entered for Bank Type "YES BANK" !');
    }else if($(nach_ac_name).val().trim().length == 0){
      err = 1;
      alert('Account name is missing !');
    }else if($(nach_ac_no).val().trim().length == 0){
      err = 1;
      alert('Account number is missing !');
    }else if($(nach_umrn_no).val().trim().length == 0){
      err = 1;
      alert('UMRN no. is missing !');
    }else if($(nach_ifsc_code).val().trim().length == 0){
      err = 1;
      alert('IFSC Code is missing !');
    }else if(!getIfscCodeDetails(nach_ifsc_code,'.bank_details_block', '#ifsc_code_loading') ){
      err = 1;
      alert('IFSC Code is not valid !');
    }

    var customer_id    = $('#customer_id').val().trim();
    var nach_ac_name   = $('#nach_ac_name').val().trim();
    var nach_ac_no     = $('#nach_ac_no').val().trim();
    var nach_ifsc_code = $('#nach_ifsc_code').val().trim();
    var nach_umrn_no   = $('#nach_umrn_no').val().trim();  
    var nach_bank_type = $('#nach_bank_type').val();
    var nach_lan_no    = $('#nach_lan_no').val().trim();
    $.ajax({
      url: 'index.php?route=sale/customer/checkForDuplicateNachDetails&token=<?php echo $token; ?>',
      type: 'post',
      async: false, //blocks window close
      data: 'customer_id='+customer_id+'&account_name=' + nach_ac_name + '&account_no=' + nach_ac_no + '&ifsc_code='+nach_ifsc_code + '&umrn_no='+ nach_umrn_no + '&bank_type='+nach_bank_type + '&lan_no='+nach_lan_no,
      success: function(json) {
        if(json.trim() != 'success' ){
          err = 1;
          alert(json.trim());
        }

      }
    });

    return err;
  }

  $('#save_nach_details').click(function() {

    var customer_id    = $('#customer_id').val().trim();
    var nach_ac_name   = $('#nach_ac_name').val().trim();
    var nach_ac_no     = $('#nach_ac_no').val().trim();
    var nach_ifsc_code = $('#nach_ifsc_code').val().trim();
    var nach_umrn_no   = $('#nach_umrn_no').val().trim();  
    var nach_bank_type = $('#nach_bank_type').val();
    var nach_lan_no    = $('#nach_lan_no').val().trim();

    //validate form value to save
    err = validateNachForm('add', '#nach_ac_name', '#nach_ac_no', '#nach_umrn_no', '#nach_ifsc_code', 0);
    
    if(err == 0){
      var auto_nach_enabled     = $('#hddn_auto_nach_enabled').val();
      var auto_nach_enabled_old = $('#hddn_old_auto_nach_enabled').val();

      if( auto_nach_enabled_old != 1 ){
        if(confirm("Auto-NACH Debit is currently DISABLED. Do you want to ENABLE that as well ?")){
          $('#hddn_auto_nach_enabled').val(1);
          $('#wsb_credit_comment').val("Mark Auto NACH Enabled, as per customer's NACH details saved.");
        }
      }

      if($('#cs-submit-button').click()){
        $.ajax({
          url: 'index.php?route=sale/customer/saveCustomerNachDetails&token=<?php echo $token; ?>',
          type: 'post',
          data: 'customer_id='+customer_id+'&account_name=' + nach_ac_name + '&account_no=' + nach_ac_no + '&ifsc_code='+nach_ifsc_code + '&umrn_no='+ nach_umrn_no + '&bank_type='+nach_bank_type + '&lan_no='+nach_lan_no,
          success: function(json) {
            if(json.trim() == 'success' ){
              alert('NACH Details Successfully Added!!');
              location.reload();
            }else{
              alert(json);
            }

          }
        });
      }
    }
    
  });

  $('.mark_default_nach').click(function(){
    var id = $(this).data('id');
    var customer_id    = $('#customer_id').val();
    if(confirm('Are you sure, you want to mark this NACH A/c as default NACH A/c ?')){
      $.ajax({
        url: 'index.php?route=sale/customer/markNachDetailAsDefault&token=<?php echo $token; ?>',
        type: 'post',
        data: 'id='+id +'&customer_id='+customer_id,
        success: function(json) {
          if(json.trim() == 'success' ){
            alert('Successfully Marked!!');
            location.reload();
          }else{
            alert(json);
          }
        }
      });
    }
  });

  $('.change_availability').click(function(){
    var id = $(this).data('id');
    var customer_id    = $('#customer_id').val();
    if(confirm('Are you sure, you want to change availability for this NACH A/c ?')){
      $.ajax({
        url: 'index.php?route=sale/customer/changeAvailabilityNachDetail&token=<?php echo $token; ?>',
        type: 'post',
        data: 'id='+id +'&customer_id='+customer_id,
        success: function(json) {
          if(json.trim() == 'success' ){
            alert('Successfully Updated!!');
            location.reload();
          }else{
            alert(json);
          }
        }
      });
    }
  });

  
  $('.move_nach_action').click(function(){
    
    var id = $(this).data('id');
    $('#hddn_nach_id').val(id);

  });

  $('.move_nach_account').click(function(){
    
    var id             = $('#hddn_nach_id').val();
    var customer_id    = $('#customer_id').val().trim();
    var to_customer_id = $('#to_customer_id').val().trim();

    if(to_customer_id.length == 0){
      alert("To Customer Id can not be empty!!");
      return;
    }else if(to_customer_id == customer_id){
      alert("From Customer Id and To Customer Id can not be same!!");
      return;
    }

    if(confirm("Are you sure, you want to move NACH A/c Details to any other Customer ID : '"+ to_customer_id +"' ?")){

      $.ajax({
        url: 'index.php?route=sale/customer/moveNachAcDetail&token=<?php echo $token; ?>',
        type: 'post',
        data: 'nach_id='+id +'&from_customer_id='+customer_id +'&to_customer_id='+to_customer_id,
        success: function(json) {
          if(json.trim() == 'success' ){
            alert('NACH a/c is moved Successfully to customer ID: '+ to_customer_id +', and marked as Default NACH a/c !!');
            location.reload();
          }else{
            alert(json);
          }
        }
      });
    }
  });

  $('.edit_nach_ac').click(function(){
    var id = $(this).data('id');

    $(this).css('display', 'none');
    $('#save_nach_details_action_'+id).css('display', 'block');
    $('#cancel_nach_details_action_'+id).css('display', 'block');
    $('#account_name_'+id).css('display', 'block');
    $('#account_no_'+id).css('display', 'block');
    $('#ifsc_code_'+id).css('display', 'block');
    $('#umrn_no_'+id).css('display', 'block');
    $('#bank_type_'+id).css('display', 'block');
    $('#lan_no_'+id).css('display', 'block');

  });

  $('.save_nach_details_action').click(function(){

    var id = $(this).data('id');
    var nach_ac_name   = $('#account_name_'+id).val();
    var nach_ac_no     = $('#account_no_'+id).val();
    var nach_ifsc_code = $('#ifsc_code_'+id).val();
    var nach_umrn_no   = $('#umrn_no_'+id).val();
    var nach_bank_type = $('#bank_type_'+id).val();
    var nach_lan_no    = $('#lan_no_'+id).val();

    err = validateNachForm('update', '#account_name_'+id, '#account_no_'+id, '#umrn_no_'+id, '#ifsc_code_'+id, id);
    if(err == 0){

      $.ajax({
        url: 'index.php?route=sale/customer/updateCustomerNachDetails&token=<?php echo $token; ?>',
        type: 'post',
        data: 'id='+id+'&account_name=' + nach_ac_name + '&account_no=' + nach_ac_no + '&ifsc_code='+nach_ifsc_code + '&umrn_no='+ nach_umrn_no + '&bank_type=' + nach_bank_type + '&lan_no='+nach_lan_no ,
        success: function(json) {
          if(json.trim() == 'success' ){
            alert('Successfully Updated!!');
            location.reload();
          }else{
            alert(json);
          }
        }
      });
    }//End of IF block

  });

  $('.cancel_nach_details_action').click(function(){
    var id = $(this).data('id');

    $(this).css('display', 'none');
    $('#edit_nach_ac_'+id).css('display', 'block');
    $('#save_nach_details_action_'+id).css('display', 'none');
    $('#cancel_nach_details_action_'+id).css('display', 'none');
    $('#account_name_'+id).css('display', 'none');
    $('#account_no_'+id).css('display', 'none');
    $('#ifsc_code_'+id).css('display', 'none');
    $('#umrn_no_'+id).css('display', 'none');
    $('#bank_type_'+id).css('display', 'none');
    $('#lan_no_'+id).css('display', 'none');
  });
</script>