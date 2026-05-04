import React, { Component } from 'react'
import { List, Datagrid, TextField, DateInput, Filter, TextInput, SelectInput, ArrayField, DateField, ShowButton, TabbedShowLayout, Tab, Show} from 'react-admin';
import DoneIcon from '@material-ui/icons/AssignmentReturned';

import $ from 'jquery'
import  './index.css'

import Icon from '@material-ui/icons/KeyboardArrowRight';
export const PickupDoneIcon = Icon;

const DoneFilter = (props) => (
    <Filter {...props}>
        <TextInput label="Order No" source="filter_order_no" />
        <DateInput label="Date To" source="filter_invoice_date_to" />
        <DateInput label="Date From" source="filter_invoice_date_from" />
        <TextInput label="Amount To" source="filter_sale_to" />
        <TextInput label="Amount From" source="filter_sale_from" />
        <TextInput label="Invoice No" source="filter_invoice_no" />
        <SelectInput
            source="filter_payment_status"
            label="Payment Status"
            choices={[
                { id: 'paid', name: 'Paid' },
                { id: 'un_paid', name: 'Un Paid' }
            ]}
        />
    </Filter>
);

 const rowStyle = (record, index) => ({
    backgroundColor: record.trxn_done === 'Paid' ? '#dfd' : record.trxn_done === 'Full Returned' ? '#fdd' : record.trxn_done === 'none' ? '#ffd' : '',
});

const AmountField = ({ record = {} }) => <span>{$('<div/>').html(record.seller_inv_amount).text() }</span>;
AmountField.defaultProps = { label: 'Amount' };

const SaleAmountField = ({ record = {} }) => <span>{$('<div/>').html(record.sale).text() }</span>;
SaleAmountField.defaultProps = { label: 'Sale' };

const ReturnAmountField = ({ record = {} }) => <span>{$('<div/>').html(record.return_amount).text() }</span>;
ReturnAmountField.defaultProps = { label: 'Return Amount' };

const PenaltyAmountField = ({ record = {} }) => <span>{$('<div/>').html(record.penalty_amount).text() }</span>;
PenaltyAmountField.defaultProps = { label: 'Penalty Amount' };

const PayableAmountField = ({ record = {} }) => <span>{$('<div/>').html(record.net_payable_amount).text() }</span>;
PayableAmountField.defaultProps = { label: 'Net Payable Amount' };

export class pickup_doneList  extends Component {
 render(){ 
return(
    <List title="Pickup Done Orders" {...this.props} sort={{ field: 'order_id', order: 'DESC' }} filters={<DoneFilter />}>
        <Datagrid className="pickup_parent_row" rowStyle={rowStyle}>
            <TextField source="order_no" label="Order No" />
             <ArrayField source="seller_invoices" label="Seller Invoice" sortable={false}>
              <Datagrid className="pickup_inner_row">
                <TextField source="seller_invoice_no" label="Invoice" />
                <AmountField source="seller_inv_amount" label="Amount"/>
                <DateField source="date_added" label="Date" />
              </Datagrid>
            </ArrayField>
            <SaleAmountField source="sale" label="Sale" sortable={false}  />
            <ReturnAmountField source="return_amount" label="Return Amount" sortable={false}  />
            <PenaltyAmountField source="penalty_amount" label="Penalty Amount" sortable={false} />
            <PayableAmountField source="net_payable_amount" label="Net Payable Amount" sortable={false} />
            <TextField source="trxn_done" label="Payment status" sortable={false} />
            <ShowButton label={false} />
        </Datagrid>
    </List>
)
}}



