
import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux';
import { Link } from 'react-router-dom'
import Drawer from 'material-ui/Drawer';
import { sendMailForCallBackRequest } from '../../actions/FooterAction'
import custom from '../../custom/custom'
import $ from 'jquery'
import Api from '../../api/Api'
import  './index.css'

class Footer  extends Component {
  
constructor(props)
  { 
     super(props);
     this.chatDrawer = this.chatDrawer.bind(this);
     this.call_back_request   = this.call_back_request.bind(this);
     this.state = {
      chatDrawerOpen:false
     }
  }

componentDidMount()
 { 
    $(document).delegate('.list-unstyled1 .dropdown-link', 'click', function(e) {
       var tab_id = $(this).attr('data-target');
       $(".list-unstyled1 li").removeClass("active");
       $(this).parent("li").addClass("active");
       $(".list-unstyled").removeClass('in active');
       $(tab_id).addClass('in active');
    });

    var userAgent = navigator.userAgent || navigator.vendor || window.opera;

    if (!/android/i.test(userAgent)) {
       $(".app_download_banner").hide(); 
    }

    $(document).delegate('.download_app_btn', 'click', function(e) {
            window.open("https://play.google.com/store/apps/details?id=in.wholesalebox");
    });

    $(document).on('click','.chat_here',function(){
       $("#tawkchat-container").show();
       $("#tawkchat-container iframe").show();
       $("#tawkchat-container iframe").first().hide();
       $("#tawkchat-container").width('100%').height('100%');
    });

    if(this.props.userlogin && this.props.userlogin.international_store === 1)
    {
      (function(){
       var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
       s1.async=true;
       s1.src='https://embed.tawk.to/5a420d52bbdfe97b137fd4ba/default';
       s1.charset='UTF-8';
       s1.setAttribute('crossorigin','*');
       s0.parentNode.insertBefore(s1,s0);
        })();
    }
    else
    {
      (function(){
      var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
      s1.async=true;
      s1.src='https://embed.tawk.to/55faa35605ceaf627695ea99/default';
      s1.charset='UTF-8';
      s1.setAttribute('crossorigin','*');
      s0.parentNode.insertBefore(s1,s0);
      })();
    }
    
  }

 call_back_request()
 {
   $('body').removeClass('loaded').addClass('loading');
   var value = $('#callback_request_input').val();
   this.props.dispatch(sendMailForCallBackRequest(value));
 } 

