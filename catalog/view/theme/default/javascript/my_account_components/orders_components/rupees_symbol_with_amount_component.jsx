class RupeesSymbolWithAmountComponent extends React.Component{

	constructor(props){
		super(props);
	}
	

	render(){
		function createMarkup(data) { return {__html: data}; };
		return(
				<div dangerouslySetInnerHTML={createMarkup(this.props.amount)} />
			  )
			
	}
}