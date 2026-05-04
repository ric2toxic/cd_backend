import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux';
import GoogleMapReact from 'google-map-react';
import Dialog from '@material-ui/core/Dialog';
import DialogContent from '@material-ui/core/DialogContent';
import Api from '../../../api/Api';
import DialogTitle from '@material-ui/core/DialogTitle';
import withMobileDialog from '@material-ui/core/withMobileDialog';
import { storelocatorData } from '../../../actions/InformationAction';
import $ from 'jquery'
import  './index.css'

const AnyReactComponent = ({ text }) => <div><img className="marker_img" src={Api.cdn_url+"dw=30,dh=30,q=90/wsb-marker.png"} alt="marker" /></div>;

class Storelocator  extends Component {
 
    constructor(props)
  {
     super(props);

     this.state = {
          center: {
           lat: 20.5937,
           lng: 78.9629
           },
           zoom: 5,
           dailog_open:false,
           store_data:{}
         };
     
     this.show_store           = this.show_store.bind(this);
     this.hide_store           = this.hide_store.bind(this);
  }

  componentDidMount()
  {   
     this.props.dispatch(storelocatorData());
  } 

 show_store(store_data)
 {
  this.setState({dailog_open:true});
  this.setState({store_data:store_data});
 }

 hide_store()
 {
  this.setState({dailog_open:false});
 }

 render(){

    return (
         <div className="contner head_margin side-collapse-container">
         <h2 className="about_title">Store Locator</h2>
         { this.props.storelocator ?
         <div className="stores row">
          { 
            this.props.storelocator.store_locators.map((store, index) => {
              return (
                <div key={index} onClick={()=>this.show_store(store)} className={index === 0 ? "col-xs-12" : "col-xs-6"}> <h3 >{store.name}</h3> </div>
                )
            })
         }
         </div>
         :''}

         <div className="clearfix"></div>

         <Dialog
             fullScreen={false}
             open={this.state.dailog_open}
             aria-labelledby="responsive-dialog-title"
             className="login_register_popup"
            >
            <button type="button" className="popup_close close" onClick={this.hide_store}>&times;</button>
            <DialogTitle className="address_popup_head popup-title">
             <h2 style={{margin:'0px'}}>{this.state.store_data.name}</h2>
            </DialogTitle>
       
            <DialogContent>
            <div className="modal-body direction-popup" style={{backgroundImage: 'url('+Api.cdn_url+'dw=140,dh=100,q=90/'+this.state.store_data.image+')'}}>
                  <p><strong>Address :</strong> { $('<div/>').html(this.state.store_data.address).text()} <br/>
                  <strong>Email :</strong> info@wholesalebox.in<br/>
                  <strong>Phone :</strong> {this.state.store_data.telephone} <br/>
                  <strong>Opening Time :</strong> {this.state.store_data.timing} <br/>
                  <strong>Whats App :</strong> (+91) 8696491521 <br/>
                  <a href={this.state.store_data.direction_url} target="_blank" rel="noopener noreferrer">Get Directions</a>
                  </p>
               </div>
      
            </DialogContent>
         </Dialog>


        <div style={{ height: '100vh', width: '100%' }}>
        <GoogleMapReact
          bootstrapURLKeys={{ key: 'AIzaSyCAwVsw0m7M9vHhGzZmzqPWZUiXblA_Uks' }}
          defaultCenter={this.state.center}
          defaultZoom={this.state.zoom}
        >

        {this.props.storelocator ?
          this.props.storelocator.store_locators.map((store, index) => {

            return(<AnyReactComponent
                      lat={store.lat}
                      lng={store.long}
                      text={store}
                      />)

           }) 
         :''}
          

        </GoogleMapReact>
      </div>

         <div className="clearfix"></div>
       
   </div>
)}}



function mapStateToProps(state){
  return {
    storelocator: state.informationReducer.storelocator,
    actions: bindActionCreators(storelocatorData)
  };
}

export default withMobileDialog()(connect(mapStateToProps)(Storelocator));