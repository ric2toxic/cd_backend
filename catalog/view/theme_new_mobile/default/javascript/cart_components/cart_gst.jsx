{/*
 * class CartGST: This will render the GST Information of customer (if not, then render the new address form)
 * @Params: {
 *      customer: customer info,
 *      language: language text for different fields
 *
 * @author:Devendra Dhayal, Date-Added:8th July 2017
 *
 */}

class CartGST extends React.Component{
    constructor(){
        super();
    }

    removeGST(){
        $('.change_gst').hide();
        $('input:radio[value="1"]').prop('checked',true);
    }

    selectGSTOption(value){
        $('input:radio[value="'+value+'"]').prop('checked',true);
        if(value == 0)
            $('.change_gst').show();
        else
            $('.change_gst').hide();
    }


    componentDidUpdate(){
        if(this.props.customer.hasOwnProperty('gst_option')){
            $('input:radio[value="'+this.props.customer.gst_option+'"]').trigger('click');
        }
    }

    componentDidMount(){
        if(this.props.customer.gst_number != ""){
            $('#gst_number').val(this.props.customer.gst_number);
        }
        if(this.props.customer.hasOwnProperty('gst_option')){
            $('input:radio[value="'+this.props.customer.gst_option+'"]').trigger('click');
        }
    }

    render(){

        return(
            <div className="col-xs-12 cart_table">
                <div className="gst_declaration_box">
                    {this.props.customer.gst_number != ""?(
                        <div>
                            <div className="gst_heading_checkout">{this.props.language.text_gst_already}</div>
                            <input type="text" className="gst_input" id="gst_number" value={this.props.customer.gst_number} disabled/>
                            &ensp;<a className="btn btn-estimate btn_delete_gst" onClick={this.props.clearGST} >{this.props.language.text_cancel}</a>
                        </div>
                    ):(
                        <div>
                            {/*<div className="gst_heading_checkout">{this.props.language.text_gst_declaration}</div>*/}
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
        );
    }
}
