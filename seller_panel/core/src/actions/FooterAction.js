import * as actionType from './ActionType';
import FooterApi from '../api/FooterApi';
import custom from '../custom/custom'

export function informationData() { 
  var customer_id = custom.getCookie('customer_id');
  var customer_access_token = custom.getCookie('customer_access_token'); 
  return FooterApi.information_link(customer_id,customer_access_token)
  .then(response => response)
  .then(user_login => user_login.data)
}


export const FooterInformationSuccess = (information) => ({
    type: actionType.FOOTER_INFORMATION_SUCCESS,
    payload: { information },
});