class ListImg extends React.PureComponent 
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
    const { src, className, product_id } = this.props;

    return (
      <img
        src={this.state.intersected ? src : cdn_url+"placeholder.png"}
        data-main={src}
        className={className}
        id={'item_image_'+product_id}
        ref={elem => (this.imgTag = elem)}
      />
    );
  }
}