import React, { Component } from 'react'

class PromoCode extends Component {
    

    render() {
    	if(this.props.coupon)
    	{
          return (<div className="alert alert-success col-xs-12" id="remove_coupon_code" style={{padding:'5px 9px 6px 1px',marginTop:'5px',borderRadius:'0px'}}>
                                <div className="col-xs-10">Promo {this.props.coupon} applied.</div>
                                <div className="col-xs-2"><span className="btn deliver_btn apply_coupon_btn" id="remove_coupon" data-toggle="tooltip" onClick={this.props.removeCoupon} title="Remove Promo Code"><i className="fa fa-trash-o" aria-hidden="true"></i></span>
                                </div>
                                </div>);
    	}
    	else
    	{
        return (<div className="promo_code_penal">
        	     <input type="text" className="form-control promo_code_input" placeholder={this.props.language.text_place_promo} name="promo_code" />
                 <button type="button" className="btn btn-apply" onClick={this.props.applyCoupon}>Apply</button> 
                </div>);
        }	          
    }
}

export default PromoCode;