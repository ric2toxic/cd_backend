{/*
 * class CartPayment: This will render the payment methods
 * @Params: {
 *      payment_methods: available payment methods,
 *      bank_transfer_info: bank details for bank_transfer payment method,
 *      language: language text for different fields
 * }
 *
 * @author:Devendra Dhayal, Date-Added:20th May 2017, Date-Modified:20th May 2017
 *
 */}
class CartPayment extends React.Component{
    constructor(props){
        super(props);
        this.createMarkup = this.createMarkup.bind(this);
    }
    componentDidUpdate(){
        // $(".payment_view_box a").click(function (e) {
        //     $(this).tab('show');
        //     e.preventDefault();
        // });

        if(this.props.cart_summary.totals.hasOwnProperty('paycharge')){
            if(this.props.cart_summary.totals.sub_total.value > 25000)
                $('.payment_discount').html('<blink>Get 3%('+this.props.cart_summary.totals.paycharge.text+') Discount ! for Online Payment</blink>');
            else
                $('.payment_discount').html('<blink>Get 2%('+this.props.cart_summary.totals.paycharge.text+') Discount ! for Online Payment</blink>');
        }
        $('.payment_title_box').click(function(){
            $(this).find('input:radio').prop( "checked", true );
        })
    }
    componentDidMount() {
        // $(".payment_view_box a").click(function (e) {
        //     $(this).tab('show');
        //     e.preventDefault();
        // });


        // $('.collapse').on('show.bs.collapse', function () {
        //     $(this).parent('div').find('input:radio').prop( "checked", true );
        // })
        $('.payment_title_box').click(function(){
            $(this).find('input:radio').prop( "checked", true );
        })

        this.props.updatePaymentMethod($('.payment_view_box .collapse.in .button-register').attr('data-payment-method'));
    }

    showUPIHelpPopup(){
        $('#upi_help_popup').modal('show');
    }

    showUPIHelpPopup(){
        $('#upi_help_popup').modal({
            backdrop: 'static',
            keyboard: false
        });
    }

    createMarkup(html){
        return {__html:html};
    }

    editVPA(){
        $('#upi_vpa').prop("disabled", false);
    }

    validateVPA(){
        var upi_vpa = $('input#upi_vpa').val();
        if(!(/[aA-zZ0-9\.\-]+@[aA-zZ0-9\.\-]+$/.test(upi_vpa))) {
            $('#upi_vpa_error').text("Invalid VPA !!");
        } else {
            $('#upi_vpa_error').text("");
        }
    }

