import React from 'react'
import Api from '../../api/Api'
import  './no_product.css'

const NoOrders = (props) => (
    <section> 
      <div className="container-fluid width_fix"> 
       <div className="row">
        <div className="col-sm-12">
      <section className="col-sm-12 nopadding privacy_text">
          <div className="wrong_search_box">
            <h3 className="wrong_search_text" style={{textAlign:'center'}}>
            <p><img src={Api.cdn_url+"dekstopwithkurti.png"} alt="error_msg_dont_have_order" width="40%" height="20%" /></p>
            <label><i className="fa fa-info-circle" aria-hidden="true"></i></label> No Matching Orders Found!</h3>
          </div>
          </section>

        </div>
        </div>
       </div> 
    </section>)

export default NoOrders