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
                     <div className="col-sm-3 heading_title">
                      My Returns
                    </div>
                     <div className="col-sm-9">
                      <div className="search_section">
                         <div className="col-sm-10">
                         <div className="form-group">
                           <input type="text" className="form-control" name="return_search" placeholder="Search by Order No" value={this.props.order_no} onChange={this.props.handleChange.bind(this, "order_no")} /></div>
                         </div>
                         <div className="col-sm-2">
                           <button type="button" className="btn btn-primary" onClick={this.props.getReturnsData}>Search</button>
                         </div>
                         </div>
                     </div>
                    </div>
                </div>

              {this.props.returns_data ? 
                 this.props.returns_data.map(function(return_order, index) {
               
               return(<div className="row">
               <div className="col-sm-12 return_box">
                      <div className="col-sm-4">
                        Order Number
                       </div>
                       <div className="col-sm-4">
                         Return Number
                       </div>
                       <div className="col-sm-4">
                          Number of Returned Product
                        </div>
                         
                         <hr style={{width: '100%', borderTop:'1px solid #ccc',float: 'left'}} />

                      <div className="col-sm-4">
                        {return_order.order_no}
                       </div>
                       <div className="col-sm-4">
                        {return_order.return_no}
                       </div>
                       <div className="col-sm-4">
                          {return_order.total_pieces}
                        </div>

                        <div className="col-sm-12">
                         <br />
                         <button className="verify_btn pull-right" type="button" onClick={()=>self.props.getReturnsDetail(return_order.master_return_id)}>View Returned Products</button>
                        </div>                      

                    </div>
                </div>
                ) }) : 
                
                      <div className="row not_record_found">
                        <div className="col-sm-12 text-center">
                        <p><img src={cdn_url+"dekstopwithkurti.png"} width="40%" height="20%" /></p>
                        <h4>No Matching Orders Found!</h4>
                        </div>
                       </div>

               }
               

            {this.props.beyond_master_return_id != '' ?
            <div id="load_more" style={{textAlign:'center', marginTop:'18px', float:'left', width:'100%'}}>
              <a className="btn btn-link" onClick={this.props.getReturnsData}>
              Load More...</a>
             </div>
            : ''} 
         

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