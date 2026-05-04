import React, { Component } from 'react'
import Api from '../../api/Api'
import Cart from "../cart";
import Category from "../category";
import $ from 'jquery'

class Catelog  extends Component {

 render(){
 $("html, body").animate({ scrollTop: 0 }, 800);
 if(this.props.match.params.path.search("index.php") === 0)
 {
    window.location.href = Api.folder_path;

 }
 else if(this.props.match.params.path === 'cart')
 {
    return (<Cart />)

 }
 else if(this.props.match.params.path !== 'p' && this.props.match.params.path !== 'i' && this.props.match.params.path !== 'account')
 { 
   return (<Category match={this.props.match} />)
 }
 else
 {
 	return false
 }  

  }
}


export default Catelog;