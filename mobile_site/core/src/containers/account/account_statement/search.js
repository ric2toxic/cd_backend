import React, { Component } from 'react'
import DatePicker from "react-datepicker";
import "react-datepicker/dist/react-datepicker.css";
import DateInput from "./date_input"

import $ from 'jquery'

class Search  extends Component {	

constructor(props) {
    super(props);
    this.state = {
      filter_ordered_date_from: this.props.filter_search.filter_ordered_date_from ? new Date(this.props.filter_search.filter_ordered_date_from) : new Date(),
      filter_ordered_date_to: this.props.filter_search.filter_ordered_date_to ? new Date(this.props.filter_search.filter_ordered_date_to) : new Date(),
      filter_invoice_date_from:this.props.filter_search.filter_invoice_date_from ? new Date(this.props.filter_search.filter_invoice_date_from) : new Date(),
      filter_invoice_date_to:this.props.filter_search.filter_invoice_date_to ? new Date(this.props.filter_search.filter_invoice_date_to) : new Date()
    };
    this.handleChange = this.handleChange.bind(this);
  }

	componentDidMount()
  {
		var self = this;
		$('input, select').on('keypress',function(e){
			if(e.keyCode===13){
				self.props.getStatementList();
			}
		});
	}

  handleChange(date, field) 
  { 
    var month     = date.getMonth()+1;
    var day       = date.getDate();
    var year      = date.getFullYear();
    date = year+'-'+month+'-'+day;
    this.props.searchChangeDateValue(field, date);
  }

	render(){
		return (
		<section>	

              <div className="row">
                <form>
                  <div className="col-sm-3 col-xs-12 col-md-3 col-lg-3">
                    <div className="form-group">
                      <div className="input-group-prepend">
                        <span className="input-group-text" id="">Order No</span>
                      </div>
                    <input type="text" className="form-control" name="filter_order_no" onChange={this.props.searchChangeValue.bind(this, "filter_order_no")} placeholder="Order No" value={this.props.filter_search.filter_order_no ? this.props.filter_search.filter_order_no: ''} />
                    </div>
                  </div>
                  <div className="col-sm-3 col-xs-12 col-md-3 col-lg-3">
                    <div className="form-group">
                      <div className="input-group-prepend">
                        <span className="input-group-text" id="">Invoice No</span>
                      </div>
                     <input type="text" className="form-control" name="filter_invoice_no" onChange={this.props.searchChangeValue.bind(this, "filter_invoice_no")} placeholder="Invoice No" value={this.props.filter_search.filter_invoice_no ? this.props.filter_search.filter_invoice_no: ''} />
                    </div>
                  </div>
                  <div className="container date_input_box">
                    <div className="input-group calender col-sm-3 col-xs-12 col-md-3 col-lg-3">
                      <div className="input-group-prepend">
                        <span className="input-group-text" id="">Order Date</span>
                      </div>
                      <DatePicker
                        selected={this.props.filter_search.filter_ordered_date_from ? new Date(this.props.filter_search.filter_ordered_date_from) : ''}
                         onChange={(e)=>this.handleChange(e,'filter_ordered_date_from')}
                         dateFormat="yyyy-MM-dd"
                         className="form-control date_calendar_from"
                         placeholderText="From"
                         id="filter_ordered_date_from" 
                         name="filter_ordered_date_from"
                         customInput={<DateInput className="form-control date_calendar_from" placeholderText="From" />}
                       />
                       <DatePicker
                        selected={this.props.filter_search.filter_ordered_date_to ? new Date(this.props.filter_search.filter_ordered_date_to) : ''}
                         onChange={(e)=>this.handleChange(e,'filter_ordered_date_to')}
                         dateFormat="yyyy-MM-dd"
                         className="form-control date_calendar_to"
                         placeholderText="To"
                         id="filter_ordered_date_to" 
                         name="filter_ordered_date_to"
                         customInput={<DateInput className="form-control date_calendar_to" placeholderText="To" />}
                       />
                    </div>
                  </div>
                  <div className="container date_input_box">
                    <div className="input-group calender col-sm-3 col-xs-12 col-md-3 col-lg-3" id='datetimepicker1'>
                      <div className="input-group-prepend">
                        <span className="input-group-text" id="">Invoice Date</span>
                      </div>
                      <DatePicker
                        selected={this.props.filter_search.filter_invoice_date_from ? new Date(this.props.filter_search.filter_invoice_date_from) : ''}
                         onChange={(e)=>this.handleChange(e,'filter_invoice_date_from')}
                         dateFormat="yyyy-MM-dd"
                         className="form-control date_calendar_from"
                         placeholderText="From"
                         id="filter_invoice_date_from" 
                         name="filter_invoice_date_from"
                         customInput={<DateInput className="form-control date_calendar_from" placeholderText="From" />}
                       />
                      <DatePicker
                        selected={this.props.filter_search.filter_invoice_date_to ? new Date(this.props.filter_search.filter_invoice_date_to) : ''}
                         onChange={(e)=>this.handleChange(e,'filter_invoice_date_to')}
                         dateFormat="yyyy-MM-dd"
                         className="form-control date_calendar_to"
                         placeholderText="To"
                         id="filter_invoice_date_to" 
                         name="filter_invoice_date_to"
                         customInput={<DateInput className="form-control date_calendar_to" placeholderText="To" />}
                       />
                    </div>
                  </div>
                </form>
              </div>
              <div className="col-xs-12 no-padding">
                <div className="account_statement_btn_group">
                    <button className="clear_search"  onClick={this.props.clearSearch}>Clear All</button>
                    <button id="search_submit" onClick={this.props.getStatementList}>Search</button>
                </div>
              </div>
        </section>
		)
	}
}

export default (Search);