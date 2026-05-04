class ImageList extends React.PureComponent 
{
  constructor(props) {
    super(props);
    this.image_load = this.image_load.bind(this);
    this.state = { intersected: false };
    this.observer = null;
  }

  componentDidMount() {
     this.image_load(); 
  }

  componentWillReceiveProps()
  {
    this.observer.disconnect();
    this.image_load(); 
  }

  componentWillUnmount() 
  {
    this.observer.disconnect();
  }

  image_load()
  {
    this.observer = new IntersectionObserver(entries => {
      const image = entries[0];
      if (image.isIntersecting) {
        this.setState({ intersected: true });
        this.observer.disconnect();
      }
    });

    this.observer.observe(this.imgTag);
  }


  render() {

    return (
      <img
        src={this.state.intersected ? this.props.image.thumb : cdn_url+"placeholder.png"}
        data-large={this.props.image.pan_detail}
        data-zoom={this.props.image.popup}
        className="img-responsive"
        data-target="#carousel-main"
        width={this.props.image.additional_width+'px'}
        height={this.props.image.additional_height+'px'}
        id={'image-'+this.props.count}
        ref={elem => (this.imgTag = elem)}
      />
    );
  }
}