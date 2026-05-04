class RightSection extends React.Component {
	
	constructor(props){
		super(props);		
		this.state = {
         language: this.props.language,
		}

	}

	render(){
    var self = this;
    if(!this.props.loading)
    {
      return(<div className="right_section">
                <div className="row">
                    <div className="col-sm-12 my_account_heading">
                     <div className="col-sm-11 heading_title">
                     Saved Bank Details
                    </div>
                    <div onClick={this.props.bankPopup} className="col-sm-1" style={{textAlign:'center', color:'#233c98', fontWeight:'bold', cursor:'pointer', 'border': '1px solid #ccc', padding: '8px 0px'}}><i className="fa fa-pencil" aria-hidden="true"></i> Edit </div>
                    </div>
                </div>

                <div className="row bank_detail">
                    <div className="col-sm-4">
                      <div className="title">ACCOUNT HOLDER NAME</div>
                      <div className="detail">{this.props.bank_data.bank_ac_holder_name}</div>
                    </div>
                    <div className="col-sm-4">
                       <div className="title">ACCOUNT NUMBER</div>
                       <div className="detail">{this.props.bank_data.bank_ac_number}</div>
                    </div>
                    <div className="col-sm-4">
                       <div className="title">IFSC CODE</div>
                       <div className="detail">{this.props.bank_data.ifsc_code}</div>
                    </div>
                </div>
 
                 <div className="row" style={{marginTop:'20px'}}>
                    <div className="col-sm-12 my_account_heading">
                     <div className="col-sm-11 heading_title">
                     UPI Details
                    </div>
                    </div>
                </div>

                <div className="row bank_detail">
                    <div className="col-sm-8">
                      <div className="title">UPI Id</div>
                      <div className="detail">{this.props.bank_data.customer_vpa}</div>
                    </div>
                </div>

              </div>)
    }
    else
    {
     return(<div className="right_section">
                 <OrderListLoadingRight />
            </div>
            ) 
    }
	}
}