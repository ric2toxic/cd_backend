
class Header extends React.Component {

   constructor(props)
   {
    super(props);
    this.state = {
      data: [],
      result_show: false
    };
   } 

  componentDidMount()
  {
    axios({
    method:'get',
    url:'./api/header/userlogin',
    responseType:'json'
   })
   .then(response => {
      this.setState({data: response.data.data});
      this.setState({result_show: 1})
   });

  $(document).delegate('#search_magnifier', 'click', function(e)
     { 
    var url = 'category';
    var value = $('header input[name=\'search\']').val();
    if (value) {
      value = value.replace(/&/g, "`");
      url += '&search=' + encodeURIComponent(value)+'#!filter=&price_filter=&option=&sort=sort_order&order=ASC&rating_filter='+'&search=' + encodeURIComponent(value)+'&stock_filter=0';
    }
   window.location.href =url;
  });

$(document).delegate('header input[name=\'search\']', 'keydown', function(e)
{ 
    if (e.keyCode == 13) {
      $('#search_magnifier').trigger('click');
    }
  });

$(document).delegate('#store_switch', 'click', function(argumen)
{ 
    $.ajax({
      url: 'index.php?route=common/header/getStoreSwitchNew',
      dataType: 'json',

      beforeSend: function () {
        $('body').removeClass('loaded').addClass('loading');
      },

      success: function (json) {
        location.reload();

      }
    });
  });

$(document).ready(function() {
  $("#mainsearch").autocomplete({
    source: './api/header/autoComplete',
    dataType: "json",
    success: function (data) {
      response($.map(data, function (item) {
        return {
          label: item['label'],
          value: item['value']
        }
      }));
    },
    autoFocus: false,
    select: function (event, ui) {
      $('input[name=\'search\']').val(ui.item.label);
    },
    open: function (event, ui) {
      $(".ui-autocomplete").addClass('dropdown-menu');
      $(".ui-autocomplete").css("z-index", 1000);
    },
    create: function () {
      $(this).data('ui-autocomplete')._renderItem = function (ul, item) {
        return $('<li>')
            .append(  $('<a/>').html(item.label).text() )
            .appendTo(ul);
      };
    }
  })
});


  }


  render() {
$('header input[name=\'search\']').val(this.props.search);

    return (
    <section>  
        <section className="sub_navbar container-fluid bg_gray">
          <div className="width_fix">
            <div className="col-sm-3 seller_btn nopadding">
            { this.state.data.logged ? 
               this.state.data.seller_login ?
                <a href={this.state.data.manufacturer_dashboard_link}>{this.props.language.dashboard}</a>
                : ''
            :
             <a href={this.state.data.manufacturer_link}>{this.props.language.manufacturer2}</a>
            }
             
             {/*this.props.international_store ? 
             <a className="text_blink" style={{float:'right'}} href="https://web.whatsapp.com/send?phone=9116134795&text=Hello,%20I%20visited%20Wholesalebox.%20I%20am%20interested%20in%20your%20products." target="_blank">For any help/assistance: WhatsApp us on <i className="fa fa-whatsapp" aria-hidden="true"></i> +91 - 9116134795</a>
              : ''
             */}
            </div>
            <div className="col-sm-9">
              <ul className="top_right_navgation">
                {this.props.international_store ? 
                    <li><CurrencyList /></li>
                : ''
                }
                  <li><a href="./i/storelocator"><label ><i className="fa fa-map-marker" aria-hidden="true"></i></label > Store Locator </a></li>
                   <li><label ><i className="fa fa-phone" aria-hidden="true"></i></label > (+91) 141 4049163</li>
                   {this.props.international_store ? 
                     <li><a href="https://web.whatsapp.com/send?phone=9116134795&text=Hello,%20I%20visited%20Wholesalebox.%20I%20am%20interested%20in%20your%20products." target="_blank"><label><i className="fa fa-whatsapp" aria-hidden="true"></i></label> +91 - 9116134795</a></li>
                   :
                    <li><a href="https://web.whatsapp.com/send?phone=918696491521&text=Hello,%20I%20visited%20Wholesalebox.%20I%20am%20interested%20in%20your%20products." target="_blank"><label><i className="fa fa-whatsapp" aria-hidden="true"></i></label> +91 - 8696491521</a></li>
                    }
              </ul> 
            </div>
          </div>          
        </section>
  <header className="">    
        <section className="full_header navbar" data-spy="affix" data-offset-top="43">
          <div className="width_fix">
                 <div className="col-sm-3 logo_width"><Logo home_page={this.props.home_page} /></div>
                <div className="search_bar col-sm-4">
                 {!this.props.hide_search ?
                   <input type="text" id="mainsearch" name="search" className="form-control serch_input_box ui-autocomplete-input" placeholder="Search from more than 1 Lac Products..." autoComplete="off" />
                   : ''} 
                 {!this.props.hide_search ?  
                    <div className="input-group-btn serch_btn">
                      <button className="btn btn-default btn-search" type="button" id="search_magnifier">
                         <i className="fa fa-search" aria-hidden="true"></i>
                      </button>
                     </div>
                  : ''}   
                   
                </div>
                <div className="col-sm-2 singles_store_btn nopadd">
                {!this.props.hide_search ?  
                <a href="javascript:void(0);" id="store_switch">
                  {this.props.custom_store}
                </a>
                 : ''}
                </div>


                <div className="col-sm-3 signup_right">
                  <Wishlist />
                  { this.state.data.logged ?
                  <div className="sign_in_box dropdown">
                    <a className="nav-a nav-a-2 dropdown-toggle"  href={this.state.data.account} data-toggle="dropdown">
                        <span className="nav-line-1">Hello, {this.state.data.cust_name}</span>
                        <span className="nav-line-2">My Account <span className="nav-icon"></span></span>
                    </a>
                     <ul className="dropdown-menu dropdown-user">
                        {/*<li><a href={this.state.data.account}><i className="fa fa-user fa-fw" aria-hidden="true"></i> Account </a></li> */}
                        { 
                          this.state.data.is_seller ? <li><a href={this.state.data.manufacturer_dashboard_link}><i className="fa fa-user fa-user" aria-hidden="true"></i> Seller Dashboard </a></li> : ''
                        }
                        <li><a href={this.state.data.order}><i className="fa fa-shopping-cart" aria-hidden="true"></i> {this.props.language.text_my_orders} </a></li>
                      </ul>
                  </div>
                  :
                  <div className="sign_in_box dropdown sign_top_box">
                    <a style={{cursor:'pointer'}} className="nav-a nav-a-2"  id="login_link_header" data-toggle="modal" data-target="#login_verify_popup">
                        <span className="nav-line-2">Sign In /Sign Up</span>
                    </a>
                  </div>
                   }
                  <CartTotal />
                </div>
                
              <div className="clearfix"></div>
            </div>
        </section>
        <div className="clearfix"></div>

        {!this.props.home_page ?
         <Menu home_page="0" international_store={this.props.international_store} />
         : ''
        }

       </header>
        </section> 
    );
}
}
