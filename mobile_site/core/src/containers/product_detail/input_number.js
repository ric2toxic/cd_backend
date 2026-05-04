import React, { Component } from 'react'
import $ from 'jquery'

class InputNumber  extends Component {

	constructor(props){
	    super(props);
	    this.state = {
	      data : parseInt(this.props.minimum,10),	
	      is_error : 0, 
	      error_msg : ''
	    }
            
        this.setIncreaseNumber = this.setIncreaseNumber.bind(this);
        this.setDecreaseNumber = this.setDecreaseNumber.bind(this);

	  };

componentWillReceiveProps()
{
      
  if(this.props.minimum >= 0){
        this.setState({data:parseInt(this.props.minimum,10)}); 
    }

   if(this.props.quantity === 0){
       this.setState({data:0});
   }   
}

setIncreaseNumber()
{      
     if(this.state.data < parseInt(this.props.quantity,10))
     { 
        this.setState({ data: this.state.data + 1}); 
        $("#option-input_"+this.props.product_option_value_id).val(this.state.data);     
     }
};

setDecreaseNumber()
{                                      
      if(this.state.data <= parseInt(this.props.minimum,10) )
      {
       this.setState({data : parseInt(this.props.minimum,10) });
       $("#option-input_"+this.props.product_option_value_id).val(this.state.data);    
      }
      else
      {
      	this.setState({ data: this.state.data - 1});
      	$("#option-input_"+this.props.product_option_value_id).val(this.state.data);
      }
};

render(){

if(this.props.from_options){	
return (<div className="set_quantity_panel">
       <span className="input-group-btn"><button className="btn btn-default bootstrap-touchspin-down" type="button"  onClick = {this.setDecreaseNumber}>-</button></span>
       <input type="number" value={this.state.data}  id={"option-input_"+this.props.product_option_value_id} placeholder="0" name={this.props.name} className="col-md-8 form-control set_quantity_box" />
       <span className="input-group-btn"><button className="btn btn-default bootstrap-touchspin-up" type="button"  onClick = {this.setIncreaseNumber}>+</button></span>
       <input type="hidden" id={"option-max_"+this.props.product_option_value_id} value={this.props.quantity} name={this.props.name_max} className="col-md-8 form-control set_box color_option" />
      </div>)
    }else{
return (<div className="set_quantity_panel_button">
       <span className="input-group-btn"><button className="btn btn-default bootstrap-touchspin-down" type="button"  onClick = {this.setDecreaseNumber}>-</button></span>
       <input type="number" value={this.state.data}  id={"option-input_"+this.props.product_option_value_id} placeholder="0" name={this.props.name} className="col-md-8 form-control set_quantity_box" />
       <span className="input-group-btn"><button className="btn btn-default bootstrap-touchspin-up" type="button"  onClick = {this.setIncreaseNumber}>+</button></span>
       <input type="hidden" id={"option-max_"+this.props.product_option_value_id} value={this.props.quantity} name={this.props.name_max} className="col-md-8 form-control set_box color_option" />
      </div>)      
    }
}
}      

export default InputNumber
