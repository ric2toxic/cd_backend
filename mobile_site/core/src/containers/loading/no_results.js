import React from 'react'

const NoResults = (props) => (
    <section> 
      <div className="container-fluid width_fix"> 
       <div className="row">
        <div className="col-sm-12">
      <section className="col-sm-12 nopadding privacy_text"  style={{textAlign: 'center', margin: '30px 0px'}}>

          <div className="no_found_img" style={{margin: '30px 0px'}} >
            <img src="https://cdnimages.net/img/No_Product_Found.png" alt="Product not found" />
          </div>

          <div className="wrong_search_box" style={{textAlign: 'left'}}>
            <h3 className="wrong_search_text"><label><i className="fa fa-info-circle" aria-hidden="true"></i></label> Sorry, no products matched your search!</h3>
            <ul className="wrong_search_box_ul">
            <li>Check your category.</li>
            <li>Use different filters and try again.</li>
            </ul>
          </div>
          </section>

        </div>
        </div>
       </div> 
    </section>)

export default NoResults