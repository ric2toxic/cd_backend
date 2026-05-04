import React from 'react'

const NoResults = (props) => (
    <section> 
      <div className="container-fluid width_fix"> 
       <div className="row">
        <div className="col-sm-12">
      <section className="col-sm-12 privacy_text">
          <div className="wrong_search_box">
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