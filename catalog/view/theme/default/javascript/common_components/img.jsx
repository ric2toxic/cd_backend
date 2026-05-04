class Img extends React.PureComponent 
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
        src={this.state.intersected ? this.props.src : cdn_url+"placeholder.png"}
        data-main={this.props.src}
        className={this.props.class}
        ref={elem => (this.imgTag = elem)}
      />
    );
  }
}