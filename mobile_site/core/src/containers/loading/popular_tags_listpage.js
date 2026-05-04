import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux';
import { Link } from 'react-router-dom'
import Api from '../../api/Api'
import { popular_tags } from '../../actions/FooterAction'
import $ from 'jquery'

class PopularTagsListpage  extends Component {  

   componentDidMount()
  {
     this.props.dispatch(popular_tags());
  }

generateChildren(childrenitem){
    return  <Link style={{marginLeft: '10px'}} to={Api.folder_path+"category#!filter=&price_filter=&option=&sort=sort_order&order=ASC&rating_filter=&search="+encodeURIComponent(childrenitem.text)}>{$('<div/>').html(childrenitem.text).text()}</Link>
   }


	render() {
		return (
			  <span className="popular_search" style={{wordWrap: 'break-word', textAlign: 'left'}}>
             {this.props.popular_tags ?
                 this.props.popular_tags.map((childrenitem, index) => {
                    return this.generateChildren(childrenitem)
                   })
             : ''}
          </span>
		);
	}
}


function mapStateToProps(state){
  return {
    popular_tags: state.footerReducer.popular_tags,
    actions: bindActionCreators(popular_tags)
  };
}
export default connect(mapStateToProps)(PopularTagsListpage);