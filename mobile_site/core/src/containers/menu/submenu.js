import React, { Component } from 'react'
import $ from 'jquery'
import  './index.css'
import Api from '../../api/Api'
import { Link } from 'react-router-dom'

class Submenu  extends Component {

 render(){

  var href = this.props.item.href;
    return (
           <li>
           <div className="submenu_title">
           {this.props.item.children ?
           <span className="sub_menu_link" >{$('<div/>').html(this.props.item.link_title).text()}</span>
           :
           <Link className="sub_link" to={Api.folder_path+href}>{$('<div/>').html(this.props.item.link_title).text()}</Link>
           }
            { this.props.item.children ?
             <i className="fa fa-angle-down sub_menu_icon" aria-hidden="true"></i>
             : ''
              }
           </div>
             { this.props.item.children ? 
               <ul className="lastmenu_list">
                {
                  this.props.item.children.map((lastitem, index) => {
                   return (
                           <li key={index}>
                             <Link key={index} className="sub_link" to={Api.folder_path+lastitem.href}>{$('<div/>').html(lastitem.link_title).text()}</Link>
                           </li>
                          ); 
                  })

                }  
               </ul>
               :''
             }
           </li>
           )
  }
}

export default Submenu;
