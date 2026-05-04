<?php if(in_array($this->user->getId(),SMS_CRITERIA_ICON_PERMISSION)) { ?>
<div>
    <?php if($sms_data['have_sms_pos_log'] > 0) { ?>
    <a id="getPosSms_<?php echo $customer['customer_id'];?>" data-id="<?php echo $customer['customer_id'];?>" data-info="<?php echo $customer['name']." - ".$customer['telephone'];?>" href="javascript:void(0);" title="POS SMS" data-toggle="modal" class="float" >
        <span class="label label-warning">POS</span>
    </a>
    <?php }
    if($sms_data['have_sms_bank_log'] > 0) { ?>
        <a id="getBankSms_<?php echo $customer['customer_id'];?>" data-id="<?php echo $customer['customer_id'];?>" data-info="<?php echo $customer['name']." - ".$customer['telephone'];?>" href="javascript:void(0);" title="Bank SMS" data-toggle="modal" class="float" >
        <span class="label label-info">Bank</span>
        </a>
    <?php } ?>
</div>
<?php  } if($sms_data['have_sms_bounce_log'] > 0) { ?>
    <a id="getBounceSms_<?php echo $customer['customer_id'];?>" data-id="<?php echo $customer['customer_id'];?>" data-info="<?php echo $customer['name']." - ".$customer['telephone'];?>" href="javascript:void(0);" title="Bounce SMS" data-toggle="modal" class="float" >
    <span class="label label-warning">BOUNCE</span>
    </a>
<?php }
    if($sms_data['have_sms_gst_log'] > 0) { ?>
    <a id="getGstSms_<?php echo $customer['customer_id'];?>" data-id="<?php echo $customer['customer_id'];?>" data-info="<?php echo $customer['name']." - ".$customer['telephone'];?>" href="javascript:void(0);" title="GST SMS" data-toggle="modal" class="float" >
    <span class="label label-warning">GST</span>
    </a>
<?php } if($sms_data['have_sms_lazypay_log'] > 0) { ?>
    <a id="getLazyPaySms_<?php echo $customer['customer_id'];?>" data-id="<?php echo $customer['customer_id'];?>" data-info="<?php echo $customer['name']." - ".$customer['telephone'];?>" href="javascript:void(0);" title="LazyPay SMS" data-toggle="modal" class="float" >
    <span class="label label-warning">LazyPay</span>
    </a>
<?php }
    if($sms_data['has_sms_loan_log'] > 0) { ?>
    <a id="getLoanSms_<?php echo $customer['customer_id'];?>" data-id="<?php echo $customer['customer_id'];?>" data-info="<?php echo $customer['name']." - ".$customer['telephone'];?>" href="javascript:void(0);" title="Loan SMS" data-toggle="modal" class="float" >
    <span class="label label-danger">Loan</span>
    </a>
<?php } ?>


<?php if( in_array($this->user->getId(),SHORT_SMS_PERMISSION)) { ?>

<hr class="btn-success">
<div>
    <?php if($sms_data['have_sms_credit_log'] > 0) { ?>
    <a id="getCreditSms_<?php echo $customer['customer_id'];?>" data-id="<?php echo $customer['customer_id'];?>" data-info="<?php echo $customer['name']." - ".$customer['telephone'];?> " href="javascript:void(0);" title="Credit SMS" data-toggle="modal" class="float" >
        <span class="label label-danger">UPL</span>
    </a>
    <?php
    }
    if($sms_data['have_sms_paytm_log'] > 0) { ?>
    <a id="getPaytmSms_<?php echo $customer['customer_id'];?>" data-id="<?php echo $customer['customer_id'];?>" data-info="<?php echo $customer['name']." - ".$customer['telephone'];?>" href="javascript:void(0);" title="Paytm SMS" data-toggle="modal" class="float" >
        <span class="label label-primary">Paytm</span>
    </a>
    <?php }

    if($sms_data['has_sms_mswipe_log'] > 0) { ?>
    <a id="getMswipeSms_<?php echo $customer['customer_id'];?>" data-id="<?php echo $customer['customer_id'];?>" data-info="<?php echo $customer['name']." - ".$customer['telephone'];?>" href="javascript:void(0);" title="Mswipe SMS" data-toggle="modal" class="float" >
        <span class="label label-warning">Mswipe</span>
    </a>
    <?php }
    if($sms_data['has_customer_short_sms']) { ?>
    <a id="getShortSms_<?php echo $customer['customer_id'];?>" data-id="<?php echo $customer['customer_id'];?>" data-info="<?php echo $customer['name']." - ".$customer['telephone'];?>" href="javascript:void(0);" title="Short SMS" data-toggle="modal" class="float" >
        <i class="fa fa-envelope" aria-hidden="true" style="font-size:16px;"></i>
    </a>
    <?php }?>

</div>

<?php } ?>

