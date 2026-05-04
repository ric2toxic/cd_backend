class SetQuantityBox extends React.Component {

    constructor(props){
        super(props);
        this.state = {
           option: [],
           
        };
    } 
    
    

    render() {
        $('[data-toggle="popover"]').popover(); 
        var self = this;
        var blur_class;
        if(!this.props.api_call) { blur_class = 'blur'; } else { blur_class = ''; }
        var options_table = this.props.options.map(function(option, i){
                return (
                    <div>
                    <table key = {i} width="100%" className="table set_qty_table table-striped table-hover">
                           <thead>
                            <tr className="cart_table_title">
                            <th>{option.name} </th>
                            <th>Available </th>
                            <th>Quantity</th>
                            </tr>
                          </thead>
                           {option.name == 'Color' ?
                            <ColorQuantityList name={option.name} key = {i} product_option_id = {option.product_option_id} minimum = {self.props.minimum} optionsval = {option.product_option_value} is_popup={self.props.is_popup} />
                            :
                            <QuantityList name={option.name} key = {i} product_option_id = {option.product_option_id} minimum = {self.props.minimum} optionsval = {option.product_option_value} />
                            }
                        </table>
                        </div>

                );
        });


        return (
               this.props.options.length==0?(
                    <div></div>
                ):(
                    <div id="set_quantity_box" className={'panel-collapse collapse '+blur_class} aria-expanded="false">
                        {options_table}
                    </div>
                )
        )
    }
}



class QuantityList extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            is_initial : 0,
            is_option : 0,
        };
    }

    componentWillMount(){
        if(this.props.optionsval.length > 1){
            this.setState({is_option:1}) 
        }

    }   

    render() {
      function createMarkup(html) {
            return {__html: html};
           }
       function want_design_option(option_value='')
       {
            /*var customer_mobile = getCookie("customer_mobile");
            var register_user = getCookie("register_user");
            var customer_id = getCookie("customer_id");
            if(getCookie("customer_mobile") == '')
            {
                $("input[name=redirect_cart]").val(2);
                $("#login_link_header").trigger("click");
                return false;     
                
            }
            else if(getCookie("register_user") == 1 && getCookie("customer_id") == '')
            {
                $("input[name=redirect_cart]").val(2);
                $("#login_link_header").trigger("click");
                return false;
            }
            else
            {
               $("#option_name").val(self.props.name);
               $("#option_value").val(option_value); 
               $("#want_designe_popup").modal("show"); 
            }*/

            if(getCookie("customer_mobile") != '')
            {
              $("#design_customer_mobile").hide();
            }
            else
            {
              $("#design_customer_mobile").show();
            }

            $("#option_name").val(self.props.name);
            $("#option_value").val(option_value); 
            $("#want_designe_popup").modal("show");

       }

        var self = this;
        var optionsVal = this.props.optionsval.map(function(optval, k){
            
              if(optval.quantity > 0)
              {  
                return (
                    <tr key={k} className="odd gradeA">
                        <td>{optval.name}</td>
                        <td>{optval.quantity + ' Set'}<br/>{optval.price?<div style={{color:'#0a8105',fontSize:"11px"}} dangerouslySetInnerHTML={createMarkup(optval.price+'/Piece')}></div>:''}</td>
                        <td><SetQuantity  table_heading={self.props.name} table_name={optval.name} key={k} is_option = {self.props.optionsval.length} product_option_id = {self.props.product_option_id} product_option_value_id = {optval.product_option_value_id} minimum="0" maximum = {optval.quantity} /></td>
                    </tr>
                );
               }
               else
               {
                 return (
                    <tr key={k} className="odd gradeA">
                        <td>{optval.name}</td>
                        <td className="out_of_stock_text">Out of stock</td>
                        <td><a href="javascript:;" className="want_design_text" onClick={() => want_design_option(optval.name) }>I want this {self.props.name}</a></td>
                    </tr>
                );
               } 

        });
        return (<tbody>{optionsVal}</tbody>
        )
    }
	
}



class ColorQuantityList extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            is_initial : 0,
            is_option : 0
        };
    }

    componentWillMount(){
        if(this.props.optionsval.length > 1){
            this.setState({is_option:1}) 
        }

    } 

    render() {
      function createMarkup(html) {
            return {__html: html};
           }

       function want_design_option(option_value='')
       {
            var customer_mobile = getCookie("customer_mobile");
            var register_user = getCookie("register_user");
            var customer_id = getCookie("customer_id");
            if(getCookie("customer_mobile") == '')
            {
                $("input[name=redirect_cart]").val(2);
                $("#login_link_header").trigger("click");
                return false;     
                
            }
            else if(getCookie("register_user") == 1 && getCookie("customer_id") == '')
            {
                $("input[name=redirect_cart]").val(2);
                $("#login_link_header").trigger("click");
                return false;
            }
            else
            {
               $("#option_name").val(self.props.name);
               $("#option_value").val(option_value); 
               $("#want_designe_popup").modal("show"); 
            }

       }

        var self = this;
        var optionsval = this.props.optionsval;
        var more = 0;

        if(optionsval.length > 4) 
            { 
                var optionsval_slice_data =  optionsval.slice(0, 4);  
                more = 1;
            }
        else { var optionsval_slice_data =  optionsval; }

        var optionsVal = optionsval_slice_data.map(function(optval, k){
          if(optval.extra_info!= null)
            {
              var color_class = optval.extra_info;
            }
            else{
              var color_class = '';
            }
            color_class = color_class.replace(" ", "");
              if(optval.quantity > 0)
              {  
                return (
                    <tr key={k} className="odd gradeA">
                        <td><span className='color_code color_code_big' style={{backgroundColor:color_class}}></span>
                            <br /> <span className="option_color_name">{optval.name}</span></td>
                        <td>{optval.quantity + ' Set'}<br/>{optval.price?<div style={{color:'#0a8105',fontSize:"11px"}} dangerouslySetInnerHTML={createMarkup(optval.price+'/Piece')}></div>:''}</td>
                        <td><SetQuantity table_heading={self.props.name} table_name={optval.name} key={k} color_option="color_option" is_option = {self.props.optionsval.length} product_option_id = {self.props.product_option_id} product_option_value_id = {optval.product_option_value_id} minimum="0" maximum = {optval.quantity} /></td>
                    </tr>
                );
               }
               else
               {
                 return (
                    <tr key={k} className="odd gradeA">
                        <td><span className='color_code color_code_big' style={{backgroundColor:color_class}}></span>
                            <br /> <span className="option_color_name">{optval.name}</span></td>
                        <td className="out_of_stock_text color_option">Out of stock</td>
                        <td><a href="javascript:;" className="want_design_text" onClick={() => want_design_option(optval.name) }>I want this {self.props.name}</a></td>
                    </tr>
                );
               } 

        });

        return (<tbody>{optionsVal} 
                 {more ?
                  <ColorQuantityPopup name={this.props.name} key = "0" product_option_id = {this.props.product_option_id} minimum = {this.props.minimum} optionsval = {this.props.optionsval} is_popup={this.props.is_popup} />
                   :''}
                 </tbody>
        )
    }
    
}