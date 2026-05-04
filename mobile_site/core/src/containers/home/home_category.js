import React, { Component } from 'react'
import $ from 'jquery'
import { Link } from 'react-router-dom'
import Api from '../../api/Api'

class HomeCategory extends Component {

 render(){
let self = this;
  return(<div className={"category scroll-x "+this.props.menu_selected}>
         { this.props.menus.map((submenu, index) => {
          return(<div key={index} className="col-xs-4 no-padding category_card">
         {submenu.children ?
          <span onClick={()=>self.props.SubCategoryPopupOpen(submenu)}>
          <div className="category_image"><img src={submenu.image} alt="" /></div>
            <div className="category_description">
            {$('<div/>').html(submenu.link_title.substr(0, 10)+'..').text()}
           </div>
           </span>
          :
          <Link to={Api.folder_path+submenu.href}>
          <div className="category_image"><img src={submenu.image} alt="" /></div>
            <div className="category_description">
            {$('<div/>').html(submenu.link_title.substr(0, 10)+'..').text()}
           </div>
           </Link>
          } 

          </div>)
        })
       }
      </div>)
}
}

export default HomeCategory
