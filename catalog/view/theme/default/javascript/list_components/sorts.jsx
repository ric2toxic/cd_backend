class Sorts extends React.Component {

   constructor(props)
   {
     super(props);
   } 

   componentDidMount()
  {

  }
  render() {
   
     return (<li className={this.props.sort.selected}>
             <a href="javascript:void(0);" data-value={this.props.sort.query_string} onClick={()=>this.props.clickHandler(this)}>{this.props.sort.text}</a></li>); 
      
  }
}