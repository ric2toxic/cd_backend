class SetQuantiyError extends React.Component {
    constructor(props){
        super(props);

    }
    render() {
        setTimeout(function() {
            //$('.set_quantity_error').fadeOut('slow');
        }, 3000);


        return (
            this.props.is_error==0?(
                <div></div>
            ):(
                <div className={"set_quantity_error error_tool_tip_"+this.props.divId+" popover"}>
                    {this.props.error_msg}
                </div>
            )
            

        )
    }

}