{/*
 * class Item: This will render the items
 * @Params: {
 *      data: {list of all products},
 *      language: language text for different fields
 *  }
 *
 * @author:Devendra Dhayal, Date-Added:20th May 2017, Date-Modified:20th May 2017
 *
 */}

class Item extends React.Component {
    constructor(props){
        super(props);

        this.changeSet = this.changeSet.bind(this);
        this.changeSetThroughPopup = this.changeSetThroughPopup.bind(this);
        this.updateSetPopup = this.updateSetPopup.bind(this);
        this.toggleCommentBox = this.toggleCommentBox.bind(this);
        this.editComment = this.editComment.bind(this);
        this.submitComment = this.submitComment.bind(this);
        this.renderSets = this.renderSets.bind(this);
        this.remove = this.remove.bind(this);
        this.addToShortlist = this.addToShortlist.bind(this);

        if(this.props.data.wishlist){
            this.wishlist = true;
        }
        else{
            this.wishlist = false;
        }
    }
    componentDidMount(){
        $('.button_i_want_design').click(function(){
            $('#want_design_product_id').val(this.props.data.product_id);
            $('#want_design_product_status').val(this.props.data.status);
            $('#want_design_warning').html("");
            $('#want_design').modal({
                backdrop: 'static',
                keyboard: false
            });
        }.bind(this));

        if(this.props.data.comment != ''){
            this.toggleCommentBox();
        }

        $('.sets_input').bind('keyup blur',function(){
            var node = $(this);
            node.val(node.val().replace(/[^0-9]/g,'') ); }
        );

        if(this.props.data.stock == false){
            $('#last_out_of_stock_product').val(this.props.data.key+this.props.data.product_id);
        }
        if(this.props.data.quantity_reduced){
            $('#last_quantity_reduced_product').val(this.props.data.key+this.props.data.product_id);
        }
        if(this.props.data.moq_error){
            $('#last_moq_error_product').val(this.props.data.key+this.props.data.product_id);
        }

        this.renderSets();
    }

    renderSets(){
        var selectList = document.getElementById("sets_" + this.props.data.key.replace(/=/g, "")+this.props.data.product_id);
        selectList.innerHTML="";
        document.getElementById("sets_value_"+this.props.data.key.replace(/=/g, "")+this.props.data.product_id).value = this.props.data.quantity;

        if (selectList.addEventListener) {
            selectList.addEventListener("change", this.changeSet, false);
        } else {
            selectList.attachEvent('onchange', this.changeSet);
        }

        var limit = 5;
        if ( this.props.data.quantity >= limit ) {
            var option = document.createElement("option");
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
                var option = document.createElement("option");
                option.value = i;
                option.text = i + " "+this.props.data.super_unit;
                if (i == this.props.data.quantity)
                    option.selected = true;
                selectList.appendChild(option);
            }

            if( this.props.data.stock_quantity > limit ) {
                var option = document.createElement("option");
                option.text = "more";
                option.value = 0;
                selectList.appendChild(option);
            }

            if( this.props.data.quantity > limit ) {
                var option = document.createElement("option");
                option.text = this.props.data.quantity+" "+this.props.data.super_unit;
                option.value = this.props.data.quantity ;
                selectList.appendChild(option);
            }

        }

