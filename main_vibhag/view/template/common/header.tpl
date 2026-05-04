<!DOCTYPE html>
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>">
<head>
<meta charset="UTF-8" />
<title><?php echo $title; ?></title>
<base href="<?php echo $base; ?>" />
<?php if ($description) { ?>
<meta name="description" content="<?php echo $description; ?>" />
<?php } ?>
<?php if ($keywords) { ?>
<meta name="keywords" content="<?php echo $keywords; ?>" />
<?php } ?>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
<script type="text/javascript" src="view/javascript/jquery/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="view/javascript/bootstrap/js/bootstrap.min.js"></script>
<link href="view/javascript/bootstrap/opencart/opencart.css" type="text/css" rel="stylesheet" />
<link href="view/javascript/font-awesome/css/font-awesome.min.css" type="text/css" rel="stylesheet" />
<link href="view/javascript/summernote/summernote.css" rel="stylesheet" />
<link href="view/stylesheet/menu.css" rel="stylesheet" />
<script type="text/javascript" src="view/javascript/summernote/summernote.js"></script>
<script src="view/javascript/jquery/datetimepicker/moment.js" type="text/javascript"></script>
<script src="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script  src="view/javascript/fancybox/jquery.fancybox.pack.js" type="text/javascript"></script>
<link href="view/javascript/fancybox/jquery.fancybox.css" rel="stylesheet" />
<link href="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css" type="text/css" rel="stylesheet" media="screen" />  
<link type="text/css" href="view/stylesheet/stylesheet.css?v=18.02" rel="stylesheet" media="screen" />
<link href="view/stylesheet/bootstrap-toggle.min.css" rel="stylesheet">
<script src="view/javascript/bootstrap/js/bootstrap-toggle.min.js"></script>

<link href="view/stylesheet/jquery-confirm.min.css" rel="stylesheet">
<script src="view/javascript/jquery/jquery-confirm.min.js"></script>

<?php /*?><!-- plupload using in filemanager --> <?php */ ?>
<link type="text/css" href="view/javascript/plupload/js/jquery.plupload.queue/css/jquery.plupload.queue.css" rel="stylesheet" media="screen" />
<script type="text/javascript" src="view/javascript/plupload/js/plupload.full.min.js"></script>
<script type="text/javascript" src="view/javascript/plupload/js/jquery.plupload.queue/jquery.plupload.queue.min.js"></script>
  <?php /*?><!-- --> <?php */ ?>

  <?php foreach ($styles as $style) { ?>
<link type="text/css" href="<?php echo $style['href']; ?>" rel="<?php echo $style['rel']; ?>" media="<?php echo $style['media']; ?>" />
<?php } ?>
<?php foreach ($links as $link) { ?>
<link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>" />
<?php } ?>
<script src="view/javascript/common.js?v=6" type="text/javascript"></script>

<?php foreach ($scripts as $script) { ?>
<script type="text/javascript" src="<?php echo $script; ?>"></script>
<?php } ?>


<!-- PUSHER NOTIFICATION -->
<!-- <script src="https://js.pusher.com/3.2/pusher.min.js"></script>

  <script>

      // Enable pusher logging - don't include this in production
      Pusher.logToConsole = true;

      var pusher = new Pusher('9daa818af6c35f4e5e41', {
        cluster: 'ap1',
        encrypted: true
      });

    var channel = pusher.subscribe('local-public-madhur');
    channel.bind('my_event', function(data) {
      alert(data.message);
    });
    
  </script> -->
<!-- PUSHER NOTIFICATION -->

