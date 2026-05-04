 import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux';
import { Link } from 'react-router-dom'
import Helmet from 'react-helmet';
import $ from 'jquery'
// import autocomplete from 'jquery-ui/ui/widgets/autocomplete'
import Api from '../../api/Api'
import Menu from '../menu'
import SearchBar from '../search'
import  'jquery-ui/themes/base/autocomplete.css'
import  './index.css'
import Anchor from '../../component/anchor'
import { getCartData } from '../../actions/CartAction'
import { userloginData, cartData, wishlistData,searchAutocompleteData } from '../../actions/HeaderAction';
import { OptPopupOpen } from '../../actions/LoginAction';
import Drawer from 'material-ui/Drawer';
import TextField from 'material-ui/TextField';
import IconButton from '@material-ui/core/IconButton';
import SearchIcon from '@material-ui/icons/Search';

import Otpform from '../login/otp_form'
import LoginForm from '../login/login_form'
import Register from '../login/register'
import Success from '../login/success'
import custom from '../../custom/custom';
import webengage from '../../custom/webengage';

class Header  extends Component {

 constructor(props)
 {
     super(props);
      this.state = {
        menuDrawerOpen: false,
        accountDrawerOpen: false,
        searchOpen: false,
        show_search_bar:0,
        search:''
      };
      
      this.search_bar_switch   = this.search_bar_switch.bind(this);
      this.login_popup         = this.login_popup.bind(this);
      this.toggleDrawer        = this.toggleDrawer.bind(this);
      this.toggleAccountDrawer = this.toggleAccountDrawer.bind(this);
      this.toggleSearchDrawer  = this.toggleSearchDrawer.bind(this);
      this.searchAutocomplete  = this.searchAutocomplete.bind(this);
      this.logout              = this.logout.bind(this); 
 } 

 componentDidMount()
 {
   let self = this;
   $(document).delegate('.parrent_link, .HomeLink', 'click', function(e) {
      $('.menu_div').parent().removeClass('submenuWidth');
      $('.navbar-toggle').trigger('click');
      $(".child_menu_container").hide('slow','swing');
      $(".child_menu_container").hide('slow','swing');
    });

   $(document).delegate('.sub_link', 'click', function(e) {
      $('.menu_div').parent().removeClass('submenuWidth');
      $('.lastmenu_list').slideUp();
      $('.navbar-toggle').trigger('click');
      $(".child_menu_container").hide('slow','swing');
    });

   $(document).delegate('#myNavmenu_2 a', 'click', function(e) {
      $("#side-collapse_right").toggleClass('in');
      $(".side-collapse-container").toggleClass('out');
    });


  $(document).click(function (event) {
        var clickover = $(event.target);
        var menu1 = $("#myNavmenu").parent().hasClass("in");
        //parrent_link parent_menu_icon
        if (menu1 === false && !clickover.is('#myNavmenu.mobile_nav_colllapse') && !clickover.hasClass("icon-bar") && !clickover.hasClass("navbar-toggle") && !clickover.hasClass("parent_menu_icon")){
            //$("button.navbar-toggle").click();
            $("#side-collapse").addClass("in");
        }
        //console.log(clickover.attr('class'));
        var menu2 = $("#myNavmenu_2").parent().hasClass("in");
       // if(menu2){console.log('true');}else{console.log('false');}
        if (menu2 === false && !clickover.is('#myNavmenu_2') && !clickover.hasClass("fa-user-o")){
             $("#side-collapse_right").addClass("in");
        }
    });


    $(document).on('click','.parent_menu_icon',function (event) {
      $('.menu_div').parent().addClass('submenuWidth');
    });
    
    $(document).on('click','.submenu_title',function (event) {

      if ($(this).hasClass("active"))
        {
          $(".submenu_title").removeClass('active');
          $('.lastmenu_list').slideUp(); 
        }
        else
        {
           $(".submenu_title").removeClass('active');
           $('.lastmenu_list').slideUp(); 
           $(this).addClass('active');
           $(this).next('.lastmenu_list').slideToggle();
        } 
    });
    
    $(document).on('click','.close_submenu',function (event) {
      $('.menu_div').parent().removeClass('submenuWidth');
    });

    $(document).delegate('#search_input_box_drawer', 'keydown', function(e)
    { 
       if (e.keyCode === 13) 
       {
          self.setState({searchOpen: false});
          var search = $(this).val();
          search = search.replace(/&/g, "`");
          window.location.href = Api.folder_path+'category#!filter=&price_filter=&option=&sort=sort_order&order=ASC&rating_filter=&search='+encodeURIComponent(search);
       }
     });
   
 } 
 