 chatDrawer()
 {
  this.setState({chatDrawerOpen: !this.state.chatDrawerOpen});

  if(!this.state.chatDrawerOpen){
    $('.custom_chat_box').parent().addClass('chat_drawer');
    }else{
    $('.custom_chat_box').parent().removeClass('chat_drawer');
    }
 }


   
render(){

 function save_cookie_policy()
  {
          custom.createCookie('cookie_policy', true, 7);
         $("#cookie_content").hide();
  }
function isNumberKey() {
     var telephone = $('#callback_request_input').val();
            if (telephone.charAt(0) === 0 )
            {
              $('#callback_request_input').val(telephone.slice(1));
            }
            if (/\D/g.test(telephone))
            {
             var node = $('#callback_request_input');
             node.val(node.val().replace(/[^0-9]/g,'') );
            }
}

return (<footer>
       <div className="col-xs-12 social_section">
         <p>Let’s connect and be friends!</p>
         <ul>
           <li><Link to="https://www.facebook.com/wholesalebox1" target="_blank" className="fb_footer"><i className="fa fa-facebook" aria-hidden="true"></i></Link></li>
           <li><Link to="https://twitter.com/wholesalebox_in" target="_blank" className="twr_footer"><i className="fa fa-twitter" aria-hidden="true"></i></Link></li>
           <li><Link to="#" className="instagram_footer"><i className="fa fa-linkedin" aria-hidden="true"></i></Link></li>
           <li><Link to="https://plus.google.com/+WholesaleBox1" target="_blank" className="googleplus_footer"><i className="fa fa-google-plus" aria-hidden="true"></i></Link></li>
           <li><Link to="https://www.youtube.com/channel/UCQFvYTsk3f0OllDs9UJgTUw" target="_blank" className="youtube_footer"><i className="fa fa-youtube-play" aria-hidden="true"></i></Link></li>
           <li><Link to="https://in.pinterest.com/wholesalebox" target="_blank" className="pinterest_footer"><i className="fa fa-pinterest-p" aria-hidden="true"></i></Link></li>
         </ul>
         <div className="clearfix"></div> 
       </div>
       
       <div className="fix-options">
         <ul>
          <li onClick={this.chatDrawer} id="tawkchat"><i className="fa fa-comments" aria-hidden="true"></i></li>
         </ul>
      </div>

       <div className="container-fluid app_download_banner">

          {!custom.getCookie('cookie_policy') && this.props.userlogin && this.props.userlogin.international_store === 1  ?
           <div id="cookie_content">
            <div className="cookie_content">
             We use cookies to personalise content, to analyse our traffic. 
             We also disclose information about your use of our site with our analytics partners. 
             Additional details are available in our cookie policy. 
              <a className="cookie_btn" onClick={()=>save_cookie_policy()}>Agree</a>
            </div> 
          </div>
          : ''}

           <div className="col-xs-1 nopadding">
           <img src={Api.cdn_url+"catalog/wholesale-box-icon-48x48.png"} alt="download" style={{width:'25px'}} />
           </div>
           <div className="col-xs-8" style={{marginTop:'2px'}}>
             2% extra Cashback + Exclusive New Designs, SALE previews only on the app.
           </div>
           <div className="col-xs-3 nopadding">
            <button className="btn btn-primary download_app_btn">DOWNLOAD</button>
           </div>
        </div>


<Drawer
  docked={false}
  open={this.state.chatDrawerOpen}
  openSecondary={true}>
    <div className="custom_chat_box">
    <div className="chat_box_heading_div">
        <p className="chat_box_heading">Online 
        <i onClick={this.chatDrawer} className="fa fa-times"></i></p>
    </div> 
    <div className="chat_box_content">
        <p>Please tell us your preferred way to connect. Use whatsapp to get fast response.</p>
        <ul className="list-unstyled1 components">
            <li className="dropdown">
                <span data-target="#whatsapp" className="dropdown-link dropdown-toggle"><i className="fa fa-whatsapp"></i> <div className="dropdown_arrow"><i className="fa fa-angle-right"></i></div>Whatsapp</span>
                <ul className="collapse list-unstyled" id="whatsapp">
                    <li>
                        <div className="whatsaap_web_title">
                            <a rel="noopener noreferrer" href={"https://api.whatsapp.com/send?phone="+this.props.userlogin.text_whatsappw_no+"&text="+this.props.userlogin.text_whatsappw_no_msg_text}>
                            <i className="fa fa-comments-o"></i>  Chat on Whatsapp Web</a>
                        </div>
                        <p className="or_outer"><span className="or">or</span></p>
                        <p className="whatsapp_on">Whatsapp on {this.props.userlogin.text_whatsappw_no}</p>
                    </li>
                </ul>
            </li>

            <li className="dropdown">
                <span data-target="#callus" className="dropdown-toggle dropdown-link">
                <i className="fa fa-phone" aria-hidden="true"></i>  Call Us 
                 <div className="dropdown_arrow"><i className="fa fa-angle-right"></i></div></span>
                <ul className="collapse list-unstyled" id="callus">
                    <li><p className="call_us">Call us on {this.props.userlogin.text_whatsappw_no}</p></li>
                </ul>
            </li>

            <li className="dropdown">
                <span data-target="#call_back_request"  className="dropdown-toggle dropdown-link">
                <i className="fa fa-user"></i>  Call Back Request 
                 <div className="dropdown_arrow"><i className="fa fa-angle-right"></i></div></span>
                <ul className="collapse list-unstyled" id="call_back_request">
                    <li>
                        <p>We shall call you shortly. Please enter your contact number</p>
                        <input className="callback_request_input" name="callback_request_input" id="callback_request_input" type="text" maxLength="10" onKeyUp={()=>isNumberKey()} />
                        <input className="callback_request_button" type="submit" value="Submit" onClick={this.call_back_request} />
                    </li>
                </ul>
            </li>

            <li className="chat_here">
               <i className="fa fa-comments"></i>  Chat Here
            </li>
            
        </ul>
    </div> 
    </div> 
   </Drawer>
    </footer>)

  }
}

function mapStateToProps(state){
  return {
    userlogin: state.headerReducer.userlogin,
    actions: bindActionCreators(sendMailForCallBackRequest)
  };
}
export default connect(mapStateToProps)(Footer);
