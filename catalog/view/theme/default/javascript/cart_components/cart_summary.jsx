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
    }
    togglePromo(){
        $('#promo_code_panel').toggle();
    }

    createMarkup(html) {
        return {__html: html};
    }


    render(){
    function createMarkup(html) {
       return {__html: html};
     }
     
     var price_in_rupees = '';
     if (this.props.cart_summary.price_in_rupees !== '') {
       price_in_rupees = '<br/>(' + this.props.cart_summary.price_in_rupees + ')';
     }
        return(
            <div >
                {this.props.checkout_page == 0 ? (<div>
                    <div className="promo_code_penal">
                        {this.props.coupon != ""?(
                            <div className="alert alert-success col-sm-12" id="remove_coupon_code" style={{padding:'5px 9px 6px 1px',marginTop:'5px',borderRadius:'0px'}}>
                                <div className="col-sm-10">Promo {this.props.coupon} applied.</div>
                                <div className="col-sm-2"><a className="btn deliver_btn" id="remove_coupon" data-toggle="tooltip" onClick={this.props.removeCoupon} title="Remove Promo Code"><i className="fa fa-trash-o" aria-hidden="true"></i></a>
                                </div>
                                </div>
                        ):(
                            <div>
                                <p>{this.props.language.text_promo}<a id="show_promo_apply" onClick={this.togglePromo} href="javascript:void(0);">Apply</a></p>
                                <div id="promo_code_panel" style={{display:'none'}}>
                                    <input type="text" className="form-control promo_code_input" placeholder={this.props.language.text_place_promo} name="promo_code" />
                                    <button type="button" id="button-coupon" onClick={this.props.applyCoupon} className="btn btn-apply" style={{opacity:'1'}}>Apply</button>
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
                                if ( ("net_payable_amount" in this.props.cart_summary.totals == true && data == "net_payable_amount") || ("net_payable_amount" in this.props.cart_summary.totals == false && data == "total")){
                                    return(
                                        <div key = {key} className="row black_text_cart">
                                            <br/>
                                            <div className="col-sm-7 order_summary_table-left">{this.props.cart_summary.totals[data]['title']}</div>
                                            <div className="col-sm-5 order_summary_table-right" dangerouslySetInnerHTML={createMarkup(this.props.cart_summary.totals[data]['text']+price_in_rupees)}></div>
                                        </div>
                                    );

                                }
                                else{
                                    var class_name = "row";
                                    if(data == "sub_total" || data == "round_off")
                                        class_name = "row bottem_line";
                                    else if (data == "coupon" || data == "paycharge"  || data == "shipping"   || data == "cashback")
                                        class_name = "row text_red";
                                    return(
                                        <div key = {key} className={class_name}>
                                            {data == "shipping"?(
                                                <div>
                                                    <div className="col-sm-7 order_summary_table-left">Shipping Charge</div>
                                                    <div className="col-sm-5 order_summary_table-right" dangerouslySetInnerHTML={createMarkup(this.props.cart_summary.totals[data]['text'])}></div>
                                                    <div className="col-sm-8 order_summary_table-left" style={{fontSize:'11px'}}>{this.props.cart_summary.totals[data]['title']}</div>
                                                </div>
                                            ):(
                                                <div>
                                                    <div className="col-sm-7 order_summary_table-left">{this.props.cart_summary.totals[data]['title']}</div>
                                                    <div className="col-sm-5 order_summary_table-right" dangerouslySetInnerHTML={createMarkup(this.props.cart_summary.totals[data]['text'])}></div>
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
                        {this.props.checkout_page == 0 ? (
                            <div><PlaceOrder cart_summary={this.props.cart_summary} language={this.props.language} placeOrder = {this.props.placeOrder}/> </div>
                        ):(
                            <div className="gst_data">
                                {this.props.customer.gst_number != ""?(
                                    <div className="row upper_line gst_text">
                                        <div className="col-sm-6 order_summary_table-left">{this.props.language.text_gst_already}</div>
                                        <div className="col-sm-6 order_summary_table-right" >{this.props.customer.gst_number}</div>
                                    </div>
                                ):(
                                    <span style={{display:'none'}}></span>
                                )}
                            </div>
                        )}

                    </div>
                </div>
            </div>
        );
    }
}
