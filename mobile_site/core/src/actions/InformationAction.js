import * as actionType from './ActionType';
import InformationApi from '../api/InformationApi';

export function aboutusData() {  
  return function(dispatch) {
    return InformationApi.about_us().then(response => {
      dispatch(InformationAboutusSuccess(response.data));
    }).catch(error => {
      throw(error); 
    });
  };
}

export function policyData() {  
  return function(dispatch) {
    return InformationApi.policies().then(response => {
      dispatch(InformationPolicySuccess(response.data));
    }).catch(error => {
      throw(error); 
    });
  };
}


export function storelocatorData() {  
  return function(dispatch) {
    return InformationApi.storelocator().then(response => {
      dispatch(InformationStorelocatorSuccess(response.data));
    }).catch(error => {
      throw(error); 
    });
  };
}


export function contactusData() {  
  return function(dispatch) {
    return InformationApi.contactus().then(response => {
      dispatch(InformationContactusSuccess(response.data));
    }).catch(error => {
      throw(error); 
    });
  };
}

export function contactus_formData(form_data) {  
  return function(dispatch) {
    return InformationApi.contactus_form(form_data).then(response => {
      return response.data;
    }).catch(error => {
      throw(error); 
    });
  };
}


export function informationsData(information_id) {  
  return function(dispatch) {
    return InformationApi.information(information_id).then(response => {
      dispatch(InformationSuccess(response.data));
    }).catch(error => {
      throw(error); 
    });
  };
}

export function sellerRegisterData(form_data) 
{  
  return function(dispatch) {
    return InformationApi.seller_register(form_data).then(response => {
      return response.data;
    }).catch(error => {
      throw(error); 
    });
  };
}

export function sellerAgreementData() 
{  
  return function(dispatch) {
    return InformationApi.seller_agreement().then(response => {
      dispatch(InformationSellerAgreementSuccess(response.data));
    }).catch(error => {
      throw(error); 
    });
  };
}



export function InformationSuccess(information_data) {  
  return {type: actionType.INFORMATION_SUCCESS, information_data};
}

export function InformationStorelocatorSuccess(storelocator) {  
  return {type: actionType.STORELOCATOR_SUCCESS, storelocator};
}

export function InformationAboutusSuccess(about_us) {  
  return {type: actionType.INFORMATION_ABOUT_SUCCESS, about_us};
}

export function InformationPolicySuccess(policies) {  
  return {type: actionType.INFORMATION_POLICY_SUCCESS, policies};
}

export function InformationContactusSuccess(contact_us) {  
  return {type: actionType.INFORMATION_CONTACT_SUCCESS, contact_us};
}

export function InformationSellerAgreementSuccess(seller_agreement_data) {  
  return {type: actionType.SELLER_AGREEMENT_DATA, seller_agreement_data};
}


export function DialogOpen(dialog_open) {  
  return {type: actionType.DIALOG_OPEN, dialog_open};
}
