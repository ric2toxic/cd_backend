import React, { Component } from 'react'
import InputNumber from './input_number'

class Options extends Component {

render(){
let self = this;
return (<tbody>
    { this.props.options.map((option, index) => { 
    	var name = 'option_quantities[' + self.props.product_option_id + '][' + option.product_option_value_id + ']';
    	var name_max = 'option_max_quantities[' + self.props.product_option_id + '][' + option.product_option_value_id + ']';
     return(<tr key={index} className={option.quantity > 0 ? 'odd' : 'odd danger'}>
     <td>
     {self.props.product_option_name === 'Color' ?
     <span className={"color_code color_code_big "+option.name.toLowerCase()}></span>
     : ''}
     
     {self.props.product_option_name === 'Color' ?
     <br />
     :''}

     <span className="option_color_name">{option.name}</span></td>
     <td>{option.quantity} Set</td>
     <td>
     {option.quantity > 0 ?
       <InputNumber name={name} name_max={name_max} minimum={0} quantity={option.quantity} product_option_value_id={option.product_option_value_id} key="index" from_options={true}/>
       : <span> Out of Stock</span>
     }
      </td></tr>)
       })
     } 
	</tbody>)
}
}

export default Options
