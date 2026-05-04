import React, { Component } from 'react'
import Dialog from '@material-ui/core/Dialog';
import DialogContent from '@material-ui/core/DialogContent';
import DialogTitle from '@material-ui/core/DialogTitle';
import DialogActions from '@material-ui/core/DialogActions';
import withMobileDialog from '@material-ui/core/withMobileDialog';
import  MultiSelectReact  from 'multi-select-react';
import DatePicker from "react-datepicker";
import "react-datepicker/dist/react-datepicker.css";
import DateInput from "./date_input"

import $ from 'jquery'

import  './index.css'

class FilterPopup extends Component {

 constructor(props) 
    {
        super(props);
        this.state = {
            multiSelect: this.props.payment_methods ? this.props.payment_methods : [],
            filter_ordered_date_from: this.props.filter_search.filter_ordered_date_from ? new Date(this.props.filter_search.filter_ordered_date_from) : new Date(),
            filter_ordered_date_to: this.props.filter_search.filter_ordered_date_to ? new Date(this.props.filter_search.filter_ordered_date_to) : new Date(),
            filter_invoice_date_from:this.props.filter_search.filter_invoice_date_from ? new Date(this.props.filter_search.filter_invoice_date_from) : new Date(),
            filter_invoice_date_to:this.props.filter_search.filter_invoice_date_to ? new Date(this.props.filter_search.filter_invoice_date_to) : new Date(),
            fiscalYr:[]         
        };

        this.handleChange = this.handleChange.bind(this);
        this.clearSearch  = this.clearSearch.bind(this);
    }

   componentWillMount()
  {
    var filter_search = this.props.filter_search;
    var multiSelect = this.state.multiSelect;
    
      $.map(multiSelect, function(option, index) 
          {
            if(filter_search.filter_payment_method && filter_search.filter_payment_method.search(option.key) >= 0)
              {
                multiSelect[index]['value'] = true;
              }
          }); 
      this.setState({ multiSelect });

    var self = this;
    $('input, select').on('keypress',function(e){
      if(e.keyCode===13){
        self.props.getStatementList();
      }
    });

    var fiscalYr = this.getCurrentFiscalYear();
    this.setState({ fiscalYr });

  }

  optionClicked(optionsList) {
        this.setState({ multiSelect: optionsList });
        this.props.searchChangeValue("filter_payment_method", optionsList);
  }
  selectedBadgeClicked(optionsList) {
        this.setState({ multiSelect: optionsList });
        this.props.searchChangeValue("filter_payment_method", optionsList);
  }

  handleChange(date, field) 
  { 
    var month     = date.getMonth()+1;
    var day       = date.getDate();
    var year      = date.getFullYear();
    date = year+'-'+month+'-'+day;
    this.props.searchChangeDateValue(field, date);
  }

  clearSearch()
  {
    var multiSelect = [{'key':'cod','label':'Cash on Delivery','value':false},
                      {'key':'prepaid', 'label':'Prepaid','value':false},
                      {'key':'wsb_credit', 'label':'WholesaleBox Credit (Pay Later Scheme)','value':false},
                      {'key':'other_credit', 'label':'Third party Credit','value':false}];
    this.setState({ multiSelect });
    this.props.searchChangeValue("filter_payment_method", multiSelect);                  
    this.props.clearSearch();
  }

  getCurrentFiscalYear(beginning=2018) 
   {
       //get current date
       var today = new Date();

       var current_year = today.getFullYear();
       //get current month
       var curMonth = today.getMonth();

        if (curMonth <= 3) {
            current_year -= 1;
        }

       var end = current_year;

     
      var fiscalYr = [];

      for (var i = beginning; i <= end; i++) {
         var nextYr1 = i+1;
          fiscalYr[i] = (i + "-" + nextYr1);
      }
       return fiscalYr;
   }

