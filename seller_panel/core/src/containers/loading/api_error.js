import React from 'react'


const ApiError = (props) => (

   
      <div className="container-fluid width_fix"> 
       <div className="row">
        <div className="col-sm-12" style={{textAlign: 'center', marginTop: '50%', marginBottom: '50%'}}>
           <div className="no_found_text">
           <p style={{fontSize: '17px', color: '#ff0000'}}>  <i className="fa fa-exclamation-triangle"></i> An internal server error!  </p>

             <p>
              <button onClick={props.retry} className="store_switch" style={{width: '120px', display: 'inline', padding:'12px 12px', backgroundColor:'#17319f', fontSize:'16px', color:'#fff', border:'none'}}>Retry</button>
             </p>
          
           </div>
           
        </div>
        </div>
       </div> 
   )

export default ApiError    