import { render } from '@wordpress/element';
import App from './App';




const container = document.getElementById('library-manager-app');
if (container) {
    render(<App />, container);
}