	render() {
  let self = this;
		return (
          <Dialog
             fullScreen={true}
             open={this.props.filterPopupOpen}
             aria-labelledby="responsive-dialog-title"
             className="login_register_popup"
            >
            <button type="button" className="popup_close close" onClick={this.props.filter_popup_close}>&times;</button>
            <DialogTitle className="address_popup_head popup-title">
              Filter
            </DialogTitle>
       
            <DialogContent className="modal-body">
                     <div className="row">
                        <div className="col-sm-12 col-xs-12 col-md-8 col-lg-12">
                          <div className="account_statement_filters">


                          <div  className="col-sm-4 col-xs-12">

                            <MultiSelectReact 
                                 options={this.state.multiSelect}
                                 optionClicked={this.optionClicked.bind(this)}
                                 selectedBadgeClicked={this.selectedBadgeClicked.bind(this)}
                                 className="custom-select select__field" 
                                 name="filter_payment_method"
                              />
                          </div>
                          <div  className="col-sm-4 col-xs-12 select">
                            <select className="custom-select select__field" name="filter_quarterly" onChange={this.props.searchChangeValue.bind(this, "filter_quarterly")}>
                              <option value="">Search by Quarter </option>

                              {this.props.filter_search.filter_quarterly && this.props.filter_search.filter_quarterly === 'Q1' ?
                              <option value="Q1" selected> April - June </option>
                              : 
                              <option value="Q1"> April - June</option>
                              }

                              {this.props.filter_search.filter_quarterly && this.props.filter_search.filter_quarterly === 'Q2' ?
                              <option value="Q2" selected>July - September</option>
                              : 
                              <option value="Q2"> July - September</option>
                              }

                              {this.props.filter_search.filter_quarterly && this.props.filter_search.filter_quarterly === 'Q3' ?
                              <option value="Q3" selected> October - December </option>
                              : 
                              <option value="Q3"> October - December</option>
                              }

                              {this.props.filter_search.filter_quarterly && this.props.filter_search.filter_quarterly === 'Q4' ?
                              <option value="Q4" selected> January - March</option>
                              : 
                              <option value="Q4"> January - March </option>
                              }
                            </select>
                          </div>
                           <div  className="col-sm-4 col-xs-12 select">
                            <select className="custom-select select__field" name="filter_financial_year" onChange={this.props.searchChangeValue.bind(this, "filter_financial_year")}>
                              <option value="">Search by Financial Year </option>

                              {this.state.fiscalYr ?
                                $.map(this.state.fiscalYr, function(fiscalYr, index) 
                                 {
                                  if(index >= 2018)
                                  {
                                   if(self.props.filter_search.filter_financial_year && self.props.filter_search.filter_financial_year === index)
                                    {
                                     return(<option value={index} selected> {fiscalYr} </option>)
                                    }
                                   else
                                    {
                                      return(<option value={index}  > {fiscalYr} </option>)
                                    }
                                  }
                                })
                              :''}

                            </select>
                          </div>

                           <div className="col-sm-4 col-xs-12">
                            <div className="form-group">
                            <div className="input-group-prepend">
                            <span className="input-group-text" id="">Order No</span>
                          </div>
                           <input type="text" className="form-control" name="filter_order_no" onChange={this.props.searchChangeValue.bind(this, "filter_order_no")} placeholder="Order No" value={this.props.filter_search.filter_order_no ? this.props.filter_search.filter_order_no: ''} />
                          </div>
                          </div>

                          <div className="col-sm-4 col-xs-12">
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
                           minDate={new Date('2018-01-01')}
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
                        minDate={new Date('2018-01-01')}
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
                        minDate={new Date('2018-01-01')}
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
                        minDate={new Date('2018-01-01')}
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
  

                        </div>            
                      </div>
                    </div>

            </DialogContent>

            <DialogActions style={{margin:'0px'}}>
               <button type="button" className="btn filter_btn_popup_download pull-right" onClick={this.clearSearch}>Clear All</button>
               <button type="button" className="btn filter_btn_popup pull-right" onClick={this.props.getStatementList}>Apply</button>
            </DialogActions>

         </Dialog>
		)
	}
}


export default withMobileDialog()(FilterPopup);