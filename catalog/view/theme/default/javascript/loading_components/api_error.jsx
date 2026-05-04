
class ApiError extends React.Component {

render() {
    return(<section> 
      <div className="container-fluid width_fix"> 
       <div className="row">
        <div className="col-sm-12" style={{textAlign: 'center', margin: '30px 0px'}}>
           <div className="no_found_text">
           <p style={{fontSize: '17px', color: '#ff0000'}}>  <i className="fa fa-exclamation-triangle"></i> An internal server error!  </p>

           <p>
             <button className="c-quick-buy__button c-button c-button_size_big c-button_color_old-orange  c-button_js_inited" style={{width: '100px', display: 'inline', float: 'none'}} onClick={() => this.props.retry(0) }>Retry</button>
             </p>
          
           </div>
           
        </div>
        </div>
       </div> 
    </section>)
	}
}