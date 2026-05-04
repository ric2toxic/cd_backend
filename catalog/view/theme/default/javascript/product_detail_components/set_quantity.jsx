class SetQuantity extends React.Component {


	constructor(props){
	    super(props);
	    this.state = {
	      data : this.props.minimum,
	      is_error : 0, 
	      error_msg : '',
              minimum : 5,
              maximum : 10,
	    }
            
            this.setIncreaseNumber = this.setIncreaseNumber.bind(this);
            this.setDecreaseNumber = this.setDecreaseNumber.bind(this);
            this.increaseErrorMssg = this.increaseErrorMssg.bind(this);
            this.decreaseErrorMssg = this.decreaseErrorMssg.bind(this); 
            this.mouseLeaveButton = this.mouseLeaveButton.bind(this);

	  };
	  

        componentWillMount(){
            if(this.props.maximum >= 0){
                this.setState({maximum:this.props.maximum})
            }
            
            if(this.props.minimum >= 0){
                this.setState({minimum:this.props.minimum}) 
            }
             
            if(this.props.maximum == 0){
                this.setState({minimum:this.props.maximum})
                this.setState({minimum:this.props.minimum})
            } 
            

        }
        componentDidMount(){
            this.setState({data : this.state.data}); 
        }  


componentWillReceiveProps()
{
  this.setState({data : this.props.minimum});
  if(this.props.maximum >= 0){
     this.setState({maximum:this.props.maximum})
    }
            
    if(this.props.minimum >= 0){
        this.setState({minimum:this.props.minimum}) 
     }
    if(this.props.maximum == 0){
       this.setState({minimum:this.props.maximum})
       this.setState({minimum:this.props.minimum})
   }   
}  

    increaseErrorMssg(){                                
            
            if(this.state.data != ''){ 
                    var sets = 'sets';
                    var error_msg='';
                    if(this.state.maximum == 1){
                        if(typeof this.props.table_heading !== 'undefined' && typeof this.props.table_name !== 'undefined'){
                        var error_msg = this.props.table_heading+' '+this.props.table_name+" "+'Available: ' + this.state.maximum + ' set';
                    }else{
                        var error_msg = 'Available: ' + this.state.maximum + ' set';
                    }
                    }else 
                    if((!this.props.is_option) && (this.state.minimum == 0 || this.state.maximum == 0)){
                        if(typeof this.props.table_heading !== 'undefined' && typeof this.props.table_name !== 'undefined'){
                        error_msg = this.props.table_heading+' '+this.props.table_name+" "+'Out of stock!!';
                    }else{
                        error_msg = 'Out of stock!!';
                    }
                    }else if(this.state.data == this.state.maximum){
                        if(typeof this.props.table_heading !== 'undefined' && typeof this.props.table_name !== 'undefined'){
                        var error_msg = this.props.table_heading+' '+this.props.table_name+" "+'Available: ' + this.state.maximum + ' sets';
                    }else{
                        var error_msg = 'Available: ' + this.state.maximum + ' sets';
                    }
                    }
                    if(error_msg !=''){
                        this.setState({is_error : 1})
                        this.setState({error_msg : error_msg});
                        $('.error_tool_tip_'+this.props.product_option_value_id).show();
                    }

            }
      };
    decreaseErrorMssg(){
        if(this.state.data !== ''){ 
                    var sets = 'sets';
                    var error_msg='';

                        if(this.state.minimum == 1 && this.state.data < '1'){
                            if(typeof this.props.table_heading !== 'undefined' && typeof this.props.table_name !== 'undefined'){
                            error_msg = this.props.table_heading+' '+this.props.table_name+" "+'Minimum order: ' + this.state.minimum + ' ' + ' set ';
                        }else{
                            error_msg = 'Minimum order: ' + this.state.minimum + ' ' + ' set ';
                        }
                        }else
                       if((!this.props.is_option) && (this.state.minimum == 0 || this.state.maximum == 0)){
                        if(typeof this.props.table_heading !== 'undefined' && typeof this.props.table_name !== 'undefined'){
                            error_msg = this.props.table_heading+' '+this.props.table_name+" "+'Out of stock!!';
                        }else{
                            error_msg = 'Out of stock!!';
                        }
                        }else{
                            if(this.state.data < this.state.minimum){
                            if(typeof this.props.table_heading !== 'undefined' && typeof this.props.table_name !== 'undefined'){ 
                                error_msg = this.props.table_heading+' '+this.props.table_name+" "+'Minimum: ' +this.state.minimum + ' ' + ' sets1';
                                }else{
                                    error_msg = 'Minimum: ' +this.state.minimum + ' ' + ' sets';
                                } 
                            }
                            else if(this.state.data == this.state.minimum || this.state.data == 0){
                                if(typeof this.props.table_heading !== 'undefined' && typeof this.props.table_name !== 'undefined'){
                                if(this.state.minimum=='0'){
                                error_msg = this.props.table_heading+' '+this.props.table_name+" "+'Minimum: ' +1+ ' ' + ' sets';
                                }
                                else{
                                error_msg = this.props.table_heading+' '+this.props.table_name+" "+'Minimum: ' +this.state.minimum + ' ' + ' sets';
                                }
                                
                                }else{
                                if(this.state.minimum=='0'){
                                 error_msg = 'Minimum: ' +1+ ' ' + ' sets';
                                }else{
                                error_msg = 'Minimum: ' +this.state.minimum + ' ' + ' sets';
                                }
                                }
                            }
                           
                        }
                    if(error_msg !=''){
                        this.setState({is_error : 1})
                        this.setState({error_msg : error_msg});
                        $('.error_tool_tip_'+this.props.product_option_value_id).fadeIn();
                        //$('.set_quantity_error').fadeIn();

                    }

            }
    }

    mouseLeaveButton(){
                    this.setState({is_error : 0});
                    this.setState({error_msg : ''});
                   // $('.error_tool_tip_'+this.props.product_option_value_id).css('display','none');
    }

	setIncreaseNumber(){                              
            
           // $('.set_quantity_error').fadeToggle( "1000", "linear" );

            // setTimeout(function() {
            //     $('.set_quantity_error').fadeOut('slow'); 
            // }, 3000);
            if(this.state.data != '' &&  parseInt(this.state.data) < parseInt(this.state.maximum)){ 
                if(parseInt(this.state.data) < parseInt(this.state.maximum)){ 
                    var state_data = parseInt(this.state.data)+1 
                    this.setState({data: state_data});     
                    this.setState({is_error : 0})
                    this.setState({error_msg : ''})

                    if( parseInt(this.state.data) < parseInt(this.state.minimum) || parseInt(this.state.data) > parseInt(this.state.maximum) ){
                        $('#detail-button-cart').attr("disabled", true); 
                        var id = 'option-input_' + this.props.product_option_value_id;
                        $('#'+id).attr("disabled", true);   
                    }else{  
                        $('#detail-button-cart').attr("disabled", false); 
                        var id = 'option-input_' + this.props.product_option_value_id;
                        $('#'+id).attr("disabled", false);
                    } 



                }else{
                    var sets = 'sets';
                    if(this.state.maximum == 1){
                        if(typeof this.props.table_heading !== 'undefined' && typeof this.props.table_name !== 'undefined'){
                            var error_msg = this.props.table_heading+' '+this.props.table_name+" "+'Available: ' + this.state.maximum + ' set';
                        }else{
                            var error_msg = 'Available: ' + this.state.maximum + ' set';
                         }
                    }else 
                    if((!this.props.is_option) && (this.state.minimum == 0 || this.state.maximum == 0)){
                        if(typeof this.props.table_heading !== 'undefined' && typeof this.props.table_name !== 'undefined'){
                            error_msg = this.props.table_heading+' '+this.props.table_name+" "+'Out of stock!!';
                        }else{
                            error_msg = 'Out of stock!!';
                        }
                    }else{
                        if(typeof this.props.table_heading !== 'undefined' && typeof this.props.table_name !== 'undefined'){
                            var error_msg = this.props.table_heading+' '+this.props.table_name+" "+'Available: ' + this.state.maximum + ' sets';
                        }else{
                            var error_msg = 'Available: ' + this.state.maximum + ' sets';
                        }
                    }   
                    this.setState({is_error : 1})
                    this.setState({error_msg : error_msg}) 

                } 
            }else{ 
            if(parseInt(this.state.data) < parseInt(this.state.maximum)){

                 var state_data = parseInt(this.state.data)+1 
                 this.setState({data: state_data});
            }
                //this.setState({data : this.state.minimum})
            }
	  };
	  
	  setDecreaseNumber(){

           // $('.set_quantity_error').fadeToggle( "1000", "linear" );

            if(this.state.data != '' && this.state.data > this.state.minimum){ 
                this.state.data = parseInt(this.state.data);
                if( this.state.data <= this.state.minimum ){
                    

                    this.setState({data : this.state.minimum})
                    
                        
                        var error_msg = '';
                        if(this.state.minimum == 1){
                            if(typeof this.props.table_heading !== 'undefined' && typeof this.props.table_name !== 'undefined'){
                                error_msg = this.props.table_heading+' '+this.props.table_name+" "+'Minimum order: ' + this.state.minimum + ' ' + ' set ';
                            }else{
                                error_msg = 'Minimum order: ' + this.state.minimum + ' ' + ' set ';
                            }
                        }
                        else 
                        if((!this.props.is_option) && (this.state.minimum == 0 || this.state.maximum == 0)){

                            if(typeof this.props.table_heading !== 'undefined' && typeof this.props.table_name !== 'undefined'){
                                error_msg = this.props.table_heading+' '+this.props.table_name+" "+'Out of stock!!';
                            }else{
                                error_msg = 'Out of stock!!';
                            }
                        }
                        else{

                            if(this.props.is_option)
                            { 
                                if(typeof this.props.table_heading !== 'undefined' && typeof this.props.table_name !== 'undefined'){
                                    error_msg = this.props.table_heading+' '+this.props.table_name+" "+'Minimum set: ' +1+ ' ' + ' sets';
                                }else{
                                    error_msg = 'Minimum set: ' +1+ ' ' + ' sets';
                                }
                            }
                            else { 
                                if(typeof this.props.table_heading !== 'undefined' && typeof this.props.table_name !== 'undefined'){
                                    error_msg = this.props.table_heading+' '+this.props.table_name+" "+'Minimum set: ' +this.state.minimum + ' ' + ' sets';
                                 }else{
                                    error_msg = 'Minimum set: ' +this.state.minimum + ' ' + ' sets';
                                 }
                             }
                           
                         }

                        this.setState({is_error : 1})
                        this.setState({error_msg : error_msg})


                        
                        if(this.state.data == this.state.minimum){ 
                            $('#detail-button-cart').attr("disabled", false);
                            var id = 'option-input_' + this.props.product_option_value_id;
                            $('#'+id).attr("disabled", false);   
                        }else{  
                            $('#detail-button-cart').attr("disabled", true); 
                            var id = 'option-input_' + this.props.product_option_value_id;
                            $('#'+id).attr("disabled", true);
                        }


                }else{
                           
                    this.state.data = this.state.data-1
                    if( parseInt(this.state.data) < parseInt(this.state.minimum) || parseInt(this.state.data) > parseInt(this.state.maximum) ){
                        
                        var error_msg = this.props.table_heading+' '+this.props.table_name+" "+'Select between ' + this.state.minimum +' and ' + this.state.maximum

                        this.setState({is_error : 1})
                        this.setState({error_msg : error_msg}) 
                         $('.set_quantity_error').show();

                         $('#detail-button-cart').attr("disabled", true);
                         var id = 'option-input_' + this.props.product_option_value_id;
                         $('#'+id).attr("disabled", true); 
                     
                    }else{
                        
                        
                        this.setState({is_error : 0})
                        this.setState({error_msg : ''})

                        $('#detail-button-cart').attr("disabled", false);
                        var id = 'option-input_' + this.props.product_option_value_id;
                        $('#'+id).attr("disabled", false); 

                    }
                  

                }
            }else{ 
                //this.setState({data : this.state.minimum})
                //error_msg = 'Please enter the sets';
                //this.setState({is_error : 1})
                //this.setState({error_msg : error_msg})
            }

	  };
	  
	  updateState(e){
                this.setState({is_error : 0})
                this.setState({error_msg : error_msg}) 

                if(typeof this.props.table_heading !== 'undefined' && typeof this.props.table_name !== 'undefined'){
                    var error_msg = this.props.table_heading+' '+this.props.table_name+" "+'Select between ' + this.state.minimum +' and ' + this.state.maximum;
                }else{
                    var error_msg = 'Select between ' + this.state.minimum +' and ' + this.state.maximum
                }
                
                if( parseInt(e.target.value) < parseInt(this.state.minimum) || parseInt(e.target.value) > parseInt(this.state.maximum) ){
                    this.setState({is_error : 1})
                    this.setState({error_msg : error_msg})            
                    //this.setState({data : e.target.value}); 

                     //$('#detail-button-cart').attr("disabled", true);

                     if(parseInt(e.target.value) > parseInt(this.state.maximum)){
                          this.setState({data: this.state.maximum});
                    }else{
                          this.setState({data: this.state.minimum});  
                    }
                     $('.error_tool_tip_'+this.props.product_option_value_id).show();
                    //  var idsClass=this.props.product_option_value_id
                    //  setTimeout(function() {
                    // $('.set_quantity_error').fadeOut('slow'); 
                    // }, 3000);
                    setTimeout(function() { 
                        this.setState({is_error : 0});
                        this.setState({error_msg : ''});
                    }.bind(this), 2000);
                }else{
                    $('.set_quantity_error').hide();   
                    this.setState({data : e.target.value});
                    $('#detail-button-cart').attr("disabled", false);
                    var id = 'option-input_' + this.props.product_option_value_id;
                     $('#'+id).attr("disabled", false); 
                }
                
               
	  }
           
          



	render() { 
            var id = 'order_set_quantity';
            var name = 'demo2';
            var max_name = 'demo2';
            if(this.props.is_option > 0){
                id = 'option-input_' + this.props.product_option_value_id;
                name = 'option_quantities[' + this.props.product_option_id + '][' + this.props.product_option_value_id + ']';
                max_name = 'option_max_quantities[' + this.props.product_option_id + '][' + this.props.product_option_value_id + ']';
                
            }
            var disable = '';
            if(this.state.is_error == 1){ 
                disable = 'disabled';        
            }

            if(typeof this.props.color_option == "undefined") {
                var color_option='';
            }else{
                var color_option=this.props.color_option;

            }
        $(".txtboxToFilter").keydown(function (e) {
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110]) !== -1 ||
             // Allow: Ctrl/cmd+A
            (e.keyCode == 65 && (e.ctrlKey === true || e.metaKey === true)) ||
             // Allow: Ctrl/cmd+C
            (e.keyCode == 67 && (e.ctrlKey === true || e.metaKey === true)) ||
             // Allow: Ctrl/cmd+X
            (e.keyCode == 88 && (e.ctrlKey === true || e.metaKey === true)) ||
             // Allow: home, end, left, right
            (e.keyCode >= 35 && e.keyCode <= 39)) {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });
    	return (
    		<div className="set_quantity_panel">
                <div className="input-group bootstrap-touchspin">
                  <span className="input-group-btn" >button-cart
                    <button className={this.state.data=='0' || this.state.data==this.state.minimum?"btn btn-default bootstrap-touchspin-down disabled_btn":"btn btn-default bootstrap-touchspin-down"} type="button" onClick = {this.setDecreaseNumber} onMouseEnter={this.decreaseErrorMssg} onMouseLeave={this.mouseLeaveButton}>-</button>
                  </span>
                        
                  <input disable type="text" id={id} value={this.state.data} name = {name} pattern="[0-9]*" onChange= {this.updateState.bind(this)}   className={"col-md-8 form-control set_box txtboxToFilter "+color_option} />
                  <input disable type="hidden" name = {max_name} value={this.state.maximum}  />
                  
                  <span className="input-group-btn">
                    <button className={this.state.data==this.state.maximum?"btn btn-default bootstrap-touchspin-up disabled_btn":"btn btn-default bootstrap-touchspin-up"} type="button"  onClick = {this.setIncreaseNumber} onMouseEnter={this.increaseErrorMssg} onMouseLeave={this.mouseLeaveButton}>+</button>
                  </span>
            	</div>
                <SetQuantiyError is_error = {this.state.is_error} error_msg = {this.state.error_msg} divId={this.props.product_option_value_id}  />  
            </div>

		)
	}
}
