import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux';
import { Link } from 'react-router-dom'
import $ from 'jquery'
import Helmet from 'react-helmet';
import { product_listData, product_pagination_listData, CategoryFilterPathSuccess, CategoryPathKeywordSuccess, getFilterData} from '../../actions/CategoryAction';
import Product from './product'
import Sort from './sort'
import Price from './price'
import FilterGroup from './filter_group'
import LoadingProduct from '../loading/loading_product'
import SearchNoResults from '../loading/search_no_results'
import NoResults from '../loading/no_results'
import ApiError  from '../loading/api_error'
import custom from '../../custom/custom'
import Anchor from '../../component/anchor'

import {GridList} from 'material-ui/GridList';  

import AppBar from '@material-ui/core/AppBar';
import Tabs from '@material-ui/core/Tabs';
import Tab from '@material-ui/core/Tab';
import Api from '../../api/Api'
import ScrollToTop from 'react-scroll-up';
import Drawer from 'material-ui/Drawer';

import  './index.css'

class Category  extends Component {

   constructor(props)
   {
     super(props);
     this.paginationData               = this.paginationData.bind(this);
     this.getProductData               = this.getProductData.bind(this);
     this.getFilterData                = this.getFilterData.bind(this);
     this.callAjaxToFilter             = this.callAjaxToFilter.bind(this);
     this.applyFilter                  = this.applyFilter.bind(this);
     this.callAjaxToRating             = this.callAjaxToRating.bind(this);
     this.callAjaxToPrice              = this.callAjaxToPrice.bind(this);
     this.callAjaxToRemovePrice        = this.callAjaxToRemovePrice.bind(this);
     this.callAjaxToSort               = this.callAjaxToSort.bind(this);
     this.callAjaxToRemoveAllFilter    = this.callAjaxToRemoveAllFilter.bind(this);
     this.callAjaxToStock              = this.callAjaxToStock.bind(this);
     this.addFilterInHistoryState      = this.addFilterInHistoryState.bind(this);
     this.getUrlParameter              = this.getUrlParameter.bind(this);
     this.retry                        = this.retry.bind(this);
     this.toggleSortDrawer             = this.toggleSortDrawer.bind(this);
     this.toggleFilterDrawer           = this.toggleFilterDrawer.bind(this);
     this.getProductListWithUrlFilters = this.getProductListWithUrlFilters.bind(this);
     this.search_out_of_stock          = this.search_out_of_stock.bind(this);

    this.state = {
      page: 1,
      search: '',
      stock_filter : 0,
      search_filter: [],
      search_option: [],
      search_price:  'all',
      search_rating: 'all',
      search_sort: 'sort_order&order=ASC',
      product_count: -1,
      show_limit:28,
      price_with_currency:'',
      last_filter_action:'',
      custom_store: custom.getCookie('custom_store'),
      sortDrawerOpen: false,
      filterDrawerOpen: false,
      clearance_sale: 0,
    };
   } 


  componentDidMount()
  {  
    var self = this; 
    $(document).delegate('.nav-tabs a', 'click', function(e) {
       var tab_id = $(this).attr('data-target');
       $(".filters_tab_btn li").removeClass("active");
       $(this).parent("li").addClass("active");
       $(".filter_group").removeClass('in active');
       $(tab_id).addClass('in active');
    });

    $(document).delegate('.sort_filter_bottom_btn', 'click', function(e) {
      self.props.sorts.map((value, index) => {
          if(value.selected === 'active')
          {
            $('#sort_'+index).prop('checked', true);
            return true;
          } 
          else
          {
            return true;
          } 
      });
    });

  if(!this.props.path_keyword && this.props.path_keyword !== this.props.match.params.path)
   {
    this.getProductListWithUrlFilters();
   }
   
    $(window).scroll(function(){
    if($(".btn_browse_more").hasClass( "page_auto_load" ))
      { 
        let pos_browse_more = $('.page_auto_load').offset().top;
        let scrollTop = $(window).scrollTop();
        let pos_area = pos_browse_more-5000;
         if (scrollTop > pos_area){
           $(".page_auto_load").click();
         }
      }   
    });
 }

