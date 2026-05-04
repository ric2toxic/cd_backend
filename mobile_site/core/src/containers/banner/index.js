import React, { Component } from 'react'
import { connect } from 'react-redux'
import { Carousel } from 'react-responsive-carousel'
import 'react-responsive-carousel/lib/styles/carousel.min.css'


class Banner  extends Component {

  generateBanner (item, index) { 
    
  if(item.link === '')
  {
    return (<div className="fill" key={index}><img src={item.image} alt="" className="img-responsive" /></div>
           )
  } 
  else
  { 
    if(item.target_blank === 1)
    {
     return (<a href={item.link} key={index} target="_blank" rel="noopener noreferrer"><div className="fill"><img src={item.image} alt="" className="img-responsive" /></div></a>
           )
    }
    else
    {
    return (<a href={item.link} key={index}><div className="fill"><img src={item.image} alt="" className="img-responsive" /></div></a>)
           
    }      
   }
  }

  render(){
    return (
         <Carousel className="home_banner" showThumbs={false} showArrows={true} showIndicators={false} showStatus={false}>
               {this.props.banner ? this.props.banner.banners.map(this.generateBanner) : '' }
       </Carousel>
    )
  }
}


function mapStateToProps(state){
  return {
    banner: state.bannerReducer.banner,
  };
}
export default connect(mapStateToProps)(Banner);
