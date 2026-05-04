class SearchBoxComponent extends React.Component {
	
	constructor(props) {
		super(props);
		this.state = {
			language: this.props.language,
			invOrOrdNoValue : ''
		}
	}

	componentDidMount(){
		$('input[name="inv_search"]').on('keypress',function(e){
			$(this).focus();
			if(e.keyCode==13){
				$('#search_submit').trigger('click');
			}
		});
	}

	clearSearch() {
		this.props.clearSearch();
	}

	

	render(){
		return (
   	 		<div className="col-sm-9 search_block no_padding">
	   	 		<div className="search_section">
	              <div className="col-sm-10">
	                <div className="form-group">
	                  <input type="text" className="form-control" name="inv_search" onChange={this.props.invOrOrdNoSearchChangeValue} placeholder={this.state.language.input_search_placeholder} value={this.props.searched_value}/>
	                  {
	                  	this.props.searched_value !='' ? <span className="pull-right search_clear" onClick={this.clearSearch.bind(this)}><i className="fa fa-times"></i></span> : ''
	                  }
	                  
	                </div>  
	              </div>
	              <div className="col-sm-2">  
	                <button type="button" className="btn btn-primary" id="search_submit" onClick={()=>this.props.invOrOrdNoSearchSubmit(this.props.searched_value)} >{this.state.language.label_search_button}</button> 
	              </div>
	            </div>
			</div>
		)
	}
}
