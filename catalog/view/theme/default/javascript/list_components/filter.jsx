class Filter extends React.Component {

   constructor(props)
   {
     super(props);
    this.custom_price = this.custom_price.bind(this);
    this.state = {
      data: [],
      filter_groups:[],
      option_groups:[],
      filter_facets:this.props.filter_facets
    };
   } 

   componentDidMount()
  { 

    let minimum_price = this.props.price_with_currency.minimum_price;
    let maximum_price = this.props.price_with_currency.maximum_price;
    let minimum_search_price = this.props.price_with_currency.minimum_price;
    let maximum_search_price = this.props.price_with_currency.maximum_price;

    if(this.props.search_price != '' && this.props.search_price != 'all') 
     { 
        var res = this.props.search_price;
            res = res.split("-"); 
            minimum_search_price = res[0];
            maximum_search_price = res[1];

        if(maximum_price == 0)
        {
          minimum_price = minimum_search_price;
          maximum_price = maximum_search_price;
        }      
    }

 minimum_price = parseInt(minimum_price);
 maximum_price = parseInt(maximum_price);
 minimum_search_price = parseInt(minimum_search_price);
 maximum_search_price = parseInt(maximum_search_price);
 if(minimum_price < 1){
    minimum_price='1';
 }
 if(minimum_search_price < 1){
    minimum_search_price='1';
 }

    $( "#slider-range" ).slider({
      range: true,
      min: minimum_price,
      max: maximum_price,
      values: [ minimum_search_price, maximum_search_price ],
      slide: function( event, ui ) {

        $( "#min_price_filter" ).val( ui.values[ 0 ]);
        $( "#max_price_filter" ).val( ui.values[ 1 ]);

      }
    });

    $( "#min_price_filter" ).val( $( "#slider-range" ).slider( "values", 0 ) );
    $( "#max_price_filter" ).val( $( "#slider-range" ).slider( "values", 1 ) );


    $(document).delegate('#min_price_filter, #max_price_filter', 'keyup', function(e)
     { 
       var node = $('#min_price_filter');
       node.val(node.val().replace(/[^0-9.]/g,'') );

       var node2 = $('#max_price_filter');
       node2.val(node2.val().replace(/[^0-9.]/g,'') );

       var min_price_filter = parseInt($("#min_price_filter").val());
       var max_price_filter = parseInt($("#max_price_filter").val());

       if(min_price_filter < 1){
            min_price_filter='1';
          }

           $( "#slider-range" ).slider({
             values: [ min_price_filter, max_price_filter ]
            }); 
    
     });

    $(document).ready(function() {

      $('.filtar_scroll_bar').scrollToFixed({
        marginTop: function() {
           var marginTop = window.innerHeight - $('.filtar_scroll_bar').outerHeight(true) + 20;
            if (marginTop >= 0)
            {
              return 0;
            } 
            return marginTop;
        },
        limit: function() {
                var limit = $('.category_description').offset().top - $('.filtar_scroll_bar').outerHeight(true) - 10;
                return limit;
            },
        zIndex: 10,    
      });

   });


  }

  componentWillReceiveProps()
  {
    let minimum_price = parseInt(this.props.price_with_currency.minimum_price);
    let maximum_price = parseInt(this.props.price_with_currency.maximum_price);
    let minimum_search_price = parseInt(this.props.price_with_currency.minimum_price);
    let maximum_search_price = parseInt(this.props.price_with_currency.maximum_price);

    if(this.props.search_price != '' && this.props.search_price != 'all') 
     { 
        var res = this.props.search_price;
            res = res.split("-"); 
            minimum_search_price = res[0];
            maximum_search_price = res[1]; 
        if(maximum_price == 0)
        {
          minimum_price = parseInt(minimum_search_price);
          maximum_price = parseInt(maximum_search_price);
        } 
    }
    if(minimum_price <'1'){
        minimum_price='1';
    }
    if(minimum_search_price <'1'){
        minimum_search_price='1';
    }
     $( "#slider-range" ).slider({
      range: true,
      min: minimum_price,
      max: maximum_price,
      values: [ minimum_search_price, maximum_search_price ],
      slide: function( event, ui ) {

        $( "#min_price_filter" ).val( ui.values[ 0 ]);
        $( "#max_price_filter" ).val( ui.values[ 1 ]);

      }
    });

    $( "#min_price_filter" ).val( $( "#slider-range" ).slider( "values", 0 ) );
    $( "#max_price_filter" ).val( $( "#slider-range" ).slider( "values", 1 ) );

  }


    custom_price(type='')
   {
     if($("#min_price_filter").val() == '' || parseInt($("#min_price_filter").val()) < parseInt(this.props.price_with_currency.minimum_price) || parseInt($("#min_price_filter").val()) > parseInt(this.props.price_with_currency.maximum_price))
     {  
        var min_price_filter = this.props.price_with_currency.minimum_price;
     }
     else
     {
        var min_price_filter = $("#min_price_filter").val();
     }

     if($("#max_price_filter").val() == '' || parseInt($("#max_price_filter").val()) < parseInt(this.props.price_with_currency.minimum_price) || parseInt($("#max_price_filter").val()) > parseInt(this.props.price_with_currency.maximum_price))
     {
        var max_price_filter = this.props.price_with_currency.maximum_price;
     }
     else
     {
        var max_price_filter = $("#max_price_filter").val();
     }
     

     if(type == 'clear')
     {
        $("#custom_price_filter").val('all');
     }
     else
     {
        $("#custom_price_filter").val(min_price_filter+'-'+max_price_filter);
     }

        $("#custom_price_filter").click();

   }

  render() {

    let self = this;
    var rating_filter_5;
    var rating_filter_4;
    var rating_filter_3;
    var rating_filter_all;

    var price_filter_1;
    var price_filter_2;
    var price_filter_3;
    var custom_price_filter;

    var stock_filter_1;
    var stock_filter_2;
    var filter_facets = [];

    if(this.props.search_rating == 5)     { rating_filter_5 = 'true'; } 
    else if(this.props.search_rating == 4)     { rating_filter_4 = 'true'; } 
    else if(this.props.search_rating == 3)     { rating_filter_3 = 'true'; } 
    else { rating_filter_all = 'true'; }

    if(this.props.search_price == 'all' || this.props.search_price == '') { price_filter_1 = 'true'; }
    else if(this.props.search_price == '500-900') { price_filter_2 = 'true'; } 
    else if(this.props.search_price == '1000-1500') { price_filter_3 = 'true'; }  
    else { 
         custom_price_filter = 'true'; 
         }  
   
  
  if(this.props.stock_filter == 1) { stock_filter_2 = 'true'; }
  else { stock_filter_1 = 'true'; } 


  let filter_groups = '';
  let filter_groups_more = '';

  if(this.props.filter_facets == '-1'){   
     filter_groups = '';     
  }
  else if(Object.keys(this.props.filter_facets).length > 0){   

     filter_facets = Object.keys(self.props.filter_facets.filters).map(function(key) {
       return self.props.filter_facets.filters[key];
      });

     if(filter_facets.length > 0)
     {
        filter_groups =  filter_facets.map(function(groups, index) {
                 return <FilterGroup key={index} count={index} groups={groups} clickHandler={self.props.clickHandler} search_filter={self.props.search_filter} />
              });
         
      filter_groups_more =  filter_facets.map(function(groups, index) { 
        if(Object.keys(groups.filter).length > 7) {
            return (<FilterMorePopup key={index} groups={groups} clickHandlerMultiFilter={self.props.clickHandlerMultiFilter} search_filter={self.props.search_filter} />)
         }
        });

      } 

              
  }
  else
  {
    filter_groups = <FilterLoading />
  } 


/*let filter_option_groups = '';
  if(this.state.option_groups && this.state.option_groups.length > 0){  
  filter_option_groups = this.state.option_groups.map(function(groups, index) {
                 return <OptionGroup key={index} count={index} groups={groups} search_option={self.props.search_option} clickHandlerOption={self.props.clickHandlerOption} />
              })
    }  */        



   $('.list-group_title').click(function(){
          if(!$(this).hasClass('collapsed')){
             $(this).addClass('arrow_rotate');
          }
          else{
              $(this).removeClass('arrow_rotate'); 
          }
       });

    function createMarkup(html) {
       return {__html: html};
     }

    return (<column className="col-sm-2 filtar_section nopadding">
            <div className="filtar_scroll_bar">

            <div className="filter_box">
              <div className="filter_penal_box" id="list-group-filter">
                  <a className="list-group_title_noarrow">Rating</a>
                  <div className="filtar_section_content">
                    <div className="filter-group">
                        <div className="rating radio rating_filter">
                         {this.props.rating_filter && this.props.rating_filter['5.0'] ?
                          <label>
                            <div className="filter_radio_btn control--radio">
                              <input id="rating_filter_5" name="rating_filter" checked={rating_filter_5} value="5" type="radio" onChange={this.props.clickHandlerRating} />
                              <div className="control__indicator"></div>
                            </div>
                            <span className="label rating_span label-success">Excellent Quality</span>
                            <br />
                          </label>
                          : 
                          <label className="disable">
                            <div className="filter_radio_btn control--radio">
                              <input id="rating_filter_5" name="rating_filter" checked={rating_filter_5} value="5" type="radio" onChange={this.props.clickHandlerRating} />
                              <div className="control__indicator"></div>
                            </div>
                            <span className="label rating_span label-success">Excellent Quality</span>
                            <br />
                          </label>
                           }
                          
                          {this.props.rating_filter && this.props.rating_filter['4.0'] ?
                          <label>
                            <div className="filter_radio_btn control--radio">
                              <input id="rating_filter_4" name="rating_filter" checked={rating_filter_4} value="4" type="radio" onChange={this.props.clickHandlerRating} />
                              <div className="control__indicator"></div>
                            </div>
                            <span className="label rating_span label-warning">Good Quality</span>
                            <br />
                          </label> 
                          : 
                           <label className="disable">
                            <div className="filter_radio_btn control--radio">
                              <input id="rating_filter_4" name="rating_filter" checked={rating_filter_4} value="4" type="radio" onChange={this.props.clickHandlerRating} />
                              <div className="control__indicator"></div>
                            </div>
                            <span className="label rating_span label-warning">Good Quality</span>
                            <br />
                          </label>
                           }
                         
                         {!this.props.international_store ?
                          this.props.rating_filter && this.props.rating_filter['3.0'] ?
                          <label>
                            <div className="filter_radio_btn control--radio">
                              <input id="rating_filter_3" name="rating_filter" checked={rating_filter_3} value="3" type="radio" onChange={this.props.clickHandlerRating} />
                              <div className="control__indicator"></div>
                            </div>
                            <span className="label rating_span label-danger">Average Quality</span>
                            <br />
                          </label>
                             
                          : 
                           <label className="disable">
                            <div className="filter_radio_btn control--radio">
                              <input id="rating_filter_3" name="rating_filter" checked={rating_filter_3} value="3" type="radio" onChange={this.props.clickHandlerRating} />
                              <div className="control__indicator"></div>
                            </div>
                            <span className="label rating_span label-danger">Average Quality</span>
                            <br />
                          </label>

                          :'' }  


                          <label>
                            <div className="filter_radio_btn control--radio">
                              <input id="rating_filter_all" name="rating_filter" checked={rating_filter_all} value="all" type="radio" onChange={this.props.clickHandlerRating} />
                              <div className="control__indicator"></div>
                            </div>
                            <span className="label rating_span label-default">All</span>
                          </label>                                    
                        </div>
                    </div>
                  </div>
              </div>
            </div>

            <div className="filter_box">
              <div className="filter_penal_box" id="list-group-filter">
                  <a className="list-group_title" data-toggle="collapse" href="#price_filter" dangerouslySetInnerHTML={createMarkup('Price ('+this.props.price_with_currency.symbol+')')}></a>
                  <div className="filtar_section_content collapse in" id="price_filter">
                    <div className="filter-group">
                    
                      <div id="slider-range"></div>

                    </div>
                    <div className="price_drop_filters">
                     <div className="col-sm-4 nopadding"> 
                  
                     <input type="text" name="min_price_filter" min="1" id="min_price_filter" className="price_drop_filters_left" /></div>
                     <div className="col-sm-3 price_to"><span>To</span></div>
                     <div className="col-sm-4 nopadding"> 
                     
                     <input type="text" name="max_price_filter" min="1" id="max_price_filter" className="price_drop_filters_right" /></div>
                      <div className="clearfix"></div>
                      <div className="filter_radio_btn hide">
                              <input name="custom_price_filter" id="custom_price_filter" checked={custom_price_filter} type="radio"  onChange={this.props.clickHandlerPrice} />
                            </div>

                      <a className="pull-left apply_btn price_filter_apply" href="javascript:;" type="button" onClick={()=> this.custom_price('clear') }>CLEAR</a>    
                     
                     {!parseInt(this.props.price_with_currency.maximum_price) ?
                      <button className="btn deliver_btn pull-right apply_btn price_filter_apply disable" type="button">Apply</button>
                      :
                      <button className="btn deliver_btn pull-right apply_btn price_filter_apply" type="button" onClick={()=> this.custom_price() }>Apply</button>
                      }  
                       <div className="clearfix"></div>
                    </div>
                  </div>
              </div>
            </div>
           
            {filter_groups}
           
           
            <div className="filter_box">
              <div className="filter_penal_box" id="list-group-filter">
                  <a className="list-group_title arrow_rotate collapsed" data-toggle="collapse" href="#list-group-filter-sort">Product Stock</a>
                  <div className="filtar_section_content  collapse" id="list-group-filter-sort">
                    <div className="filter-group">
                      <div className="rating radio rating_filter">
                        <label>
                            <div className="filter_radio_btn control--radio">
                              <input name="stock_filter" value="0" type="radio" checked={stock_filter_1} onChange={this.props.clickHandlerStock} />
                              <div className="control__indicator"></div>
                            </div>
                            <span>In Stock</span>
                          </label>
                          <br />
                          <label>
                            <div className="filter_radio_btn control--radio">
                              <input name="stock_filter" value="1" type="radio" checked={stock_filter_2} onChange={this.props.clickHandlerStock} />
                              <div className="control__indicator"></div>
                            </div>
                            <span>All Stock</span>
                          </label> 
                       </div>   
                    </div>
                  </div>
              </div>
            </div>

          </div>


         {filter_groups_more}

           </column>);
  }
}