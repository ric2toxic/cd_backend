class PopularTagsListpage extends React.Component {

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
    return  <a style={{marginLeft: '10px'}} href={childrenitem.href}>{childrenitem.text}</a>
   }


	render() {
		return (
			  <span className="popular_search" style={{wordWrap: 'break-word', textAlign: 'left'}}>
             {
                 this.state.data.map((childrenitem, index) => {
                    return this.generateChildren(childrenitem)
                   })
             }
          </span>
		);
	}
}
