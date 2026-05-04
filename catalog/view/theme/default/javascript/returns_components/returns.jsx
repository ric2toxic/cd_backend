class Returns extends React.Component {
	
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
		    returns_data:false,
		    success_msg:false,
		    error_msg:false,
		    return_detail:false,
		    bank_detail:false,
		    master_return_id:this.props.master_return_id,
		    order_no:this.props.order_no,
        beyond_master_return_id:false
       	}

      this.getReturnsData          = this.getReturnsData.bind(this);
      this.getReturnsDetail        = this.getReturnsDetail.bind(this);
      this.getCustomerBankInfo     = this.getCustomerBankInfo.bind(this);
      this.addFilterInHistoryState = this.addFilterInHistoryState.bind(this);
      this.handleChange            = this.handleChange.bind(this); 
      this.uploadCourierDetails    = this.uploadCourierDetails.bind(this);
      this.submitCourierDetails    = this.submitCourierDetails.bind(this);
      this.handleValidation        = this.handleValidation.bind(this); 
	}

	componentDidMount()
	{
       this.getReturnsData();  
       this.getCustomerBankInfo();
       if(this.state.master_return_id)
       {
       	 this.getReturnsDetail(this.state.master_return_id);
       } 
	}



    getCustomerBankInfo()
	{   
      var self = this;
	    var filters = '';
        filters = filters+'customer_id='+getCookie('customer_id');
        filters = filters+'&customer_access_token='+getCookie('customer_access_token');
        var encode =  window.btoa(filters);	

      axios({
			method:'GET',
			url:'./api/customer_account/return/getCustomerBankInfo&data='+encode,
			dataType:'json'
		}) 
		.then(response => {

      if(response.data.statusCode == 900){
                     window.location.href = self.state.logout;
                     return false;
                    }

			this.setState({ bank_detail: response.data.data,
							loading:false,
						     something_went_wrong:false });
		});  
	}

	getReturnsData()
	{
      var self = this;
	    var filters = '';
        filters = filters+'customer_id='+getCookie('customer_id');
        filters = filters+'&customer_access_token='+getCookie('customer_access_token');
        if(this.state.order_no != '')
        {
          filters = filters+'&order_no='+this.state.order_no;	
        }

        if(this.state.beyond_master_return_id)
        {
         filters = filters+'&beyond_master_return_id='+this.state.beyond_master_return_id;
        } 

        var encode =  window.btoa(filters);	

        this.addFilterInHistoryState('',this.state.order_no);
        this.setState({master_return_id:0});

      axios({
			method:'GET',
			url:'./api/customer_account/return/getReturnListData&data='+encode,
			dataType:'json'
		}) 
		.then(response => {

      if(response.data.statusCode == 900){
                     window.location.href = self.state.logout;
                     return false;
                    }

      let returns_data = response.data.data.returns;
      
      if(response.data.data.beyond_master_return_id == '')
      {
        $("#load_more").hide();
      }
        let old_returns_data = this.state.returns_data;
        returns_data = [...old_returns_data,...returns_data];
 
			this.setState({ returns_data: returns_data,
              beyond_master_return_id:response.data.data.beyond_master_return_id,
							loading:false,
						    something_went_wrong:false });
		});  
	}

	getReturnsDetail(master_return_id)
	{
    var self = this;
		this.addFilterInHistoryState(master_return_id, this.state.order_no);
		this.setState({master_return_id:master_return_id});

	    var filters = '';
        filters = filters+'customer_id='+getCookie('customer_id');
        filters = filters+'&customer_access_token='+getCookie('customer_access_token');
        filters = filters+'&master_return_id='+master_return_id;
        var encode =  window.btoa(filters);	

      axios({
			method:'GET',
			url:'./api/customer_account/return/getReturnDetailPageData&data='+encode,
			dataType:'json'
		}) 
		.then(response => {

      if(response.data.statusCode == 900){
                     window.location.href = self.state.logout;
                     return false;
                    }

			this.setState({ return_detail: response.data.data,
							loading:false,
						     something_went_wrong:false });
		});  
	}

	addFilterInHistoryState(master_return_id, order_no){

  		let baseUrl = [location.protocol, '//', location.host, location.pathname].join('')+'?route=account/return/getReturns';
  		
  		var filter_string 	= '';

  		if(master_return_id!='')
  		{
  			filter_string   = filter_string+"&master_return_id=" + master_return_id;
  		}

  		if(order_no!='')
  		{
  			filter_string   = filter_string+"&order_no=" + order_no;
  		}
  		
       	window.history.pushState('', null, baseUrl+filter_string);
   	}

    handleChange(field, e)
	{  
       this.setState({order_no:e.target.value});
    } 

    uploadCourierDetails()
    {
      $('#courier_detail_form input[name=shipment_company]').val('');
      $('#courier_detail_form input[name=tracking_no]').val('');
      $(".courier_success").hide();
      $(".courier_error").hide();
      $("#courier_popup").modal("show");
    }

    handleValidation()
    {
      var result = true;
      $(".courier_details_required").each(function(i,e) {
          if($(e).val() == ''){
              if($(e).attr('type') == "file"){
                  $(e).addClass("error_text");
              } else{
                  $(e).addClass("error_border");
                   result = false;
              }
          }
          else
          {
          	if($(e).attr('type') == "file"){
                  $(e).removeClass("error_text");
              } else{
                  $(e).removeClass("error_border");
              }
          }
     });
      return result;	
    }

    submitCourierDetails()
    {
      var self = this;
      if(this.handleValidation())
      {
        $("#return_submit_btn").html('<i class="fa fa-circle-o-notch fa-spin"></i>');
      	var form_data = new FormData();
        var file_data = $('#file').prop('files')[0];
        var allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;
        if(!allowedExtensions.exec(file_data.name)){
            $("#courier_slip").css("color", "#a94442");
            return false;
        }
        form_data.append('shipment_slip', file_data);
        var tracking_no = $('#courier_detail_form input[name=tracking_no]').val();
        form_data.append('tracking_no', tracking_no);
        var shipping_company = $('#courier_detail_form input[name=shipment_company]').val();
        form_data.append('shipment_company', shipping_company);
        form_data.append('master_return_id', this.state.master_return_id);
        form_data.append('order_no', this.state.return_detail.order_no);
        form_data.append('order_id', this.state.return_detail.order_id);
        form_data.append('customer_id', getCookie('customer_id'));
        form_data.append('customer_access_token', getCookie('customer_access_token'));

        $.ajax({
            type:'post',
            dataType:'json',
            data:form_data,
            url:"./api/customer_account/return/uploadCourierDetails",
            async: false,
            mimeType: "form-data",
            contentType: false,
            processData: false,
            success: function(json) {
                $("#return_submit_btn").html('Continue');

                if(json.statusCode == 900){
                     window.location.href = self.state.logout;
                     return false;
                    }

                if(json.statusCode == 200)
                {
                   $(".courier_error").hide();
                   $(".courier_success").html(json.message);
                   $(".courier_success").show();
                   $(".courier_detail_returns").hide();
                   setTimeout(function(){ $("#courier_popup").modal("hide"); }, 1000);	
                }
                else
                {
                  $(".courier_success").hide();
                  $(".courier_error").html(json.message);
                  $(".courier_error").show();
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
  	
	render(){
    let self = this;
		return (
			     <div className="container-fluid width_fix">
			     	<div className="row">
						<section className="my_account">
						   <div className="order_list_block">
			             	<div className="col-sm-3 customer_account_section">
		     	          	<LeftSection 
		     	          	   active="profile"
		     	          	   sub_active="My Returns"
		     				   language={this.props.language}
		     				    />
			             	</div>
			            	
			   		       {this.state.master_return_id ?
                             <div className="col-sm-9">
			   		        <DetailSection 
		     				    language={this.props.language}
		     				    loading={this.state.loading}
		     				    return_detail={this.state.return_detail}
		     				    bank_detail={this.state.bank_detail}
		     				    getReturnsData={this.getReturnsData}
		     				    uploadCourierDetails={this.uploadCourierDetails}
		     				   />
                             </div> 
			   		        :  
			   		        <div className="col-sm-9 my_account_page">
			   		        <RightSection 
		     				    language={this.props.language}
		     				    loading={this.state.loading}
		     				    returns_data={this.state.returns_data}
		     				    getReturnsDetail={this.getReturnsDetail}
		     				    getReturnsData={this.getReturnsData}
		     				    order_no={this.state.order_no}
		     				    handleChange={this.handleChange}
                    beyond_master_return_id={this.state.beyond_master_return_id}
		     				   />
		     				 </div>   
                            } 
			            	
			              </div> 


           <div className="modal fade add_new_address" id="courier_popup" role="dialog">
            <div className="modal-dialog">
                  <div className="modal-content">
                   <div className="modal-header address_popup_head">
                   <button type="button" className="close" data-dismiss="modal">&times;</button>
                    <h4 className="modal-title">Upload Courier Detail</h4>
                  </div>

                 <form className="form-horizontal" id="courier_detail_form">

                 <div className="modal-body">
                  <div className="success_msg courier_success" style={{display:'none'}}></div>
                   <div className="danger_msg courier_error" style={{display:'none'}}></div>
                 
                    <div className="form-group required">
						<span className="col-sm-3 control-label">Upload Courier Slip</span>
						<div className="col-sm-9">
							<input type="file" name="shipment_slip" id="file" className="courier_details_required" style={{border:'none', padding: '7px 0px 4px 0px', margin:'0px'}} onChange={this.handleValidation} />
							<span id="courier_slip">allow only jpeg,jpg or png type</span>
						</div>
					</div>

                    <div className="form-group required">
						<span className="col-sm-3 control-label">Tracking Number</span>
						<div className="col-sm-9">
							<input type="text" name="tracking_no" placeholder="Tracking Number" className="account_input courier_details_required" style={{margin:'0px'}} onChange={this.handleValidation} />
						</div>
					</div>

					<div className="form-group required">
						<span className="col-sm-3 control-label">Shipping company</span>
						<div className="col-sm-9">
							<input type="text" name="shipment_company" placeholder="Shipping company" data-parent_class="payment" className="account_input post_code courier_details_required" style={{margin:'0px'}} onChange={this.handleValidation} />
						</div>
					</div>

               </div>
                  <div className="modal-footer">
						<button id="return_submit_btn" type="button" className="btn btn-primary pull-right" onClick={this.submitCourierDetails} >Continue</button>
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