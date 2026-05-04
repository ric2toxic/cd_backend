import React, { Component } from 'react'
import { List, Datagrid, TextField, ChipField, DateInput, Filter, TextInput, SelectInput, DateField, ShowButton, TabbedShowLayout, Tab, Show} from 'react-admin';
import $ from 'jquery'
import Panding from './panding/';
import NotGiven from './not_given/';
import ChangeInvoice from './change_invoice/'
import  './index.css'
import Icon from '@material-ui/icons/KeyboardArrowRight';
export const PickupRequestIcon = Icon;


const RequestFilter = (props) => (
    <Filter {...props}>
       <TextInput label="Order No" source="filter_order_no_requested" />
        <DateInput label="Processing Date To" source="filter_order_processing_date_to" />
        <DateInput label="Processing Date From" source="filter_order_processing_date_from" />
        <TextInput label="Amount To" source="filter_order_amount_to" />
        <TextInput label="Amount From" source="filter_order_amount_from" />
        <SelectInput
            source="edit_type_status"
            label="Status"
            choices={[
                { id: 'invoiced', name: 'Invoiced' },
                { id: 'partial', name: 'Partial' },
                { id: 'cancelled', name: 'Cancelled' },
                { id: 'pending', name: 'Pending' },
            ]}
        />
    </Filter>
);

 const rowStyle = (record, index) => ({
    backgroundColor: record.edit_type_status === 'Pending' ? '#ffd' : record.edit_type_status === 'Cancelled' ? '#fdd' : '#dfd',
});

const TotalField = ({ record = {} }) => <span>{$('<div/>').html(record.total).text() }</span>;
TotalField.defaultProps = { label: 'Total Order Amount' };

export const pickup_requestList = (props) => (

    <List  title="Pickup Requested Orders" {...props} sort={{ field: 'order_processing_date_time', order: 'DESC' }} filters={<RequestFilter />}>
        <Datagrid rowStyle={rowStyle}>
            <TextField source="order_no" label="Order No" />
            <TextField source="suborder_id" label="Suborder Id"  />
            <DateField source="order_processing_date" label="Order Processing Date"  />
            <TotalField source="total" label="Total Order Amount" sortable={false} />
            <ChipField source="edit_type_status" label="Status" sortable={false} />
            <ShowButton label={false} />
        </Datagrid>
    </List>
);


//const TotalAmountField = ({ record = {} }) => <div className="detail_box"><label>Total</label><span>{$('<div/>').html(record.total).text()}</span></div>;




export class TabField  extends Component {
 render(){ 
let self = this;
let pending_orders = this.props.record.pending_orders;
var pending_orders_arr = $.map(pending_orders, function(value, index) {
                           return  $.map(value, function(value2, index2) {
                                 return [value2];
                               });
                            });
return(<TabbedShowLayout {...this.props}>
        
        {this.props.record.pending_orders.length !== 0 ?
         <Tab label="Pending Orders" className="pickup_requested">
            <Panding pending_orders={this.props.record.pending_orders} total_product={pending_orders_arr.length} order_id={this.props.record.order_id} suborder_id={this.props.record.suborder_id} date_ranges={this.props.record.date_ranges} />          
         </Tab>
         : ''} 

         { this.props.record.seller_not_given.length !== 0 ?
          <Tab label="Seller Not Given" className="pickup_requested">
            <NotGiven pending_orders={this.props.record.seller_not_given} order_id={this.props.record.order_id} suborder_id={this.props.record.suborder_id} />          
          </Tab> : ''}

        {  $.map(this.props.record.sllr_invc_genrted, function(sllr_invc_data, index) {
            return (<Tab key={index} className="pickup_requested" label={'Invoice #'+sllr_invc_data.invoice_no}>
                      <ChangeInvoice key={index} products_data={sllr_invc_data.products_data} seller_invoice_id={index} invoice_no={sllr_invc_data.invoice_no} date={sllr_invc_data.date} edit_invoice_available={sllr_invc_data.edit_invoice_available} pickup_status={sllr_invc_data.pickup_status} invoice_pdf={sllr_invc_data.invoice_pdf} order_id={self.props.record.order_id} suborder_id={self.props.record.suborder_id} date_ranges={self.props.record.date_ranges} />
                   </Tab> )
            }) 
        }
       </TabbedShowLayout>)
}}


