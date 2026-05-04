class DetailSection extends React.Component {
	
	constructor(props){
		super(props);		
		this.state = {
         language: this.props.language,
		}

	}

	render(){
    var self = this;
    if(this.props.return_detail)
    {
      return(<div className="right_section">
                
              <div className="row">        
               <div  className="col-sm-12 col-xs-12 nopadding">            
                <div className="col-sm-12">
                 <div className="top_headline_return_detail">
                 <div className="return_detail_segments">
                  <div className="col-sm-4">
                  <i style={{cursor: 'pointer'}} onClick={this.props.getReturnsData} className="fa fa-arrow-left" aria-hidden="true"></i> &nbsp; 
                  Order Number :  {this.props.return_detail.order_no}</div>
                  <div className="col-sm-5">Return Number : {this.props.return_detail.return_no}</div>
                 
                  <div className="col-sm-3">
                   {this.props.return_detail.total_return_pieces > 0 ?
                   <span>Total Return :  {this.props.return_detail.total_return_pieces}</span>
                  : ''}
                   <br />
                  {this.props.return_detail.total_net_refundable > 0 ?
                   <span>Refund Amount :  {this.props.return_detail.total_net_refundable}</span>
                  : ''}
                  </div>
                </div>
             </div>

             

            <div className="col-sm-12 nopadding return_address_detail">
            
             <div className="col-sm-3"> 
             <div className="bank_details">
                <h5>Bank Details : </h5>
                 <p>
                  <b>Account holder name :</b>   {this.props.bank_detail.bank_ac_holder_name}<br />
                  <b>Account Number :</b>   {this.props.bank_detail.bank_ac_number}<br />
                  <b>Ifsc Code :</b>   {this.props.bank_detail.ifsc_code}
                 </p>
                 {this.props.bank_detail.bank_ac_number ?
                  <a href={this.props.bank_detail.bank_url} target="_blank" className="btn btn-primary">Update Bank Info</a>
                  :
                  <a href={this.props.bank_detail.bank_url} target="_blank" className="btn btn-primary">Add Bank Info</a> 
                 }

             </div>
             </div>

             <div className="col-sm-3"> 
             {this.props.return_detail.pickup_address ?
             <div className="pick_up_details">
               <h5>Pick Up Details :</h5>
               {this.props.return_detail.pickup_address.pickup_type != 'Self Shipment' ?
               <p><b>Pickup Type :</b> <br /> {this.props.return_detail.pickup_address.pickup_type} <br />
               <b>Pick Up Address :</b> <br /> 
               {this.props.return_detail.pickup_address.shipping_name} <br /> 
               {this.props.return_detail.pickup_address.shipping_company} <br /> 
               
               {this.props.return_detail.pickup_address.shipping_address_1}
               {this.props.return_detail.pickup_address.shipping_address_2} <br />

               {this.props.return_detail.pickup_address.shipping_city}
               {this.props.return_detail.pickup_address.shipping_zone != '' ?
                 ', '+this.props.return_detail.pickup_address.shipping_zone
                : ''}
               <br /> 

               {this.props.return_detail.pickup_address.shipping_postcode}

               {this.props.return_detail.pickup_address.shipping_country != '' ?
                 ', '+this.props.return_detail.pickup_address.shipping_country
                : ''}

                {this.props.return_detail.show_upload_courier_btn ? 
                 <button onClick={this.props.uploadCourierDetails} type="button" className="courier_detail_returns">Upload Courier Detail</button>
                  : ''}
               </p>

               :
                <p>
                 <b>Pickup Type :</b> <br /> {this.props.return_detail.pickup_address.pickup_type} <br />
                 <b>Pick Up Address :</b> <br /> 
                 {this.props.return_detail.pickup_address.warehouse_name} <br /> 
                 {this.props.return_detail.pickup_address.address_1} <br /> 
                 {this.props.return_detail.pickup_address.address_2} <br /> 
                 {this.props.return_detail.pickup_address.city}, {this.props.return_detail.pickup_address.postcode} <br /> 
                 {this.props.return_detail.show_upload_courier_btn ? 
                 <button onClick={this.props.uploadCourierDetails} type="button" className="courier_detail_returns">Upload Courier Detail</button>
                  : ''}
                </p>
               }

             </div>
             :
              <div className="pick_up_details">
               <h5>Pick Up Details :</h5>
                <p>No data found</p>
              </div> 
             }
             </div>

             <div className="col-sm-3"> 
             <div className="credit_note_details">
               <h5>Credit Note : </h5>
               <table width="100%" border="1" className="credit_table">
                <tr>
                  <th>Number</th>
                  <th>Amount</th>
                </tr>

               {this.props.return_detail.credit_note_data ? 
                 this.props.return_detail.credit_note_data.map(function(credit_note, index) {
                 return( <tr><td>
                              <a href={credit_note.download_link}><i className="fa fa-download"></i></a> &nbsp; 
                             {credit_note.credit_note_id}</td>
                             <td>{credit_note.credit_note_amount}</td>
                               </tr>) }) : ''}

               </table>

             </div></div>

             <div className="col-sm-3"> 
             <div className="tracking_details">
               <h5>Tracking Details :</h5>
               <p><b>Tracking Number :</b>  {this.props.return_detail.tracking_no}</p>
             </div></div>

           </div>


             <div className="return_product_table">
               <table className="table table-striped">
                  <thead>
                    <tr>
                      <th scope="col">Product</th>
                      <th scope="col">Total Pieces</th>
                      <th scope="col">Price/ Pieces</th>
                      <th scope="col">Reason For Return</th>
                      <th scope="col">Pieces of Return</th>
                      <th scope="col">Status Of Return</th>
                      <th scope="col">Comment</th>
                    </tr>
                  </thead>
                  <tbody>

                  {this.props.return_detail.products ? 
                    Object.keys(this.props.return_detail.products).map(function(key) {
                    return(<tr>
                      <td>
                        <img src={self.props.return_detail.products[key].thumb} />
                        <p>{self.props.return_detail.products[key].name}</p>
                      </td>
                      <td>{self.props.return_detail.products[key].total_pieces}</td>
                      <td>{self.props.return_detail.products[key].price_per_piece}</td>
                      <td>{self.props.return_detail.products[key].return_reason}</td>
                      <td>{self.props.return_detail.products[key].return_quantity}</td>
                      <td>{self.props.return_detail.products[key].return_status}</td>
                      <td>{self.props.return_detail.products[key].comment}</td>
                     </tr>)
                     }) : ''}
                  </tbody>
                </table>
             </div>
           </div>           
         
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