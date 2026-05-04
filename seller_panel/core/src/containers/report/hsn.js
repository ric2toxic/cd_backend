import React, { Component } from 'react'
import custom from '../../custom/custom'
import { connect } from 'react-redux'
import { List, Datagrid, TextField, Filter, SelectInput} from 'react-admin';
import DownloadIcon from '@material-ui/icons/AssignmentReturned';
import Icon from '@material-ui/icons/KeyboardArrowRight';
import { showNotification as showNotificationAction } from 'react-admin'
import { check_file_exist } from '../../actions/ReportAction';
export const HsnReportIcon = Icon;

var dt = new Date();
var current_year = dt.getFullYear();
let year = [];
for(var i=2017; i<= current_year; i++)
{
  year.push({id:i, name: i})  
}


const HsnFilter = (props) => (
    <Filter {...props}>

      <SelectInput
            source="filter_select_year"
            label="Select Year"
            choices={year}
         />

        <SelectInput
            source="filter_select_month"
            label="Select Month"
            choices={[
                { id: '1', name: 'Jan' },
                { id: '2', name: 'Feb' },
                { id: '3', name: 'Mar' },
                { id: '4', name: 'Apr' },
                { id: '5', name: 'May' },
                { id: '6', name: 'June' },
                { id: '7', name: 'July' },
                { id: '8', name: 'Aug' },
                { id: '9', name: 'Sept' },
                { id: '10', name: 'Oct' },
                { id: '11', name: 'Nov' },
                { id: '12', name: 'Dec' }
            ]}
         />
       <SelectInput
            source="filter_select_quarter"
            label="Select Quarter"
            choices={[
                { id: 'Q1', name: '01 April TO 30 June' },
                { id: 'Q2', name: '01 July TO 30 September' },
                { id: 'Q3', name: '01 October TO 31 December' },
                { id: 'Q4', name: '01 January TO 31 March' }
            ]}
         />
           
    </Filter>
);


const FullNameField = (props) => <a style={{cursor:'pointer'}} onClick={()=>props.file_exist(props.record.file_name, props.record.file_link)}>{props.record.file_name ? <DownloadIcon /> : ''}</a>;
FullNameField.defaultProps = { label: 'Download Report' };

const TotalTaxField = ({ record = {} }) => <span>{record.total_tax.toFixed(2)}</span>;
TotalTaxField.defaultProps = { label: 'Total Tax' };

const IGSTField = ({ record = {} }) => <span>{record.IGST.toFixed(2)}</span>;
IGSTField.defaultProps = { label: 'IGST' };

const SGSTField = ({ record = {} }) => <span>{record.SGST.toFixed(2)}</span>;
SGSTField.defaultProps = { label: 'SGST' };

const CGSTField = ({ record = {} }) => <span>{record.CGST.toFixed(2)}</span>;
CGSTField.defaultProps = { label: 'CGST' };

const ProductField = ({ record = {} }) => <span>{record.total_product_value.toFixed(2)}</span>;
ProductField.defaultProps = { label: 'Product Value' };

 class hsnReportList  extends Component {

  constructor(props)
  {
    super(props);
    this.file_exist       = this.file_exist.bind(this);
  }

  file_exist(file_name, file_link)
  {  
    const { showNotification } = this.props;   
    var response = check_file_exist(file_name);
    response.then(function(data){
      if(data.statusCode === 200)
      {
        var customer_id = custom.getCookie('customer_id');
        var customer_access_token = custom.getCookie('customer_access_token');
        window.location.href = file_link+'&customer_id='+customer_id+'&customer_access_token='+customer_access_token;
      }
      else
      {
        showNotification(data.message, 'warning');
      }
    })
    .catch(function() {
     console.log('data fetch error');
    });
  }

 render(){ 
return(
    <List title="HSN Report" {...this.props} sort={{ field: 'order_date_added', order: 'DESC' }} filters={<HsnFilter />}>
        <Datagrid>
            <TextField source="hsn_code" label="HSN Code" sortable={false} />
            <TextField source="seller_input_tax" label="GST Rate" sortable={false} />
            <ProductField source="total_product_value" label="Product Value" sortable={false}  />
            <CGSTField source="CGST" label="SGST" sortable={false}  />
            <SGSTField source="SGST" label="SGST" sortable={false}  />
            <IGSTField source="IGST" label="IGST" sortable={false}  />
            <TotalTaxField source="total_tax" label="Total Tax" sortable={false}  />
            <FullNameField source="file_name" sortable={false} file_exist={this.file_exist} />
        </Datagrid>
    </List>
)
}}

export default connect(null, {
    showNotification: showNotificationAction
})(hsnReportList);