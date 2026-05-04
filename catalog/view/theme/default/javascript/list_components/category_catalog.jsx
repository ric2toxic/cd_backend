class CategoryCatalog extends React.Component {

   constructor(props)
   {
     super(props);
      this.state = {
      filters   :   this.props.filter_array.filters,
      price     :   this.props.filter_array.price,
      price_min :   this.props.price_with_currency.minimum_price,
      price_max :   this.props.price_with_currency.maximum_price,
      rating    :   this.props.filter_array.rating,
      }
      this.handleRenderBrand = this.handleRenderBrand.bind(this);
      this.handleRenderPrice = this.handleRenderPrice.bind(this);


    $(document).on('click','.filter_brand_img',function(){
          var filterId=$(this).attr('rel');
          $('#filter'+filterId).trigger('click');
    });

    

    $(document).on('click','.priceContent',function(){
          var min=Math.round($(this).attr('min'));
          var max=Math.round($(this).attr('max'));
          $('#min_price_filter').val(min);
          $('#max_price_filter').val(max);
          $('.price_filter_apply').trigger('click');
    });

   } 

handleRenderPrice(){
      var pricetotalItems = $('#PriceViewSlider .item').length;
      if(pricetotalItems>1){
          if(pricetotalItems > 4){
            pricetotalItems='4';
          }
          $('#PriceViewSlider .item').each(function(){
            var itemToClone = $(this);
            for (var i=1;i<pricetotalItems;i++) {
              itemToClone = itemToClone.next();     
              // wrap around if at end of item collection
              if (!itemToClone.length) {
                itemToClone = $(this).siblings(':first');
              }             
              // grab item, clone, add marker class, add to collection
              itemToClone.children(':first-child').clone()
                .addClass("cloneditem-"+(i))
                .addClass("rightest")
                .appendTo($(this)); 
              //listener for after slide
                jQuery('.carousel').on('slid.bs.carousel', function(){
              //Each slide has a .item class to it, you can get the total number of slides like this
                    var totalItems = jQuery('.carousel .item').length;
              //find current slide number
                    var currentIndex = jQuery('.carousel .item div.active').index() + 1;
              //if slide number is last then stop carousel
                  if(totalItems == currentIndex){
                    clearInterval(jQuery('.carousel .item').data('bs.carousel').interval);
                  } // end of if
           });
            }
          });
        }

}
handleRenderBrand(){
   var brandtotalItems = $('#brandSlider .item').length;
      if(brandtotalItems>1){
          if(brandtotalItems > 4){
            brandtotalItems='4';
          }
          $('#brandSlider .item').each(function(){
            var itemToClone = $(this);
            for (var i=1;i<brandtotalItems;i++) {
              itemToClone = itemToClone.next();     
              // wrap around if at end of item collection
              if (!itemToClone.length) {
                itemToClone = $(this).siblings(':first');
              }             
              // grab item, clone, add marker class, add to collection
              itemToClone.children(':first-child').clone()
                .addClass("cloneditem-"+(i))
                .addClass("rightest")
                .appendTo($(this)); 
              //listener for after slide
                jQuery('.carousel').on('slid.bs.carousel', function(){
              //Each slide has a .item class to it, you can get the total number of slides like this
                    var totalItems = jQuery('.carousel .item').length;
              //find current slide number
                    var currentIndex = jQuery('.carousel .item div.active').index() + 1;
              //if slide number is last then stop carousel
                  if(totalItems == currentIndex){
                    clearInterval(jQuery('.carousel .item').data('bs.carousel').interval);
                  } // end of if
           });
            }
          });
        }


}

   componentDidUpdate(prevProps){
    var brand_key='';
    $.each(this.props.filter_array.filters, function( index, value ) {
                        if(value.group_label.toLowerCase() == "brand" || value.group_label.toLowerCase() == "brand name"){
                        brand_key = index;
                        }
                      });
    //console.log(this.props.filter_array.filters);
   // console.log(prevProps.filter_array.filters.length);
      if(this.props.search_price != prevProps.search_price || this.props.filter_array.filters[brand_key]['filter'].length!=prevProps.filter_array.filters[brand_key]['filter'].length || this.props.search_filter !=prevProps.search_filter){
        this.handleRenderPrice();
        this.handleRenderBrand();
      }

    }

   componentDidMount()
  {
      this.handleRenderPrice();
      this.handleRenderBrand();
  }



  render() {
    var html='';
    function createMarkup(html,sliderClass='', produCountVal) {
      var complete_html='';
      var data_ride = '';
        if(produCountVal > 4){
          data_ride = 'data-ride="carousel"';
        }
        complete_html +='<div id="brandSlider" class="carousel slide '+sliderClass+'" ' + data_ride + '>';
        complete_html +='<div class="carousel-inner">';
        complete_html +=html;
        complete_html +='</div>';

        if(produCountVal > 4){
          complete_html +='<a class="left_arrow pull-left" href="#brandSlider" data-slide="prev">';
          complete_html +='<span class="fa fa-chevron-left"></span>';
          complete_html +='</a>';
          complete_html +='<a class="right_arrow pull-right" href="#brandSlider" data-slide="next">';
          complete_html +='<span class="fa fa-chevron-right"></span>';
          complete_html +='</a>';
        }
        
        complete_html +='</div> ';
        
        return {__html: complete_html};
      }

    function precisionRound(number) {
      var factor = Math.pow(10, -1);
      return Math.round(number * factor) / factor;
    }

    function priceRangeDiv(min,max,symbol){
      var firstNum= '0';
      var secondNum= '0';
      var last= '0';
      var priceType='1';
      var break_value = '';
      // using for css
      var text_center = '';

      if(promotionArray.indexOf("brand")>=0 && promotionArray.indexOf("price_range")>=0){
        break_value = '<br />';
        text_center = 'text_center';
      }

      var totalDiff=max-min;
      if(totalDiff=='0'){
          return {__html: '<div></div>'};
      }
      if(totalDiff>=11){
        var partition=Math.round(totalDiff/4);
            if(partition > 10){
            var firstNum=precisionRound(parseInt(min)+parseInt(partition));
            var secondNum=precisionRound(parseInt(firstNum)+parseInt(partition));
            var last=precisionRound(parseInt(secondNum)+parseInt(partition));
          }else{
            var firstNum=parseInt(min)+parseInt(partition);
            var secondNum=parseInt(firstNum)+parseInt(partition);
            var last=parseInt(secondNum)+parseInt(partition);
          }

        }
        else if(totalDiff >= 6){
            var partition=Math.round(totalDiff/2);
            var firstNum=parseInt(min)+parseInt(partition);
            var last=firstNum;
            var priceType='2';

        }else{
          var last=parseInt(max)+parseInt(1);
          var priceType='3';
        }
        if(priceType=='1'){
          var priceHtml ='';
                priceHtml +='<div class="price_range_new">';
                priceHtml +='<div class="col-sm-12 ShopByBrand_hedline"><h5><strong>SHOP BY PRICE</strong></h5></div>';       
                 priceHtml +=' <div class="col-sm-12 ShopByBrand_toggle"> ';         
                  priceHtml +='  <div id="PriceViewSlider" class="carousel slide four">';
                    priceHtml +='  <div class="carousel-inner">';
                    //====under===//
                           priceHtml +=' <div class="item active">';
                            priceHtml +='  <div>';
                              priceHtml +='  <a href="javascript:void(0);"><div class="col-sm-3 cad_landing_padding_right_zero"><div class="Rupees_div"><div class="forth_Rupees_div"><p class="priceContent '+text_center+'" min="'+min+'" max="'+firstNum+'">Under '+ break_value+' &#8377 '+firstNum+'</p></div></div></div></a>';
                            priceHtml +='</div>';
                            priceHtml +=' </div>';
                     //====middile Div===//
                             priceHtml +=' <div class="item">';
                               priceHtml +=' <div>';
                                 priceHtml +=' <a href="javascript:void(0);"><div class="col-sm-3 cad_landing_padding_right_zero"><div class="Rupees_div"><div class="forth_Rupees_div"><p class="priceContent '+text_center+'" min="'+firstNum+'" max="'+secondNum+'">&#8377 '+firstNum+ break_value + ' to '+break_value+' &#8377 '+secondNum+'</p></div></div></div></a>';
                               priceHtml +=' </div>';
                             priceHtml +=' </div>';
                             priceHtml +=' <div class="item">';
                             priceHtml +='  <div>';
                                 priceHtml +=' <a href="javascript:void(0);"><div class="col-sm-3 cad_landing_padding_right_zero"><div class="Rupees_div"><div class="forth_Rupees_div"><p class="priceContent '+text_center+'"  min="'+secondNum+'" max="'+last+'">&#8377 '+secondNum+ break_value+ ' to '+break_value+' &#8377 '+last+'</p></div></div></div></a>';
                              priceHtml +='</div>';
                              priceHtml +=' </div>';
                       //====last Div===//
                              priceHtml +='<div class="item">';
                                priceHtml +='<div>';
                                 priceHtml +=' <a href="javascript:void(0);"><div class="col-sm-3 cad_landing_padding_right_zero"><div class="Rupees_div"><div class="forth_Rupees_div"><p class="priceContent '+text_center+'" min="'+last+'" max="'+max+'">Above '+ break_value+' &#8377 '+last+'</p></div></div></div></a>';
                                 priceHtml +='</div>';
                              priceHtml +='</div>';
                            priceHtml +='</div>';
                            //priceHtml +='<a class="left_arrow pull-left" href="#PriceViewSlider" data-slide="prev"><span class="fa fa-chevron-left"></span></a>';
                            //priceHtml +='<a class="right_arrow pull-right" href="#PriceViewSlider" data-slide="next"><span class="fa fa-chevron-right"></span></a>';
                    priceHtml +='</div> ';         
                  priceHtml +='</div>';
              priceHtml +='</div>';
        }
        else if(priceType=='2'){
          var priceHtml ='';
              priceHtml +='<div class="price_range_new">';
                priceHtml +='<div class="col-sm-12 ShopByBrand_hedline"><h6>Price Range</h6></div>';       
                 priceHtml +=' <div class="col-sm-12 ShopByBrand_toggle"> ';         
                  priceHtml +='  <div id="PriceViewSlider" class="carousel slide two" data-ride="carousel">';
                    priceHtml +='  <div class="carousel-inner">';
                    //====under===//
                           priceHtml +=' <div class="item active">';
                            priceHtml +='  <div>';
                              priceHtml +='  <a href="javascript:void(0);"><div class="col-sm-6"><div class="Rupees_div"><div class="two_logo_img"><p class="priceContent '+text_center+'" min="'+min+'" max="'+firstNum+'">Under '+ break_value+' &#8377 '+firstNum+'</p></div></div></div></a>';
                            priceHtml +='</div>';
                            priceHtml +=' </div>';
                       //====last Div===//
                              priceHtml +='<div class="item">';
                                priceHtml +='<div>';
                                 priceHtml +=' <a href="javascript:void(0);"><div class="col-sm-6"><div class="Rupees_div"><div class="two_logo_img"><p class="priceContent '+text_center+'" min="'+last+'" max="'+max+'">Above '+ break_value+' &#8377 '+last+'</p></div></div></div></a>';
                                 priceHtml +='</div>';
                              priceHtml +='</div>';
                            priceHtml +='</div>';
                            priceHtml +='</div> ';         
                  priceHtml +='</div>';
              priceHtml +='</div>';
        }else{
          var priceHtml ='';
         priceHtml +='<div class="price_range_new">';
                priceHtml +='<div class="col-sm-12 ShopByBrand_hedline"><h6>Price Range</h6></div>';       
                 priceHtml +=' <div class="col-sm-12 ShopByBrand_toggle"> ';         
                  priceHtml +='  <div id="PriceViewSlider" class="carousel slide" data-ride="carousel">';
                    priceHtml +='  <div class="carousel-inner">';
                       //====last Div===//
                              priceHtml +='<div class="item">';
                                priceHtml +='<div>';
                                 priceHtml +=' <a href="javascript:void(0);"><div class="col-sm-12"><div class="Rupees_div"><div class="one_logo_img"><p class="priceContent '+text_center+'" min="'+last+'" max="'+max+'">Above '+break_value+' &#8377 '+last+'</p></div></div></div></a>';
                                 priceHtml +='</div>';
                              priceHtml +='</div>';
                            priceHtml +='</div>';
                            priceHtml +='</div> ';         
                  priceHtml +='</div>';
              priceHtml +='</div>';
        }
       return {__html: priceHtml};

    }
     var search_filter=this.props.search_filter.length;
     var promotionArray=this.props.promotion;
     return (
      <div>
{
       promotionArray.length > 0? 
     <div className="cad_landing_parent_div">
     {
      search_filter == 0 && promotionArray.indexOf("brand")>=0 && (this.props.search_price=='' || this.props.search_price=='all')?
      this.state.filters?
Object.keys(this.state.filters).map((item, key) =>{
                  if(this.state.filters[item]['group_label'].toLowerCase() == "brand" || this.state.filters[item]['group_label'].toLowerCase() == "brand name") {
                     var i='0';
                      var k='0';
                      var maxItem='3';
                      var start='';
                      var sliderEvent='';
                      var brandProductlength=this.state.filters[item]['filter'].length;
                      var produCountVal='0';
                      $.each(this.state.filters[item]['filter'], function( index, value ) {
                        if(value.product_count>0 && value.image.indexOf('placeholder.png') == -1)
                        {
                            produCountVal++;
                        }
                      });
                      var sliderClass='';
                      if(produCountVal > '3'){
                        sliderClass='four';
                      }
                        else if(produCountVal == '3'){
                           sliderClass='three';
                        }
                          else if(produCountVal == '2'){
                              sliderClass='two';
                      }


                                    if(produCountVal > '0'){
                                      // using for css
                                      let text_center = '';

                                      if(promotionArray.indexOf("brand")>=0 && promotionArray.indexOf("price_range")>=0){
                                        text_center = 'text_center';
                                      }

            return(
              <div className="brand_price_filters landing">
              <div className={promotionArray.indexOf("brand")>=0 && promotionArray.indexOf("price_range")>=0?'col-sm-6 categoryLandingBrandMain':'col-sm-12 categoryLandingBrandMain'}>
              <div className="brand">
              <div className="col-sm-12 ShopByBrand_hedline"><h5><strong>SHOP BY BRAND</strong></h5></div>        
              <div className="col-sm-12 ShopByBrand_toggle">          
                  {
              this.state.filters[item]['filter'].map((filter_val, index) => {
               if(filter_val.image.indexOf('placeholder.png') == -1) 
               { 
                k=index+1;
                if(filter_val.product_count>0){ 
                                  i++; 
                                  if(i=='1'){
                                    var item_active_class=' active';
                                  } else{
                                    var item_active_class='';
                                  } if(i!='1'){ start=i-1; } 
                                                        if(produCountVal>3){ 
                                                                html +=i==1?'<div class="item active">':'<div class="item">';
                                                                html +='<div>'; 
                                                                html +='<a href="javascript:void(0);">';
                                                                html +='<div class="col-sm-3 filter_brand_img cad_landing_padding_right_zero" rel="'+filter_val.filter_id+'">';
                                                                var matches = filter_val.image.match(/placeholder./g);
                                                                if(matches || filter_val.image == ''){
                                                                html +='<div class="Rupees_div"> ';
                                                                html +='<div class="forth_Rupees_div">';
                                                                if(filter_val.name != ''){
                                                                html +='<p class="'+text_center+'">'+filter_val.name+'</p>';
                                                                }else{
                                                                html +='<p>&nbsp;</p>'; 
                                                                }
                                                                html +='</div>';
                                                                html +='</div>';
                                                                }else{
                                                                html +='<div class="four_logo_img"><img src="'+filter_val.image+'" /></div>'; 
                                                                }
                                                                html +='</div>';
                                                                html +='</a>'; 
                                                                html +='</div>'; 
                                                                html +='</div>'; 
                                                        }else if(produCountVal=='3'){ 
                                                              html +=i==1?'<div class="item active">':'<div class="item">'; 
                                                              html +='<div>';
                                                              html +='<a href="javascript:void(0);">';
                                                              html +='<div class="col-sm-4 filter_brand_img" rel="'+filter_val.filter_id+'">';
                                                              var matches = filter_val.image.match(/placeholder./g);
                                                              if(matches || filter_val.image == ''){
                                                              html +='<div class="Rupees_div"> ';
                                                              html +='<div class="forth_Rupees_div">';
                                                              if(filter_val.name != ''){
                                                              html +='<p>'+filter_val.name+'</p>';
                                                              }else{
                                                              html +='<p>&nbsp;</p>'; 
                                                              }
                                                              html +='</div>';
                                                              html +='</div>';
                                                              }else{
                                                              html +='<div class="four_logo_img"><img src="'+filter_val.image+'" /></div>'; 
                                                              }
                                                              html +='</div>';
                                                              html +='</a>'; 
                                                              html +='</div>';
                                                              html +='</div>'; 
                                                        }
                                                        else if(produCountVal=='2'){ 
                                                                html +=i==1?'<div class="item active">':'<div class="item">'; 
                                                                html +='<div>'; 
                                                                html +='<a href="javascript:void(0);">';
                                                                html +='<div class="col-sm-6 filter_brand_img" rel="'+filter_val.filter_id+'">';
                                                                var matches = filter_val.image.match(/placeholder./g);
                                                                if(matches || filter_val.image == ''){
                                                                html +='<div class="Rupees_div"> ';
                                                                html +='<div class="forth_Rupees_div">';
                                                                if(filter_val.name != ''){
                                                                html +='<p>'+filter_val.name+'</p>';
                                                                }else{
                                                                html +='<p>&nbsp;</p>'; 
                                                                }
                                                                html +='</div>';
                                                                html +='</div>';
                                                                }else{
                                                                html +='<div class="four_logo_img"><img src="'+filter_val.image+'" /></div>'; 
                                                                }
                                                                html +='</div>';
                                                                html +='</a>'; 
                                                                html +='</div>';
                                                                html +='</div>'; 
                                                          }else{ 
                                                              html += i==1 ? '<div class="item active">':'<div class="item">'; 
                                                              html +='<div>'; 
                                                              html +='<a href="javascript:void(0);">';
                                                              html +='<div class="col-sm-12 filter_brand_img" rel="'+filter_val.filter_id+'">';
                                                              var matches = filter_val.image.match(/placeholder./g);
                                                             if(matches || filter_val.image == ''){
                                                              html +='<div class="Rupees_div"> ';
                                                              html +='<div class="forth_Rupees_div">';
                                                              if(filter_val.name != ''){
                                                              html +='<p>'+filter_val.name+'</p>';
                                                              }else{
                                                               html +='<p>&nbsp;</p>'; 
                                                              }
                                                              html +='</div>';
                                                              html +='</div>';
                                                              }else{
                                                               html +='<div class="four_logo_img"><img src="'+filter_val.image+'" /></div>'; 
                                                              }
                                                              html +='</div>';
                                                              html +='</a>'; 
                                                              html +='</div>'; 
                                                              html +='</div>'; 
                                                                } 
                        } 
                                                if(produCountVal==k){
                                                      return(
                                                      <div>
                                                          { produCountVal==k?
                                                          <div dangerouslySetInnerHTML={createMarkup(html,sliderClass,produCountVal)} /> :'' }
                                                      </div>
                                                        )
                                                }
                                                       
                 }
                 })

              }
                       
              </div>
              </div>  
              </div>
              </div>
          )
}



                    }
      })

      
      
:''
:''
}
  
      { 
        search_filter == 0  && promotionArray.indexOf("price_range")>=0 && (this.props.search_price=='' || this.props.search_price=='all')?
      this.state.filters?
      this.state.price_min != this.state.price_max ?
      <div className={promotionArray.indexOf("brand")>=0 && promotionArray.indexOf("price_range")>=0?'col-sm-6 categoryLandingPriceMain':'col-sm-12 categoryLandingPriceMain no-padding'}>
      <div className="categoryLandingPrice" dangerouslySetInnerHTML={priceRangeDiv(this.state.price_min,this.state.price_max,this.props.price_with_currency.symbol)} />
      </div>
      :''
      :''
      :''
    } 
  </div>
:''
      }
      </div>
     ); 
      
  }
}