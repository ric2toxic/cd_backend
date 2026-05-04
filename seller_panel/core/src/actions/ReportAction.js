import ReportApi from '../api/ReportApi';
import custom from '../custom/custom'

export function check_file_exist(file_name) {  
	var customer_id = custom.getCookie('customer_id');
    var customer_access_token = custom.getCookie('customer_access_token'); 
    return ReportApi.check_file_exist(file_name,customer_id,customer_access_token).then(response => {
      if(response.statusCode === 902) { window.location.href = response.message;  }
      return response;
    }).catch(error => {
      throw(error); 
    });
}