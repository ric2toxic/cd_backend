import React, { Component } from 'react'
import SearchMenu from './search_menu'
import Dialog from '@material-ui/core/Dialog'
import Paper from 'material-ui/Paper'
import  './index.css'

class Search  extends Component {


	render(){
		
    return (
        <Dialog
             fullScreen={true}
             open={this.props.searchOpen}
             aria-labelledby="responsive-dialog-title"
             className="search_drawer"
            >
    	<div className="search_bar search_bar_drawer">
         <div className="search_full_box"> 
          <div className="nav_singles_store_btn nav_singles_store_btn_drawer"><span onClick={() => this.props.clickHandler(this)}> <i className="fa fa-long-arrow-left" aria-hidden="true"></i></span></div>
           <input type="text" id="search_input_box_drawer" placeholder="Search from more than 1 Lac Products..." onKeyUp={() => this.props.searchHandler(this)} />
          <div className="nav_singles_store_cross_btn"><span onClick={() => this.props.clickHandler(this, '', 1)}> &times; </span></div>
         </div>
         <Paper className="search_menu_list" style={{boxShadow : "none",fontFamily: "Roboto, sans-serif"}}>
        	<SearchMenu clickHandler={this.props.clickHandler} searchList = {this.props.searchList}/>
    	</Paper>
        </div>
    </Dialog>
    )
	}
}


export default Search;