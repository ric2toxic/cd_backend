class OptionGroupValue extends React.Component {

   constructor(props)
   {
     super(props);
   } 

   componentDidMount()
  {

  }
  render() {
     let self = this;;
    var active_checked;
       active_checked = $.grep(this.props.search_option, function( a ) {
                return a == self.props.option.option_value_id;
             });
       
    if(active_checked == self.props.option.option_value_id) 
      { active_checked="true";
        $("#filter"+self.props.filter.filter_id).parents().eq(4).addClass("in");
        $("#filter"+self.props.filter.filter_id).parents().eq(4).prev().addClass("arrow_rotate");

       } 

    else { active_checked = ""; }
    return (<div className="price_filter">
                            <div className="checkbox_information">
                                <label>
                                     <input type="checkbox" checked={active_checked} id={'filter'+this.props.option.option_value_id}  name="filter[]" value={this.props.option.option_value_id} onChange={this.props.clickHandlerOption} />
                                    {this.props.option.option_value} 
                                    <div className="control__indicator"></div>
                                </label>
                            </div>
                        </div>);
  }
}