<div id="read_short_sms" class="modal fade in " role="dialog" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog  modal-lg" style="width: 75%; height: 700px!important; overflow: auto;">
    <!-- Modal content-->
    <div class="modal-content" >
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">X</button>
        <h3 class="modal-title">Short SMS</h3>
      </div>
      <div class="modal-body">

        <div id="response_text">
        </div>
        <div id="sms_short_sms_logs">
          <i class="fa fa-spinner fa-spin" style="font-size:36px"></i>
        </div>

      </div>
    </div>
    <div class="modal-footer">
    </div>
  </div>
</div>



<div id="read_sms_credit" class="modal fade in " role="dialog" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog  modal-lg" >
    <!-- Modal content-->
    <div class="modal-content" >
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h3 class="modal-title">Credit SMS</h3>
      </div>
      <div class="modal-body">

        <div id="sms_credit_logs">

        </div>

      </div>
    </div>
    <div class="modal-footer">
    </div>
  </div>
</div>

<div id="read_sms_pos" class="modal fade in " role="dialog" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog  modal-lg" >
    <!-- Modal content-->
    <div class="modal-content" >
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h3 class="modal-title">POS SMS</h3>
      </div>
      <div class="modal-body">

        <div id="sms_pos_logs">

        </div>

      </div>
    </div>
    <div class="modal-footer">
    </div>
  </div>
</div>

<div id="read_sms_bounce" class="modal fade in " role="dialog" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog  modal-lg" >
    <!-- Modal content-->
    <div class="modal-content" >
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h3 class="modal-title">Bounce SMS</h3>
      </div>
      <div class="modal-body">

        <div id="sms_bounce_logs">

        </div>

      </div>
    </div>
    <div class="modal-footer">
    </div>
  </div>
</div>


<div id="read_sms_gst" class="modal fade in " role="dialog" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog  modal-lg" >
    <!-- Modal content-->
    <div class="modal-content" >
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h3 class="modal-title">GST SMS</h3>
      </div>
      <div class="modal-body">

        <div id="sms_gst_logs">

        </div>

      </div>
    </div>
    <div class="modal-footer">
    </div>
  </div>
</div>

<div id="read_sms_bank" class="modal fade in " role="dialog" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog  modal-lg" >
    <!-- Modal content-->
    <div class="modal-content" >
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">X</button>
        <h3 class="modal-title">Bank SMS</h3>
      </div>
      <div class="modal-body">

        <div id="sms_bank_logs">

        </div>

      </div>
    </div>
    <div class="modal-footer">
    </div>
  </div>
</div>

<div id="read_sms_paytm" class="modal fade in " role="dialog" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog  modal-lg" >
    <!-- Modal content-->
    <div class="modal-content" >
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">X</button>
        <h3 class="modal-title">Paytm SMS</h3>
      </div>
      <div class="modal-body">

        <div id="sms_paytm_logs">

        </div>

      </div>
    </div>
    <div class="modal-footer">
    </div>
  </div>
</div>

<div id="read_sms_lazypay" class="modal fade in " role="dialog" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog  modal-lg" >
    <!-- Modal content-->
    <div class="modal-content" >
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">X</button>
        <h3 class="modal-title">LazyPay SMS</h3>
      </div>
      <div class="modal-body">

        <div id="sms_lazypay_logs">

        </div>

      </div>
    </div>
    <div class="modal-footer">
    </div>
  </div>
