import React, { Component } from 'react'
import { List, Datagrid, TextField, DateField, ShowButton, TabbedShowLayout, Tab, Show} from 'react-admin';
import $ from 'jquery'
import  './index.css'

import Icon from '@material-ui/icons/KeyboardArrowRight';
export const TentativeIcon = Icon;


const TotalField = ({ record = {} }) => <span>{$('<div/>').html(record.total).text() }</span>;
TotalField.defaultProps = { label: 'Total Amount' };

export class tentativeList  extends Component {
 render(){ 
return(
    <List title="Tentative Orders" {...this.props} sort={{ field: 'o.date_added', order: 'DESC' }}>
        <Datagrid>
            <TextField source="order_no" label="Order No" />
            <DateField source="order_date_added" label="Date"  />
            <TotalField source="total" label="Total Amount"  />
            <ShowButton label={false} />
        </Datagrid>
    </List>
)
}}


export class Table  extends Component {
 render(){ 
return(<tr className="MuiTableRow-root MuiTableRow-hover Datagrid-row Datagrid-rowEven">
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
           {this.props.table_data.quantity}
           </td>
            <td className="MuiTableCell-root MuiTableCell-body  Datagrid-rowCell">
           {this.props.table_data.piece_in_set}
           </td>
            <td className="MuiTableCell-root MuiTableCell-body  Datagrid-rowCell">
           {$('<div/>').html(this.props.table_data.price_per_piece).text()}
           </td>
            <td className="MuiTableCell-root MuiTableCell-body  Datagrid-rowCell">
           {$('<div/>').html(this.props.table_data.product_amount).text()}
           </td>
           <td className="MuiTableCell-root MuiTableCell-body  Datagrid-rowCell">
           {this.props.table_data.vat_cst_rate} ({this.props.table_data.vat_cst_amount})
           
           </td>
            <td className="MuiTableCell-root MuiTableCell-body  Datagrid-rowCell">
           {this.props.table_data.total_amount}
           </td>
         </tr>)
}}



export class TabField  extends Component {
 render(){ 

return(<TabbedShowLayout {...this.props}>
        <Tab label="summary">
         <TextField  source="order_no" label="Order No"  />
         <DateField source="order_date_added" label="Order Date"  />
         <TextField source="total" label="Total"  />
         <TextField source="order_status" label="Status"  />
        </Tab>
        <Tab label='Products'>
           <table className="MuiTable-root">
                       <thead>
                         <tr className="MuiTableRow-root">
                         <th className="MuiTableCell-root MuiTableCell-head  Datagrid-headerCell"> Product</th>
                         <th className="MuiTableCell-root MuiTableCell-head  Datagrid-headerCell">SKU</th>
                         <th className="MuiTableCell-root MuiTableCell-head  Datagrid-headerCell">Set Description</th>
                         <th className="MuiTableCell-root MuiTableCell-head  Datagrid-headerCell">Sets</th>
                         <th className="MuiTableCell-root MuiTableCell-head  Datagrid-headerCell">No. of Pieces</th>
                         <th className="MuiTableCell-root MuiTableCell-head  Datagrid-headerCell">Price/Piece</th>
                         <th className="MuiTableCell-root MuiTableCell-head  Datagrid-headerCell">Product Amount</th>
                          <th className="MuiTableCell-root MuiTableCell-head  Datagrid-headerCell">Tax Rate/Amount</th>
                         <th className="MuiTableCell-root MuiTableCell-head  Datagrid-headerCell">Total Amount</th>
                         </tr>
                        </thead>
                        <tbody>
                       {
                       $.map(this.props.record.product_data, function(product_arr, index) {
                              
                            return  $.map(product_arr, function(product, index) {

                                 return <Table table_data={product} />
                              })
                          })
                        }
            </tbody>
            </table>
           </Tab>
           
       </TabbedShowLayout>)
}}


export class tentativeShow  extends Component {
 render(){ 
return(
    <Show title="Order view" {...this.props}>
       <TabField />
    </Show> 
)
}}