class ProductDiscount extends React.Component {

    constructor(props) {
    super(props);
        this.state = {
            discounts: [],
            is_discount : this.props.discounts.length,
        };
    }


    render() {
        var discounts = '';
         discounts = this.props.discounts.map(function(discount, i){
                return (
                <tr key={i} className="odd gradeA">
                      <td className="discount_content">{discount.quantity} or more </td>
                      <td className="discount_content">{discount.price}</td>
                </tr>
                );
        });
        
        

    	return (
                this.props.discounts.length==0?(
                    <table></table>
                ):(
                    <table width="100%" className="table set_qty_table table-striped table-hover" id="dataTables-example">
                         <thead>
                          <tr className="cart_table_title">
                          <th className="discount_heading">Set Qty </th>
                          <th className="discount_heading">Price  / Piece </th>
                          </tr>
                        </thead>
                        <tbody>
                          {discounts}
                         </tbody>
                    </table>
                )  
    	)
	}
}


