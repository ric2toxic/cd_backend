import React,{ Component } from 'react';

class MobileTearSheet  extends Component {

render() {

const styles = {
  root: {
    marginBottom: 24,
    marginRight: 24,
    // maxWidth: '80%',
    width: '100%',
    height:'100%',
    maxHeight: '100%'
  },
  container: {
    border: 'solid 1px #d9d9d9',
    borderBottom: 'none',
    height: '100%',
    overflow: 'hidden',
  },
  bottomTear: {
    display: 'block',
    position: 'relative',
    marginTop: -10,
    maxWidth: 360,
  },
};

return (
  <div style={Object.assign({},styles.root,this.props.style||{})}>
    <div style={styles.container}>
      {this.props.children}
    </div>
    <div style={styles.bottomTear}>
     
    </div>
  </div>
);
  }
}

export default MobileTearSheet;