export class NotGivenTabField  extends Component {
 render(){ 
let self = this;
let pending_orders = this.props.record.pending_orders;
var pending_orders_arr = $.map(pending_orders, function(value, index) {
                           return  $.map(value, function(value2, index2) {
                                 return [value2];
                               });
                            });
return(<TabbedShowLayout {...this.props}>
        
         { this.props.record.seller_not_given.length !== 0 ?
          <Tab label="Seller Not Given" className="pickup_requested">
            <NotGiven pending_orders={this.props.record.seller_not_given} order_id={this.props.record.order_id} suborder_id={this.props.record.suborder_id} />          
          </Tab> : ''}
         
         {this.props.record.pending_orders.length !== 0 ?
         <Tab label="Pending Orders" className="pickup_requested">
            <Panding pending_orders={this.props.record.pending_orders} total_product={pending_orders_arr.length} order_id={this.props.record.order_id} suborder_id={this.props.record.suborder_id} date_ranges={this.props.record.date_ranges} />          
         </Tab>
         : ''} 

        {  $.map(this.props.record.sllr_invc_genrted, function(sllr_invc_data, index) {
            return (<Tab key={index} className="pickup_requested" label={'Invoice #'+sllr_invc_data.invoice_no}>
                      <ChangeInvoice key={index} products_data={sllr_invc_data.products_data} seller_invoice_id={index} invoice_no={sllr_invc_data.invoice_no} date={sllr_invc_data.date} edit_invoice_available={sllr_invc_data.edit_invoice_available} pickup_status={sllr_invc_data.pickup_status} invoice_pdf={sllr_invc_data.invoice_pdf} order_id={self.props.record.order_id} suborder_id={self.props.record.suborder_id} date_ranges={self.props.record.date_ranges} />
                   </Tab> )
            }) 
        }

       </TabbedShowLayout>)
}}

export class InvoiceTabField  extends Component {
 render(){ 
let self = this;
let pending_orders = this.props.record.pending_orders;
var pending_orders_arr = $.map(pending_orders, function(value, index) {
                           return  $.map(value, function(value2, index2) {
                                 return [value2];
                               });
                            });
return(<TabbedShowLayout {...this.props}>
          
        {  $.map(this.props.record.sllr_invc_genrted, function(sllr_invc_data, index) {
            return (<Tab key={index} className="pickup_requested" label={'Invoice #'+sllr_invc_data.invoice_no}>
                      <ChangeInvoice key={index} products_data={sllr_invc_data.products_data} seller_invoice_id={index} invoice_no={sllr_invc_data.invoice_no} date={sllr_invc_data.date} edit_invoice_available={sllr_invc_data.edit_invoice_available} pickup_status={sllr_invc_data.pickup_status} invoice_pdf={sllr_invc_data.invoice_pdf} order_id={self.props.record.order_id} suborder_id={self.props.record.suborder_id} date_ranges={self.props.record.date_ranges} />
                   </Tab> )
            }) 
        }

        {this.props.record.pending_orders.length !== 0 ?
         <Tab label="Pending Orders" className="pickup_requested">
            <Panding pending_orders={this.props.record.pending_orders} total_product={pending_orders_arr.length} order_id={this.props.record.order_id} suborder_id={this.props.record.suborder_id} date_ranges={this.props.record.date_ranges} />          
         </Tab>
         : ''} 

         { this.props.record.seller_not_given.length !== 0 ?
          <Tab label="Seller Not Given" className="pickup_requested">
            <NotGiven pending_orders={this.props.record.seller_not_given} order_id={this.props.record.order_id} suborder_id={this.props.record.suborder_id} />          
          </Tab> : ''}
 
       </TabbedShowLayout>)
}}



export class ListTabField  extends Component {
 render(){ 
    return(<div>
          
         <div className="order_data" style={{marginLeft:'15px'}}>          
          Order No : <strong>{this.props.record.order_no}</strong>
          &nbsp; &nbsp; 
          Order Date : <strong>{this.props.record.order_processing_date}</strong> 
         </div>
          <br /> 
         { this.props.record.pending_orders.length !== 0 ?
            <TabField {...this.props} />
            : 
            this.props.record.seller_not_given.length !== 0 ?
             <NotGivenTabField {...this.props} />
            :
             <InvoiceTabField {...this.props} />
             }

        </div>)
 }}

export class pickup_requestShow  extends Component {
 render(){ 
return(
    <Show title="Order view" {...this.props}>
       <ListTabField {...this.props} />
    </Show> 
)
}}