import React, { Component } from 'react'
import Api from '../../api/Api'
import { connect } from 'react-redux'
import AccountUserDetail from './edit'
import AccountChangePassword from './change_password'
import AccountBankDetail from './bank_detail'
import AccountAddressBook from './address_book'
import AccountOrderHistory from './order_history'
import AccountOrderDetail from './order_detail'
import AccountStatement from './account_statement'
import AccountStatementBreakup from './account_statement_breakup'
import RecentView from './recent_view'
import AccountWishlist from '../wishlist'
import $ from 'jquery'

class Account  extends Component {
  
 render(){
$("html, body").animate({ scrollTop: 0 }, 800);
 
    if(this.props.match.path === Api.folder_path+'account/my_account_edit')
    {
      return (<AccountUserDetail />) 
    }
    else if(this.props.match.path === Api.folder_path+'account/change_password')
    {
      return (<AccountChangePassword />) 
    }
    else if(this.props.match.path === Api.folder_path+'account/bank_detail')
    {
      return (<AccountBankDetail />) 
    }
    else if(this.props.match.path === Api.folder_path+'account/address_book')
    {
      return (<AccountAddressBook />) 
    }
    else if(this.props.match.path === Api.folder_path+'account/order_history')
    {
      return (<AccountOrderHistory />) 
    }
    else if(this.props.match.path === Api.folder_path+'account/order_detail/:order_id/:suborder_id')
    {
      return (<AccountOrderDetail order_id={this.props.match.params.order_id} suborder_id={this.props.match.params.suborder_id} />) 
    }
    else if(this.props.match.path === Api.folder_path+'account/statement')
    {
      return (<AccountStatement />) 
    }
    else if(this.props.match.path === Api.folder_path+'account/breakup/:order_id/:order_no')
    {
      return (<AccountStatementBreakup order_id={this.props.match.params.order_id} order_no={this.props.match.params.order_no} />) 
    }
    else if(this.props.match.path === Api.folder_path+'account/wishlist')
    {
      return (<AccountWishlist />) 
    }
    else if(this.props.match.path === Api.folder_path+'account/recent_view')
    {
      return (<RecentView />) 
    }
    else
    {
      window.location.assign(Api.folder_path);
      return(<span>url not match</span>)
    }

  }
}


function mapStateToProps(state){
  return {
    userlogin: state.headerReducer.userlogin
  };
}
export default connect(mapStateToProps)(Account);
