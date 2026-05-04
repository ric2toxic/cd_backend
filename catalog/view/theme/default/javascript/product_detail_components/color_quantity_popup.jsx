class ColorQuantityPopup extends React.Component {
    constructor(props) {
        super(props);
        this.close_popup = this.close_popup.bind(this);
        this.open_popup = this.open_popup.bind(this);
        this.add_to_cart_colors = this.add_to_cart_colors.bind(this);
        
        this.state = {
            is_initial : 0,
            is_option : 0,
            popup_type : 'popup_text'
        };
    }

    componentWillMount(){
        if(this.props.optionsval.length > 1){
            this.setState({is_option:1}) 
        }

    } 

   close_popup()
    {
      $(".modal").css("z-index", "1050");
      $("#more_color_option").modal("hide");
      this.setState({popup_type: "popup_text"});
      if(this.props.is_popup)
      {
        $('#more_color_option').on('hidden.bs.modal', function () {
            $('body').addClass('modal-open');
          });
      }
    }  

   open_popup()
    { 
      this.setState({popup_type: "text"});
      $(".modal").css("z-index", "1051");
      $("#more_color_option").modal("show");
    } 

   add_to_cart_colors()
    { 
      $(".modal").css("z-index", "1050");
      $("#detail-button-cart").trigger("click");
      
      /*$("#more_color_option").modal("hide");
      if(this.props.is_popup)
      {
        $('#more_color_option').on('hidden.bs.modal', function () {
            $('body').addClass('modal-open');
          });
      }
      this.setState({popup_type: "popup_text"});*/      
    }     

    render() {
           function createMarkup(html) {
            return {__html: html};
           }

       function want_design_option(option_value='')
       {
            var customer_mobile = getCookie("customer_mobile");
            var register_user = getCookie("register_user");
            var customer_id = getCookie("customer_id");
            if(getCookie("customer_mobile") == '')
            {
                $("input[name=redirect_cart]").val(2);
                $("#login_link_header").trigger("click");
                return false;     
                
            }
            else if(getCookie("register_user") == 1 && getCookie("customer_id") == '')
            {
                $("input[name=redirect_cart]").val(2);
                $("#login_link_header").trigger("click");
                return false;
            }
            else
            {
               $("#option_name").val(self.props.name);
               $("#option_value").val(option_value); 
               $("#want_designe_popup").modal("show"); 
            }

       }

        var self = this;
        var optionsval = this.props.optionsval;
      
        var moreOptionsVal = this.props.optionsval.map(function(optval, k){
             var color_class = optval.extra_info;
             color_class = color_class.replace(" ", "");
             
            if(k%4==0)
            {
              return (
                <div className="li_divider">
                    <li>
                        <div className="more_color_box_title">Color</div>
                        <div className="more_color_box_title_1">Available</div>
                        <div className="more_color_box_title_1">Quantity</div>
                    </li>
                    {optval.quantity > 0 ?

                      <li>
                        <div className="more_color_box"><span className="color_code color_code_big" style={{backgroundColor:color_class}}></span> <br /> <span className="option_color_name">{optval.name}</span></div>
                        <div className="more_color_box_1">{optval.quantity + ' Set'}<br/>{optval.price?<div style={{color:'#0a8105',fontSize:"11px"}} dangerouslySetInnerHTML={createMarkup(optval.price+'/Piece')}></div>:''}</div>
                        <div className="more_color_box_1"><SetQuantityPopup key={k} index_key={k}  popup_type={self.state.popup_type} is_option = {self.props.optionsval.length} product_option_id = {self.props.product_option_id} product_option_value_id = {optval.product_option_value_id} minimum="0" maximum = {optval.quantity} /></div>
                     </li> 
                      :
                      <li>
                        <div className="more_color_box"><span className="color_code color_code_big" style={{backgroundColor:color_class}}></span> <br /> <span className="option_color_name">{optval.name}</span></div>
                        <div className="more_color_box_1 out_of_stock_text">Out of stock</div>
                        <div className="more_color_box_1"><a href="javascript:;" className="want_design_text" onClick={() => want_design_option(optval.name) }>I want this {self.props.name}</a></div>
                       </li>
                    }
                    </div>
                    );
            }
            else
            { 
              if(optval.quantity > 0)
              {  
                return (
                    <li>
                        <div className="more_color_box"><span className='color_code color_code_big' style={{backgroundColor:color_class}}></span> <br /> <span className="option_color_name">{optval.name}</span></div>
                        <div className="more_color_box_1">{optval.quantity + ' Set'}<br/>{optval.price?<div style={{color:'#0a8105',fontSize:"11px"}} dangerouslySetInnerHTML={createMarkup(optval.price+'/Piece')}></div>:''}</div>
                        <div className="more_color_box_1"><SetQuantityPopup key={k} index_key={k}  popup_type={self.state.popup_type} is_option = {self.props.optionsval.length} product_option_id = {self.props.product_option_id} product_option_value_id = {optval.product_option_value_id} minimum="0" maximum = {optval.quantity} /></div>
                    </li>
                );
               }
               else
               {
                 return (
                    <li>
                        <div className="more_color_box"><span className='color_code color_code_big' style={{backgroundColor:color_class}}></span> <br /> <span className="option_color_name">{optval.name}</span></div>
                        <div className="more_color_box_1 out_of_stock_text">Out of stock</div>
                        <div className="more_color_box_1"><a href="javascript:;" className="want_design_text" onClick={() => want_design_option(optval.name) }>I want this {self.props.name}</a></div>
                    </li>
                );
               } 
              } 


        });

        return (<tr>
                    <td style={{'border-right':'none'}}></td>
                    <td style={{'border-right':'none'}}></td>
                    <td> <a href="javascript:;" onClick={this.open_popup} className="more_btn">+ View More</a>
                    
                    <div className="modal fade more_search_filter" id="more_color_option" data-backdrop="static" role="dialog">
                      <div className="modal-dialog filter_poppup_width">
                       <div className="modal-content">
                          <div className="modal-header">
                            <button type="button" className="close filter_popup_close" onClick={this.close_popup}>&times;</button>
                            <h3>Select Color Option</h3>
                          </div>
                              <div className="modal-body filter_more_box">
                              <div className="clearfix"></div>
                                <div className="filter_search_content">
                                  <ul>
                                   {moreOptionsVal}
                                  </ul>
                                <button className="btn deliver_btn pull-right apply_btn more_margin" type="button" style={{'padding':'6px 12px'}} onClick={this.add_to_cart_colors}>ADD TO CART</button>
                                <button className="btn pull-right apply_btn more_margin" type="button" onClick={this.close_popup}>Cancel</button>
                                </div>
                              </div>
                            </div>
                         </div>
                       </div>

                    </td>
                   </tr>
        )
    }
    
}