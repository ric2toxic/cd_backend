import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import custom from '../../custom/custom'
import { Link } from 'react-router-dom'
import $ from 'jquery'
import Api from '../../api/Api'

import Dialog from '@material-ui/core/Dialog'
import DialogContent from '@material-ui/core/DialogContent'
import DialogTitle from '@material-ui/core/DialogTitle'
import DialogActions from '@material-ui/core/DialogActions'
import withMobileDialog from '@material-ui/core/withMobileDialog'

import { ConfirmPopupOpen } from '../../actions/CartAction'
import { DesignPopupOpen } from '../../actions/LoginAction'

class Item extends Component {
    
   constructor(props){
        super(props);
        this.design_popup = this.design_popup.bind(this);
        this.remove = this.remove.bind(this);
        this.addToShortlist = this.addToShortlist.bind(this);
        this.renderSets = this.renderSets.bind(this);
        this.updateSetPopup = this.updateSetPopup.bind(this);
        this.changeSet = this.changeSet.bind(this);
        this.changeSetThroughPopup = this.changeSetThroughPopup.bind(this);
        this.toggleCommentBox = this.toggleCommentBox.bind(this);
        this.editComment = this.editComment.bind(this);
        this.submitComment = this.submitComment.bind(this);
        this.quantity_popup_close     = this.quantity_popup_close.bind(this);

        this.state = {
            QuantityPopup: false,
        }

        if(this.props.data.wishlist){
            this.wishlist_icon = 'fa fa-heart-o custom_heart';
            this.wishlist      = true;
        }
        else{
            this.wishlist_icon = 'fa fa-heart-o';
            this.wishlist      = false;
        }
    }

    componentDidMount(){

        if(this.props.data.stock === false){
            $('#last_out_of_stock_product').val(this.props.data.key);
        }
        if(this.props.data.quantity_reduced){
            $('#last_quantity_reduced_product').val(this.props.data.key);
        }
        if(this.props.data.moq_error){
            $('#last_moq_error_product').val(this.props.data.key);
        }
        
        if(this.props.data.comment !== ''){
            this.toggleCommentBox();
        }
        this.renderSets();
    }


    componentDidUpdate() {
       this.renderSets();

        if(this.props.data.stock === false){
            $('#last_out_of_stock_product').val(this.props.data.key);
        }
        if(this.props.data.quantity_reduced){
            $('#last_quantity_reduced_product').val(this.props.data.key);
        }
        if(this.props.data.moq_error){
            $('#last_moq_error_product').val(this.props.data.key);
        }
    } 

 quantity_popup_close()
 {
    this.setState({QuantityPopup:false}); 
 } 

 design_popup()
 {
   var self = this;
   this.props.dispatch(DesignPopupOpen(1));
   setTimeout(function(){ 
      $('#want_design_warning').html("");
      $('#want_design_product_id').val(self.props.data.product_id);
      if(custom.getCookie("customer_mobile") !== '')
      {
         $("#design_customer_mobile").hide();
      }
      else
      {
        $("#design_customer_mobile").show();
      }
    }, 200);
 }

    editComment(){
        var commentboxid = "comment_"+this.props.data.key.replace(/=/g, "")+this.props.data.product_id;
        var comment_id = "comment_div_"+this.props.data.key.replace(/=/g, "")+this.props.data.product_id;
        var comment_data_id = "comment_data_"+this.props.data.key.replace(/=/g, "")+this.props.data.product_id;
        var submit_id = "submit_comment_"+this.props.data.key.replace(/=/g, "")+this.props.data.product_id;
        $('#'+comment_id).html('<div class="width_80" style>' +
            '<textarea maxlength="300" id="'+comment_data_id+'" class="password_box comment_box" rows="3" placeholder="Enter your Comment" >'+this.props.data.comment+'</textarea>' +
            '<button type="button" '+
            ' class= "btn deliver_btn comment_cancel_btn pull-right" id="cancel_'+submit_id+'">Cancel</button>' +
            '<button type="button" id="'+submit_id+'"'+
            ' class= "btn deliver_btn comment_btn pull-right">Save</button>' +
            '</div>');
        $('#'+submit_id).click(function(){ this.submitComment(); }.bind(this));
        $('#cancel_'+submit_id).click(function(){ $('#'+commentboxid).remove(); this.toggleCommentBox(); }.bind(this));
    }

