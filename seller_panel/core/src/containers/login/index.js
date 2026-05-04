import React, { Component } from 'react';
import { connect } from 'react-redux'
import Header from '../header'
import Footer from '../footer'
import Home from '../home';
import {  userloginData, HeaderUserloginSuccess } from '../../actions/HeaderAction';


class Login extends Component {
   
    componentWillMount() 
    {
        userloginData()
       .then(this.props.HeaderUserloginSuccess);
    }

    render() {
        return (<section>
         <Header />
          <div className="clearfix"></div>
          <main>
          <Home />
          </main>
          <div className="clearfix"></div>
          <Footer />
          </section>)
    }
}

const mapStateToProps = state => ({ user_login: state.user_login });

export default connect(mapStateToProps, {
    HeaderUserloginSuccess:HeaderUserloginSuccess
})(Login);