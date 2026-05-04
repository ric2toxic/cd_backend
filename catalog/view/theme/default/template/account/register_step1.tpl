<html>
<head>
<script src="catalog/view/javascript/jquery/jquery-2.1.1.min.js"></script>

<link href="catalog/view/theme/default/javascript/jasny-bootstrap/css/jasny-bootstrap.min.css" rel="stylesheet" media="screen" />

<link href="catalog/view/javascript/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen" />

<script src="catalog/view/javascript/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>

<link href="catalog/view/javascript/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
<!--<link href="//fonts.googleapis.com/css?family=Open+Sans:400,400i,300,700" rel="stylesheet" type="text/css" />-->
<link href='https://fonts.googleapis.com/css?family=Roboto:400,100,300,700,900 +' rel='stylesheet' type='text/css'>

<script src="catalog/view/theme/default/javascript/jasny-bootstrap/js/jasny-bootstrap.min.js" type="text/javascript"></script>
<link href="catalog/view/theme/default/stylesheet/stylesheet.css" rel="stylesheet" />
<link href="catalog/view/theme/default/stylesheet/stylesheet.css" rel="stylesheet" />
<link href="catalog/view/theme/default/stylesheet/expand-search/component.css" rel="stylesheet" />
</head>

<body>
<div id="signup">
    <div class="container-fluid nopadding  col-xs-12 col-sm-12 ">
        <div class="row">
            <div class="col-md-8 col-xs-8 col-sm-6">
                <div class="tab-panels col-xs-12 col-sm-12  ">
                    <ul class="tabs">
                        <li rel="panel1" class="active">Sign Up</li>
                        <li rel="panel2">LogIn</li>
                    </ul>

                    <div id="panel1"  Class="panel active" >
                        <form id="formsignup" action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" >

                                <?php /* ?>
                                <div class="form-group required">
                                    <label class="col-sm-2 control-label" for="input-firstname"><?php echo $entry_firstname; ?></label>
                                    <div class="col-sm-10">
                                        <input type="text" name="firstname" value="<?php echo $firstname; ?>" placeholder="<?php echo $entry_firstname; ?>" id="input-firstname" class="form-control" />
                                        <?php if ($error_firstname) { ?>
                                        <div class="text-danger"><?php echo $error_firstname; ?></div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="form-group required">
                                    <label class="col-sm-2 control-label" for="input-lastname"><?php echo $entry_lastname; ?></label>
                                    <div class="col-sm-10">
                                        <input type="text" name="lastname" value="<?php echo $lastname; ?>" placeholder="<?php echo $entry_lastname; ?>" id="input-lastname" class="form-control" />
                                        <?php if ($error_lastname) { ?>
                                        <div class="text-danger"><?php echo $error_lastname; ?></div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <?php */ ?>

                                <div class="form-group required">
                                    <label class="control-label" for="input-name"><?php echo $entry_name; ?></label>
                                    <input type="text" name="name" id="input-name" class="form-control" />
                                    <?php if ($error_name) { ?>
                                    <div class="text-danger" style="position:absolute;"><?php echo $error_name; ?></div>
                                    <?php } ?>
                                </div>

                                <div class="form-group required">
                                    <label class="control-label" for="input-telephone"><?php echo $entry_telephone; ?></label>
                                    <input type="tel" name="telephone" id="input-telephone" class="form-control" />
                                    <?php if ($error_telephone) { ?>
                                    <div class="text-danger" style="position:absolute;"><?php echo $error_telephone; ?></div>
                                    <?php } ?>
                                </div>
                                <div class="form-group">
                                    <label class="control-label" for="input-email"><?php echo $entry_email; ?></label>
                                    <input type="email" name="email"  id="input-email" class="form-control" />
                                    <?php if ($error_email) { ?>
                                    <div class="text-danger" style="position:absolute;"><?php echo $error_email; ?></div>
                                    <?php } ?>
                                </div>
                                <?php if ($text_agree) { ?>
                                <div class="buttons" >

                                    <input type="submit" value="<?php echo $text_signup; ?>" class="btn btn-primary" id="newbtn" />

                                </div>
                                <?php } else { ?>
                                <div class="buttons">

                                    <input type="submit" value="NEXT" class="btn btn-primary" />

                                </div>
                                <?php } ?>

                        </form>
                    </div>

                    <div id="panel2"  class="panel" >
                        <form id="login" name="loginform" action="<?php $actionlogin; ?>" method="post" enctype="multipart/form-data">
                            <fieldset id="account">
                            <div class="form-group">
                                <label class="control-label" for="input-email"><?php echo $entry_email; ?></label>
                                <input type="text" name="email" value="<?php echo $email; ?>" id="input-email" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="input-password"><?php echo $entry_password; ?></label>
                                <input type="password" name="password" value="Password"  id="input-password" class="form-control" />
                                <a href="#" id="forgot"><?php echo $text_forgotten; ?></a> </div>
                            <input type="button" id="newbtn2" value="<?php echo $text_login; ?>" class="btn btn-primary" />
                            <input type="hidden" name="redirect" value="Redirect" />
                                </fieldset>
                        </form>
                    </div>

                    <div class="forgotten">
                       <a href="#" class="back_login"> <i class="fa fa-arrow-left back_icon"></i> </a>
                        <form id="forgots" action="http://localhost/wholesale-box-opencart/index.php?route=account/forgotten" method="post" onsubmit="return validateforgotForm()" enctype="multipart/form-data" class="form-horizontal">


                                <div class="form-group required">

                                    <label class="control-label" for="input-email"><?php echo $entry_email; ?></label>
                                    <input type="text" name="email" id="inputforgotten-email" class="form-control" />
                                </div>
								<div class="form-group forgotbtnDiv">
									<input type="button" value="<?php echo $button_continue; ?>" class="btn btn-primary" id="forgotbtn" />
								</div>


                        </form>
                    </div>

                </div>
            </div>

            <div class="col-md-8 hidden-xs col-sm-6">
                <div id="right-container" class="col-md-10">
                    <i class="fa fa-times cross_icon" id="closeimage"></i>
                    <h3 style="color: white; margin-top: 30%; margin-left: 9%;">Why Should Shops Buy From WholesaleBox ?</h3></br>
                    <ul id="ul">
                        <li style="color: white; font-size: large;">Buy directly at Factory Rates</li></br>
                        <li style="color: white; font-size: large;">Easy 48 hour return policy</li></br>
                        <li style="color: white; font-size: large;">Cash on Delivery</li></br>
                        <li style="color: white; font-size: large;">Door Delivery</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>



