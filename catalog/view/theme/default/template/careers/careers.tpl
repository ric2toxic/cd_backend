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
.carrer .modal-dialog {
    width: 750px;
}
.contact_section .padding_left_right{padding:0px 5px;}
.para_heading{color: #17319f; }
.career_image img {align-self: center;margin: auto;}
.career_content_text{ font-size: 16px; line-height: 1.8; }
.career_title{  line-height: 0.8; text-align: center;}
.career_title span::before {margin-right: 15px; right: 100%;}
.career_title span::after {    border-top: 1px solid #b6b6b6;    content: "";    height: 1px;    position: absolute;    top: 11px;    width: 70%;}
.career_title span::before {    border-top: 1px solid #b6b6b6;    content: "";    height: 1px;    position: absolute;    top: 11px;    width: 70%;}
.career_title  span::after {    left: 100%;    margin-left: 15px;}
.career_title span {display: inline-block; position: relative;}


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

<body class="loaded">

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
<div class="container width_fix">

<div class="career_image">
    <img src= " <?php echo $banner_image; ?>" alt="Wholesalebox" >
</div>

<div class="container">
    <div class="row">

                <div class="col-sm-12">
                    <div class="row">
                        <!-- contact saction(start) -->
                        <section class="col-sm-12 contact_section">
                            <h2 class="page_title career_title"><span>Why Join Us?</span></h2>
                            <div class="col-sm-9">
                                <div class="row">
                                    <p class="career_content_text"><b>Bored with formals? Going to office in Jeans and slippers seems like a dream? No time to enjoy and fatigued with same work? Looking for a job without any politics?</b><br><br>
                                        Welcome to WholesaleBox! Wear what you want to, work in your ways, live your dreams, explore yourself and enjoy working in the extremely friendly atmosphere! WholesaleBox consists of enthusiastic people and we are employing a number of people in different domains. From interns, youth to experienced people we hire all. The founders and other higher management will always treat you like your friends/colleagues and welcome all types suggestions, even crazy perspective too. We believe in team work without any conflicts, keeping everything transparent between employees and management. No doubt here you will get loads of opportunities to grow but on the same side, there is need to volunteer the responsibilities. </p><br>
                                </div>
                            </div>
                            <div class="col-sm-3 pull-right"><img src="<?php echo $six; ?>" alt="wholesalebox about" class="img-responsive about_img"></div>
                        </section>
                        <section class="col-sm-12 contact_section">
                            <div class="col-sm-3"><img src="<?php echo $one; ?>" alt="wholesalebox" class="img-responsive about_img"></div>
                            <div class="col-sm-9">
                                <div class="row">
                                    <h3 class="para_heading">WholesaleBox: an awesome place to work</h3>
                                    <p class="career_content_text">Thinking of why work with us? We are a startup and startups are the best place to learn, show your skills, creativity, and talent.  Of course to attract and keep the best talent we at WholesaleBox offer a competitive packages. But on the same side working with us is more than your salary. Entering at our company means you are going to be a key part of the extremely friendly atmosphere where you are given a freedom to express yourself. With a diverse team every day, you will grow, learn and accord every day. </p>
                                </div>
                            </div>
                        </section>
                        <section class="col-sm-12 contact_section">
                            <div class="col-sm-9">
                                <div class="row">
                                    <h3 class="para_heading">A Lively environment to work:</h3>
                                    <p class="career_content_text">We provide an environment where you can enjoy the work along with your bosses. There is always a zeal and determination to work and to build something different. Of course, money is not flowing like rivers but we are going to build something from scratch, fill you with a lot of positive energy and definitely reward you in long term. Also, we are party people. We celebrate every month every year and don't leave single reason to party! </p>
                                </div>
                            </div>
                            <div class="col-sm-3 pull-right"><img src="<?php echo $four; ?>" alt="wholesalebox" class="img-responsive about_img"></div>
                        </section>
                        <section class="col-sm-12 contact_section">
                            <div class="col-sm-3"><img src="<?php echo $three; ?>" alt="wholesalebox" class="img-responsive about_img"></div>
                            <div class="col-sm-9">
                                <div class="row">
                                    <h3 class="para_heading">Technology and innovation leaders</h3>
                                    <p class="career_content_text">We’re at the forefront of all clothing wholesalers.  Our team consists of group IITians and IIM's passed out founders and employees which makes our technical and management strong. Technically everything we use in the field is created, developed, and manufactured by WholesaleBox tech people. Our operations and sales are equally powerful because of good management and healthy atmosphere. </p>
                                </div>
                            </div>
                        </section>
                        <section class="col-sm-12 contact_section">
                            <div class="col-sm-9">
                                <div class="row">
                                    <h3 class="para_heading">Training and rise people</h3>
                                    <p class="career_content_text">We provide training to people whosoever required and on-the-job learning is a continuous process with WholesaleBox. We ensure you that soon you will build the skills needed to meet the demands of our customers and ultimately this will enhance your own skills too.We bestow training which improves critical thinking in a person and attitude of problem solving improves. The Communication, Creativity and innovation are the most important part of our training.</p>
                                </div>
                            </div>
                            <div class="col-sm-3 pull-right"><img src="<?php echo $two; ?>" alt="wholesalebox" class="img-responsive about_img"></div>
                        </section>
                        <section class="col-sm-12 contact_section">
                            <div class="col-sm-3"><img src="<?php echo $five; ?>" alt="wholesalebox" class="img-responsive about_img"></div>
                            <div class="col-sm-9">
                                <div class="row">
                                    <h3 class="para_heading">Dynamic work culture and definitely Career progression</h3>
                                    <p class="career_content_text">Being a startup we understand the demand of energy, new ideas, techniques, and solutions for the necessary requirements. Exciting challenges every now then, you get an exposure to work with different people, sometimes early responsibilities to make you strong. We provide you ample opportunities to grow and support you in whichever direction your ambitions as well as talents taking you. Many times you will be challenged but soon you be recognized.  </p>
                                </div>
                            </div>
                        </section>
                        <div class="clearfix"></div>
                        <h3  align="center">We value people for their competencies, and, as a company, we encourage fair employment practices PAN India and offer equal opportunities to all our employees.</h3>

                        <!-- contact saction(end) -->

                        <div class="clearfix"></div>

                    </div>
                </div>

        <div class="col-sm-12 career-page" id="content">
            <?php if(!empty($all_data)){ ?>
            <!-- <div class="top_heading"><strong><h3><?php //echo $text_top_heading; ?></h3></strong></div> -->
            <div class="panel-group" id="accordion">
                
                    <?php 
                    foreach($all_data as $detail){
                    ?>
                    <div class="col-sm-4 panel_line">
                        <div class="panel_jobs">
                            <!--     <div class="jobtitle_icons">
                            <img src="<?php echo HTTP_SERVER.'image/career/'.$detail['job_icon']; ?>">
                        </div> -->
                            <div class="job_heading">
                                <h3 class="panel-title">
                                    <a class="accordion-toggle" data-parent="#accordion">
                                         <?php echo $detail['title'];?> (<?php echo $detail['location'];?>)
                                    </a>
                                </h3>
                                <p> <?php echo $detail['job_type'];?></p>
                            </div>
                            <hr>
                            <div id="collapse<?php echo $detail['job_id'];?>">
                                <div class="panel-body category_text">
                                    <?php echo $detail['description']; ?>
                                    <?php /* if(!empty($detail['email_to'])){ ?>
                                        <p><?php echo $text_email_label; ?> <?php echo $detail['email_to'];?> </p>
                                    <?php } */ ?>
                                </div>
                                <div class="apply-buttons">
                                    <a id="apply_job<?php echo $detail['job_id'];?>" data-id="<?php echo $detail['job_id'];?>" class="btn btn-primary apply_job" data-toggle="modal" data-target="#apply_job_form<?php echo $detail['job_id'];?>">Apply Job</a>
                            </div>
                            </div>
                        </div>
                    </div>


<div id="apply_job_form<?php echo $detail['job_id'];?>" class="modal fade carrer" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><?php echo $title_popup; ?>"<?php echo $detail['title']; ?>"</h4>
      </div>
       <form class="form-horizontal" id="apply_job_form<?php echo $detail['job_id']; ?>" data-id="<?php echo $detail['job_id']; ?>" name="apply_job_form" action="<?php echo $form_action; ?>" method="POST" enctype="multipart/form-data">
      <div class="modal-body">
                                   
                                        <div class="col-sm-12">
                                            <input type="text" name="your_name" value="<?php //echo $searchTextValue; ?>" placeholder="<?php echo $text_name; ?>" id="input-name<?php echo $detail['job_id'];?>" class="form-control input-name"/>
                                            <span id="input-name-error<?php echo $detail['job_id'];?>" class=" error"></span>
                                        </div>
                                   
                                  
                                        <div class="col-sm-12">
                                            <input type="email" name="email" value="<?php //echo $searchTextValue; ?>" placeholder="<?php echo $text_email; ?>" id="input-email<?php echo $detail['job_id'];?>" class="form-control input-email"/>
                                            <span id="input-email-error<?php echo $detail['job_id'];?>" class=" error"></span>
                                        </div>
                                  
                                   
                                        <div class="col-sm-12">
                                            <input type="text" name="mobile"  value="<?php //echo $searchTextValue; ?>" placeholder="<?php echo $text_mobile; ?>" id="input-mobile<?php echo $detail['job_id'];?>" class="form-control input-mobile"/>
                                            <span id="input-mobile-error<?php echo $detail['job_id'];?>" class=" error"></span>
                                        </div>
                                   
                               
                                        <div class="col-sm-12">
                                            <input type="text" name="current_ctc"  value="<?php //echo $searchTextValue; ?>" placeholder="<?php echo $text_current_ctc; ?>" id="input-current_ctc<?php echo $detail['job_id'];?>" class="form-control input-current_ctc"/>
                                            <span id="input-current_ctc-error<?php echo $detail['job_id'];?>" class=" error"></span>
                                        </div>
                                   
                                 
                                        <div class="col-sm-12">
                                            <input type="text" name="expected_ctc"  value="<?php //echo $searchTextValue; ?>" placeholder="<?php echo $text_expected_ctc; ?>" id="input-expected_ctc<?php echo $detail['job_id'];?>" class="form-control input-expected_ctc"/>
                                            <span id="input-expected_ctc-error<?php echo $detail['job_id'];?>" class=" error"></span>
                                        </div>
                                   
                                
                                        <div class="col-sm-12">
                                            <textarea name="cover_letter" placeholder="<?php echo $text_cover_letter; ?>" id="input-cover-letter<?php echo $detail['job_id'];?>" class="form-control input-cover-letter" /><?php //echo $searchTextValue; ?></textarea>
                                            <span id="input-cover-letter-error<?php echo $detail['job_id'];?>" class=" error"></span>
                                        </div>
                                   
                                    <div class="uploading_resume" id="uploading_resume<?php echo $detail['job_id'];?>">
                                        <div class="col-sm-12">
                                            <input type="file" name="resume" data-value="<?php echo $detail['job_id'];?>" id="input-resume<?php echo $detail['job_id'];?>" class="input-resume"/>
                                        </div>
                                    </div>
                                    
                                        <div class="col-sm-12">
                                            <div class="upload-message" id="upload-message<?php echo $detail['job_id'];?>"></div>
                                            <span id="input-resume-error<?php echo $detail['job_id'];?>" class=" error"><?php //echo $error_file_extension; ?></span>
                                        </div>
                                  
                              </div>
                            <div class="modal-footer">
                                 <input type="hidden" name="job_id" value="<?php echo $detail['job_id']; ?>" class="btn btn-primary"/>
                                 <input type="hidden" name="member_email" value="<?php echo $detail['email_to']; ?>" class="btn btn-primary">
                                 <input type="hidden" name="uploading_resume" class="btn btn-primary uploading_data" id="uploading_data<?php echo $detail['job_id'];?>">
                                  <input type="submit"  value="Send" id="job_submit<?php echo $detail['job_id']; ?>" data-id="<?php echo $detail['job_id']; ?>" class="job_submit btn btn-primary"/>
                            </div>
                              </form>
                            </div>
                         </div>
                      </div>

                <?php }?>
            </div>
            <?php }else{ ?>
                <div class="empty_heading"><strong><h3><?php echo $text_empty; ?></h3></strong></div>
            <?php } ?>
        </div>
    </div>
</div>
</div>

<script type="text/javascript">
    $('.collapse').on('shown.bs.collapse', function(){
        $(this).parent().find(".glyphicon-plus").removeClass("glyphicon-plus").addClass("glyphicon-minus");
    }).on('hidden.bs.collapse', function(){
        $(this).parent().find(".glyphicon-minus").removeClass("glyphicon-minus").addClass("glyphicon-plus");
    });

    $(document).ready(function(){

        function containsAny(str, substrings){
        for (var i = 0; i != substrings.length; i++) {
            var substring = substrings[i];
            if (str.indexOf(substring) != - 1) {
                return substring;
            }
        }
        return null;
    }
        function phonenumber(inputtxt){
        var flag = 1;
        var phoneno = /^\d{10}$/;
        var mobile = inputtxt;
        var res = mobile.charAt(0);
        var result = containsAny(res, ["9", "8", "7"]);

        var flc = (mobile.match(new RegExp(res, "g")) || []).length;
        if(flc > 9){
            flag = 0;
        }
        if(!result){
            flag = 0;
        }
        if(flag == '1'){
            if(inputtxt.match(phoneno))
            {
                return true;
            }
            else
            {
//                $("div.removePopError").remove();
//                $("div.apply_job_form form").append("<div class='removePopError' style='color:#f03140;'>Please Enter Valid Mobile Number</div>");
                $('#input-mobile-error'+values).text('Please enter your mobile Number !');
                return false;
            }
        }
        else
        {
//            $("div.removePopError").remove();
//            $("div.apply_job_form form").append("<div class='removePopError' style='color:#f03140;'>Please Enter Valid Mobile Number</div>");
            $('#input-mobile-error'+values).text('Please enter your mobile Number !');
            return false;
        }
    }


        var values = '';
        var name = ''; var email = '';
        var mobile= ''; var cover_letter = '';
        var resume = ''; var ext = '';
        var hidden_file = '';

        $(".apply_job").on('click',function(e){
            values = $(this).attr('data-id');
            e.preventDefault();
            $.fancybox({
                'href'          : '#apply_job_form'+values,
                'titleShow': false,
                'transitionIn': 'elastic',
                'transitionOut': 'elastic',
                'minWidth':'600',
                'hideOnContentClick': false
            });
        });


        $('.job_submit').click(function(){
            values = $(this).attr('data-id');
            name = $('#input-name'+values).val();
            email = $('#input-email'+values).val();
            mobile = $('#input-mobile'+values).val();
            current_ctc =$('#input-current_ctc'+values).val();
            expected_ctc =$('#input-expected_ctc'+values).val();
            cover_letter = $('#input-cover-letter'+values).val();
            resume = $('#input-resume'+values).val();
            //ext = $('#input-resume'+values).val().split('.').pop().toLowerCase();
            hidden_file = $('#hidden-files-name').val(resume);
            //console.log( $( this ).serialize() );

            flag = 0;
            if(name==''){
                $('#input-name-error'+values).text('Please enter your name !');
                flag = 1;
            }else{
                $('#input-name-error'+values).remove();
            }
            if(email==''){
                $('#input-email-error'+values).text('Please enter your email id !');
                flag = 1;
            }else{
                $('#input-email-error'+values).remove();
            }
            if(mobile==''){
                $('#input-mobile-error'+values).text('Please enter your mobile Number !');
                flag = 1;
            }else{

                phone_value = phonenumber(mobile);
                if(phone_value == true){
                    $('#input-mobile-error'+values).remove();
                }else{
                    $('#input-mobile-error'+values).text('Please enter your mobile Number !');
                    flag = 1;
                }
            }
            if(current_ctc==''){
                $('#input-current_ctc-error'+values).text('Please enter your Current CTC !');
                flag = 1;
            }else{
                $('#input-current_ctc-error'+values).remove();
            }
            if(expected_ctc==''){
                $('#input-expected_ctc-error'+values).text('Please enter your Expected CTC !');
                flag = 1;
            }else{
                $('#input-expected_ctc-error'+values).remove();
            }
            if(cover_letter==''){
                $('#input-cover-letter-error'+values).text('Please enter cover letter !');
                flag = 1;
            }else{
                $('#input-cover-letter-error'+values).remove();
            }

            if(resume==''){
                $('#input-resume-error'+values).text('Please select resume !');
                flag = 1;
            }else{
                if(!/(\.txt|\.pdf|\.rtf|\.doc)$/i.test(resume))
                {
                    $('#input-resume-error'+values).text('Please select resume in doc, pdf, txt and rtf !');
                    flag = 1;
                }else{
                    $('#input-resume-error'+values).remove();
                }
            }

            if(flag) {
                return false;
            }

        });

    });
</script>

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



<?php
if(!empty($redirect_popup))
    echo $redirect_popup;

if(!empty($show_redirect_popup)) { ?>
    <script type="text/javascript">
        $("#indiaStoreOtherCountryAlert").modal({show: 'true',backdrop: 'static', keyboard: false});
        $(".productInfoAjax, .productRelatedAjax").each(function() {
            $(this).removeClass("fancybox"); // unbind  to click action of fancybox
        });

        $(".addtocart_bottem, .addtocart").each(function() {
            $(this).prop('onclick',null).off('click'); // unbind click action on this button
        });

        $('.productInfoAjax, .addtocart_bottem, .addtocart, .productRelatedView').on('click',function(e){
            e.preventDefault();
            e.stopImmediatePropagation();
            var pId = $(this).data('product-id');

            var goToUrl = '<?php echo "http://".INTERNATIONAL_STORE_HOST."/index.php?route=product/product&product_id=";?>' + pId;

            $('.btn-redirect').attr('href',goToUrl);

            $("#indiaStoreOtherCountryAlert").modal({show: 'true',backdrop: 'static', keyboard: false});
            return false;
        });

    </script>
<?php } ?>

<script type="text/javascript">
<?php if ($international_store == 1) { ?>

var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/5a420d52bbdfe97b137fd4ba/default';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();

<?php } else { ?>

var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/55faa35605ceaf627695ea99/default';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();

<?php } ?>
</script>
</body>

</html>