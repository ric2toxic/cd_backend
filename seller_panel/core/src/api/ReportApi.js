import Api from './Api'

class ReportApi { 

   static check_file_exist(file_name,customer_id,customer_access_token) {
   	var encode = window.btoa(`download_file=${file_name}&customer_id=${customer_id}&customer_access_token=${customer_access_token}`);
    return fetch(`${Api.api_url}api/sellers/report/check_file_exist&data=${encode}`).then(response => {
      return response.json();
    }).catch(error => {
      return error;
    });
  } 

}
export default ReportApi;  