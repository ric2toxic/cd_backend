import * as actionType from '../actions/ActionType';
import initialState from './initialState';

export default function accountReducer(state = initialState, action) {
  switch(action.type) {
    case actionType.USER_DETAIL_SUCCESS:
      return {
        ...state,
        user_detail: action.user_detail
        }

    case actionType.BANK_DETAIL_SUCCESS:
      return {
        ...state,
        bank_detail: action.bank_detail
        }
    case actionType.USER_ADDRESS_LIST_SUCCESS:
      return {
        ...state,
        address_list: action.address_list.addresses,
        form_detail: action.address_list
        }
    case actionType.USER_ADDRESS_FORM_SUCCESS:
      return {
        ...state,
        new_form_detail: action.new_form_detail
        }
  case actionType.ACCOUNT_SUCCESS_MESSAGE:
      return {
        ...state,
        account_success_message: action
        }
    case actionType.ORDER_DETAIL_SUCCESS:
      return {
        ...state,
        order_details: action.order_detail,
        orders: action.order_detail.orders
        }
    case actionType.ORDER_DETAIL_PAGINATION_SUCCESS:
      return {
        ...state,
        order_details: action.order_detail,
        orders: state.orders.concat(action.order_detail.orders)
        }    
    case actionType.ORDER_DETAIL_INFO_SUCCESS:
      return {
        ...state,
        order_detail_info: action.order_detail_info
        }
    case actionType.ACCOUNT_SUMMARY_SUCCESS:
      return {
        ...state,
        account_summary: action.account_summary,
        payment_methods: action.payment_methods
        }
    case actionType.ACCOUNT_STATEMENT_SUCCESS:
      return {
        ...state,
        account_statement: action.account_statement,
        statement_orders: action.account_statement.data.orders,
        account_summary: action.account_statement.data.customer_summary,
        beyond_order_id: action.account_statement.data.beyond_order_id,
        balance: action.account_statement.data.balance,
        important_note: action.account_statement.important_note,
        payment_methods: action.payment_methods
        }
    case actionType.ACCOUNT_STATEMENT_PAGINATION_SUCCESS:
      return {
        ...state,
        account_statement: action.account_statement,
        statement_orders: state.statement_orders.concat(action.account_statement.orders)
        }
    case actionType.ACCOUNT_STATEMENT_BREAKUP_SUCCESS:
      return {
        ...state,
        account_statement_breakup: action.account_statement_breakup.breakup,
        breakup_debit: action.account_statement_breakup.total_debit,
        breakup_credit: action.account_statement_breakup.total_credit,
        breakup_balance: action.account_statement_breakup.balance,
        breakup_is_positive_bal: action.account_statement_breakup.is_positive_bal
        }
    case actionType.ORDER_PRODUCT_DETAIL_SUCCESS:
      return {
        ...state,
        order_product_detail: action.order_product_detail
        }  
    case actionType.ORDER_SHIPPING_PREFERENCES_SUCCESS:
      return {
        ...state,
        shipping_preferences: action.shipping_preferences
        }             
    case actionType.WISHLIST_PRODUCT_SUCCESS:
      return {
        ...state,
        wishlist_product_detail: action.wishlist_product_detail
        }
    case actionType.RECENT_VIEW_PRODUCT_SUCCESS:
      return {
        ...state,
        recent_view_product_detail: action.recent_view_product_detail
        }

    case actionType.POST_YOUR_REQUIRMENT_CATEGORY_SUCCESS:
      return {
        ...state,
        post_req_category: action.post_req_category
        }
    case actionType.UPDATE_POPUP:
      return {
        ...state,
        UpdatePopup: action.UpdatePopup
        }
    case actionType.ADDRESS_POPUP:
      return {
        ...state,
        AddressPopup: action.AddressPopup
        }
    case actionType.SHIPPING_PREFERENCES_POPUP:
      return {
        ...state,
        ShippingPreferencesPopup: action.ShippingPreferencesPopup
        }            
    default: 
      return state;
  }
}