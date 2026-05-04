class FilterOptionPopup extends React.Component {

   constructor(props)
   {
     super(props); 
     this.option_checked       = this.option_checked.bind(this);
     this.active_checked       = this.active_checked.bind(this);
   } 

  componentDidMount()
  {

  }

  decode_html(text)
  {
     var decoded = $('<div/>').html(text).text();
     return decoded;
  }

  active_checked()
  {
       let active_checked;
       let self = this;
       active_checked = $.grep(this.props.search_filter, function( a ) {
                return a == self.props.filter.filter_id;
             });
       if(active_checked == self.props.filter.filter_id) 
      {  $('#more_filter'+self.props.filter.filter_id).prop('checked', true); } 
      else { $('#more_filter'+self.props.filter.filter_id).prop('checked', false); }

  }

  option_checked(newValue)
  {
  
    if(newValue.target.checked)
      {
         $('#more_filter'+newValue.target.value).prop('checked', true);
         $('#filter'+newValue.target.value).prop('checked', true);
         this.props.option_checked_value(newValue.target.value, true);
      }
    else
      {
         $('#more_filter'+newValue.target.value).prop('checked', false);
         $('#filter'+newValue.target.value).prop('checked', false); 
         this.props.option_checked_value(newValue.target.value, false);
      }
  }


  render() {

       let self = this;
       var disable_class='';
       if(self.props.filter.product_count == 0) { var disable_class = 'disable'; }


       setTimeout(this.active_checked, 1500);

    return (<li><div className={"price_filter "+disable_class}>
                           {this.props.filter.name != '' ? 
                            <div className="checkbox_information">
                                <label>
                                     <input type="checkbox" id={'more_filter'+this.props.filter.filter_id}  name="filter[]" value={this.props.filter.filter_id} onChange={this.option_checked} />
                                    {this.decode_html(this.props.filter.name)}
                                    <div className="control__indicator"></div>
                                </label>
                            </div>
                            : ''}
                        </div></li>);
  }
}