const DebitField = ({ record }) => (
    <ul className="invoice_link" style={{display: 'inline-table'}}>
         
        {record.debit_note_link.map(item => (
            <li style={{display: 'block'}} key={item.label}><a href={item.url}> <DoneIcon style={{fontSize:'14px'}} /> {item.label}</a></li>
        ))}
    </ul>
)


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
           {this.props.table_data.total_pieces}
           </td>
            <td className="MuiTableCell-root MuiTableCell-body  Datagrid-rowCell">
           {$('<div/>').html(this.props.table_data.price_per_piece).text()}
           </td>
            <td className="MuiTableCell-root MuiTableCell-body  Datagrid-rowCell">
           {$('<div/>').html(this.props.table_data.product_amount).text()}
           </td>
           <td className="MuiTableCell-root MuiTableCell-body  Datagrid-rowCell">
           {this.props.table_data.vat_cst_rate}
           </td>
           <td className="MuiTableCell-root MuiTableCell-body  Datagrid-rowCell">
           {$('<div/>').html(this.props.table_data.vat_cst_amount).text()}
           </td>
            <td className="MuiTableCell-root MuiTableCell-body  Datagrid-rowCell">
           {$('<div/>').html(this.props.table_data.total_amount).text()}
           </td>
         </tr>)
}}



export class TabField  extends Component {
 render(){ 

let sllr_invc_genrted = this.props.record.sllr_invc_genrted;
var sllr_invc = $.map(sllr_invc_genrted, function(value, index) {
    return [value];
});

return(<TabbedShowLayout {...this.props}>
        <Tab label="summary">
         <TextField  source="order_no" label="Order No"  />
         <TextField source="suborder_id" label="Suborder Id"  />
         <DateField source="order_date_added" label="Processing Date"  />
         <TextField source="sale" label="Sale Amount"  />
         <TextField source="return_amount" label="Return Amount"  />
         <TextField source="penalty_amount" label="Penalty Amount"  />
         <TextField source="net_payable_amount" label="Net Payable Amount"  />
        </Tab>
        {
          sllr_invc.map((sllr_invc_data, index) => { 
            return (<Tab key={index} label={'Invoice #'+sllr_invc_data.invoice_no}>
                      <div className="download_box"><a href={sllr_invc_data.invoice_pdf} target="_blank">Invoice <DoneIcon /></a></div>
                     <table className="MuiTable-root">
                       <thead>
                         <tr className="MuiTableRow-root">
                         <th className="MuiTableCell-root MuiTableCell-head  Datagrid-headerCell"> Product</th>
                         <th className="MuiTableCell-root MuiTableCell-head  Datagrid-headerCell">SKU</th>
                         <th className="MuiTableCell-root MuiTableCell-head  Datagrid-headerCell">Set Description</th>
                         <th className="MuiTableCell-root MuiTableCell-head  Datagrid-headerCell">Total Pieces</th>
                         <th className="MuiTableCell-root MuiTableCell-head  Datagrid-headerCell">Price/Piece</th>
                         <th className="MuiTableCell-root MuiTableCell-head  Datagrid-headerCell">Product Amount</th>
                         <th className="MuiTableCell-root MuiTableCell-head  Datagrid-headerCell">Tax Rate</th>
                         <th className="MuiTableCell-root MuiTableCell-head  Datagrid-headerCell">Tax Amount</th>
                         <th className="MuiTableCell-root MuiTableCell-head  Datagrid-headerCell">Total Amount</th>
                         </tr>
                        </thead>
                        <tbody>
                        {
                          $.map(sllr_invc_data.products_data, function(product_arr, index) {
                              
                            return  $.map(product_arr, function(product, index) {

                                 return <Table table_data={product} invoice={sllr_invc_data.invoice_pdf} />
                              })
                          })
                        }
                        </tbody>
                        </table>
                   </Tab>

                   )
            })       
        } 
           {this.props.record.debit_note_link && this.props.record.debit_note_link.length > 0 ?
            <Tab label="Debit Invoice">
             <DebitField source="debit_note_link" label="Download" />
            </Tab>
            : ''}
           
       </TabbedShowLayout>)
}}


export class pickup_doneShow  extends Component {
 render(){ 
return(
    <Show title="Order view" {...this.props}>
       <TabField />
    </Show> 
)
}}