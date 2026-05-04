class FilterGroup extends React.Component {

   constructor(props)
   {
     super(props);
     this.filter_group_tab       = this.filter_group_tab.bind(this);
   } 

   filter_group_tab()
  {
    if($( "#list-group-filter"+this.props.groups.filter_group_id ).hasClass( "in" ))
    {
      $("#list-group-filter"+this.props.groups.filter_group_id).css('height', 'auto');
    }

  }

  render() {

  let self = this;
  let filter_groups;
  if(self.props.count < 4) { var open_class = 'in'; var arrow = ''; } else { var open_class = ''; var arrow = 'arrow_rotate collapsed'; } 
  setTimeout(this.filter_group_tab, 500);


   filter_groups = Object.keys(self.props.groups.filter).map(function(key) {
       return self.props.groups.filter[key];
      });

   if(this.props.groups.group_label == 'Color')
   {
     return (<div className="filter_box">
              <div className="filter_penal_box" id="list-group-filter">
                  <a className={'list-group_title '+arrow} data-toggle="collapse" href={'#list-group-filter'+this.props.groups.filter_group_id}>{this.props.groups.group_label}</a>
                  <div className={"filtar_section_content  collapse "+open_class} id={'list-group-filter'+this.props.groups.filter_group_id}>
                    <div className="filter-group">
                         <div className="color_filter">
                          <ul>
                         { 
                           filter_groups.map(function(filter, index) {
                             return <FilterColorOption key={index} filter={filter} clickHandler={self.props.clickHandler} search_filter={self.props.search_filter} />
                            })
                          }
                          </ul>
                           <div className="clearfix"></div>
                        </div>
                    </div>
                  </div>
              </div>
            </div>);
    }

    if(this.props.groups.group_label == 'Size')
   {
     return (<div className="filter_box">
              <div className="filter_penal_box" id="list-group-filter">
                  <a className={'list-group_title '+arrow} data-toggle="collapse" href={'#list-group-filter'+this.props.groups.filter_group_id}>{this.props.groups.group_label}</a>
                  <div className={"filtar_section_content  collapse "+open_class} id={'list-group-filter'+this.props.groups.filter_group_id}>
                    <div className="filter-group">
                         <div className="size_filter">
                          <ul>
                         { 
                           filter_groups.map(function(filter, index) {
                             if(filter.product_count > 0)
                             return <FilterSizeOption key={index} filter={filter} clickHandler={self.props.clickHandler} search_filter={self.props.search_filter} />
                             
                            })
                          }
                          </ul>
                           <div className="clearfix"></div>
                        </div>
                    </div>
                  </div>
              </div>
            </div>);
    }
       
     else
    {
      if(filter_groups.length > 7) { var filter_slice_data =  filter_groups.slice(0, 7); var more_data = 1;  }
      else { var filter_slice_data =  filter_groups; }
      
     return (<div className="filter_box">
              <div className="filter_penal_box" id="list-group-filter">
                  <a className={'list-group_title '+arrow} data-toggle="collapse" href={'#list-group-filter'+this.props.groups.filter_group_id}>{this.props.groups.group_label}</a>
                  <div className={"filtar_section_content  collapse "+open_class} id={'list-group-filter'+this.props.groups.filter_group_id}>
                    <div className="filter-group">
                         { 
                           filter_slice_data.map(function(filter, index) {
                             return <FilterOption key={index} filter={filter} clickHandler={self.props.clickHandler} search_filter={self.props.search_filter} />
                            })
                         }
                         {more_data ?
                         <a href={'#more_search_filter'+this.props.groups.filter_group_id} className="more_btn" data-toggle="modal">+ More</a>
                         : '' }
                    </div>
                  </div>
              </div>
            </div>); 
    }        
  }
}