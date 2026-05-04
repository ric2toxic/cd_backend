<link href="catalog/view/theme_new_mobile/default/stylesheet/all-library.css" rel="stylesheet" media="screen" />
<script src="catalog/view/javascript/jquery/jquery-3.2.0.min.js" type="text/javascript"></script>
<script src="catalog/view/theme_new_mobile/default/javascript/bootstrap.min.js"></script>

  
<style type="text/css">
    .account-credit_application_form{
        background: #fff; 
    }
    .clear{
        clear: both;
    }
    .create_app_wrapper{
        padding-top: 40px;
    }   
    .app_img img{
       width: 100%;
       margin: auto;
    }
    .create_app_des{
        padding: 50px 0;
    }
    .create_app_des p{
        color: #233c98;
        font-size: 40px;
    }
    .app_button button{
        background: #f13041;
        width: 100%;
        padding: 15px;
        font-size: 35px;
        text-transform: uppercase;
        box-shadow: 11px 14px 39px -9px #ccc;
        color: #fff;
    }
    .app_button button.active.focus, 
    .app_button button.active:focus, 
    .app_button button.focus, 
    .app_button button:active.focus, 
    .app_button button:active:focus, 
    .app_button button:focus {
        outline: 0;
    }
    #success_mail{
        text-align: center;
        font-size: 20px;
        display: none;
    }
    #error_msg{
        color : red;
    }
    .mob_no p {
        margin-top: 5px
    }
    
    /*css for bootstrap model */
    .create_app_wrapper .modal-dialog {
        margin: 20% auto;
    }
    .create_app_wrapper .modal-title{
        font-size: 50px;
    }
    .create_app_wrapper .modal-body p{
        font-size: 40px;
    }
    .create_app_wrapper .mobile{
        font-size: 40px;
        height: auto;
    }
    .create_app_wrapper #error_msg{ 
        font-size: 33px;
    }
    .create_app_wrapper .closefirstmodal{
        font-size: 40px;
        padding: 0px 19px;
        background: #17319f;
        color: #fff;
    }
    .create_app_wrapper .modal-body{
        position: relative;
        padding: 50px 15px;
    }
    .create_app_wrapper #loading img{
        width: 40px;
        margin: 10px;
    }
    .create_app_wrapper .close{
        font-size: 40px;
    }
    .create_app_wrapper .close{
        font-size: 40px;
    }
    .create_app_wrapper #success_mail{
        font-size: 37px;
        color: green;
    }
    .create_app_wrapper #success_mail img{
        width: 30px;
    }
    
    
    
</style>
<script type="text/javascript">
    function validateSubscribeForm() {
            
            var x = $("#mobile").val();
            var name = $("#name").val();
            
            var status = true;
            var msg = '';
            
            if( x == ''){
              msg = "Enter value";
              status = false;
            }else
            if(isNaN(x)||x.indexOf(" ")!=-1){
                msg = "Enter numeric value";
                status = false;
            }else
            if (x.length!=10){
                msg = "Enter 10 digits";
                status = false;
            }else
            if (x.charAt(0)=="0"){
                msg = "It should not start with 0";
                status = false;
            }
            
            
            if(status == false){
                $('#error_msg').html(msg);
                return false
            }else{
                $('#myModal').modal('hide');
                var ajax_url = 'index.php?route=account/credit_application_form/sendMailForCallBackRequest&mobile='+x+'&name='+name
                $.ajax({
                    url: ajax_url,
                    beforeSend: function() {
                        $('#loading').html('<img src="http://cdnimages.net/loader.gif" />');
                    },
                    success: function( data ){
                        $('#error_msg').html('');
                        $('#loading').html('');
                        $("#success_mail").fadeTo(2000, 500).slideUp(500, function(){
                            $("#success_mail").slideUp(500);
                        });
                    }
                }); 
                
            }
            
        }
</script>
<div class="">
  <div class="row">
    <div id="content" class="<?php echo $class; ?>"><?php echo $content_top; ?>
      
      <div class="create_app_wrapper container">
        <div class="create_app row">
            <div class="app_img col-xs-12">
                 <img src="https://cdnimages.net/img/dw=487,dh=116,q=90/wholesalebox-credit-logo.jpg" alt="wholesalebox-logo" class="img-responsive">
            </div>     
            <div class="clear"></div>
            <div id="success_mail" class="alert alert-success fade in alert-dismissible" style="margin-top:18px;">
                <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
                Thank you! We shall call you shortly.
            </div>
        </div>
        <div class="create_app_des">
            <p>You are just few steps away from 0% interest credit limit from &#x20b9 25,000 to &#x20b9 3,00,000 upto 40 days at Wholesatebox!
            </p>
        </div> 
            
        <div class="row app_button">
            <div class="col-xs-12">
                <a href="tel:01414049163">
                <button type="button" >
                    <i class="fa fa-phone" aria-hidden="true"></i>
                    &nbsp;
                    Call Us - 01414049163
                </button></a>
            </div>
            <div class="col-xs-12" style="height:50px;"></div> 
            <div class="col-xs-12"> 
                <button  data-toggle="modal" data-target="#myModal">
                    <i class="fa fa-clock-o" aria-hidden="true"></i>
                    &nbsp;
                    Call back request
                </button>
                <span id="loading"></span>  
            </div>
            
        </div> 
        
        <div id="myModal" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Call back request</h4>
                    </div>
                    <div class="modal-body">
                        <div class="col-xs-5 mob_no">
                            <p>Mobile No.</p>
                        </div>
                        <div class="col-xs-7">
                            <input class="mobile form-control" type="tel" id="mobile" maxlength="10" value="<?php echo $mobile; ?>">
                            <input type="hidden" id="name" value="<?php echo $name; ?>">
                            <div id="error_msg" class="clearfix"></div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="modal-footer">
                        <button class="closefirstmodal" type="button" onclick="validateSubscribeForm()">Submit</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
    
</div>
