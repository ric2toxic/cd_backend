import React, { Component } from 'react'
import { connect } from 'react-redux'
import Checkbox from 'material-ui/Checkbox';
import Replay from '@material-ui/icons/Replay';
import Clear from '@material-ui/icons/Clear';
import Create from '@material-ui/icons/Create';
import AirportShuttle from '@material-ui/icons/AirportShuttle';
import { showNotification as showNotificationAction } from 'react-admin'
import  '../index.css'
import $ from 'jquery'

 class Table  extends Component {
    constructor(props)
   {
     super(props);

     this.edit_order   = this.edit_order.bind(this);
     this.cancel_order = this.cancel_order.bind(this);
     this.update_order = this.update_order.bind(this);

     this.state = {}
   }  

   edit_order(order_data)
    { 
     const { showNotification } = this.props; 
     var product_id = order_data.props.table_data.order_product_id;
     var partial = order_data.props.table_data.partial; 
       if(partial)
       {
         var no_of_piece = $("#quantity_"+product_id).prev("span").html(); 
         $("#quantity_"+product_id).prev("span").addClass("hidden");
         $(".checkbox_information_"+product_id).addClass("hidden");
         $("#quantity_"+product_id).removeClass("hidden");
         $("#quantity_btn_"+product_id).removeClass("hidden");
         $("#quantity_cancel_btn_"+product_id).removeClass("hidden");
         $("#quantity_"+product_id).val(no_of_piece);
       }
      else
      {
        showNotification('Error: Partial Edit is not available for some products.', 'warning');
      } 

    }

   cancel_order(order_data)
    { 
     var product_id = order_data.props.table_data.order_product_id;  
     var no_of_piece = $("#quantity_"+product_id).prev("span").html(); 
     $("#quantity_"+product_id).prev("span").removeClass("hidden");
     $(".checkbox_information_"+product_id).removeClass("hidden");
     $("#quantity_"+product_id).addClass("hidden");
     $("#quantity_btn_"+product_id).addClass("hidden");
     $("#quantity_cancel_btn_"+product_id).addClass("hidden");
     $("#quantity_"+product_id).val(no_of_piece);
    }

    update_order(order_data)
    { 
      const { showNotification } = this.props;
      var product_id     = order_data.props.table_data.order_product_id;
      var no_of_piece_old = order_data.props.table_data.no_of_piece;
      var no_of_piece = $("#quantity_"+product_id).val();
      var partial = order_data.props.table_data.partial;

      if(partial)
      { 
         if(no_of_piece > 0 && no_of_piece < no_of_piece_old)
         {
           $("#quantity_"+product_id).prev("span").html(no_of_piece); 
           $("#quantity_"+product_id).prev("span").removeClass("hidden");
           $(".checkbox_information_"+product_id).removeClass("hidden");
           $("#quantity_"+product_id).addClass("hidden");
           $("#quantity_btn_"+product_id).addClass("hidden");
           $("#quantity_cancel_btn_"+product_id).addClass("hidden");
           this.props.setChanges(order_data, 'SELLER_PARTIAL', no_of_piece);
         }
         else
         {
           showNotification('Error: Please enter a valid number', 'warning');
         }
      }
      else
       {
        showNotification('Error: Partial Edit is not available for some products.', 'warning');
       }
    }

 render(){ 

let backgroundColor = this.props.combo_product >= 0 ? '#e6e6e6' : '';

return(<tr className={"MuiTableRow-root MuiTableRow-hover Datagrid-row Datagrid-rowEven row_"+this.props.table_data.order_product_id} style={{backgroundColor:backgroundColor}}>
           <td className="MuiTableCell-root MuiTableCell-body  Datagrid-rowCell">
             {this.props.combo_product <= 0 ?
             <div className="pd_action_btn_box">
               <ul>
                 <li className={"checkbox_information_"+this.props.table_data.order_product_id} id={"checkbox_information_agree_"+this.props.table_data.order_product_id}>
                   <Checkbox id="information_agree" checked={this.props.information_agree} onChange={()=> this.props.setChanges(this, 'SELLER_APPROVED')}  /> 
                 </li>
                 
                 <li id={"checkbox_information_edit_"+this.props.table_data.order_product_id} className={"checkbox_information_"+this.props.table_data.order_product_id} onClick={()=>this.edit_order(this)}>
                      <Create className="blue_full"  />
                      <Checkbox id="information_edit" className="hidden" /> 
                 </li>
                                                 
                 <li id={"checkbox_information_decline_"+this.props.table_data.order_product_id} className={"checkbox_information_"+this.props.table_data.order_product_id} onClick={()=>this.props.setChanges(this, 'SELLER_NOT_SUPPLIED')}>
                      <Clear className="red_full"  />
                       <Checkbox id="information_decline" className="hidden"  />
                 </li>

                  <li id={"checkbox_information_dispatch_"+this.props.table_data.order_product_id} className={"checkbox_information_"+this.props.table_data.order_product_id} onClick={()=>this.props.setChanges(this, 'SELLER_LATER_DISPATCH')}>
                       <AirportShuttle className="orange_full"  />
                       <Checkbox id="information_dispatch" className="hidden"  />
                  </li>

                  <li id={"checkbox_information_undo_"+this.props.table_data.order_product_id} className={"hidden checkbox_information_undo_"+this.props.table_data.order_product_id} onClick={()=>this.props.undoChanges(this)}>
                      <Replay className="blue_full"  />
                      <Checkbox id="information_undo" className="hidden"  />
                  </li>

                </ul>
               </div>
               : 
                 <span id={"associate_"+this.props.table_data.order_product_id} data-product_id={this.props.table_data.product_id} data-price_per_piece={this.props.table_data.price_per_piece} data-seller_input_tax={this.props.table_data.seller_input_tax} data-no_of_piece={this.props.table_data.no_of_piece} data-total_amount={this.props.table_data.total_amount}>Associate product</span>
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
            <span>{this.props.table_data.no_of_piece}</span>
            <input type="text" id={"quantity_"+this.props.table_data.order_product_id} className="quantity_box hidden" />
            <button type="buton" className="quantity_btn hidden" id={"quantity_btn_"+this.props.table_data.order_product_id} onClick={()=>this.update_order(this)}>Update</button>
            <button onClick={()=>this.cancel_order(this)} type="buton" className="quantity_cancel_btn hidden" id={"quantity_cancel_btn_"+this.props.table_data.order_product_id}>Cancel</button>
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
            <td className="MuiTableCell-root MuiTableCell-body  Datagrid-rowCell" id={"total_amount_"+this.props.table_data.order_product_id}>
           {this.props.table_data.total_amount}
           </td>
           
         </tr>)
}}

export default connect(null, {
    showNotification: showNotificationAction
})(Table);