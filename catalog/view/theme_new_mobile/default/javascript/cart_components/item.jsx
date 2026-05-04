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
        this.renderSets = this.renderSets.bind(this);
        this.remove = this.remove.bind(this);
        this.addToShortlist = this.addToShortlist.bind(this);
        this.createMarkup = this.createMarkup.bind(this);

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
            //this.toggleCommentBox();
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
                <div className="modal-dialog" style={{top:'20%'}}>
                    <div className="modal-content">
                        <div className="modal-body padding_top_bottem" style={{minHeight:'100px'}}>
                            <div className="mobile_details_panel">
                                <button type="button" className="close" data-dismiss="modal">&times;</button>
                                <h4 className="col-xs-6">Enter {this.props.data.super_unit}</h4>
                                <div className="clearfix"></div>
                                <div className="col-xs-6 nopadding">
                                    <input id={sets_value_id} className="password_box sets_input" style={{padding:'6px',marginLeft:'15px'}} type="text" placeholder="Sets" alt="Sets"/>
                                </div>
                                <div className="col-xs-6 nopadding">
                                    <button type="button" onClick={this.changeSetThroughPopup} className="btn deliver_btn pull-right" style={{margin:'10px 2px',fontSize:'16px'}} >Update</button>
                                </div>
                                <div className="clearfix"></div>
                                <div className="col-xs-12 sets_order_notes text-danger" style={{marginLeft:'5%'}}>*{this.props.data.stock_quantity} Sets are available</div>
                                <div className="clearfix"></div>
                            </div>
                            <div className="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>
        );
    }

    toggleCommentBox(){
        $('#comment_product_key').val(this.props.data.key+this.props.data.product_id);
        $('#product_comment').val(this.props.data.comment);
        $('#error_product_comment').html("");
        $('#product_comment_popup').modal({
            backdrop: 'static',
            keyboard: false
        });
    }
    
    remove(){
		// If product is out of stock, then no need to confirm,directly deleting the item
		
		if(this.props.data.stock == false){
			
			this.props.removeItem(this.props.data);
		}
		else{
			
			var modal_body = "Are you sure to remove item from cart?";
			var modal_footer = '<button type="button" data-dismiss="modal" class="btn deliver_btn clear_popup_btn" id="remove_item">Yes</button>'+
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
        var tr_background = {};
        if(this.props.data.stock == false){
            tr_background = {
                background:'#ffe6e6',
                borderBottom: '1px solid #ccc'
            }
        }
        else if(this.props.data.quantity_reduced){
            tr_background = {
                background:'#e6e6ff'
            }
        }
        else if(this.props.data.moq_error == true){
            tr_background = {
                background:'#f7f5de'
            }
        }

        return (
            <div id={this.props.data.key+this.props.data.product_id} className="cart_table" style={tr_background}>
                <div className="col-xs-3 nopadding product_img_box">
                    <a href={this.props.data.href}>
                        <img className="img-thumbnail" src={this.props.data.thumb} alt={this.props.data.name} title={this.props.data.name} width="74" height="111"/>
                    </a>
                </div>
                <div className="col-xs-9 product_dec">
                    <a href={this.props.data.href} dangerouslySetInnerHTML={this.createMarkup(this.props.data.name)}></a>
                    <br/>
                    <span style={{fontSize:'13px'}}>Product Code: <span dangerouslySetInnerHTML={this.createMarkup(this.props.data.model)}></span></span>
                    <br/>
                    <span style={{fontSize:'13px'}} dangerouslySetInnerHTML={this.createMarkup(this.props.data.set_description)}></span>
                    <br/>
                    {this.props.data.previously_ordered == true?
                        (
                            <span style={{fontSize:'13px'}} className="red_text">( {this.props.language.text_previously_ordered} )</span>
                        ):(<span style={{display:'none'}}></span>)
                    }

                    {!jQuery.isEmptyObject(this.props.data.option)?(
                        <div>
                            {this.props.data.option.map(function(option,key){
                                return <span key={key} style={{fontSize:'13px'}}> {option.name} : {option.value} </span>
                            })}
                        </div>
                    ):(
                        <div style={{display:'none'}}></div>
                    )
                    }


                    { this.props.data.non_returnable == true ?(
                        <div><span className="red_text" style={{fontSize:'13px'}}><strong>{this.props.language.text_non_returnable}</strong></span></div>
                    ):(<div style={{display:'hidden'}} ></div>)
                    }
                    { this.props.data.exp_dispatch_date != '' ?(
                        <div><span className="red_text" style={{fontSize:'13px'}}><strong>{this.props.data.exp_dispatch_date}</strong></span></div>
                    ):(<div style={{display:'hidden'}} ></div>)
                    }
                    { this.props.data.store_pickup == true ?(
                        <div><span className="text-warning" style={{fontSize:'13px'}}><strong>{this.props.language.text_store_pickup}</strong></span></div>
                    ):(<div style={{display:'hidden'}} ></div>)
                    }
                    { this.props.data.wrong_store == true ?(
                        <div><span className="red_text" style={{fontSize:'13px'}}><strong>{this.props.language.error_wrong_store}</strong></span></div>
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
                        <div><span className="red_text " style={{fontSize:'13px'}}><strong>Minimum quantity required to buy this product is {this.props.data.minimum}. Please reset the quantity.</strong></span></div>
                    ):(<div style={{display:'hidden'}} ></div>)
                    */}

                    <div className="product_dec_bottem">
                        <div className="col-xs-12 nopadding">
                            <div className="col-xs-8 nopadding">
                                {this.props.data.comment == '' && this.props.data.stock != false?(
                                <div className="price_breakup_text">
                                    <a href="javascript:void(0)" onClick={this.toggleCommentBox}><i className="fa fa-commenting-o" aria-hidden="true"></i>&nbsp;Comment</a>
                                </div>
                                ):(<div style={{display:'none'}}></div>)}
                            </div>
                            <div className="col-xs-4 nopadding pull-right">
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
                </div>
                {this.props.data.comment != ''?(
                    <div className="col-xs-12">
                        <div className="price_breakup_text">
                            <strong>Comment: </strong>{this.props.data.comment}&nbsp;<a href="javascript:void(0)" onClick={this.toggleCommentBox}><i className="fa fa-edit" aria-hidden="true"></i>&nbsp;Edit</a>
                        </div>
                    </div>
                ):(<div style={{display:'none'}}></div>)}


                    {this.props.data.stock == false && this.props.data.error_store_limit == false?(
                        <div className="price_breakup_table col-xs-12">
                            <div className="out-of-stock col-xs-6" >{this.props.language.text_out_of_stock}</div>
                            <div className="col-xs-6"><button type="button" className="addtocart_bottem btn comment_btn button_i_want_design" ><span>{this.props.language.i_want_this_design}</span></button></div>
                            <div className="col-xs-12">{this.props.language.text_out_of_stock_alert}</div>
                        </div>
                    ):(
                        <div className="price_breakup_table col-xs-12">
                            <div className="row bottem_line">
                                <div className="col-xs-6 order_summary_table-left">Number of {this.props.data.base_unit}</div>
                                <div className="col-xs-6 order_summary_table-right"> {this.props.data.piece_in_set} </div>
                            </div>
                            <div className="row text_red">
                                <div className="col-xs-6 order_summary_table-left">Price per {this.props.data.base_unit}</div>
                                <div className="col-xs-6 order_summary_table-right" dangerouslySetInnerHTML={this.createMarkup(this.props.data.price_per_piece)}></div>
                            </div>
                            <div className="row">
                                <div className="col-xs-6 order_summary_table-left">{this.props.data.tax_rate}</div>
                                <div className="col-xs-6 order_summary_table-right" dangerouslySetInnerHTML={this.createMarkup(this.props.data.tax)}></div>
                            </div>
                            <br/>
                            <div className="row black">
                                <div className="col-xs-6 order_summary_table-left"><strong>Total Price</strong></div>
                                <div className="col-xs-6 order_summary_table-right"><strong dangerouslySetInnerHTML={this.createMarkup(this.props.data.total)}></strong></div>
                            </div>
                        </div>
                    )}

                <div className="col-xs-12 bottom_btn">
                    <div className="col-xs-6 remove"><button onClick={this.remove}><span><i className="fa fa-trash-o"></i></span>&nbsp;Remove</button></div>
                    <div className="col-xs-6 nopadding">
                        {this.wishlist != false?(
                            <button onClick={this.addToShortlist}><span><i className="fa fa-heart" aria-hidden="true"></i></span>&nbsp;Saved in Shortlist</button>
                        ):(
                            <button onClick={this.addToShortlist}><span><i className="fa fa-heart-o" aria-hidden="true"></i></span>&nbsp;Add to Shortlist</button>
                        )}
                    </div>
                </div>
                <div className="clearfix"></div>
            </div>
        );
    }

}
