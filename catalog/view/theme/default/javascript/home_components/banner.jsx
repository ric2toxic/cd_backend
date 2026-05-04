class Banner extends React.Component {

   constructor()
   {
   	 super();
   	this.state = {
      data: []
    };
   } 

   componentDidMount()
  {
    var preferences='';
    preferences = getCookie('preferences');

    axios({
    method:'get',
    url:'./api/home/banner&preferences='+preferences,
    responseType:'json'
   })
   .then(response => {
            this.setState({data: response.data.data.banners})
   });
  }



	render() {
    var banner_images;
      banner_images = this.state.data.map(function(banner, index) {
   

       if(index == 0)
       {
        if(banner.target_blank == 1)
        {
           return (<div className="item active" key={index}>
                       <a href={banner.link} target="_blank">   
                       <img className="img-responsive" id={'slider_img_'+index} src={banner.image} />
                       </a>
                      </div>)
        }
        else
        {
         return (<div className="item active" key={index}>
                       <a href={banner.link} >   
                       <img className="img-responsive" id={'slider_img_'+index} src={banner.image} />
                       </a>
                      </div>)
        }
       }
       else
       {
        if(banner.target_blank == 1)
        {
           return (<div className="item" key={index}>
                       <a href={banner.link} target="_blank">   
                       <img className="img-responsive" id={'slider_img_'+index} src={banner.image} />
                       </a>
                      </div>)
        }
        else
        {
         return (<div className="item" key={index}>
                       <a href={banner.link} >   
                       <img className="img-responsive" id={'slider_img_'+index} src={banner.image} />
                       </a>
                      </div>)
        }
       }


      })

     var banner_button = this.state.data.map(function(banner, index) {
       if(index == 0)
       {
         return (<li key={index} data-target={'slider_img_'+index} className="active" data-slide-to={index}></li>)
       }
       else
       {
         return (<li key={index} data-target={'slider_img_'+index} className="" data-slide-to={index}></li>)
       }
      })


		return (
			  <div id="myCarousel" className="carousel slide carousel_slider-control" data-ride="carousel" style={{background: '#f3f3f3',
    minHeight: '260',
    overflow: 'hidden',
    width: '100%'}}>
                <ol className="carousel-indicators">
                 {banner_button}
                </ol> 
  
                <div className="carousel-inner">
                  {banner_images}
                </div>
                <a className="left carousel-control width_slider_btn" href="#myCarousel" data-slide="prev">
                  <i className="fa fa-angle-left" aria-hidden="true"></i>
                </a>
                <a className="right carousel-control width_slider_btn" href="#myCarousel" data-slide="next">
                  <i className="fa fa-angle-right" aria-hidden="true"></i>
                </a> 
              </div>
		);


	}
}

