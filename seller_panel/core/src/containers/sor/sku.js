import React, { Component } from 'react'
import { List, Datagrid, TextField, DateField, ImageField, ShowButton, TabbedShowLayout, Tab, Show, ReferenceManyField} from 'react-admin';
import $ from 'jquery'

import Icon from '@material-ui/icons/KeyboardArrowRight';
export const SkuIcon = Icon;

const TotalPiecesField = ({ record = {} }) => <div>{record.total_pur_qty} Pieces <br /> <span className="price_cls"> {$('<div/>').html(record.total_pur_amt).text()} </span></div>;
TotalPiecesField.defaultProps = { label: 'Total Pieces' };

const ReturnPiecesField = ({ record = {} }) => <div>{record.purchase_return_pieces} Pieces <br /> <span className="price_cls"> {$('<div/>').html(record.purchase_return_amt).text()} </span></div>;
ReturnPiecesField.defaultProps = { label: 'Pieces returned back to you' };

const UnsoldPiecesField = ({ record = {} }) => <div>{record.unsold_pieces} Pieces <br /> <span className="price_cls"> {$('<div/>').html(record.total_unsold_amt).text()} </span></div>;
UnsoldPiecesField.defaultProps = { label: 'Unsold Pieces with us' };

const SoldPiecesField = ({ record = {} }) => <div>{record.total_actual_sold_qty} Pieces <br /> <span className="price_cls"> {$('<div/>').html(record.total_actual_sold_amt).text()} </span></div>;
SoldPiecesField.defaultProps = { label: 'Net Pieces sold' };

const PaidField = ({ record = {} }) => <span>{$('<div/>').html(record.paid_amount).text()}</span>;
PaidField.defaultProps = { label: 'Amount Paid' };


export class skuList  extends Component {
 render(){ 
return(
    <List title="SOR SKU" {...this.props} sort={{ field: 'order_processing_date', order: 'DESC' }}>
        <Datagrid {...this.props}>
            <ImageField source="image" label="Image" sortable={false} />
            <TextField source="sku" label="SKU Code" sortable={false} />
            <TotalPiecesField source="total_pur_qty" label="Total Pieces" sortable={false}  />
            <ReturnPiecesField source="purchase_return_pieces" label="Pieces returned back to you" sortable={false} />
            <UnsoldPiecesField source="unsold_pieces" label="Unsold Pieces with us" sortable={false} />
            <SoldPiecesField source="total_actual_sold_qty" label="Net Pieces Sold" sortable={false} />
            <PaidField source="paid_amount" label="Amount Paid" sortable={false} />
            <ShowButton label={false} />
        </Datagrid>
    </List>
)
}}


const ProductSoldAmountField = ({ record = {} }) => <span>{$('<div/>').html(record.sold_amt).text()}</span>;
ProductSoldAmountField.defaultProps = { label: 'Total Amount' };

const TotalAmountField = ({ record = {} }) => <div className="detail_box"><label>Total Amount</label><span>{$('<div/>').html(record.total_pur_amt).text()}</span></div>;
const ReturnedAmountField = ({ record = {} }) => <div className="detail_box"><label>Returned Pieces Amount</label><span>{$('<div/>').html(record.purchase_return_amt).text()}</span></div>;
const UnsoldAmountField = ({ record = {} }) => <div className="detail_box"><label>Unsold Pieces Amount</label><span>{$('<div/>').html(record.total_unsold_amt).text()}</span></div>;
const SoldAmountField = ({ record = {} }) => <div className="detail_box"><label>Net Sold Amount</label><span>{$('<div/>').html(record.total_actual_sold_amt).text()}</span></div>;
const PaidAmountField = ({ record = {} }) => <div className="detail_box"><label>Paid Amount</label><span>{$('<div/>').html(record.paid_amount).text()}</span></div>;
const BalanceAmountField = ({ record = {} }) => <div className="detail_box"><label>Balance</label><span>{$('<div/>').html(record.seller_balance).text()}</span></div>;

export class TabField  extends Component {
 render(){ 
return(<TabbedShowLayout {...this.props}>
        <Tab label="summary">
         <TextField source="sku" label="SKU Code"  />
         <TextField source="total_pur_qty" label="Total Pieces"  />
         <TextField source="purchase_return_pieces" label="Pieces returned back to you"  />
         <TextField source="unsold_pieces" label="Unsold Pieces with us"  />
          <TextField source="total_actual_sold_qty" label="Net Pieces Sold"  />
        </Tab>
        <Tab label="amount">
         <TotalAmountField  source="total_pur_amt" label="Total Amount"  />
         <ReturnedAmountField source="purchase_return_amt" label="Returned Pieces Amount"  />
         <UnsoldAmountField source="total_unsold_amt" label="Unsold Pieces Amount"  />
         <SoldAmountField source="total_actual_sold_amt" label="Net Sold Amount"  />
         <PaidAmountField source="paid_amount" label="Paid Amount"  />
         <BalanceAmountField source="seller_balance" label="Balance"  />
        </Tab>
        <Tab label="Product">
         <ImageField source="image" label="Image" sortable={false} />
        </Tab>
        <Tab label="Payment Detail of Sku">
                <ReferenceManyField reference="sor/sellerPaymentDetailOfSingleSku" target="product_id" filter={{purchase_id: this.props.record.purchase_id}} addLabel={false} sort={{ field: 'trxn_utr_date', order: 'DESC'}}>
                    <Datagrid >
                        <TextField source="suborder_id" label="Sub Order No" />
                        <TextField source="total_piece" label="Total Pieces" />
                        <ProductSoldAmountField source="sold_amt" label="Total Amount" />
                        <TextField source="trxn_utr" label="Reference No." />
                        <DateField source="trxn_utr_date" label="Payment Done Date" />
                    </Datagrid>
                </ReferenceManyField>
            </Tab>
       </TabbedShowLayout>)
}}


export class skuShow  extends Component {
 render(){ 
return(
    <Show title="SKU view" {...this.props}>
       <TabField />
    </Show> 
)
}}