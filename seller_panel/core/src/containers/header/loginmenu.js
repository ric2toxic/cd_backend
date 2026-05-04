import React, { Component } from 'react'
import { Link } from 'react-router-dom'
import Api from '../../api/Api'
import  './index.css'


class Loginmenu  extends Component {
 
 render(){

    return (
         <div className="seller_head col-sm-12">
          <div className="col-sm-4 logo">
           <Link to={Api.folder_path}> <img src="http://cdnimages.net/img/logo_seller.png" width="296px" height="60px" alt="wholesalebox" /></Link>
            <div className="seller_hub">Manufacturer Hub</div>
          </div>

           <div className="col-sm-4 gst_title gst_title_text_blink">
            Transfer Price will be inclusive of GST <br />
            <span> ( For any help: 9116134791 | 9649558363 )</span>
          </div> 

          <div className="col-sm-4 float_right login_seller">
            <div className="in_style_reg logout_btn">
              <a onClick={this.props.logout}>LOGOUT</a>
            </div>
            <div className="new_sm">
                <Link className="new_sm" to={Api.folder_path}><span className="fa fa-dashboard"></span>Dashboard</Link>
            </div>
          </div>

         </div>
    )
  }
}


export default Loginmenu;