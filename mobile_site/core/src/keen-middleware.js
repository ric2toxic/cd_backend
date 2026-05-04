import Keen from 'keen-tracking';

// Record all actions to a single event stream
const EVENT_STREAM_NAME = 'app-actions';

// Omit noisy actions if necessary
const OMITTED_ACTIONS = [
  // '@@router/LOCATION_CHANGE'
];

// Define a client instance
const client = new Keen({
  projectId: '5a8fbc4346e0fb00016bc6df',
  writeKey: '582E62BBCC545DFE9829EE2AA2B89C9DC197FC1AAECE0C1DD78E1178E74A62D8AE3D3F5F36DAC0A0B3598BACFB6B56B3FDD54D60E045D1E40184F922AF7BF28E515E3EFC9F5F5E232DED25241A15705779F81E29284DB7152E9B3ACF9A869B91'
});

if (process.env.NODE_ENV !== 'production') {
  // Optionally prevent recording in dev mode
  Keen.enabled = false;
  // Display events in the browser console
  client.on('recordEvent', Keen.log);
}

// Track a 'pageview' event and initialize auto-tracking data model
client.initAutoTracking({
  recordClicks: false,
  recordFormSubmits: false,
  recordPageViews: true
});

const reduxMiddleware = function({ getState }) {
  return (next) => (action) => {
    const returnValue = next(action);
    const eventBody = {
      'action': action,
      'state': getState()
      /*
          Include additional properties here, or
          refine the state data that is recorded
          by cherry-picking specific properties
      */
    };
    // Filter omitted actions by action.type
    // ...or whatever you name this property
    if (OMITTED_ACTIONS.indexOf(action.type) < 0) {
      client.recordEvent(EVENT_STREAM_NAME, eventBody);
    }
    return returnValue;
  };
}

export default reduxMiddleware;