import React from 'react';
import { BrowserRouter as Router, Route } from 'react-router-dom'
import { LastLocationProvider } from 'react-router-last-location'
import TagManager from 'react-gtm-module'
import asyncComponent from "./AsyncComponent";

import Api from '../../api/Api'
import Header from '../header'

import MuiThemeProvider from 'material-ui/styles/MuiThemeProvider'


let gtmId= 'GTM-TRP9H5';
if(window.location.hostname === 'www.wholesalebox.co')
{
  gtmId= 'GTM-MBB945';
}

const tagManagerArgs = {
    gtmId: gtmId
}
 
TagManager.initialize(tagManagerArgs)

const AsyncHome          = asyncComponent(() => import("../home"));
const AsyncInformation   = asyncComponent(() => import("../information/information"));
const AsyncCatelog       = asyncComponent(() => import("../catelog"));
const AsyncProductDetail = asyncComponent(() => import("../product_detail"));
const AsyncAccount       = asyncComponent(() => import("../account"));


const App = () => (
  // <section>
  <MuiThemeProvider>
  <Router>
   <LastLocationProvider>
    <Header />  

    <div className="clearfix"></div>
    <main>

      <Route exact path={Api.folder_path} component={AsyncHome} />
      <Route exact path={Api.folder_path+"i/:information_id"} component={AsyncInformation} />
      <Route exact path={Api.folder_path+":path"} component={AsyncCatelog} />
      <Route exact path={Api.folder_path+":path/:path2"} component={AsyncCatelog} />
      <Route exact path={Api.folder_path+"p/:product_id"} component={AsyncProductDetail} />
      <Route exact path={Api.folder_path+"account/my_account_edit"} component={AsyncAccount} />
      <Route exact path={Api.folder_path+"account/change_password"} component={AsyncAccount} />
      <Route exact path={Api.folder_path+"account/bank_detail"} component={AsyncAccount} />
      <Route exact path={Api.folder_path+"account/address_book"} component={AsyncAccount} />
      <Route exact path={Api.folder_path+"account/order_history"} component={AsyncAccount} />
      <Route exact path={Api.folder_path+"account/order_detail/:order_id/:suborder_id"} component={AsyncAccount} />
      <Route exact path={Api.folder_path+"account/wishlist"} component={AsyncAccount} />
      <Route exact path={Api.folder_path+"account/recent_view"} component={AsyncAccount} />
       <Route exact path={Api.folder_path+"account/statement"} component={AsyncAccount} />
       <Route exact path={Api.folder_path+"account/breakup/:order_id/:order_no"} component={AsyncAccount} />
    </main>
    <div className="clearfix"></div>
     </LastLocationProvider>
    </Router>
    </MuiThemeProvider>
)

export default App
