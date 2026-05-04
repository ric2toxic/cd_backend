class SearchBoxComponent extends React.Component {
	
	constructor(props) {
		super(props);
		this.state = {
			language: this.props.language,
			invOrOrdNoValue : '',
      fiscalYr:[]
		}
    this.getCurrentFiscalYear = this.getCurrentFiscalYear.bind(this);
	}

	componentDidMount()
  {
		var self = this;
		$('input, select').on('keypress',function(e){
			$(this).focus();
			if(e.keyCode==13){
				$('#search_submit').trigger('click');
			}
		});

		$('.date_calendar').datepicker({
         dateFormat:"yy-mm-dd",
         minDate: new Date(2018, 1 - 1, 1),
         changeMonth:true,
         changeYear:true,
         onSelect: function(date, datepicker) {
                     var field = datepicker.input.context.name;
                     self.props.searchChangeDateValue(field, date);
                    }
        }); 

    $('#filter_payment_method').multiselect({
       nonSelectedText: 'Search by Payment Method',
       numberDisplayed: 1,
       onChange: function(element, checked) {
        self.props.searchChangeValue("filter_payment_method", element);
       }
     });
   
   if(this.props.filter_search && this.props.filter_search.filter_payment_method)
   {

    var data = this.props.filter_search.filter_payment_method;
    var valArr = data.split(",");
    var i = 0, size = valArr.length;
    for (i; i < size; i++) 
    {
      $('#filter_payment_method').multiselect('select', valArr[i]);
    }
  }

    var fiscalYr = this.getCurrentFiscalYear();
    this.setState({ fiscalYr });

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

      for (i = beginning; i <= end; i++) {
         var nextYr1 = i+1;
          fiscalYr[i] = (i + "-" + nextYr1);
      }
       return fiscalYr;
   }

	render(){
    let self = this;
		return (
		<section>	
   	 	<div className="row">
            <div className="col-sm-12 col-xs-12 col-md-12 col-lg-12 nopadding" style={{marginTop:'10px'}}>
              <div className="account_statement_filters">
              <div  className="col-sm-4 col-xs-12">
                <select id="filter_payment_method" className="custom-select select__field" name="filter_payment_method[]" onChange={this.props.searchChangeValue.bind(this, "filter_payment_method")} multiple="multiple">
                  {this.props.payment_methods ?
                     $.map(this.props.payment_methods, function(payment_method, index) 
                      {
                           return(<option value={index}>{payment_method}</option>)
                      
                       })
                    :''}
                </select>

              </div>
              <div  className="col-sm-4 col-xs-12 select">
                <select className="custom-select select__field" name="filter_quarterly" onChange={this.props.searchChangeValue.bind(this, "filter_quarterly")}>
                  <option value="">Search by Quarter </option>

                  {this.props.filter_search.filter_quarterly && this.props.filter_search.filter_quarterly == 'Q1' ?
                  <option value="Q1" selected> April - June </option>
                  : 
                  <option value="Q1"> April - June</option>
                  }

                  {this.props.filter_search.filter_quarterly && this.props.filter_search.filter_quarterly == 'Q2' ?
                  <option value="Q2" selected>July - September</option>
                  : 
                  <option value="Q2"> July - September</option>
                  }

                  {this.props.filter_search.filter_quarterly && this.props.filter_search.filter_quarterly == 'Q3' ?
                  <option value="Q3" selected> October - December </option>
                  : 
                  <option value="Q3"> October - December</option>
                  }

                  {this.props.filter_search.filter_quarterly && this.props.filter_search.filter_quarterly == 'Q4' ?
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
                          if(self.props.filter_search.filter_financial_year && self.props.filter_search.filter_financial_year == index)
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
            </div>            
          </div>
          
        </div>

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
            <div className="col-sm-3 col-xs-12 col-md-3 col-lg-3">
              <div className="input-group calender">
                <div className="input-group-prepend">
                  <span className="input-group-text" id="">Order Date</span>
                </div>
                <input type="text" className="form-control date_calendar" id="filter_ordered_date_from" name="filter_ordered_date_from" placeholder="From" onChange={this.props.searchChangeValue.bind(this, "filter_ordered_date_from")} value={this.props.filter_search.filter_ordered_date_from ? this.props.filter_search.filter_ordered_date_from : ''} />
                <span className="input-group-addon_1" onClick={()=> $( "#filter_ordered_date_from" ).datepicker("show")}>
                        <span className="blue_color_theme"><i className="fa fa-calendar"></i></span>
                    </span>
                <input type="text" className="form-control date_calendar" id="filter_ordered_date_to" name="filter_ordered_date_to" onChange={this.props.searchChangeValue.bind(this, "filter_ordered_date_to")} placeholder="To" value={this.props.filter_search.filter_ordered_date_to ? this.props.filter_search.filter_ordered_date_to : ''} />
                <span className="input-group-addon_2" onClick={()=> $( "#filter_ordered_date_to" ).datepicker("show")}>
                        <span className="blue_color_theme"><i className="fa fa-calendar"></i></span>
                    </span>
              </div>
            </div>
            <div className="col-sm-3 col-xs-12 col-md-3 col-lg-3">
              <div className="input-group calender id='datetimepicker1'">
                <div className="input-group-prepend">
                  <span className="input-group-text" id="">Invoice Date</span>
                </div>
                 <input type="text" className="form-control date_calendar" id="filter_invoice_date_from" name="filter_invoice_date_from" onChange={this.props.searchChangeValue.bind(this, "filter_invoice_date_from")} placeholder="From" value={this.props.filter_search.filter_invoice_date_from ? this.props.filter_search.filter_invoice_date_from : ''} />
                <span className="input-group-addon_1" onClick={()=> $( "#filter_invoice_date_from" ).datepicker("show")}>
                        <span className="blue_color_theme"><i className="fa fa-calendar"></i></span>
                    </span>
               <input type="text" className="form-control date_calendar" id="filter_invoice_date_to" name="filter_invoice_date_to" onChange={this.props.searchChangeValue.bind(this, "filter_invoice_date_to")} placeholder="To" value={this.props.filter_search.filter_invoice_date_to ? this.props.filter_search.filter_invoice_date_to : ''} />
                <span className="input-group-addon_2" onClick={()=> $( "#filter_invoice_date_to" ).datepicker("show")}>
                        <span className="blue_color_theme"><i className="fa fa-calendar"></i></span>
                    </span>
              </div>
            </div>
          </form>
        </div>
        <div className="col-sm-12 no-padding" style={{marginTop: '10px'}}>
        
             
             <div className="col-sm-4 col-xs-12 col-md-4 col-lg-4 nopadding">
              <button style={{width:'100px'}} className="clear_all_btn" onClick={this.props.clearSearch}>Clear All</button>
             </div>

              <div className="col-sm-4 col-xs-12 col-md-4 col-lg-4" style={{textAlign:'center'}}>
                <button className="report_download" style={{cursor: 'pointer'}} onClick={()=>this.props.getStatementList('download')}>
                 <span><i className="fa fa-download"></i></span> Download Report
                </button>
              </div>
              
               <div className="col-sm-4 col-xs-12 col-md-4 col-lg-4 nopadding">  
                <button style={{width:'100px'}} className="pull-right" id="search_submit" onClick={this.props.getStatementList}>Search</button>
               </div>
          
        </div>
        </section>
		)
	}
}
