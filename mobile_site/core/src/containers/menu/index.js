import React, { Component } from 'react'
import { connect } from 'react-redux'
import { Link } from 'react-router-dom'
import { bindActionCreators } from 'redux';
import $ from 'jquery'
import Api from '../../api/Api'
import  './index.css'
import custom from '../../custom/custom'
import { customstoreData } from '../../actions/MenuAction';
import Submenu from './submenu'
import {List} from 'material-ui/List';
import Subheader from 'material-ui/Subheader';
import MobileTearSheet from './MobileTearSheet';
import { latestData, trendingData } from '../../actions/HomeAction';
import { setCurrencySession } from '../../actions/HeaderAction';
import ReactFlagsSelect from 'react-flags-select';
import 'react-flags-select/css/react-flags-select.css';

class Menu  extends Component {
 
  constructor(props)
  {
     super(props);
     this.custom_store       = this.custom_store.bind(this);
     this.generateLinks      = this.generateLinks.bind(this);
     this.generateMenuLinks  = this.generateMenuLinks.bind(this);
     this.onSelectCurrency   = this.onSelectCurrency.bind(this);
  }

  custom_store()
  {
    this.props.dispatch(customstoreData(custom.getCookie('custom_store')));
    this.props.dispatch(latestData());
    this.props.dispatch(trendingData());
  }

  generateLinks (item, index) { 
    var href = item.mobile_href; 
    if(item.mobile_href.charAt(0) === "/")
     {
        href = item.mobile_href.substr(1);  
     }

    return <li  className="highlighted" key={index}>
         <Link to={Api.folder_path+href} className="parrent_link">{ $('<div/>').html(item.title).text() } </Link>
        </li>

  }


// <img src="https://cdnimages.net/img/logo.png" alt="OpenCart" title="OpenCart">
  generateMenuLinks (item, index) { 

     var href = item.href;  

    return <li key={index} className="highlighted opt">

            {item.children ?
            <span className="parrent_link_static" onClick={() => $('#child_menu_container'+index).toggle('fast','swing') }>{ $('<div/>').html(item.link_title).text() }</span>
            :
           <Link className="parrent_link" to={Api.folder_path+href}>{ $('<div/>').html(item.link_title).text() }</Link> 
            }
             { item.children ?
             <i className="fa fa-angle-right parent_menu_icon" aria-hidden="true" onClick={() => $('#child_menu_container'+index).toggle('fast','swing') }></i>
             : ''
              }

             {item.children ?

                <div className="child_menu_container" id={"child_menu_container"+index}>
                  <List>
                  <Subheader className="SubMenuHeader">
                  <span className="nav_singles_store_btn close_submenu" onClick={() => $('#child_menu_container'+index).toggle('fast','swing') }> <i className="fa fa-long-arrow-left" aria-hidden="true"></i> </span>
                  
                  <Link className="SubMenuHeading ListItemOnClick" id={href} to={Api.folder_path+href}>{ $('<div/>').html(item.link_title).text() }</Link>
                  
                  </Subheader>
                  <MobileTearSheet>
                   <ul className="submenu_list">
                   { 
                    item.children.map((lastitem, subindex) => {
                     return (<Submenu item={lastitem} key={subindex} count={subindex} parent_index={index} /> )
                     })
                   }
                   </ul>
                  </MobileTearSheet>

                </List>  
                </div>
                : ''
               }
           </li>
  }

  onSelectCurrency(country)
  {
    var countryCode = 'IND';

    $.map(this.props.currency_data.currency_list, function(currency, index) 
    {
      if(currency.country_code.toUpperCase() === country)
      {
        countryCode = index;
      }
    });
    
    custom.createCookie("currency", countryCode, 7);

    this.props.dispatch(setCurrencySession(countryCode));

    setTimeout(function(){ window.location.reload(); }, 500);

  }


 render(){
  
  let countries = [];
  let customLabels = {};
  let default_country = "IN";

if(this.props.currency_data)
{
  $.map(this.props.currency_data.currency_list, function(currency, index) {
      countries.push(currency.country_code.toUpperCase());
      customLabels[currency.country_code.toUpperCase()] = currency.text;
      
      if(custom.getCookie("currency") === index)
      {
         default_country = currency.country_code.toUpperCase();
      }

    });
}

    return (
          <div className="menu_div">

            <ul className="nav navmenu-nav" >
                <li className="HomeMenu">
                {custom.getCookie('custom_store') ?
                 <Link className="HomeLink" to={Api.folder_path} onClick={this.custom_store} id="store_switch">{custom.getCookie('store_switch_label')}</Link>
                 :
                <Link className="HomeLink" to={Api.folder_path} onClick={this.custom_store} id="store_switch">{this.props.custom_store.store_switch_label}</Link>
                }

                {this.props.userlogin.international_store && this.props.currency_data ?  
                 <ReactFlagsSelect
                       defaultCountry={default_country}
                       countries={countries}
                       customLabels={customLabels}
                       onSelect={this.onSelectCurrency}
                        />
                  : ''}
                </li>
                <li className="HomeMenu sale_link">
                 <Link className="HomeLink" to={Api.folder_path+"category#!filter=&price_filter=&option=&sort=sort_order&order=ASC&rating_filter=&search=&stock_filter=0&last_filter_action=&clearance_sale=1"}>Sale</Link>
                </li>
                {this.props.menus.menus ? this.props.menus.menus.map(this.generateMenuLinks) : '' }
                <li className="highlighted accountMenuLiSeprater" >&nbsp;</li>
                <li className="highlighted"><Link to={Api.folder_path+"i/about-us"} className="parrent_link">About Us</Link></li>
                <li className="highlighted"><Link to={Api.folder_path+"i/policies"} className="parrent_link">Policies</Link></li>
                {this.props.userlogin.is_dropshipper === "0" ?
                <li className="highlighted"><Link to={Api.folder_path+"i/dropshipper"} className="parrent_link">Become A Dropshipper</Link></li>
                : '' }
                <li className="highlighted"><Link to={Api.folder_path+"i/storelocator"} className="parrent_link">Store Locator</Link></li>
                <li className="highlighted"><Link to={Api.folder_path+"i/contact-us"} className="parrent_link">Contact Us</Link></li>
            </ul>
        </div>
    )
  }
}


function mapStateToProps(state){
  return {
    userlogin: state.headerReducer.userlogin,
    menus: state.menuReducer.menus,
    information: state.menuReducer.information,
    custom_store: state.menuReducer.custom_store,
    actions: bindActionCreators(customstoreData, latestData, trendingData, setCurrencySession)
  };
}
export default connect(mapStateToProps)(Menu);
