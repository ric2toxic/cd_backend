class ListLoadingProducts extends React.Component {

	render() {
		return (
        <div>
               <div className="product loadbar"></div>
               <div className="product loadbar"></div>
               <div className="product loadbar"></div>
               <div className="clearfix"></div>
               <LoadingProductTitle />
               <div className="clearfix"></div>
               <LoadingProductDesc />
               <div className="clearfix"></div>
               <LoadingProduct />
               <LoadingProduct />
               <LoadingProduct />
               <LoadingProduct />
               <LoadingProduct />
               <LoadingProduct />
               <LoadingProduct />
               <LoadingProduct />
            </div>
		)
	}
}