</head>
<body>
<div id="container">
<header id="header" class="navbar navbar-static-top">
  <div class="navbar-header">
    <?php if ($logged) { ?>
    <a type="button" id="button-menu" class="pull-left"><i class="fa fa-indent fa-lg"></i></a>
    <?php } ?>
    <a href="javascript:void(0)" class="navbar-brand"
    ><img src="https://cdnimages.net/img/logo.png" alt="<?php echo $heading_title; ?>" title="<?php echo $heading_title; ?>" />
    </a></div>
  <?php
   if ($logged) { 
    if (!$is_mobile_site) {
    ?>
    <div class="text-center" style="display: block; width: 61%; float: left; margin-top: 6px;">
      <form class="text-center" style="display: inline-block;" id="common_search_orders" action="<?php echo $common_search_orders_url;?>">
      <input class="form-control pull-left" style="width: 12%; margin-right:10px;" type="text" placeholder="Order No" name="common_search_filter_order_no" id="filter_order_no" value="<?php echo $common_search_filter_order_no;?>">
      <input  class="form-control pull-left" style="width: 28%; margin-right:10px;" type="text" placeholder="Customer Name OR Email OR Phone" name="common_search_filter_customer" id="filter_customer" value="<?php echo $common_search_filter_customer;?>">
      <input class="form-control pull-left" style="width: 10%; margin-right:10px;" type="text" placeholder="CID" name="common_search_filter_customer_id" id="filter_customer_id" value="<?php echo $common_search_filter_customer_id ;?>">
      <input  class="form-control pull-left" style="width: 12%; margin-right:10px;" type="text" placeholder="City" name="common_search_filter_city" id="filter_city" value="<?php echo $common_search_filter_city;?>">
      <input  class="form-control pull-left" style="width: 12%; margin-right:10px;" type="text" placeholder="Tracking No." name="common_search_filter_tracking_number" id="filter_tracking_no" value="<?php echo $common_search_filter_tracking_number;?>">
      <button id="common_search_orders_button" class="btn btn-primary pull-left" type="button">Search</button>
      </form>
    </div>
    <?php
    }
    ?>
  <ul class="nav pull-right">
    <li id="change-password"><button class="change_password" style=" margin-top:8px"><?php echo $text_change_password; ?></button></li>

    <li class="dropdown">
      <a class="dropdown-toggle pickup_issue_notice" data-toggle="dropdown">
        <?php if ($alerts > 0) { ?>
          <span class="label label-danger pull-left" data-alerts="<?php echo $alerts; ?>"><?php echo $alerts; ?></span>
        <?php } ?>
        <i class="fa fa-bell fa-lg"></i>
      </a>

      <ul class="dropdown-menu dropdown-menu-right alerts-dropdown">
        <li class="dropdown-header"><?php echo $text_product; ?></li>
          <?php if( ($product_notification ?? false) == true ){ ?>
            <li><a href="<?php echo $moderate_link; ?>"><span class="label <?php if ($moderate_total > 0) { echo 'label-danger'; } else { echo 'label-success'; } ?> pull-right"><?php echo $moderate_total; ?></span><?php echo $text_product_moderate; ?></a></li>
        <?php } ?>
        <?php if( ($user_questions_notification ?? false) == true ){ ?>
        <li><a href="<?php echo $user_questions_link; ?>"><span class="label <?php if ($user_question_total > 0) { echo 'label-danger'; } else { echo 'label-success'; } ?> pull-right"><?php echo $user_question_total; ?></span><?php echo $text_user_question; ?></a></li>
        <?php } ?>
      </ul>

    </li>

    <li><a href="<?php echo $logout; ?>"><span class="hidden-xs hidden-sm hidden-md"><?php echo $text_logout; ?></span> <i class="fa fa-sign-out fa-lg"></i></a></li>

  </ul>
  <?php } ?>
</header>

<script type="text/javascript">

function copyToClipboard(containerid)
{ 
    /* Get the text field */
    var copyText = document.getElementById(containerid);

    /* Select the text field */
    copyText.select();

    /* Copy the text inside the text field */
    document.execCommand("copy");

    /* Alert the copied text */
    alert("Password ("+copyText.value+") copied in clipboard ");

     /*Remove text field selection*/
    $("#"+containerid).blur();
}

