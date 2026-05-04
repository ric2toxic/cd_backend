class DetailSection extends React.Component {
	
	constructor(props){
		super(props);		
		this.state = {
         language: this.props.language,
		}

	}

	render(){
    var self = this;
    if(this.props.ticket_detail)
    {
      return(<div className="right_section">

                <div className="row">
                    <div className="col-sm-12 my_account_heading">
                     <div className="col-sm-6 heading_title">
                      <i style={{cursor: 'pointer'}} onClick={()=>this.props.getTicketData(this.props.ticket_status)} className="fa fa-arrow-left" aria-hidden="true"></i> &nbsp;
                       #{this.props.ticket_detail.ticket_id} {this.props.ticket_detail.subject}
                    </div>
                    <div className="col-sm-6 heading_title">
                      {this.props.ticket_detail.status != 'CLOSED' ?
                       <a style={{cursor: 'pointer', width:'120px'}} onClick={()=>this.props.closeTicket(this.props.ticket_detail.ticket_id)} className="btn btn-continue pull-right close_ticket_button">Close Ticket </a>
                       : ''}
                     </div>
                  </div>
                </div>


                  <div className="row">
                        <div className="col-sm-6 name_circle"> 
                           <a href="javascript:;"><div className="text_name">
                          {this.props.ticket_detail.ticket_creater.toUpperCase().charAt(0)}
                           </div></a>
                          <b> &nbsp;{this.props.ticket_detail.ticket_creater}</b> {this.props.ticket_detail.report}                          
                         </div>  
                        <div className="clearfix"></div>  
                        <div className="helpdesk_reply">
                          <p dangerouslySetInnerHTML={{ __html: this.props.ticket_detail.description }} />
                          
                          {this.props.ticket_detail.attachment ?
                            this.props.ticket_detail.attachment.map(function(attachment, index) {
                             
                             return(
                                 <div className="helpdesk_attachment">
                                    <div className="helpdesk_attachment_type">
                                      <i className="fa fa-file-o" style={{fontSize:'36px'}}></i>
                                       {attachment.ext ?
                                       <span className="helpdesk_file_type">{attachment.ext}</span>
                                       : ''
                                       }
                                    </div>

                                    <div className="helpdesk_attach_content">
                                        <div> 
                                         <a href={attachment.path} className="filename" download="">
                                         {attachment.attachment_name}
                                         </a> 
                                        </div>
                                        <div>({attachment.size})</div>
                                    </div>
                                 </div>  
                              )

                            })
                          
                            :''}
                        </div> 

                   </div>

                    <hr /> 

                    <div className= "conversation_reload">
                     {this.props.ticket_detail.conversation ?
                       this.props.ticket_detail.conversation.map(function(conversation, index) {
                        
                        return(
                              <div className="row"> 

                                 {conversation.agent_name ?                                       
                                  <div className="col-sm-12 agent_msg name_circle">
                                    
                                    <div className="text_full_name">
                                     {conversation.time}
                                       &nbsp; 
                                     <b>{conversation.agent_name}</b>
                                    </div> 
                                    <div className="text_name">
                                      {conversation.agent_name.toUpperCase().charAt(0)}
                                   </div>
                                 </div>
                                 :
                                  <div className="col-sm-12 name_circle">
                                    <div className="text_name">
                                      {conversation.customer_name.toUpperCase().charAt(0)}
                                   </div>
                                    <div className="text_full_name">
                                     <b>{conversation.customer_name}</b>
                                      &nbsp;
                                     {conversation.time}
                                    </div> 
                                   </div>
                                  }

                                 <div className="clearfix"></div>
                                 <div className={conversation.agent_name ? "helpdesk_reply agent" : "helpdesk_reply" }>  
                                  <p dangerouslySetInnerHTML={{ __html: conversation.body }} />

                                   {conversation.attachment ?
                                     conversation.attachment.map(function(attachment, index) {
                                    
                                    return(
                                          <div className="helpdesk_attachment">
                                          <div className="helpdesk_attachment_type">
                                          <i className="fa fa-file-o" style={{fontSize:'36px'}}></i>
                                           {attachment.ext ?
                                           <span className="helpdesk_file_type">{attachment.ext}</span>
                                           : ''
                                            }
                                          </div>

                                          <div className="helpdesk_attach_content">
                                           <div> 
                                            <a href={attachment.path} className="filename" download="">
                                            {attachment.attachment_name}
                                           </a> 
                                          </div>
                                          <div>({attachment.size})</div>
                                         </div>
                                        </div>  
                                      )
                                    })
                                   :''}

                                 </div>  

                              </div>   
                              )

                        })
                      : ''} 
                    </div>

                    <hr style={{marginTop:'22px'}} />

                    {this.props.ticket_detail.conversation_pagination && this.props.ticket_detail.conversation_pagination.next_page && this.props.ticket_detail.conversation_pagination.next_page != '' ?
                     <div style={{textAlign:'center', float:'left', width:'100%'}}>
                     <button type="button" className="btn btn-link" onClick={()=>this.props.getTicketDetail(this.props.ticket_id, this.props.ticket_detail.conversation_pagination.next_page)}>
                      Load More...</button>
                     </div>
                    : ''}
                    
                     
 
                    <div className="row" id="reply_div"  >
                            <div className="col-sm-6 name_circle" > 
                                <a href="javascript:;"><div className="text_name">
                                  {this.props.ticket_detail.ticket_creater.toUpperCase().charAt(0)}
                                </div> </a>
                                <b> {this.props.ticket_detail.ticket_creater}</b>
                            </div>  
                            <div className="clearfix"></div>  
                            <div className="helpdesk_reply">
                             <form method="post" enctype="multipart/form-data" className="form-horizontal" id="reply_form"> 
                                   
                                  <div className="form-group " >
                                      <textarea className="form-control" name="body" rows="3"  id="reply_body"></textarea>
                                      <input type="hidden" name="ticket_id" id="reply_ticket_id" value={this.props.ticket_detail.ticket_id} />         
                                  </div>  
                                                 
                                  <div className="form-group ">
                                     <input id="reply_attachment" name ="attachment" type="file" className="file" />
                                  </div>  

                                  <div className="form-group ">
                                    <button type="button" id= "reply_form_submit" className="btn btn-continue pull-right" onClick={this.props.conversation_reply} style={{width:'70px'}}> Submit </button>
                                  </div>  
                              </form>                                                                                                                

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