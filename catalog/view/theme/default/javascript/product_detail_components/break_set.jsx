class BreakSet extends React.Component {
	render() {
    	return (

    		<div className="brack_set_box">
                    <a className="break_set" data-toggle="collapse" data-parent="#accordion" href="#break_set">Break set!</a> 
                    &nbsp;<div className="clearfix"></div>
                <div className="break_set_saction">
                  <p id="break_set" className="collapse">If you are not comfotable buying full set then you can break the set and can buy 3 or more sizes of your choce.<br /><br />  
                  Want to break Set ? <a className="click_here collapsed" data-toggle="collapse" href="#set_quantity_box" aria-expanded="false">Click Here</a></p>
                    <SetQuantityBox options = {this.props.options} />
                
                  
                </div>
                    
                 
      		</div>

		)
	}
}

