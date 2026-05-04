import React, { Component } from 'react'
import Card, { CardHeader, CardContent } from 'material-ui/Card';
import {  terms_data } from '../../actions/ProfileAction';
import Icon from '@material-ui/icons/AssignmentLate';

export const SellerIcon = Icon;

class SellerPage  extends Component {

    constructor(props)
   {
     super(props);
     this.state = {
     terms_data: false
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

 render(){ 

 return(<Card>
          <CardHeader title="Seller Agreement" />
          <CardContent>
          
           {this.state.terms_data ?
              <section className="col-xs-12 contact_sanction privacy_text" dangerouslySetInnerHTML={{ __html: this.state.terms_data.description }} />
            :''}

           
           </CardContent> 
        </Card>
)
}}


export default SellerPage;