    submitComment(){
        var comment_id = "comment_div_"+this.props.data.key.replace(/=/g, "")+this.props.data.product_id;
        var comment_data_id = "comment_data_"+this.props.data.key.replace(/=/g, "")+this.props.data.product_id;
        var comment = $('#'+comment_data_id).val();
        var cleanComment = comment.replace(/<\/?[^>]+(>|$)/g, "");
        if(cleanComment.length < 10){

            var modal_body = "Comment should have at least 10 characters.";
            var modal_footer = '<button type="button" class="btn popup_close_btn">OK</button>';
            
            this.props.dispatch(ConfirmPopupOpen(1));
            setTimeout(function(){ 
            $('#confirm_body').html(modal_body);
            $('#confirm_footer').html(modal_footer);
            }, 200);
            
            return;
        }
        this.props.data.comment = cleanComment;
        var edit_id = "edit_comment_"+this.props.data.key.replace(/=/g, "")+this.props.data.product_id;
        $('#'+comment_id).html(
            '<div class="comments_box_data width_80" style="font-size: 14px; padding-bottom:10px;">'+
            '<strong>Comment:-</strong>'+this.props.data.comment+
            '&nbsp;&nbsp;<a id="'+edit_id+'" href="javascript:void(0)" style="color: #1b6d85">&nbsp;<i class="fa fa-pencil" aria-hidden="true"></i>Edit</a>'+
            '</div>'
        );
        $('#'+edit_id).click(function(){ this.editComment(); }.bind(this));
        var key = this.props.data.key;
        this.props.addComment(key, cleanComment);
    }

    toggleCommentBox(){
        var commentboxid = "comment_"+this.props.data.key.replace(/=/g, "")+this.props.data.product_id;
        var comment_box = document.getElementById(commentboxid);
        if(comment_box == null) {
            var target = document.getElementById(this.props.data.key+this.props.data.product_id);
            var newElement = document.createElement('fieldset');
            newElement.setAttribute('id',commentboxid);
            var newtd = document.createElement('div');
            newtd.setAttribute('class', "col-sm-12");
            var comment_id = "comment_div_"+this.props.data.key.replace(/=/g, "")+this.props.data.product_id;
            var comment_data_id = "comment_data_"+this.props.data.key.replace(/=/g, "")+this.props.data.product_id;
            var submit_id = "submit_comment_"+this.props.data.key.replace(/=/g, "")+this.props.data.product_id;
            var edit_id = "edit_comment_"+this.props.data.key.replace(/=/g, "")+this.props.data.product_id;
            newtd.setAttribute('id', comment_id);
            if(this.props.data.comment !== ''){
                newtd.innerHTML = '<div class="comments_box_data width_80" style="font-size: 14px; padding-bottom:10px;">'+
                    '<strong>Comment:-</strong>'+this.props.data.comment+
                    '&nbsp;&nbsp;<a id="'+edit_id+'" href="javascript:void(0)" style="color: #1b6d85">&nbsp;<i class="fa fa-pencil" aria-hidden="true"></i>Edit</a>'+
                    '</div>';
            }else{
                newtd.innerHTML = '<div class="width_80" >' +
                    '<textarea maxlength="300" id="'+comment_data_id+'" class="password_box comment_box" rows="3" placeholder="Enter your Comment" >'+this.props.data.comment+'</textarea>' +
                    '<button type="button" '+
                    ' class= "btn deliver_btn comment_btn pull-right" id="cancel_'+submit_id+'">Cancel</button>' +
                    '<button type="button" id="'+submit_id+'"'+
                    ' class= "btn deliver_btn comment_cancel_btn pull-right">Save</button>' +
                    '</div>';

            }
            newElement.appendChild(newtd);
            target.parentNode.insertBefore(newElement, target.nextSibling);
            var newtr = document.createElement('div');
            newtr.style.display = 'none';
            newElement.parentNode.insertBefore(newtr,newElement.nextSibling);
            $("#"+commentboxid).prev().css("border-bottom",'0px');
            $("#"+commentboxid).css("border-bottom",'1px solid #ddd');
            $('#'+edit_id).click(function(){ this.editComment(); }.bind(this));
            $('#'+submit_id).click(function(){ this.submitComment(); }.bind(this));
            $('#cancel_'+submit_id).click(function(){ $("#"+commentboxid).prev().css("border-bottom",'');$('#'+commentboxid).remove(); });
        }
        else{
            $('#'+commentboxid).remove();
        }

    }       

