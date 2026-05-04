import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux';
import { Link } from 'react-router-dom'
import $ from 'jquery'
import { getCartData, remove, addToWishlist, clearPickupCityCart, estimateShipping, getZones, shippingMethod, updateShippingMethod, update, addComment, applyCoupon, removeCoupon } from '../../actions/CartAction'
import { wishlistData, cartData } from '../../actions/HeaderAction'
import { OptPopupOpen} from '../../actions/LoginAction'
import Api from '../../api/Api'
import  './index.css'
import PickupCity from  './pickup_city'
import Item from  './item'
import Estimate from './estimate'
import PromoCode from './promo_code'
import CartSummary from './cart_summary'
import IWantDesign from '../login/i_want_design'
import ConfirmPopup from './confirm_popup'
import CartOffer from './cart_offer'
import { ConfirmPopupOpen,EstimatePopupOpen } from '../../actions/CartAction'
import webengage from '../../custom/webengage'

class Cart extends Component {
 
 constructor(props)
  {
        super(props);
        this.removeItem          = this.removeItem.bind(this);
        this.addToWishlist       = this.addToWishlist.bind(this);
        this.clearPickupCityCart = this.clearPickupCityCart.bind(this);
        this.estimateShipping    = this.estimateShipping.bind(this);
        this.getZones            = this.getZones.bind(this);
        this.getShippingMethods  = this.getShippingMethods.bind(this);
        this.updateShippingMethod= this.updateShippingMethod.bind(this);
        this.updateItem          = this.updateItem.bind(this);
        this.addComment          = this.addComment.bind(this);
        this.applyCoupon         = this.applyCoupon.bind(this);
        this.removeCoupon        = this.removeCoupon.bind(this);
        this.placeOrder          = this.placeOrder.bind(this);

        this.state = 
        {
          shipping_address:false,
          shipping_methods:false,
          shipping_method: false
        }
   }

 componentDidMount()
  {
    var self            = this;
    var response = this.props.dispatch(getCartData());
     response.then(function(json) {
          self.props.dispatch(cartData());
         })
        .catch(function() {
          console.log('data fetch error');
         });
   
      $(document).delegate('.popup_close_btn', 'click', function(e)
        { 
          self.props.dispatch(ConfirmPopupOpen(0));
        });
             
  }


  removeItem(item){
        var self            = this;
        var response = this.props.dispatch(remove(item.key, item.product_id));
        
        response.then(function(json) {
          self.props.dispatch(wishlistData());
          self.props.dispatch(cartData());
           if(self.state.shipping_address)
           {
             self.getShippingMethods('',self.state.shipping_address.zone_id, self.state.shipping_address.country_id);
           }

         })
        .catch(function() {
          console.log('data fetch error');
         });
    }

  updateItem(item){
        var self            = this;
        var response = this.props.dispatch(update(item.key, item.quantity, item.product_id));
        response.then(function(json) {
          self.props.dispatch(wishlistData());
          self.props.dispatch(cartData());
           if(self.state.shipping_address)
           {
             self.getShippingMethods('',self.state.shipping_address.zone_id, self.state.shipping_address.country_id);
           }

         })
        .catch(function() {
          console.log('data fetch error');
         });
    }

  addComment(key, comment){
     var self   = this;
    var response = this.props.dispatch(addComment(key, comment));

      response.then(function(json) {
           if(self.state.shipping_address)
           {
             self.getShippingMethods('',self.state.shipping_address.zone_id, self.state.shipping_address.country_id);
           }

         })
        .catch(function() {
          console.log('data fetch error');
         });
    }    

  addToWishlist(item){
        this.props.dispatch(addToWishlist(item.product_id));
    }

  clearPickupCityCart(city, add_to_wishlist = 0){
      var self            = this;
      if(this.props.clear_cart)
      {  
        var keys = this.props.clear_cart[city];
        keys = keys.join();
        var response = this.props.dispatch(clearPickupCityCart(keys, add_to_wishlist, this.props.products, city));
       
        response.then(function(json) {
          self.props.dispatch(wishlistData());
          self.props.dispatch(cartData());
           if(self.state.shipping_address)
           {
             self.getShippingMethods('',self.state.shipping_address.zone_id, self.state.shipping_address.country_id);
           }

         })
        .catch(function() {
          console.log('data fetch error');
         });
      }  
    }


