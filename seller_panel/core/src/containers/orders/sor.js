import React, { Component } from 'react'
import { List, Datagrid, TextField, DateInput, Filter, TextInput, ArrayField, DateField, ShowButton, TabbedShowLayout, Tab, Show} from 'react-admin';
import $ from 'jquery'
import  './index.css'

import Icon from '@material-ui/icons/KeyboardArrowRight';
export const SorIcon = Icon;

const SorFilter = (props) => (
    <Filter {...props}>
        <TextInput label="Order No" source="filter_order_no" />
        <DateInput label="Date To" source="filter_invoice_date_to" />
        <DateInput label="Date From" source="filter_invoice_date_from" />
        <TextInput label="Amount To" source="filter_sale_to" />
        <TextInput label="Amount From" source="filter_sale_from" />
        <TextInput label="Invoice No" source="filter_invoice_no" />
    </Filter>
);

const SaleField = ({ record = {} }) => <span>{$('<div/>').html(record.sale_amt).text() }</span>;
SaleField.defaultProps = { label: 'Sale' };

const ReturnField = ({ record = {} }) => <span>{$('<div/>').html(record.return_amount).text() }</span>;
ReturnField.defaultProps = { label: 'Return Amount' };

const PenaltyField = ({ record = {} }) => <span>{$('<div/>').html(record.penalty_amount).text() }</span>;
PenaltyField.defaultProps = { label: 'Penalty Amount' };

const PayableField = ({ record = {} }) => <span>{$('<div/>').html(record.net_payable_amount).text() }</span>;
PenaltyField.defaultProps = { label: 'Net Payable Amount' };

export class sorList  extends Component {
 render(){ 
return(
    <List title="SOR Orders" {...this.props} sort={{ field: 'order_processing_date_time', order: 'DESC' }} filters={<SorFilter />}>
        <Datagrid className="pickup_parent_row" {...this.props}>
            <TextField source="order_no" label="Order No" />
             <ArrayField source="sor_invoices" label="Sor Invoice" sortable={false}>
              <Datagrid className="pickup_inner_row">
                <TextField source="sor_invoice_no" label="Invoice No" />
                <DateField source="order_processing_date" label="Processing Date" />
              </Datagrid>
            </ArrayField>
            <SaleField source="sale_amt" label="Sale"  />
            <ReturnField source="return_amount" label="Return Amount" sortable={false}  />
            <PenaltyField source="penalty_amount" label="Penalty Amount" sortable={false} />
            <PayableField source="net_payable_amount" label="Net Payable Amount" sortable={false} />
            <TextField source="order_status_name" label="Payment status" sortable={false} />
            <ShowButton label={false} />
        </Datagrid>
    </List>
)
}}


export class Table  extends Component {
 render(){ 
return(<tr className="MuiTableRow-root MuiTableRow-hover Datagrid-row Datagrid-rowEven">
          <td className="MuiTableCell-root MuiTableCell-body  Datagrid-rowCell">
            {this.props.table_data.sor_invoice_no} <br /> 
            {this.props.table_data.sor_invoice_date} 
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
           {this.props.table_data.seller_invoice_id}
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
           {this.props.table_data.vat_cst_rate} ({$('<div/>').html(this.props.table_data.vat_cst_amount).text()})
           </td>
            <td className="MuiTableCell-root MuiTableCell-body  Datagrid-rowCell">
           {$('<div/>').html(this.props.table_data.total_amount).text()}
           </td>
         </tr>)
}}



export class TabField  extends Component {
 render(){ 

return(<TabbedShowLayout {...this.props}>
        <Tab label="summary">
         <TextField  source="order_no" label="Order No"  />
         <TextField source="suborder_id" label="Suborder Id"  />
         <DateField source="order_date_added" label="Order Date"  />
         <TextField source="sale_amt" label="Sale Amount"  />
         <TextField source="return_amount" label="Return Amount"  />
         <TextField source="penalty_amount" label="Penalty Amount"  />
         <TextField source="net_payable_amount" label="Net Payable Amount"  />
         <TextField source="order_status_name" label="Status"  />
        </Tab>
        <Tab label='Products'>
           <table className="MuiTable-root">
                       <thead>
                         <tr className="MuiTableRow-root">
                         <th className="MuiTableCell-root MuiTableCell-head  Datagrid-headerCell">Invoice Detail</th>
                         <th className="MuiTableCell-root MuiTableCell-head  Datagrid-headerCell"> Product</th>
                         <th className="MuiTableCell-root MuiTableCell-head  Datagrid-headerCell">SKU</th>
                         <th className="MuiTableCell-root MuiTableCell-head  Datagrid-headerCell">Set Description</th>
                         <th className="MuiTableCell-root MuiTableCell-head  Datagrid-headerCell">Invoice No</th>
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
                       $.map(this.props.record.seller_sor_product, function(product_arr, index) {
                              
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


export class sorShow  extends Component {
 render(){ 
return(
    <Show title="Order view" {...this.props}>
       <TabField />
    </Show> 
)
}}