    renderSets(){
        var selectList = document.getElementById("sets_" + this.props.data.key.replace(/=/g, "")+this.props.data.product_id);
        selectList.innerHTML="";
        //document.getElementById("sets_value_"+this.props.data.key.replace(/=/g, "")).value = this.props.data.quantity;
         var option = ''; 
        if (selectList.addEventListener) {
            selectList.addEventListener("change", this.changeSet, false);
        } else {
            selectList.attachEvent('onchange', this.changeSet);
        }

        var limit = 5;
        if (this.props.data.quantity >= limit) {
            option = document.createElement("option");
            option.text = this.props.data.quantity+" "+this.props.data.super_unit;
            option.value = this.props.data.quantity;
            option.selected = true;
            selectList.appendChild(option);
            option = document.createElement("option");
            option.text = "change";
            option.value = "0"; // It(value = 0) means we need to open popup to update number of sets
            selectList.appendChild(option);
        }
        else {
            if( this.props.data.stock_quantity < limit ) limit = this.props.data.stock_quantity;
           
            for (var i = 1; i <= limit; i++) {
                option = document.createElement("option");
                option.value = i;
                option.text = i + " "+this.props.data.super_unit;
                if (i === this.props.data.quantity)
                    option.selected = true;
                selectList.appendChild(option);
            }


            if( this.props.data.stock_quantity > limit ) {
                option = document.createElement("option");
                option.text = "more";
                option.value = 0;
                selectList.appendChild(option);
            }

            if( this.props.data.quantity > limit ) {
                option = document.createElement("option");
                option.text = this.props.data.quantity+" "+this.props.data.super_unit;
                option.value = this.props.data.quantity ;
                selectList.appendChild(option);
            }

        }

        if(this.props.data.stock === false && this.props.data.error_store_limit === false){
            selectList.disabled = true;
        }
        else{
            selectList.disabled = false;
        }

    }

    changeSet(){

        var selectList = document.getElementById("sets_"+this.props.data.key.replace(/=/g, "")+this.props.data.product_id);
        var qty = selectList.value;
        if(qty === '0')
        {
            selectList.value = this.props.data.quantity; // show current value in drop down list.
            var key      = this.props.data.key+this.props.data.product_id;
            var quantity = this.props.data.quantity;
            this.setState({QuantityPopup:true});
            
            setTimeout(function(){ 
             document.getElementById("sets_value_"+key.replace(/=/g, "")).value = quantity;
             $('.sets_order_notes').html('');
            }, 200);

            return;
        }
        if( $.isEmptyObject(this.props.data.option) && (qty < this.props.data.minimum) ){
            selectList.value = this.props.data.quantity;
            var modal_body = "Minimum Quantity required to buy this product is "+this.props.data.minimum;
            var modal_footer = '<button type="button" class="btn popup_close_btn">OK</button>';
            
            this.props.dispatch(ConfirmPopupOpen(1));
            setTimeout(function(){ 
            $('#confirm_body').html(modal_body);
             $('#confirm_footer').html(modal_footer);
            }, 200);
            return;
        }

        this.props.data.quantity = qty;
        this.props.updateItem(this.props.data);
    }
    
