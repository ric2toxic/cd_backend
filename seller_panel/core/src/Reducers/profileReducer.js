import * as actionType from '../actions/ActionType';

export default (previousState = false, { type, payload }) => {
    if (type === actionType.PROFILE_USERDATA_SUCCESS) {
        return payload.profile_data;
    }
    return previousState;
}