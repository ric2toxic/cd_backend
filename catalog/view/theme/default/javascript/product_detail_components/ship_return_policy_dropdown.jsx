class ShipRetrnPolicyDropDown extends React.Component {
	


	constructor(props) {
        super(props);
       
        this.state = {
        	active: false,
        };
    }


    componentDidMount(){
      $('.toggle_icon').click(function(){
          $(this).find('i').toggleClass('fa-plus-square-o fa-minus-square-o');  
        });
     }


	render() {
    if(this.props.international_store)
    {
    	return (
    		<div className="trem_Policy">
    			<p>Shipping Charge and Return Policy 
                  <a className="tram_box toggle_icon collapsed" data-toggle="collapse" data-parent="#accordion" href="#Policy">
                  <i className="fa fa-plus-square-o" aria-hidden="true"></i>
                  </a>
                </p>
                
                <div className="policy_text collapse" id="Policy">
                <p>For any query regarding returns, mail us on returns@wholesalebox.in or WhatsApp chat/Call us on +91 95878 96265</p>
                </div>
            </div>)
    }
    else
    {
      return (
        <div className="trem_Policy">
          <p>Shipping Charge and Return Policy 
                  <a className="tram_box toggle_icon collapsed" data-toggle="collapse" data-parent="#accordion" href="#Policy">
                  <i className="fa fa-plus-square-o" aria-hidden="true"></i>
                  </a>
                </p>
                
                <div className="policy_text collapse" id="Policy">
                 <p>{this.props.language.shipping_charges_txt}</p>

                 <p>{this.props.language.delivery_txt}</p>

                 <p>{this.props.language.dispatch_txt}</p>

                 <p>
                 <b>{this.props.language.courier_txt}</b><br />
                    {this.props.language.courier_txt_1}<br />
                    {this.props.language.courier_txt_2}
                 </p>

                 <p>
                 <b>{this.props.language.surface_txt}</b> 
                 {this.props.language.surface_txt_1}
                 </p>
                
                {this.props.seller_returnable == false ?
                 <p>{this.props.language.return_policy_txt}</p>
                 :''} 
                 
                 {this.props.seller_returnable == false ?
                  <p>For more information <a href="./returns-policy" target="_blank">click here</a></p>
                 : <div className="red_text"><strong>This item is non-returnable</strong></div> } 
                
                </div>
            </div>)      
    }

    }

}
