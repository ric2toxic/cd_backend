import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux';
import Helmet from 'react-helmet';
import ContactUs from "../contact";
import About from "../about";
import Policies from '../policies';
import Dropshipper from "../dropshipper";
import Storelocator from "../storelocator";
import SellerLogin from "../sellerlogin";


import AccountPostYourRequirement from '../post_your_requirement'
import  './index.css'

import { informationsData } from '../../../actions/InformationAction';

class Information  extends Component {

 render(){
 if(this.props.match.params.information_id === 'contact-us')
 {
   
    return (<ContactUs />)

 }
 else if(this.props.match.params.information_id === 'about-us')
 {
   
    return (<About />)

 }
  else if(this.props.match.params.information_id === 'policies')
 {
   
    return (<Policies />)

 }
  else if(this.props.match.params.information_id === 'dropshipper')
 {
   
    return (<Dropshipper />)

 }
  else if(this.props.match.params.information_id === 'storelocator')
 {
   
    return (<Storelocator />)

 }
  else if(this.props.match.params.information_id === 'post-your-requirement')
 {
   
    return (<AccountPostYourRequirement />)

 }
  else if(this.props.match.params.information_id === 'seller_login')
 {
   
    return (<SellerLogin />)

 }
 else
 { 
  if(this.props.information_data.keyword !== this.props.match.params.information_id)
  {
    this.props.dispatch(informationsData(this.props.match.params.information_id));
  }
    return (
         <div className="contner head_margin side-collapse-container">
         <Helmet title={this.props.information_data.heading_title} />
         <section className="col-xs-12 contact_sanction privacy_text" dangerouslySetInnerHTML={{ __html: this.props.information_data.description }} />
         <div className="clearfix"></div>
   </div>)

  }  

  }}


function mapStateToProps(state){
  return {
    information_data: state.informationReducer.information_data,
    actions: bindActionCreators(informationsData,)
  };
}
export default connect(mapStateToProps)(Information);