$(document).ready(function () {
    
  var user_old_password_status = '<?php echo $old_password_status?>';
  var new_password_string  = '<?php echo $new_password_string?>';
  var isLoggedIn = '<?php echo $isLoggedIn;?>';
  var username = '<?php echo $isLoggedIn;?>';

  if(user_old_password_status == '1' && new_password_string !='') {
    $("#login-form").hide();
    $("#password-information").show();
  }

  $(".confirm-password-saved").click(function(){
      /*Save new password string in clipborad memory*/
      var copyText = document.getElementById('password_string_popup');
      copyText.select();
      document.execCommand("copy");
      $("#password_string_popup").blur();
      $('#input-password').val(new_password_string);
      $("#password-information").hide();
      $("#login-form").show();
  })

    $('.change_password').click(function() {
  		
      $.ajax({
              url: "index.php?route=common/dashboard/changePassword&token=<?php echo $token; ?>",
              type: "get",
              dataType: "json",
              beforeSend: function() {
                $('#change-password').html('Loading....');
              },
              success: function(json) {
                var innerHtml = '<span style="color:red">'; 
                     innerHtml += 'Save your new password';
                     innerHtml += '<br>';
                     innerHtml += 'It will never show you again';
                    innerHtml += '</span>';
                    innerHtml += '<br>';
                    innerHtml += '<input style="width:100px; border:0px; color:green " type="text" value="'+json.password+'" id="password_string">'
                    innerHtml += '<i style="padding-left:10px; color:#1e91cf; font-size:15px; cursor:pointer;" title="Copy to clipboard" class="fa fa-copy" onClick=copyToClipboard("password_string") aria-hidden="true"></i>';
                  $('#change-password').html(innerHtml); 
              },
              error: function(xhr, ajaxOptions, thrownError) { 
                var string = xhr.responseText.replace(/<b>/g,'');
                    string = string.replace(/<\/b>/g,'');
                    alert("Error occure during request processing ::\n"+string);
              }                 
            });
    });

  ///////////////// common_search_orders_button
  $('#common_search_orders_button').click(function(){

    var filter_order_no    = $('#filter_order_no').val();
    var filter_customer    = $('#filter_customer').val();
    var filter_customer_id    = $('#filter_customer_id').val();
    var filter_city        = $('#filter_city').val();
    var filter_tracking_no = $('#filter_tracking_no').val();
    
    if (filter_customer=='' && filter_order_no=='' && filter_city=='' && filter_tracking_no== '' && filter_customer_id=='') {
      alert('Please fill atleast one value in order number or customer name/email/phone or customer id or city.');
      return false;
    }else{

      var url = 'index.php?route=sale/order&token=<?php echo $token; ?>';
      
      if (filter_order_no) {
        url += '&filter_order_no=' + encodeURIComponent(filter_order_no);
      }
      
      if (filter_customer) {
        url += '&filter_customer=' + encodeURIComponent(filter_customer);
      }

      if (filter_customer_id) {
        url += '&filter_customer_id=' + encodeURIComponent(filter_customer_id);
      }
      
      if (filter_city) {
        url += '&filter_city=' + encodeURIComponent(filter_city);
      }

      if (filter_tracking_no) {
        url += '&filter_tracking_no=' + encodeURIComponent(filter_tracking_no);
      }

      window.location.href = url;
    }

  });
  //redirect to order listing page with filter data whan press enter
  $("#filter_order_no,#filter_customer,#filter_city,#filter_tracking_no").keydown(function (e) {        
        
      if (e.keyCode==13) {

        var filter_order_no    = $('#filter_order_no').val();
        var filter_customer    = $('#filter_customer').val();
        var filter_customer_id    = $('#filter_customer_id').val();
        var filter_city        = $('#filter_city').val();
        var filter_tracking_no = $('#filter_tracking_no').val();

        if (filter_customer!='' || filter_order_no!='' || filter_customer_id!='' || filter_city!='' || filter_tracking_no!='') {

          var url = 'index.php?route=sale/order&token=<?php echo $token; ?>';     
    
          if (filter_order_no) {
            url += '&filter_order_no=' + encodeURIComponent(filter_order_no);
          }
          
          if (filter_customer) {
            url += '&filter_customer=' + encodeURIComponent(filter_customer);
          }
          
          if (filter_customer_id) {
            url += '&filter_customer_id=' + encodeURIComponent(filter_customer_id);
          }

          if (filter_city) {
            url += '&filter_city=' + encodeURIComponent(filter_city);
          }

          if (filter_tracking_no) {
            url += '&filter_tracking_no=' +encodeURIComponent(filter_tracking_no);
          }

          window.location.href = url;

        }
      }
  }); 
});  
</script> 