    render(){

        var sub_total_amount = this.props.cart_summary.totals.sub_total;
        var cod_available_limit  = this.props.cod_available_limit;
        var cod_advance_amount_limit = this.props.cod_advance_amount_limit;

        var total_payable_amount = this.props.cart_summary.totals.total;
        if("net_payable_amount" in this.props.cart_summary.totals == true) {
            total_payable_amount = this.props.cart_summary.totals.net_payable_amount;
        }
        var cod_advance_amount = Math.round(total_payable_amount.value/10);
        if(cod_advance_amount < cod_advance_amount_limit)
            cod_advance_amount = cod_advance_amount_limit;
        var remaining_amount = (total_payable_amount.value - cod_advance_amount).toFixed(2);
        var customer_vpa = this.props.customer.customer_vpa;

        if(customer_vpa != ''){
           $('input#upi_vpa').prop('disabled', true);
        } else {
           $('input#upi_vpa').prop('disabled', false);
        }

        return(
            <div>
                {this.props.payment_data.hasOwnProperty('payment_methods')?(
                    <div >
                        <div className="col-sm-12 nopadding" style={{background:'#e8ecfe',marginBottom:'3px'}}>
                            <img className="img-responsive" src={this.props.payment_data.netbanking_help_mobile_url} width="100%"/>
                        </div>
                        {parseInt(this.props.payment_data.show_offers) != 1? (
                            <div style={{display:'none'}}></div>
                        ):(
                          <div className="col-xs-12 cart_offer" >
                            <div className=" online_offer">
                              <p>{this.props.language.text_discount_2}</p>
                              </div>
                               <div className="col-xs-3 nopadding">
                                 <div className="discount_image">
                                 </div>
                               </div>
                               <div className="col-xs-9 right-nopadding">
                                 <div className="discount_description_checkout align_left">
                                   
                                   <div className="offer_heading">    
                                     <h5>{this.props.language.text_wsb_offer}</h5>                       
                                     <span>{this.props.language.text_wsb_offer_line1}</span><br/>
                                     <p>{this.props.language.text_wsb_offer_line2}</p>
                                   </div>
                                 </div>
                                 <div className="align_left">
                                   <span className="yellow_color">{this.props.language.text_wsb_offer_line3}</span>
                                 </div>
                               </div>
                               
                            
                          </div>
                        )}
                        <div className="clearfix"></div>
                        <div id="accordion_payment" className="panel-group payment_view_box">
                            {Object.keys(this.props.payment_data.payment_methods).map(
                                (method,key) => {
                                    var active_class = "panel-collapse collapse";
                                    if(key == 0) active_class="panel-collapse collapse in";
                                    var remain_credit_amount='0';
                                    var remain_rbl_credit_amount=0;

                                    if(method == 'credit' && total_payable_amount.value > this.props.neo_credit_user_credit_limit){
                                      remain_credit_amount = (total_payable_amount.value-this.props.neo_credit_user_credit_limit).toFixed(2);
                                    }

                                     if(method == 'rbl' && total_payable_amount.value > this.props.rbl_credit_user_credit_limit){
                                        remain_rbl_credit_amount = (total_payable_amount.value-this.props.rbl_credit_user_credit_limit).toFixed(2);
                                        }

                                    return(
                                        <div className="panel panel-default" key={key} >
                                            <div className="panel-heading">
                                                <h4 className="panel-title">
                                                    <a className="payment_title_box" onClick={this.props.updatePaymentMethod.bind(this,method)} data-toggle="collapse" data-parent="#accordion_payment" href={"#"+method}>
                                                        {key ==0?(
                                                            <label className="payment_btn control--radio">
                                                                <input className="radio" name="payment_method" type="radio" defaultChecked/>
                                                                <div className="control__indicator"></div>
                                                            </label>
                                                        ):(
                                                            <label className="payment_btn control--radio">
                                                                <input className="radio" name="payment_method" type="radio"/>
                                                                <div className="control__indicator"></div>
                                                            </label>
                                                        )}
                                                        {this.props.payment_data.payment_methods[method].title}
                                                    </a>
                                                </h4>
                                            </div>
                                            <div id={method} className={active_class}>
                                                <div className="panel-body" id={method}>
                                                    {method == 'citrus' || method == 'razorpay'?(
                                                        <div>
                                                            <p>{this.props.language.payment_debit_card}</p>
                                                            <p>{this.props.language.text_amount_pay}  <span  className="Payable_Amount" dangerouslySetInnerHTML={this.createMarkup(total_payable_amount.text)}></span></p>
                                                            <div className="clearfix"></div>
                                                            <button className="btn deliver_btn button-register collapsed" onClick={this.props.placeOrder} data-payment-method={method} data-toggle="collapse" data-parent="#accordion" data-direction="#collapsefour"  type="button" aria-expanded="false">{this.props.language.text_pay_now}</button>
                                                        </div>
                                                    ):(
                                                        <div>
                                                            {method == 'cod'?(
                                                                <div>
                                                                    {parseInt(this.props.payment_data.cod_available) == 1 ?
                                                                        (
                                                                        <div>
                                                                        {cod_available_limit > sub_total_amount.value ?
                                                                            (
                                                                            <div>
                                                                              <p style={{color:'#f00'}}>
                                                                                  Cash on Delivery facility is only available on Orders with product value (Cart Subtotal) more than Rs. {cod_available_limit}. Please add more products to your cart.
                                                                              </p>
                                                                             </div>
                                                                           ):(
                                                                            <div>
                                                                            <p>Amount Payable  <span  className="Payable_Amount" dangerouslySetInnerHTML={this.createMarkup(total_payable_amount.text)}></span></p>
                                                                            <p><b>Please note:</b> We do not do 100% Cash On Delivery. 10% of the amount(or Rs. {cod_advance_amount_limit} whichever is greater) has to be paid in advance and rest can be paid at the time of delivery.

                                                                                <br/>
                                                                                <br/>
                                                                              
                                                                                <div dangerouslySetInnerHTML={this.createMarkup(this.props.language.text_cod_10_per.replace("%s", cod_advance_amount))}></div>
                                                                                 <br/>
                                                                                 <div dangerouslySetInnerHTML={this.createMarkup(this.props.language.text_cod_90_per.replace("%s", remaining_amount))}></div>
 
                                                                                </p>
                                                                             <p>{this.props.language.text_cash_deposit}</p>
                                                                            <p><strong>{this.props.language.text_cash_deposit_option}</strong></p>
                                                                            <p><strong>{this.props.payment_data.bank_transfer_info.text_instruction}</strong></p>
                                                                            <p className="font_small" dangerouslySetInnerHTML={this.createMarkup(this.props.payment_data.bank_transfer_info.bank_transfer)} ></p>
                                                                            {this.props.payment_data.hasOwnProperty('upi_id')?(<div><p><p><strong>{this.props.language.text_upi_option}</strong></p><strong>{this.props.language.text_upi_id}:</strong> {this.props.payment_data.upi_id}</p><p><a href="javascript:void(0)" onClick={this.showUPIHelpPopup}>{this.props.language.text_click_here}</a> {this.props.language.text_upi_using}</p></div>):(<div style={{display:'none'}}></div>)}
                                                                            <p className="font_small">{this.props.language.text_ship_order} </p>
                                                                            <form>
                                                                                <div className="clearfix"></div>
                                                                                <button className="btn deliver_btn button-register pull-right" onClick={this.props.placeOrder} data-payment-method={method} type="button">{this.props.language.text_checkout_confirm}</button>
                                                                            </form>
                                                                        </div>)}

                                                                        </div>
                                                                    ):(
                                                                        <div key={key}>
                                                                            <p>
                                                                              {this.props.payment_data.cod_not_available_text}
                                                                              <button
                                                                                  className="btn change_shipping_btn"
                                                                                  onClick={() => this.props.goToShippingTab()}
                                                                                  type="button"
                                                                              >
                                                                                {this.props.payment_data.change_shipping_text}
                                                                              </button>
                                                                            </p>
                                                                        </div>
                                                                    )}
                                                                </div>
                                                            ):(
                                                                <div>
                                                                    {method == 'bank_transfer'?(
                                                                        <div>
                                                                            <p>{this.props.language.text_amount_pay}  <span  className="Payable_Amount" dangerouslySetInnerHTML={this.createMarkup(total_payable_amount.text)}></span></p>
                                                                            <br/>
                                                                            <p><b>{this.props.language.text_note}:</b> {this.props.payment_data.bank_transfer_info.text_payment}</p>
                                                                            <p>{this.props.language.text_cash_deposit}</p>
                                                                            <p><strong>{this.props.language.text_cash_deposit_option}</strong></p>
                                                                            <p><strong>{this.props.payment_data.bank_transfer_info.text_instruction}</strong></p>
                                                                            <p className="font_small" dangerouslySetInnerHTML={this.createMarkup(this.props.payment_data.bank_transfer_info.bank_transfer)}>
                                                                            </p>
                                                                            {this.props.payment_data.hasOwnProperty('upi_id')?(<div><p><strong>{this.props.language.text_upi_option}</strong></p><p><strong>{this.props.language.text_upi_id}:</strong> {this.props.payment_data.upi_id}</p><p><a href="javascript:void(0)" onClick={this.showUPIHelpPopup}>{this.props.language.text_click_here}</a> {this.props.language.text_upi_using}</p></div>):(<div style={{display:'none'}}></div>)}
                                                                            <p className="font_small">{this.props.language.text_ship_order} </p>
                                                                            <form>
                                                                                <div className="clearfix"></div>
                                                                                <button className="btn deliver_btn button-register pull-right" onClick={this.props.placeOrder} data-payment-method={method} type="button">{this.props.language.text_checkout_confirm}</button>
                                                                            </form>
                                                                        </div>
                                                                    ):(
                                                                        <div>
                                                                            {method == 'upi'?(
                                                                                <div>
                                                                                    <p>{this.props.language.text_amount_pay}  <span  className="Payable_Amount" dangerouslySetInnerHTML={this.createMarkup(total_payable_amount.text)}></span></p>
                                                                                    <br/>
                                                                                    <p>{this.props.language.entry_upi_id}:&nbsp;</p>
                                                                                    <br/>
                                                                                    <form>
                                                                                        <p><input type="text" name="upi_vpa" id="upi_vpa" defaultValue={customer_vpa} disabled onChange={this.validateVPA}/>&nbsp;<i className="fa fa-pencil edit_vpa" onClick={this.editVPA} onMouseOver="" style={{cursor: 'pointer'}}/></p>
                                                                                        <p><font color='red'><span id="upi_vpa_error"></span></font></p>
                                                                                        <div className="clearfix"></div>
                                                                                        <button className="btn deliver_btn button-register pull-right" onClick={this.props.placeOrder} data-payment-method={method} type="button">{this.props.language.text_checkout_confirm}</button>
                                                                                    </form>
                                                                                </div>
                                                                            ):(
                                                                              <div>
                                                                                {method == 'credit'?(
                                                                                    <div>
                                                                                        <p>{this.props.language.text_amount_pay} <span  className="Payable_Amount" dangerouslySetInnerHTML={this.createMarkup(total_payable_amount.text)}></span></p>
                                                                                        <p>
                                                                                        <div dangerouslySetInnerHTML={this.createMarkup(this.props.language.text_credit_limit.replace('%s', this.props.neo_credit_user_credit_limit))}></div>
                                                                                        </p>
                                                   
                                                                                        {
                                                                                        (total_payable_amount.value > this.props.neo_credit_user_credit_limit && this.props.neo_credit_user_credit_limit>0)?
                                                                                        <div>
                                                                                        <p>
                                                                                        <div dangerouslySetInnerHTML={this.createMarkup(this.props.language.text_remain_amount.replace('%s', remain_credit_amount))}></div>
                                                                                        </p>
                                                                                        <p>{this.props.language.text_cash_deposit}</p>
                                                                                        <div className="bank_transfer_instruction checkout_neft"><p><strong>{this.props.language.text_cash_deposit_option}</strong></p>
                                                                                        <p><strong>{this.props.payment_data.bank_transfer_info.text_instruction}</strong></p>
                                                                                        <p className="font_small" dangerouslySetInnerHTML={this.createMarkup(this.props.payment_data.bank_transfer_info.bank_transfer)}>
                                                                                        </p>
                                                                                        </div>
                                                                                        {this.props.payment_data.hasOwnProperty('upi_id')?(<div className="bank_transfer_instruction checkout_upi"><p><strong>{this.props.language.text_upi_option}</strong></p><p><strong>{this.props.language.text_upi_id}:</strong> {this.props.payment_data.upi_id}</p><p><a href="javascript:void(0)" onClick={this.showUPIHelpPopup}>{this.props.language.text_click_here}</a> {this.props.language.text_upi_using}</p></div>):(<div style={{display:'none'}}></div>)}
                                                                                        <p className="font_small">{this.props.language.text_ship_order}</p>
                                                                                        </div>
                                                                                        :""
                                                                                        }

                                                                                        <form>
                                                                                            <div className="clearfix"></div>
                                                                                            <button className="btn deliver_btn button-register pull-right" onClick={this.props.placeOrder} data-payment-method={method} type="button">{this.props.language.text_checkout_confirm}</button>
                                                                                        </form>

                                                                                    </div>
                                                                                 ):(
                                                                                <div>
                                                                                  {method == 'rbl'?(
                                                                                    <div>
                                                                                     <p dangerouslySetInnerHTML={{ __html: this.props.cart_summary.text_rbl_consent }} />
                                                                                    
                                                                                     {total_payable_amount.value < this.props.RBL_ORDER_LIMIT ?
                                                                                      <div>
                                                                                         <p>Your Rbl Credit limit is <span  className="Payable_Amount">&#8377; {this.props.rbl_credit_user_credit_limit}</span></p>
                                                                                        <p>Amount Payable <span  className="Payable_Amount" dangerouslySetInnerHTML={this.createMarkup(total_payable_amount.text)}></span></p>
                                                                                        <p dangerouslySetInnerHTML={{ __html: this.props.text_rbl_limit_error }} />
                                                                                      </div>
                                                                                      :  
                                                                                    <div>
                                                                                        <p>
                                                                                        <div dangerouslySetInnerHTML={this.createMarkup(this.props.language.text_rbl_limit.replace('%s', this.props.rbl_credit_user_credit_limit))}></div>
                                                                                        </p>

                                                                                        <p>{this.props.language.text_amount_pay} <span  className="Payable_Amount" dangerouslySetInnerHTML={this.createMarkup(total_payable_amount.text)}></span></p>
                                                                                       
                                                   
                                                                                        {
                                                                                        (total_payable_amount.value > this.props.rbl_credit_user_credit_limit && this.props.rbl_credit_user_credit_limit>0)?
                                                                                        <div>
                                                                                        <p dangerouslySetInnerHTML={{ __html: '<span style="color:red">Dear Customer, You do not have sufficient RBL credit balance! Please choose another payment method to place your order.</span>' }} />
                                                                                        </div>
                                                                                        :
                                                                                         <form>
                                                                                            <div className="clearfix"></div>
                                                                                            <button className="btn deliver_btn button-register pull-right" onClick={this.props.placeOrder} data-payment-method={method} type="button">{this.props.language.text_checkout_confirm}</button>
                                                                                        </form>
                                                                                        }


                                                                                       
                                                                                    </div> 
                                                                                    }  
                                                                                    </div> 
                                                                                ):(
                                                                                  <div>
                                                                                  {method == 'lazypay'?(
                                                                                    <div>
                                                                                     {this.props.lazypay_eligibility ?
                                                                                        <div id="payment_lazypay_div">
                                                                                        <p>{this.props.language.text_amount_pay} <span  className="Payable_Amount" dangerouslySetInnerHTML={this.createMarkup(total_payable_amount.text)}></span></p>
                                                                                        {this.props.lazypay_eligibility.success?
                                                        
                                                                                            <form>
                                                                                            <p>{this.props.lazypay_eligibility.msg}</p>
                                                                                            <div className="clearfix"></div>
                                                                                            <button className="btn deliver_btn button-register pull-right" onClick={this.props.placeOrder} data-payment-method={method} type="button">Confirm Order</button>
                                                                                            </form>
                                                                                            :<p>{this.props.lazypay_eligibility.msg}</p>
                                                                                        }

                                                                                        <div className="lazypay_feature" style={{marginTop:'8%'}}>
                                                                                         {this.props.language.text_lazypay_features}
                                                                                           <ul style={{paddingLeft:'20px'}}>
                                                                                            <li>{this.props.language.text_lazypay_features_1}</li>
                                                                                            <li>{this.props.language.text_lazypay_features_2}</li>
                                                                                            <li>{this.props.language.text_lazypay_features_3}</li>
                                                                                            <li>{this.props.language.text_lazypay_features_4}</li>
                                                                                             </ul>
                                                                                           </div>
                                                                                    </div>
                                                                                    : 
                                                                                     <div>
                                                                                     <p>{this.props.language.text_amount_pay} <span  className="Payable_Amount" dangerouslySetInnerHTML={this.createMarkup(total_payable_amount.text)}></span></p>
                                                                                     <p>{this.props.language.text_please_wait}</p>
                                                                                     </div>
                                                                                    }
                                                                                     <div id="confirm_lazypay_div" style={{display:'none'}}></div>
                                                                                   </div>
                                                                                  ):(
                                                                                    <div>
                                                                                    <p>Amount Payable <span  className="Payable_Amount" dangerouslySetInnerHTML={this.createMarkup(total_payable_amount.text)}></span></p>
                                                                                      <form>
                                                                                          <div className="clearfix"></div>
                                                                                          <button className="btn deliver_btn button-register pull-right" onClick={this.props.placeOrder} data-payment-method={method} type="button">{this.props.language.text_checkout_confirm}</button>
                                                                                      </form>
                                                                                       </div>
                                                                                  )}
                                                                                      
                                                                                  </div>
                                                                                )}
                                                                                </div>
                                                                               )}
                                                                              </div>
                                                                            )}

                                                                        </div>
                                                                    )}
                                                                </div>
                                                            )}

                                                        </div>
                                                    )}
                                                </div>
                                            </div>
                                        </div>
                                    );
                                }
                            )}
                        </div>
                    </div>
                ):(
                    <div>
                        <p>We are sorry! No payment method is available.</p>
                    </div>
                )}
            </div>
        );
    }
}
