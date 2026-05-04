class ProductFilter extends React.Component {

    constructor(props) {
    super(props);
        this.state = {
            discounts: [],
            is_filter : 0,
        };

        

    }
    
   /* componentWillMount(){
        if(this.props.filters.length > 0){
            this.setState({is_filter:1})
        }
    }
*/
    render() {

        
          
        var filters = this.props.filters.map(function(filter, i){
                return (
                <tr key={i} className="odd gradeA">
                    <td className="filter_heading">{filter.group_name}</td>
                    <td className="filter_content">{filter.filter_name}</td>    
                </tr>
                );
        });
        
        

    	return (
                this.props.filters.length==0?(
                    <table></table>
                ):(
                    <table width="100%" className="table set_qty_table table-striped table-hover" id="dataTables-example">
                        <tbody>
                          {filters}
                        </tbody>
                    </table>
                )  
    	)
	}
}


