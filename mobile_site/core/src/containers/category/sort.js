import React, { Component } from 'react'

class Sort extends Component {

  render(){

 return (<div className="form-group sort_filter_section">
 	        <span onClick={()=>this.props.clickHandler(this)}>
             <label className="sort_filter_btn sort_control_radio">
                <input name="radio" type="radio" id={"sort_"+this.props.count} value={this.props.sort.query_string} />
                <div className="sort_control_indicator"></div>
              </label> {this.props.sort.text}
              </span>
            </div>)

}

}
export default Sort
