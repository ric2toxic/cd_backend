
class Footer extends React.Component {

   constructor()
   {
     super();
     this.state = {
      informations: [],
      footer_images: [],
      static_data: ''
     };
   } 

 componentDidMount()
  {

     axios({
    method:'get',
    url:'./api/footer/information',
    responseType:'json'
    })
   .then(response => {
            this.setState({informations: response.data.data.informations, footer_images: response.data.data.footer_images, static_data: response.data.data});
    });



   $(window).scroll(function() {
    if ($(this).scrollTop()) {
        $('#scroll_to_top1:hidden').stop(true, true).fadeIn();
    } else {
        $('#scroll_to_top1').stop(true, true).fadeOut();
     }
    });

    $("a[href='#top']").click(function () {
        $("html, body").animate({scrollTop: 0}, "slow");
        return false;
    });

    $(document).on("click","a[href='#toRecentView']",function () {
    var slideRecent=$("#recentSlider:first").offset().top-50;
        $("html, body").animate({scrollTop: slideRecent}, "slow");
        return false;
    });
    
  }

	render() {
      
      function save_cookie_policy()
      {
          createCookie('cookie_policy', true, 7);
         $("#cookie_content").hide();
      }

      function get_applink()
      {
            $('.app-link-error').hide();
            var mobile_pattern = new RegExp(/^\d{10}$/); 
            var mobile_no = $('input[name="get_app_link"]').val();
            if (mobile_pattern.test(mobile_no))
            {
                $.ajax({
                    type: 'POST',
                    url : './api/footer/getAppLink',
                    data: {mobile_no},
                    dataType:'json',
                    beforeSend: function() {
                      $('.get_applink_button').button('loading');
                    },
                    complete: function() {
                      $('.get_applink_button').button('reset');
                    },
                    success: function(data){
                        var json = data.data;
                        if(json.error == true){

                            $('.app-link-error').html(json['error_msg']);
                            $('.app-link-error').show();

                        }else{
                            $('input[name="get_app_link"]').attr("placeholder", "Enter your mobile number").val("").focus().blur();
                        }
                        if(json.success == true){
                            //$("#link_popup p").html('<i class="fa fa-check"></i> '+ json['success_msg']);
                            //$("#link_popup").show();
                             var success_div = '<div id="notification"><div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span> <strong>Success</strong><hr class="message-inner-separator"><p>'+json['success_msg']+'</p></div></div>';
                            $(document.body).append(success_div);   
                            $('#notification').fadeOut(2000);
                            setTimeout(function() {
                             $('#notification').remove();
                              }, 2000);
                        }

                    }
                });
            }
            else
            {
               $('.app-link-error').html('please enter valid mobile number');
               $('.app-link-error').show();
              
            }
        }
       
      function link_popup_hide()
        {
           $("#link_popup").hide();
        }

		return (
			<footer>

     

      <div className="col-sm-12">
 <RecentViewSlider key="latest"  category_id="latest" active_item="5" page_type="footer"/>
               <div className="clearfix"></div>
</div>

          <div className="container-fluid">
            <div className="row">
       <div className="col-sm-12 social_section">
         <ul>
           <li>Let’s connect and be friends!</li>
           {this.props.international_store ? 
           <li><a href="https://www.facebook.com/WholesaleboxInternational" className="fb_footer" target="_blank"><i className="fa fa-facebook" aria-hidden="true"></i></a></li>
           :
           <li><a href="https://www.facebook.com/wholesaleboxofficial" className="fb_footer" target="_blank"><i className="fa fa-facebook" aria-hidden="true"></i></a></li>
           }
           <li><a href="https://twitter.com/wholesalebox_in" className="twr_footer"  target="_blank"><i className="fa fa-twitter" aria-hidden="true"></i></a></li>
           <li><a href="https://in.linkedin.com/company/wholesalebox-internet-pvt--ltd-"  target="_blank" className="instagram_footer"><i className="fa fa-linkedin" aria-hidden="true"></i></a></li>
           <li><a href="https://plus.google.com/+WholesaleBox1" className="googleplus_footer"  target="_blank"><i className="fa fa-google-plus" aria-hidden="true"></i></a></li>
           <li><a href="https://www.youtube.com/channel/UCQFvYTsk3f0OllDs9UJgTUw"  target="_blank" className="youtube_footer"><i className="fa fa-youtube-play" aria-hidden="true"></i></a></li>
           <li><a href="https://in.pinterest.com/wholesalebox" className="pinterest_footer"  target="_blank"><i className="fa fa-pinterest-p" aria-hidden="true"></i></a></li>
         </ul>
       </div>


       <div className="width_fix">

           <Information informations={this.state.informations} international_store={this.props.international_store} static_data={this.state.static_data} /> 

          <div className="col-sm-3 information_section">
            <h3>Helpline Numbers</h3>
            <ul>
              <li><a href="javascript:;"><label><i className="fa fa-phone" aria-hidden="true"></i></label> (+91) 141 - 4049163</a> <small className="office_time">(10 AM to 8 PM)</small></li>
                {this.props.international_store ? 
                   <li><a href="https://web.whatsapp.com/send?phone=9116134795&text=Hello,%20I%20visited%20Wholesalebox.%20I%20am%20interested%20in%20your%20products." target="_blank"><label><i className="fa fa-whatsapp" aria-hidden="true"></i></label> +91 - 9116134795</a></li>
                 :
                  <li><a href="https://web.whatsapp.com/send?phone=918696491521&text=Hello,%20I%20visited%20Wholesalebox.%20I%20am%20interested%20in%20your%20products." target="_blank"><label><i className="fa fa-whatsapp" aria-hidden="true"></i></label> +91 - 8696491521</a></li>
                  }
            </ul>
          </div>
          <div className="col-sm-3 information_section">
            <h3>Logistics Partners  </h3>
            <ul>
              <li className="Partners "><img className="img-responsive" src={this.state.footer_images.fd} alt="" /></li>
              <li className="Partners "><img className="img-responsive" src={this.state.footer_images.gk} alt="" /></li>
              <li className="Partners "><img className="img-responsive" src={this.state.footer_images.dtdc} alt="" /></li>
              <li className="Partners "><img className="img-responsive" src={this.state.footer_images.blue_dart} alt="" /></li>
            </ul>
          </div>
          <div className="col-sm-3 information_section">
            <h3>Scan QR code to get the link</h3>
            <ul>
              <li className="qr_code_box "><img className="img-responsive" src={this.state.footer_images.qr_code_new} alt="" width="100" height="100" /></li>
              <li className="qr_code_box middle_box"><a href="https://play.google.com/store/apps/details?id=in.wholesalebox&hl=en" target="_blank"><img className="img-responsive" src={cdn_url+"google-play-android-app.svg"} alt="" width="140px" /></a></li>
              <li className="qr_code_box "><a href="https://itunes.apple.com/us/app/wholesalebox/id1254820324?mt=8" target="_blank"><img className="img-responsive" src={cdn_url+"ios_download.svg"} alt="" width="140px" /></a></li>
            </ul>
           {!this.props.international_store ?
            <p className="app_purchase">Purchase from our app and get 2% cashback for your next order within 7 days</p>
            : ''
           }
          </div>
         
       </div>

       <div id="link_popup" className="link_popup">
            <button className="appLink_close_popup" onClick={link_popup_hide}><i className="fa fa-close"></i></button>
            <p></p>
        </div>

       <div className="subscribe_box container" id="getAppLink">
           <span>Get app download link over SMS</span>
           <div className="get_link">
            <input type="text" className="form-control subscibe_input" name="get_app_link" placeholder="Enter your mobile number" maxLength="10" />
            <button id="download-app-landing" type="button" id="get_applink_button" className="btn subscribe_btn get_applink_button" onClick={get_applink}>Get the App</button>
             <span className="app-link-error"></span>
          </div>
       </div>


       <div className="col-sm-12 Partners_box">
         <ul>
            <li className="Payment_partners"><img className="img-responsive payment_img" src={this.state.footer_images.PAYMENT} alt="" /><span> Secure<br /> Payment</span></li>
            <li className="ic_payment_methods"></li> 
         </ul>
       </div>
       <div className="clearfix"></div>

       <div className="container width_fix">
        <PopularTags />
       </div>
       <div className="container width_fix footer_bottem information_section footer_Subscribe_Section">
          <ul>
            <li className="col-sm-12" style={{textAlign:'center'}}><a href="">@2017 Wholesalebox Internet Private Limited. All Rights Reserved</a></li>
          </ul>
        </div>
      </div>
    </div>
  <div className="fix-options display_block" id="scroll_to_top">
      <ul>
          <li id="scroll_to_top1"><a href="#top" className="full-radius"><i className="fa fa-angle-up" aria-hidden="true"></i></a></li>
          <li id="recentSlideBtn"></li>
      </ul>    
     </div> 

     {this.props.international_store && !getCookie('cookie_policy')  ?
      <div className="fix-options display_block" id="scroll_to_top" style={{right:'unset', left:'-40px'}}>
      <ul>
          <li id="cookie_content">
            <div className="cookie_content" style={{borderRadius: '0px 30px 30px 0px'}}>
             We use cookies to personalise content, to analyse our traffic. 
             We also disclose information about your use of our site with our analytics partners. 
             Additional details are available in our cookie policy. 
              <a className="cookie_btn" onClick={()=>save_cookie_policy()}>Agree</a>
            </div> 
          </li>
      </ul>    
     </div>
      : ''}

</footer>
		);

	}
}
