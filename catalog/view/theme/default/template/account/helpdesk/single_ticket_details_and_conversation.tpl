<!DOCTYPE html  PUBLIC>
<!--[if IE]><![endif]-->
<!--[if IE 8 ]><html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" class="ie8"><![endif]-->
<!--[if IE 9 ]><html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" class="ie9"><![endif]-->
<!--[if (gt IE 9)|!(IE)]><!-->
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>">
<!--<![endif]-->
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title><?php echo $title; ?></title>
    <base href="<?php echo $base; ?>" />
    <?php if ($description) { ?>
    <meta name="description" content="<?php echo $description; ?>" />
    <?php } ?>
    <?php if ($keywords) { ?>
    <meta name="keywords" content= "<?php echo $keywords; ?>" />
    <?php } ?>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <?php if ($icon) { ?>
    <link href="<?php echo $icon; ?>" rel="icon" />
    <?php } ?>
    <?php foreach ($links as $link) { ?>
    <link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>" />
    <?php } ?>
    <link rel="alternate" hreflang="x-default" href="<?php echo $co_store.$request_uri;?>" />
    <link rel="alternate" hreflang="en-in" href="<?php echo $in_store.$request_uri;?>" />
    <link rel="dns-prefetch" href="//fonts.googleapis.com"/>
    <link rel="dns-prefetch" href="//www.googletagmanager.com"/>

    <link href="//fonts.googleapis.com/css?family=Roboto" rel="stylesheet" />
    <link href="<?php echo LOCAL_CDN_URL_SSL.COMMON_CSS; ?>" rel="stylesheet">
<style type="text/css">
.affix {
     top: 0;
     width: 100%;
 }
.affix + .container-fluid {
     padding-top: 70px;
 } 
