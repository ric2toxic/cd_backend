{/*
 * class CartShipping: This will render the shipping methods on onepagecheckout page
 * @Params: {
 *    shipping: {
 *           shipping_methods: available shipping methods,
 *           shipping_address:selected shipping address for which shipping methods has been calculated
 *           shipping_method:shipping method, if shipping method is already selected
 *       },
 *    language: language text for different fields
 *  }
 *
 * @author:Devendra Dhayal, Date-Added:20th May 2017, Date-Modified:20th May 2017
 *
*/}

class CartShipping extends React.Component{

    constructor(){
        super();
        this.updateShippingMethod = this.updateShippingMethod.bind(this);
        this.changeTab = this.changeTab.bind(this);
    }

    updateShippingMethod(shipping_method){
        $('input[value="'+shipping_method+'"]').prop("checked",true);
        this.props.updateShippingMethod();
    }

    changeTab(){
        if(parseInt(this.props.have_gst_tab) == 1){
            this.props.changeTab('gst');
        }
        else{
            this.props.changeTab('payment');
        }
    }

    componentDidUpdate(){
            $('.no_shipping').html(this.props.language.error_no_shipping);
    }

    componentDidMount(){
        if(this.props.shipping.hasOwnProperty('shipping_method')){
            //$('input[value="'+this.props.shipping.shipping_method+'"]').attr('checked',true);
            //$('#select_payment_button').css('display','block');
        }
    }
    render(){
function createMarkup(html) {
       return {__html: html};
     }
        return (
            <div >
                <div id="shipping_tab_header"></div>
                <div className="clearfix"></div>
                {jQuery.isEmptyObject(this.props.shipping)?(
                        <div className="no_shipping">
                            {this.props.language.error_no_shipping}
                        </div>
                    ):(
                        <div>

                            {jQuery.isEmptyObject(this.props.shipping.shipping_methods)?(
                                <div className="no_shipping"> </div>
                            ):(
                                <div className="col-sm-10 cart_table" id="shipping_method_tab">
                                    <div className="col-sm-12 cart_table nopadding">
                                        <span className="cart_data_heading">
                                        <p>Please select preferred Shipping method</p>
                                        <p>Shipping Estimate for <span className="blue_text">PINCODE {this.props.shipping.shipping_address.postcode} ({this.props.shipping.shipping_address.city}, {this.props.shipping.shipping_address.zone})</span></p>
                                        </span>

                                       
                                          {this.props.shipping.shipping_address.country == 'India' ?
                                            this.props.surface_shipping ?
                                            <div className="free_Surface_banner"> 
                                             Add products worth atleast <b dangerouslySetInnerHTML={createMarkup(this.props.surface_shipping)}></b> more to get 
                                             <img src={cdn_url+"free_shipping_label.png"} />
                                             <span>*T&C (Delivery within India)</span>
                                            </div>
                                           :
                                            <div className="free_Surface_banner ">
                                             YAY! You get FREE Surface Shipping.
                                            </div>
                                           : ''}
                                        

                                        <div className="clearfix"></div>
                                        <table width="100%" className="table table-striped table-bordered table-hover" id="dataTables-example">
                                            <thead>
                                            <tr className="cart_table_title">
                                                <th></th>
                                                <th>SHIPPING MODE</th>
                                                <th>AMOUNT</th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            {Object.keys(this.props.shipping.shipping_methods).map((shipping_method,key) =>
                                            {
                                                return(
                                                    <tr className="gradeA shipping_methods" key={key} onClick={this.updateShippingMethod.bind(this,this.props.shipping.shipping_methods[shipping_method]['code'])} style={{cursor:'pointer'}}>
                                                        <td><input type="radio" name="shipping_charge"  value={this.props.shipping.shipping_methods[shipping_method]['code']} /></td>
                                                        <td>{this.props.shipping.shipping_methods[shipping_method]['title']}
                                                              <br />
                                                              { this.props.shipping.shipping_methods[shipping_method]['description']  ?
                                                              <span style={{color:'#999'}}>({this.props.shipping.shipping_methods[shipping_method]['description']})</span>
                                                              : '' }
                                                        </td>
                                                        <td dangerouslySetInnerHTML={createMarkup(this.props.shipping.shipping_methods[shipping_method]['text'])}></td>
                                                    </tr>
                                                )
                                            })
                                            }
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            )}

                            <div className="clearfix"></div>
                        </div>
                    )
                }
                <div className="col-sm-12 cart_table" style={{display:'none'}} id="select_payment_button">
                    <button className="btn deliver_btn pull-right" data-parent="#accordion"  onClick={this.changeTab}  data-target="#collapsePayment" >CONTINUE &nbsp;<i className="fa fa-caret-right" aria-hidden="true"></i> </button>
                </div>
            </div>
        );
    }
}
