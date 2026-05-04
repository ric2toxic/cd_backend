<style type="text/css">
  .msg{
    text-align: center;
    /*border: 1px solid #fbe8e4;*/
    margin: 0px 0px 15px;
  }
</style>
<?php 
$disabled = 'disabled';
$is_submit = 0;
if( empty($customer['bank_ac_number']) || 
    empty($customer['bank_ac_holder_name']) || 
    empty($customer['ifsc_code']) ||
    $admin_mode === 'on'
  ) { 
    $disabled = 'required';
    $is_submit = 1;
 } ?>        
<div class="table-responsive">
<form name="bank_detail_form" id="bank_detail_form" action="" method="">
  <div id="msg" class="msg" style="display: none;"></div>
    <input type="hidden" id="customer_id" name="customer_id" value="<?php echo $customer_id;?>">
    <table class="bank_detail">
      <tbody>
        <tr>
          <td><label for="ac_no">Bank Account No.: </label></td>
          <td><input type="text" name="ac_no" id="ac_no" value="<?php echo !empty($customer['bank_ac_number'])? $customer['bank_ac_number']:'';?>" <?php echo $disabled?> ><br /></td>
        </tr>
        <tr>
          <td><label for="ac_holder">Account Holder Name: </label></td>
          <td><input type="text" name="ac_holder" id="ac_holder" value="<?php echo !empty($customer['bank_ac_holder_name'])? $customer['bank_ac_holder_name']:'';?>" <?php echo $disabled?> ><br /></td>
        </tr>
        <tr>
          <td><label for="ifsc_code">IFSC Code: </label></td>
          <td><input type="text" name="ifsc_code" id="ifsc_code" min="11" max="11" value="<?php echo !empty($customer['ifsc_code'])? $customer['ifsc_code']:'';?>" <?php echo $disabled?> ><br />
          <div><span style="color:#337ab7;" id="ifsc_code_loading"></span></div>
          </td>
        </tr>

<?php if( $is_submit ) { ?>
        <tr>
          <td></td>
          <td>
            <br>
            <button type="button" id="save_bank_detail" class="btn btn-primary save_bank_detail">Save</button>
          </td>
        </tr>
  <?php } ?>

      </tbody>
    </table>
  </form>

<?php if( $disabled === 'disabled' ) { ?>
  <h4 style="padding-top: 10px;"><i>Note: If you want edit Bank-Details, Please contact administrator. </i></h4>
<?php } ?>

</div>
<script type="text/javascript">
  $(document).ready(function(){
   $("#ifsc_code").on('keyup',function(e){
      var filter = /^([a-zA-Z0-9]){1}$/;
      if (filter.test(e.key)){
          getIfscCodeDetails(this,'.bank_details_block', '#ifsc_code_loading');   
      }else{
          $('.bank_details_block').hide();
      } 
    });
  });

  $('.save_bank_detail').click(function(){
    if(validateBankDetails()){
    var formdata = {
                    ac_no     : $('#ac_no').val(), 
                    ac_holder : $('#ac_holder').val(),
                    ifsc_code : $('#ifsc_code').val(), 
                    customer_id : $('#customer_id').val()
                    };

    $.ajax({
      url: 'index.php?route=sale/customer/updateBankDetails&token=<?php echo $token; ?>',
      type: 'POST',
      data: formdata,
      success: function(data){
        $('#msg').text('Bank Details Successfully Updated.');
        $('#msg').removeClass('alert-danger');
        $('#msg').addClass('alert-success');
        $('#msg').css('display','block');
      }
    });
    }
  });

  //Validation of Bank IFSC Code
  function getIfscCodeDetails(obj, class_ctn, id_loading){
        var ifsc_code = $(obj).val().toUpperCase();
        flag = false;
        if(ifsc_code.length == 11){
            $.ajax({
                url : 'index.php?route=sellers/sellers/bankDetails&token=<?php echo $token;?>&ifsc_code='+ifsc_code ,
                async: false,
                dataType: "json",
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
                    $('.seller-ifsc').hide();
                    if(typeof(json) == 'object'){ 
                        $(".seller-ifsc").remove();
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
                        $(obj).parent().find('.sample_ifsc_code').after('<span class="alert-danger seller-ifsc">'+ json +'</span>');
                        $(class_ctn).html('');
                        flag = false;
                    }
                },
            });
            return flag;
        }else{
          $(class_ctn).html('');
          return flag;
        }
  }

  function validateBankDetails(){
      var ac_name = $('input[id="ac_holder"]').val().trim();
      var ac_no = $('input[id="ac_no"]').val().trim();
      var ifsc = $('input[id="ifsc_code"]').val().trim();
      var error = [];
      var flag = true;
      if(ac_no.length == 0 ){
        error.push("* Bank Account Number must be entered.");
      }else if(ac_name.length == 0){
        error.push("* Account Holder Name must be entered.");
      } else if(ifsc.length == 0 ){
        error.push("* IFSC must be entered.");
      }else{
        flag = getIfscCodeDetails('input[id=\'ifsc_code\']','.bank_details_block', '#ifsc_code_loading');
        if(!flag){
          error.push("* IFSC Code Validation Failed.");
        }
      }
      if(error.length > 0){
        $('#msg').text(error[0]);
        $('#msg').removeClass('alert-success');
        $('#msg').addClass('alert-danger');
        $('#msg').css('display','block');
        return false;
      }else{
        return true;
      }
  }
</script>