  import React, { Component } from 'react'

class Filter extends Component {
  
 render(){

    let self = this;
    let disable_class='';

 if(self.props.filter.product_count === 0) { disable_class = 'disable'; }

 return ( <li className={disable_class}><span className="pd_popup_selected"> {this.props.filter.name} <label>
           <input onClick={()=>this.props.clickHandler(this)} className="filters" type="checkbox" id={'filter'+this.props.filter.filter_id}  name="filter[]" value={this.props.filter.filter_id} /><div className="filter_checkbox"></div></label></span>
          </li>)

}

}
export default Filter