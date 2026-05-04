import React, { Component } from 'react';
import { Admin, Resource } from 'react-admin';

import Menu from './menu';
import customRoutes from './routes';

import myRestProvider from '../../myRestProvider';

import SellerAgreement from '../seller_agreement';

import AgreementConsent from '../agreement_consent'

import Dashboard from '../dashboard';
import {tentativeList, tentativeShow} from '../orders/tentative';
import {sorList, sorShow} from '../orders/sor';
import {pickup_doneList, pickup_doneShow} from '../orders/pickup_done';
import {pickup_requestList, pickup_requestShow} from '../orders/pickup_request';

import {tentativeReturnList, approvedReturnList, deliveredReturnList, disputeReturnList} from '../return';
import {returnShow} from '../return/return_show';

import {tentativeReplacementList, approvedReplacementList, deliveredReplacementList, disputeReplacementList} from '../replace';
import {replaceShow} from '../replace/replace_show';

import {paymentreportList} from '../payment_report';

import {invoiceList, invoiceShow} from '../sor/invoice';
import {skuList, skuShow} from '../sor/sku';

import salesReportList from '../report/sales';
import returnReportList from '../report/return';
import hsnReportList from '../report/hsn';

import login from '../login';
import authProvider from '../../authProvider';

import rootReducers from '../../Reducers';

import {  userloginData } from '../../actions/HeaderAction';

import custom from '../../custom/custom'

import LogoutIcon from '@material-ui/icons/PowerSettingsNew'
import MenuIcon from '@material-ui/icons/Menu'
import $ from 'jquery'

class App  extends Component {

constructor(props)
{
  super(props);
  this.logout       = this.logout.bind(this);
  this.menu_squeeze = this.menu_squeeze.bind(this);

  this.state = {
     username: false,
     logout: false,
     profile_status: false,
     SELLER_AGREEMENT_POPUP:false,
     AGREEMENT_CONSENT_POPUP:true
     };
}

componentWillMount() 
{ 
  let current_url = window.location.href;
  current_url   = current_url.split("#");
  if(current_url[1]) { current_url = current_url[1]; }
 else { current_url=current_url[0]; }

  var self = this;
  var response = userloginData();
  response.then(function(data){
      if(data.cust_name)
      {
        self.setState({username:data.cust_name});
        self.setState({logout:data.logout});
        self.setState({profile_status:data.profile_status});
        self.setState({SELLER_AGREEMENT_POPUP:data.SELLER_AGREEMENT_POPUP});
        self.setState({AGREEMENT_CONSENT_POPUP:data.AGREEMENT_CONSENT_POPUP});
      }
      else
      {
        /*custom.delete_cookie('customer_id');
        custom.delete_cookie('customer_mobile');
        custom.delete_cookie('customer_access_token');
        if(current_url !== '/login')
        {
         window.location.href = './#/login';
        }*/
      }
    })
   .catch(function() {
      console.log('Error: data fetch error', 'warning');
   });
}

logout()
{
  window.location.href = this.state.logout;
}

menu_squeeze()
{
  if(this.state.squeeze)
  {
    this.setState({squeeze:false});
    $(".menu_bar").parent("div").removeClass("squeeze");
  }
  else
  {
    this.setState({squeeze:true});
    $(".menu_bar").parent("div").addClass("squeeze");
  }
}

render()
{
  let current_url = window.location.href;
  current_url   = current_url.split("#");
  if(current_url[1]) { current_url = current_url[1]; }
  else { current_url=current_url[0]; }

      return (
      <div className="root_area">
     {this.state.AGREEMENT_CONSENT_POPUP && this.state.SELLER_AGREEMENT_POPUP && !custom.getCookie('seller_agreement_'+custom.getCookie('customer_id')) ?
      <SellerAgreement dialog_open={true} />
      : ''}

    {!this.state.AGREEMENT_CONSENT_POPUP ?
      <AgreementConsent dialog_open={true} SELLER_AGREEMENT_POPUP={this.state.SELLER_AGREEMENT_POPUP} />
      : ''}

      <header className="custom_header"  style={current_url === '/login' ? {'display':'none'}: {}}>
      <div className="custom_logo">
        <MenuIcon className="custom_menu" onClick={this.menu_squeeze}  />  
        <span className="custom_logo_title">Seller Panel</span> 
       </div>

       <div className="gst_title_new">
        Transfer Price will be inclusive of GST ( For any help: 9116134791 | 9649558363 )
       </div>

       <div className="login_user">
         {$('<div/>').html(this.state.username).text()} 

          <button onClick={this.logout} className="logout_custom_btn" type="button">
          <LogoutIcon className="logout_custom_icon" /> Logout
        </button> 
       </div>
      </header>

      <Admin customRoutes={customRoutes} menu={Menu} customReducers={{ rootReducers }} loginPage={login} data title="Seller Panel" dashboard={Dashboard} authProvider={authProvider} dataProvider={myRestProvider}>
        <Resource name="orders/getPickpupOrderRequested" options={{ label: 'Pickup Request Order' }} list={pickup_requestList} show={pickup_requestShow}  />
        <Resource name="orders/getPickupOrderDone" options={{ label: 'Pickup Done Order' }} list={pickup_doneList} show={pickup_doneShow}  />
        <Resource name="orders/getTentativeOrders" options={{ label: 'Tentative Order' }} list={tentativeList} show={tentativeShow}  />
        <Resource name="orders/getSorOrders" options={{ label: 'Sor Order' }} list={sorList} show={sorShow}  />
        
        <Resource name="returns/getTentativeReturns" options={{ label: 'Returns' }} list={tentativeReturnList} show={returnShow}  />
        <Resource name="returns/getApprovedReturns" options={{ label: 'Returns' }} list={approvedReturnList} show={returnShow}  />
        <Resource name="returns/getDeliveredReturns" options={{ label: 'Returns' }} list={deliveredReturnList} show={returnShow}  />
        <Resource name="returns/getDisputeReturns" options={{ label: 'Returns' }} list={disputeReturnList} show={returnShow}  />

        <Resource name="replacement/getTentativeReplacement" options={{ label: 'Replacement' }} list={tentativeReplacementList} show={replaceShow}  />
        <Resource name="replacement/getApprovedReplacement" options={{ label: 'Replacement' }} list={approvedReplacementList} show={replaceShow}  />
        <Resource name="replacement/getDeliveredReplacement" options={{ label: 'Replacement' }} list={deliveredReplacementList} show={replaceShow}  />
        <Resource name="replacement/getDisputeReplacement" options={{ label: 'Replacement' }} list={disputeReplacementList} show={replaceShow}  />
  
        <Resource name="payment/paymentreports" options={{ label: 'Payment Report' }} list={paymentreportList} />
        <Resource name="sor/sorInvoices" options={{ label: 'Sor Invoice' }} list={invoiceList} show={invoiceShow}  />
        <Resource name="sor/sorSku" options={{ label: 'Sor Sku' }} list={skuList} show={skuShow}  />
        <Resource name="report/salesReport" options={{ label: 'Sales Report' }} list={salesReportList}  />
        <Resource name="report/returnReport" options={{ label: 'Return Report' }} list={returnReportList}  />
        <Resource name="report/hsnReport" options={{ label: 'HSN Report' }} list={hsnReportList}  />
        <Resource name="sor/sellerPaymentDetailOfSingleSku" />
        <Resource name="returns/product_list" /> 
      </Admin>
      </div>
          )

}}

export default App;