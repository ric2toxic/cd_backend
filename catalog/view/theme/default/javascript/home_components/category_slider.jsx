class CategorySlider extends React.Component {

   constructor()
   {
   	 super();
   	this.state = {
      category_data: []
    };
   } 

   componentDidMount()
  {
    
    /*axios({
    method:'get',
    url:'./api/home/category_list',
    responseType:'json'
   })
   .then(response => {
            this.setState({category_data: response.data.data.category_data})
   });*/

  }

	render() {
		return (<section className="col-sm-12 contact_sanction">
            <div className="home_category_section">
              <h2 className="page_title home_title"><span>New Arrivals</span></h2>
              <div className="clearfix"></div>
              <Slider key="latest"  category_id="latest" />
            </div>
            <div className="home_category_section">
              <h2 className="page_title home_title"><span>Trending Products</span></h2>
              <div className="clearfix"></div>
              <Slider key="trending"  category_id="trending" />
            </div>
      }

      </section>);
	}
}
