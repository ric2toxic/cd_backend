class FilterMorePopup extends React.Component {

   constructor(props)
   {
     super(props);
     this.searchByKeyword        = this.searchByKeyword.bind(this);
     this.option_checked_value   = this.option_checked_value.bind(this);
     this.close_popup            = this.close_popup.bind(this);
     this.apply_filter           = this.apply_filter.bind(this);
      this.state = {
      serach_term: "",
      popup_filter:[]
      };

   } 

   searchByKeyword(newValue) { 
      this.setState({serach_term: newValue.target.value});
    } 

   componentDidMount()
   {

   }

  option_checked_value(value, checked)
  {
     var popup_filter = this.state.popup_filter;
      if(checked)
      {
        popup_filter.push(value);
      }
      else
      {
       popup_filter = $.grep(popup_filter, function( a ) {
             return a !== value;
             });
      } 
      this.state.popup_filter = popup_filter;  
  }

  close_popup()
  {
     this.state.popup_filter.map(function(filter, index) {
     $('#more_filter'+filter).prop('checked', false);
     $('#filter'+filter).prop('checked', false);  
     });

     this.props.search_filter.map(function(filter, index) {
     $('#more_filter'+filter).prop('checked', true);
     $('#filter'+filter).prop('checked', true);  
     });

     $(".filter_more_serch").val('');
     this.setState({serach_term: ''});
     this.state.popup_filter = [];
     $(".more_search_filter").modal("hide");
  }

  apply_filter(e)
  {
    $(".filter_more_serch").val('');
    this.setState({serach_term: ''});
    this.props.clickHandlerMultiFilter(this.state.popup_filter, e);
    this.state.popup_filter = [];
  }

  render() {
    let self = this;
    let term = this.state.serach_term;
    let x;
 
    function searchingFor(term){
      return function(x){
        return x.name.toLowerCase().includes(term.toLowerCase()) || !term;
      }
    }


  let filter_groups;

   filter_groups = Object.keys(self.props.groups.filter).map(function(key) {
       return self.props.groups.filter[key];
      });


    return (<div className="modal fade more_search_filter" id={'more_search_filter'+this.props.groups.filter_group_id} data-backdrop="static" role="dialog">
            <div className="modal-dialog filter_poppup_width">
            <div className="modal-content">
             <div className="modal-header">
              <div className="filter_Search_box">
              <button type="button" className="close filter_popup_close" onClick={this.close_popup}>&times;</button>
                <input className="filter_more_serch" type="text" placeholder={'Search '+this.props.groups.group_label} alt="Fabric" onChange={this.searchByKeyword.bind(this)} />
              </div>
            </div>
            <div className="modal-body filter_more_box">
              <div className="clearfix"></div>
              <div className="filter_search_content">
               <ul>
               { 
                  filter_groups.filter(searchingFor(term)).map(function(filter, index) {
                    return <FilterOptionPopup key={index} filter={filter}  search_filter={self.props.search_filter} option_checked_value={self.option_checked_value} />
                  })
               }
              </ul>

               <button className="btn deliver_btn pull-right apply_btn more_margin" type="button" onClick={() => self.apply_filter(this) }>Apply</button>
              </div>
              </div>
            </div>
            </div>
            </div>
            );
  }
}