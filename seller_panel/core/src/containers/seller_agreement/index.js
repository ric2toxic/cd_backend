import React, { Component } from 'react'
import Dialog from 'material-ui/Dialog';
import DialogActions from 'material-ui/Dialog/DialogActions';
import DialogContent from 'material-ui/Dialog/DialogContent';
import DialogContentText from 'material-ui/Dialog/DialogContentText';
import DialogTitle from 'material-ui/Dialog/DialogTitle';
import Button from 'material-ui/Button';
import {  terms_data } from '../../actions/ProfileAction';
import custom from '../../custom/custom'


class SellerAgreement  extends Component {

    constructor(props)
   {
     super(props);

     this.dailogAgree = this.dailogAgree.bind(this);
     this.state = {
     terms_data: false,
     dialog_open:this.props.dialog_open
     };
   }
     
    componentWillMount() 
    {   
        var self = this;
        var response = terms_data();
        response.then(function(data){
          self.setState({terms_data:data.data});
         })
        .catch(function() {
          console.log('data fetch error');
         });
        //.then(this.props.TermsDataSuccess);
    }


    componentWillReceiveProps()
    {
     this.setState({dialog_open:this.props.dialog_open});  
    }


   dailogAgree()
   {
     custom.createCookie('seller_agreement_v2_'+custom.getCookie('customer_id'), true, 7);
     this.setState({dialog_open:false});
   }

 render(){ 

    return(<Dialog
                open={this.state.dialog_open}
                aria-labelledby="alert-dialog-title"
                aria-describedby="alert-dialog-description">
                <DialogTitle id="alert-dialog-title">Seller Agreement</DialogTitle>
                 <DialogContent>
                   <DialogContentText id="alert-dialog-description">
                       {this.state.terms_data ?
                        <section className="col-xs-12 contact_sanction privacy_text" dangerouslySetInnerHTML={{ __html: this.state.terms_data.description }} />
                        :'please wait...'}
                   </DialogContentText>
                  </DialogContent>
                  <DialogActions>
                   {this.props.login_page ?
                     <Button onClick={this.dailogAgree} color="primary" autoFocus>Close</Button>
                    :  
                    <Button onClick={this.dailogAgree} color="primary" autoFocus>Agree</Button>
                    }
                  </DialogActions>
               </Dialog>)
}}


export default SellerAgreement;