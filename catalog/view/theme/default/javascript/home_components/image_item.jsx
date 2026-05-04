class ImageItem extends React.PureComponent 
{
  constructor(props) {
    super(props);
    this.state = { intersected: false };
    this.observer = null;
  }

  componentDidMount() {
    this.observer = new IntersectionObserver(entries => {
      const image = entries[0];
      if (image.isIntersecting) {
        this.setState({ intersected: true });
        this.observer.disconnect();
      }
    });

    this.observer.observe(this.imgTag);
  }

  componentWillUnmount() {
    this.observer.disconnect();
  }

  render() {

    return (
      <img
        src={this.state.intersected ? this.props.image.thumb : cdn_url+"placeholder.png"}
        data-large={this.props.image.image}
        className="img-responsive"
        data-target="#carousel-main"
        width={this.props.image.additional_width+'px'}
        height={this.props.image.additional_height+'px'}
        id={'item_image_'+this.props.product_id+this.props.count}
        ref={elem => (this.imgTag = elem)}
      />
    );
  }
}