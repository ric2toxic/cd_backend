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
                      Support
                    </div>
                     <div className="col-sm-9">
                       <div className="create_text pull-right">
                          <a style={{cursor:'pointer'}} onClick={this.props.ticketPopup}> <i className="fa fa-plus"></i> Create Ticket</a>
                        </div>
                    </div>
                  </div>
                </div>
              


         <div className="row">
          <div className ="col-sm-12">
          <ul className="nav nav-tabs support">
          <li className={this.props.ticket_status == "" ? "active" : "" }><a data-toggle="tab" href="#menu1" onClick={()=>this.props.getTicketData('')}>All Tickets</a></li>
          <li className={this.props.ticket_status == "OPEN" ? "active" : "" }><a data-toggle="tab" href="#menu2" onClick={()=>this.props.getTicketData('OPEN')}>Opened</a></li>
          <li className={this.props.ticket_status == "CLOSED" ? "active" : "" }><a data-toggle="tab" href="#menu3" onClick={()=>this.props.getTicketData('CLOSED')}>Closed</a></li>
         </ul>
         </div>
         </div>

 
    <div className="tab-content">
      <div id="menu1" className="tab-pane fade in active">
          
          {this.props.ticket_data && this.props.ticket_data.length > 0 ? 
                 this.props.ticket_data.map(function(ticket, index) {
          
          return(<div className="col-sm-12 ticket_tile" style={{cursor: 'pointer'}} onClick={()=>self.props.getTicketDetail(ticket.ticket_id)}>  
            <div className="col-sm-1">
               <div className="name_circle">
               <a href="javascript:;"><div className="text_name">{ticket.ticket_creater.toUpperCase().charAt(0)}</div></a>
               </div>
            </div>
            <div className="col-sm-7">
               <div className="return_number_assigned_to">
               <p className="return_number"> {ticket.subject}#{ticket.ticket_id}  </p>
               <p>Assigned to : {ticket.assigned_to_agent}  
               {ticket.unread_reply != '0' ?
               <span>New message ({ticket.unread_reply})</span>
               : ''}
               </p>
               </div>
            </div>
            <div className="col-sm-4">
               <div className="status_of_return  pull-right">
               <div className={ticket.status == 'OPEN' ? "opened_ticket" : "closed_ticket"}><p>{ticket.status}</p></div>
               <p className="ticket_created_date">{ticket.created_at}</p>
              </div>
            </div>
         </div>) }) :
          <div className="col-sm-12 ticket_tile">
            No data found !
          </div> }  

          {this.props.pagination.nextpage && this.props.pagination.nextpage != '' ?
            <div style={{textAlign:'center', marginTop:'18px', float:'left', width:'100%'}}>
              <button type="button" className="btn btn-link" onClick={()=>this.props.getTicketData(this.props.ticket_status, this.props.pagination.nextpage)}>
              Load More...</button>
             </div>
          : ''}

    </div>

    <div id="menu2" className="tab-pane fade">

      {this.props.ticket_data && this.props.ticket_data.length > 0 ? 
                 this.props.ticket_data.map(function(ticket, index) {
          
          return(<div className="col-sm-12 ticket_tile" style={{cursor: 'pointer'}} onClick={()=>self.props.getTicketDetail(ticket.ticket_id)}>  
            <div className="col-sm-1">
               <div className="name_circle">
               <a href="javascript:;"><div className="text_name">{ticket.ticket_creater.charAt(0)}</div></a>
               </div>
            </div>
            <div className="col-sm-7">
               <div className="return_number_assigned_to">
               <p className="return_number"> {ticket.subject}#{ticket.ticket_id}  </p>
               <p>Assigned to : {ticket.assigned_to_agent}  
               {ticket.unread_reply != '0' ?
               <span>New message ({ticket.unread_reply})</span>
               : ''}
               </p>
               </div>
            </div>
            <div className="col-sm-4">
               <div className="status_of_return  pull-right">
               <div className={ticket.status == 'OPEN' ? "opened_ticket" : "closed_ticket"}><p>{ticket.status}</p></div>
               <p className="ticket_created_date">{ticket.created_at}</p>
              </div>
            </div>
         </div>) }) :
          <div className="col-sm-12 ticket_tile">
            No data found !
          </div> }

          {this.props.pagination.nextpage && this.props.pagination.nextpage != '' ?
            <div style={{textAlign:'center', marginTop:'18px', float:'left', width:'100%'}}>
              <button type="button" className="btn btn-link" onClick={()=>this.props.getTicketData(this.props.ticket_status, this.props.pagination.nextpage)}>
              Load More...</button>
             </div>
          : ''} 

    </div>


    <div id="menu3" className="tab-pane fade">
      
      {this.props.ticket_data && this.props.ticket_data.length > 0 ? 
                 this.props.ticket_data.map(function(ticket, index) {
          
          return(<div className="col-sm-12 ticket_tile" style={{cursor: 'pointer'}} onClick={()=>self.props.getTicketDetail(ticket.ticket_id)}>  
            <div className="col-sm-1">
               <div className="name_circle">
               <a href="javascript:;"><div className="text_name">{ticket.ticket_creater.charAt(0)}</div></a>
               </div>
            </div>
            <div className="col-sm-7">
               <div className="return_number_assigned_to">
               <p className="return_number"> {ticket.subject}#{ticket.ticket_id}  </p>
               <p>Assigned to : {ticket.assigned_to_agent}  
               {ticket.unread_reply != '0' ?
               <span>New message ({ticket.unread_reply})</span>
               : ''}
               </p>
               </div>
            </div>
            <div className="col-sm-4">
               <div className="status_of_return  pull-right">
               <div className={ticket.status == 'OPEN' ? "opened_ticket" : "closed_ticket"}><p>{ticket.status}</p></div>
               <p className="ticket_created_date">{ticket.created_at}</p>
              </div>
            </div>
         </div>) }) :
          <div className="col-sm-12 ticket_tile">
            No data found !
          </div> } 

          {this.props.pagination.nextpage && this.props.pagination.nextpage != '' ?
            <div style={{textAlign:'center', marginTop:'18px', float:'left', width:'100%'}}>
              <button type="button" className="btn btn-link" onClick={()=>this.props.getTicketData(this.props.ticket_status, this.props.pagination.nextpage)}>
              Load More...</button>
             </div>
          : ''}

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