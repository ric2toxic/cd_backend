import * as actionType from './ActionType';
import HomeApi from '../api/HomeApi';
import custom from '../custom/custom';

export function categoryData() {  
  return function(dispatch) {
    return HomeApi.category_list().then(response => {
      dispatch(HomeCategorySuccess(response.data));
    }).catch(error => {
      throw(error); 
    });
  };
}

export function latestData() {  

  var customer_id = custom.getCookie('customer_id');
  var customer_access_token = custom.getCookie('customer_access_token');
  var custom_store = custom.getCookie('custom_store');
  var preferences = custom.getCookie('preferences');

  return function(dispatch) {
    return HomeApi.latest_product(customer_id, customer_access_token, custom_store, preferences).then(response => {
      dispatch(HomeLatestSuccess(response.data));
    }).catch(error => {
      throw(error); 
    });
  };
}

export function trendingData() {  
  
  var customer_id = custom.getCookie('customer_id');
  var customer_access_token = custom.getCookie('customer_access_token');
  var custom_store = custom.getCookie('custom_store');
  var preferences = custom.getCookie('preferences');

  return function(dispatch) {
    return HomeApi.trending_product(customer_id, customer_access_token, custom_store, preferences).then(response => {
      dispatch(HomeTrendingSuccess(response.data));
    }).catch(error => {
      throw(error); 
    });
  };
}


export function HomeCategorySuccess(category_list) {  
  return {type: actionType.HOME_CATEGORY_SUCCESS, category_list};
}

export function HomeLatestSuccess(latest_product) {  
  return {type: actionType.HOME_LATEST_SUCCESS, latest_product};
}
export function HomeTrendingSuccess(trending_product) {  
  return {type: actionType.HOME_TRENDING_SUCCESS, trending_product};
}