import * as actionType from './ActionType';
import FooterApi from '../api/FooterApi';
import custom from '../custom/custom'
import $ from 'jquery'

export function sendMailForCallBackRequest(mobile) { 
  var customer_id = custom.getCookie('customer_id');
  var customer_access_token = custom.getCookie('customer_access_token');
  return function(dispatch) {
    return FooterApi.sendMailForCallBackRequest(customer_id, customer_access_token, mobile).then(response => {
      $('body').removeClass('loading').addClass('loaded');
      var success_div = ''
      if(response.statusCode === 200)
      {
            success_div = '<div id="notification"><div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span> <strong>Success</strong><hr class="message-inner-separator"><p>'+response.message+'</p></div></div>';
            $(document.body).append(success_div);   
            $('#notification').fadeOut(2000);
            setTimeout(function() {
                $('#notification').remove();
            }, 2000);
      }
      else
      {
           success_div = '<div id="notification"><div class="alert alert-danger"><span class="glyphicon glyphicon-remove"></span> <hr class="message-inner-separator"><p><strong>Error </strong>something went wrong, please try again.</p></div></div>';
           $(document.body).append(success_div);
           $('#notification').fadeOut(2000); 
           setTimeout(function() {
           $('#notification').remove();
           }, 2000);
      }  

    }).catch(error => {
      throw(error);
    });
  };
}

export function popular_tags() { 
  var customer_id = custom.getCookie('customer_id');
  var customer_access_token = custom.getCookie('customer_access_token');
  return function(dispatch) {
    return FooterApi.popular_tags(customer_id, customer_access_token).then(response => {
        dispatch(FooterPopularTags(response.data.popular_tags));
    }).catch(error => {
      throw(error);
    });
  };
}

export function FooterPopularTags(popular_tags) {  
  return {type: actionType.FOOTER_POPULAR_TAGS, popular_tags};
}