
class SearchNoResults extends React.Component {


  decode_html(text)
  {
     var decoded = $('<div/>').html(text).text();
     return decoded;
  }

render() {

function generateChildren(menu)
  {

   menu.children.map(function(item, index) {
    return (<li> <a href={item.href}>{ this.decode_html(item.link_title) }</a></li>) 
    })

   }

    return(<section> 
      <div className="container-fluid width_fix"> 
       <div className="row">
        <div className="col-sm-12" style={{textAlign: 'center', margin: '30px 0px'}}>
          <div className="no_found_img" style={{margin: '30px 0px'}} >
            <img src={cdn_url+"No_Product_Found.png"} />
          </div>
           <div className="no_found_text">
           <p style={{fontSize: '17px', color: '#ff0000'}}> OOPS! we couldn't find "{this.props.search}"!  </p>
           <p style={{fontSize: '15px'}}> Please check your spelling! or Try searching again with correct keyword  </p>
           {this.props.stock_filter==0 ?
            <p style={{fontSize: '15px'}}> To find in out of stock <a href="javascript:;" onClick={this.props.search_out_of_stock}><span className="green_text">click here</span></a> </p>
           : ''
           }
           <div className="row" style={{width: '90%', margin: 'auto'}}>
           <div className="col-sm-5" style={{textAlign: 'right', padding: '0px'}}>
           POPULAR SEARCHES:
           </div>
           <div className="col-sm-7"  style={{textAlign: 'left', padding: '0px'}}><PopularTagsListpage /></div>
           </div>
           </div>

            <div className="no_found_menu_area" style={{width: '92%', margin: 'auto'}}>

              <div className="purchasing_saction">
                <div className="purchasing_body">

                 {this.props.menus.length ?
                      this.props.menus.map(function(menu, index) {
                         return(<div className={"col-sm-4 purchasing_category section_"+index} style={{'backgroundImage':'url('+menu.image+')'}} onClick={()=> set_preferences_cookie('preferences', menu.value, 7)}>
                              <div className="purchasing_category_text no_found_menu" > <a href={menu.href}>{menu.link_title}</a></div>
                                <ul className="no_found_submenu">
                                { menu.children.map(function(item, index) {
                                     return (<li> <a href={item.href}>{item.link_title}</a></li>) 
                                 }) }
                                </ul>
                           </div>)

                      })
                    : ''  
                    }

                  <div className="clearfix"></div>
                  </div></div>

            </div>
        </div>
        </div>
       </div> 
    </section>)
	}
}