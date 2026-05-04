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
                     <div className="col-sm-10 heading_title">Manage Address</div>
                     <div onClick={this.props.addressPopup} className="col-sm-2" style={{textAlign:'center', color:'#233c98', fontWeight:'bold', cursor:'pointer', 'border': '1px solid #ccc', padding: '8px 0px'}}><i className="fa fa-map-marker" aria-hidden="true"></i> Add New Address</div>
                  </div>

              {this.props.address_data ? 
               this.props.address_data.addresses.map(function(address, index) {
                return(<div className="col-sm-4" id={"address_box_"+address.address_id}>
                <div className="address_card">
                    <div className="default_heading">
                     {address.address_id == self.props.address_data.customer_address_id ?
                     <span  id={"defaultbox_"+address.address_id} onClick={()=>self.props.edit_address_popup_open(address, self.props.address_data.customer_address_id, true)}>Default</span>
                      :
                      <span id={"defaultbox_"+address.address_id} onClick={()=>self.props.edit_address_popup_open(address, self.props.address_data.customer_address_id, true)}>Set As Default</span> 
                     }
                    </div>
                     
                     <div className="delete_icon">
                      <a href="javascript:;" onClick={()=>self.props.deleteAddress(address.address_id)}><i className="fa fa-trash" aria-hidden="true"></i></a>
                     </div>

                     <div className="address_marker">
                       <span>{address.firstname} {address.lastname}</span>
                     </div>

                     <div className="shop_address">
                        <p>
                         
                         {address.company} <br />
                         {address.address_1}  {address.address_2}<br />
                          {address.city}, {address.postcode} <br />
                          {address.zone} <br /> {address.country} 
                        </p>            
                      </div>

                      <div className="address_edit_btn col-sm-12">
                       <button onClick={()=>self.props.edit_address_popup_open(address, self.props.address_data.customer_address_id)}><i className="fa fa-pencil" aria-hidden="true"></i> Edit</button>
                      </div>
                </div>
                </div>) }) : ''}

            </div>
            </div>
            )
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