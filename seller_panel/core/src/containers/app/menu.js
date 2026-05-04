import React, { Component } from 'react'
import { connect } from 'react-redux';
import { MenuItemLink, getResources } from 'react-admin';
import ArrowIcon from '@material-ui/icons/KeyboardArrowRight';
import LanguageIcon from '@material-ui/icons/Language';

import {
    DashboardMenuItem,
    Responsive,
} from 'react-admin';
import $ from 'jquery'

import OrderIcon from '@material-ui/icons/ShoppingCart';
import SorInventoryIcon from '@material-ui/icons/Assignment';
import ListIcon from '@material-ui/icons/List';
import GstIcon from '@material-ui/icons/Launch';

import { withRouter } from 'react-router-dom';
import { PickupRequestIcon } from '../orders/pickup_request';
import { PickupDoneIcon } from '../orders/pickup_done';
import { SorIcon } from '../orders/sor';
import { TentativeIcon } from '../orders/tentative';
import { ReturnIcon } from '../return';
import { ReplaceIcon } from '../replace';
import { PaymentIcon } from '../payment_report';
import { InvoiceIcon } from '../sor/invoice';
import { SkuIcon } from '../sor/sku';
import { SalesReportIcon } from '../report/sales';
import { ReturnReportIcon } from '../report/return';
import { HsnReportIcon } from '../report/hsn';
import {ProfileIcon} from '../profile'
import {SellerIcon} from '../seller_agreement/seller_page'

const items = [
    { name: 'Payment Report', to: 'payment/paymentreports', icon: <PaymentIcon /> },
];

const returnitems = [
    { name: 'Tentative', to: 'returns/getTentativeReturns', icon: <ArrowIcon /> },
    { name: 'Request Approved', to: 'returns/getApprovedReturns', icon: <ArrowIcon /> },
    { name: 'Delivered', to: 'returns/getDeliveredReturns', icon: <ArrowIcon /> },
    { name: 'Delivery Dispute', to: 'returns/getDisputeReturns', icon: <ArrowIcon /> },
];

const replaceitems = [
    { name: 'Tentative', to: 'replacement/getTentativeReplacement', icon: <ArrowIcon /> },
    { name: 'Request Approved', to: 'replacement/getApprovedReplacement', icon: <ArrowIcon /> },
    { name: 'Delivered', to: 'replacement/getDeliveredReplacement', icon: <ArrowIcon /> },
    { name: 'Delivery Dispute', to: 'replacement/getDisputeReplacement', icon: <ArrowIcon /> },
];

const orderitems = [
    { name: 'Pickup Requested', to: 'orders/getPickpupOrderRequested', icon: <PickupRequestIcon /> },
    { name: 'Pickup Done', to: 'orders/getPickupOrderDone', icon: <PickupDoneIcon /> },
    { name: 'Tentative', to: 'orders/getTentativeOrders', icon: <TentativeIcon /> },
    { name: 'SOR', to: 'orders/getSorOrders', icon: <SorIcon /> },
];
const soritems = [
    { name: 'Invoice', to: 'sor/sorInvoices', icon: <InvoiceIcon /> },
    { name: 'Sku', to: 'sor/sorSku', icon: <SkuIcon /> },
];
const reportitems = [
    { name: 'Sales Report', to: 'report/salesReport', icon: <SalesReportIcon /> },
    { name: 'Return Report', to: 'report/returnReport', icon: <ReturnReportIcon /> },
    { name: 'HSN Report', to: 'report/hsnReport', icon: <HsnReportIcon /> },
];
const profileitem = [
    { name: 'Profile', to: 'profile', icon: <ProfileIcon /> },
    { name: 'Seller Agreement', to: 'seller_agreement', icon: <SellerIcon /> },
];

const styles = {
    main: {
        display: 'flex',
        flexDirection: 'column',
        justifyContent: 'flex-start',
        height: '100%',
    },
};