 search_bar_switch()
 {
   this.setState({show_search_bar: 1});
 }

 login_popup()
 {
   this.props.dispatch(OptPopupOpen(1));
 }

 toggleDrawer(){
   this.setState({menuDrawerOpen: !this.state.menuDrawerOpen});
 }

toggleAccountDrawer(){
   this.setState({accountDrawerOpen: !this.state.accountDrawerOpen});
 }

 toggleSearchDrawer(e, search='', drawerShow=0)
 { 
  if(!drawerShow)
  {
    if(search !== '') { 
      this.setState({search:search});
      this.searchAutocomplete();
     }

    this.setState({searchOpen: !this.state.searchOpen});
    if(!this.state.searchOpen){
    $('.search_bar_drawer').parent().addClass('drawerShow');
    $('.search_bar_drawer').parent().removeClass('drawerCollapse');
    }else{
    $('.search_bar_drawer').parent().removeClass('drawerShow');
    $('.search_bar_drawer').parent().addClass('drawerCollapse');
    }
    $('#search_input_box_drawer').val(this.state.search);
  }
  else
  {
    this.setState({search:search}); 
    $('#search_input_box_drawer').val(search); 
    $('#search_input_box_drawer').focus();
    if(search === '') { this.searchAutocomplete(); }
  }
 }

 searchAutocomplete(){
  var value = $('#search_input_box_drawer').val();
  this.props.dispatch(searchAutocompleteData(value));
 }


 logout()
 { 
     custom.delete_cookie('cart_session_id');
     custom.delete_cookie('customer_access_token');
     custom.delete_cookie('customer_id');
     custom.delete_cookie('customer_mobile');
     custom.delete_cookie('register_user');
     custom.delete_cookie('wishlist_session_id');
     var self = this;
     webengage.logout();
     setTimeout(function(){ window.location.href = self.props.userlogin.logout; }, 500);
 }


