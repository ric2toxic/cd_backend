import Api from '../api/Api'
import $ from 'jquery'

var custom = {

createCookie: function (name, value, days) {
  var expires;
  if (days) {
    var date = new Date();
    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
    expires = "; expires=" + date.toGMTString();
  }
  else {
    expires = "";
  }
  document.cookie = name + "=" + value + expires + "; path="+Api.folder_path;
},

getCookie: function (c_name) {
  if (document.cookie.length > 0) {
   var c_start = document.cookie.indexOf(c_name + "=");
    if (c_start !== -1) {
      c_start = c_start + c_name.length + 1;
     var c_end = document.cookie.indexOf(";", c_start);
      if (c_end === -1) {
        c_end = document.cookie.length;
      }
      return unescape(document.cookie.substring(c_start, c_end));
    }
  }
  return "";
},

delete_cookie: function (c_name) {
  document.cookie = c_name + '=;expires=Thu, 01 Jan 1970 00:00:01 GMT; path='+Api.folder_path;
},

getUrlParameter: function (sParam) {
    var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
        }
    }
},

downloadImages: function (product_id,model) 
{  

    $.ajax({
      url: Api.api_url+'api/image/download_mobile_image',
      type: 'post',
      data: {product_id:product_id, model:model},
      dataType: 'json',
      complete: function(result) {
        
        var win = [];
        $(result.responseJSON.data).each(function (index, element) {
         
          var image = element.replace(Api.cdn_url+"img/dw=800,dh=1200,q=90/", "");
         
           win[index] = window.open(Api.cdn_url+"dynamic/staging/download_images.php?filename="+image, "MsgWindow"+index, "width=200,height=100");
              
             setTimeout(function() { win[index].close() }, 4000); 
        })
        
        //$('body').removeClass('loading').addClass('loaded'); 
      },
      success: function(json) {
        
      }
    });

}

}



export default custom;