   changeSetThroughPopup(){
        var qty = document.getElementById("sets_value_"+this.props.data.key.replace(/=/g, "")+this.props.data.product_id).value;
        if( (!isNaN(parseFloat(qty)) && isFinite(qty)) && qty > 0  ){

            if( parseInt(qty, 10) > parseInt(this.props.data.stock_quantity, 10))
            {
                console.log(this.props.data.stock_quantity);
                $('.sets_order_notes').html("*only "+this.props.data.stock_quantity+" "+this.props.data.super_unit+" are available.");
                return;
            }
            if( $.isEmptyObject(this.props.data.option) && (qty < this.props.data.minimum) ){
                $('.sets_order_notes').html("*minimum quantity required to buy this product is "+this.props.data.minimum);
                return;
            }
            this.setState({QuantityPopup:false});
            this.props.data.quantity = qty;
            this.props.updateItem(this.props.data);
        }
        else{
            $('.sets_order_notes').html("*please enter a valid value.");
            return;
        }

    }   

    updateSetPopup(){

        var sets_value_id = "sets_value_"+this.props.data.key.replace(/=/g, "")+this.props.data.product_id;
        return (
         <Dialog
             fullScreen={false}
             open={this.state.QuantityPopup}
             aria-labelledby="responsive-dialog-title"
             className="login_register_popup"
            >
            <button type="button" className="popup_close close" onClick={this.quantity_popup_close}>&times;</button>
            <DialogTitle className="address_popup_head popup-title">
              <h4 className="modal-title">Enter {this.props.data.super_unit}</h4>
            </DialogTitle>
       
            <DialogContent className="modal-body">
              <div className="mobile_details_panel">
                    <div className="col-sm-6 nopadding" style={{marginLeft:'5%'}}>
                        <input id={sets_value_id} className="password_box sets_input" style={{padding:'7px'}} type="text" placeholder="Sets" alt="Sets"/>
                    </div>

                    <div className="col-sm-4 nopadding">
                      <button type="button" onClick={this.changeSetThroughPopup} className="btn deliver_btn pull-right" style={{fontSize:'15px'}} >Update</button>
                    </div>

                    <div className="clearfix"></div>
                    <div className="sets_order_notes text-danger" style={{marginLeft:'5%'}}>*{this.props.data.stock_quantity} Sets are available</div>
                </div>
            </DialogContent>

            <DialogActions>
            </DialogActions>

         </Dialog>
        );
    }    

   remove(){
    if(this.props.data.stock === false){
      this.props.removeItem(this.props.data);
    }
    else
    {
      var modal_body = "Are you sure to remove item from cart?";
      var modal_footer = '<button type="button" data-dismiss="modal" class="btn cart_btn" id="remove_item">Yes</button>'+
        '<button type="button" data-dismiss="modal" class="btn popup_close_btn">No</button>';
      
       var self = this; 
       this.props.dispatch(ConfirmPopupOpen(1));
       setTimeout(function(){ 
            $('#confirm_body').html(modal_body);
             $('#confirm_footer').html(modal_footer);
        }, 200);
      
      $(document).delegate('#remove_item', 'click', function(e)
        { 
          self.props.dispatch(ConfirmPopupOpen(0));  
          self.props.removeItem(self.props.data);
        });

      }
    }

    addToShortlist(){
        if(this.wishlist){

          var modal_body = "Product already added to your short list";
          var modal_footer = '<button type="button" data-dismiss="modal" class="btn popup_close_btn">OK</button>';
      
          this.props.dispatch(ConfirmPopupOpen(1));
          setTimeout(function(){ 
            $('#confirm_body').html(modal_body);
             $('#confirm_footer').html(modal_footer);
           }, 200);

        }
        else{
            this.props.addToWishlist(this.props.data);
            this.wishlist = true;
            this.wishlist_icon = 'fa fa-heart-o custom_heart';
        }

    } 

