import React, { Component } from 'react'

class DateInput  extends Component {	

	render(){
		return (
      <div onClick={this.props.onClick}>
          <input type="text" className={this.props.className} placeholder={this.props.placeholderText} value={this.props.value} readonly="readonly" />
                <span className="input-group-addon_1" id='datetimepicker1'>
                        <span className="blue_color_theme"><i className="fa fa-calendar"></i></span>
                </span>
      </div>
		)
	}
}

export default (DateInput);