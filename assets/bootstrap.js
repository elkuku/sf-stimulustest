import {startStimulusApp} from '@symfony/stimulus-bridge'

// import { Autocomplete } from 'stimulus-autocomplete';
import {Autocomplete} from '@symfony/stimulus-bridge/lazy-controller-loader?lazy=true&export=Autocomplete!stimulus-autocomplete'

// OK:
// import Clipboard from 'stimulus-clipboard'

// OK
import Clipboard from '@symfony/stimulus-bridge/lazy-controller-loader?export=Clipboard!stimulus-clipboard'

// KO:
// import Clipboard from '@symfony/stimulus-bridge/lazy-controller-loader?lazy=true&export=Clipboard!stimulus-clipboard';

// Registers Stimulus controllers from controllers.json and in the controllers/ directory
export const app = startStimulusApp(require.context(
    '@symfony/stimulus-bridge/lazy-controller-loader!./controllers',
    true,
    /\.(j|t)sx?$/
))

app.register('autocomplete', Autocomplete)
app.register('clipboard', Clipboard)
