class PopularTags extends React.Component {

   constructor()
   {
   	 super();
   	this.state = {
      data: []
    };
   } 

   componentDidMount()
  {
    axios({
    method:'get',
    url:'./api/footer/popular_tags',
    responseType:'json'
   })
   .then(response => {
            this.setState({data: response.data.data.popular_tags})
   });
  }

generateChildren(childrenitem){
    return <li className="col-sm-3">
           <a href={childrenitem.href}>{childrenitem.text}</a>
            </li>   
   }


	render() {
		return (
			  <div className="information_section footer_Subscribe_Section">
          <h3>Popular Searches</h3>
            <ul>
             {
                 this.state.data.map((childrenitem, index) => {
                    return this.generateChildren(childrenitem)
                   })
             }
            </ul>
          </div>
		);
	}
}