  getShippingMethods(pincode,zone_id,country_id){
        
        var self = this;
        var response = this.props.dispatch(shippingMethod(country_id, zone_id, pincode, this.props.cartlimitcross));
        
        response.then(function(json) {
            
            var target = document.getElementById('shipping_method_table');

            if(json.hasOwnProperty('error') ){
                target.innerHTML = '<div class="panel-body estimate_shipping_box">'+json['error']['warning']+'</div>';
                return;
            }
            var shipping_methods = json['shipping_method']['weight']['quote'];
            target.innerHTML = ' <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">'+
                '<thead><tr class="cart_table_title"><th></th><th>SHIPPING MODE</th><th>AMOUNT</th></tr></thead>'+
                '<tbody id ="shipping_table_data" ></tbody></table>';
                
            if(pincode !== '' && json['ess_service']){
                target.innerHTML += '<br/><p style="font-size: 14px;">Your PINCODE comes under ESS (Extra Service Station).So, you have to pay extra amount for Delivery charges: </p>'
            }

            var table_data = document.getElementById('shipping_table_data');
            for (var shipping_method in shipping_methods) {
                var newtr = document.createElement('tr');
                newtr.setAttribute('class', 'shipping_methods');
                var newtd = document.createElement('td');
                newtd.style.width = '30px';
                newtd.innerHTML = '<input type="radio" style="margin:13% 0px 0px 0px;" name="shipping_charge" value="' + shipping_methods[shipping_method]['code'] + '" data-cost="'+shipping_methods[shipping_method]['cost']+'">';
                newtr.appendChild(newtd);
                var newtd2 = document.createElement('td');
                newtd2.innerHTML = shipping_methods[shipping_method]['title']+'<br /><span style="color:#999;">('+shipping_methods[shipping_method]['description']+')</span>';
                newtr.appendChild(newtd2);
                var newtd3 = document.createElement('td');
                newtd3.style.width = '100px';
                newtd3.innerHTML = shipping_methods[shipping_method]['text'];
                newtr.appendChild(newtd3);
                table_data.appendChild(newtr);
            }

            $('.shipping_methods').click(function(){
                var code = $(this).find('input').val();
                $('input[value="'+code+'"]').prop("checked",true);
            });
           

            if(self.state.shipping_method){
              $('input[value="'+self.state.shipping_method+'"]').prop('checked',true);
              self.setState({shipping_address:json['shipping_address']});
              self.setState({shipping_methods:shipping_methods});
              //self.updateShippingMethod();
            }
            else{
                  self.setState({shipping_address:json['shipping_address']});
                  self.setState({shipping_methods:shipping_methods});
                }
          
        })
        .catch(function() {
          console.log('data fetch error');
         });  
    }

  getZones(zone_id = ""){
        var self  = this;
        var country_list = document.getElementById('country_list');
        if(country_list === null || country_list.value === ""){
            return;
        }
        var country_id = country_list.value;
        var response = this.props.dispatch(getZones(country_id));

        response.then(function(json) {
            var select_list = document.getElementById('input-shipping-zone');
            select_list.innerHTML = '<option value="0" selected>---Select Your State---</option>';
            for(var i = 0; i < json['zone'].length; i++){
                var option = document.createElement('option');
                option.value = json['zone'][i]['zone_id'];
                option.text = json['zone'][i]['name'];
                select_list.appendChild(option);
            }
            select_list.disabled = false;
            if(zone_id !== "")
                select_list.value = zone_id;

            self.getShippingMethods('',select_list.value,country_id);
        })
        .catch(function() {
          console.log('data fetch error');
         });
    }    

  estimateShipping(){
        var self = this;
        var pincode = document.getElementById("estimate_pincode").value;
        if(pincode.length < 2 || pincode.length > 10){

            this.props.dispatch(ConfirmPopupOpen(1));
            setTimeout(function(){ 
            $('#confirm_body').html(self.props.language.error_postcode);
            $('#confirm_footer').html('<button type="button" data-dismiss="modal" class="btn popup_close_btn">OK</button>');
             }, 200);

            return;
        }

        var zone_id = 0;
        var country_id = 0;
        var response = this.props.dispatch(estimateShipping(pincode));

        response.then(function(json) {

            self.props.dispatch(EstimatePopupOpen(1)); 
            var pin = '';
            if(json['city'] !== '') 
            {
                zone_id = json['zone_id'];
                country_id = json['country_id'];

                setTimeout(function(){ 
                   pin = document.getElementById('shipping_method_header');
                   pin.innerHTML = '<p>Please select preferred Shipping method</p><p>Shipping Estimate for <span class="blue_text" >PINCODE ' + pincode + ' (' + json['city'] + ', ' + json['state'] + ')</span></p>';
               }, 200);

                self.getShippingMethods(pincode,zone_id,country_id);
            }
            else
            {
                setTimeout(function(){ 
                pin = document.getElementById('shipping_method_header');
                pin.innerHTML = '<p>Please select your Country for Shipping Estimate</p>'+
                                '<div class="clearfix"></div>' +
                                '<select class="form-control" id="country_list"><option value="0" selected>--Select Country--</option></select>'+
                                '<select class="form-control estimate_state" id="input-shipping-zone"></select><br>';
                document.getElementById('shipping_method_table').innerHTML = '';
                
                var select_list = document.getElementById('country_list');
                var option = '';
                for (var i = 0; i < json['countries'].length; i++) 
                {
                    option = document.createElement('option');
                    option.value = json['countries'][i]['country_id'];
                    option.text = json['countries'][i]['name'];
                    select_list.appendChild(option);
                }

                var zone_select_list =  document.getElementById('input-shipping-zone');
                 zone_select_list.innerHTML = '<option value="0" selected>---Select Your State---</option>';
                for (var z = 0; z < json['zones'].length; z++) 
                {
                    option = document.createElement('option');
                    option.value = json['zones'][z]['zone_id'];
                    option.text = json['zones'][z]['name'];
                    zone_select_list.appendChild(option);
                }

                if(json['country_id'] !== 0)
                {
                    select_list.value = json['country_id'];
                }
                else
                {
                    select_list.value = 99;
                   
                }

                $('#country_list').change(function(){
                    self.getZones();
                });

                $('#input-shipping-zone').change(function(){
                    self.getShippingMethods('',$('#input-shipping-zone').val(), $('#country_list').val());
                });

                   
               }, 200);

             }
           
        })
        .catch(function() {
          console.log('data fetch error');
         });

    }


