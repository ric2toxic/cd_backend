
class Logo extends React.Component {

   constructor()
   {
   	 super();
   	this.state = {data: false}
   } 

   componentDidMount()
  {
    axios({
    method:'get',
    url:'./api/header/logo',
    responseType:'json'
   })
   .then(response => {
            this.setState({data: response.data.data})
        });

  }

	render() {
		return (
        <a href={this.state.data.home} className={this.props.home_page ? 'home_logo' : 'home_logo logo_scroll_right' }>
			  { this.state.data.logo ?
			  <img src={this.state.data.logo} alt="wholesalebox-logo" className="img-responsive" />
			   : <img src="https://d36qiqd7gl7e25.cloudfront.net/img/catalog/rsz_wsb_tmp_logo_286.png" alt="wholesalebox-logo" className="img-responsive" />
			  }
			  </a>
		);
	}
}

