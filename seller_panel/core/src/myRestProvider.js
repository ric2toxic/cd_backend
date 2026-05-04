// in myRestProvider.js
import { stringify } from 'query-string';
import custom from './custom/custom'
import {
    GET_LIST,
    GET_ONE,
    CREATE,
    UPDATE,
    DELETE,
    GET_MANY,
    GET_MANY_REFERENCE,
} from 'react-admin';
import $ from 'jquery';

  const apiUrl = 'https://www.wholesalebox.in/api/sellers/';
  //const apiUrl = 'http://www.wsb.in/api/sellers';

/**
 * Maps react-admin queries to my REST API
 *
 * @param {string} type Request type, e.g GET_LIST
 * @param {string} resource Resource name, e.g. "posts"
 * @param {Object} payload Request parameters. Depends on the request type
 * @returns {Promise} the Promise for a data response
 */
export default (type, resource, params) => {

    let url = '';
    const options = { 
        headers : new Headers({
            Accept: 'application/json',
        }),
    };

var customer_id = custom.getCookie('customer_id');
var customer_access_token = custom.getCookie('customer_access_token');
var encode = '';

$(".list-page").find("table").addClass("light_box");
$(".load").remove();
$(".list-page, .show-page").append('<div class="load"></div>');

    switch (type) {
        case GET_LIST: {
            const { page, perPage } = params.pagination;
            const { field, order } = params.sort;

            const query = {
                sort: field,
                order: order,
                start: (page - 1) * perPage,
                limit: perPage,
                customer_id:customer_id,
                customer_access_token:customer_access_token
            };

            encode = window.btoa(`${stringify(query)}&${stringify(params.filter)}`);
            url = `${apiUrl}/${resource}?data=${encode}`;
            break;
        }
        case GET_ONE:
            const query = {
                id: params.id,
                customer_id:customer_id,
                customer_access_token:customer_access_token
            };
            encode = window.btoa(`${stringify(query)}`);
            url = `${apiUrl}/${resource}?data=${encode}`;
            break;
        case CREATE:
            url = `${apiUrl}/${resource}`;
            options.method = 'POST';
            options.body = JSON.stringify(params.data);
            break;
        case UPDATE:
            url = `${apiUrl}/${resource}/${params.id}`;
            options.method = 'PUT';
            options.body = JSON.stringify(params.data);
            break;
        case DELETE:
            url = `${apiUrl}/${resource}/${params.id}`;
            options.method = 'DELETE';
            break;
        case GET_MANY: {
            const query = {
                filter: JSON.stringify({ id: params.ids }),
            };
            encode = window.btoa(`${stringify(query)}`);
            url = `${apiUrl}/${resource}?data=${encode}`;
            break;
        }
        case GET_MANY_REFERENCE: {
            const { page, perPage } = params.pagination;
            const { field, order } = params.sort;
            const query = {
                sort: field,
                order: order,
                start: (page - 1) * perPage,
                limit: perPage,
                customer_id:customer_id,
                customer_access_token:customer_access_token,
                id:params.id
            };
            encode = window.btoa(`${stringify(query)}&${stringify(params.filter)}`);
            url = `${apiUrl}/${resource}?data=${encode}`;
            break;
        }
        default:
            throw new Error(`Unsupported Data Provider request type ${type}`);
    }




    /*return fetch(url, options)
        .then(res => res.json())
        .then(response =>
            console.log(response)
        );*/
        return fetch(url, options)
        .then(res => {
            return res.json();
        })
        .then(json => {
            switch (type) {
                case GET_LIST:
                   $(".list-page").find("table").removeClass("light_box");
                   $(".load").remove(); 
                    if (json.statusCode === null) {
                        throw new Error(
                            'The Content-Range header is missing in the HTTP Response. The simple REST data provider expects responses for lists of resources to contain this header with the total number of results to build the pagination. If you are using CORS, did you declare Content-Range in the Access-Control-Expose-Headers header?'
                        );
                     }
                    if(json.statusCode === 900) 
                        { 
                            window.location.href = './#/login'; 
                        }
                    if(json.statusCode === 901) 
                        { 
                            window.location.href = './#/profile'; 
                            throw new Error(
                             json.message
                            );
                        }
                    if(json.statusCode === 902) 
                        { 
                            window.location.href = json.message; 
                            throw new Error(
                             json.message
                            );
                        }        
                    return {
                        data: json.data,
                        total: parseInt(json.totalRecord,
                            10
                        ),
                    };
                case GET_MANY_REFERENCE:
                   $(".list-page").find("table").removeClass("light_box");
                   $(".load").remove(); 

                    if (json.statusCode === null) {
                        throw new Error(
                            'The Content-Range header is missing in the HTTP Response. The simple REST data provider expects responses for lists of resources to contain this header with the total number of results to build the pagination. If you are using CORS, did you declare Content-Range in the Access-Control-Expose-Headers header?'
                        );
                     }
                    if(json.statusCode === 900) 
                        { 
                            window.location.href = './#/login'; 
                        }
                    if(json.statusCode === 901) 
                        { 
                            window.location.href = './#/profile'; 
                            throw new Error(
                             json.message
                            );
                        }
                    if(json.statusCode === 902) 
                        { 
                            window.location.href = json.message; 
                            throw new Error(
                             json.message
                            );
                        }      

                    return {
                        data: json.data,
                        total: parseInt(json.totalRecord,
                            10
                        ),
                    };
                case CREATE:
                    return { data: { ...params.data, id: json.id } };
                case GET_ONE:
                    $(".load").remove();
                    
                    if (json.statusCode === null) {
                        throw new Error(
                            'The Content-Range header is missing in the HTTP Response. The simple REST data provider expects responses for lists of resources to contain this header with the total number of results to build the pagination. If you are using CORS, did you declare Content-Range in the Access-Control-Expose-Headers header?'
                        );
                     }
                    if(json.statusCode === 900) 
                        { 
                            window.location.href = './#/login'; 
                        }
                    if(json.statusCode === 901) 
                        { 
                            window.location.href = './#/profile'; 
                            throw new Error(
                             json.message
                            );
                        }
                    if(json.statusCode === 902) 
                        { 
                            window.location.href = json.message; 
                            throw new Error(
                             json.message
                            );
                        }    

                    return {
                        data: json.data[0],
                    };
                default:
                    return { data: json };
            }
        });


};