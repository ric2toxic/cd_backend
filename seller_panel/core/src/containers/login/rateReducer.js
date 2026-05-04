import { BITCOIN_RATE_RECEIVED } from './bitcoinRateReceived';

export default (previousState = 123456, { type, payload }) => {
    if (type === BITCOIN_RATE_RECEIVED) {
        return payload.rate;
    }
    return previousState;
}