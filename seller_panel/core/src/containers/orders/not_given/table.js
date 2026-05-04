import React, { Component } from 'react'
import Checkbox from 'material-ui/Checkbox';
import  '../index.css'

export class Table  extends Component {

   constructor(props)
   {
     super(props);
 
     this.information       = this.information.bind(this);

     this.state = {
     information_agree:false,
     };
   }

   information(order_data)
   {
     if(this.state.information_agree)
     {
       this.setState({information_agree:false});
       this.props.setChanges(order_data, false);
     }
     else
     {
      this.setState({information_agree:true});
      this.props.setChanges(order_data, true);
     }
   }

 render(){ 

   let backgroundColor = this.props.combo_product >= 0 ? '#e6e6e6' : '';  

return(<tr className={"MuiTableRow-root MuiTableRow-hover Datagrid-row Datagrid-rowEven row_"+this.props.table_data.order_product_id} style={{backgroundColor:backgroundColor}}>
           <td className="MuiTableCell-root MuiTableCell-body  Datagrid-rowCell">
              { this.props.combo_product <= 0 ?
             <div className="pd_action_btn_box">
               <ul>
                 <li className={"checkbox_information_"+this.props.table_data.order_product_id} id={"checkbox_information_agree_"+this.props.table_data.order_product_id}>
                   <Checkbox id="information_agree" checked={this.state.information_agree} onChange={()=>this.information(this)}  /> 
                 </li>
                </ul>
               </div>
               :
               <span id={"associate_"+this.props.table_data.order_product_id} data-product_id={this.props.table_data.product_id} data-seller_id={this.props.table_data.seller_id} >Associate product</span>
               } 
           </td>

           <td className="MuiTableCell-root MuiTableCell-body  Datagrid-rowCell">
            <img src={this.props.table_data.image} alt={this.props.table_data.name} />
            <br />
            {this.props.table_data.name}
           </td>
           <td className="MuiTableCell-root MuiTableCell-body  Datagrid-rowCell">
           {this.props.table_data.seller_sku}
           </td>
           <td className="MuiTableCell-root MuiTableCell-body  Datagrid-rowCell">
           {this.props.table_data.comment}
           </td>
            <td className="MuiTableCell-root MuiTableCell-body  Datagrid-rowCell">
            {this.props.table_data.no_of_piece}
           </td>
            <td className="MuiTableCell-root MuiTableCell-body  Datagrid-rowCell">
           {this.props.table_data.price_per_piece}
           </td>
            <td className="MuiTableCell-root MuiTableCell-body  Datagrid-rowCell">
           {this.props.table_data.product_amount}
           </td>
            <td className="MuiTableCell-root MuiTableCell-body  Datagrid-rowCell">
           {this.props.table_data.vat_cst_rate} 
           </td>
           <td className="MuiTableCell-root MuiTableCell-body  Datagrid-rowCell">
           {this.props.table_data.vat_cst_amount}
           </td>
            <td className="MuiTableCell-root MuiTableCell-body  Datagrid-rowCell">
           {this.props.table_data.total_amount}
           </td>
           
         </tr>)
}}