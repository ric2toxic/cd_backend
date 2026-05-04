class News extends React.Component {

   constructor(props)
   {
     super(props);
    this.state = {
      data: [],
      
    };
   } 

   componentDidMount()
  {
    
  }

  render() {
      
        var newss_li = news.map(function(news_item, index){
            var cls_li = (index === 0) ? 'active' : ''; 
            if(news.length > 1){
                return (
                    <li data-target="#myCarouselNews" data-slide-to={index} className={cls_li}></li>
                );
            }else{
                return ('');
            }
        });
      
        var newss = news.map(function(news_item, index){
            var cls = (index === 0) ? 'item active' : 'item'; 
            if(news_item.link){
                return (
                    <div key={index} className={cls}>
                        <a href={news_item.link}>
                            <img src={news_item.image} />
                            <span>{news_item.title}</span>
                        </a>
                    </div>
                );
            }else{
                return (
                    <div key={index} className={cls}>
                        <img src={news_item.image} />
                        <span>{news_item.title}</span>
                    </div>
                );
            }
        });
      
        return (
                <div className="news">
                    <div className="news_heading">
                        <h4>Wholesalebox in News</h4>
                    </div>
                    <div className="news_carousel">
                        <div id="myCarouselNews" className="carousel slide" data-ride="carousel">
                            <ol className="carousel-indicators">
                                {newss_li}
                            </ol>
                            <div className="carousel-inner">
                                {newss}
                            </div>
                        </div>
                    </div>
                    <div className="news_title">
                        
                    </div>
                </div>
              )
      
      
      
    }
}

  