</div>


<div id="read_sms_loan" class="modal fade in " role="dialog" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog  modal-lg" >
    <!-- Modal content-->
    <div class="modal-content" >
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">X</button>
        <h3 class="modal-title">Loan SMS</h3>
      </div>
      <div class="modal-body">
        <div id="sms_loan_logs"></div>
      </div>
    </div>
    <div class="modal-footer">
    </div>
  </div>
</div>


<div id="read_sms_mswipe" class="modal fade in " role="dialog" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog  modal-lg" >
    <!-- Modal content-->
    <div class="modal-content" >
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">X</button>
        <h3 class="modal-title">Mswipe SMS</h3>
      </div>
      <div class="modal-body">
        <div id="sms_mswipe_logs"></div>
      </div>
    </div>
    <div class="modal-footer">
    </div>
  </div>
</div>

<script type="text/javascript">

    var page = 0;
    $("body").delegate(".more_btn", "click", function(){
        var customer_id = $(this).data('customer_id');
        var type = $(this).data('type');
        page++;
        $.ajax({
            url: 'index.php?route=sale/customer_credit_application/readSmsLog&token=<?php echo $token; ?>&customer_id=' + customer_id + '&type='+type+ '&page='+page,
            dataType: "html",
            success: function(data) {
                $("#smsTable").append(data);
                if(data.indexOf('No Record Found') != -1) { $(".more_btn").remove(); }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });

    });


    var sms_page = 0;
    $("body").delegate(".more_short_sms_btn", "click", function(){
        var customer_id = $(this).data('customer_id');

        sms_page++;
        $.ajax({
            url: 'index.php?route=sale/customer/readShortSmsLog&token=<?php echo $token; ?>&customer_id=' + customer_id + '&page='+sms_page,
            dataType: "html",
            success: function(data) {
                $("#shortSmsTable").append(data);
                if(data.indexOf('No Record Found') != -1) { $(".more_short_sms_btn").remove(); }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });

    });

    $("body").delegate("#getShortSms_<?php echo $customer['customer_id'];?>", "click", function(){
        var customer_id = $(this).data('id');
        var customer_info = $(this).data('info');
        $('#read_short_sms').modal();
        $("#read_short_sms .modal-title").html("Short SMS - " + customer_info);
        $("#sms_short_sms_logs").html('<i class="fa fa-spinner fa-spin" style="font-size:36px"></i>');
        $("#response_text").html('');
        $.ajax({
            url: 'index.php?route=sale/customer/readShortSmsLog&token=<?php echo $token; ?>&customer_id=' + customer_id,
            dataType: "html",
            success: function(data) {
                $("#sms_short_sms_logs").html(data);
                $("#read_short_sms .modal-title").html("Short SMS - " + customer_info);
                $("#response_text").html('<div><a href="javascript:void(0);" id="download_short_sms" data-id="'+ customer_id +'"><i class="fa fa-download"  aria-hidden="true" style="font-size:16px;"> Download SMS Data</i></a></div><div id="download_alert" class="alert alert-secondary" role="alert"></div>');

            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });

    });

    $("body").delegate("#download_short_sms", "click", function(){
        var customer_id = $(this).data('id');
        $("#response_text").html('<i class="fa fa-spinner fa-spin" style="font-size:16px"></i>');
        $.ajax({
            url: 'index.php?route=sale/customer/readShortSmsLog&token=<?php echo $token; ?>&dwnld=yup&customer_id=' + customer_id,
            dataType: "html",
            success: function(data) {
                $("#response_text").html(data);
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });

    });

    $(".modal").on('hidden.bs.modal', function () {
        $(this).data('bs.modal', null);
    });

    $("body").delegate("#getPosSms_<?php echo $customer['customer_id'];?>", "click", function(){
        var customer_id = $(this).data('id');
        var customer_info = $(this).data('info');

        $.ajax({
            url: 'index.php?route=sale/customer_credit_application/readSmsLog&token=<?php echo $token; ?>&customer_id=' + customer_id + '&type=pos',
            dataType: "html",
            success: function(data) {
                $("#sms_pos_logs").html(data);
                $("#read_sms_pos .modal-title").html("POS SMS - " + customer_info);
                $('#read_sms_pos').modal();
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });

    });

    $("body").delegate("#getCreditSms_<?php echo $customer['customer_id'];?>", "click", function(){
        var customer_id = $(this).data('id');
        var customer_info = $(this).data('info');

        $.ajax({
            url: 'index.php?route=sale/customer_credit_application/readSmsLog&token=<?php echo $token; ?>&customer_id=' + customer_id + '&type=udaan',
            dataType: "html",
            success: function(data) {
                $("#sms_credit_logs").html(data);
                $("#read_sms_credit .modal-title").html("Credit SMS - " + customer_info);
                $('#read_sms_credit').modal();
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });

    });

    $("body").delegate("#getBounceSms_<?php echo $customer['customer_id'];?>", "click", function(){
        var customer_id = $(this).data('id');
        var customer_info = $(this).data('info');

        $.ajax({
            url: 'index.php?route=sale/customer_credit_application/readSmsLog&token=<?php echo $token; ?>&customer_id=' + customer_id + '&type=bounce',
            dataType: "html",
            success: function(data) {
                $("#sms_credit_logs").html(data);
                $("#read_sms_bounce .modal-title").html("Bounce SMS - " + customer_info);
                $('#read_sms_bounce').modal();
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });

    });

    $("body").delegate("#getGstSms_<?php echo $customer['customer_id'];?>", "click", function(){
        var customer_id = $(this).data('id');
        var customer_info = $(this).data('info');

        $.ajax({
            url: 'index.php?route=sale/customer_credit_application/readSmsLog&token=<?php echo $token; ?>&customer_id=' + customer_id + '&type=gst',
            dataType: "html",
            success: function(data) {
                $("#sms_gst_logs").html(data);
                $("#read_sms_gst .modal-title").html("Gst SMS - " + customer_info);
                $('#read_sms_gst').modal();
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });

    });

    $("body").delegate("#getBankSms_<?php echo $customer['customer_id'];?>, #radio_all_bank_sms", "click", function(e){
        var customer_id = $(this).data('id');
        var customer_info = $(this).data('info');

        $.ajax({
            url: 'index.php?route=sale/customer_credit_application/getAllBanksNameFromSmsLog&token=<?php echo $token; ?>&customer_id=' + customer_id + '&type=account',
            dataType: "html",
            success: function(data) {
                $("#sms_pos_logs").html(data);
                $("#read_sms_pos .modal-title").html("Bank SMS - " + customer_info);
                $('#read_sms_pos').modal();
                $("#response_text").html('');
                $('#myTab li > a:first').trigger('click');
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });


    $("body").delegate("#radio_bank_statement", "click", function(e){
        var customer_id = $(this).data('id');

        $.ajax({
            url: 'index.php?route=sale/customer_credit_application/getAllBanksHavingStatements&token=<?php echo $token; ?>&customer_id=' + customer_id + ' ',
            dataType: "html",
            success: function(data) {
                $("#data_container").html(data);
                $('#myStatementTab li > a:first').trigger('click');
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });

    $(document).delegate('#myTab li > a', 'click', function (e) {
        e.preventDefault();
        var customer_id = $(this).data('id');
        var bank_name = $(this).data('bank_name');
        console.log(bank_name);
        console.log(customer_id);
        var a_href = $(this).attr('href');
        console.log(a_href);
        var url = 'index.php?route=sale/customer_credit_application/readSmsLog&token=<?php echo $token; ?>&customer_id=' + customer_id + '&type=account&bank='+bank_name+' ';
        $(a_href).html('<i class="fa fa-spinner fa-spin" style="font-size:36px"></i>');
        $.ajax({
            url: url,
            dataType: "html",
            success: function(data) {
                $(a_href).html(data);
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });

    $(document).delegate('#myStatementTab li > a', 'click', function (e) {
        e.preventDefault();
        var customer_id = $(this).data('id');
        var bank_name = $(this).data('bank_name');
        console.log(bank_name);
        console.log(customer_id);
        var a_href = $(this).attr('href');
        console.log(a_href);
        var url = 'index.php?route=sale/customer_credit_application/readBankStatementSmsLog&token=<?php echo $token; ?>&customer_id=' + customer_id + '&type=bank_statement&bank='+bank_name+' ';
        $(a_href).html('<i class="fa fa-spinner fa-spin" style="font-size:36px"></i>');
        $.ajax({
            url: url,
            dataType: "html",
            success: function(data) {
                $(a_href).html(data);
                $('#btn_grp_bank_'+bank_name+' label.btn  input[type=radio]:first').trigger('click');

            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });


    $(document).delegate('label.btn  input[type=radio]', 'change', function (e) {
        e.preventDefault();
        var customer_id = $(this).data('id');
        var ac_no = $(this).data('ac_no');
        var ac_bank_name = $(this).data('bank_name');
        console.log(ac_no);
        console.log(ac_bank_name);
        console.log(customer_id);
        $("#show_bank_smt_"+ ac_bank_name).html('');

        var url = 'index.php?route=sale/customer_credit_application/getBankAccountStatement&token=<?php echo $token; ?>&customer_id=' + customer_id + '&ac_no='+ac_no+'&bank_name='+ac_bank_name+' ';
        $("#show_bank_smt_"+ ac_bank_name).html('<i class="fa fa-spinner fa-spin" style="font-size:36px"></i>');
        $.ajax({
            url: url,
            dataType: "html",
            success: function(data) {
                $("#show_bank_smt_"+ ac_bank_name).html(data);
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });

    $("body").delegate("#getPaytmSms_<?php echo $customer['customer_id'];?>", "click", function(){
        var customer_id = $(this).data('id');
        var customer_info = $(this).data('info');

        $.ajax({
            url: 'index.php?route=sale/customer_credit_application/readSmsLog&token=<?php echo $token; ?>&customer_id=' + customer_id + '&type=paytm',
            dataType: "html",
            success: function(data) {
                $("#sms_paytm_logs").html(data);
                $("#read_sms_paytm .modal-title").html("Paytm SMS - " + customer_info);
                $('#read_sms_paytm').modal();
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });

    $("body").delegate("#getLazyPaySms_<?php echo $customer['customer_id'];?>", "click", function(){
        var customer_id = $(this).data('id');
        var customer_info = $(this).data('info');

        $.ajax({
            url: 'index.php?route=sale/customer_credit_application/readSmsLog&token=<?php echo $token; ?>&customer_id=' + customer_id + '&type=lazypay',
            dataType: "html",
            success: function(data) {
                $("#sms_lazypay_logs").html(data);
                $("#read_sms_lazypay .modal-title").html("LazyPay SMS - " + customer_info);
                $('#read_sms_lazypay').modal();
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });

    $("body").delegate("#getLoanSms_<?php echo $customer['customer_id'];?>", "click", function(){
        var customer_id = $(this).data('id');
        var customer_info = $(this).data('info');

        $.ajax({
            url: 'index.php?route=sale/customer_credit_application/readSmsLog&token=<?php echo $token; ?>&customer_id=' + customer_id + '&type=loan',
            dataType: "html",
            success: function(data) {
                $("#sms_loan_logs").html(data);
                $("#read_sms_loan .modal-title").html("Loan SMS - " + customer_info);
                $('#read_sms_loan').modal();
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });

    $("body").delegate("#getMswipeSms_<?php echo $customer['customer_id'];?>", "click", function(){
        var customer_id = $(this).data('id');
        var customer_info = $(this).data('info');

        $.ajax({
            url: 'index.php?route=sale/customer_credit_application/readSmsLog&token=<?php echo $token; ?>&customer_id=' + customer_id + '&type=mswipe',
            dataType: "html",
            success: function(data) {
                $("#sms_mswipe_logs").html(data);
                $("#read_sms_mswipe .modal-title").html("Mswipe SMS - " + customer_info);
                $('#read_sms_mswipe').modal();
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });


</script>