/**
 * DocInit Class
 * This class will create an element to go in the document
 * head that takes a filepath for its resList property (resource  list )
 * which is a json file of the resources used and the and info about the resources
 * This custome element will, usiong the json array of resources
 * create either a link or a script tag and add them to the head, or the shadow doc frag
 * that replaces the html element
 */
class DocInit extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({ mode: 'open' });
    }
    connectedCallback() {
        this.render();
    }
    render() {
        resourceList = this.getAttribute("resList");
        //see if jquery is loaded
        if (window.jQuery) {
            $()
        }
        this.shadowRoot.innerHTML = `
        <style>
        
        </style>
        <div>
        
        </div>
        `;
    }
}
customElements.define('doc-init', DocInit);
