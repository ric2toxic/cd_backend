
class CurrencyList extends React.Component {

    constructor()
    {
   	super();
   	this.state = {
            currency: '',
            currencyList: [],
            country_code_to_currency_title: {},
            country_code_to_currency_code:{}
        };
        this.handleCurrency = this.handleCurrency.bind(this);
    } 

    componentWillMount()                                                                                                                                                
    {
        
    }
    
    componentDidMount() {
      axios({
          method:'get',
          url:'./api/header/currencyList',
          responseType:'json'
      })
      .then(response => {
          this.setState({currency: response.data.data.currency});
          this.setState({
            currencyList: response.data.data.currency_list,
            country_code_to_currency_title: response.data.data.country_code_to_currency_title,
            country_code_to_currency_code: response.data.data.country_code_to_currency_code
          });
          var self = this;
          $('#currency_list').attr('data-selected-country',response.data.data.currency_list[response.data.data.currency].country_code);
          $('#currency_label').html('Change Currency: ');
          $('#currency_list').flagStrap({
            countries: response.data.data.country_code_to_currency_title,
            inputName: 'country',
            buttonSize: "btn-xs",
            buttonType: "btn-primary",
            labelMargin: "8px",
            scrollable: false,
            onSelect: function(value, element) {
              console.log(value);
              var currency = self.state.country_code_to_currency_code[value];
              axios({
                  method:'get',
                  url:'./api/header/setCurrency?currency='+currency,
                  responseType:'json'
              })
             .then(response => { 
                 location.reload();
              });
            }
          });
          $('.flagstrap').click(function(e){
            $('.caret').toggleClass('open-caret');
          });
      });  
    }
  
    
    handleCurrency(event) {
        var currency = event.target.value;
        axios({
            method:'get',
            url:'./api/header/setCurrency?currency='+currency,
            responseType:'json'
        })
       .then(response => { 
           location.reload();
        });
    } 
    
    
    render() {
        
        return (
            <div className="currency_div">
              <span id = "currency_label"></span>
              <div 
                className="flagstrap currency_list" 
                id="currency_list" 
              ></div>
            </div>
        );
    }
}
