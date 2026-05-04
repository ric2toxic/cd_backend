class ImageCarousel extends React.Component {
    
    constructor(props){
        super(props);
        this.state = {
           data: [],
           is_initial : 0
        };
        
    }

    render() {
 

        var prevNowPlaying = null;
        prevNowPlaying = setInterval(function () {
        var zoom_effact = $('#image-0').attr('src');
        if(zoom_effact != cdn_url+'placeholder.png') { $('#image-0').data('effect', 1); $("#image-0").trigger("click");  clearInterval(prevNowPlaying); }
         }, 500);
      

        var self = this;
        var imgs = this.props.images.map(function(img, index){
                return (
                <li><a href="javascript:;"><ImageList image={img} key={index} count={index} total={self.props.images.length} SITE_ENVIRONMENT={self.props.SITE_ENVIRONMENT} /></a></li>  
                );
        });
         

 $.preload(this.props.preload_img_string);
 
 function category_prev()
 { 
  
  if($('#carousel-category_prev').attr('data') == 0)
  {
     var carousel = $('#carousel-product').carousel({
           vertical: true,
           group: 2,
           loop: false
           });
     $('#carousel-category_prev').attr('data',1);
  }
   $('#carousel-product').carousel('prev');
 } 
                    
 function category_next()
 {
  if($('#carousel-category_prev').attr('data') == 0)
  {
     var carousel = $('#carousel-product').carousel({
           vertical: true,
           group: 2,
           loop: false
           });
     $('#carousel-category_prev').attr('data',1);
  }
   $('#carousel-product').carousel('next');
 } 

        return (
                <div>
                    <column className="col-sm-12 col-md-4 images_section">
                    {this.props.images.length > 0 ?
                 <div className="col-sm-12" id="panzoom-container">
                  <div className="col-sm-3 thum_image" id="gallery_01">
                   {this.props.images.length > 4 ?
                           <a className="image_btn_product" id="carousel-category_prev" data="0" onClick={()=>category_prev()}><i className="fa fa-angle-up" aria-hidden="true"></i>
                            </a>
                    : ''}   
                     {this.props.images.length > 4 ?
                            <a className="image_btn_product" id="carousel-category_next"  data="0" onClick={()=>category_next()}><i className="fa fa-angle-down" aria-hidden="true"></i>
                            </a>
                            : ''}     
                            <div className="ThumbSlider_wrap carousel-inner product_thumbs_box carousel-category_vertical">
                               <ul id="carousel-product">
                               {imgs}
                               </ul>
                            </div>
                 </div>
                 <div className="col-sm-9 nopadding product_full_image">
                 <img src={cdn_url+"placeholder.png"} className="img-responsive" id="img_zoom" data-zoom-image={cdn_url+"placeholder.png"}  />
                   {this.props.pickup_city != '' ?
                    <div className="product_card_city_banner" style={{top:'30px'}}>
                        <div className="card-header-opt">
                          <small>from {this.props.pickup_city}</small>
                        </div>                                            
                    </div>
                    : ''}

                </div>
                  </div>
                  : 
                 <div className="col-sm-12">
                   <div className="col-sm-3"></div>
                   <div className="col-sm-9"><img src={cdn_url+"placeholder.png"} className="img-responsive"  /></div>
                 </div>

                  }
                        
                        <div className="clearfix"></div>
                        <div className="col-sm-12">
                            <div className="col-sm-3 thum_image"> 
                            </div>
                            <div className="col-sm-9 nopadding">
                                <div className="col-sm-12 nopadding">
                                    <div className="col-sm-4 download_pic_2 getImage">
                                      <a href={'./api/image/download_image&product_id='+this.props.product_id+'&model='+this.props.model} target="_blank">
                                      <div style={{width:'100%'}}>
                                        <i className="fa fa-arrow-down" aria-hidden="true"></i> 
                                        Download 
                                        </div> 
                                      </a> 
                                      
                                    </div>
                                </div>
                            </div>
                        </div>
                    </column>
                </div>
    	)
	}
}

