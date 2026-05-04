class Support extends React.Component {
	
	constructor(props)
	{
		super(props);
		 this.state = {
		  fields: {},
          errors: {},
	      language: this.props.language,
	      international_store: this.props.international_store,
		    loading: true,
		    something_went_wrong: false,
		    logout:this.props.logout,
		    success_msg:false,
		    error_msg:false,
        data_count:false,
        pagination:false,
        ticket_type:false,
        ticket_data:false,
        ticket_detail:false,
        ticket_status:'',
        ticket_id:this.props.ticket_id
       	} 
    
     this.getTicketType      = this.getTicketType.bind(this);
     this.getTicketData      = this.getTicketData.bind(this);
     this.getTicketDetail    = this.getTicketDetail.bind(this);
     this.closeTicket        = this.closeTicket.bind(this);
     this.conversation_reply = this.conversation_reply.bind(this);
     this.ticketPopup        = this.ticketPopup.bind(this);
     this.addFilterInHistoryState = this.addFilterInHistoryState.bind(this);
     this.handleValidation   = this.handleValidation.bind(this);
     this.submitTicket       = this.submitTicket.bind(this);
        
	}

	componentDidMount()
	{
      this.getTicketType();
      this.getTicketData();  
      if(this.state.ticket_id)
       {
       	 this.getTicketDetail(this.state.ticket_id);
       } 
	}

   getTicketType()
  {  
      var self = this;
      var filters = '';
        filters = filters+'customer_id='+getCookie('customer_id');
        filters = filters+'&customer_access_token='+getCookie('customer_access_token');
        
      var encode =  window.btoa(filters); 

      axios({
      method:'GET',
      url:'./api/customer_account/support/getHelpdeskTicketType&data='+encode,
      dataType:'json'
    }) 
    .then(response => {

      if(response.data.statusCode == 900){
        window.location.href = self.state.logout;
        return false;
      }
        
      this.setState({ ticket_type: response.data.data});
    });  
  }


  getTicketData(status='', page=1)
  {  
  	  this.setState({ ticket_status: status});
  	  var self = this;
      var filters = '';
      filters = filters+'customer_id='+getCookie('customer_id');
      filters = filters+'&customer_access_token='+getCookie('customer_access_token');
      filters = filters+'&page='+page;  	
      if(status != '')
      {
        filters = filters+'&status='+status;
      } 
       
       console.log(filters);
      var encode =  window.btoa(filters); 

      this.addFilterInHistoryState('');
      this.setState({ticket_id:0});

      axios({
      method:'GET',
      url:'./api/customer_account/support/getTicketListData&data='+encode,
      dataType:'json'
    }) 
    .then(response => {

      if(response.data.statusCode == 900){
        window.location.href = self.state.logout;
        return false;
      }
      	
      let ticket_data = response.data.data.ticket_data;
         
      if(page > 1)
      {
        let old_ticket_data = this.state.ticket_data;
            ticket_data = [...old_ticket_data,...ticket_data];
      }

      this.setState({ ticket_data: ticket_data,
              data_count: response.data.data.data_count,
              pagination: response.data.data.pagination,
              loading:false,
                something_went_wrong:false });

    });  
  }


  getTicketDetail(ticket_id, page=1)
  {  
  	  this.addFilterInHistoryState(ticket_id);
  	  this.setState({ ticket_id: ticket_id});
  	  var self = this;
      var filters = '';
        filters = filters+'customer_id='+getCookie('customer_id');
        filters = filters+'&customer_access_token='+getCookie('customer_access_token');
        filters = filters+'&ticket_id='+ticket_id;
        filters = filters+'&page='+page; 
        var encode =  window.btoa(filters); 
     
      axios({
      method:'GET',
      url:'./api/customer_account/support/getTicketDetail&data='+encode,
      dataType:'json'
    }) 
    .then(response => {

      if(response.data.statusCode == 900){
        window.location.href = self.state.logout;
        return false;
      }
      
      let ticket_detail = response.data.data
      this.setState({ ticket_detail: response.data.data,
              loading:false,
                something_went_wrong:false });
    });  
  }

  closeTicket(ticket_id)
  {
     $(".close_ticket_button").html('<i class="fa fa-circle-o-notch fa-spin"></i>');
     var self = this;
     var filters = '';
     filters = filters+'customer_id='+getCookie('customer_id');
     filters = filters+'&customer_access_token='+getCookie('customer_access_token');
     filters = filters+'&ticket_id='+ticket_id;
     var encode =  window.btoa(filters); 

    axios({
      method:'GET',
      url:'./api/customer_account/support/closeTicket&data='+encode,
      dataType:'json'
    }) 
    .then(response => {

      if(response.data.statusCode == 900){
        window.location.href = self.state.logout;
        return false;
      }
      $(".close_ticket_button").html('Close Ticket');

      self.getTicketData();
        
    }); 
  }

  conversation_reply()
  {   
      var self = this;
      $("#reply_form_submit").html('<i class="fa fa-circle-o-notch fa-spin"></i>');

      var file_data = $('#reply_attachment').prop('files')[0];
      var ticket_id = $('#reply_ticket_id').val();
      var body      = $('#reply_body').val();
      if(body != '')
      {
        $('#reply_body').removeClass("error_border");
        var form_data = new FormData();
        form_data.append('attachment', file_data);
        form_data.append('ticket_id', ticket_id);
        form_data.append('body', body);
        form_data.append('customer_id', getCookie('customer_id'));
        form_data.append('customer_access_token', getCookie('customer_access_token'));

        $.ajax({
            type:'post',
            dataType:'json',
            data:form_data,
            url:"./api/customer_account/support/conversation_reply",
            mimeType: "form-data",
            contentType: false,
            processData: false,
            success: function(json) {
                $("#reply_form_submit").html('Submit'); 
                $('#reply_body').val('');
                $('#reply_attachment').val('');

                if(json.statusCode == 900){
                     window.location.href = self.state.logout;
                     return false;
                }

                if(json.statusCode == 200)
                {
                  self.getTicketDetail(ticket_id);   
                }
                else
                {
                  alert(json.data);
                }
            }
        });
        
      } 
      else
      {
        $("#reply_form_submit").html('Submit'); 
        $('#reply_body').addClass("error_border");
        return false;
      }
  }

  addFilterInHistoryState(ticket_id)
   {

  		let baseUrl = [location.protocol, '//', location.host, location.pathname].join('')+'?route=account/helpdesk';
  		
  		var filter_string 	= '';

  		if(ticket_id!='')
  		{
  			filter_string   = filter_string+"&ticket_id=" + ticket_id;
  		}

       	window.history.pushState('', null, baseUrl+filter_string);
   	}

  handleValidation()
    {
      var result = true;
      $(".ticket_required").each(function(i,e) {
          if($(e).val() == '')
          {
            $(e).addClass("error_border");
            result = false;
            
          }
          else
          {
            $(e).removeClass("error_border");
          }
        });
      return result;  
    } 
  
  submitTicket()
    {
      var self = this;
      if(this.handleValidation())
      {
        $("#support_submit_btn").html('<i class="fa fa-circle-o-notch fa-spin"></i>');
        var form_data      = new FormData();
        var file_data      = $('#attachment_file').prop('files')[0];
        var ticket_type_id = $('#ticket_form select[name=ticket_type_id]').val();
        var subject        = $('#ticket_form input[name=subject]').val();
        var description    = $('#ticket_form textarea[name=description]').val();
        
        form_data.append('customer_id', getCookie('customer_id'));
        form_data.append('customer_access_token', getCookie('customer_access_token'));
        form_data.append('attachment_file', file_data);
        form_data.append('ticket_type_id', ticket_type_id);
        form_data.append('subject', subject);
        form_data.append('description', description);

        $.ajax({
            type:'post',
            dataType:'json',
            data:form_data,
            url:"./api/customer_account/support/createTicket",
            async: false,
            mimeType: "form-data",
            contentType: false,
            processData: false,
            success: function(json) {

                $("#support_submit_btn").html('Continue');

                if(json.statusCode == 900){
                     window.location.href = self.state.logout;
                     return false;
                    }

                if(json.statusCode == 200)
                {
                   $(".ticket_error").hide();
                   $(".ticket_success").html(json.message);
                   $(".ticket_success").show();
                   setTimeout(function(){ $("#ticket_popup").modal("hide"); }, 1000);  
                   self.getTicketData(); 
                }
                else
                {
                  $(".ticket_success").hide();
                  $(".ticket_error").html(json.data);
                  $(".ticket_error").show();
                }
            }
        });
        
      } 
      else
      {
        alert("please fill the empty field");
        return false;
      }
    }


  ticketPopup()
  {
      $('#ticket_form select[name=ticket_type_id]').val(0);
      $('#ticket_form input[name=subject]').val('');
      $('#ticket_form textarea[name=description]').val('');
      $('#ticket_form input[name=attachment_file]').val('');
       $(".ticket_success").hide();
      $(".ticket_error").hide();
      $("#ticket_popup").modal("show");
  }  
  	
	render(){
    let self = this;
		return (
			     <div className="container-fluid width_fix">
			     	<div className="row">
						<section className="my_account">
						   <div className="order_list_block">
			             	<div className="col-sm-3 customer_account_section">
		     	          	<LeftSection 
		     	          	   active="support"
		     				        language={this.props.language} />
			             	</div>
			   		       
			   		        <div className="col-sm-9 my_account_page">
			   		        {this.state.ticket_id ?

			   		          <DetailSection 
		     				        language={this.props.language}
		     				        loading={this.state.loading}
                        ticket_data={this.state.ticket_data}
                        getTicketData={this.getTicketData}
                        ticket_status={this.state.ticket_status}
                        ticket_detail={this.state.ticket_detail}
                        closeTicket={this.closeTicket}
                        conversation_reply={this.conversation_reply}
                        getTicketDetail={this.getTicketDetail}
                        ticket_id={this.state.ticket_id} />	

			   		          : 	
			   		          <RightSection 
		     				        language={this.props.language}
		     				        loading={this.state.loading}
                        ticket_data={this.state.ticket_data}
                        getTicketData={this.getTicketData}
                        getTicketDetail={this.getTicketDetail}
                        ticket_status={this.state.ticket_status}
                        ticketPopup={this.ticketPopup}
                        data_count={this.state.data_count}
                        pagination={this.state.pagination} />
                              }     
		     			  	 </div>   
			            	
			              </div> 


             <div className="modal fade add_new_address" id="ticket_popup" role="dialog">
              <div className="modal-dialog">
                  <div className="modal-content">
                   <div className="modal-header address_popup_head">
                   <button type="button" className="close" data-dismiss="modal">&times;</button>
                    <h4 className="modal-title">Create Ticket</h4>
                  </div>

                 <form className="form-horizontal" id="ticket_form">

                 <div className="modal-body">
                  <div className="success_msg ticket_success" style={{display:'none'}}></div>
                  <div className="danger_msg ticket_error" style={{display:'none'}}></div>
                 
                  <div className="form-group required">
                   <span className="col-sm-3 control-label">Ticket Type</span>
                   <div className="col-sm-9">
                    <select className="account_input ticket_required" name="ticket_type_id" style={{width:'96%'}} onChange={this.handleValidation}>
                     <option value = "0" selected="selected" >Please select a issue type</option>
                     {this.state.ticket_type ? 
                       this.state.ticket_type.map(function(ticket, index) {
                        return(
                        <option value = {ticket.group_id}>{ticket.group_name}</option>
                        )
                      }) : '' }  
                    </select>
                   </div>
                  </div>

                  <div className="form-group required">
                   <span className="col-sm-3 control-label">Subject</span>
                   <div className="col-sm-9">
                    <input type="text" name="subject" placeholder="Subject" className="account_input ticket_required" style={{margin:'0px'}} onChange={this.handleValidation} />
                   </div>
                 </div>

                 <div className="form-group required">
                   <span className="col-sm-3 control-label">Description</span>
                   <div className="col-sm-9">
                    <textarea className="account_input input-description ticket_required" name="description" rows="4"  placeholder="Description" style={{margin:'0px'}} onChange={this.handleValidation}></textarea>
                   </div>
                 </div>

                  <div className="form-group required">
                    <span className="col-sm-3 control-label">Attach files</span>
                    <div className="col-sm-9">
                       <input type="file" name="attachment_file" id="attachment_file" style={{border:'none', padding: '7px 0px 4px 0px', margin:'0px'}} />
                    </div>
                  </div>
                </div>

                  <div className="modal-footer">
                   <button type="button" onClick={this.submitTicket} className="btn btn-primary pull-right" id="support_submit_btn" >Continue</button>
                  </div>

                </form>
              </div>
             </div>
            </div>           

			</section>
		  </div>
		</div>
	)
 }
}