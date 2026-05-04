import React, { Component } from 'react'
import { connect } from 'react-redux'
import Button from 'material-ui/Button'
import { showNotification as showNotificationAction } from 'react-admin'
import { push as pushAction } from 'react-router-redux'
import {Table} from './table'
import  '../index.css'
import $ from 'jquery'
import { revertGoodsBySeller } from '../../../actions/OrderAction'

 class NotGiven  extends Component {
   constructor(props)
   {
     super(props);
     
     this.revertGoodsBySeller = this.revertGoodsBySeller.bind(this);
     this.setChanges       = this.setChanges.bind(this);
     this.associate        = this.associate.bind(this);

     this.state = {
     order_product_ids:[],
     order_id:this.props.order_id,
     suborder_id:this.props.suborder_id,
     seller_id:false,
     };
   }

   revertGoodsBySeller()
    {
      const { push, showNotification } = this.props;
      if(this.state.order_product_ids.length > 0)
      {
        $(".invoice_submit_btn").html("Please wait...");
        var form_data = new FormData();
        form_data.append('order_product_ids', this.state.order_product_ids.join());
        form_data.append('order_id', this.state.order_id);
        form_data.append('suborder_id', this.state.suborder_id);
        form_data.append('seller_id', this.state.seller_id);
        var response = revertGoodsBySeller(form_data);
        response.then(function(data){
          if(data.data.error === 0)
          {
            $(".invoice_submit_btn").html("Revert Goods");
            showNotification('Order revert successfully');
            push('/orders/getPickpupOrderRequested');
          }
         else
         {
           $(".invoice_submit_btn").html("Revert Goods");
           showNotification('Error:'+data.data.message, 'warning');
         } 
          
        })
        .catch(function() {
          showNotification('Error: data fetch error', 'warning');
        });
     }
     else
     {
      showNotification('Error: please select atleast one product', 'warning');
     }
 
    }
   
associate(order_data, checked)
{
  let self = this;
  var sibling_associates     = order_data.props.table_data.sibling_associates;
  this.setChanges(order_data, checked);

  let associate_order_data;
  let seller_id;

  $.map(sibling_associates, function(order_product, index) {
   
   if(order_product !== order_data.props.table_data.order_product_id)
    {
      seller_id            = $("#associate_"+order_product).attr('data-seller_id');

      associate_order_data = {order_product_id:order_product, seller_id:seller_id};
      
      setTimeout(function(){ self.setChanges(associate_order_data, checked, 1); }, 50);
    }
  });
}


setChanges(order_data, checked, combo_product=0){
    var order_product_id = 0;
    var seller_id = 0;

    if(combo_product)
    {
       order_product_id = order_data.order_product_id;
       seller_id = order_data.seller_id;

    }
    else
    {
      order_product_id = order_data.props.table_data.order_product_id;
      seller_id = order_data.props.table_data.seller_id;
    } 

    let order_product_ids  = this.state.order_product_ids;

    if(checked)
    {
      order_product_ids.push(order_product_id);
    }
    else
    {
      order_product_ids = $.grep(order_product_ids, function( a ) {
          return a !== order_product_id;
        });
    }

     this.setState({order_product_ids:order_product_ids});
     this.setState({seller_id:seller_id});
  }
  

 render(){
 let self = this; 
  let combo_product=-1;
return(<div className="pending_request"> 
                  
                     <table className="MuiTable-root">
                       <thead>
                         <tr className="MuiTableRow-root">
                         <th className="MuiTableCell-root MuiTableCell-head  Datagrid-headerCell">Action</th>
                         <th className="MuiTableCell-root MuiTableCell-head  Datagrid-headerCell">Product</th>
                         <th className="MuiTableCell-root MuiTableCell-head  Datagrid-headerCell">SKU</th>
                         <th className="MuiTableCell-root MuiTableCell-head  Datagrid-headerCell"> Set Description</th>
                         <th className="MuiTableCell-root MuiTableCell-head  Datagrid-headerCell">Total Pieces</th>
                         <th className="MuiTableCell-root MuiTableCell-head  Datagrid-headerCell"> Price / Piece</th>
                         <th className="MuiTableCell-root MuiTableCell-head  Datagrid-headerCell">Product Amount</th>
                         <th className="MuiTableCell-root MuiTableCell-head  Datagrid-headerCell">Tax Rate</th>
                         <th className="MuiTableCell-root MuiTableCell-head  Datagrid-headerCell">Tax Amount</th>
                         <th className="MuiTableCell-root MuiTableCell-head  Datagrid-headerCell">Total Amount</th>
                         </tr>
                        </thead>
                        <tbody>
                        {
                          $.map(this.props.pending_orders, function(product_arr, index) {
                            return  $.map(product_arr, function(product, index) {
                                 combo_product = $.inArray(product.order_product_id, product.sibling_associates );
                                 return <Table table_data={product} combo_product={combo_product} order_id={self.props.order_id} suborder_id={self.props.suborder_id}  setChanges={combo_product >= 0 ? self.associate :self.setChanges} />
                              })
                          })
                        }
                        </tbody>
                        </table>
                         
                         <hr />
                         <div className="pickup_done_records_box">
                          <Button primary={true} className="invoice_submit_btn" onClick={this.revertGoodsBySeller}>Revert Goods</Button>
                         </div>
                        </div>)
}}

export default connect(null, {
    showNotification: showNotificationAction,
    push: pushAction,
})(NotGiven);