</div>



<script type="text/javascript">
            $(function(){
                      $('.tab-panels .tabs li').on('click', function(){

                          $('.tab-panels .tabs li.active').removeClass('active');
                          $(this).addClass('active');

                          // find the panel
                          var paneltoshow = $(this).attr('rel');

                          // hide current panel

                          $('.tab-panels .panel.active').slideUp(300, function() {

                              $(this).removeClass('active');

                              $('#'+paneltoshow).slideDown(300, function(){

                                  $(this).addClass('active');

                              });
                          });
                      });
                    });



</script>

<script type="text/javascript">
        function validateforgotForm(){
            var eemail = document.forms["forgots"]["email"].value;
            if(eemail == null || eemail == ""){
                alert("Required Email");
                return false;
            }
        }

</script>

<script type="text/javascript">


$("a#forgot, a.back_login").click(function () {
    $(".panel.active").toggle();
    $(".forgotten").toggle();
    $("ul.tabs").toggle();

});

    $("#newbtn2").click(function(e){ 
       // e.preventDefault();
        var email = $('form#login #input-email').val(); 
        var passd = $('form#login #input-password').val();
        $.ajax({
            url: 'index.php?route=account/login/ajaxLogin',
            type: 'post',
            data: 'email=' + email + '&password=' + passd,
            dataType: 'json',
            beforeSend: function() {
                $('input#newbtn2').val('loading');
            },
            complete: function() {
                $('input#newbtn2').val('Login');
            },
            success: function(json) { 
                if(json['redirect']){
                    parent.jQuery.fancybox.close();
                    window.location.replace(json['redirect']);
                }else{
					$("div.alert-warning").remove();
                    $('form#login').before('<div class="alert alert-warning"><i class="fa fa-error-circle"></i> ' + json['errors'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                }
            }

        });
    });

    // new closing Icon

    $("#closeimage").click(function(){
            parent.jQuery.fancybox.close();
    });
</script>

<script type="text/javascript">
    $("#forgotbtn").click(function(e){
        // e.preventDefault();
        email = $('form#forgots #inputforgotten-email').val();
        $.ajax({
            url: 'index.php?route=account/forgotten/ajaxforgot',
            type: 'post',
            data: 'email=' + email,
            dataType: 'json',
            beforeSend: function() {
                $('input#forgotbtn').val('loading');
            },
            complete: function() {
                $('input#forgotbtn').val('Continue');
            },
            success: function(json) {
                if(json['redirect']){
                    parent.jQuery.fancybox.close();
                    window.location.replace(json['redirect']);
                }else{
					$("div.alert-warning").remove();
                    $('form#forgots').before('<div class="alert alert-warning"><i class="fa fa-error-circle"></i> ' + json['errors'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                }
            }

        });
    });
</script>

</body>
</html>
