class OptionGroup extends React.Component {

   constructor(props)
   {
     super(props);
   } 

   componentDidMount()
  {

  }
  render() {
    let self = this;
    var open_class = ''; var arrow = 'arrow_rotate collapsed';


    if(this.props.groups.name == 'Size')
   {
     return (<div className="filter_box">
              <div className="filter_penal_box" id="list-group-filter">
                  <a className={'list-group_title '+arrow} data-toggle="collapse" href={'#filter-group'+this.props.groups.option_group_id}>{this.props.groups.name}</a>
                  <div className={"filtar_section_content  collapse "+open_class} id={'filter-group'+this.props.groups.option_group_id}>
                    <div className="filter-group">
                      <div className="size_filter">
                          <ul>
                         { 
                           this.props.groups.option.map(function(option, index) {
                             return <OptionGroupSizeValue key={index} option={option} clickHandlerOption={self.props.clickHandlerOption} search_option={self.props.search_option} />
                            })
                         }
                         </ul>
                       </div>  
                    </div>
                  </div>
              </div>
            </div>);
    }
    else
    {
         return (<div className="filter_box">
              <div className="filter_penal_box" id="list-group-filter">
                  <a className={'list-group_title '+arrow} data-toggle="collapse" href={'#filter-group'+this.props.groups.option_group_id}>{this.props.groups.name}</a>
                  <div className={"filtar_section_content  collapse "+open_class} id={'filter-group'+this.props.groups.option_group_id}>
                    <div className="filter-group">
                         { 
                           this.props.groups.option.map(function(option, index) {
                             return <OptionGroupValue key={index} option={option} clickHandlerOption={self.props.clickHandlerOption} search_option={self.props.search_option} />
                            })
                         }
                    </div>
                  </div>
              </div>
            </div>);  
    }             
  }
}