  getProductListWithUrlFilters(type='product_list')
  {
    var filter = [];
    var price_filtering = 'all';
    var option = [];
    var sorting = 'sort_order&order=ASC';
    var get_rating_filter = 'all';
    var get_search = '';
    var stock_filter = 0;
    var last_filter_action = '';
    var clearance_sale = 0;

    if(window.location.hash) 
    {
       var hash = window.location.hash;
       this.props.dispatch(CategoryFilterPathSuccess(hash));

       var filter_arr_str = this.getUrlParameter('filter',hash);
       if(filter_arr_str && typeof filter_arr_str != 'undefined')
       {
         var arr_filter_data = filter_arr_str.split(",");
         arr_filter_data = arr_filter_data.filter(Boolean);
          if(arr_filter_data.length > 0) 
          {
            $.each(arr_filter_data, function (index, value) {
             filter.push(parseInt(value, 10));
            });
          }
        }  


       var option_str = this.getUrlParameter('option',hash);
       if(option_str && typeof option_str != 'undefined')
        { 
          var arr_option_data = option_str.split(',') ;
          arr_option_data = arr_option_data.filter(Boolean);
            if(arr_option_data.length > 0) 
             {
                $.each(arr_option_data, function (index, value) {
                 option.push(value);
                });
            }
        }  
        

        var price_filter = this.getUrlParameter('price_filter',hash);
        if(price_filter && typeof price_filter != 'undefined')
        {
          price_filtering = price_filter;
        }

        var search_sort  = this.getUrlParameter('sort',hash);
        if(search_sort && typeof search_sort != 'undefined')
        {
          var sorting_order = this.getUrlParameter('order',hash);
          if(sorting_order && typeof sorting_order != 'undefined')
          {
            search_sort = search_sort+ "&order=" +sorting_order;  
          }
          sorting = search_sort;
        }


        var rating_filter = this.getUrlParameter('rating_filter',hash);
        if(rating_filter && typeof rating_filter != 'undefined')
        {
          get_rating_filter = rating_filter;
        }
        

        var search = this.getUrlParameter('search',hash);
        if(search && typeof search != 'undefined')
        {
          get_search = search;
        }

        var get_stock_filter = this.getUrlParameter('stock_filter',hash);

        if(get_stock_filter && get_stock_filter === '1')
        {
           stock_filter = get_stock_filter;
        }



        var last_filter = this.getUrlParameter('last_filter_action',hash);
        if(last_filter && typeof last_filter != 'undefined')
        {
           last_filter_action = last_filter;
        }

        var get_clearance_sale = this.getUrlParameter('clearance_sale',hash);
        if(get_clearance_sale && typeof get_clearance_sale != 'undefined')
        {
            clearance_sale = get_clearance_sale;
        }

    }
  
       this.setState({search_filter:filter});
       this.setState({search_price:price_filtering});
       this.setState({search_option:option});
       this.setState({search_sort:sorting});
       this.setState({search_rating:get_rating_filter});
       this.setState({search:get_search});
       this.setState({stock_filter:stock_filter});
       this.setState({last_filter_action:last_filter_action});
       this.setState({clearance_sale:clearance_sale});

   if(type === 'product_list')
   {
     this.getProductData(filter, option, get_rating_filter, price_filtering, sorting, get_search, stock_filter, last_filter_action, clearance_sale);
   }
   else
   {
     this.getFilterData(filter, option, get_rating_filter, price_filtering, sorting, get_search, stock_filter, last_filter_action, clearance_sale);
   }

  }


