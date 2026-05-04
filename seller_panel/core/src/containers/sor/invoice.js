import React, { Component } from 'react'
import { List, Datagrid, TextField, DateField, ShowButton, TabbedShowLayout, Tab, Show} from 'react-admin';
import $ from 'jquery'
import  './index.css'

import Icon from '@material-ui/icons/KeyboardArrowRight';
export const InvoiceIcon = Icon;

const TotalPiecesField = ({ record = {} }) => <div>{record.total_pieces} Pieces <br /> <span className="price_cls"> {$('<div/>').html(record.total_amount).text()} </span></div>;
TotalPiecesField.defaultProps = { label: 'Total Pieces' };

const ReturnPiecesField = ({ record = {} }) => <div>{record.total_purchase_return_pieces} Pieces <br /> <span className="price_cls"> {$('<div/>').html(record.total_purchase_return_amount).text()} </span></div>;
ReturnPiecesField.defaultProps = { label: 'Pieces returned back to you' };

const UnsoldPiecesField = ({ record = {} }) => <div>{record.unsold_pieces} Pieces <br /> <span className="price_cls"> {$('<div/>').html(record.unsold_amount).text()} </span></div>;
UnsoldPiecesField.defaultProps = { label: 'Unsold Pieces with us' };

const SoldPiecesField = ({ record = {} }) => <div>{record.pieces_sold} Pieces <br /> <span className="price_cls"> {$('<div/>').html(record.amount_sold).text()} </span></div>;
SoldPiecesField.defaultProps = { label: 'Net Pieces sold' };

const PaidField = ({ record = {} }) => <span>{$('<div/>').html(record.paid_amount).text()}</span>;
PaidField.defaultProps = { label: 'Amount Paid' };

const PendingField = ({ record = {} }) => <span>{$('<div/>').html(record.seller_balance).text()}</span>;
PendingField.defaultProps = { label: 'Pending Paid' };


export class invoiceList  extends Component {
 render(){ 
return(
    <List title="SOR Invoice" {...this.props} sort={{ field: 'order_processing_date', order: 'DESC' }}>
        <Datagrid {...this.props}>
            <TextField source="invoice_no" label="Invoice No" />
            <DateField source="invoice_date" label="Invoice Date"  />
            <TotalPiecesField source="total_pieces" label="Total Pieces" sortable={false}  />
            <ReturnPiecesField source="total_purchase_return_pieces" label="Pieces returned back to you" sortable={false} />
            <UnsoldPiecesField source="unsold_pieces" label="Unsold Pieces with us" sortable={false} />
            <SoldPiecesField source="pieces_sold" label="Net Pieces sold" sortable={false} />
            <PaidField source="paid_amount" label="Amount Paid" sortable={false} />
            <PendingField source="seller_balance" label="Pending Amount" sortable={false} />
            <ShowButton label={false} />
        </Datagrid>
    </List>
)
}}


export class Table  extends Component {
 render(){ 
return(<tr className="MuiTableRow-root MuiTableRow-hover Datagrid-row Datagrid-rowEven">
           
           {this.props.count === 1 ? 
           <td className="MuiTableCell-root MuiTableCell-body  Datagrid-rowCell" rowspan={this.props.total_length}>
            <img src={this.props.table_data.image} alt={this.props.table_data.image} />
           </td>
           : ''}

           <td className="MuiTableCell-root MuiTableCell-body  Datagrid-rowCell">
            {this.props.table_data.sku}
           </td>
           <td className="MuiTableCell-root MuiTableCell-body  Datagrid-rowCell">
           {this.props.table_data.pieces} Pieces
           </td>
            <td className="MuiTableCell-root MuiTableCell-body  Datagrid-rowCell">
           {this.props.table_data.purchase_return_pieces > 0 ? this.props.table_data.purchase_return_pieces: 0} Pieces
           </td>
            <td className="MuiTableCell-root MuiTableCell-body  Datagrid-rowCell">
           {this.props.table_data.actual_pieces_sold > 0 ? this.props.table_data.actual_pieces_sold : 0} Pieces<br />
           </td>
            <td className="MuiTableCell-root MuiTableCell-body  Datagrid-rowCell">
           {this.props.table_data.return_quantity > 0 ? this.props.table_data.return_quantity : 0} Pieces<br />
           </td>
            <td className="MuiTableCell-root MuiTableCell-body  Datagrid-rowCell">
           {this.props.table_data.pieces_sold > 0 ? this.props.table_data.pieces_sold : 0} Pieces<br />
           </td>
         </tr>)
}}


export class TabField  extends Component {
 render(){ 

return(<TabbedShowLayout {...this.props}>
        <Tab label='Products'>
           <table className="MuiTable-root">
                       <thead>
                         <tr className="MuiTableRow-root">
                         <th className="MuiTableCell-root MuiTableCell-head  Datagrid-headerCell">Image</th>
                         <th className="MuiTableCell-root MuiTableCell-head  Datagrid-headerCell"> SKU</th>
                         <th className="MuiTableCell-root MuiTableCell-head  Datagrid-headerCell">Total Pieces</th>
                         <th className="MuiTableCell-root MuiTableCell-head  Datagrid-headerCell">Pieces return back to you</th>
                         <th className="MuiTableCell-root MuiTableCell-head  Datagrid-headerCell">Pieces Sold by us</th>
                         <th className="MuiTableCell-root MuiTableCell-head  Datagrid-headerCell">Pieces return by Customer</th>
                         <th className="MuiTableCell-root MuiTableCell-head  Datagrid-headerCell">Net Pieces sold</th>
                         </tr>
                        </thead>
                        <tbody>
                       {
                       $.map(this.props.record.invoice_products, function(product_arr, sku_key) {
                            var count=0;
                            var total_length = Object.keys(product_arr).length;
                            return  $.map(product_arr, function(product, index) {
                                  count++;
                                 return <Table table_data={product} key={index} count={count} total_length={total_length}  />
                              })
                          })
                        }
            </tbody>
            </table>
           </Tab>
           
       </TabbedShowLayout>)
}}


export class invoiceShow  extends Component {
 render(){ 
return(
    <Show title="SOR Invoice" {...this.props}>
       <TabField />
    </Show> 
)
}}