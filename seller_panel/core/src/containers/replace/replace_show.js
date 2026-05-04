import React, { Component } from 'react'
import { TextField, DateField, TabbedShowLayout, Tab, Show, ReferenceManyField, Datagrid, ImageField} from 'react-admin';
import $ from 'jquery'


const ProductField = ({ record = {} }) => <span>{$('<div/>').html(record.product_amount).text() }</span>;
ProductField.defaultProps = { label: 'Product Amount' };

export class TabField  extends Component {
 render(){ 
 	const { order_product_ids } = this.props.record;
return(<TabbedShowLayout {...this.props}>
        <Tab label="summary">
         <TextField  source="seller_invoice_number" label="Invoice No"  />
         <DateField source="seller_invoice_date" label="Invoice Date"  />
         <TextField source="order_no" label="Order No"  />
         <TextField source="quantity" label="Replace Pieces"  />
         <TextField source="reason_name" label="Details"  />
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

export class replaceShow  extends Component {
 render(){ 
return(
    <Show title="Replace view" {...this.props}>
       <TabField />
    </Show> 
)
}}