import React, { Component } from 'react';
import axios from 'axios';

class Information extends React.Component {

   constructor()
   {
   	 super();
   	this.state = {
      data: []
    };
   } 

   componentDidMount()
  {
    var information_id = this.props.params.id;
    axios({
    method:'get',
    url:'http://www.wsb.in/api/information/information/&information_id='+information_id,
    responseType:'json'
   })
   .then(response => {
            this.setState({data: response.data.data})
   });
  }

	render() {
		return (
           <div className="container-fluid width_fix">
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
        </div></div>
		);
	}
}

export default Information;
