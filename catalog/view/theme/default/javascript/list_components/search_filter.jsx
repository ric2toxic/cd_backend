class SearchFilter extends React.Component {

   constructor(props)
   {
     super(props);
   } 

   componentDidMount()
  {

  }
  render() {

      var color_name = this.props.filter.name;
      color_name = color_name.toLowerCase();
      color_name = color_name.replace(/\s/g, '');
   
     if(this.props.filter.group_name == 'Color')
     {
       if(color_name == 'multicolor')
      {
        return (<li data-placement="top"  data-toggle="popover"  data-trigger="hover" data-content="multicolor" onClick={()=>this.props.clickHandler(this)} data-id={this.props.filter.filter_id}>
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
         return (<li data-placement="top"  data-toggle="popover"  data-trigger="hover" data-content={this.props.filter.name} onClick={()=>this.props.clickHandler(this)} data-id={this.props.filter.filter_id}><span className={'filtar_color_box '+color_name}></span> <label className="filtar_close_icon"></label> </li>); 
      }    
    
     }
     else
     {
      return (<li data-placement="top"  data-toggle="popover"  data-trigger="hover" data-content={this.props.filter.name} onClick={()=>this.props.clickHandler(this)} className="filter" data-id={this.props.filter.filter_id}>{this.props.filter.name}<label className="filtar_close_icon"></label></li> ); 
     }
     
      
  }
}