class Menu extends React.Component {

   constructor()
   {
     super();
    this.state = {
      data: [],
      preference_menu: [],
      pathname: window.location.pathname,
      home_link:false
    };
    
   } 

   componentDidMount()
  {
    var preferences='';
    preferences = getCookie('preferences');

    axios({
    method:'get',
    url:'./api/header/menu&preferences='+preferences,
    responseType:'json'
   })
   .then(response => {
    
            this.setState({data: response.data.data.menus})
            this.setState({preference_menu: response.data.data.preference_menu})
            this.setState({home_link: response.data.data.home_link})
            
   });

      $(document).delegate('.left_sub_navigation li', 'mouseover', function(e) {       
      $(this).parent('ul').find(".dropdown-submenu").removeClass("active");
      $(this).parent('ul').find("li").removeClass("active");
      $(this).children('a').next(".dropdown-submenu").addClass("active");
      $(this).addClass("active");
   });
   
  $(document).delegate('.parent_category a', 'click', function(event) {  
      event.preventDefault(); 
      var href = $(this).attr('href');
      var category_id = $(this).parent("li").attr('data-category');
    if(href != '#')
    {  
      if(!$(this).hasClass( "no-link" ))
      {
        var category_id = $(this).parents(".parent_category").attr('data-category');
        createCookie('preferences', category_id, 7);
        history.pushState('', null, href);
        location.reload();

      }

      $(this).children(".click_add_btn").toggleClass( "icono-minus" );

        $( ".parent_category" ).each(function( index ) {
          if(!$(this).hasClass( "collapsed" ))
          {
            if($(this).attr('data-category') != category_id)
            {
              $(this).trigger("click");
              $(this).children("a").children(".click_add_btn").toggleClass( "icono-minus" );
            }  
          }
        });

    }
    else
    {
       event.stopPropagation();
    }

   });

  
   $(document).delegate('.left_category', 'click', function(event) { 
     var category_id = $(this).attr('data-category');
     $(this).children("i").toggleClass( "icono-minus" );
     $( ".parent_category" ).each(function( index ) {
          if(!$(this).hasClass( "collapsed" ))
          {
            if($(this).attr('data-category') != category_id)
            {
              $(this).trigger("click");
              $(this).children("a").children(".click_add_btn").toggleClass( "icono-minus" );
            }  
          }
      });

   });  


  if(this.props.home_page && this.props.home_page == 1)
  {
    
      $(document).delegate('.hover_mega_menu a', 'mouseover', function(event) {
         if($(".head_menu_icon").hasClass( "hidden"))
          {
            var top_height = $(document).scrollTop();
            if(top_height > 0) { top_height = top_height+45; }
            $(this).next(".left_megamenu_section").css('top', 105+top_height+'px');
          }
         else
          {
           $(this).next(".left_megamenu_section").css('top', 151+'px');
          }  
       });
   
  }   



    /*if(this.props.international_store == 1)
    {
       var changeClass;
       changeClass = setInterval(function(){ if($( "div" ).hasClass( "in_price" ))
            {
              $(".in_price").addClass("hide");
            } 
            clearInterval(changeClass);
        }, 1000);
    }
    else
    {
      var changeClass;
       changeClass = setInterval(function(){ if($( "div" ).hasClass( "co_price" ))
            {
              $(".co_price").addClass("hide");
            } 
            clearInterval(changeClass);
        }, 1000);
    }*/   

  }

  render() {

//$(".boxscroll").niceScroll({cursorborder:"",cursorcolor:"#ccc"});
 var self = this;
 var isHome  = this.props.home_page;
  if(isHome && isHome == 1)
  {
    if(this.state.data.length > 0)
   {
    var parent_menu =  this.state.preference_menu;
    var sub_menu    =  this.state.data;

		return ( 
                <ul className="nav header_navigation" >
                <li className="dropdown dropdown-toggle">
                <a className="nav_icon_bar head_menu_icon hidden" data-toggle="dropdown" href="#" id='data_toggle'> 
                  <span className="icon-bar"></span>
                  <span className="icon-bar"></span>
                  <span className="icon-bar"></span>
                </a>

              <div className="home_page_menu left_navigation_box">
                <ul className="left_navigation_update">
                  <li className="left_navigation_title">Shop by Category</li>
                  {
                    parent_menu.map((menu, index) => {
                   return <ParentMenuItem key={index} count_index={index} item={menu} home_link={self.state.home_link}  />
                     })
                  }
                </ul>

                <ul className="navigation_main nav">
                  <li className="left_category">More Categories</li>
                  {
                    sub_menu.map((menu, index) => {
                   return <MenuItem key={index} count_index={index}  item={menu} home_link={self.state.home_link}  />
                     })
                  }
                </ul>
              </div>  

                </li>
                </ul>);
    
   }
  else
  {
     return (<div style={{background: '#f3f3f3',
    minHeight: '500px',
    overflow: 'hidden',
    width: '210px'}}> </div>);
  } 
}
   else
   {
     
    var parent_menu =  this.state.preference_menu;
    var sub_menu    =  this.state.data;

     return (
            <div className="container-fluid nopadding " >
            <ul className="nav header_navigation" >
             <li className="dropdown dropdown-toggle sticky_menu_top_btn inner_menu_icon">
                <a className="nav_icon_bar head_menu_icon" data-toggle="dropdown" href="#" id='data_toggle'> 
                  <span className="icon-bar"></span>
                  <span className="icon-bar"></span>
                  <span className="icon-bar"></span>
                </a>
                 
               <div className="home_page_menu left_navigation_box">
                 <ul className="left_navigation_update">
                  <li className="left_navigation_title">Shop by Category</li>
                  {
                    parent_menu.map((menu, index) => {
                   return <ParentMenuItem key={index} count_index={index}  item={menu} home_link={self.state.home_link}  />
                     })
                  }
                </ul>

                <ul className="home_page_menu navigation_main nav">
                  <li className="left_category">More Categories</li>
                  {
                    sub_menu.map((menu, index) => {
                   return <MenuItem key={index} count_index={index}  item={menu} home_link={self.state.home_link}  />
                     })
                  }
                </ul>
             
              </div>

              </li>
            </ul>
            </div>
    );

   } 


  }
}
