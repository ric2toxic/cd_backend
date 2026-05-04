import * as actionType from './ActionType';
import BannerApi from '../api/BannerApi';
import custom from '../custom/custom'

export function bannerData() {  
  return function(dispatch) {
    var preferences = custom.getCookie('preferences');
    return BannerApi.banner(preferences).then(response => {
      dispatch(BannerSuccess(response.data));
    }).catch(error => {
      throw(error); 
    });
  };
}

export function BannerSuccess(banner) {  
  return {type: actionType.BANNER_SUCCESS, banner};
}