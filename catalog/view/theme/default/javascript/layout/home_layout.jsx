
class HomeLayout extends React.Component {
  
  constructor(props)
   {
     super(props);
   }

  render() {
    return (
          <app>
          <Header custom_store={this.props.custom_store} language={this.props.header_language} international_store={this.props.international_store} home_page={1} />
          <Home custom_store_val={this.props.custom_store_val} language={this.props.home_language} international_store={this.props.international_store} preferences_menus={this.props.preferences_menus} news={this.props.news} />
          <Footer language={this.props.footer_language} international_store={this.props.international_store} />
          <Otpform language={this.props.login_language} international_store={this.props.international_store} otp_verify_success={this.props.otp_verify_success} />
          <Login language={this.props.login_language} international_store={this.props.international_store} otp_verify_success={this.props.otp_verify_success} />
          <Register language={this.props.login_language} international_store={this.props.international_store} otp_verify_success={this.props.otp_verify_success} />
          <Success language={this.props.login_language} otp_verify_success={this.props.otp_verify_success} />
          </app>
    	);
  }
}
