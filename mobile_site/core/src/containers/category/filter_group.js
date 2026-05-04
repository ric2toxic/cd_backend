  import React, { Component } from 'react'

  import Filter from './filter'

class FilterGroup extends Component {

 render(){

 let self = this;
 return (<div id={"filter_group_"+this.props.filter.filter_group_id} data-group_id={this.props.filter.filter_group_id} className="filter_group tab-pane fade">
                    <ul className="more_filters_right_tab">
                     {this.props.filter ?
                      this.props.filter.filter.map((value, index) => { 
                      return (<Filter search_filter={self.props.search_filter} clickHandler={self.props.clickHandler} filter={value} key={index} count={index} />)
                      })
                     : ''
                     }
                    </ul>
                  </div>)

}

}
export default FilterGroup