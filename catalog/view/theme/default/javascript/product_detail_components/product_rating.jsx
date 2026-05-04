class ProductRating extends React.Component {
	
	render(){

                {/* for star rating
                var stars = [];
                for(var i = 1; i <= 5; i++) {
                    if( i <= this.props.rating){
                        stars.push(
                            <span key={i} className="fa fa-stack">
                                <i className="fa fa-star fa-stack-1x"></i>
                            </span>
                        );
                    }else{
                        stars.push(
                            <span key={i} className="fa fa-stack">
                                <i className="fa fa-star-o fa-stack-1x"></i>
                            </span>
                        );
                    }
                }  
                */}
                var rating = '';
                var rating_cls = 'danger'; 
                var ratind_value = 'danger'; 
                if(this.props.rating == 1 || this.props.rating == 2 || this.props.rating == 3){
                    rating_cls = 'danger'; 
                    ratind_value = 'Average';
                }else
                if(this.props.rating == 4){
                    rating_cls = 'warning'; 
                    ratind_value = 'Good';
                }else
                if(this.props.rating == 5){
                    rating_cls = 'success'; 
                    ratind_value = 'Excellent';
                }

		return(
                    <div className="rating col-sm-10"> 
                        {/*{stars}*/}

                        {this.props.rating > 0 ?
                            <span className={'label rating_span label-'+rating_cls}>{ratind_value} Quality</span>  
                        :  ''
                        }
                    </div>
		)
	}

}
