import { AUTH_LOGIN, AUTH_LOGOUT, AUTH_ERROR, AUTH_CHECK } from 'react-admin';
import custom from './custom/custom';

export default (type, params) => {
    // called when the user attempts to log in
    if (type === AUTH_LOGIN) {
        const { username } = params;
         custom.createCookie('customer_id',username,7);
        // accept all username/password combinations
        return Promise.resolve();
    }
    // called when the user clicks on the logout button
    if (type === AUTH_LOGOUT) {
        custom.delete_cookie('customer_id');
        custom.delete_cookie('customer_access_token');
        custom.delete_cookie('customer_mobile');
        return Promise.resolve();
    }
    // called when the API returns an error
    if (type === AUTH_ERROR) {
        const { status } = params;
        if (status === 401 || status === 403) {
            custom.delete_cookie('customer_id');
            custom.delete_cookie('customer_access_token');
            custom.delete_cookie('customer_mobile');
            return Promise.reject();
        }
        return Promise.resolve();
    }
    // called when the user navigates to a new location
    if (type === AUTH_CHECK) {
         
         if(custom.getCookie('customer_id') && custom.getCookie('customer_access_token'))
         {
          return Promise.resolve(); 
         }
         else
         {
          return Promise.reject(); 
         }
    }
    return Promise.reject('Unknown method');
};