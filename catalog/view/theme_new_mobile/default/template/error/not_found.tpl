<!DOCTYPE html>
<html>
  <head>
    <title><?php echo $title; ?></title>
   
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://www.wholesalebox.in/catalog/view/theme/default/css/bootstrap.min.css">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://d36qiqd7gl7e25.cloudfront.net/img/catalog/16_by_16_wsbIcon.png" rel="icon" />
    <style type="text/css"> 
    @font-face
    {
      font-family: SourceSans;
      src:url(https://cdnimages.net/css/fonts/SourceSansPro-Regular.ttf);
    }
    body
    {
      
      font-family: SourceSans;
    }
    .nopadding
    {
      padding: 0px;
    }  
    .page_not_found img
    {
      margin: auto;      
    }
    .page_not_found
    {
      text-align: center;
      margin-top: 3%;
      
    }  
    .left_side, .right_side
    {
      margin-top: 3%;
    }
    .page_not_found a img
    {
      width: 250px;
      margin-top: 2%;
    }
    .headline_text
    {
      margin-top: 5%;
      font-size: 17px;
    }
    .left_side, .right_side  {
    
    width: 48%!important;
    }
    .left_side ul, .right_side ul  {
    list-style: none;
    line-height: 2;
    color:#636363;
    font-size: 16px;
    margin-top: 7%;
    }

    .left_side ul li::before, .right_side ul li::before
    {
    content: "\2022";
    color: #D75245; 
    font-weight: bold; 
    display: inline-block; 
    width: 2em;  
    margin-left: -2em; 
    }
    .wsb-apple-link 
    {
      background: rgba(0,0,0,0) url(https://cdnimages.net/maintenance/app_links.png) no-repeat scroll 0px 0px;
      height: 27px;
      width: 27px;
      display: inline-block;
      
    }
    .wsb-playstore-link 
    {
      background: rgba(0,0,0,0) url(https://cdnimages.net/maintenance/app_links.png) no-repeat scroll -40px 0px;
      height: 26px;
      width: 32px;
      display: inline-block;
      border-right: 1px solid #cccccc;
      padding-right: 40px;
    }
    .wsb-fb-link
    {
      background: rgba(0,0,0,0) url(https://cdnimages.net/maintenance/social_media_icons.png) no-repeat scroll -3px -2px;
      height: 30px;
      width: 30px;
      display: inline-block;
    }
    .wsb-insta-link
    {
      background: rgba(0,0,0,0) url(https://cdnimages.net/maintenance/social_media_icons.png) no-repeat scroll -46px -2px;
      height: 30px;
      width: 30px;
      display: inline-block;
    }
    .wsb-twitter-link
    {
      background: rgba(0,0,0,0) url(https://cdnimages.net/maintenance/social_media_icons.png) no-repeat scroll -87px -2px;
      height: 30px;
      width: 30px;
      display: inline-block;
    }
    .page_not_found p
    {
      font-size: 17px;
      margin-top: 2%;
    }
    .wsb-follow-link
    {
      font-size: 15px;
      color:#636363;
    }
    .middle_shadow_line
    {
      float: left;
      margin:auto;
      margin-top: 2%
    }
    .right_hand
    {
      float: right;
    }
    .wsb-info
    {
      margin-top: 15%;
    }
   .logo_app_link
   {
    margin-top: 3%
   }
   .right_side .links_content
   {
    padding-left: 50px;
   }
   .right_padding_adjust
   {
    padding-right: 20px;
   }
    </style>
  </head>
  <body>
                
          <div class="page_not_found">
            <div class="container">
              <div class="col-xs-12">
                <img src="https://cdnimages.net/maintenance/404_image.png" />              
                <p>We can’t ﬁnd the page you’re looking for.</p>
                <div><a href="https://www.wholesalebox.in/"><img src="https://cdnimages.net/maintenance/home_page_button.png" /></a></div>
              </div> 
              </div>
          </div> 
          <div class="container">
                <div class="col-xs-12"> 
                   <p class="headline_text">You may not be able to find the page you were after because of:
                   </p>
                   <ul>
                     <li>An out-of-date bookmark favorite</li>
                     <li>A search engine that has an out-of-date listing for us</li>
                     <li>A mis-typed address</li>
                   </ul>
                  

                   
                </div>


                <div class="col-xs-12">
                  
                  <div class="links_content">
                  <p class="headline_text">We think you will find one of the following links useful:</p>

                  <?php if(!empty($data['category_parent']) && isset($data['category_parent'])){ ?>
                    <div class="col-xs-6">
                      <ul>
                        <?php
                          $i = 1;
                          foreach($data['category_parent'] as $category){
                        ?>
                            <li><a href="<?php echo $category['href']; ?>"><?php echo $category['name'];?></a></li>
                        <?php
                            if($i==10){
                        ?>
                      </ul>
                    </div>
                    <div class="col-xs-6">
                      <ul>
                        <?php
                            }
                            $i++;
                          }
                        ?>
                      </ul>
                    </div>
                  <?php  } ?>
              
                   </div>
                </div>


                 <div class="col-xs-12 wsb-info">
                    <div class="row">
                   <div class="col-xs-6 nopadding">
                     <p class="wsb-follow-link">
                        <a class="wsb-fb-link" href="https://www.facebook.com/wholesaleboxOfficial/"></a> &nbsp; &nbsp;
                        <a class="wsb-insta-link" href="https://www.instagram.com/wholesalebox/"></a>&nbsp; &nbsp;
                        <a class="wsb-twitter-link" href="https://twitter.com/WholesaleBox_in?ref_src=twsrc%5Egoogle%7Ctwcamp%5Eserp%7Ctwgr%5Eauthor"></a>
                   </p>
                   </div>
                   <div class="col-xs-6 nopadding right_padding_adjust">
                     <p class="right_hand">
                      <a href="https://www.wholesalebox.in/i/about-us">About Us</a>&nbsp; &nbsp; | &nbsp; &nbsp; 
                      <a href="https://www.wholesalebox.in/i/contact-us">Contact Us</a>
                     </p>
                   </div> 
                   </div>
                   <div class="row logo_app_link">
                     <div class="col-xs-6 nopadding"><a href="https://www.wholesalebox.in"><img style="width: 200px;" src="https://d36qiqd7gl7e25.cloudfront.net/img/catalog/rsz_wsb_tmp_logo_286.png"></a></div>
                    <div class="col-xs-6 nopadding right_padding_adjust">
                      <div class="right_hand">
                    <a class="wsb-playstore-link" href="https://play.google.com/store/apps/details?id=in.wholesalebox&hl=en_IN"></a> &nbsp; &nbsp;
                    <a class="wsb-apple-link" href="https://apps.apple.com/us/app/wholesalebox/id1254820324"></a> 
                    </div> 
                  </div>
                   </div>
                   
                   </div>
            
          </div>   
           
          
    
  </body>
  </html>