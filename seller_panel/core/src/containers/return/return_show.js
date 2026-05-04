import React, { Component } from 'react'
import { TextField, DateField, TabbedShowLayout, Tab, Show, ReferenceManyField, Datagrid, ImageField} from 'react-admin';
import DownloadIcon from '@material-ui/icons/AssignmentReturned';
import $ from 'jquery'

const FullNameField = ({ record = {} }) => <a href={record.download_debit_note}>{record.debit_note_no ? <DownloadIcon /> : ''}{record.debit_note_pdf}</a>;
FullNameField.defaultProps = { label: 'Download Debit Note' };

const ProductField = ({ record = {} }) => <span>{$('<div/>').html(record.product_amount).text() }</span>;
ProductField.defaultProps = { label: 'Product Amount' };

const AmountField = ({ record = {} }) => <div className="detail_box"><label>Return Amount</label><span>{$('<div/>').html(record.amount).text()}</span></div>;

export class TabField  extends Component {
 render(){ 
 	const { order_product_ids } = this.props.record;
return(<TabbedShowLayout {...this.props}>
        <Tab label="summary">
         <TextField  source="seller_invoice_number" label="Invoice No"  />
         <DateField source="seller_invoice_date" label="Invoice Date"  />
         <TextField source="order_no" label="Order No"  />
         <TextField source="quantity" label="Return Pieces"  />
         <AmountField source="amount" label="Return Amount"  />
         <TextField source="reason_name" label="Details"  />
        </Tab>
         <Tab label="Debit Note">
         <TextField source="debit_note_no" label="Debit Note No"  />
         <DateField source="debit_note_date" label="Debit Note Date"  />
         <TextField source="debit_note_amount" label="Debit Note Amount"  />
          <FullNameField source="Download Debit" />
        </Tab>
         <Tab label="products">
                <ReferenceManyField reference="returns/product_list" filter={{ order_product_ids: order_product_ids }} addLabel={false}>
                    <Datagrid>
                        <TextField source="name" label="Product" />
                        <ImageField source="image" label="Image" sortable={false} />
                        <TextField source="seller_sku" label="SKU" />
                        <TextField source="comment" label="Set Description" />
                        <TextField source="quantity" label="No. of Pieces" />
                        <TextField source="transfer_price_per_piece" label="Price Per Piece" />
                        <ProductField source="product_amount" label="Product Amount" />
                    </Datagrid>
                </ReferenceManyField>
        </Tab>
       </TabbedShowLayout>)
}}

export class returnShow  extends Component {
 render(){ 
return(
    <Show title="Return view" {...this.props}>
       <TabField />
    </Show> 
)
}}