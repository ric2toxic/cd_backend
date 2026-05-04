import React, { Component } from 'react';
import axios from 'axios';

class Careers extends React.Component {

   constructor()
   {
   	 super();
   	this.state = {
      data: []
    };
   } 

	render() {
		return (
			     <div className="row">
            <div className="col-sm-12">
            <div className="row">
           <section className="col-sm-12 contact_sanction privacy_text">
          <div className="col-sm-12 nopadding">
            <div className="col-sm-12 nopadding"><h2 className="page_title">{this.state.data.heading_title}</h2></div>
          </div>
          <div className="clearfix"></div>
           <div dangerouslySetInnerHTML={{ __html: this.state.data.description }} />
          </section>
         <div className="clearfix"></div>
         </div>   
         </div>
        </div>
		);
	}
}

export default Careers;
