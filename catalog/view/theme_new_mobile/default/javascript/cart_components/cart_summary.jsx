{/*
 * class CartSummary: This will render the cart summary
 * @Params: {
 *      totals: {list of different type totals: sub-total,tax,shipping charge,TOTAL},
 *      tax_refund: amount customer will get on submission of form-c,
 *      language: language text for different fields
 * }
 *
 * @author:Devendra Dhayal, Date-Added:20th May 2017, Date-Modified:20th May 2017
 *
 */}
class CartSummary extends React.Component{
    constructor(){
        super();
        this.togglePromo = this.togglePromo.bind(this);
        this.createMarkup = this.createMarkup.bind(this);
    }

    togglePromo(){
        $('#promo_code_panel').toggle();
    }

    createMarkup(html) {
        return {__html: html};
    }


    render(){
      var price_in_rupees = '';
      if (this.props.cart_summary.price_in_rupees !== '') {
        price_in_rupees = '<br/>(' + this.props.cart_summary.price_in_rupees + ')';
      }
        return(
            <div className="cart_statement cart_statement_pree_load" >
                {this.props.checkout_page == 0 ? (<div>
                    <div className="promo_code_penal">
                        {this.props.coupon != ""?(
                            <div className="alert alert-success col-xs-12" id="remove_coupon_code" style={{padding:'5px',marginTop:'5px',borderRadius:'0px'}}>
                                <span style={{padding:'5px'}}>{this.props.language.text_promo} {this.props.coupon} {this.props.language.text_applied}.</span>
                                <a className="btn btn-estimate pull-right" id="remove_coupon" data-toggle="tooltip" onClick={this.props.removeCoupon} title="Remove Promo Code"><i className="fa fa-trash-o" aria-hidden="true"></i></a>

                                </div>
                        ):(
                            <div>
                                <p>{this.props.language.text_promo}<a id="show_promo_apply" onClick={this.togglePromo} href="javascript:void(0);">{this.props.language.text_apply}</a></p>
                                <div id="promo_code_panel" style={{display:'none'}}>
                                    <input type="text" className="form-control promo_code_input" placeholder={this.props.language.text_place_promo} name="promo_code" />
                                    <button type="button" id="button-coupon" onClick={this.props.applyCoupon} className="btn btn-apply" style={{opacity:'1'}}>{this.props.language.text_apply}</button>
                                </div>
                            </div>
                        )}

                    </div>
                </div>):(<div></div>)}

                <div className="order_summary">
                    <h2>{this.props.language.text_order_summary}</h2>
                    <div className="order_summary_table">
                        {Object.keys(this.props.cart_summary.totals).map((data,key) =>
                            {
                                if (("net_payable_amount" in this.props.cart_summary.totals == true && data == "net_payable_amount") || ("net_payable_amount" in this.props.cart_summary.totals == false && data == "total")){
                                    return(
                                        <div key={key} className="col-xs-12 nopadding black">
                                            <br/>
                                            <div className="col-xs-6 order_summary_table-left">{this.props.cart_summary.totals[data]['title']}</div>
                                            <div className="col-xs-6 order_summary_table-right" dangerouslySetInnerHTML={this.createMarkup(this.props.cart_summary.totals[data]['text']+price_in_rupees)}></div>
                                        </div>
                                    );

                                }
                                else{
                                    var class_name = "col-xs-12 nopadding";
                                    if(data == "sub_total" || data == "round_off")
                                        class_name = "col-xs-12 nopadding bottem_line";
                                    else if (data == "coupon" || data == "paycharge"  || data == "shipping"   || data == "cashback")
                                        class_name = "col-xs-12 nopadding text_red";
                                    return(
                                        <div key={key} className={class_name}>
                                            {data == "shipping"?(
                                                <div>
                                                    <div className="col-xs-6 order_summary_table-left">{this.props.language.text_shipping_charge}</div>
                                                    <div className="col-xs-6 order_summary_table-right" dangerouslySetInnerHTML={this.createMarkup(this.props.cart_summary.totals[data]['text'])}></div>
                                                    <div className="col-xs-8 order_summary_table-left" style={{fontSize:'11px'}}>{this.props.cart_summary.totals[data]['title']}</div>
                                                </div>
                                            ):(
                                                <div>
                                                    <div className="col-xs-6 order_summary_table-left">{this.props.cart_summary.totals[data]['title']}</div>
                                                    <div className="col-xs-6 order_summary_table-right" dangerouslySetInnerHTML={this.createMarkup(this.props.cart_summary.totals[data]['text'])}></div>
                                                </div>
                                            )}
                                        </div>
                                    );
                                }

                            })
                        }
                        {parseInt(this.props.cart_summary.disable_place_order) == 1?(
                            <div className="text_red" dangerouslySetInnerHTML={this.createMarkup(this.props.cart_summary.text_cart_minimum)} ></div>
                            ):(
                                <div></div>
                            )
                        }
                        {parseInt(this.props.checkout_page) == 1?(
                            <div className="gst_data">
                                {this.props.customer.gst_number != ""?(
                                    <div className="col-xs-12 nopadding upper_line gst_text">
                                        <div className="col-xs-6 order_summary_table-left">{this.props.language.text_gst_already}</div>
                                        <div className="col-xs-6 order_summary_table-right">{this.props.customer.gst_number}</div>
                                    </div>
                                ):(
                                    <span style={{display:'none'}}></span>
                                )}
                            </div>
                        ):(<div style={{display:'none'}}></div>)}
                        <div className="clearfix"></div>
                    </div>
                    <div className="clearfix"></div>
                </div>
            </div>
        );
    }
}
