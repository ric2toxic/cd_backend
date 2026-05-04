class CourierPartnerPreference extends React.Component{
	
	constructor(props){
		super(props);
	}

	render(){
		
		return (
					<div className="form-group">
							<label className="radio-inline">
								<input type="radio"
									   className="dont_use_wholesalebox_packing_tape" 
									   name="courier_partner_preference"
									   
									   value={this.props.partner_preference.courier_name} 
									   disabled
									   
									     /> {this.props.partner_preference.courier_name} 
							</label>		     
						</div>
			
		)	
		
				
	}
}