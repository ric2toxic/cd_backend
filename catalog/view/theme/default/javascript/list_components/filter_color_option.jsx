class FilterColorOption extends React.Component {

   constructor(props)
   {
     super(props);
   } 

   componentDidMount()
  {

  }
  
  render() {

  let self = this;
  var active;
  var active_checked;
  var color_name = this.props.filter.name;
      color_name = color_name.toLowerCase();
      color_name = color_name.replace(/\s/g, '');

       active = $.grep(this.props.search_filter, function( a ) {
                return a == self.props.filter.filter_id;
             });
       
    if(active == self.props.filter.filter_id) 
      { 
        active = "active"; active_checked="checked";
        $("#filter"+self.props.filter.filter_id).parents().eq(4).addClass("in");
        $("#filter"+self.props.filter.filter_id).parents().eq(4).prev().addClass("arrow_rotate");
      } 
    else { active = ""; active_checked = ''; }

var disable_class;
if(self.props.filter.product_count == 0) { var disable_class = 'disable'; }


 if(color_name == 'multicolor')
 {
    return (<li className={active}>
             <a href="javascript:;" className={disable_class} onClick={()=> $('#filter'+this.props.filter.filter_id).click() } title={this.props.filter.name}>
             {disable_class ?
              <div className="line2"></div>
              : ''}  

              <span className="red"></span>
              <span className="brown"></span>
              <span className="blue"></span>
              <span className="white"></span>
              <span className="darkGray"></span>
              <span className="black"></span>
             </a>
              <input type="checkbox" checked={active_checked} id={'filter'+this.props.filter.filter_id} name="filter[]" value={this.props.filter.filter_id} className="hide" onChange={this.props.clickHandler} />     
             </li>);
 }
 else
   {
    return (<li className={active}>
             <a href="javascript:;" className={color_name+' '+disable_class} onClick={()=> $('#filter'+this.props.filter.filter_id).click() } title={this.props.filter.name}></a>

             {disable_class ?
              <div className="line2"></div>
              : ''}
              <input type="checkbox" checked={active_checked} id={'filter'+this.props.filter.filter_id} name="filter[]" value={this.props.filter.filter_id} className="hide" onChange={this.props.clickHandler} />     
             </li>);

   }
  }
}