class Menu  extends Component {
  
render(){ 

function menu_toggle(show_tab, hide_tab)
{
  $(show_tab).slideToggle(500);
  $(hide_tab).slideUp(500)
}

var current_url = this.props.location.pathname;
let self = this;

    return(<div style={styles.main} className="menu_bar">
      <a className="custom_submenu" href="../"><LanguageIcon className="custom_submenu_icon" /> Go To Main Website  </a>
      <DashboardMenuItem onClick={self.props.onMenuClick} />
         
        <a className="custom_submenu" onClick={()=>menu_toggle('.orders_submenu','.sor_submenu,.report_submenu,.return_submenu,.replace_submenu')}>
         <OrderIcon className="custom_submenu_icon" /> Orders  </a>

         <div className="submenu orders_submenu" style={current_url.search("orders/") === -1 ? {'display':'none'} : {'display':'block'}}>
            {orderitems.map(item => (
              <MenuItemLink
                key={item.name}
                to={`/${item.to}`}
                primaryText={item.name}
                leftIcon={item.icon}
                onClick={self.props.onMenuClick}
              />
            ))}
         </div> 

         <a className="custom_submenu" href="../index.php?route=seller/manage-inventory&sort=product_id&order=DESC">
         <ListIcon className="custom_submenu_icon" /> Inventory  </a>

        <a className="custom_submenu" onClick={()=>menu_toggle('.sor_submenu','.orders_submenu,.report_submenu,.return_submenu,.replace_submenu')}>
         <SorInventoryIcon className="custom_submenu_icon" /> SOR Inventory  </a>
        <div className="submenu sor_submenu" style={current_url.search("sor/") === -1 ? {'display':'none'} : {'display':'block'}}>
            {soritems.map(item => (
              <MenuItemLink
                key={item.name}
                to={`/${item.to}`}
                primaryText={item.name}
                leftIcon={item.icon}
                onClick={self.props.onMenuClick}
              />
            ))}
         </div>

        <a className="custom_submenu" onClick={()=>menu_toggle('.return_submenu','.orders_submenu,.report_submenu,.sor_submenu,.replace_submenu')}>
         <ReturnIcon className="custom_submenu_icon" /> Returns  </a>
        <div className="submenu return_submenu" style={current_url.search("returns/") === -1 ? {'display':'none'} : {'display':'block'}}>
            {returnitems.map(item => (
              <MenuItemLink
                key={item.name}
                to={`/${item.to}`}
                primaryText={item.name}
                leftIcon={item.icon}
                onClick={self.props.onMenuClick}
              />
            ))}
         </div> 

         <a className="custom_submenu" onClick={()=>menu_toggle('.replace_submenu','.orders_submenu,.report_submenu,.sor_submenu,.return_submenu')}>
         <ReplaceIcon className="custom_submenu_icon" /> Replacement  </a>
        <div className="submenu replace_submenu" style={current_url.search("replacement/") === -1 ? {'display':'none'} : {'display':'block'}}>
            {replaceitems.map(item => (
              <MenuItemLink
                key={item.name}
                to={`/${item.to}`}
                primaryText={item.name}
                leftIcon={item.icon}
                onClick={self.props.onMenuClick}
              />
            ))}
         </div>         

         {items.map(item => (
            <MenuItemLink
                key={item.name}
                to={`/${item.to}`}
                primaryText={item.name}
                leftIcon={item.icon}
                onClick={self.props.onMenuClick}
            />
        ))}


         <a className="custom_submenu" onClick={()=>menu_toggle('.report_submenu','.orders_submenu,.sor_submenu,.return_submenu,.replace_submenu')}>
         <GstIcon className="custom_submenu_icon" /> GST Report  </a>
        <div className="submenu report_submenu" style={current_url.search("report/") === -1 ? {'display':'none'} : {'display':'block'}}>
            {reportitems.map(item => (
              <MenuItemLink
                key={item.name}
                to={`/${item.to}`}
                primaryText={item.name}
                leftIcon={item.icon}
                onClick={self.props.onMenuClick}
              />
            ))}
         </div>

          {profileitem.map(item => (
            <MenuItemLink
                key={item.name}
                to={`/${item.to}`}
                primaryText={item.name}
                leftIcon={item.icon}
                onClick={self.props.onMenuClick}
            />
        ))}

        <Responsive xsmall={self.props.logout} medium={null} />
    </div>
)
}}

const mapStateToProps = state => ({
    resources: getResources(state),
});

export default withRouter(connect(mapStateToProps, {})(Menu));