  getFilterData(search_filter, search_option, search_rating, search_price, search_sort, search, stock_filter, last_filter_action)
  {
     $('body').removeClass('loaded').addClass('loading');
     var search_filter_join       = search_filter.join();
     search_option                = search_option.join();
     this.setState({page: 1});
     var filter_data = 'path='+this.props.match.params.path+'&page='+1+'&filter='+search_filter_join+'&option='+search_option+'&rating_filter='+search_rating+'&price_filter='+search_price+'&sort='+search_sort+'&search='+search+'&stock_filter='+stock_filter+'&last_filter_action='+last_filter_action+'&custom_store='+this.state.custom_store;
     this.props.dispatch(getFilterData(search_filter, filter_data, last_filter_action));
  }

  getProductData(search_filter, search_option, search_rating, search_price, search_sort, search, stock_filter, last_filter_action, clearance_sale)
  {
     $("html, body").animate({ scrollTop: 0 }, 800);
     $('body').removeClass('loaded').addClass('loading');
     var search_filter_join  = search_filter.join();
     search_option           = search_option.join();
     var customer_id               =  custom.getCookie('customer_id');
     var customer_access_token     = custom.getCookie('customer_access_token');
     var cart_session_id           = custom.getCookie('cart_session_id');
     var wishlist_session_id     = custom.getCookie('wishlist_session_id');
     this.setState({page: 1});
     
     var filter_data = '';
     if(this.props.match.params.path2 && this.props.match.params.path2 !== '')
     {
        filter_data = 'path='+this.props.match.params.path+'/'+this.props.match.params.path2+'&page='+1+'&filter_retain=1&filter='+search_filter_join+'&option='+search_option+'&rating_filter='+search_rating+'&price_filter='+search_price+'&sort='+search_sort+'&search='+search+'&stock_filter='+stock_filter+'&last_filter_action='+last_filter_action+'&custom_store='+this.state.custom_store+'&customer_id='+customer_id+'&customer_access_token='+customer_access_token+'&wishlist_session_id='+wishlist_session_id+'&cart_session_id='+cart_session_id+'&clearance_sale='+clearance_sale;
        this.setState({path: this.props.match.params.path+'/'+this.props.match.params.path2});
     }
     else
     {
        filter_data = 'path='+this.props.match.params.path+'&page='+1+'&filter_retain=1&filter='+search_filter_join+'&option='+search_option+'&rating_filter='+search_rating+'&price_filter='+search_price+'&sort='+search_sort+'&search='+search+'&stock_filter='+stock_filter+'&last_filter_action='+last_filter_action+'&custom_store='+this.state.custom_store+'&customer_id='+customer_id+'&customer_access_token='+customer_access_token+'&wishlist_session_id='+wishlist_session_id+'&cart_session_id='+cart_session_id+'&clearance_sale='+clearance_sale;
        this.setState({path: this.props.match.params.path});
     }
     
     if(this.props.handpicked_ids)
     {
      filter_data = filter_data+'&handpicked_ids='+this.props.handpicked_ids;
     }
     if(this.props.random_string)
     {
      filter_data = filter_data+'&random_string='+this.props.random_string;
     }
     if(this.props.product_total)
     {
      filter_data = filter_data+'&product_total='+this.props.product_total;
     }

     this.props.dispatch(product_listData(search_filter, filter_data));
  }

  paginationData()
  { 
    $(".btn_browse_more").removeClass("page_auto_load");
    let page = this.state.page;
        page = page+1;
    var search_filter = this.state.search_filter.join();
    var search_option = this.state.search_option.join();
    var clearance_sale = this.state.clearance_sale;
    var customer_id   =  custom.getCookie('customer_id');
    var customer_access_token = custom.getCookie('customer_access_token');
    var cart_session_id           = custom.getCookie('cart_session_id');
    var wishlist_session_id     = custom.getCookie('wishlist_session_id');
    this.setState({page: page});
    var filter_data = '';

    let path = this.props.match.params.path;
    if(this.props.match.params.path2 && this.props.match.params.path2 !== '')
    {
      path = path+'/'+this.props.match.params.path2;
    }
   
    filter_data = 'path='+path+'&page='+page+'&filter_retain=1&filter='+search_filter+'&option='+search_option+'&rating_filter='+this.state.search_rating+'&price_filter='+this.state.search_price+'&sort='+this.state.search_sort+'&search='+this.state.search+'&stock_filter='+this.state.stock_filter+'&last_filter_action='+this.state.last_filter_action+'&custom_store='+this.state.custom_store+'&customer_id='+customer_id+'&customer_access_token='+customer_access_token+'&wishlist_session_id='+wishlist_session_id+'&cart_session_id='+cart_session_id+'&clearance_sale='+clearance_sale;

    if(this.props.handpicked_ids)
    {
     filter_data = filter_data+'&handpicked_ids='+this.props.handpicked_ids;
    }

    if(this.props.random_string)
    {
     filter_data = filter_data+'&random_string='+this.props.random_string;
    }

    if(this.props.product_total)
    {
     filter_data = filter_data+'&product_total='+this.props.product_total;
    }
    this.props.dispatch(product_pagination_listData(filter_data));
  }

