class Home extends React.Component {

   constructor(props)
   {
     super(props);
    this.state = {
      data: [],
      home_images: [],
      result_show: false,
      custom_store_val: this.props.custom_store_val,
      text_tabs_bottom_1: this.props.language.text_tabs_bottom_1,
      text_tabs_bottom_2:this.props.language.text_tabs_bottom_2,
      text_tabs_bottom_3:this.props.language.text_tabs_bottom_3,
      text_tabs_bottom_co_1: this.props.language.text_tabs_bottom_co_1,
      text_tabs_bottom_co_2:this.props.language.text_tabs_bottom_co_2,
      text_tabs_bottom_co_3:this.props.language.text_tabs_bottom_co_3,
      preferences: getCookie('preferences'),
      news:this.props.news,
    };
   }

   componentDidMount()
  {
    axios({
    method:'get',
    url:'./api/home/latest_state',
    responseType:'json'
   })
   .then(response => {
            this.setState({data: response.data.data});
            this.setState({home_images: response.data.data.home_images});
            this.setState({result_show: true});
            var str = this.props.language.text_tabs_bottom_1;
            var str2 = this.props.language.text_tabs_bottom_2;
            var str3 = this.props.language.text_tabs_bottom_co_1;
            var str4 = this.props.language.text_tabs_bottom_co_2;


            str = str.replace("%s", this.state.data.total_products);
            str2 = str2.replace("%s", this.state.data.total_products);

            str3 = str3.replace("%s", this.state.data.total_products);
            str4 = str4.replace("%s", this.state.data.total_products);

            this.setState({text_tabs_bottom_1: str});
            this.setState({text_tabs_bottom_2: str2});
            this.setState({text_tabs_bottom_co_1: str3});
            this.setState({text_tabs_bottom_co_2: str4});

    });

    if(this.state.preferences == '') { $("#preferences_popup").modal("show"); }

  }


