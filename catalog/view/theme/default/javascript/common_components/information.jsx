class Information extends React.Component {


  generatelinks (item, index) { 
    return <li key={index}>
         <a href={item.href} id={'footer-'+item.name}>{ $('<div/>').html(item.title).text() } </a>
        </li>
  }

	render() {


function logout(self)
{
   dataLayer.push({'event': 'we-custom-logout'});
   setTimeout(function(){ window.location.assign(self.props.static_data.logout); }, 500);
}


		return (
			  <div className="col-sm-3 information_section">
            <h3>About Us</h3>
            <ul>
            <li> <a href="./i/about-us" id="footer-aboutus">About Us</a></li>
            <li><a href="./i/contact-us" id="footer-contactus">Contact Us</a></li>
            <li><a href="i/policies" id="footer-policies">Policies</a></li>
             { /*this.props.informations.map(this.generatelinks)*/ }
             <li><a href="./i/storelocator" id="footer-store-locator">Storelocator</a></li>

              {
                this.props.static_data.logged ?
                 <li><a href="javascript:;" onClick={()=>logout(this)}>Logout</a></li>
                : 
               <li><a id="footer-login" href="javascript:;" data-toggle="modal" data-target="#login_verify_popup"><span className="hidden-xs hidden-sm hidden-md">Sign in / Sign up</span></a></li>
              }
              
               { !this.props.static_data.logged ?
                <li><a href="./i/dropshipper" id="footer-dropshipper">Dropshipper</a></li>
                : ''
               }
            </ul>
          </div>
		);
	}
}