  updateShippingMethod(){
        this.props.dispatch(EstimatePopupOpen(0));  
        var shipping_method = $('input[name="shipping_charge"]:checked').val();
        var shipping_cost   = $('input[name="shipping_charge"]:checked').attr('data-cost');
        if(typeof shipping_method === 'undefined'){
            return;
        }
        this.props.dispatch(updateShippingMethod(shipping_method, shipping_cost));
        this.setState({shipping_method:shipping_method});
    }

  applyCoupon(){
        var self   = this;
        var promo_code = encodeURIComponent($('input[name=\'promo_code\']').val());
        var response  = this.props.dispatch(applyCoupon(promo_code));
        response.then(function(json) {
           if(self.state.shipping_address)
           {
             self.getShippingMethods('',self.state.shipping_address.zone_id, self.state.shipping_address.country_id);
           }
         })
        .catch(function() {
          console.log('data fetch error');
         });
    }

  removeCoupon(){
       var self   = this;
       var response = this.props.dispatch(removeCoupon());
        response.then(function(json) {
           if(self.state.shipping_address)
           {
             self.getShippingMethods('',self.state.shipping_address.zone_id, self.state.shipping_address.country_id);
           }
         })
        .catch(function() {
          console.log('data fetch error');
         });
    } 

  placeOrder(){

    if(!this.props.userlogin.logged)
    {
        this.props.dispatch(OptPopupOpen(1));
        setTimeout(function(){ $("#redirect_cart, #redirect_cart1, #login_redirect_cart").val('cart'); }, 1000);
        return;
    }
    
    var customer_id = this.props.userlogin.logged;
    var shipping = this.state.shipping_address;

    webengage.checkout_start(this.props.total_pieces, this.props.total_sets, this.props.cart_summary.totals.sub_total.value);

    var cart = {"customer":this.props.customer,"cart_data":{"products":this.props.products ,"total_sets":this.props.total_sets,"total_pieces":this.props.total_pieces,
            "clear_cart":this.props.clear_cart,"cartlimitcross":this.props.cartlimitcross,"weight":this.props.weight},"cart_summary":this.props.cart_summary ,"shipping":shipping,"tab":"delivery","have_gst_tab":0};
    localStorage.setItem(customer_id+'_cart_data', JSON.stringify(cart));

     var self = this;
     setTimeout(function(){ window.location = self.props.checkout_page; }, 500);
    
  }



  createMarkup(html) {
        return {__html: html};
    } 

