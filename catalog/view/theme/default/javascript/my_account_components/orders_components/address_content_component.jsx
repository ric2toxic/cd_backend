class AddressContentComponent extends React.Component{
	
	constructor(props){
		super(props);
	}

	render(){

		return (
				<div className="col-sm-4 address_content">
					<p className="bold_content">{this.props.title}</p>
					<address>
						{this.props.address.firstname} {this.props.address.lastname} <br />
						{this.props.address.company} <br />
						{this.props.address.alternate_contact_number ? this.props.address.alternate_contact_number : ''} 
						{this.props.address.alternate_contact_number ? <br /> : '' }
						{this.props.address.address_1} {this.props.address.address_2} <br />
						{this.props.address.city} {this.props.address.postcode} <br />
						{this.props.address.zone} {this.props.address.country}
					</address>
				</div>	
			)
	}
}