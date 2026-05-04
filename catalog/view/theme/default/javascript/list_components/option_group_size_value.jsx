class OptionGroupSizeValue extends React.Component {

   constructor(props)
   {
     super(props);
   } 

   componentDidMount()
  {

  }
  render() {
     let self = this;;
    var active;
    var active_checked;
       active_checked = $.grep(this.props.search_option, function( a ) {
                return a == self.props.option.option_value_id;
             });
       
    if(active_checked == self.props.option.option_value_id) 
      { 
        active = "active";  active_checked="true";
        $("#filter"+self.props.filter.filter_id).parents().eq(4).addClass("in");
        $("#filter"+self.props.filter.filter_id).parents().eq(4).prev().addClass("arrow_rotate");
     }
   else { active = "";  active_checked = ""; }

    return (<li className={active}>
              <a href="javascript:;" onClick={()=> $('#option'+this.props.option.option_value_id).click() } title={this.props.option.option_value}>{this.props.option.option_value}</a>
                  <input type="checkbox" checked={active_checked} id={'option'+this.props.option.option_value_id} name="filter[]" value={this.props.option.option_value_id} className="hide" onChange={this.props.clickHandlerOption} />     
             </li>);
  }
}