  render(){


$("#cart-total").html(parseInt(this.props.total_sets, 10));

  return (
    <div className="container-fluid cart_page white_bg">
    {this.props.total_sets ?
      <div className="row">
        <div className="cart_full_box">
          <div className="col-xs-12 nopadding mobile_section">
              <div className="panel-group" id="accordion">

                  
                  <section className="panel cart_box">
                      <div className="cart_main_title main_title_active">
                          <h4 className="panel-title shopping_cart_title">
                              <span className="accordion-toggle" data-parent="#accordion">Shopping Cart <small>({this.props.total_sets} Sets, {this.props.total_pieces} Pieces &nbsp;| &nbsp;Weight: {this.props.weight})</small>
                              </span>
                          </h4>
                      </div>

                      <div id="collapseOne" className="collapse in">
                          <div className="panel-body nopadding">
                           
                             { Object.keys(this.props.products).map( (city, product_arr) => {

                                return(
                                  <div  key={city} >
                                  <PickupCity city={city} language={this.props.language}  clearPickupCityCart={this.clearPickupCityCart} />
                                  <div className="clearfix"></div>
                                  <div className="col-sm-12 nopadding collapse in" id={"shipment_"+city}>
                                  {Object.keys(this.props.products[city]).map( (product,) => {
                                   return this.props.products[city][product].map((item,key) =>
                                         <Item key = {item.key} data = {item} language={this.props.language} removeItem={this.removeItem} addToWishlist={this.addToWishlist} updateItem={this.updateItem} addComment={this.addComment} />
                                       )
                                  })}
                                  </div>
                                  </div>
                                  )

                             })}

                            <div className="cart_detail col-xs-12">
                             <Estimate language={this.props.language} estimateShipping={this.estimateShipping} updateShippingMethod={this.updateShippingMethod} />
                            </div>  
                          </div>
                      </div>
                  </section>
              </div>
          </div>

        
          <div className="col-xs-12 nopadding">
            <div className="cart_statement cart_statement_pree_load">
                <PromoCode language = {this.props.language} applyCoupon={this.applyCoupon} removeCoupon={this.removeCoupon} coupon={this.props.coupon} />
                <CartSummary cart_summary = {this.props.cart_summary} language = {this.props.language} checkout_page={0} />
            </div> 
            <CartOffer language={this.props.language} surface_shipping={this.props.surface_shipping} />
          </div>

           <div className="clearfix"></div>



          <input id = "last_out_of_stock_product" type="hidden" value=""></input>
          <input id = "last_quantity_reduced_product" type="hidden" value=""></input>
          <input id = "last_moq_error_product" type="hidden" value=""></input>
         
          { this.props.cart_summary.disable_place_order === 1 ?
            <div className="down_btn" id="place_order_button"><span style={{backgroundColor:'grey'}}>PLACE ORDER  <i className="fa fa-angle-right" aria-hidden="true"></i></span></div> 
            :
            <div className="down_btn" onClick={this.placeOrder} id="place_order_button"><span>PLACE ORDER  </span></div> 
          }
           
        <ConfirmPopup />

         <IWantDesign />
         
      </div>
    </div>
    :
     this.props.is_empty === "1" ?
      <div className="row">
        <div className="cart_full_box">
          <div className="col-xs-12 mobile_section">
            <div className="cart_empty_img">
               <img src={Api.cdn_url+"cart_empty.jpg"} className="img-responsive" alt="cart empty"/>
            </div>

            {this.props.language ?
              <div className="cart_empty_btn" dangerouslySetInnerHTML={this.createMarkup(this.props.language.cart_empty)}></div>
            : 
              <div className="cart_empty_btn"><p>Your WholesaleBox cart is empty<br />but it does not have to be</p></div>
            }
                           
            <div className="cart_empty_btn">
                <Link to={Api.folder_path}>
                  {this.props.language ?
                    this.props.language.text_start_shopping
                    : 'text_start_shopping SHOPPING'
                  }
                </Link>
            </div>
          </div>
        </div>
      </div>  
     :
    <div className="contner head_margin side-collapse-container" style={{minHeight:'500px', paddingTop:'50%', textAlign:'center'}}>
             <i className="fa fa-circle-o-notch fa-spin" style={{fontSize:'40px'}}></i></div>
    }         
  </div> 
    	)                                   
  }
}


function mapStateToProps(state){
  return {
    cart_api_suceess: state.cartReducer.cart_api_suceess,
    customer:state.cartReducer.cart_customer,
    products: state.cartReducer.cart_products,
    total_sets: state.cartReducer.cart_total_sets ? state.cartReducer.cart_total_sets : 0,
    total_pieces: state.cartReducer.cart_total_pieces,
    surface_shipping:state.cartReducer.surface_shipping,
    cart_summary: state.cartReducer.cart_summary,
    checkout_page: state.cartReducer.checkout_page,
    clear_cart: state.cartReducer.clear_cart,
    cartlimitcross:state.cartReducer.cartlimitcross,
    coupon: state.cartReducer.cart_coupon,
    weight: state.cartReducer.cart_weight,
    is_empty: state.cartReducer.cart_is_empty,
    language: state.cartReducer.cart_language,
    userlogin: state.headerReducer.userlogin,
    actions: bindActionCreators(getCartData, remove, addToWishlist, clearPickupCityCart, estimateShipping, getZones, shippingMethod, updateShippingMethod, wishlistData, cartData, update, addComment, applyCoupon, removeCoupon, OptPopupOpen, ConfirmPopupOpen, EstimatePopupOpen)
  };
}
export default connect(mapStateToProps)(Cart);
