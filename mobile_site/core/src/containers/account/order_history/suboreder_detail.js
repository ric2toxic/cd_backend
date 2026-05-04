import React, { Component } from 'react'
import { Link } from 'react-router-dom'
import Api from '../../../api/Api'
import  './index.css'
import $ from 'jquery'
import Anchor from '../../../component/anchor'

class SuborderDetail  extends Component {

 render(){
  var self = this;
    return (
          <div>
          { this.props.suborders ?
                  Object.keys(this.props.suborders).map((suborderdata, suborderindex) =>{
                  return(
                  <div key={suborderindex}>
                    <div className="col-xs-12 nopadding view_more_detail"><b>Suborder No.</b> {this.props.suborders[suborderdata]['suborder_id']} </div>
                    <div className="col-xs-12 nopadding view_more_detail"><b>Amount</b> {$('<div/>').html(this.props.suborders[suborderdata]['total_amt']).text()} </div>
                    <div className="col-xs-12 nopadding view_more_detail"><b>Status</b> <span style={{color:this.props.suborders[suborderdata]['status_color_code']}}> {this.props.suborders[suborderdata]['suborder_status']} </span></div>
                    <div className="clearfix"></div>
                    <div className="view_order_moredetail">
                      <Anchor icon="fa fa-refresh" onClick={self.props.return_popup} title=" Return" /> &nbsp; 
                      <Link to={Api.folder_path+"account/order_detail/"+this.props.product_order_id+"/"+this.props.suborders[suborderdata]['suborder_id']}><i className="fa fa-eye" aria-hidden="true"></i> View Details</Link>
                    </div>
                    <div className="clearfix"></div>
                  </div>
                     )
                  }):''
                }
        </div>
        )
  }
}

export default SuborderDetail;