import React, { Component } from 'react'
import { connect } from 'react-redux'
import TextField from 'material-ui/TextField'
import Select from 'material-ui/Select'
import MenuItem from 'material-ui/Menu/MenuItem'
import Done from '@material-ui/icons/Done'
import Replay from '@material-ui/icons/Replay'
import Clear from '@material-ui/icons/Clear'
import Create from '@material-ui/icons/Create'
import AirportShuttle from '@material-ui/icons/AirportShuttle'
import Button from 'material-ui/Button'

import Dialog from 'material-ui/Dialog';
import DialogActions from 'material-ui/Dialog/DialogActions';
import DialogContent from 'material-ui/Dialog/DialogContent';
import DialogContentText from 'material-ui/Dialog/DialogContentText';
import DialogTitle from 'material-ui/Dialog/DialogTitle'

import { showNotification as showNotificationAction } from 'react-admin'
import { push as pushAction } from 'react-router-redux'
import  '../index.css'
import $ from 'jquery'
import  Table from './table'
import { splitOrderProducts } from '../../../actions/OrderAction'

  class Panding  extends Component {
   constructor(props)
   {
     super(props);
     this.SplitOrderSubmit = this.SplitOrderSubmit.bind(this);
     this.handleChange     = this.handleChange.bind(this);
     this.undoChanges      = this.undoChanges.bind(this);
     this.setChanges       = this.setChanges.bind(this);
     this.associate        = this.associate.bind(this);
     this.undo_associate   = this.undo_associate.bind(this);
     this.dailogClose      = this.dailogClose.bind(this);
     this.dailogAgree      = this.dailogAgree.bind(this);
     this.dailogOpen       = this.dailogOpen.bind(this);


     this.state = {
     invoice_number: '',
     invoice_date: '',
     SELLER_APPROVED:[],
     SELLER_NOT_SUPPLIED:[],
     SELLER_LATER_DISPATCH:[],
     SELLER_PARTIAL:[],
     value:[],
     product_ids:[],
     order_product_ids:[],
     order_id:this.props.order_id,
     suborder_id:this.props.suborder_id,
     changes: false,
     information_agree: false,
     total_amount: 0,
     dialog_open: false
     };
   }

   dailogOpen()
   {
     if(this.state.SELLER_APPROVED.length === 0 && this.state.SELLER_PARTIAL.length === 0 && this.state.SELLER_NOT_SUPPLIED.length === 0)
       {
          this.setState({dialog_open:true});
         setTimeout(function(){ $("#alert-dialog-description").html("Do you really want to mark ALL Products as Later Dispatch?"); }, 100);
       }

       else if(this.state.SELLER_APPROVED.length === 0 && this.state.SELLER_PARTIAL.length === 0 && this.state.SELLER_LATER_DISPATCH.length === 0)
       {
          this.setState({dialog_open:true});
          setTimeout(function(){ $("#alert-dialog-description").html("If seller frequently mark SKU 'Out of Stock' then Wholesalebox may block seller"); }, 100);
       }
       else
       {
         this.SplitOrderSubmit();
       }
   }

   dailogClose()
   {
     this.setState({dialog_open:false});
   }

   dailogAgree()
   {
     this.setState({dialog_open:false});
     this.SplitOrderSubmit();
   }

   SplitOrderSubmit()
    {
         var changes          = this.state.changes;
         const { push, showNotification } = this.props; 
         $(".invoice_submit_btn").html("Please wait...");
         var form_data = new FormData();
        form_data.append('invoice_number', this.state.invoice_number);
        form_data.append('invoice_date', this.state.invoice_date);
        form_data.append('invoice_date', this.state.invoice_date);
        form_data.append('SELLER_APPROVED', this.state.SELLER_APPROVED);
        form_data.append('SELLER_PARTIAL', this.state.SELLER_PARTIAL);
        form_data.append('SELLER_NOT_SUPPLIED', this.state.SELLER_NOT_SUPPLIED);
        form_data.append('SELLER_LATER_DISPATCH', this.state.SELLER_LATER_DISPATCH);
        form_data.append('product_ids', this.state.product_ids);
        form_data.append('order_id', this.state.order_id);
        form_data.append('suborder_id', this.state.suborder_id);
        form_data.append('changes', changes);
        var response = splitOrderProducts(form_data);
        response.then(function(data){
        $(".invoice_submit_btn").html("Submit Invoice Detail");
        if(data.data.error === 0)
        {
          showNotification('Order approved successfully');
          push('/orders/getPickpupOrderRequested');
        }
        else
        {
          showNotification('Error:'+data.data.error_msg, 'warning');
        }
        })
        .catch(function() {
           showNotification('Error: data fetch error', 'warning');
        }); 
    }
   


handleChange(field, e){ 
    this.setState({
      [field]: e.target.value,
    });
  }


associate(order_data, field, no_of_piece=1)
{
  let self = this;
  var sibling_associates     = order_data.props.table_data.sibling_associates;
  this.setChanges(order_data, field, no_of_piece);

  let associate_order_data;
  let product_id;
  let price_per_piece;
  let seller_input_tax;
  let associate_no_of_piece;

  $.map(sibling_associates, function(order_product, index) {
   
   if(order_product !== order_data.props.table_data.order_product_id)
    {
      product_id            = $("#associate_"+order_product).attr('data-product_id');
      price_per_piece       = $("#associate_"+order_product).attr('data-price_per_piece');
      seller_input_tax      = $("#associate_"+order_product).attr('data-seller_input_tax');
      associate_no_of_piece = $("#associate_"+order_product).attr('data-no_of_piece');

      associate_order_data = {order_product_id:order_product, product_id:product_id, price_per_piece:price_per_piece, seller_input_tax:seller_input_tax, no_of_piece:associate_no_of_piece};
      
      setTimeout(function(){ self.setChanges(associate_order_data, field, no_of_piece, 1); }, 50);
    }
  });
}

undo_associate(order_data)
{
  let self = this;
  var sibling_associates     = order_data.props.table_data.sibling_associates;
  this.undoChanges(order_data);

  let associate_order_data;
  let product_id;
  let price_per_piece;
  let seller_input_tax;
  let associate_no_of_piece;
  let total_amount;

  $.map(sibling_associates, function(order_product, index) {
   
   if(order_product !== order_data.props.table_data.order_product_id)
    {
      product_id            = $("#associate_"+order_product).attr('data-product_id');
      price_per_piece       = $("#associate_"+order_product).attr('data-price_per_piece');
      seller_input_tax      = $("#associate_"+order_product).attr('data-seller_input_tax');
      associate_no_of_piece = $("#associate_"+order_product).attr('data-no_of_piece');
      total_amount          = $("#associate_"+order_product).attr('data-total_amount');

      associate_order_data = {order_product_id:order_product, product_id:product_id, price_per_piece:price_per_piece, seller_input_tax:seller_input_tax, no_of_piece:associate_no_of_piece, total_amount:total_amount};
      
      setTimeout(function(){ self.undoChanges(associate_order_data, 1); }, 50);
    }
  });
}


setChanges(order_data, field, no_of_piece=1, combo_product=0){
  
    var total_amount_invoice = 0;
    var order_product_id     = 0;
    var product_id           = 0;
    var price_per_piece      = 0;
    var seller_input_tax     = 0;
    var order_no_of_piece    = 0;

    if(combo_product)
    {
      order_product_id     = order_data.order_product_id;
      product_id           = order_data.product_id;
      price_per_piece      = order_data.price_per_piece;
      seller_input_tax     = order_data.seller_input_tax;
      order_no_of_piece    = order_data.no_of_piece; 
    
    }
    else
    {
      order_product_id     = order_data.props.table_data.order_product_id;
      product_id           = order_data.props.table_data.product_id;
      price_per_piece      = order_data.props.table_data.price_per_piece;
      seller_input_tax     = order_data.props.table_data.seller_input_tax;
      order_no_of_piece    = order_data.props.table_data.no_of_piece; 
    } 

     var total_amount         = this.state.total_amount;
      
     $("#checkbox_information_undo_"+order_product_id).removeClass("hidden");
     $(".checkbox_information_"+order_product_id).addClass("hidden");
     let value = { 'value': no_of_piece, 'product_id': product_id };
     
       let changes = {};
       if(this.state.changes)
       {
          changes  = $.parseJSON(this.state.changes);
       }

       if(!changes[order_product_id]){
          changes[order_product_id] = {};
       }
       changes[order_product_id] = {[field]: value}; 
       changes = JSON.stringify(changes);
       this.setState({changes:changes});

     let product_ids  = this.state.product_ids;
     product_ids.push(product_id);
     this.setState({product_ids:product_ids});

    let order_product_ids  = this.state.order_product_ids;
     order_product_ids.push(order_product_id);
     this.setState({order_product_ids:order_product_ids});

     let fields;
     if(field === 'SELLER_APPROVED')
     {
       fields = this.state.SELLER_APPROVED;
       fields.push(order_product_id);
       this.setState({SELLER_APPROVED:fields});
       $(".row_"+order_product_id).addClass("complete");
       
       total_amount_invoice = (price_per_piece*order_no_of_piece)+((price_per_piece*order_no_of_piece)*(seller_input_tax/100))
       total_amount         += Math.round(total_amount_invoice);
       this.setState({total_amount:total_amount});

     }
     if(field === 'SELLER_PARTIAL')
     {
       fields = this.state.SELLER_PARTIAL;
       fields.push(order_product_id);
       this.setState({SELLER_PARTIAL:fields});
       $(".row_"+order_product_id).addClass("edit");

       total_amount_invoice = (price_per_piece*no_of_piece)+((price_per_piece*no_of_piece)*(seller_input_tax/100))
       total_amount         += Math.round(total_amount_invoice);
       this.setState({total_amount});
       $("#total_amount_"+order_product_id).html(Math.round(total_amount_invoice));

     }
     if(field === 'SELLER_NOT_SUPPLIED')
     {
       fields = this.state.SELLER_NOT_SUPPLIED;
       fields.push(order_product_id);
       this.setState({SELLER_NOT_SUPPLIED:fields});
       $(".row_"+order_product_id).addClass("cancel");
     }
     if(field === 'SELLER_LATER_DISPATCH')
     {
       fields = this.state.SELLER_LATER_DISPATCH;
       fields.push(order_product_id);
       this.setState({SELLER_LATER_DISPATCH:fields});
       $(".row_"+order_product_id).addClass("processing");
     }

    if(this.props.total_product === this.state.order_product_ids.length)
    {
      if(this.state.SELLER_APPROVED.length === 0 && this.state.SELLER_PARTIAL.length === 0)
      {
        $(".invoice_box").addClass("hidden");
        $(".invoice_date_box").addClass("hidden");
      }
      else
      {
        $(".invoice_box").removeClass("hidden");
        $(".invoice_date_box").removeClass("hidden");
      }
      $(".pickup_done_records_box").removeClass("hidden");
    }

  }

undoChanges(order_data, combo_product=0){ 
    var total_amount_invoice = 0;
    var order_product_id     = 0;
    var product_id           = 0;
    var price_per_piece      = 0;
    var seller_input_tax     = 0;
    var no_of_piece          = 0;
    var product_total_amount = 0;

    if(combo_product)
    {
      order_product_id     = order_data.order_product_id;
      product_id           = order_data.product_id;
      price_per_piece      = order_data.price_per_piece;
      seller_input_tax     = order_data.seller_input_tax;
      no_of_piece          = order_data.no_of_piece;
      product_total_amount = order_data.total_amount;
    
    }
    else
    {
      order_product_id     = order_data.props.table_data.order_product_id;
      product_id           = order_data.props.table_data.product_id;
      price_per_piece      = order_data.props.table_data.price_per_piece;
      seller_input_tax     = order_data.props.table_data.seller_input_tax;
      no_of_piece          = order_data.props.table_data.no_of_piece;
      product_total_amount = order_data.props.table_data.total_amount; 
    } 

     var total_amount         = this.state.total_amount;
     var no_of_piece_old      = $("#quantity_"+order_product_id).val();

     $("#checkbox_information_undo_"+order_product_id).addClass("hidden");
     $(".checkbox_information_"+order_product_id).removeClass("hidden");
     $(".pickup_done_records_box").addClass("hidden");
     $(".row_"+order_product_id).removeClass("processing").removeClass("complete").removeClass("cancel").removeClass("edit");
     $("#quantity_"+order_product_id).prev("span").html(no_of_piece);

      let check = $.inArray( order_product_id, this.state.SELLER_APPROVED );

      if(check !== -1)
      {  
         total_amount_invoice = (price_per_piece*no_of_piece)+((price_per_piece*no_of_piece)*(seller_input_tax/100))
         total_amount         = total_amount - Math.round(total_amount_invoice);
         this.setState({total_amount});
      }
      else
      {
         check = $.inArray( order_product_id, this.state.SELLER_PARTIAL );
         if(check !== -1)
         {  
          total_amount_invoice = (price_per_piece*no_of_piece_old)+((price_per_piece*no_of_piece_old)*(seller_input_tax/100))
          total_amount         = total_amount - Math.round(total_amount_invoice);
          this.setState({total_amount});
          $("#total_amount_"+order_product_id).html(product_total_amount);
         }
      } 
   
      
      let field = 'SELLER_APPROVED';
      let value = 0;
      let changes = {};
       if(this.state.changes)
       {
          changes  = $.parseJSON(this.state.changes);
       }

       if(!changes[order_product_id]){
          changes[order_product_id] = {};
       }
       changes[order_product_id] = {[field]: value}; 
       changes = JSON.stringify(changes);
       this.setState({changes:changes}); 

      let product_ids  = this.state.product_ids;
       product_ids = $.grep(product_ids, function( a ) {
             return a !== product_id;
             });
       this.setState({product_ids:product_ids});

       let order_product_ids  = this.state.order_product_ids;
       order_product_ids = $.grep(order_product_ids, function( a ) {
             return a !== order_product_id;
             });
       this.setState({order_product_ids:order_product_ids});

       let fields;
       fields = this.state.SELLER_APPROVED;
       fields = $.grep(fields, function( a ) {
             return a !== order_product_id;
             });
       this.setState({SELLER_APPROVED:fields});    
    
       fields = this.state.SELLER_PARTIAL;
       fields = $.grep(fields, function( a ) {
             return a !== order_product_id;
             });
       this.setState({SELLER_PARTIAL:fields});

       fields = this.state.SELLER_NOT_SUPPLIED;
       fields = $.grep(fields, function( a ) {
             return a !== order_product_id;
             });
       this.setState({SELLER_NOT_SUPPLIED:fields});

       fields = this.state.SELLER_LATER_DISPATCH;
       fields = $.grep(fields, function( a ) {
             return a !== order_product_id;
             });
       this.setState({SELLER_LATER_DISPATCH:fields});
  }  

 render(){
 let self = this; 
 let combo_product=-1;
return(<div className="pending_request"> 

             <Dialog
                open={this.state.dialog_open}
                onClose={this.dailogClose}
                aria-labelledby="alert-dialog-title"
                aria-describedby="alert-dialog-description">
                <DialogTitle id="alert-dialog-title">Alert</DialogTitle>
                 <DialogContent>
                   <DialogContentText id="alert-dialog-description">
                    
                   </DialogContentText>
                  </DialogContent>
                  <DialogActions>
                  <Button onClick={this.dailogClose} color="primary"> Disagree</Button>
                  <Button onClick={this.dailogAgree} color="primary" autoFocus>Agree</Button>
                  </DialogActions>
               </Dialog>   
                    <p className="pd_approve_section">To generate an Invoice, Please approve below products using these options.</p>
                      <ul>
                        <li className="complete"><Done className="green" /> Complete (You are shipping all requested pieces)</li>
                        <li className="edit"><Create className="blue"  /> Partial(you can select how many pieces you will ship) </li>
                        <li className="cancel"><Clear className="red"  /> None (Out of stock. you will not ship this product) </li>
                        <li className="processing"><AirportShuttle className="orange"  /> Later Dispatch </li>
                        <li className="edit"><Replay className="blue"  /> Undo</li>
                      </ul>

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
                                 return <Table table_data={product} combo_product={combo_product} order_id={self.props.order_id} suborder_id={self.props.suborder_id}  setChanges={combo_product >=0 ? self.associate : self.setChanges} undoChanges={combo_product >= 0 ? self.undo_associate : self.undoChanges} information_agree={self.state.information_agree} />
                             
                              })
                          })
                        }
                        </tbody>
                        </table>
                        {this.state.total_amount > 0 ?
                         <div style={{width:'100%', textAlign:'right'}}>
                           <hr />
                         <b> Total Amount: {this.state.total_amount.toFixed(2)} </b>
                         </div>
                         : ''}
                        <hr />
                        <div className="pickup_done_records_box hidden">
                          <TextField type="text" id="invoice_number" name="invoice_number" label="Invoice Number"  className="invoice_box" value={this.state.invoice_number} onChange={this.handleChange.bind(this, "invoice_number")} />
                          <Select className="invoice_date_box"
                                  value={this.state.invoice_date}
                                  name="invoice_date"
                                  id="invoice_date"
                                   onChange={this.handleChange.bind(this, "invoice_date")}
                                  >
                                  {this.props.date_ranges.map((value, index) => {
                                    return (<MenuItem value={value}>{value}</MenuItem>)
                                   })}
                          </Select>
                          
                          <Button primary={true} className="invoice_submit_btn" onClick={this.dailogOpen}>Submit Invoice Detail</Button>
                         </div>


                        </div>)
}}

export default connect(null, {
    showNotification: showNotificationAction,
    push: pushAction,
})(Panding);