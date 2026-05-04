import React, { Component } from 'react'
import { connect } from 'react-redux'
import { Link } from 'react-router-dom'
import $ from 'jquery'
import {userloginData, HeaderUserloginSuccess} from '../../actions/HeaderAction';
import { informationData, FooterInformationSuccess } from '../../actions/FooterAction';
import custom from '../../custom/custom'
import Api from '../../api/Api';

import  './index.css'

class Footer  extends Component {

 constructor(props)
 {
     super(props);
     this.logout = this.logout.bind(this);
 } 
 
 componentWillMount()
 {
     $(window).scroll(function() {
            if ($(this).scrollTop()) {
                $('#scroll_to_top:hidden').stop(true, true).fadeIn();
            } else {
                $('#scroll_to_top').stop(true, true).fadeOut();
            }
        });

        $("a[href='#top']").click(function () {
            $("html, body").animate({scrollTop: 0}, "slow");
            return false;
        });

       informationData()
       .then(this.props.FooterInformationSuccess);

 }

generateLinks (item, index) { 
    return <li key={index}>
         <a href={item.href}>{ $('<div/>').html(item.title).text() } </a>
        </li>
  }

logout()
 {
    custom.delete_cookie('customer_id');
    custom.delete_cookie('customer_access_token');
    custom.delete_cookie('customer_mobile');
    custom.delete_cookie('register_user');
    custom.delete_cookie('country_code');
    custom.delete_cookie('country_iso_code');
    custom.delete_cookie('cart_session_id');
    userloginData()
    .then(this.props.HeaderUserloginSuccess);
 } 


render(){
const { user_login } = this.props;
return (
<footer className="color_white">
    <div className="container">
        <div className="row">
            <div className="col-sm-3">
                <h3>Information</h3>
                 <ul className="list-unstyle list-unstyled">
                    <li><a href={Api.api_url+'about-us'}>About us</a></li>
                    <li><a href={Api.api_url+'contact-us'}>Contact us</a></li>
                    {this.props.information ? this.props.information.informations.map(this.generateLinks) : '' }
                     {user_login && user_login.logged ?
                        <li><a onClick={this.logout}>Logout</a></li>
                      :
                        <li><a href={Api.api_url+'dropshipper'}>Become a Dropshipper</a></li>
                     }   
                  </ul>
            </div>
            
            <div className="col-sm-3">
                <div className="footer4">
                    <h3>Helpline Number</h3>
                    <ul className="list-unstyle list-unstyled">
                        <li>
                            <a href="tel:+91 141 4049163" className="phone"><i className="fa fa-phone"></i>(+91) 141 - 4049163</a>
                            <br /><span className="ofc_time" style={{padding:'0px'}}>(10am to 8pm)</span>
                        </li>
                        <li>
                            <a href="tel:+918696491521" className="whatsapp"><i className="fa fa-whatsapp"></i>+918696491521</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div className="col-sm-3">
                <div className="footer4">
                    <h3>Logistics Partners</h3>
                    <div className="col-sm-12 col-xs-4" style={{padding:'0px'}}>
                        <img src="https://cdnimages.net/img/fd.jpg" alt="Fedex is our Logistics Partner" />
                    </div>
                    <div className="col-sm-12 col-xs-8" style={{padding:'0px'}}>
                        <img src="https://cdnimages.net/img/blue_dart.jpg" alt="BlueDart is our Logistics Partner" />
                    </div>
                </div>
            </div>
            <div className="col-sm-3">
                <div className="trust_seal">
                </div>
            </div>
        </div>

        <div className="row">
            <div className="col-sm-3 social_profiles">
                <ul>
                    <li>
                        <Link className="sf_facebook" to="https://web.facebook.com/wholesalebox1" rel="noopener noreferrer" target="_blank"></Link>
                    </li>
                    <li>
                        <Link className="sf_googleplus" to="https://plus.google.com/115306833718886596322" rel="noopener noreferrer" target="_blank"></Link>
                    </li>
                    
                </ul>
            </div>
            <div className="col-sm-6 ">
                <div className="ic_payment_methods">
                </div>
            </div>

        </div>
    <div className="footer_copyright">
        <div className="container ">
            <div className="row">
                <div className="col-sm-12">
                    <p>WholesaleBox {this.props.information ? this.props.information.year : ''}</p>
                </div>
            </div>
        </div>
    </div>
    <a href="#top" id="scroll_to_top" style={{display: 'block'}}>&nbsp;</a>
        <div style={{display: 'none'}}></div>
        <input type="hidden" name="term_condition" id="seller_term_condition" />
    </div>
</footer>
)

}}


const mapStateToProps = state => ({ information: state.information, user_login:state.user_login });

export default connect(mapStateToProps, {
    FooterInformationSuccess: FooterInformationSuccess,
    HeaderUserloginSuccess:HeaderUserloginSuccess
})(Footer);