	render() {

function set_preferences_cookie(name, value, days)
{
  var expires;
  if (days) {
    var date = new Date();
    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
    expires = "; expires=" + date.toGMTString();
  }
  else {
    expires = "";
  }
  document.cookie = name + "=" + value + expires + "; path=/";
  location.reload();
}

  $('#preOderPopup').submit(function(e){
    e.preventDefault();
    var popup_comment = $('#popup_comment').val();
    var error = 'Fill out all the given field';
    var preOderPopup = $("#preOderPopup").serialize();

    if(popup_comment == '')
    {
     $('.alert_msg').hide();
     $('.want_header').after('<div class="alert_msg" style="padding: 17px 15px 0px"><div class="alert alert-danger">'+error+'</div></div>');
      return false;
    }
   else
   {
    $(".preeorder_submit_bnt").html("please wait...");
   var ajax = $.ajax({
          type : "POST",
          url  : 'api/product/user_comment',
          data : preOderPopup,
          beforeSend: function() {
              if(ajax != null) { ajax.abort(); }
              $('.popup-footer .btn-default').button('loading');
            },
          complete: function() {
              $('.popup-footer .btn-default').button('reset');
              var ajax = null;
            },
          success: function(data){
              $(".preeorder_submit_bnt").html("Send");
              $('.alert_msg').hide();
              $('#popup_comment').val('');
              $('.want_header').after('<div class="alert_msg" style="padding: 17px 15px 0"><div class="alert alert-success"><i class="fa fa-check-circle"></i>'+data['message']+'</div></div>');
            }
            });
    }
        });

var loading_style = {
  BackgroundColor: '#ccc',
  width: '200px',
  height: '30px'
};
		return (
    <section>
	 <div className="container-fluid width_fix">
       <div className="col-sm-12 nopadding">
          <div className="slider_navgation">
            <Menu home_page="1" international_store={this.props.international_store} />
            <News news={this.props.news} />
            {!this.props.international_store ?
            <div className="cloth_link">
              <a href="./wholesale-clothes-market-in-mumbai">Wholesale clothes market in Mumbai</a> <br />
              <a href="./kolkata-wholesale-cloth-market">Kolkata wholesale cloth market</a><br />
              <a href="./wholesale-cloth-market-in-delhi">Wholesale cloth market in Delhi</a><br />
              <a href="./wholesale-cloth-market-in-surat">Wholesale cloth market in Surat</a><br />
              <a href="./wholesale-cloth-market-in-bangalore">Wholesale cloth market in Bangalore</a><br />
              <a href="./jaipur-wholesale-cloth-market">Jaipur wholesale cloth market</a><br />
              </div>
              : ''}

          </div>
          <div className="slider_box">
          <div className="col-sm-12 nopadding">
           {this.state.result_show ?
            <ul className="nav" >
                  <li><a style={{padding:'0px'}} href="./category#!filter=&price_filter=&option=&sort=sort_order&order=ASC&rating_filter=&search=&stock_filter=0&clearance_sale=1"><img src={cdn_url+"sale_is_on.png"} alt="sale" /> </a></li>
                  <li><a href="javascript:;" data-toggle="modal" data-target="#wsb_business">Buy & Save 25%</a></li>
                  <li><a target="_blank" href="https://play.google.com/store/apps/details?id=in.wholesalebox"><label><img src={this.state.home_images.app_icon} alt="mobile_app" /> </label>Android</a></li>
                  <li><a target="_blank" href="https://itunes.apple.com/us/app/wholesalebox/id1254820324?mt=8"><label><img src={this.state.home_images.ios_home} alt="mobile_app" /> </label>IOS</a></li>
                 <li><a href="./index.php?route=common/home/postYourRequirement">Post Your Requirement</a></li>
              </ul>
              :
              <div style={{width:'74%', height:'40px', marginBottom:'10px', 'backgroundColor':'rgba(204, 204, 204, 0.24)'}}></div>
              }
          </div>
            <div className="clearfix"></div>
            <div className="col-sm-9 padding_left_none">
            <Banner />
            </div>
            <div className="col-sm-3 b2bVideo">
              <div className="b2bVideoinner">
              </div>
            </div>
            <div className="clearfix"></div>

            {!this.props.international_store ?
            <div>
            {!this.state.data.is_dropshipper ?
            <div className="col-sm-9" style={{paddingLeft:'0px', 'marginTop':'5px'}}>
            {!this.state.data.logged ?
              <a href="javascript:;" id="credit_banner" data-toggle="modal" data-target="#login_verify_popup">
                <img className="img-responsive"  src={cdn_url+"credit_banner_hindi_new.png"} />
              </a>
             :
             <a href={this.state.data.credit_application} target="_blank">
              <img className="img-responsive"  src={cdn_url+"credit_banner_hindi_new.png"} />
            </a>
            }
            </div>
            : <div className="col-sm-9"></div>}

            <div className="col-sm-3 nopadding">
            <div className="offer_box">
             <img className="img-responsive" src={cdn_url+"offer_tag_2.png"} style={{width:'200px', marginBottom:'10px'}} />
                <p style={{paddingLeft:'15px',fontSize:'13px'}}><strong>Buy stock at maximum possible discount</strong></p>
             <ul>
               <li><div className="icon"><i className="fa fa-circle" aria-hidden="true"></i></div> <div className="text"> Get 2% discount on every prepaid order</div></li>
               <li><div className="icon"><i className="fa fa-circle" aria-hidden="true"></i> </div> <div className="text"> Get 3% discount on prepaid order of Rs. 25,000 & above</div></li>
               <li><div className="icon"><i className="fa fa-circle" aria-hidden="true"></i> </div> <div className="text"> 2% Cashback on order by App. Available for 15 days</div></li>
             </ul>
              </div>
            </div>
            </div>
            : '' }
            <div className="clearfix"></div>


              <Slider key="latest" custom_store_val={this.state.custom_store_val}  category_id="latest" active_item="4" logged={this.state.data.logged} />
               <div className="clearfix"></div>
          </div>
        </div>
        </div>

<div className="container-fluid width_fix">
        
              <Slider key="trending" custom_store_val={this.state.custom_store_val}  category_id="trending" active_item="5" logged={this.state.data.logged} />
         <div className="clearfix"></div>

         <div className="col-sm-12">
          <RecentViewSlider key="latest"  category_id="latest" active_item="5" page_type="home"/>
               <div className="clearfix"></div>
</div>
<div className="clearfix"></div>
         <section className="footer_home">
           <h2 className="page_title home_title"><span>How It Works</span></h2>


            <div className="steps container">
               <div className="col-md-3 col-sm-6 col-xs-6 work_box_section">
                   
                     <div className="procedure"> <img src={cdn_url+"icons/Manufacturer.svg"} width="66px" height="auto" /></div>
                     <div className="mannuals">DIRECT FROM MANUFACTURER</div>
                  
               </div>
               <div className="col-md-3 col-sm-6 col-xs-6 work_box_section">
                     <div className="procedure"> <img src={cdn_url+"icons/Returns.svg"} width="66px" height="auto" /></div>
                       <div className="mannuals">EASY RETURNS</div>
               </div>
               <div className="col-md-3 col-sm-6 col-xs-6 work_box_section">
                  
                     <div className="procedure"><img src={cdn_url+"icons/Delivery.svg"} width="66px" height="auto" /></div>
                     <div className="mannuals">DOOR DELIVERY</div>
                  
               </div>
               <div className="col-md-3 col-sm-6 col-xs-6 work_box_section">
                   
                     <div className="procedure"> <img src={cdn_url+"icons/Payment.svg"} width="66px" height="auto" /></div>
                     <div className="mannuals">SECURE PAYMENT</div>
                   
               </div>            
            </div>

            
            <Review />


            <div className="col-sm-12 nopadding faq-section">
            {this.props.international_store ?
              <h1 className="page_title home_title2"><span>Exclusive wholesale womens clothing and Boutique Designer wholesale clothing</span></h1>
              : ''}
                <div className="col-md-9" >
                    <ul className="tab_list">
                     <li className="active">
                        <a href="#Lowest_Factory" className="list-group_btn" data-toggle="tab">
                          <h4>{this.props.language.text_tab_info_1}</h4>
                          <div className="down"></div>
                        </a>
                      </li>
                      <li>
                          <a href="#WholeSaleBox_Work" className="list-group_btn" data-toggle="tab">
                            <h4>{this.props.language.text_tab_info_2}</h4>
                            <div className="down"></div>
                          </a>
                       </li>
                       <li>   
                      <a href="#About_Wholesalebox" className="list-group_btn" data-toggle="tab">
                        <h4>{this.props.language.text_tab_info_3}</h4>
                        <div className="down"></div>
                      </a>
                      </li>
                    </ul>

                     {this.props.international_store ?
                     
                      <div className="tab-content">
                        <div className="tab-pane fade in active" id="Lowest_Factory">
                          <div className="col-sm-8 col-xs-12">
                            <p> {this.state.text_tabs_bottom_co_1} </p>
                          </div>
                          <div className="col-sm-4 col-xs-12"> <img src={this.state.home_images.camparision} alt="comparision" className="img-responsive" /> </div>
                      </div>
           
         
                      <div className="tab-pane fade" id="WholeSaleBox_Work">
                        <div className="col-sm-8 col-xs-12 ">
                          <p> {this.state.text_tabs_bottom_co_2} </p>
                        </div>
                        <div className="col-sm-4 col-xs-12"> <img src={this.state.home_images.gnrha} alt="comparision" className="img-responsive" /> </div>
                      </div>
  
                        <div className="tab-pane fade" id="About_Wholesalebox">
                          <div className="col-sm-8 col-xs-12">
                            <p> {this.state.text_tabs_bottom_co_3}  </p>
                          </div>
                          <div className="col-sm-4 col-xs-12"> <img src={this.state.home_images.infographics_3} alt="comparision" className="img-responsive" /> </div>
                        </div>
                         <div className="clearfix"></div>
                      </div>

                      :

                    <div className="tab-content">
                        <div className="tab-pane fade in active" id="Lowest_Factory">
                          <div className="col-sm-8 col-xs-12">
                            <p> {this.state.text_tabs_bottom_1} </p>
                          </div>
                          <div className="col-sm-4 col-xs-12"> <img src={this.state.home_images.camparision} alt="comparision" className="img-responsive" /> </div>
                      </div>
           
         
                      <div className="tab-pane fade" id="WholeSaleBox_Work">
                        <div className="col-sm-8 col-xs-12 ">
                          <p> {this.state.text_tabs_bottom_2} </p>
                        </div>
                        <div className="col-sm-4 col-xs-12"> <img src={this.state.home_images.gnrha} alt="comparision" className="img-responsive" /> </div>
                      </div>
  
                        <div className="tab-pane fade" id="About_Wholesalebox">
                          <div className="col-sm-8 col-xs-12">
                            <p> {this.state.text_tabs_bottom_3}  </p>
                          </div>
                          <div className="col-sm-4 col-xs-12"> <img src={this.state.home_images.infographics_3} alt="comparision" className="img-responsive" /> </div>
                        </div>
                         <div className="clearfix"></div>
                      </div>
                      }
                    </div>
                <div className=" col-sm-3">
                  <aside className="stats-bar">
                    <h4>{this.props.language.text_latest_stats}</h4>
                    <ul>
                      <li>
                        <a href="javascript:void(0)">
                          <span><img src={this.state.home_images.stats_img1} alt="stats" className="img-responsive" /></span>
                          <span> {this.props.language.text_designs}<br /><strong>{this.state.data.total_products}+</strong></span>
                         </a></li>
                      <li>
                          <a href="javascript:void(0)">
                            <span><img src={this.state.home_images.stats_img2} alt="stats" className="img-responsive" /></span>
                            <span> {this.props.language.text_users}<br /><strong>{this.state.data.total_customers}+</strong></span>
                            </a></li>
                    </ul>
                  </aside>
               </div>  
            </div>
         </section>
         </div>

             <div className="modal fade want_designe_popup question_popup" id="want_designe_popup" role="dailog">
                <div className="modal-dialog">
                <div className="modal-content">
                  <form id="preOderPopup">
                   <input type="hidden" name="product_id" id="want_designe_product_id" />
                   <input type="hidden" name="product_status" value="out of stock" />
                      <div id="want_header_popup" className="modal-header want_header">
                        <button type="button" className="close" data-dismiss="modal">&times;</button>
                        <h4 className="modal-title">I want this design.</h4>
                      </div>

                       <div className="modal-body popup-q-body"> 
                          <div className="popup-title preorder_title_box">
                                <p>The product is out of stock but you could still ask factory if they can provide it. </p>
                                <p>Please specify how many pieces and what sizes/colors you like to have:</p>
                          </div>
                          <textarea name="popup_comment" id="popup_comment" rows="6" className="popup_comment"></textarea>
                          <div className="clearfix"></div>
                          <div className="popup-footer">
                             <button type="Submit" className="btn deliver_btn preeorder_submit_bnt pull-right">Send </button>
                             <div className="clearfix"></div>
                          </div> 
                      </div>
                  </form>    
               </div>
               </div>
               </div>

      {this.state.preferences == '' ?
            <div className="modal fade" id="preferences_popup" role="dailog" data-backdrop="static" data-keyboard="false">
                <div className="modal-dialog">
                <div className="modal-content">
                  <div className="purchasing_saction">
                 <div className="purchasing_body">
                   
                   <div className="purchasing_header"> Tell us your preferences and never miss new stocks matching your taste.</div>
                    {this.props.preferences_menus.length ?
                      this.props.preferences_menus.map(function(menu, index) {

                         return(<div className={"col-sm-4 purchasing_category section_"+index} style={{'backgroundImage':'url('+menu.image+')'}} onClick={()=> set_preferences_cookie('preferences', menu.value, 7)}>
                             <div className="right_arrow_box"><img src="image/right_arrow.png" /></div>
                              <div className="purchasing_category_text" >{menu.link_title}</div>
                           </div>)

                      })
                    : ''  
                    }
                      <div className="clearfix"></div>
                  </div>
                 </div>
                 </div>
                 </div>
                 </div> 
           : '' } 

             <div className="modal fade" id="wsb_business" role="dailog">
                <div className="modal-dialog wsb_business">
                <div className="modal-content">
                  <div className="modal-header">
                          <button type="button" className="close" data-dismiss="modal">&times;</button>
                          <h4 className="modal-title">Buy from Wholesalebox & Save 25%</h4>
                   </div>
                  <div className="modal-body">
                     <img src={this.state.home_images.WSB_business_v1} />
                  </div> 

               </div>
               </div>
               </div>

        </section>)

	}
}
