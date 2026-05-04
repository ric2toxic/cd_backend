
class ParentMenuItem extends React.Component {

  constructor(props)
  {
   	super(props);
    this.generateSubChildren = this.generateSubChildren.bind(this);
    this.generateChildren    = this.generateChildren.bind(this);
  }

  decode_html(text)
  {
     var decoded = $('<div/>').html(text).text();
     return decoded;
  }


  generateSubChildren(subchildrenitem, index){
    return (<li className="dropdown hover_mega_menu" key={index}>
             <a href={subchildrenitem.href} id={'wsb-nav-'+subchildrenitem.name_for_id}>
             { this.decode_html(subchildrenitem.link_title) }</a>
             {subchildrenitem.children ?
             <div className="dropdown-menu left_megamenu_section" role="menu">
               <div className="col-sm-12 nopadding">
                  <ul className="left_sub_navigation">
                      {
                       subchildrenitem.children.map((childrenitem, index) => {

                         return this.generateChildren(childrenitem, index)
                         })
                      }
                      
                  </ul>
             </div>
            </div>
            : ''}
            </li>)   
   }


  generateChildren(childrenitem, index){
   if(index == 0) { var active="active"; } else { var active=""; }
    return (<li key={index} className={'dropdown '+active}>
           <a href={childrenitem.href} id={'wsb-nav-'+childrenitem.name_for_id} >{ this.decode_html(childrenitem.link_title) }</a>
             {childrenitem.children ? 
               childrenitem.child_link_type == 'megamenu' ?
                childrenitem.children.map((lastitem, index) => {
                     return (<div className={'dropdown-submenu left-submenu '+active} role="menu" dangerouslySetInnerHTML={{ __html: lastitem.name }} />)  
                })
              :
                <div className={"dropdown-submenu left_submenu submenu_3level "+ active} role="menu">
                <div className="col-sm-12 nopadding">
                  <ul className="left_sub_navigation left_sub_navigation3">
                    {childrenitem.children.map((lastitem, index) => {
                       return (this.generateChildren3level(lastitem, index))  
                     })
                    }
                 </ul>
                  </div>
                    </div>     
            : ''}
            </li>)   
   }


  generateChildren3level(childrenitem, index)
  {
     if(index == 0) { var active="active"; } else { var active=""; }

       return (<li key={index} className={'dropdown '+active}>
                      <a href={childrenitem.href} id={'wsb-nav-'+childrenitem.name_for_id} >{ this.decode_html(childrenitem.link_title) }</a>
               </li>)        
   }


    render() {

      return (
        <section>
      
          <li className="parent_category highlighted left_category" data-toggle="collapse" data-target={'#wsb-nav-'+this.props.item.value} data-category={this.props.item.value}>
           
           { this.decode_html(this.props.item.link_title) }
            
            {this.props.item.children ?          
             <i className="fa icono-plus pull-right click_add_btn icono-minus" aria-hidden="true"></i>
             : ''
            }
           </li>
          
          <div className="parent_category collapse in" id={'wsb-nav-'+this.props.item.value} data-category={this.props.item.value}>
          {this.props.item.children ?
             this.props.item.children.map((childrenitem, index) => {
                    return this.generateSubChildren(childrenitem, index)
             })
          : ''
          }
          </div>
       </section>
        )

     }
   } 