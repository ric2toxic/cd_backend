import React, { Component } from 'react'
import Dialog from 'material-ui/Dialog';
import DialogActions from 'material-ui/Dialog/DialogActions';
import DialogContent from 'material-ui/Dialog/DialogContent';
import DialogContentText from 'material-ui/Dialog/DialogContentText';
import DialogTitle from 'material-ui/Dialog/DialogTitle';
import Button from 'material-ui/Button';
import {  saveAgreementConsent } from '../../actions/HeaderAction';
import custom from '../../custom/custom'

import $ from 'jquery'
import  './index.css'


class AgreementConsent  extends Component {

    constructor(props)
   {
     super(props);
      this.dailogSubmit = this.dailogSubmit.bind(this);
     this.state = {
     dialog_open:this.props.dialog_open,
     SELLER_AGREEMENT_POPUP:this.props.SELLER_AGREEMENT_POPUP
     };
   }

   dailogSubmit()
   {
     var declaration = $("input[name=declaration]:checked").val();
     saveAgreementConsent(declaration);
     this.setState({dialog_open:false});
     if(this.state.SELLER_AGREEMENT_POPUP && !custom.getCookie('seller_agreement_'+custom.getCookie('customer_id')))
     {
         window.location.reload();
     }
   }
    
 render(){ 

    return(<Dialog
                open={this.state.dialog_open}
                aria-labelledby="alert-dialog-title"
                aria-describedby="alert-dialog-description">
                <DialogTitle id="alert-dialog-title">MSME Consent</DialogTitle>
                 <DialogContent>
                   <DialogContentText>
                       <p><b>Criteria for classification of micro, small and medium enterprises</b></p>
                       <p>Manufacturer: on the basis of investment in plant & Machinery</p>
                        <table className="agreement_form">
                        <tr style={{backgroundColor:'#daedef'}}>
                        <th><input type="radio" name="declaration" value="Micro" checked="checked" /> Micro</th>
                        <th><input type="radio" name="declaration" value="Small" /> Small</th>
                        <th><input type="radio" name="declaration" value="Medium" /> Medium</th>
                        <th><input type="radio" name="declaration" value="Not Covered under MSME" /> Not Covered under MSME</th></tr>
                        <tr>
                          <td>Less then 25 Lacs</td>
                          <td>Between 25 lacs and 5 crores</td>
                          <td>Between 5 crores and 10 crores</td>
                          <td></td>
                        </tr>
                        </table>
                   </DialogContentText>
                  </DialogContent>
                     <Button onClick={this.dailogSubmit} className="submit_btn" color="primary" autoFocus>Submit</Button>
                  <DialogActions>
                  </DialogActions>
               </Dialog>)
}}


export default AgreementConsent;