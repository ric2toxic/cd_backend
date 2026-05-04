class CartPayment extends React.Component{
    constructor(props){
        super(props);
        this.createMarkup = this.createMarkup.bind(this);
    }
    componentDidUpdate(){
        $(".payment a").click(function (e) {
            $(this).tab('show');
            e.preventDefault();
        });

        if(this.props.cart_summary.totals.hasOwnProperty('paycharge')){
            if(this.props.cart_summary.totals.sub_total.value > 25000)
                $('.payment_discount').html('<blink>Get 3%('+this.props.cart_summary.totals.paycharge.text+') Discount ! for Online Payment</blink>')
            else
                $('.payment_discount').html('<blink>Get 2%('+this.props.cart_summary.totals.paycharge.text+') Discount ! for Online Payment</blink>');
        }
    }
    componentDidMount() {
        this.props.updatePaymentMethod($('.payment_view .active .button-register').attr('data-payment-method'));
        $(".payment a").click(function (e) {
            $(this).tab('show');
            e.preventDefault();
        });
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
        
        var sub_total_amount         = this.props.cart_summary.totals.sub_total;
        var cod_available_limit      = this.props.cod_available_limit;
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
    function createMarkup(html) {
       return {__html: html};
     }

        return(
            <div>
                {this.props.payment_data ?
                    this.props.payment_data.hasOwnProperty('payment_methods')?(
                    <div >
                        <div className="col-sm-12 nopadding" style={{background:'#e8ecfe',marginBottom:'3px'}}>
                            <img src={this.props.payment_data.netbanking_help_url} width="100%"/>
                        </div>
                        {parseInt(this.props.payment_data.show_offers) != 1? (
                            <div style={{display:'none'}}></div>
                        ):(
                            <div className="col-sm-12 nopadding" style={{background:'#e8ecfe',marginBottom:'3px'}}>
                                <p className="payment_discount" style={{background:'#e8ecfe',padding: '3px',color: '#17319f',marginLeft:'33%',fontSize:'15px'}}><blink>Get 2% Discount ! for Online Payment</blink></p>
                            </div>
                        )}
                        <div className="col-sm-5 nopadding">
                            <ul className="nav payment" id="payment_method_title_panel">
                                {Object.keys(this.props.payment_data.payment_methods).map(
                                    (method,key) => {
                                        var icon_class = "fa fa-window-maximize";
                                        var active_class = "";
                                        if(key == 0) active_class="active";
                                        if(method == 'citrus')
                                            icon_class = "fa fa-credit-card-alt";
                                        else if(method == 'razorpay')
                                            icon_class = "fa fa-credit-card-alt";
                                        else if(method == 'cod')
                                            icon_class = "fa fa-truck";
                                        else if(method == 'paytm')
                                            icon_class = "fa fa-desktop";
                                        return ( <li key={key} onClick={this.props.updatePaymentMethod.bind(this,method)} className={active_class} ><a href={"#"+method} className="disabled" ><label><i className={icon_class} aria-hidden="true"></i> {this.props.payment_data.payment_methods[method].title} <span className="arrow"><i className="fa fa-angle-right" aria-hidden="true"></i></span> </label></a></li> );
                                    }
                                )}
                            </ul>
                        </div>
                        <div className="col-sm-7 nopadding">
                            <div className="tab-content payment_view">
                                {Object.keys(this.props.payment_data.payment_methods).map(
                                    (method,key) => {
                                        var active_class = "";
                                        var remain_credit_amount='0';
                                        var remain_lazypay_amount='0';
                                        var remain_rbl_credit_amount=0;

                                        if(method == 'credit' && total_payable_amount.value > this.props.neo_credit_user_credit_limit){
                                        remain_credit_amount = (total_payable_amount.value-this.props.neo_credit_user_credit_limit).toFixed(2);
                                        }

                                        if(method == 'rbl' && total_payable_amount.value > this.props.rbl_credit_user_credit_limit){
                                        remain_rbl_credit_amount = (total_payable_amount.value-this.props.rbl_credit_user_credit_limit).toFixed(2);
                                        }

                                        if(key == 0) active_class="in active";
                                        if(method == 'citrus' || method == 'razorpay'){
                                            return(

                                                <div key={key} id={method} className={"tab-pane payment_detail fade "+active_class}>
                                                    <div>
                                                        <p>Pay using Debit Card/Credit Card/Net Banking</p>
                                                        <p>Amount Payable  <span  className="Payable_Amount" dangerouslySetInnerHTML={this.createMarkup(total_payable_amount.text)}></span></p>
                                                        <div className="clearfix"></div>
                                                        <button className="btn deliver_btn button-register pull-right" onClick={this.props.placeOrder} data-payment-method={method}  type="button">PAY NOW</button>
                                                    </div>
                                                </div>
                                            );
                                        }
                                        else if (method == 'cod'){
                                                 if(cod_available_limit > sub_total_amount.value)
                                                 {
                                                    return(
                                                         <div key={key} id="cod"
                                                         className={"tab-pane payment_detail fade " + active_class}>
                                                          <div>
                                                              <p style={{color:'#f00'}}>
                                                                  Cash on Delivery facility is only available on Orders with product value (Cart Subtotal) more than Rs. {cod_available_limit}. Please add more products to your cart.
                                                              </p>
                                                          </div>
                                                         </div>
                                                    )
                                                 }
                                                 else if(parseInt(this.props.payment_data.cod_available) == 1) {
                                                    return(
                                                    <div key={key} id="cod"
                                                         className={"tab-pane payment_detail fade " + active_class}>
                                                        <div>
                                                            <p>Amount Payable <span
                                                                className="Payable_Amount" dangerouslySetInnerHTML={this.createMarkup(total_payable_amount.text)}></span>
                                                            </p>
                                                            <p><b>Please note:</b> We do not do 100% Cash On Delivery.
                                                                10% of the amount(or Rs. {cod_advance_amount_limit} whichever is greater) has
                                                                to be paid in advance and rest can be paid at the time
                                                                of delivery.
                                                                <br/>
                                                                <br/>
                                                                10% of Total Amount: <span className="Payable_Amount" dangerouslySetInnerHTML={createMarkup(' &#8377 '+cod_advance_amount)}></span>
                                                                Pay using RTGS/NEFT/IMPS or Pay Online after
                                                                Confirm-Order {/*<span className="payment_online_btn">Pay Online</span>*/}
                                                                <br/> 90% of Total Amount : <span
                                                                    className="Payable_Amount" dangerouslySetInnerHTML={createMarkup(' &#8377 '+remaining_amount)}></span>
                                                                Cash On Delivery</p>
                                                            <p>Pay using Cash Deposit/Cheque/NEFT/RTGS</p>
                                                            <div className="bank_transfer_instruction checkout_neft"><p>
                                                                <strong>Option 1: Pay using Cash
                                                                    Deposit/Cheque/NEFT/RTGS</strong></p>
                                                                <p>
                                                                    <strong>{this.props.payment_data.bank_transfer_info.text_instruction}</strong>
                                                                </p>
                                                                <p className="font_small"
                                                                   dangerouslySetInnerHTML={this.createMarkup(this.props.payment_data.bank_transfer_info.bank_transfer)}></p>
                                                            </div>
                                                            {this.props.payment_data.hasOwnProperty('upi_id') ? (
                                                                <div className="bank_transfer_instruction checkout_upi">
                                                                    <p><strong>Option 2: Pay using UPI id</strong></p>
                                                                    <p><strong>Wholesalebox UPI
                                                                        Id:</strong> {this.props.payment_data.upi_id}
                                                                    </p><p><a href="javascript:void(0)"
                                                                              onClick={this.showUPIHelpPopup}>Click
                                                                    here</a> to know "How to pay using UPI id?"</p>
                                                                </div>) : (<div style={{display: 'none'}}></div>)}
                                                            <p className="font_small">We shall ship your order after
                                                                payment confirmation </p>
                                                            <form>
                                                                <div className="clearfix"></div>
                                                                <button
                                                                    className="btn deliver_btn button-register pull-right"
                                                                    onClick={this.props.placeOrder}
                                                                    data-payment-method="cod" type="button">Confirm
                                                                    Order
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    );
                                                }
                                                else{
                                                    return(
                                                    <div key={key} id="cod" className={"tab-pane payment_detail fade "+active_class}>
                                                        <p>{this.props.payment_data.cod_not_available_text}
                                                            <button
                                                                className="btn change_shipping_btn"
                                                                onClick={() => this.props.changeTab('shipping')}
                                                                type="button"
                                                            >
                                                              {this.props.payment_data.change_shipping_text}
                                                            </button>
                                                          </p>
                                                    </div>
                                                    );
                                                }

                                        }
                                        else if (method == 'bank_transfer'){
                                            return(
                                                <div key={key} id="bank_transfer" className={"tab-pane payment_detail fade "+active_class}>
                                                    <div>
                                                        <p>Amount Payable  <span  className="Payable_Amount" dangerouslySetInnerHTML={this.createMarkup(total_payable_amount.text)}></span></p>
                                                        <br/>
                                                        <p><b>Please note:</b> {this.props.payment_data.bank_transfer_info.text_payment}</p>
                                                        <p>Pay using Cash Deposit/Cheque/NEFT/RTGS</p>
                                                        <div className="bank_transfer_instruction checkout_neft"><p><strong>Option 1: Pay using Cash Deposit/Cheque/NEFT/RTGS</strong></p>
                                                        <p><strong>{this.props.payment_data.bank_transfer_info.text_instruction}</strong></p>
                                                        <p className="font_small" dangerouslySetInnerHTML={this.createMarkup(this.props.payment_data.bank_transfer_info.bank_transfer)}>
                                                        </p>
                                                        </div>
                                                        {this.props.payment_data.hasOwnProperty('upi_id')?(<div className="bank_transfer_instruction checkout_upi"><p><strong>Option 2: Pay using UPI id</strong></p><p><strong>Wholesalebox UPI Id:</strong> {this.props.payment_data.upi_id}</p><p><a href="javascript:void(0)" onClick={this.showUPIHelpPopup}>Click here</a> to know "How to pay using UPI id?"</p></div>):(<div style={{display:'none'}}></div>)}
                                                        <p className="font_small">We shall ship your order after payment confirmation </p>
                                                        <form>
                                                            <div className="clearfix"></div>
                                                            <button className="btn deliver_btn button-register pull-right" onClick={this.props.placeOrder} data-payment-method="bank_transfer" type="button">Confirm Order</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            );
                                        }
                                        else if(method == 'paytm'){
                                            return(
                                                <div key={key} id={method} className={"tab-pane payment_detail fade "+active_class}>
                                                    <div>
                                                        <p>Amount Payable <span  className="Payable_Amount" dangerouslySetInnerHTML={this.createMarkup(total_payable_amount.text)}></span></p>
                                                        <form>
                                                            <div className="clearfix"></div>
                                                            <button className="btn deliver_btn button-register pull-right" onClick={this.props.placeOrder} data-payment-method={method} type="button">PAY NOW</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            );
                                        }else if(method == 'upi'){
                                            return(
                                                <div key={key} id={method} className={"tab-pane payment_detail fade "+active_class}>
                                                    <div>
                                                        <p>Amount Payable <span  className="Payable_Amount" dangerouslySetInnerHTML={this.createMarkup(total_payable_amount.text)}></span></p>
                                                        <p>Enter your UPI Id:&nbsp;</p><input type="text" name="upi_vpa" id="upi_vpa" defaultValue={customer_vpa} disabled onChange={this.validateVPA}/><i className="fa fa-pencil edit_vpa" onClick={this.editVPA} onMouseOver="" style={{cursor: 'pointer'}} />
                                                        <p><font color='red'>&nbsp;&nbsp;&nbsp;<span id="upi_vpa_error"></span></font></p>
                                                        <form>
                                                            <div className="clearfix"></div>
                                                            <button id="deliver_btn" className="btn deliver_btn button-register pull-right" onClick={this.props.placeOrder} data-payment-method={method} type="button">PAY NOW</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            );
                                        }else if(method == 'credit'){
                                            return(
                                                <div key={key} id={method} className={"tab-pane payment_detail fade "+active_class}>
                                                    <div>
                                                        <p>Amount Payable <span  className="Payable_Amount" dangerouslySetInnerHTML={this.createMarkup(total_payable_amount.text)}></span></p>
                                                        <p>Your Credit limit is <span  className="Payable_Amount">Rs. {this.props.neo_credit_user_credit_limit}</span></p>

                                                        {
                                                        (total_payable_amount.value > this.props.neo_credit_user_credit_limit && this.props.neo_credit_user_credit_limit>0)?
                                                        <div>
                                                        <p><b>Please note:</b> Your order will not ship until we receive payment <span className="Payable_Amount">Rs. {remain_credit_amount}</span></p>
                                                        <p>Pay using Cash Deposit/Cheque/NEFT/RTGS</p>
                                                        <div className="bank_transfer_instruction checkout_neft"><p><strong>Option 1: Pay using Cash Deposit/Cheque/NEFT/RTGS</strong></p>
                                                        <p><strong>{this.props.payment_data.bank_transfer_info.text_instruction}</strong></p>
                                                        <p className="font_small" dangerouslySetInnerHTML={this.createMarkup(this.props.payment_data.bank_transfer_info.bank_transfer)}>
                                                        </p>
                                                        </div>
                                                        {this.props.payment_data.hasOwnProperty('upi_id')?(<div className="bank_transfer_instruction checkout_upi"><p><strong>Option 2: Pay using UPI id</strong></p><p><strong>Wholesalebox UPI Id:</strong> {this.props.payment_data.upi_id}</p><p><a href="javascript:void(0)" onClick={this.showUPIHelpPopup}>Click here</a> to know "How to pay using UPI id?"</p></div>):(<div style={{display:'none'}}></div>)}
                                                        <p className="font_small">We shall ship your order after payment confirmation </p>
                                                        </div>
                                                        :""
                                                        }
                                                        {
                                                        this.props.neo_credit_user_credit_limit>0?
                                                        <form>
                                                            <div className="clearfix"></div>
                                                            <button className="btn deliver_btn button-register pull-right" onClick={this.props.placeOrder} data-payment-method={method} type="button">Confirm Order</button>
                                                        </form>
                                                        :<p>Dear Customer, Your Credit limit is zero! Please choose another payment method to place your order.</p>
                                                        }

                                                    </div>
                                                </div>
                                            );
                                        }else if(method == 'rbl'){
                                            return(
                                                <div key={key} id={method} className={"tab-pane payment_detail fade "+active_class}>
                                                    <p dangerouslySetInnerHTML={{ __html: this.props.cart_summary.text_rbl_consent }} />
                                                    {total_payable_amount.value < this.props.RBL_ORDER_LIMIT ?
                                                      <div>
                                                         <p>Your Rbl Credit limit is <span  className="Payable_Amount">&#8377; {this.props.rbl_credit_user_credit_limit}</span></p>
                                                        <p>Amount Payable <span  className="Payable_Amount" dangerouslySetInnerHTML={this.createMarkup(total_payable_amount.text)}></span></p>
                                                       <p dangerouslySetInnerHTML={{ __html: this.props.text_rbl_limit_error }} />
                                                     </div>
                                                     :   
                                                    <div>
                                                         <p>Your Rbl Credit limit is <span  className="Payable_Amount">&#8377; {this.props.rbl_credit_user_credit_limit}</span></p>
                                                        <p>Amount Payable <span  className="Payable_Amount" dangerouslySetInnerHTML={this.createMarkup(total_payable_amount.text)}></span></p>
                                                        {
                                                        (total_payable_amount.value > this.props.rbl_credit_user_credit_limit)?
                                                        <div>
                                                        <p dangerouslySetInnerHTML={{ __html: '<span style="color:red">Dear Customer, You do not have sufficient RBL credit balance! Please choose another payment method to place your order.</span>' }} />
                                                        </div>
                                                        :
                                                        <form>
                                                            <div className="clearfix"></div>
                                                            <button className="btn deliver_btn button-register pull-right" onClick={this.props.placeOrder} data-payment-method={method} type="button">Confirm Order</button>
                                                        </form>
                                                        }
                                                    </div>
                                                   }
                                                </div>
                                            );    
                                        } else if(method == 'lazypay'){
                                            return(
                                                <div key={key} id={method} className={"tab-pane payment_detail fade "+active_class}>
                                                    {this.props.lazypay_eligibility ?
                                                    <div id="payment_lazypay_div">
                                                        <p>Amount Payable <span  className="Payable_Amount" dangerouslySetInnerHTML={this.createMarkup(total_payable_amount.text)}></span></p>
                                                        {this.props.lazypay_eligibility.success?
                                                        
                                                        <form>
                                                            <p>{this.props.lazypay_eligibility.msg}</p>
                                                            <div className="clearfix"></div>
                                                            <button className="btn deliver_btn button-register pull-right" onClick={this.props.placeOrder} data-payment-method={method} type="button">Confirm Order</button>
                                                        </form>
                                                        :<p>{this.props.lazypay_eligibility.msg}</p>
                                                        }
                                                      
                                                     <div className="lazypay_feature" style={{marginTop:'8%'}}>
                                                      Top features of LazyPay - Pay Later
                                                        <ul style={{paddingLeft:'20px'}}>
                                                        <li> Zero Cost Credit(0% interest)</li>
                                                        <li> No registration needed</li>
                                                        <li> Place your order with just an OTP</li>
                                                        <li> Pay us back within the due date – we will remind you</li>
                                                        </ul>
                                                      </div>
                                                    </div>
                                                    : 
                                                    <div>
                                                    <p>Amount Payable <span  className="Payable_Amount" dangerouslySetInnerHTML={this.createMarkup(total_payable_amount.text)}></span></p>
                                                    <p>please wait...</p>
                                                    </div>
                                               }
                                                <div id="confirm_lazypay_div" style={{display:'none'}}></div>
                                                </div>
                                            );
                                        }
                                        else{
                                            return(
                                                <div key={key} id={method} className={"tab-pane payment_detail fade "+active_class}>
                                                    <div>
                                                        <p>Amount Payable <span  className="Payable_Amount" dangerouslySetInnerHTML={this.createMarkup(total_payable_amount.text)}></span></p>
                                                        <form>
                                                            <div className="clearfix"></div>
                                                            <button className="btn deliver_btn button-register pull-right" onClick={this.props.placeOrder} data-payment-method={method} type="button">Confirm Order</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            );
                                        }
                                    }
                                )}
                            </div>
                        </div>
                    </div>
                ):(
                    <div>
                        <p>We are sorry! No payment method is available.</p>
                    </div>
                ) : ''}
            </div>
        );
    }
}