    render() {

        var self = this;
        var style = {
            fontSize: 13
        };
        var item_background = {};
        if(this.props.data.stock === false){
            item_background = {
                background:'#ffe6e6',
                borderBottom: '1px solid #ccc'
            }
        }
        else if(this.props.data.quantity_reduced){
            item_background = {
                background:'#e6e6ff'
            }
        }
        else if(this.props.data.moq_error === true){
            item_background = {
                background:'#f7f5de'
            }
        }

        return (<div className="cart_table" style={item_background}>
                              <fieldset className="product_section" id={this.props.data.key+this.props.data.product_id}>
                                <div className="col-xs-3 nopadding product_img_box">
                                   <Link to={Api.folder_path+this.props.data.href}>
                                     <img className="img-thumbnail" src={this.props.data.thumb} alt={this.props.data.name} title={this.props.data.name} width="74" height="111" />
                                  </Link>
                                </div>
                                <div className="col-xs-9 product_dec">
                                     <Link to={Api.folder_path+this.props.data.href}>
                                     {this.props.data.name}
                                    </Link>
                                        <br />
                                        <span style={style}>Product Code: {this.props.data.model}</span>
                                            <br />
                                        <span style={style}>{this.props.data.set_description}</span> 
                                        <br />
                                        
                                        {!$.isEmptyObject(this.props.data.option)?
                                         <div>
                                            {this.props.data.option.map(function(option,key){
                                              return <div key={key}> {option.name} : {option.value} </div>
                                             })}
                                         </div>
                                       : ''
                                         }

                                        <br />
                       
                                         <div className="product-card_rating product-card_location">
                                          <span className="label label-warning">from {this.props.data.pickup_city}</span>
                                         </div>

                                         {this.props.data.is_sor_enabled ?
                                           <div className="blue_bg_detail">
                                            <div ><img className="round_box" src={Api.cdn_url+"sor_box.png"} alt="sor_box" />
                                           <div className="buyback_text"><span>{this.props.data.sor_enabled_text}</span></div>
                                           </div>
                                           </div> 
                                         : ''}

                                      <br />
                       
                        { this.props.data.non_returnable === true ?
                            <div><div className="red_text" style={{fontSize:'13px'}}><strong>{this.props.language.text_non_returnable}</strong></div></div>
                         :''
                        }

                        { this.props.data.exp_dispatch_date !== '' ?
                            <div><div className="red_text" style={{fontSize:'13px'}}><strong>{this.props.data.exp_dispatch_date}</strong></div></div>
                        : ''
                        }

                        { this.props.data.store_pickup === true ?
                            <div><div className="text-warning" style={{fontSize:'13px'}}><strong>{this.props.language.text_store_pickup}</strong></div></div>
                        :''
                        }

                        { this.props.data.wrong_store === true ?
                            <div><div className="red_text" style={{fontSize:'13px'}}><strong>{this.props.language.error_wrong_store}</strong></div></div>
                        : ''
                        }

                        { this.props.data.error_store_limit === true ?
                            <div><div className="red_text" style={{fontSize:'13px'}}><strong>{this.props.data.error_store_limit_msg}</strong></div></div>
                        : ''
                        }

                        { this.props.data.cod_available === 0 ?
                            <label className="not_available_cod_new" style={{position:'relative'}}></label>
                        : ''
                        }
                        </div>
                        </fieldset>

                        <div className="product_dec_bottom">
                           <div className="col-xs-12">
                            <div className="col-xs-8 nopadding">
                                {this.props.data.stock !== false?
                                    <div className="price_breakup_text" onClick={this.toggleCommentBox}>
                                        <i className="fa fa-commenting-o" aria-hidden="true"></i> 
                                            {this.props.language.text_comment}
                                     </div>
                                     : ''  
                                }
                            </div>

                            <div className="col-xs-3 nopadding pull-right">
                                <div>QUANTIY</div>
                                <div className="selectdiv ">
                                    <label>
                                    <select className="select_set_box" id={"sets_"+this.props.data.key.replace(/=/g, "")+this.props.data.product_id}>
                                    </select>
                                    </label>
                                </div>
                                {this.updateSetPopup()}
                                </div>
                             </div>
                        </div>


                            {this.props.data.stock === false && this.props.data.error_store_limit === false ?
                               
                              <div className="price_breakup_table col-xs-12">
                                    <div className="row bottem_line">
                                        <div className="col-xs-6 order_summary_table-left"><button type="button" className="btn"  style={{fontSize:'13px',background:'#f03140',color:'white',padding:'6px 20px'}} ><span>{this.props.language.text_out_of_stock}</span></button></div>
                                        <div className="col-xs-6 order_summary_table-right"><button type="button" onClick={this.design_popup} className="btn button_i_want_design"  style={{fontSize:'13px',background:'#17319f',color:'white',padding:'6px 20px'}} ><span>{this.props.language.i_want_this_design}</span></button></div>
                                    </div>
                                    <div className="row text_red">
                                    <div className="col-xs-12 order_summary_table-left">
                                       {!$.isEmptyObject(this.props.data.option)?

                                           this.props.data.option.map(function(option,key){
                                          return <div key={key} style={{fontSize:'14px',marginTop:'4%'}}> {option.name} {option.value} &nbsp;is OUT OF STOCK! Other {option.name}s are available.
                                             <br />To add other {option.name}s, <Link to={Api.folder_path+self.props.data.href}>click here</Link>
                                           </div>
                                              })

                                        :''
                                        }
                                     </div>   
                                    </div>

                                </div> 

                              :  <div className="price_breakup_table col-xs-12">
                                    <div className="row bottem_line">
                                        <div className="col-xs-6 order_summary_table-left">NUMBER OF PIECES</div>
                                        <div className="col-xs-6 order_summary_table-right"> {this.props.data.piece_in_set} {this.props.data.base_unit} </div>
                                    </div>
                                    <div className="row text_red">
                                        <div className="col-xs-6 order_summary_table-left">PRICE PER PIECE</div>
                                        <div className="col-xs-6 order_summary_table-right">  {$('<div/>').html(this.props.data.price_per_piece).text()}/{this.props.data.base_unit} <br/>+ {$('<div/>').html(this.props.data.tax_rate).text()} </div>
                                    </div>
                                     <div className="row bottem_line text_red">
                                        <div className="col-xs-6 order_summary_table-left">AMOUNT</div>
                                        <div className="col-xs-6 order_summary_table-right"> {$('<div/>').html(this.props.data.total).text()} </div>
                                    </div>
                                    <div className="row">
                                        <div className="col-xs-6 order_summary_table-left">GST</div>
                                        <div className="col-xs-6 order_summary_table-right"> {$('<div/>').html(this.props.data.tax).text()} </div>
                                    </div>                                                                      
                                </div>
                             }
                                <div className="col-xs-12 bottom_btn">
                                  <div className="col-xs-6 remove"><button onClick={this.remove}><span><i className="fa fa-trash-o"></i></span> Remove</button></div>
                                  <div className="col-xs-6 nopadding"><button onClick={this.addToShortlist}><span><i className={this.wishlist_icon} aria-hidden="true"></i>
                                  </span> Add to shortlist</button></div>
                                </div>
                                <div className="clearfix"></div>
                            </div>);
    }
}



function mapStateToProps(state){
  return {
    actions: bindActionCreators(DesignPopupOpen,ConfirmPopupOpen)
  };
}

export default withMobileDialog()(connect(mapStateToProps)(Item));
