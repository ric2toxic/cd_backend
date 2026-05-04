import React from 'react'

const Anchor = (props) => (
        <a href={props.href} id={props.id} onClick={props.onClick} className={props.className} data-toggle={props.dataToggle} data-target={props.dataTarget}>
         {props.icon ?
          <i className={props.icon} aria-hidden="true" />
          : '' }
          {props.title}
        </a>
)

export default Anchor
