import * as actionType from '../actions/ActionType';

export default (previousState = false, { type, payload }) => {
    if (type === actionType.HEADER_USERLOGIN_SUCCESS) {
        return payload.userlogin;
    }
    return previousState;
}