  applyFilter()
  {
    $(".filter_checkbox").removeClass("checkbox_visual_check");
    $(".filter_checkbox").removeClass("checkbox_visual_uncheck");
    this.addFilterInHistoryState(this.state.search_filter, this.state.search_option, this.state.search_rating, this.state.search_price, this.state.search_sort, this.state.search, this.state.stock_filter, this.state.last_filter_action);
    this.getProductData(this.state.search_filter, this.state.search_option, this.state.search_rating, this.state.search_price, this.state.search_sort, this.state.search, this.state.stock_filter, this.state.last_filter_action);
   
    this.setState({filterDrawerOpen: false });
    $('.filter_drawer_div').parent().removeClass('drawerShow');
    $('.filter_drawer_div').parent().addClass('drawerCollapse');
  }


  callAjaxToFilter(newValue) 
  {   
      var filter_id;
      var checked= false;
      if($("#filter"+newValue.props.filter.filter_id).is(":checked"))
      {
         filter_id = newValue.props.filter.filter_id;
         checked = true;
      }
    else
      {
         filter_id = newValue.props.filter.filter_id;
         checked   = false;
      }


      var filter = this.state.search_filter;
      if(checked)
      {
        filter.push(filter_id);
      }
      else
      {
       filter = $.grep(filter, function( a ) {
             return a !== filter_id;
             });
      }

      this.setState({search_filter:filter});
      this.setState({last_filter_action:"filter"});
      this.getFilterData(filter, this.state.search_option, this.state.search_rating, this.state.search_price, this.state.search_sort, this.state.search, this.state.stock_filter, "filter");
  }

   callAjaxToRemoveAllFilter() { 
      this.setState({search_filter:[]});
      this.setState({search_option:[]});
      this.setState({search_price:'all'});
      this.setState({search_rating:''});
      this.setState({last_filter_action:'filter'});
      this.getFilterData([], [], '', 'all', this.state.search_sort, this.state.search, this.state.stock_filter, 'filter');
    }   

  callAjaxToRating(newValue) {
      this.setState({search_rating:newValue});
      this.setState({last_filter_action:"rating"});
      this.getFilterData(this.state.search_filter, this.state.search_option, newValue, this.state.search_price, this.state.search_sort, this.state.search, this.state.stock_filter, "rating");
    }

  callAjaxToPrice(newValue) { 
      var search_price = newValue.min+'-'+newValue.max;
      this.setState({search_price:search_price});
      this.setState({last_filter_action:"price"});
      this.getFilterData(this.state.search_filter, this.state.search_option, this.state.search_rating, search_price, this.state.search_sort, this.state.search, this.state.stock_filter, "price");
    }

  callAjaxToRemovePrice() { 
      this.setState({search_price:'all'});
      this.setState({last_filter_action:"price"});
      this.getFilterData(this.state.search_filter, this.state.search_option, this.state.search_rating, 'all', this.state.search_sort, this.state.search, this.state.stock_filter, "price");
    }    

