import React, { Component } from 'react'
import { List, Datagrid, TextField, Filter, TextInput, DateInput, DateField, ShowButton} from 'react-admin';
import $ from 'jquery'
import Icon from '@material-ui/icons/AssignmentReturn';
import DownloadIcon from '@material-ui/icons/AssignmentReturned';

export const ReturnIcon = Icon;

const ReturnFilter = (props) => (
    <Filter {...props}>
        <TextInput label="Order No" source="filter_order_no" />
        <TextInput label="Debit Note No" source="debit_note_no" />
        <DateInput label="Debit Note Date To" source="debit_note_date_to" />
        <DateInput label="Debit Note Date From" source="debit_note_date_from" />
    </Filter>
);


const InvoiceField = ({ record = {} }) => <a href={record.download_invoice_pdf}>{record.seller_invoice_number ? <DownloadIcon /> : ''}{record.seller_invoice_number}</a>;
InvoiceField.defaultProps = { label: 'Invoice No' };

const DebitNoteField = ({ record = {} }) => <a href={record.download_debit_note}>{record.debit_note_no ? <DownloadIcon /> : ''}{record.debit_note_no}</a>;
DebitNoteField.defaultProps = { label: 'Debit Note No' };

const AmountField = ({ record = {} }) => <span>{$('<div/>').html(record.amount).text() }</span>;
AmountField.defaultProps = { label: 'Return Amount' };


export class tentativeReturnList  extends Component {
 render(){ 
return(
    <List title="Tentative Returns" {...this.props} sort={{ field: 'oo.order_id', order: 'DESC' }} filters={<ReturnFilter />}>
        <Datagrid>
            <InvoiceField source="seller_invoice_number" label="Invoice No" sortable={false} />
            <TextField source="order_no" label="Order No" sortable={false} />
            <TextField source="quantity" label="Return Pieces" sortable={false}  />
            <AmountField source="amount" sortable={false} />
            <TextField source="reason_name" label="Details"  sortable={false} />
            <DebitNoteField source="debit_note_no" sortable={false} />
            <DateField source="debit_note_date" label="Debit Note Date" sortable={false} />
            <ShowButton label={false} />
        </Datagrid>
    </List>
)
}}

export class approvedReturnList  extends Component {
 render(){ 
return(
    <List title="Return Request Approved" {...this.props} sort={{ field: 'oo.order_id', order: 'DESC' }} filters={<ReturnFilter />}>
        <Datagrid>
            <InvoiceField source="seller_invoice_number" label="Invoice No" sortable={false} />
            <TextField source="order_no" label="Order No" sortable={false} />
            <TextField source="quantity" label="Return Pieces" sortable={false}  />
            <AmountField source="amount" sortable={false} />
            <TextField source="reason_name" label="Details"  sortable={false} />
            <DebitNoteField source="debit_note_no" sortable={false} />
            <DateField source="debit_note_date" label="Debit Note Date" sortable={false} />
            <ShowButton label={false} />
        </Datagrid>
    </List>
)
}}

export class deliveredReturnList  extends Component {
 render(){ 
return(
    <List title="Return Delivered" {...this.props} sort={{ field: 'oo.order_id', order: 'DESC' }} filters={<ReturnFilter />}>
        <Datagrid>
            <InvoiceField source="seller_invoice_number" label="Invoice No" sortable={false} />
            <TextField source="order_no" label="Order No" sortable={false} />
            <TextField source="quantity" label="Return Pieces" sortable={false}  />
            <AmountField source="amount" sortable={false} />
            <TextField source="reason_name" label="Details"  sortable={false} />
            <DebitNoteField source="debit_note_no" sortable={false} />
            <DateField source="debit_note_date" label="Debit Note Date" sortable={false} />
            <ShowButton label={false} />
        </Datagrid>
    </List>
)
}}

export class disputeReturnList  extends Component {
 render(){ 
return(
    <List title="Return Delivery Dispute" {...this.props} sort={{ field: 'oo.order_id', order: 'DESC' }} filters={<ReturnFilter />}>
        <Datagrid>
            <InvoiceField source="seller_invoice_number" label="Invoice No" sortable={false} />
            <TextField source="order_no" label="Order No" sortable={false} />
            <TextField source="quantity" label="Return Pieces" sortable={false}  />
            <AmountField source="amount" sortable={false} />
            <TextField source="reason_name" label="Details"  sortable={false} />
            <DebitNoteField source="debit_note_no" sortable={false} />
            <DateField source="debit_note_date" label="Debit Note Date" sortable={false} />
            <ShowButton label={false} />
        </Datagrid>
    </List>
)
}}