import * as actionType from './ActionType';
import custom from '../custom/custom'
import MenuApi from '../api/MenuApi';

export function customstoreData(change_store='') {  
  if(change_store === '')
    {
      return function(dispatch) {
       return MenuApi.custom_store(change_store).then(response => {
       custom.createCookie('custom_store', response.data.custom_store, 7);
       custom.createCookie('store_switch_label', response.data.store_switch_label, 7);
       dispatch(MenuCustomStoreSuccess(response.data));
      }).catch(error => {
      throw(error); 
      });
     };
   }
   else
   {
    return function(dispatch) {
       return MenuApi.custom_store(change_store).then(response => {
       custom.createCookie('custom_store', response.data.custom_store, 7);
       custom.createCookie('store_switch_label', response.data.store_switch_label, 7);
       dispatch(MenuCustomStoreSuccess(response.data));
      }).catch(error => {
      throw(error); 
      });
     };
   }
}

export function menuData() {  
  return function(dispatch) {
    return MenuApi.menu().then(response => {
      dispatch(MenuSuccess(response.data));
    }).catch(error => {
      throw(error);
    });
  };
}

export function informationData() {  
  return function(dispatch) {
    return MenuApi.information().then(response => {
      dispatch(MenuInformationSuccess(response.data));
    }).catch(error => {
      throw(error);
    });
  };
}


export function MenuCustomStoreSuccess(custom_store) {  
  return {type: actionType.MENUS_CUSTOMSTORE_SUCCESS, custom_store};
}

export function MenuSuccess(menus) {  
  return {type: actionType.MENUS_SUCCESS, menus};
}

export function MenuInformationSuccess(information) {  
  return {type: actionType.MENUS_INFORMATION_SUCCESS, information};
}