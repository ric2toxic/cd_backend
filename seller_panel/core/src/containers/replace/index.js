import React, { Component } from 'react'
import { List, Datagrid, TextField, Filter, TextInput, ShowButton} from 'react-admin';
import Icon from '@material-ui/icons/FindReplace';
import DownloadIcon from '@material-ui/icons/AssignmentReturned';

export const ReplaceIcon = Icon;

const ReplaceFilter = (props) => (
    <Filter {...props}>
        <TextInput label="Order No" source="filter_order_no" />
    </Filter>
);

const InvoiceField = ({ record = {} }) => <a href={record.download_invoice_note}>{record.seller_invoice_number ? <DownloadIcon /> : ''}{record.seller_invoice_number}</a>;
InvoiceField.defaultProps = { label: 'Invoice No' };

export class tentativeReplacementList  extends Component {
 render(){ 
return(
    <List title="Tentative Replacement" {...this.props} sort={{ field: 'oo.order_id', order: 'DESC' }} filters={<ReplaceFilter />}>
        <Datagrid>
            <InvoiceField source="seller_invoice_number" label="Invoice No" sortable={false} />
            <TextField source="order_no" label="Order No" sortable={false} />
            <TextField source="quantity" label="Pieces" sortable={false}  />
            <TextField source="reason_name" label="Details" sortable={false}  />
            <ShowButton label={false} />
        </Datagrid>
    </List>
)
}}

export class approvedReplacementList  extends Component {
 render(){ 
return(
    <List title="Replacement Request Approved" {...this.props} sort={{ field: 'oo.order_id', order: 'DESC' }} filters={<ReplaceFilter />}>
        <Datagrid>
            <InvoiceField source="seller_invoice_number" label="Invoice No" sortable={false} />
            <TextField source="order_no" label="Order No" sortable={false} />
            <TextField source="quantity" label="Pieces" sortable={false}  />
            <TextField source="reason_name" label="Details" sortable={false}  />
            <ShowButton label={false} />
        </Datagrid>
    </List>
)
}}

export class deliveredReplacementList  extends Component {
 render(){ 
return(
    <List title="Replacement Delivered" {...this.props} sort={{ field: 'oo.order_id', order: 'DESC' }} filters={<ReplaceFilter />}>
        <Datagrid>
            <InvoiceField source="seller_invoice_number" label="Invoice No" sortable={false} />
            <TextField source="order_no" label="Order No" sortable={false} />
            <TextField source="quantity" label="Pieces" sortable={false}  />
            <TextField source="reason_name" label="Details" sortable={false}  />
            <ShowButton label={false} />
        </Datagrid>
    </List>
)
}}

export class disputeReplacementList  extends Component {
 render(){ 
return(
    <List title="Replacement Delivery Dispute" {...this.props} sort={{ field: 'oo.order_id', order: 'DESC' }} filters={<ReplaceFilter />}>
        <Datagrid>
            <InvoiceField source="seller_invoice_number" label="Invoice No" sortable={false} />
            <TextField source="order_no" label="Order No" sortable={false} />
            <TextField source="quantity" label="Pieces" sortable={false}  />
            <TextField source="reason_name" label="Details" sortable={false}  />
            <ShowButton label={false} />
        </Datagrid>
    </List>
)
}}