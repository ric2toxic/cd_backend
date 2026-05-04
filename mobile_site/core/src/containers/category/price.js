  import React, { Component } from 'react'
  import InputRange from 'react-input-range';
  import 'react-input-range/lib/css/index.css'
  import './price.css'

class Price extends Component 
{
  constructor(props) {
    super(props);
    this.set_currency  = this.set_currency.bind(this);
    this.state = {
      value: {min:0,max:0},
      fix_value: {min:0,max:0},
      minimum_price:0,
      maximum_price:0,
    };
  }	
   
  componentDidMount()
  {
    this.set_currency();
  }

  set_currency()
  {
    this.setState({ value: this.props.price_with_currency.price_value});
    this.setState({ fix_value: this.props.price_with_currency.price_value});
    this.setState({ minimum_price: this.props.price_with_currency.minimum_price});
    this.setState({ maximum_price: this.props.price_with_currency.maximum_price});
  }


render()
{
 if(this.props.price_with_currency && this.props.price_with_currency.price_value) 
 {
   if(this.props.price_with_currency.minimum_price !== this.state.minimum_price || this.props.price_with_currency.maximum_price !== this.state.maximum_price || this.props.price_with_currency.price_value.min !== this.state.fix_value.min || this.props.price_with_currency.price_value.max !== this.state.fix_value.max)
   {
     this.set_currency();
   }
 }  

 return (<div id="price_filter" className="filter_group tab-pane fade in active">
                      <div className="range_filter_input_box">
                        <InputRange  maxValue={this.state.maximum_price}  minValue={this.state.minimum_price} value={this.state.value} onChange={value => this.setState({ value })} />
                        <div className="clearfix"></div>
                      </div>

                      <span className="filter_price_btn" onClick={()=>this.props.clickHandlerPrice(this.state.value)}>Set Price</span>
                       <span className="clear-price-all" onClick={()=>this.props.callAjaxToRemovePrice()}>Reset</span>
                  </div>)

}}

export default Price