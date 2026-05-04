class ProductInfoTitle extends React.Component {

    constructor(props){
        super(props);
        this.state = {
           data: [],
           is_initial : 0
        };
    }

    render() {
    function createMarkup(html) {
       return {__html: html};
     }
        if(this.props.previously_ordered){
            var previously_ordered_div='<div class="previous_order_div_product">Previously Ordered</div>';
        }
            else{
                var previously_ordered_div='';
            }
    	return (
    		<section className="product_title_box">
                 <h3>{this.props.heading_title}</h3>
                 <div dangerouslySetInnerHTML={createMarkup(previously_ordered_div)} />
                 <span><b>Model:</b> {this.props.model}</span> <br />
                 <span><b>HSN:</b> {this.props.hsn_code}</span>
                  
                   {this.props.is_sor_enabled ?
                  <div className="blue_bg_detail">
                   <div ><img className="round_box" src={cdn_url+"sor_box.png"} /></div>
                   <div className="buyback_text_detail"><span>{this.props.sor_enabled_detail_text}</span></div>
                  </div>
                  : ''}

              </section> 
    	)
    }
}