 render(){

  
  var show_search_bar = 0;
  if(window.location.pathname === "" || window.location.pathname === Api.folder_path){
    show_search_bar = 1 ;
   }

    return (
    <header className="navbar-fixed-top" role="navigation">
       <Helmet>
        <meta charSet="utf-8" />
        <title>{this.props.logo.meta_title}</title>
        <meta name="description" content={this.props.logo.meta_description} />
        <meta name="keywords" content={this.props.logo.meta_keywords} /> 
      </Helmet>
      <div className="pull-left">
        <span className="navbar-header">
          <button className="navbar-toggle" onClick={this.toggleDrawer} type="button">
              <span className="sr-only">Toggle navigation</span>
              <span className="icon-bar"></span>
              <span className="icon-bar"></span>
              <span className="icon-bar"></span>
          </button>
          
          <div className="logo_box">
          <Link to={Api.folder_path} onClick={this.search_bar_switch}><img src={this.props.logo.mobile_logo} alt={this.props.logo.name} className="img-responsive" /></Link>
          </div> 
        </span> 
      </div>
      <div className="pull-right">
         
        
          <IconButton className={!show_search_bar ? "header_search_icon" : "header_search_icon hide"} aria-label="Search" onClick={this.toggleSearchDrawer}>
          <SearchIcon />
          </IconButton>
        
         
          <span className="head_right_icon">

            {parseInt(this.props.wishlist.wishlist_total,10) ?
             <span className="wishlist_count" id="wishlist-total-span">{this.props.wishlist.wishlist_total}</span>
             : ''}

             {this.props.wishlist.logged ?
              <Link to={Api.folder_path+"account/wishlist"}><i className="fa fa-heart" aria-hidden="true"></i></Link>
              :
              <Anchor id="wishlist-total" icon="fa fa-heart" onClick={this.login_popup} />
             }
          </span>
      

          <span className="head_right_icon">
          {parseInt(this.props.cart.total_in_cart,10) ?
          <span className="cart_amount" id="cart-total">{this.props.cart.total_in_cart}</span>
          : ''}
          <Link to={Api.folder_path+"cart"}><i className="fa fa-shopping-cart" aria-hidden="true"></i></Link></span>

          {this.props.userlogin.logged ?
          <span className="head_right_icon">
          <Anchor icon="fa fa-user" onClick={this.toggleAccountDrawer}/>
          </span>
          :
          <span className="head_right_icon">
          <Anchor icon="fa fa-user" id="login_link_header" onClick={this.login_popup} />
          </span>
          }
      </div>

      <div className={!show_search_bar ? "search_bar search_bar_header hide" : "search_bar search_bar_header"} >
      <TextField className="serch_input_box"
      hintText= {this.props.search ? this.props.search :  <span><i className="fa fa-search" style={{color:'#666666'}} aria-hidden="true"></i> &nbsp; Search from more than 1 Lac Products... </span> }
      onClick={this.toggleSearchDrawer}
      />
      </div>
      

      <SearchBar searchOpen={this.state.searchOpen} clickHandler={this.toggleSearchDrawer} searchHandler={this.searchAutocomplete} searchList = {this.props.search_data} />
        
     <div className="clearfix"></div> 
     <Drawer
          docked={false}
          width={300}
          open={this.state.menuDrawerOpen}
          onRequestChange={(menuDrawerOpen) => this.setState({menuDrawerOpen})}
          onClick={(menuDrawerOpen) => this.setState({menuDrawerOpen})}
        >
      <Menu logo={this.props.logo} currency_data={this.props.currency_data} search_bar_switch={this.search_bar_switch} />
      </Drawer>
      
      {this.props.userlogin.logged ?

       <Drawer
          docked={false}
          width={250}
          openSecondary={true}
          open={this.state.accountDrawerOpen}
          onRequestChange={(accountDrawerOpen) => this.setState({accountDrawerOpen})}
          onClick={(accountDrawerOpen) => this.setState({accountDrawerOpen})}
        >
         <ul className="nav navmenu-nav mb_right_menu">
           <li className="login_name"><i className="fa fa-user" aria-hidden="true"></i> {this.props.userlogin.cust_name}</li>
           <li className="highlighted"><Link to={Api.folder_path+"account/my_account_edit"} onClick={this.toggleAccountDrawer}><i className="fa fa-pencil-square-o" aria-hidden="true"></i> Edit Profile</Link></li>
            <li className="highlighted"><Link to={Api.folder_path+"account/change_password"} onClick={this.toggleAccountDrawer}><i className="fa fa-unlock" aria-hidden="true"></i> Change Password</Link></li>
            <li className="highlighted"><Link to={Api.folder_path+"account/bank_detail"} onClick={this.toggleAccountDrawer}><i style={{fontSize:'12px'}} className="fa fa-university" aria-hidden="true"></i> Bank Detail</Link></li>
            <li className="highlighted"><Link to={Api.folder_path+"account/address_book"} onClick={this.toggleAccountDrawer}><i className="fa fa-address-book-o" aria-hidden="true"></i> Address Book</Link></li>
           <li className="highlighted"><Link to={Api.folder_path+"account/order_history"} onClick={this.toggleAccountDrawer}><i className="fa fa-bars" aria-hidden="true"></i> Order History</Link></li>
           {this.props.userlogin.account_statement ?
           <li className="highlighted"><Link to={Api.folder_path+"account/statement"} onClick={this.toggleAccountDrawer}><i className="fa fa-file-excel-o" aria-hidden="true"></i> Account Statement</Link></li>
           : ''
           }
           <li className="highlighted accountMenuLiSeprater" >&nbsp;</li>
           
           <li className="highlighted"><Link to={Api.folder_path+"account/wishlist"} onClick={this.toggleAccountDrawer}><i className="fa fa-heart" aria-hidden="true"></i> Short List</Link></li>
           <li className="highlighted"><Link to={Api.folder_path+"account/recent_view"} onClick={this.toggleAccountDrawer} ><i className="fa fa-list-alt" aria-hidden="true"></i> Recently Viewed</Link></li>
           <li className="highlighted accountMenuLiSeprater">&nbsp;</li>

           <li className="highlighted"><Link to={Api.folder_path+"i/post-your-requirement"} onClick={this.toggleAccountDrawer} ><i className="fa fa-wpforms" aria-hidden="true"></i> Post Your Request</Link></li>
           <li className="highlighted accountMenuLiSeprater">&nbsp;</li>

           <li className="highlighted"><span className="logout_btn" onClick={this.logout}><i className="fa fa-lock" aria-hidden="true"></i> Logout</span></li>
         </ul>
        </Drawer>
      : '' }
   
   {!this.props.userlogin.logged ?
       <div className="login_popup">
        <Otpform />
        <LoginForm />
        <Register />
       </div> 
    : '' } 
       
     <Success />
    </header>
    )
  }
}


function mapStateToProps(state){
  return {
    logo: state.headerReducer.logo,
    wishlist: state.headerReducer.wishlist,
    cart: state.headerReducer.cart,
    userlogin: state.headerReducer.userlogin,
    search_data: state.headerReducer.search_data,
    currency_data: state.headerReducer.currency_data,
    search: state.categoryReducer.category_data.search,
    actions: bindActionCreators(userloginData, cartData, wishlistData, getCartData,searchAutocompleteData,OptPopupOpen)
  };
}
export default connect(mapStateToProps)(Header);