  callAjaxToSort(newValue) { 
      var search_sort = newValue.props.sort.query_string;
      this.setState({search_sort:search_sort});
      this.addFilterInHistoryState(this.state.search_filter, this.state.search_option, this.state.search_rating, this.state.search_price, search_sort, this.state.search, this.state.stock_filter, this.state.last_filter_action);
      this.getProductData(this.state.search_filter, this.state.search_option, this.state.search_rating, this.state.search_price, search_sort, this.state.search, this.state.stock_filter, this.state.last_filter_action);
      this.setState({sortDrawerOpen: false });
      $('.sort_drawer_div').parent().removeClass('drawerShow');
      $('.sort_drawer_div').parent().addClass('drawerCollapse');
    }

  callAjaxToStock(newValue) 
   { 
      this.setState({stock_filter:newValue});
      this.setState({last_filter_action:"stock"});
   }

  search_out_of_stock()
  {
    this.setState({stock_filter:1});
    this.setState({last_filter_action:"stock"});
    this.addFilterInHistoryState(this.state.search_filter, this.state.search_option, this.state.search_rating, this.state.search_price, this.state.search_sort, this.state.search, 1, "stock");
    this.getProductData(this.state.search_filter, this.state.search_option, this.state.search_rating, this.state.search_price, this.state.search_sort, this.state.search, 1, "stock");
  }   

  retry()
    {
       this.addFilterInHistoryState(this.state.search_filter, this.state.search_option, this.state.search_rating, this.state.search_price, this.state.search_sort, this.state.search, this.state.stock_filter, this.state.last_filter_action);
       this.getProductData(this.state.search_filter, this.state.search_option, this.state.search_rating, this.state.search_price, this.state.search_sort, this.state.stock_filter, this.state.last_filter_action);
    }

   addFilterInHistoryState(filter, option, rating_filter, price_filter, sorting, search, stock_filter, last_filter_action)
   {
       var final_url            = window.location;
       var filter_string       = '';

        if (window.location.hash) {
             var url = window.location.href;
             var url_page = url.split("&");
             final_url = url_page[0].split("#!");

              if(final_url.length === 1 )
               {
                 var url_page2 = url_page[1].split("#!");
                 final_url = final_url+'&'+url_page2[0];
               }
               else
               {
                final_url = final_url[0];
               }

              filter_string =  "#!filter=" + filter.join() + "&price_filter=" + price_filter + "&option="+ option.join()+ "&sort="+sorting + "&rating_filter="+ rating_filter+'&search='+search+'&stock_filter='+stock_filter+'&last_filter_action='+last_filter_action;
        } else {
             filter_string =   "#!filter=" + filter.join() + "&price_filter=" + price_filter + "&option="+ option.join()+ "&sort="+sorting + "&rating_filter="+ rating_filter+'&search='+search+'&stock_filter='+stock_filter+'&last_filter_action='+last_filter_action;
        }

        window.history.pushState('', null, final_url+filter_string);
        this.props.dispatch(CategoryFilterPathSuccess(filter_string));
    }

