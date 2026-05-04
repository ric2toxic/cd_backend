import * as actionType from '../actions/ActionType';

export default (previousState = false, { type, payload }) => {
    if (type === actionType.FOOTER_INFORMATION_SUCCESS) {
        return payload.information;
    }
    return previousState;
}