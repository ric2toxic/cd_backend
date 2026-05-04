class SearchOption extends React.Component {

   constructor(props)
   {
     super(props);
   } 

   componentDidMount()
  {

  }
  render() {

      var color_name = this.props.option.name;
      color_name = color_name.toLowerCase();
      color_name = color_name.replace(/\s/g, '');
   
     if(this.props.option.group_name == 'Color')
     {
       if(color_name == 'multicolor')
      {
        return (<li data-placement="top"  data-toggle="popover"  data-trigger="hover" data-content="Multi Color" onClick={()=>this.props.clickHandler(this)} data-id={this.props.option.option_value_id}>
              <span className="filtar_color_box filtar_color_box_mini red"></span>
              <span className="filtar_color_box filtar_color_box_mini brown"></span>
              <span className="filtar_color_box filtar_color_box_mini blue"></span>
              <span className="filtar_color_box filtar_color_box_mini white"></span>
              <span className="filtar_color_box filtar_color_box_mini darkGray"></span>
              <span className="filtar_color_box filtar_color_box_mini black"></span>
          <label className="filtar_close_icon"></label> </li>); 
      }
      else
      {
         return (<li data-placement="top"  data-toggle="popover"  data-trigger="hover" data-content={this.props.option.name} onClick={()=>this.props.clickHandler(this)} data-id={this.props.option.option_value_id}><span className={'filtar_color_box '+color_name}></span> <label className="filtar_close_icon"></label> </li>); 
      }    
    
     }
     else
     {
      return (<li data-placement="top"  data-toggle="popover"  data-trigger="hover" data-content={this.props.option.name} onClick={()=>this.props.clickHandler(this)} className="filter" data-id={this.props.option.option_value_id}>{this.props.option.name}<label className="filtar_close_icon"></label></li> ); 
     }
     
      
  }
}