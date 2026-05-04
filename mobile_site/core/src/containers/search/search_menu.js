import React, { Component } from 'react'
import { Link } from 'react-router-dom'
import Api from '../../api/Api'

class SearchMenu  extends Component {

	render(){
    return (
      <span>
        {this.props.searchList && this.props.searchList.length > 0 ?
          this.props.searchList.map((search, index) =>
            <div className="search_item">
            <Link onClick={() => this.props.clickHandler(this, search.value)} to={Api.folder_path+'category#!filter=&price_filter=&option=&sort=sort_order&order=ASC&rating_filter=&search='+encodeURIComponent(search.value)}>
             {<span dangerouslySetInnerHTML={{ __html: search.label.substr(0, 30)+'...' }} />}
            </Link>

            <span onClick={() => this.props.clickHandler(this, search.value, 1)} className="search_item_icon"><i className="fa fa-mouse-pointer" aria-hidden="true"></i></span>
           </div> 
          ) : ''}
      </span>
    )
	}
}

export default SearchMenu;