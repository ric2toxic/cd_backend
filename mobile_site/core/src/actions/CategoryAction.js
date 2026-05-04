import * as actionType from './ActionType';
import Api from '../api/Api'
import CategoryApi from '../api/CategoryApi';
import webengage from '../custom/webengage';
import $ from 'jquery'

export function getFilterData(search_filter, filter_data, last_filter_action) {  
  return function(dispatch) {
    return CategoryApi.getFilterData(filter_data).then(response => {
     
      dispatch(CategoryFilterSuccess(response.data));
      $(".filters").prop('checked', false);
      var active_filter = 0;
      
      search_filter.map((value, index) => {
        $('#filter'+value).prop('checked', true); 
        active_filter = value;
        return true;
      });
      
      if(last_filter_action === 'filter' && active_filter > 0)
      {
        $(".filters_tab_section ul li").removeClass("active");
        $(".filters_tab_section .tab-content").children("div").removeClass("in").removeClass("active"); 
        var group_id = $('#filter'+active_filter).parents(".filter_group").attr('data-group_id');
        $('#filter_group_'+group_id).addClass("in").addClass("active"); 
        $('#filter_group_list'+group_id).addClass("active"); 
      }

      $('body').removeClass('loading').addClass('loaded');

    }).catch(error => {
      dispatch(CategoryProductError(error));
      $('body').removeClass('loading').addClass('loaded');
    });
  };
}

export function product_listData(search_filter, filter_data) {  
  return function(dispatch) {
    return CategoryApi.product_list(filter_data).then(response => {
      dispatch(CategoryProductSuccess(response.data));

      var product_id = filter_data[1].split("&");
      if(response.data.path_type === 'product_id' || response.data.path_type === 'product')
      {
        filter_data = filter_data.split("path=");
        window.location.href = Api.folder_path+'p/'+product_id[0];
      }

      if(response.data.path_type === 'information_id' )
      {
        filter_data = filter_data.split("path=");
        window.location.href = Api.folder_path+'i/'+product_id[0];
      }

      if(response.data.static_redirect.redirect_type && response.data.static_redirect.redirect_type === '404')
      {
        window.location.href = Api.folder_path;
      }

      if(response.data.static_redirect.redirect_type && response.data.static_redirect.redirect_type === '301')
      {
        window.location.href = Api.folder_path+response.data.static_redirect.redirect_url;
      }
      
      if(response.data.search !== '')
      {
         webengage.product_search(response.data);  
      }
      else if(response.data.parent_category_info && response.data.parent_category_info.category_id > 0)
      {
         webengage.sub_category_view(response.data);
      }
      else
      {
         webengage.category_view(response.data);
      }


      $('body').removeClass('loading').addClass('loaded');
       if(response.data.products.length === 0)
       {
          $(".btn_browse_more").removeClass("page_auto_load");
          $(".btn_browse_more").addClass("last_page");
       }
       else
       {
          $(".btn_browse_more").addClass("page_auto_load");
          $(".btn_browse_more").removeClass("last_page");
       }

    }).catch(error => {
      dispatch(CategoryProductError(error));
      $('body').removeClass('loading').addClass('loaded');
    });
  };
}

export function product_pagination_listData(filter_data) { 
   $(".category_loading").slideDown(500); 
  return function(dispatch) {
    return CategoryApi.product_list(filter_data).then(response => {
      dispatch(CategoryProductPaginationSuccess(response.data));
       $(".category_loading").slideUp(500);
      $('body').removeClass('loading').addClass('loaded');
       if(response.data.products.length === 0)
       {
          $(".btn_browse_more").removeClass("page_auto_load");
          $(".btn_browse_more").addClass("last_page");
       }
       else
       {
          $(".btn_browse_more").addClass("page_auto_load");
          $(".btn_browse_more").removeClass("last_page");
       }
    }).catch(error => {
      throw(error); 
    });
  };
}



export function CategoryFilterSuccess(filter_list) {  
  return {type: actionType.CATEGORY_FILTER_SUCCESS, filter_list};
}

export function CategoryProductSuccess(product_list) {  
  return {type: actionType.CATEGORY_PRODUCT_SUCCESS, product_list};
}

export function CategoryProductPaginationSuccess(product_list) {  
  return {type: actionType.CATEGORY_PRODUCT_PAGINATION_SUCCESS, product_list};
}

export function CategoryFilterPathSuccess(filter_string) {  
  return {type: actionType.CATEGORY_FILTER_PATH_SUCCESS, filter_string};
}

export function CategoryPathKeywordSuccess(path_keyword) {  
  return {type: actionType.CATEGORY_PATH_KEYWORD_SUCCESS, path_keyword};
}

export function CategoryProductError(error) {  
  return {type: actionType.CATEGORY_PRODUCT_ERROR, error};
}