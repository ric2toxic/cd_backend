

class RecentViewSlider extends React.Component {

   constructor(props)
   {
     super(props);
    this.state = {
      data: [],
      products: [],
    };
   } 

   componentDidMount()
  {


  }

  render() {
         var sliderEvent='';
         var i='1';
         var item_active_class='';
         var maxItem='6';
         var allWidth=(99/maxItem);
         //var allWidth=90/maxItem;  //total width is 90%;
         var start='1';
         var first='1';
         var rcentImg='';
         var viewSlider='1';
         var recentViewProduct="recentViewProduct";
         if(window.location.href.indexOf("/staging") > -1) {
          var recentViewProduct="staging_recentViewProduct";
          }
    if( typeof homePage !== 'undefined' ) {
          viewSlider=homePage;
    }
    if (localStorage.getItem(recentViewProduct) && (viewSlider =='1' || this.props.page_type=="home")) {
          sliderEvent +='<div class="home_category_section" id="recentSliderPos">';
          sliderEvent +='<h2 class="page_title home_title recentViewSlider"><span>My Recently Viewed Items</span></h2>';
          sliderEvent +='<div class="clearfix"></div>';
          sliderEvent +='<div class="carousel slide multi-item-carousel" id="RecentViewSlider">';
          sliderEvent +='<div class="carousel-inner related_product_slider">';
          var previousProduct = JSON.parse(localStorage.getItem(recentViewProduct));
            $.each(previousProduct, function(index, value) {
                 if(value.current=='1' && (typeof detailPage !== 'undefined')){
                    first++;
                 }else{
              if(rcentImg==''){
                rcentImg=value.image;
              }
              if(i=='1'){item_active_class=' active';}
              else{item_active_class='';}
              
              if(i!='1'){
                start=i-1;
              }
                if(i=='1' || start%maxItem=='0'){
                  sliderEvent +='<div class="item '+item_active_class+'">';
                }
                    sliderEvent +='<div class="c-product-card new_ outofstock installments_ mastercard c-product-card_js_inited c-product-card_view_grid home-product-list2 productSlide" style="width: '+allWidth+'%;">';
                    sliderEvent +='<div class="c-product-card__img-placeholder min_height_50">';
                    sliderEvent +='<a href="'+value.href+'" class="c-product-card__img-placeholder-inner min_height_50"><span class="c-img-lazy  c-product-card__img  c-img-lazy_js_inited c-img-lazy_loaded">';
                    sliderEvent +='<img class="largeImage c-img-lazy__img" src="'+value.image+'" data-src="'+value.image+'" data-main="'+value.image+'"></span></a></div>';
                    sliderEvent +='<div class="c-product-card__description recntSlideTitle RecentViewSlider_padding">';
                    sliderEvent +='<a class="recntSlideTitle_a" href="'+value.href+'">'+value.title+'</a>';
                    sliderEvent +='</div>';
                    sliderEvent +='<div class="c-product-card__description recntSlidePrz RecentViewSlider_padding">';
                    sliderEvent +='<a class="recntSlidePrz_a RecentViewSlider_price_font" href="'+value.href+'">'+value.price+'</a>';
                    sliderEvent +='</div>';
                    sliderEvent +='</div>';
                  if(i%maxItem=='0' || previousProduct.length==i){
                    sliderEvent +='</div>';
                  }
                       i++;
                   if (i == "31") {
                       return false;
                   }
                 }

             });
            sliderEvent +='</div>';
            sliderEvent +='</div>';
            if(i > 7){
              sliderEvent +='<div class="home_slider_btn" style="">';
              sliderEvent +='<a class="slideLeftIcon" href="#RecentViewSlider" data-slide="prev"><i class="fa fa-angle-left" aria-hidden="true"></i></a>';
              sliderEvent +='<a class="slideRightIcon" href="#RecentViewSlider" data-slide="next"><i class="fa fa-angle-right" aria-hidden="true"></i></a>';
              sliderEvent +='</div>';
            }
            sliderEvent +='</div>';

            if(previousProduct.length=='1' && typeof detailPage !== 'undefined'){
              sliderEvent='';
            }
         }

if (localStorage.getItem(recentViewProduct) && (viewSlider =='1' || this.props.page_type=="home")) {
if(sliderEvent!=''){
$('#recentSlider').html(sliderEvent);
$('#recentSlideBtn').html('<a href="#toRecentView" class="full-slide-recent"><div class="history-wrap"><div class="img util-left" style="background-image: url('+rcentImg+');"></div><div class="view util-left">Recently Viewed</div><i class="fa fa-angle-down" aria-hidden="true"></i></div></a>');

}else{
$('#recentSlideBtn').html('');
}
}
return (<div className={"home_category_section "}>

              <div className="clearfix"></div><div id="recentSlider"></div>
        </div>);

  }
}

