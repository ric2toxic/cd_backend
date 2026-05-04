import React, { Component } from 'react'
import { List, Datagrid, TextField, Filter, TextInput, DateInput, DateField, ArrayField} from 'react-admin';
import { CardActions, RefreshButton } from 'react-admin';
import Icon from '@material-ui/icons/CreditCard';
import Button from 'material-ui/Button'
import DownloadIcon from '@material-ui/icons/AssignmentReturned';
import { download_csv } from '../../actions/PaymentAction';
import { check_file_exist } from '../../actions/ReportAction';
import  './index.css';
import custom from '../../custom/custom'
import $ from 'jquery';


export const PaymentIcon = Icon;


const InvoiceField = ({ record = {} }) => <span className="green">{$('<div/>').html(record.invoice_value_formatted).text() }</span>;
InvoiceField.defaultProps = { label: 'Amount' };

const DnField = ({ record = {} }) => <span className="red">{$('<div/>').html(record.dn_value_formatted).text() }</span>;
DnField.defaultProps = { label: 'Amount' };

const TrxnField = ({ record = {} }) => <span className="green">{$('<div/>').html(record.trxn_amount_formatted).text() }</span>;
TrxnField.defaultProps = { label: 'Amount' };

const NetPayableField = ({ record = {} }) => <span className="green">{$('<div/>').html(record.net_payable_formatted).text() }</span>;
NetPayableField.defaultProps = { label: 'Net Payable' };

const PaidPayableField = ({ record = {} }) => <span className="green">{$('<div/>').html(record.paid_formatted).text() }</span>;
PaidPayableField.defaultProps = { label: 'Paid' };

const BalancePayableField = ({ record = {} }) => <span>{$('<div/>').html(record.balance_formatted).text() }</span>;
BalancePayableField.defaultProps = { label: 'Balance' };

const DownloadInvoiceField = ({ record = {} }) => <span>
{record.download_link !== "" ?
<a href={record.download_link}>{ record.invoice_no}</a>
:
record.invoice_no}
</span>;
DownloadInvoiceField.defaultProps = { label: 'Inv.' };

const DownloadDebitField = ({ record = {} }) => <span>
{record.download_link !== "" ?
<a href={record.download_link}>{ record.dn_no}</a>
:
record.dn_no}
</span>;
DownloadInvoiceField.defaultProps = { label: 'DN' };


function download_file(filterValues)
{
  if($("#download_btn").html() === 'Download')
  {   
   $("#download_btn").html("please wait...");
   var form_data = '';
   form_data = form_data+'&customer_id='+custom.getCookie('customer_id');
   form_data = form_data+'&customer_access_token='+custom.getCookie('customer_access_token');
   form_data = form_data+'&download=csv';
   if(filterValues.filter_order_no) { form_data = form_data+'&filter_order_no='+filterValues.filter_order_no; }
   if(filterValues.filter_payment_date_to) { form_data = form_data+'&filter_payment_date_to='+filterValues.filter_payment_date_to; }
   if(filterValues.filter_payment_date_from) { form_data = form_data+'&filter_payment_date_from='+filterValues.filter_payment_date_from; }
   if(filterValues.filter_invoice_date_to) { form_data = form_data+'&filter_invoice_date_to='+filterValues.filter_invoice_date_to; }
   if(filterValues.filter_invoice_date_from) { form_data = form_data+'&filter_invoice_date_from='+filterValues.filter_invoice_date_from; }
   if(filterValues.filter_utr) { form_data = form_data+'&filter_utr='+filterValues.filter_utr; }
   if(filterValues.filter_invoice_no) { form_data = form_data+'&filter_invoice_no='+filterValues.filter_invoice_no; }

   var response = download_csv(form_data);
   response.then(function(data){

     setTimeout(function(){ 
       var response2 = check_file_exist(data.data.file_name);
       response2.then(function(data2){
         $("#download_btn").html("Download");
         if(data2.statusCode === 200)
         {
            var customer_id = custom.getCookie('customer_id');
            var customer_access_token = custom.getCookie('customer_access_token');
            window.location.href = data.data.file_link+'&customer_id='+customer_id+'&customer_access_token='+customer_access_token;
         }
        else
         {
           alert(data2.message);
         }
      })
     .catch(function() {
         console.log('data fetch error');
      });

      }, 2000);
      

    })
      .catch(function() {
          console.log('data fetch error');
      });
  }    
}

const ReportFilter = (props) => (
    <Filter {...props}>
        <TextInput label="Order No" source="filter_order_no" />
        <DateInput label="Payment Date To" source="filter_payment_date_to" />
        <DateInput label="Payment Date From" source="filter_payment_date_from" />
        <DateInput label="Invoice Date To" source="filter_invoice_date_to" />
        <DateInput label="Invoice Date From" source="filter_invoice_date_from" />
        <TextInput label="UTR No." source="filter_utr" />
        <TextInput label="Invoice No" source="filter_invoice_no" />
    </Filter>
);

const PaymentActions = ({ resource, filters, displayedFilters, filterValues, basePath, showFilter }) => (
    <CardActions>
        <Button primary onClick={()=>download_file(filterValues)}><DownloadIcon /> <span id="download_btn">Download</span></Button>
        {filters && React.cloneElement(filters, {
            resource,
            showFilter,
            displayedFilters,
            filterValues,
            context: 'button',
        }) }
        <RefreshButton />
    </CardActions>
);


export class paymentreportList  extends Component {
 render(){ 

return(
    <List className="payment_report_page" title="Payment Report" {...this.props} actions={<PaymentActions />} sort={{ field: 'order_id', order: 'DESC' }} filters={<ReportFilter />}>
        <Datagrid className="parent_row">
            <TextField source="order_no" label="Order No" sortable={false} />
            <ArrayField source="invoice" label="Invoice" sortable={false}>
              <Datagrid className="inner_row">
                <DownloadInvoiceField source="invoice_no" label="Inv." />
                <DateField source="invoice_date" label="Date"/>
                <InvoiceField source="invoice_value_formatted"  label="Amount" className="green" />
              </Datagrid>
            </ArrayField>

             <ArrayField source="returns" label="Debit Notes" sortable={false}>
              <Datagrid className="inner_row">
                <DownloadDebitField source="dn_no" label="DN" />
                <DateField source="dn_date" label="Date"/>
                <DnField source="dn_value_formatted"  label="Amount" className="green" />
              </Datagrid>
            </ArrayField>

            <ArrayField source="payments" label="Payments" sortable={false}>
              <Datagrid className="inner_row">
                <TextField source="trxn_utr" label="Utr" className="link" />
                <DateField source="trxn_utr_date" label="Date"/>
                <TrxnField source="trxn_amount_formatted"  label="Amount" className="green" />
              </Datagrid>
            </ArrayField>

            <ArrayField source="summary" label="Summary" sortable={false}>
              <Datagrid className="inner_row">
                <NetPayableField source="net_payable_formatted"  label="Net payable" className="green" />
                <PaidPayableField source="paid_formatted"  label="Paid" className="green" />
                <BalancePayableField source="balance_formatted"  label="Balance" className="green" />
              </Datagrid>
            </ArrayField>
        </Datagrid>
    </List>
)
}}