import React, { Component } from 'react';
import { connect } from 'react-redux'
import {TabbedShowLayout, Tab} from 'react-admin';
import Card, { CardHeader, CardContent } from 'material-ui/Card';
import Icon from '@material-ui/icons/Person';

import {  userData, ProfileUserDataSuccess } from '../../actions/ProfileAction';

import Bassic from './bassic';
import Address from './address';
import PickupCoordinator from './pickup_coordinator';
import PickupAddress  from './pickup_address';
import AccountsCoordinator from './accounts_coordinator';
import InventoryListingCoordinator from './inventory_listing_coordinator'

import BusinessInformation from './business_information'
import BankAccountDetails from './bank_account_details'
import Password from './password'

import custom from '../../custom/custom'

export const ProfileIcon = Icon;

 class profile  extends Component {

    componentWillMount() 
    {
        userData()
       .then(this.props.ProfileUserDataSuccess);
    }

 render(){ 

const { profile_data } = this.props;
if(profile_data && profile_data.error)
{
   custom.delete_cookie('customer_id');
   custom.delete_cookie('customer_mobile');
   custom.delete_cookie('customer_access_token');
   window.location.href = './#/login';
   return(<h1>Logout</h1>);
}
else
{   
return(
	  <Card>
	  <CardHeader title="Profile" />
	  <CardContent>
     {profile_data ?
      <TabbedShowLayout>
        <Tab label="Basic">
          <br />
          <Bassic profile_data={profile_data} />
          <br />
          <Address profile_data={profile_data} />
           <br />
          <PickupCoordinator profile_data={profile_data} />
           <br />
           <PickupAddress profile_data={profile_data} />
           <br />
          <AccountsCoordinator profile_data={profile_data} />
           <br />
          <InventoryListingCoordinator profile_data={profile_data} />  
        </Tab>
         <Tab label="Business">
          <br />
          <BusinessInformation profile_data={profile_data} />
          <br />
          <BankAccountDetails profile_data={profile_data} />
        </Tab>
        <Tab label="Change Password">
          <br />
          <Password />
        </Tab>
       </TabbedShowLayout> 
       : 'Please wait...'}
       </CardContent>
      </Card> 
     )
   }
}}

const mapStateToProps = state => ({ profile_data: state.rootReducers.profileReducer.data });

export default connect(mapStateToProps, {
    ProfileUserDataSuccess:ProfileUserDataSuccess
})(profile);