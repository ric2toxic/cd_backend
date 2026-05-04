class SetQuantityPopup extends React.Component {


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

      var popup_option = [];
      $( ".color_option" ).each(function() {
       popup_option.push($(this).val());
      });

     if(popup_option[this.props.index_key])
      {
        var popup_option = popup_option[this.props.index_key];
        this.setState({data:popup_option})
      } 
}    

	setIncreaseNumber(){                                      
            
            $('.set_quantity_error').fadeToggle( "1000", "linear" );

            setTimeout(function() {
                $('.set_quantity_error').fadeOut('slow'); 
            }, 3000);

            if(this.state.data != ''){ 
                if(parseInt(this.state.data) < parseInt(this.state.maximum)){ 
                    this.state.data = parseInt(this.state.data)+1      
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
                        var error_msg = 'Available set: ' + this.state.maximum + ' set';
                    }else 
                    if((!this.props.is_option) && (this.state.minimum == 0 || this.state.maximum == 0)){
                        error_msg = 'Out of stock!!';
                    }else{
                        var error_msg = 'Available set: ' + this.state.maximum + ' sets';
                    }   
                    this.setState({is_error : 1})
                    this.setState({error_msg : error_msg}) 

                } 
            }else{ 
                this.setState({data : this.state.minimum})
            }
	  };
	  
	  setDecreaseNumber(){

            $('.set_quantity_error').fadeToggle( "1000", "linear" );
            
            if(this.state.data != ''){ 
                this.state.data = parseInt(this.state.data);
                if( this.state.data <= this.state.minimum ){
                    

                    this.setState({data : this.state.minimum})
                    
                        
                        var error_msg = '';
                        if(this.state.minimum == 1){
                            error_msg = 'Minimum order: ' + this.state.minimum + ' ' + ' set ';
                        }else
                       if((!this.props.is_option) && (this.state.minimum == 0 || this.state.maximum == 0)){
                            error_msg = 'Out of stock!!';
                        }else{

                            if(this.props.is_option)
                            { error_msg = 'Minimum set: ' +1+ ' ' + ' sets'; }
                            else { error_msg = 'Minimum set: ' +this.state.minimum + ' ' + ' sets'; }
                           
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
                        
                        var error_msg = 'Select between ' + this.state.minimum +' and ' + this.state.maximum

                        this.setState({is_error : 1})
                        this.setState({error_msg : error_msg})            
                        this.setState({data : e.target.value})       
                        //alert(this.state.error_msg); 
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
                this.setState({data : this.state.minimum})
                //error_msg = 'Please enter the sets';
                //this.setState({is_error : 1})
                //this.setState({error_msg : error_msg})
            }

	  };
	  
	  updateState(e){
                this.setState({is_error : 0})
                this.setState({error_msg : error_msg}) 

                var error_msg = 'Select between ' + this.state.minimum +' and ' + this.state.maximum
                
                if( parseInt(e.target.value) < parseInt(this.state.minimum) || parseInt(e.target.value) > parseInt(this.state.maximum) ){
                    this.setState({is_error : 1})
                    this.setState({error_msg : error_msg})            
                    this.setState({data : e.target.value})       
                    //alert(this.state.error_msg); 
                     $('.set_quantity_error').show();

                     $('#detail-button-cart').attr("disabled", true);
                     //var id = 'option-input_' + this.props.product_option_value_id;
                     //$('#'+id).attr("disabled", true);
                     
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
            

    	return (
    		<div className="set_quantity_panel">
                <div className="input-group bootstrap-touchspin">
                  <span className="input-group-btn">button-cart
                    <button disable className="btn btn-default bootstrap-touchspin-down" type="button" onClick = {this.setDecreaseNumber}>-</button>
                  </span>
                        
                  <input disable type={this.props.popup_type} id={id} value={this.state.data} name = {name} pattern="[0-9]*" onChange= {this.updateState.bind(this)}   className={"col-md-8 form-control set_box "+this.props.colorbox} />
                  <input disable type="hidden" name = {max_name} value={this.state.maximum}  />
                  
                  <span className="input-group-btn">
                    <button disable className="btn btn-default bootstrap-touchspin-up" type="button"  onClick = {this.setIncreaseNumber}>+</button>
                  </span>
            	</div>
                <SetQuantiyError is_error = {this.state.is_error} error_msg = {this.state.error_msg}  />  
            </div>

		)
	}
}
