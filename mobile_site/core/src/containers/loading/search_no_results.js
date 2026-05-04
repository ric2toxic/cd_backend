import React, { Component } from 'react'
import { Link } from 'react-router-dom'
import Api from '../../api/Api'
import PopularTagsListpage from './popular_tags_listpage'
import $ from 'jquery'

class SearchNoResults  extends Component {


render() {

    return(<section> 
      <div className="container-fluid width_fix"> 
       <div className="row">
        <div className="col-sm-12" style={{textAlign: 'center', margin: '30px 0px'}}>
          <div className="no_found_img" style={{margin: '30px 0px'}} >
            <img src="https://cdnimages.net/img/No_Product_Found.png" alt="Product not found" />
          </div>
           <div className="no_found_text" style={{wordBreak:'break-word'}}>
           <p style={{fontSize: '17px', color: '#ff0000'}}> OOPS! we couldn't find "{this.props.search}"!  </p>
           <p style={{fontSize: '15px'}}> Please check your spelling! or Try searching again with correct keyword  </p>
           {this.props.stock_filter === 0 ?
            <p style={{fontSize: '15px'}}> To find in out of stock <span onClick={this.props.search_out_of_stock} className="green_text">click here</span> </p>
           : ''
           }
           <div className="row" style={{width: '90%', margin: 'auto'}}>
           <div className="col-sm-5" style={{textAlign: 'left', padding: '0px'}}>
           POPULAR SEARCHES: <PopularTagsListpage />
           </div>

           </div>
           </div>

            <div className="no_found_menu_area" style={{width: '92%', margin: 'auto'}}>

              <div className="purchasing_saction">
                <div className="purchasing_body">

                 {this.props.menus && this.props.menus.length ?
                      this.props.menus.map(function(menu, index) {
                         return(<div className={"col-sm-4 purchasing_category section_"+index} style={{'backgroundImage':'url('+menu.image+')'}}>
                              <div className="purchasing_category_text no_found_menu" > <Link to={menu.href}>{menu.link_title}</Link></div>
                                <ul className="no_found_submenu">
                                { menu.children.map(function(item, index) {
                                     return (<li> <Link to={Api.folder_path+item.mobile_href}>{ $('<div/>').html(item.link_title).text()}</Link></li>) 
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

export default SearchNoResults;