        if(this.props.data.stock == false && this.props.data.error_store_limit == false){
            selectList.disabled = true;
        }
        else{
            selectList.disabled = false;
        }

    }
    componentDidUpdate() {
       this.renderSets();

       $('.button_i_want_design').click(function(){
            $('#want_design_product_id').val(this.props.data.product_id);
            $('#want_design_product_status').val(this.props.data.status);
            $('#want_design').modal({
                backdrop: 'static',
                keyboard: false
            });
        }.bind(this));

        if(this.props.data.stock == false){
            $('#last_out_of_stock_product').val(this.props.data.key+this.props.data.product_id);
        }
        if(this.props.data.quantity_reduced){
            $('#last_quantity_reduced_product').val(this.props.data.key+this.props.data.product_id);
        }
        if(this.props.data.moq_error){
            $('#last_moq_error_product').val(this.props.data.key+this.props.data.product_id);
        }
    }

    changeSet(){

        var selectList = document.getElementById("sets_"+this.props.data.key.replace(/=/g, "")+this.props.data.product_id);
        var qty = selectList.value;
        if(qty == 0 ){
            selectList.value = this.props.data.quantity; // show current value in drop down list.
            document.getElementById("sets_value_"+this.props.data.key.replace(/=/g, "")+this.props.data.product_id).value = this.props.data.quantity;
            $('.sets_order_notes').html('');
            $('#sets_popup_'+this.props.data.key.replace(/=/g, "")+this.props.data.product_id).modal({
                backdrop: 'static',
                keyboard: false
            });
            return;
        }
        if( jQuery.isEmptyObject(this.props.data.option) && (parseInt(qty) < parseInt(this.props.data.minimum)) ){
            selectList.value = this.props.data.quantity;
            $('#alert_body').html("Minimum Quantity required to buy this product is "+this.props.data.minimum);
            $('#alert_popup').modal({
                backdrop: 'static',
                keyboard: false
            });
            return;
        }

        this.props.data.quantity = qty;
        this.props.updateItem();
    }

    changeSetThroughPopup(){
        var qty = document.getElementById("sets_value_"+this.props.data.key.replace(/=/g, "")+this.props.data.product_id).value;

        if( (!isNaN(parseFloat(qty)) && isFinite(qty)) && qty > 0  ){
            if( parseInt(qty) > parseInt(this.props.data.stock_quantity)){
                $('.sets_order_notes').html("*only "+this.props.data.stock_quantity+" "+this.props.data.super_unit+" are available.");
                return;
            }
            if( jQuery.isEmptyObject(this.props.data.option) && (parseInt(qty) < parseInt(this.props.data.minimum)) ){
                $('.sets_order_notes').html("*minimum quantity required to buy this product is "+this.props.data.minimum);
                return;
            }
            $('#sets_popup_'+this.props.data.key.replace(/=/g, "")+this.props.data.product_id).modal('hide');
            this.props.data.quantity = qty;
            this.props.updateItem();
        }
        else{
            $('.sets_order_notes').html("*please enter a valid value.");
            return;
        }

    }

    updateSetPopup(){

        var sets_value_id = "sets_value_"+this.props.data.key.replace(/=/g, "")+this.props.data.product_id;
        var sets_popup_id = "sets_popup_"+this.props.data.key.replace(/=/g, "")+this.props.data.product_id;
        return (
            <div className="modal fade add_new_address" id={sets_popup_id} role="dialog">
                <div className="modal-dialog" style={{width:'28%',top:'20%',zIndex:'1050'}}>
                    <div className="modal-content">
                        <div className="modal-header address_popup_head">
                            <button type="button" className="close" data-dismiss="modal">&times;</button>
                            <h4 className="modal-title">Enter {this.props.data.super_unit}</h4>
                        </div>
                        <div className="modal-body padding_top_bottem" style={{minHeight:'100px'}}>
                            <div className="mobile_details_panel">
                                <div className="col-sm-6 nopadding" style={{marginLeft:'5%'}}>
                                    <input id={sets_value_id} className="password_box sets_input" style={{padding:'7px'}} type="text" placeholder="Sets" alt="Sets"/>
                                </div>
                                <div className="col-sm-4 nopadding">
                                    <button type="button" onClick={this.changeSetThroughPopup} className="btn deliver_btn pull-right" style={{margin:'10px 13px',fontSize:'15px'}} >Update</button>
                                </div>
                                <div className="clearfix"></div>
                                <div className="sets_order_notes text-danger" style={{marginLeft:'5%'}}>*{this.props.data.stock_quantity} Sets are available</div>
                                <div className="clearfix"></div>
                            </div>
                            <div className="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>
        );
    }

    editComment(){
        var commentboxid = "comment_"+this.props.data.key.replace(/=/g, "")+this.props.data.product_id;
        var comment_id = "comment_div_"+this.props.data.key.replace(/=/g, "")+this.props.data.product_id;
        var comment_data_id = "comment_data_"+this.props.data.key.replace(/=/g, "")+this.props.data.product_id;
        var submit_id = "submit_comment_"+this.props.data.key.replace(/=/g, "")+this.props.data.product_id;
        $('#'+comment_id).html('<div class="width_80" >' +
            '<textarea maxlength="300" id="'+comment_data_id+'" class="password_box" rows="2" placeholder="Enter your Comment" >'+this.props.data.comment+'</textarea>' +
            '<button type="button" '+
            ' class= "btn deliver_btn pull-right" style="margin-right: 2.9%" id="cancel_'+submit_id+'">Cancel</button>' +
            '<button type="button" id="'+submit_id+'"'+
            ' class= "btn deliver_btn pull-right" style="margin-right: 2.9%">Save</button>' +
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
            $('#alert_body').html("Comment should have at least 10 characters.");
            $('#alert_popup').modal({
                backdrop: 'static',
                keyboard: false
            });
            return;
        }
        this.props.data.comment = cleanComment;
        var edit_id = "edit_comment_"+this.props.data.key.replace(/=/g, "")+this.props.data.product_id;
        $('#'+comment_id).html(
            '<div class="comments_box_data width_80" style="font-size: 14px;">'+
            '<strong>Comment:-</strong>'+this.props.data.comment+
            '&nbsp;&nbsp;<a id="'+edit_id+'" href="javascript:void(0)" style="color: #1b6d85">&nbsp;<i class="fa fa-pencil" aria-hidden="true"></i>Edit</a>'+
            '</div>'
        );
        $('#'+edit_id).click(function(){ this.editComment(); }.bind(this));
        var key = this.props.data.key;
        $.ajax({
            url: 'api/cart/addComment',
            type: 'post',
            data: 'key=' + key + '&comment='+ cleanComment,
            dataType: 'json',


        })
        .fail(function(xhr,exception){
            console.log("Error: "+xhr.status+", exception: "+exception);
        });
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
            if(this.props.data.comment != ''){
                newtd.innerHTML = '<div class="comments_box_data width_80" style="font-size: 14px;">'+
                    '<strong>Comment:-</strong>'+this.props.data.comment+
                    '&nbsp;&nbsp;<a id="'+edit_id+'" href="javascript:void(0)" style="color: #1b6d85">&nbsp;<i class="fa fa-pencil" aria-hidden="true"></i>Edit</a>'+
                    '</div>';
            }else{
                newtd.innerHTML = '<div class="width_80" >' +
                    '<textarea maxlength="300" id="'+comment_data_id+'" class="password_box" rows="2" placeholder="Enter your Comment" >'+this.props.data.comment+'</textarea>' +
                    '<button type="button" '+
                    ' class= "btn deliver_btn pull-right" style="margin-right: 2.9%" id="cancel_'+submit_id+'">Cancel</button>' +
                    '<button type="button" id="'+submit_id+'"'+
                    ' class= "btn deliver_btn pull-right" style="margin-right: 2.9%">Save</button>' +
                    '</div>';

            }
            newElement.appendChild(newtd);
            target.parentNode.insertBefore(newElement, target.nextSibling);
            var newtr = document.createElement('div');
            newtr.style.display = 'none';
            newElement.parentNode.insertBefore(newtr,newElement.nextSibling);
            //$("#"+commentboxid).css("background-color", function(){
            //    return $("#"+commentboxid).prev().css("background-color");
            //});
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
    
    remove(){
		// If product is out of stock, then no need to confirm,directly deleting the item
		
		if(this.props.data.stock == false){
			
			this.props.removeItem(this.props.data);
		}
		else{
			
			var modal_body = "Are you sure to remove item from cart?";
			var modal_footer = '<button type="button" data-dismiss="modal" class="btn deliver_btn" id="remove_item">Yes</button>'+
				'<button type="button" data-dismiss="modal" class="btn popup_close_btn">No</button>';
			$('#confirm_body').html(modal_body);
			$('#confirm_footer').html(modal_footer);
			
			$('#confirm_popup').modal({
                backdrop: 'static',
                keyboard: false
            });

            $('#remove_item').click(function(){
                this.props.removeItem(this.props.data);
            }.bind(this));
			
		}
    }
    
    addToShortlist(){

        /*var modal_body = "Are you sure to add item to shortlist?";
        var modal_footer = '<button type="button" data-dismiss="modal" class="btn deliver_btn" style="border-radius:3px;padding:5px; margin-right: 10px;" id="move_to_wishlist_item">Move to Wishlist</button>'+
            '<button type="button" data-dismiss="modal" class="btn popup_close_btn">Cancel</button>';
        $('#confirm_body').html(modal_body);
        $('#confirm_footer').html(modal_footer);

        $('#confirm_popup').modal({
            keyboard: false
        });

        $('#move_to_wishlist_item').click(function(){
            this.props.moveToWishlist(this.props.data);
        }.bind(this));*/

        if(this.wishlist){
            $('#alert_body').html("Product already added to your short list");
            $('#alert_popup').modal({
                backdrop: 'static',
                keyboard: false
            });
        }
        else{
            this.props.moveToWishlist();
            this.wishlist = true;
        }

    }

    createMarkup(html) {
        return {__html: html};
    }

    render() {
        var self = this;
        var style = {
            fontSize: 12
        };
        var item_background = {};
        if(this.props.data.stock == false){
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
        else if(this.props.data.moq_error == true){
            item_background = {
                background:'#f7f5de'
            }
        }

  let rating;
  let rating_class; 

  if(this.props.data.rating)
  {
    
    if(this.props.data.rating == 5) 
     {
       rating = 'Excellent Quality';
       rating_class = 'label-success';
     }
     else if(this.props.data.rating == 4)
     {
       rating = 'Good Quality';
       rating_class = 'label-warning';   
     }
     else if(this.props.data.rating <= 3)
     {
       rating = 'Average Quality';
       rating_class = 'label-danger'; 
     }
  }        
    function createMarkup(html) {
       return {__html: html};
     }
     
        return (
            <fieldset className="product_section" id={this.props.data.key+this.props.data.product_id} style={item_background}>
                <div className="col-sm-6">
                    <div className="col-sm-3 nopadding">
                        <a href={this.props.data.href}>
                            <img className="img-thumbnail" src={this.props.data.thumb} alt={this.props.data.name} title={this.props.data.name} width="74" height="111" />
                        </a>
                    </div>
                    <div className="col-sm-9 nopadding product_dec">
                        <a href={this.props.data.href} dangerouslySetInnerHTML={this.createMarkup(this.props.data.name)}></a>
                        <br/>
                        <span style={style} >SKU: <span dangerouslySetInnerHTML={this.createMarkup(this.props.data.model)}></span></span>
                        <br/>
                        {this.props.data.previously_ordered == true?
                            (
                                <div><span style={style} className="red_text">( {this.props.language.text_previously_ordered} )</span><br/></div>
                            ):(<span></span>)
                        }
                        <span style={style} dangerouslySetInnerHTML={this.createMarkup(this.props.data.set_description)}></span>

                        {!jQuery.isEmptyObject(this.props.data.option)?(
                                <div>
                                    {this.props.data.option.map(function(option,key){
                                        return <div key={key}> {option.name} : {option.value} </div>
                                    })}
                                </div>
                            ):(
                                <div style={{display:'none'}}></div>
                            )
                        }
                         <br/> <br/>

                        {this.props.data.rating ?
                        <div className="product-card_rating" style={{display:'inline-block', 'marginRight':'20px'}}>
                         <span className={'label '+rating_class}>{rating}</span>
                         </div>
                         :''
                        }

                        {this.props.data.is_sor_enabled ?
                        <div className="blue_bg">
                        <div ><img className="round_box" src={cdn_url+"sor_box.png"} /></div>
                        <div className="buyback_text"><span>{this.props.data.sor_enabled_text}</span></div>
                       </div>
                       : ''}
                        
                        {this.props.data.pickup_city != '' ?
                         <div className="product-card_rating product-card_location">
                         <span className='label label-warning'>from {this.props.data.pickup_city}</span>
                         </div>
                        : ''
                        }

                        { this.props.data.non_returnable == true ?(
                            <div><div className="red_text" style={{fontSize:'13px'}}><strong>{this.props.language.text_non_returnable}</strong></div></div>
                        ):(<div style={{display:'hidden'}} ></div>)
                        }
                        { this.props.data.exp_dispatch_date != '' ?(
                            <div><div className="red_text" style={{fontSize:'13px'}}><strong>{this.props.data.exp_dispatch_date}</strong></div></div>
                        ):(<div style={{display:'hidden'}} ></div>)
                        }
                        { this.props.data.store_pickup == true ?(
                            <div><div className="text-warning" style={{fontSize:'13px'}}><strong>{this.props.language.text_store_pickup}</strong></div></div>
                        ):(<div style={{display:'hidden'}} ></div>)
                        }
                        { this.props.data.wrong_store == true ?(
                            <div><div className="red_text" style={{fontSize:'13px'}}><strong>{this.props.language.error_wrong_store}</strong></div></div>
                        ):(<div style={{display:'hidden'}} ></div>)
                        }
                        { this.props.data.error_store_limit == true ?(
                            <div><div className="red_text" style={{fontSize:'13px'}}><strong>{this.props.data.error_store_limit_msg}</strong></div></div>
                        ):(<div style={{display:'hidden'}} ></div>)
                        }
                        { this.props.data.cod_available == 0 ?(
                            <label className="not_available_cod_new" style={{position:'relative'}}></label>
                        ):(<div style={{display:'hidden'}} ></div>)
                        }

                        {/* this.props.data.moq_error == true ?(
                            <div><div className="red_text " style={{fontSize:'14px'}}><strong>Minimum quantity required to buy this product is {this.props.data.minimum}. Please reset the quantity.</strong></div></div>
                        ):(<div style={{display:'hidden'}} ></div>)
                        */}

                       
                        <div className="product_dec_bottem">
                            <button onClick={this.remove} type="button" className="btn product_dec_bottem_btn"  >
                                <label><i className="fa fa-trash-o" aria-hidden="true"></i></label> {this.props.language.text_remove_button}
                            </button>
                            {this.wishlist != false?(
                                <button type="button" className="btn product_dec_bottem_btn" onClick={this.addToShortlist}>
                                    <label><i className="fa fa-heart custom_heart" aria-hidden="true"></i></label> {this.props.language.text_move_to_wishlist}
                                </button>
                            ):(
                                <button type="button" className="btn product_dec_bottem_btn" onClick={this.addToShortlist}>
                                    <label><i className="fa fa-heart-o" aria-hidden="true"></i></label> {this.props.language.text_move_to_wishlist}
                                </button>
                            )}

                            {this.props.data.stock != false?(
                            <button type="button" className="btn product_dec_bottem_btn"
                                    onClick={this.toggleCommentBox}>
                                <label><i className="fa fa-commenting-o"
                                          aria-hidden="true"></i></label> {this.props.language.text_comment}
                            </button>
                            ):(
                                <div style={{display:'hidden'}}></div>
                            )}
                        </div>
                    </div>


                </div>
                <div className="col-sm-1 text-center nopadding">
                    <div className="form-group" >
                        <select className="select_set_box" id={"sets_"+this.props.data.key.replace(/=/g, "")+this.props.data.product_id}>
                        </select>
                    </div>
                    {this.updateSetPopup()}
                </div>

                {this.props.data.stock == false && this.props.data.error_store_limit == false?(
                    <div className="col-sm-5 text-center">
                        <div className="out-of-stock col-sm-5" style={{fontSize:'13px',padding: '7px'}}>{this.props.language.text_out_of_stock}</div>
                        <div className="col-sm-6" ><button type="button" className="addtocart_bottem btn comment_btn button_i_want_design"  style={{fontSize:'13px',background:'#17319f',color:'white',padding:'6px 20px'}} ><span>{this.props.language.i_want_this_design}</span></button></div>
                        <div className="" style={{fontSize:'15px',marginTop:'12%'}}>{this.props.language.text_out_of_stock_alert}</div>


                        {(!jQuery.isEmptyObject(this.props.data.option) && this.props.data.stock_quantity == 0)?(

                                this.props.data.option.map(function(option,key){
                                    return <div key={key} style={{fontSize:'14px',marginTop:'4%'}}> {option.name} {option.value} &nbsp;is OUT OF STOCK! Other {option.name}s are available.
                                        <br />To add other {option.name}s, <a href={self.props.data.href}>click here</a>
                                    </div>
                                })

                            ):(
                                <div style={{display:'none'}}></div>
                            )
                        }

                    </div>

                ):(
                    <div className="col-sm-5">
                        <div className="col-sm-2 text-center nopadding">{this.props.data.piece_in_set} {this.props.data.base_unit}</div>
                        <div className="col-sm-4 text-center nopadding" dangerouslySetInnerHTML={createMarkup(this.props.data.price_per_piece+'/'+this.props.data.base_unit+'<br/>'+this.props.data.tax_rate)}></div>
                        <div className="col-sm-4 text-center nopadding" dangerouslySetInnerHTML={createMarkup(this.props.data.total)}></div>
                        <div className="col-sm-2 text-center nopadding" dangerouslySetInnerHTML={createMarkup(this.props.data.tax)}></div>
                    </div>

                )}
                {/*
                    {this.props.data.stock == false && this.props.data.error_store_limit == false?(null):(<div className="col-sm-1 text-center nopadding">{this.props.data.price_per_piece}/{this.props.data.base_unit}</div>
                    )}
                    {this.props.data.stock == false && this.props.data.error_store_limit == false?(null):( <div className="col-sm-2 text-center nopadding">{this.props.data.total}</div>
                    )}
                    {this.props.data.stock == false && this.props.data.error_store_limit == false?(null):( <div className="col-sm-1 text-center nopadding">{this.props.data.tax}</div>
                    )}
                 */}

            </fieldset>
        );
    }

}
