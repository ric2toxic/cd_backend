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
        this.createMarkup = this.createMarkup.bind(this);
    }

    updateShippingMethod(shipping_method){
        $('input[value="'+shipping_method+'"]').prop("checked",true);
        this.props.updateShippingMethod();
    }

    createMarkup(html) {
        return {__html: html};
    }

    componentDidUpdate(){
            //$('.no_shipping').html(this.props.language.error_no_shipping);
    }

    componentDidMount(){
        if(this.props.shipping.hasOwnProperty('shipping_method')){
            $('input[value="'+this.props.shipping.shipping_method+'"]').attr('checked',true);
            this.props.updateShippingMethod(0);
        }
    }
    render(){

        return (
            <div >
                <div id="shipping_tab_header"></div>
                <div className="clearfix"></div>
                {jQuery.isEmptyObject(this.props.shipping)?(
                        <div className="no_shipping" dangerouslySetInnerHTML={this.createMarkup(this.props.language.error_no_shipping)}>
                        </div>
                    ):(
                        <div>

                            {jQuery.isEmptyObject(this.props.shipping.shipping_methods)?(
                                <div className="no_shipping" dangerouslySetInnerHTML={this.createMarkup(this.props.language.error_no_shipping)}>
                                </div>
                            ):(
                                <div>
                                    <p>{this.props.language.text_preferred_shipping}</p>
                                    <p>{this.props.language.text_shipping_estimate} <span className="blue_text"> {this.props.shipping.shipping_address.postcode} ({this.props.shipping.shipping_address.city}, {this.props.shipping.shipping_address.zone})</span></p>
                                    
                                    {this.props.shipping.shipping_address.country == 'India' ?
                                     this.props.surface_shipping ?
                                            <div className="free_Surface_banner"> 
                                             <div className="col-xs-9">
                                             Add products worth atleast <br /> <b dangerouslySetInnerHTML={this.createMarkup(this.props.surface_shipping)}></b> more to get
                                              </div>
                                              <div className="col-xs-3 nopadding">
                                              <img src={cdn_url+"free_shipping_label.png"} />
                                              </div>
                                              <span>*T&C (Delivery within India)</span>
                                            </div>
                                           :
                                            <div className="free_Surface_banner ">
                                             YAY! You get FREE Surface Shipping.
                                            </div>
                                    : ''}

                                    <div className="clearfix"></div>
                                    <div className="col-xs-12 cart_table">
                                        <table width="100%" className="table table-striped table-bordered table-hover" id="dataTables-example">
                                            <thead>
                                            <tr className="cart_table_title">
                                                <th></th>
                                                <th>{this.props.language.text_shipping_mode}</th>
                                                <th>{this.props.language.text_amount}</th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            {Object.keys(this.props.shipping.shipping_methods).map((shipping_method,key) =>
                                            {
                                                return(
                                                    <tr className="gradeA shipping_methods" key={key} onClick={this.updateShippingMethod.bind(this,this.props.shipping.shipping_methods[shipping_method]['code'])}>
                                                        <td><input type="radio" name="shipping_charge"  value={this.props.shipping.shipping_methods[shipping_method]['code']} /></td>
                                                        <td>
                                                            {this.props.shipping.shipping_methods[shipping_method]['title']}
                                                            <br />
                                                            { this.props.shipping.shipping_methods[shipping_method]['description']  ?
                                                              <span style={{color:'#999'}}>({this.props.shipping.shipping_methods[shipping_method]['description']})</span>
                                                              : '' }
                                                        </td>
                                                        <td><span dangerouslySetInnerHTML={this.createMarkup(this.props.shipping.shipping_methods[shipping_method]['text'])}></span></td>
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
            </div>
        );
    }
}
