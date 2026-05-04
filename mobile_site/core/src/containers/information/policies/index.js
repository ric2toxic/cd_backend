import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux';
import Api from '../../../api/Api'
import { Link } from 'react-router-dom'
import Helmet from 'react-helmet';
import AppBar from '@material-ui/core/AppBar';
import Tabs from '@material-ui/core/Tabs';
import Tab from '@material-ui/core/Tab';
import { policyData } from '../../../actions/InformationAction';
import $ from 'jquery'
import  './index.css'

class Policies  extends Component {

    constructor(props)
   {
     super(props);
    this.state = {
      tab_new_value: 0
    };
   } 
   
  componentDidMount()
  {   
     this.props.dispatch(policyData());
  } 

 render(){
  function tab_change(information_id, index)
  { 
    $(".privacy_data").removeClass('active');
    $("#information_"+information_id).addClass('active');
  }

    return (
         <div className="contner head_margin side-collapse-container">
         <Helmet title="Policies" />
            
         <AppBar position="static" className="category_appbar">
          <Tabs
            value={this.state.tab_new_value}
            onChange={(event,tab_new_value) => { this.setState({tab_new_value}) }}
            textColor="primary"
            scrollable
            scrollButtons="auto"
            className="category_tabs"
            classes={{ indicator: 'indicator_class' }}
            >
          {$.map(this.props.policies, function(information_data, index) {
            return(<Tab onClick={()=>tab_change(information_data.information_id, index)} key={index} label={$('<div/>').html(information_data.title).text()} className="category_tab category_tab_All" />)
           })
         }
          </Tabs>
        </AppBar>

           <section className="col-xs-12" style={{marginTop:'10px'}}>
            {$.map(this.props.policies, function(information_data, index) {
              
              return(<div id={"information_"+information_data.information_id} className={"privacy_data "+information_data.class}>
                 <section className="col-xs-12" dangerouslySetInnerHTML={{ __html: information_data.short_description }} />
              <Link className="read_more" to={Api.folder_path+information_data.mobile_href} >View More Information</Link>
              </div>)
             
             })
           }
           </section>

           <div className="clearfix"></div>


         <div className="clearfix"></div>
       
   </div>
)}}


function mapStateToProps(state){
  return {
    policies: state.informationReducer.policies,
    actions: bindActionCreators(policyData)
  };
}
export default connect(mapStateToProps)(Policies);