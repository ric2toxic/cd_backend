import React, { Component } from 'react';
import axios from 'axios';

class Contactus extends React.Component {

   constructor()
   {
   	 super();
   	this.state = {
      data: []
    };
   } 

	render() {
		return (
      <div className="container-fluid width_fix">
			<div className="row">
      <div className="col-sm-12">
        <div className="row">
         <section className="col-sm-12 contact_sanction">
         <h2 className="page_title">Contact Us</h2>
         <div className="col-sm-8 padding_left">
           <div className="contact_box">
              <form>
                <div className="col-sm-6 contact_input_box"><input id="name" className="password_box" type="text" placeholder="Your Name*" alt="Name" /></div>
                <div className="col-sm-6 contact_input_box"><input id="name" className="password_box" type="E-mail" placeholder="Email Address*" alt="Email" /></div>
                <div className="col-sm-6 contact_input_box has-error"><input id="inputError" className="password_box" type="text" placeholder="Your Phone*" alt="Phone*" /></div>
                <div className="col-sm-6 contact_input_box"><input id="name" className="password_box" type="text" placeholder="Business / Company Name*" alt="Business" /></div>
                <div className="col-sm-12 contact_teaxtarea_box"><textarea id="address" rows="2" className="password_box" placeholder="Enter your Address" alt="Address"></textarea></div>
                <div className="clearfix"></div>
                <button type="button" className="btn deliver_btn pull-right margin_none" data-dismiss="modal" data-direction="right">Submit</button>
              </form>
              <div className="clearfix"></div>
           </div>
         </div>
         <div className="col-sm-4">
            <div className="contact_box">
              <h3 className="page_title">Head Office</h3>
              <div className="col-sm-12 head_office_contact">
                 <div className="col-sm-2 head_office_box"><i className="fa fa-map-marker" aria-hidden="true"></i></div>
               <div className="col-sm-10">  
                 <p>Wholesalebox Pvt. Ltd.<br />
                 B-1, Crystal Mall, Banipark,<br />
                Jaipur-302016 Rajasthan </p>
               </div>     
              </div>
              <div className="col-sm-12 head_office_contact">
                 <div className="col-sm-2 head_office_box"><i className="fa fa-volume-control-phone" aria-hidden="true"></i></div>
               <div className="col-sm-10">  
                 <p>(+91) 141-4049163<br />
                  +91-8696491521</p>
               </div>     
              </div>
              <div className="col-sm-12 head_office_contact">
                 <div className="col-sm-2 head_office_box"><i className="fa fa-envelope" aria-hidden="true"></i></div>
               <div className="col-sm-10">  
                 <p>info@wholesalebox.in</p>
               </div>     
              </div>
              <div className="clearfix"></div>
            </div>
        </div>
        <div className="clearfix"></div>
        <div className="col-sm-12 store_details">
          <ul>
              <li>
                <div className="col-sm-12 store_details_box">
                   <div className="col-sm-1 head_office_box"><i className="fa fa-map-marker" aria-hidden="true"></i></div>
                 <div className="col-sm-10">
                   <h3>Surat</h3> 
                   <p>A 2009/10, Millennium Textile Market,<br />
                   Ring Road, Surat-395002<br />
                  Gujarat </p>
                 </div>     
                </div>
                <div className="col-sm-12 store_details_box">
                   <div className="col-sm-1 head_office_box"><i class="fa fa-volume-control-phone" aria-hidden="true"></i></div>
                 <div className="col-sm-10">  
                   <p>+91-8696491521</p>
                 </div>     
                </div>
                <div className="col-sm-12 store_details_box">
                   <div className="col-sm-1 head_office_box"><i className="fa fa-envelope" aria-hidden="true"></i></div>
                 <div className="col-sm-10">  
                   <p>info@wholesalebox.in</p>
                 </div>     
                </div>
              </li>
              <li>
                <div className="col-sm-12 store_details_box">
                   <div className="col-sm-1 head_office_box"><i className="fa fa-map-marker" aria-hidden="true"></i></div>
                 <div className="col-sm-10">
                   <h3>New Delhi</h3> 
                   <p>F-45, Old Double Storey, Block-F<br />
                   Amar Colony Market, Lajpat Nagar 4,<br />
                  New Delhi-110024</p>
                 </div>     
                </div>
                <div className="col-sm-12 store_details_box">
                   <div className="col-sm-1 head_office_box"><i className="fa fa-volume-control-phone" aria-hidden="true"></i></div>
                 <div className="col-sm-10">  
                   <p>011-4101 0196</p>
                 </div>     
                </div>
                <div className="col-sm-12 store_details_box">
                   <div className="col-sm-1 head_office_box"><i className="fa fa-envelope" aria-hidden="true"></i></div>
                 <div className="col-sm-10">  
                   <p>info@wholesalebox.in</p>
                 </div>     
                </div>
              </li>
              <li>
                <div className="col-sm-12 store_details_box">
                   <div className="col-sm-1 head_office_box"><i className="fa fa-map-marker" aria-hidden="true"></i></div>
                 <div className="col-sm-10">
                   <h3>Bangalore</h3> 
                   <p>Shop No 2118, 1st floor NKS premier <br />
                   Mamulpet, Bangalore-560053 <br />
                  Karnataka </p>
                 </div>     
                </div>
                <div className="col-sm-12 store_details_box">
                   <div className="col-sm-1 head_office_box"><i className="fa fa-volume-control-phone" aria-hidden="true"></i></div>
                 <div className="col-sm-10">  
                   <p>+91-8696491521</p>
                 </div>     
                </div>
                <div className="col-sm-12 store_details_box">
                   <div className="col-sm-1 head_office_box"><i className="fa fa-envelope" aria-hidden="true"></i></div>
                 <div className="col-sm-10">  
                   <p>info@wholesalebox.in</p>
                 </div>     
                </div>
              </li>
          </ul>
        </div>
         </section>
         <div className="clearfix"></div>
        </div>   
      </div>
  </div></div>
		);
	}
}

export default Contactus;
