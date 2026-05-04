{/*
 * class CartGST: This will render the gst information of customer
 * @Params: {
 *      customer: customer's info,
 *      language: language text for different fields
 *
 * @author:Devendra Dhayal, Date-Added:08th July 2017
 *
 */}

class CartGST extends React.Component{
    constructor(){
        super();
        this.changeTab = this.changeTab.bind(this);
    }

    removeGST(){
        $('.change_gst').hide();
        $('input:radio[value="1"]').prop('checked',true);
    }

    changeTab(){
        var gst_option = $('input[name="accept_non_gst_declaration"]:checked').val();
        if(typeof gst_option == 'undefined'){
            gst_option = 0;
        }
        if(parseInt(gst_option) == 0){
            var gst_number = $('#gst_number').val();
            if(!validateGSTNumber(gst_number)){
                $('#alert_body').html(this.props.language.error_gst);
                $('#alert_popup').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                return;
            }
        }
        else{
            $('#gst_number').val('');
        }

        var access_token = getCookie('wat');
        var customer_id = getCookie('customer_id');
        $.ajax({
            url: 'api/checkout/validateGSTNumber',
            type: 'post',
            data: 'gst_number=' + $('#gst_number').val() +'&access_token='+access_token+'&customer_id='+customer_id,
            dataType: 'json',
            beforeSend:function(){
                $('#loading-indicator').show();
            },
            complete:function () {
                $('#loading-indicator').hide();
            }
        }).promise()
        .then(function(json){
            if(json['error']){
                $('#alert_body').html(json['error']['warning']);
                $('#alert_popup').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                return;
            }
            this.props.changeTab('payment');
        }.bind(this))
        .fail(function(xhr,exception){
            $('#loading-indicator').hide();
            $('#alert_body').html("Oops,Something went wrong.Please try again.");
            $('#alert_popup').modal({
                backdrop: 'static',
                keyboard: false
            });
            console.log("Error: "+xhr.status+", exception: "+exception);
        });

    }
    selectGSTOption(value){
        $('input:radio[value="'+value+'"]').prop('checked',true);
        if(value == 0)
            $('.change_gst').show();
        else
            $('.change_gst').hide();
    }

    componentDidMount(){
        $('input:radio[value="'+this.props.customer.gst_option+'"]').trigger('click');
        $('#gst_number').val(this.props.customer.gst_number);
    }

    render(){
        return(
            <div>
                <div className="col-sm-12 cart_table">
                    <div className="gst_declaration_box">
                        {this.props.customer.gst_number != ""?(
                            <div>
                                <div className="gst_heading">{this.props.language.text_gst_already}</div>
                                <input type="text" className="gst_input" id="gst_number" value={this.props.customer.gst_number}/>
                                &ensp;<a className="btn btn_delete_gst" onClick={this.props.clearGST} >Cancel</a>
                            </div>
                        ):(
                            <div>
                                <div className="gst_declaration_option">
                                    <span onClick={this.selectGSTOption.bind(this,0)} style={{cursor:'pointer'}}><input type="radio" name="accept_non_gst_declaration" value="0" style={{position:'relative',top: '5px'}} defaultChecked/> &nbsp;&nbsp;{this.props.language.text_gst_option_no}</span><br/>
                                    <div className="change_gst">
                                        <input type="text" className="gst_input" id="gst_number" placeholder="Enter GST Number"/>
                                    </div>
                                    <span onClick={this.selectGSTOption.bind(this,1)} style={{cursor:'pointer'}}><input type="radio" name="accept_non_gst_declaration" value="1" style={{position:'relative',top: '5px'}}/> &nbsp;&nbsp;{this.props.language.text_gst_declaration}</span><br/>
                                </div>
                            </div>
                        )}

                    </div>
                </div>
                <div className="col-sm-12 cart_table">
                    <button className="btn deliver_btn pull-right" data-parent="#accordion" onClick={this.changeTab} style={{position:'relative',bottom:'5px'}} data-target="#collapsePayment" >CONTINUE &nbsp;<i className="fa fa-caret-right" aria-hidden="true"></i> </button>
                </div>

            </div>
        );
    }
}
