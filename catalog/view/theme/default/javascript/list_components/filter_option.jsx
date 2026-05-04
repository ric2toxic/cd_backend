class FilterOption extends React.Component {

   constructor(props)
   {
     super(props);
   } 

   componentDidMount()
  {

  }

  decode_html(text)
  {
     var decoded = $('<div/>').html(text).text();
     return decoded;
  }

  render() {
    let self = this;;
    var active_checked;
       active_checked = $.grep(this.props.search_filter, function( a ) {
                return a == self.props.filter.filter_id;
             });
       
    if(active_checked == self.props.filter.filter_id) 
      {
        active_checked="true";
        $("#filter"+self.props.filter.filter_id).parents().eq(4).addClass("in");
        $("#filter"+self.props.filter.filter_id).parents().eq(4).prev().addClass("arrow_rotate");
        
       } 
    else { active_checked = ""; }

var disable_class='';
if(self.props.filter.product_count == 0) { var disable_class = 'disable'; }


    return (<div className={'price_filter '+disable_class}>
                           {this.props.filter.name != '' ? 
                            <div className="checkbox_information">
                                <label>
                                     <input type="checkbox"  checked={active_checked} id={'filter'+this.props.filter.filter_id}  name="filter[]" value={this.props.filter.filter_id} onChange={this.props.clickHandler} />
                                    {this.decode_html(this.props.filter.name)}
                                    <div className="control__indicator"></div>
                                </label>
                            </div>
                            : ''}
                        </div>);
  }
}