.bg_gray {background: #233c98;}
.navbar {min-height: 50px;}
.slider_navgation {display: block;float: left;width: 250px;}
.carousel-inner {overflow: hidden;position: relative;width: 100%;}
.b2bVideo {display: inline-block;float: right;padding: 7px 7px 20px;}
.offerBox {display: block;min-height: 130px;width: 180px;}
.home-product-list {width: 16.6%;}
.c-product-card__gallery{width: 42px;}
.steps .procedure {width: 77%;}
.steps .procedure .procedure_img_box {height: 90px;width: 90px;}
.tab_list li a h4 {font-size: 1.2em;}
.stats-bar ul li a { font-size: 24px;padding: 25px 30px;}
.cart_page {margin: 6% 0;}
.cart_statement_pree_load{width: 25%;min-height: 300px;}
.cart_full_box .cart_box .cart_main_title{border-radius: 0; padding: 17px 8px; margin-top:7px;}
.panel-body .cart_table .cart_table_height{ min-height: 400px; }
.li_position{position: absolute; list-style: none;}
.dropdown-toggle .menu_bar {left: -1px;min-width: 254px;top: 57px; padding: 0px;}
.sticky_menu_top_btn .menu_bar {left: -1px;min-width: 254px;top: 57px; height: 70px; }
.header_navigation li .nav_icon_bar{display: none;left: 0px;position: relative;list-style: none; height: 50px; width: 50px; padding: 10px;
    padding-bottom: 0px;}
.header_navigation li .nav_icon_bar:hover{background-color: transparent;}
.header_navigation li .nav_icon_bar .icon-bar{background-color: #17319f;border-radius: 1px;display: block;height: 4px; width: 28px;}
.header_navigation li .nav_icon_bar .icon-bar + .icon-bar {margin-top: 4px;}
.sticky_menu_top_btn {left: 23px;width: 50px;padding:0px; position: fixed; top: 13px; z-index: 999;}
.inner_page_menu_bar{ position: absolute; top: 33px; z-index: 999;display: block!important;left: 23px;width: 50px;padding: 0px; }
.inner_page_menu_bar .nav_icon_bar{display: block!important;}
</style>

<?php if ($international_store == 1) { ?>
    <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-MBB945');</script>
    <!-- End Google Tag Manager -->
<?php } else { ?>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','GTM-TRP9H5');</script>
<!-- End Google Tag Manager -->
<?php } ?>

</head>

<body class="loaded my_account_body">

<?php if ($international_store == 1) { ?>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MBB945"
                      height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
<?php } else { ?>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TRP9H5"
                  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript)    -->
<?php } ?>

<div id="myProgress">
  <div id="myBar"></div>
</div>
<section id="header_box"></section>


  <div class="container-fluid width_fix">
    <div class="clearfix"></div>
    <div class="row my_account"><?php echo $column_left; ?>
          <?php if ($column_left && $column_right) { ?>
            <?php $class = 'col-sm-6'; ?>
            <?php } elseif ($column_left || $column_right) { ?>
            <?php $class = 'col-sm-9'; ?>
            <?php } else { ?>
            <?php $class = 'col-sm-12'; ?>
            <?php } ?>
            <?php echo $column_right; ?>
          
        <div id="content" class="<?php echo $class; ?> box_shadow_none">
                                                                              
               <?php echo $content_top; ?>                                                    

               <h1 class="account_title"><?php echo $heading_title; ?></h1>
            
          
                <div class="buttons clearfix">
                  <div class="pull-left"><a href="<?php echo $back; ?>" class="btn btn-default"><?php echo $button_back; ?></a></div> 
                <div class="pull-right"><a href="<?php echo $create; ?>" class="btn btn-continue"><?php echo $button_new_address; ?></a></div>
                </div>
<!-- 
                <div class="helpdesk_alert" >        
                             
                   <b><?php  if(isset($being)){ echo $being;  } ?> </b>
                      <?php if(isset($since)){ echo $since; } ?>
             
                   </div>   -->

                   <h3 class="word_break" ><?php echo '#'.$output['ticket_id'].' '.$output['subject'] ;?></h3>

                   <div class="row">

                      <div class="col-sm-6"> 
                          <div class="helpdesk_letter">
                          <?php echo $letter_icon; ?> 
                          </div>  
                          <b> &nbsp;<?php print_r($output['ticket_creater']);?></b><?php echo ' '.$report; ?>
                          
                      </div>  

                        <div class="col-sm-2  pull-right ">                         
                          
                          <?php if($output['status'] !== "CLOSED"){ ?>
                             <a href="<?php echo $close_ticket.'&ticket_id='.$ticket_id; ?>" class="btn btn-continue"> 
                          <?php echo $ticket ?> </a>
                          <?php
                          }
                          ?>  
                        </div> 
                          <div class="clearfix"></div>  

                         <div class="helpdesk_reply">
                          <p>  <?php print_r(nl2br($output['description'])); ?> </p>
                            
                            <?php if(isset($output['attachment'])&& !empty($output['attachment'])){ 
                              foreach ($output['attachment'] as $attachment_single) {
                              
                              ?> 
                              <div class="helpdesk_attachment">  
                                            <div class="helpdesk_attachment_type">             
                                              <i class="fa fa-file-o" style="font-size:36px;"></i>
                                              <?php $ext = pathinfo($attachment_single['attachment_name'], PATHINFO_EXTENSION);    
                                                if(strlen($ext) <= 3){  ?>
                                              <span class="helpdesk_file_type">                                      
                                               <?php echo $ext; ?> 
                                             </span>
                                            <?php } ?>
                                            </div> 
                                            <div class="helpdesk_attach_content">
                                                <div> 
                                                  <a href="<?php print_r($attachment_single['path']); ?>" class="filename" download=""><?php echo $attachment_single['attachment_name'] ?>  </a>
                                              </div>
                                             <div>(<?php $size =$attachment_single['size'];
                                                    $kb = $size/1024;echo round($kb,2);?> KB)
                                             </div>
                                            </div>
                                          </div>                              
                          <?php } } ?>  
                                
                        </div> <!-- helpdesk reply --> 
          
      

                   </div> <!-- row closing -->  
                         <hr>           
                          <div class= "conversation_reload">            
                             <!-- Conversation between agent and customer --> 
                             <!-- $wsb_cust_data['fresh_id'] -->
                            <?php if(!empty($conversation)){ ?>            
                                    <?php foreach($conversation as $record) {  ?>

                                  <div class="row">                                        
                                    <div class="col-sm-6"> 
                                        <?php
                                      date_default_timezone_set('Asia/Kolkata');

                                      $now = date('Y-m-d h:i:s'); //current time                  
                                  
                                      $created = date('Y-m-d h:i:s',strtotime($record['created_at']));
                                      
                                      $diff= date_diff(date_create($now),date_create($created));         
                                  
                                      // print_r($diff->i.'<br>'. $diff->s);               

     

                                      if($diff->d > 0){                    
                                      $time ='Said'.' '.$diff->d.(($diff->d > 1) ? ' days' : ' day ').' '.$diff->h.' '.(($diff->h > 1) ? 'hours' : 'hour');

                                       }elseif($diff->h > 0){ 
                                          $time ='Said'.' '.$diff->h.(($diff->h > 1) ? ' hours' : ' hour ').' '.$diff->i.' '.(($diff->i > 1) ? 'minutes' : ' minute ');            
                                       }elseif ($diff->i > 0) {
                                          $time ='Said' .' '.$diff->i.' '.(($diff->i > 1) ? 'minutes' : 'minute'). ' '.$diff->s.' '.(($diff->s > 0 ) ? 'seconds':'second');                        
                                       }elseif ($diff->h == 0 && $diff->i == 0 && $diff->s > 0 ) {
                                          $time ='Said'. ' '.$diff->s.' '.(($diff->s > 1) ? 'seconds' : 'second'); 
                                       }       


                                       ?>                                         

                                      <?php   
                                      if(!empty($record['agent_name'])){
                                        $user_name = $record['agent_name'];
                                      }else{
                                        $user_name = $record['customer_name'];
                                      }
                                      ?>

                                      <div class="helpdesk_letter">
                                            <?php  
                                            $capital = ucwords($user_name);
                                            $first_letter = substr($capital, 0,1);
                                            $letter = $first_letter; 
                                            echo $letter;
                                            ?>
                                      </div>    
                                          <b>&nbsp;<?php echo $user_name; ?></b>
                                          <?php 
                                          if(!empty($time)){
                                            echo ', '.$time.' ago'; 
                                          }
                                          ?>

                                    </div> <!-- col-sm-6 closing -->
                                        

                                       <div class="clearfix"></div>  

                                    <div class="helpdesk_reply">                                        
                                        <p><?php print_r(nl2br($record['body'])); ?> </p>

                                        <?php if(!empty($record['attachment'])){

                                          foreach($record['attachment'] as $attachment_data){

                                         ?>  
                                        
                                          <div class="helpdesk_attachment">  
                                            <div class="helpdesk_attachment_type">
                                              <i class="fa fa-file-o" style="font-size:36px;"></i>

                                                <?php $ext = pathinfo($attachment_data['attachment_name'], PATHINFO_EXTENSION);    
                                                  if(strlen($ext) <= 3){  ?>
                                                <span class="helpdesk_file_type">                                      
                                                 <?php echo $ext;  ?>                                                                          
                                               </span>
                                              <?php } ?>
                                              </div> 
                                              <div class="helpdesk_attach_content">
                                                <div> 
                                                
                                                  <a href="<?php print_r($attachment_data['path']); ?>" class="filename" download=""><?php echo $attachment_data['attachment_name'] ?>  </a>
                                              </div>
                                             <div>(<?php $size = $attachment_data['size'];
                                                    $kb = $size/1024;echo round($kb,2);?> KB)
                                             </div>
                                            </div>
                                          </div>                              
                                         <?php } }?>
                                    </div><!-- helpdesk_reply closing  -->
                                </div>       

                              <?php }?>                            
                              <?php }?>        
                            </div>  
                            <?php  
                              // echo "<pre>"; print_r($output); die();
                              $limit = 5;
                              if(isset($output['conversation_pagination']) && $output['conversation_pagination']['data_count'] > $limit){ ?>
                                <div align="center" >
                                   <button type="button" class="btn btn-link"  id="loadMoreData" data-page="<?php echo $output['conversation_pagination']['next_page']; ?>" data-count="<?php echo $output['conversation_pagination']['data_count']; ?>" data-ticket="<?php echo $output['ticket_id']; ?>">Load More...
                                   </button>
                                </div>
                              <?php  } ?>     

                        <!-- Reply customer  --> 

                        <div class="row" id="reply_div"  >

                            <div class="col-sm-6" > 
                                <div class="helpdesk_letter">
                                <?php echo $customer_letter; ?> 
                                </div>  
                                <b> &nbsp;<?php print_r($firstname.' '.$lastname);?></b>
                            </div>  

                            <div class="clearfix"></div>  

                            <div class="helpdesk_reply">
                                      
                             <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" class="form-horizontal" id="reply_form"> 
                                   
                                  <div class="form-group " >
                                                                          
                                      <textarea class="form-control required" name="body" rows="3"  id="input-description" required/></textarea>
                                      
                                      <input type="hidden" name="ticket_id" value="<?php echo $ticket_id; ?>">         
                                  </div>  
                                                 
                                  <div class="form-group ">
                                     <input id="attachment" name ="attachment" type="file" class="file">
                                  </div>  

                                  <div class="form-group ">
                                    <input type="submit" value="<?php echo $button_continue; ?>" id= "reply_form_submit" class="btn btn-continue"  />
                                  </div>  
                              </form>                                                                                                                
                                                        
                            </div> <!-- helpdesk reply --> 
              
                        

                         </div> <!-- row closing --> 
                 

 


        </div> <!-- content closing  -->         
    </div>    <!-- row closing  -->

      <?php echo $content_bottom; ?>



</div><!--  "container account_page_bg" closing  -->



<section id="footer_box"></section>
<section id="login_box"></section>
<section id="opt_box"></section>
<section id="register_box"></section>
<section id="success_box"></section>
<div class="global-bg-layer"></div>
<div class="global-ajax-loader"></div>
<!-- jQuery -->

<script src="<?php echo LOCAL_CDN_URL_SSL.COMMON_VENDOR_JS; ?>"></script>
<script src="<?php echo LOCAL_CDN_URL_SSL.COMMON_REACT_JS; ?>"></script>

<script>

  var header_language     = <?php echo $header_language; ?>;
  var footer_language     = <?php echo $footer_language; ?>;
  var login_language      = <?php echo $login_language ; ?>;
  var international_store = <?php echo $international_store; ?>;

<?php if (isset($this->session->data['custom_store'])) {
        if ($this->session->data['custom_store'] == 'single') { ?>
var custom_store        = header_language.text_wholesale_store;
<?php  } else { ?>
var custom_store        = header_language.text_singles_store;
<?php  } }else { ?>
var custom_store        = header_language.text_singles_store;
<?php } ?>

ReactDOM.render(React.createElement(Header, {custom_store:custom_store, language:header_language, international_store:international_store},null ), document.getElementById('header_box'));
ReactDOM.render(React.createElement(Footer, {language:footer_language, international_store:international_store},null ), document.getElementById('footer_box'));
ReactDOM.render(React.createElement(Otpform, {language:login_language, international_store:international_store},null ), document.getElementById('opt_box'));
ReactDOM.render(React.createElement(Login, {language:login_language, international_store:international_store},null ), document.getElementById('login_box'));
ReactDOM.render(React.createElement(Register, {language:login_language, international_store:international_store},null ), document.getElementById('register_box'));
ReactDOM.render(React.createElement(Success, {language:login_language, international_store:international_store},null ), document.getElementById('success_box'));

</script>

<script type="text/javascript">
   $(document).ready(function() {
    $('.toggle_icon').click(function(){
     $(this).find('i').toggleClass('fa-plus-square-o fa-minus-square-o');
    });

    $('input[placeholder], textarea[placeholder]').not("input.serch_input_box, input.subscibe_input").placeholderLabel();
   // navcation Icons(header)
      $(window).on('scroll',function(){
          if($('.full_header').hasClass('affix')){
            $('.header_navigation').find('.nav_icon_bar_bottem').hide();
            $('.header_navigation').find('li').eq(0).removeClass('inner_menu_icon');
          }else{
            $('.header_navigation').find('.nav_icon_bar_bottem').show();
            $('.header_navigation').find('li').eq(0).addClass('inner_menu_icon');
          }
        });
       $("header").affix({offset: {top: $(".sub_navbar").outerHeight(true)} });

 });
</script>



<script>

    /*
       Load more content with jQuery
      */
        $("#loadMoreData").on('click', function (e) {
            e.preventDefault();
            var element = $(this);
            var page = $(this).attr('data-page');
            var tickets = $(this).attr('data-ticket');
            var load_url = '<?php echo $conversation_load_url; ?>';            
            var limit = 5;
            var offset = page * limit;
            var send_data = "ticket_id=" + tickets +"&page=" + page ;
            var total_count = $(this).attr('data-count');

            $.ajax({
                type: "POST",
//                url: 'http://localhost/wsb-helpdesk-api/api/agent/getAllAgents',
                url: load_url,
                data: send_data, // serializes the form's elements.
                dataType:'html',
                beforeSend: function(request) {
                    request.setRequestHeader("api_key", '0b01c347-ceb4-4f7b-bc13-b9e4ccffdb44');
                },
                success: function (data) {
                    var html = '';
                    if (data == ''){
                        $("#loadMoreData").hide();
                    }
                    else {
                        $('.conversation_reload').html(data);

                        if (total_count <= offset){
                            $("#loadMoreData").hide();
                        }
                        element.attr('data-page', (parseInt(page) + 1));
                    }
                }
            });
        });



$("#reply_form_submit").click(function(e) {
e.preventDefault();
  $('#reply_form').find('.required').each(function(){    

      if($(".required").val().trim() == ''){

          $(".required").addClass('error');
          $(this).css("border-color","red");
          $('.error').show(); 

       }
       else{
          $("#reply_form").submit();    
       }
  }); 
     
});

$(".required").keyup(function(e) {
e.preventDefault();
   

      if($(this).val().trim() == ''){

          $(".required").addClass('error');
          $(this).css("border-color","red");

           

       }
       else{
          $(".required").removeClass('error')
            $(this).css("border-color","#ccc");            
       }

     
});


var scroll_to_reply = '<?php echo $scroll_to_reply; ?>';

$(document).ready(function() {
  if(scroll_to_reply !== ''){
    //alert(scroll_to_reply);
     var a =  $("#reply_div").offset().top;
     var slideto=a;
     if(a > 500){
      var slideto = a-500;
     }
     
    $('html, body').animate({
          scrollTop: slideto 
    }, 2000);
  }
});



</script>

</body>

</html>
