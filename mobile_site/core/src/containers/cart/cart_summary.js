import React, { Component } from 'react'
import $ from 'jquery'

class CartSummary extends Component {


    createMarkup(html) {
        return {__html: html};
    }

    render() {
        return (<div className="order_summary">
                    <h2>ORDER SUMMARY</h2>
                    <div className="order_summary_table">

                    {Object.keys(this.props.cart_summary.totals).map((data,key) =>
                        {
                             if ( ("net_payable_amount" in this.props.cart_summary.totals === true && data === "net_payable_amount") || ("net_payable_amount" in this.props.cart_summary.totals === false && data === "total")){
                               
                               return(
                                        <div key = {key} className="col-xs-12 nopadding bottem_line black_text_cart">
                                            <div className="col-xs-6 order_summary_table-left">{this.props.cart_summary.totals[data]['title']}</div>
                                            <div className="col-xs-6 order_summary_table-right">{ $('<div/>').html(this.props.cart_summary.totals[data]['text']).text() }</div>
                                        </div>
                                    );

                              }
                             else
                             {
                               var class_name = "col-xs-12 nopadding";
                               if(data === "sub_total" || data === "round_off")
                                  class_name = "col-xs-12 nopadding bottem_line";
                               else if (data === "coupon" || data === "paycharge"  || data === "shipping"   || data === "cashback")
                                   class_name = "col-xs-12 nopadding text_red";
                                    
                                    return(
                                        <div key = {key} className={class_name}>
                                            {data === "shipping"?(
                                                <div>
                                                    <div className="col-xs-6 order_summary_table-left">Shipping Charge</div>
                                                    <div className="col-xs-6 order_summary_table-right">{$('<div/>').html(this.props.cart_summary.totals[data]['text']).text() }</div>
                                                    <div className="col-xs-8 order_summary_table-left" style={{fontSize:'11px'}}>{this.props.cart_summary.totals[data]['title']}</div>
                                                </div>
                                            ):(
                                                <div>
                                                    <div className="col-xs-6 order_summary_table-left">{this.props.cart_summary.totals[data]['title']}</div>
                                                    <div className="col-xs-6 order_summary_table-right">{$('<div/>').html(this.props.cart_summary.totals[data]['text']).text()}</div>
                                                </div>
                                            )}
                                        </div>
                                    );
                             } 
                        })
                        }        
                        <br />
                        {this.props.cart_summary.disable_place_order === 1 ?
                            <div className="col-xs-12 text_red" dangerouslySetInnerHTML={this.createMarkup(this.props.cart_summary.text_cart_minimum)} ></div>
                            :
                           ''
                        }
                        <div className="clearfix"></div> 
                    </div>
                    <div className="clearfix"></div>
                </div>);
    }
}

export default CartSummary;