  getUrlParameter(sParam,url) {
        var sPageURL = decodeURIComponent(url.substring(1)),
                sURLVariables = sPageURL.split('&'),
                sParameterName,
                i;

        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');

            if (sParameterName[0] === sParam) {
                return sParameterName[1] === undefined ? true : sParameterName[1];
            }
        }
    }


  toggleSortDrawer(){
    this.setState({sortDrawerOpen: !this.state.sortDrawerOpen});
    if(!this.state.sortDrawerOpen){
    $('.sort_drawer_div').parent().addClass('drawerShow');
    $('.sort_drawer_div').parent().removeClass('drawerCollapse');
    }else{
    $('.sort_drawer_div').parent().removeClass('drawerShow');
    $('.sort_drawer_div').parent().addClass('drawerCollapse');
    }
  }

  toggleFilterDrawer(){
    this.setState({filterDrawerOpen: !this.state.filterDrawerOpen});
    if(!this.state.filterDrawerOpen){
    $('.filter_drawer_div').parent().addClass('drawerShow');
    $('.filter_drawer_div').parent().removeClass('drawerCollapse');
    this.getProductListWithUrlFilters('filter');
    }else{
    $('.filter_drawer_div').parent().removeClass('drawerShow');
    $('.filter_drawer_div').parent().addClass('drawerCollapse');
    }
  }


  render(){

  let self = this;
  let path = this.props.match.params.path;

  if(this.props.match.params.path2 && this.props.match.params.path2 !== '')
  {
    path = path+'/'+this.props.match.params.path2;
  }

 if(this.props.path_keyword)
   {
       var path_url_keyword = path.split("&");
       var path_keyword = this.props.path_keyword.split("&");

       console.log(path_keyword[0]+'t');
       console.log(path_url_keyword[0]+'m');
       if(path_keyword[0] !== path_url_keyword[0])
      {
       this.props.dispatch(CategoryPathKeywordSuccess(path));
       this.getProductListWithUrlFilters();
      }  
   }


  var search_val = this.getUrlParameter('search',window.location.hash);
  if(search_val && this.state.search !== search_val)
   {
      this.props.dispatch(CategoryPathKeywordSuccess(path));
      this.getProductListWithUrlFilters();
   }    

  if(this.props.rating_filter === '5')       { $('#rating_filter_5').prop('checked', true); } 
  else if(this.props.rating_filter === '4')  { $('#rating_filter_4').prop('checked', true); } 
  else if(this.props.rating_filter === '3')  { $('#rating_filter_3').prop('checked', true); } 
  else                                    { $('#rating_filter_all').prop('checked', true); }


let product_item = '';
if(this.props.product_count > 0)
{   
    product_item = this.props.product_list.map((product, index) => { 
              return (<Product product={product} key={index} count={index} />)
           })
}
else if(this.state.search !== '' && this.props.product_count === 0)
{
  product_item = <SearchNoResults search={this.state.search} stock_filter={this.state.stock_filter} search_out_of_stock={this.search_out_of_stock} menus={this.props.menus} />
}
else if(this.props.product_count === 0)
{
  product_item = <NoResults />
}
else
{
  product_item = <LoadingProduct />
}

var tab_new_value = this.props.selected_tab;
var seacrh_sort_text;

if(this.props.sorts)
{
  this.props.sorts.map((sort, index) => {

    if(sort.query_string === self.state.search_sort)
    {
       seacrh_sort_text = sort.text;
    }
    return false;

  });
}

    return (
    <section>  
      <Helmet>
        <meta charSet="utf-8" />
        <title>{this.props.meta_title}</title>
        <meta name="description" content={this.props.meta_description} />
        <meta name="keywords" content={this.props.meta_keywords} />
      </Helmet>
   {this.props.product_count === -2 ?
     <ApiError retry={this.retry} />
    :
   <div className="category_list_result category_head_margin"> 
    <div className="contner">
        {this.state.search === '' ?
          this.props.categories ?
          <AppBar position="static" className="category_appbar">
          <Tabs
            value={tab_new_value}
            onChange={(event,value) => { tab_new_value = value }}
            textColor="primary"
            scrollable
            scrollButtons="auto"
            className="category_tabs"
            classes={{ indicator: 'indicator_class' }}
          >
            {
            this.props.categories.map((sub_category,index) => {
              var name = sub_category.name; 
             return( <Tab className='category_tab' key={index} label={<Link className="category_tab_links" to={Api.folder_path+sub_category.keyword}>{$('<div/>').html(name).text()}</Link>} /> );
            })
            }

          </Tabs>
        </AppBar>
        : ''
        : 

        <AppBar position="static" className="category_appbar">
         <Tabs
            value={tab_new_value}
            onChange={(event,value) => { tab_new_value = value }}
            indicatorColor="primary"
            textColor="primary"
            scrollable
            scrollButtons="auto"
            className="category_tabs"
            classes={{ indicator: 'indicator_class' }}
          >
           <Tab label={this.state.search.replace(/`/g, "&")} className="category_tab category_tab_All" />
          </Tabs>
        </AppBar>
      }

        <div className="search_sorting"><b>Sort By:</b> {seacrh_sort_text}</div> 
       
        <section id="myCarousel">
        {
          this.props.product_count > 0?
              <div className="rootDiv">
                <GridList cellHeight={385} className="gridList category_gridlist">
                  {product_item}
                </GridList>
              </div>
          :product_item
        }
       </section>
   </div>

<div className="scroll_top_area">
<ScrollToTop showUnder={100}>
  <div className="scroll_top">^</div>
</ScrollToTop>
</div>

<div className="category_end"></div>

   <div className="bottom_filter">
     <ul>
        <li><span onClick = {this.toggleSortDrawer} className="sort_filter_bottom_btn"><i className="fa fa-sort"></i> SORT</span></li>
        <li><span className="sort_filter apply-selected" onClick={this.toggleFilterDrawer}><i className="fa fa-filter" aria-hidden="true"></i> FILTER</span></li>
     </ul>
   </div>
   <Drawer
          docked={false}
          open={this.state.sortDrawerOpen}
        >
  <div id="sort_filter_popup" className="filter_containers sort_drawer_div">
      <div className="filter_box">
        <div className="sort_filter_head">
          <span>Sort By</span>
          <button type="button" id="sort_filter_popup_close" className="close close_filters" onClick={this.toggleSortDrawer}>&times;</button>
        </div>
        <div className="filter_body">
           {this.props.sorts ?
             this.props.sorts.map((sort, index) => { 
              return (<Sort sort={sort} key={index} count={index} clickHandler={self.callAjaxToSort} />)
           })
           : ''
         }    
        </div>
      </div>
  </div>
  </Drawer>
  <Drawer
          docked={false}
          open={this.state.filterDrawerOpen}
          openSecondary={true}
        >
  <div className="filter_container filter_drawer_div" id="filter_container">
       <div className="apply_filters">
          <div className="clear-all" onClick={this.callAjaxToRemoveAllFilter}>Clear All</div>
          <div className="filter_apply_area filter_container_close">
          <button type="button" className="filter_apply_btn" onClick={this.applyFilter}>Apply</button>
          </div>
        </div>
        <div className="filter_box">
          <div className="sort_filter_head">
            <span>Filter By</span>
            <button type="button"  className="close close_filters filter_container_close" onClick={this.toggleFilterDrawer}>&times;</button>
          </div>
          <div className="filter_body">
            <div className="filters_tab_section">

              <div className="filters_tab_btn">
                <ul className="nav-tabs">
                  <li className="active">
                  <Anchor  dataTarget="#price_filter" title="Price Filter" />
                  </li>
                  <li>
                   <Anchor  dataTarget="#ratings" title="Ratings" />
                  </li>
                  {this.props.filter_facets ?
                    this.props.filter_facets.filters.map((filter, index) => { 
                      return ( <li key={index} id={'filter_group_list'+filter.filter_group_id}> <Anchor  dataTarget={'#filter_group_'+filter.filter_group_id} title={filter.group_label} /> </li>)
                   })
                     : ''
                   } 
                   <li>
                   <Anchor  dataTarget="#product_stock" title="Product Stock" />
                  </li> 
                </ul>
                 <div style={{height:'30px'}}></div> 
               </div>    

                <div className="tab-content">
                  <Price price_with_currency={this.props.price_with_currency} clickHandlerPrice={this.callAjaxToPrice} callAjaxToRemovePrice={this.callAjaxToRemovePrice} />
                  <div id="ratings" className="filter_group tab-pane fade">
                    <ul className="more_filters_right_tab">

                     <li><span onClick={(e)=>this.callAjaxToRating(5)} className="pd_popup_selected">Excellent <label><input id="rating_filter_5" name="rating_filter"  value="5" type="radio" /><div className="filter_checkbox"></div></label></span></li>
                     <li><span onClick={(e)=>this.callAjaxToRating(4)}  className="pd_popup_selected">Good <label><input id="rating_filter_4" name="rating_filter"  value="4" type="radio" /><div className="filter_checkbox"></div></label></span></li>
                     <li><span onClick={(e)=>this.callAjaxToRating(3)}  className="pd_popup_selected">Average <label><input id="rating_filter_3" name="rating_filter"  value="3" type="radio" /><div className="filter_checkbox"></div></label></span></li>
                     <li><span onClick={(e)=>this.callAjaxToRating('all')} className="pd_popup_selected">All <label><input id="rating_filter_all" name="rating_filter"  value="all" type="radio" /><div className="filter_checkbox"></div></label></span></li>

                    </ul>
                  </div>

                  {this.props.filter_facets ?
                    this.props.filter_facets.filters.map((filter, index) => { 
                      return (<FilterGroup filter={filter} key={index} count={index} search_filter={self.state.search_filter} clickHandler={self.callAjaxToFilter} />)
                   })
                     : ''
                   } 

                   <div id="product_stock" className="filter_group tab-pane fade">
                    <ul className="more_filters_right_tab">
                     <li className="radio_filter">
                      <span onClick={()=>this.callAjaxToStock(0)}>
                        <label className="sort_filter_btn sort_control_radio">
                        <input name="radio" type="radio" id="stock_filter" value="0" checked={this.state.stock_filter ? false : true} />
                        <div className="sort_control_indicator"></div>
                        </label> In Stock
                      </span>
                     </li>
                     <li className="radio_filter">
                      <span onClick={()=>this.callAjaxToStock(1)}>
                       <label className="sort_filter_btn sort_control_radio">
                         <input name="radio" type="radio" id="stock_filter_all" checked={this.state.stock_filter ? true : false}  value="1" />
                         <div className="sort_control_indicator"></div>
                        </label> All Stock
                       </span>
                     </li>
                    </ul>
                  </div>
                   <div style={{height:'30px'}}></div> 
                </div>
              
            </div>
            <div className="clearfix"></div>
          </div>
        </div>

       </div>
       </Drawer>
        <div className="btn_browse_more page_auto_load" onClick={this.paginationData}></div>
        <div className="category_loading">loading...</div>
        </div>
      }
      </section>
    )
  }
}

function mapStateToProps(state){
  return {
    category_filter_path: state.categoryReducer.category_filter_path,
    product_list: state.categoryReducer.product_list,
    product_count:state.categoryReducer.product_count,
    path_keyword: state.categoryReducer.path_keyword,
    parent_path_keyword: state.categoryReducer.parent_path_keyword,
    selected_tab: state.categoryReducer.selected_tab,
    filter_facets: state.categoryReducer.filter_facets,
    rating_filter: state.categoryReducer.rating_filter,
    stock_filter:state.categoryReducer.category_data.stock_filter,
    price_with_currency:state.categoryReducer.price_with_currency,
    filters: state.categoryReducer.category_data.filters,
    sorts: state.categoryReducer.category_data.sorts,
    categories: state.categoryReducer.category_data.categories,
    page:state.categoryReducer.category_data.page,
    handpicked_ids:state.categoryReducer.category_data.handpicked_ids,
    random_string:state.categoryReducer.category_data.random_string,
    show_limit:state.categoryReducer.category_data.show_limit,
    single_store_alert:state.categoryReducer.category_data.single_store_alert,
    bandhani_alert:state.categoryReducer.category_data.bandhani_alert,
    alert_thaan_dispatch:state.categoryReducer.category_data.alert_thaan_dispatch,
    product_total:state.categoryReducer.category_data.product_total,
    meta_title:state.categoryReducer.category_data.meta_title,
    meta_description:state.categoryReducer.category_data.meta_description,
    meta_keywords:state.categoryReducer.category_data.meta_keywords,
    menus:state.categoryReducer.category_data.menus,
    actions: bindActionCreators(product_listData, product_pagination_listData, CategoryFilterPathSuccess, CategoryPathKeywordSuccess, getFilterData)
  };
}

export default connect(mapStateToProps)(Category);

