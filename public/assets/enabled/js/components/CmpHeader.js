class CmpHeader extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({ mode: 'open' });
    }

    // Define which attributes to observe for changes
    static get observedAttributes() {
        return ['title', 'logo', 'links'];
    }

    connectedCallback() {
        // Render the component when it's added to the DOM
        this.render();
    }

    /**
     * Renders the entire header structure into the shadow DOM.
     * This method is called initially and whenever an observed attribute changes.
     */
    render() {
        const title = this.getAttribute('title') || 'Default Title';
        const logoClass = this.getAttribute('logo') || ''; // No default icon
        let links = [];

        try {
            // Attempt to parse the links attribute as JSON
            const linksAttr = this.getAttribute('links');
            if (linksAttr) {
                links = JSON.parse(linksAttr);
                // Ensure links is an array
                if (!Array.isArray(links)) {
                    console.warn('cmp-header: "links" attribute is not a valid JSON array. Using empty array.');
                    links = [];
                }
            }
        } catch (e) {
            console.error('cmp-header: Error parsing "links" attribute:', e);
            links = []; // Fallback to empty array on parse error
        }

        // Generate navigation items
        const navItemsHtml = links.map(link => `
            <li class="nav-item">
                <a href="${link.uri || '#'}" class="nav-link ${link.active === 'true' ? 'active' : ''}">
                    ${link.label || 'Link'}
                </a>
            </li>
        `).join('');

        this.shadowRoot.innerHTML = `
            <style>
                /* Minimal styles for the custom element itself */
                :host {
                    display: flex; /* Make the custom element itself a flex container */
                    justify-content: space-between; /* Space between brand and nav */
                    align-items: center; /* Vertically align items */
                    padding: 15px 20px;
                    background-color: #333; /* Simple dark background for the header */
                    color: white;
                    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
                    flex-wrap: wrap; /* Allow wrapping on smaller screens */
                }

                .brand {
                    display: flex;
                    align-items: center;
                    gap: 10px; /* Space between logo and title */
                }

                /* User-specified classes */
                .brand-logo {
                    font-size: 1.5em; /* Adjust icon size */
                }

                .brand-title {
                    font-size: 1.4em; /* Adjust title size */
                    font-weight: bold;
                }

                .nav-bar {
                    list-style: none; /* No list style dots */
                    padding: 0;
                    margin: 0;
                    display: flex; /* Horizontal navigation */
                    gap: 20px; /* Space between nav items */
                    flex-wrap: wrap; /* Allow nav items to wrap */
                }

                .nav-item {
                    /* No specific styles requested, just a container for the link */
                }

                .nav-link {
                    color: white;
                    text-decoration: none; /* Remove underline */
                    padding: 5px 0; /* Add some padding for click area */
                    transition: color 0.3s ease;
                }

                .nav-link:hover {
                    color: #007bff; /* Simple hover effect */
                }

                .nav-link.active {
                    font-weight: bold;
                    color: #007bff; /* Highlight active link */
                }

                /* Basic responsiveness */
                @media (max-width: 600px) {
                    :host {
                        flex-direction: column; /* Stack brand and nav vertically */
                        align-items: flex-start; /* Align to start when stacked */
                        gap: 15px;
                    }
                    .nav-bar {
                        width: 100%; /* Full width for nav bar when stacked */
                        justify-content: center; /* Center nav items when stacked */
                    }
                }
            </style>
            <div class="brand">
                ${logoClass ? `<i class="brand-logo ${logoClass}"></i>` : ''}
                <span class="brand-title">${title}</span>
            </div>
            <nav>
                <ul class="nav-bar">
                    ${navItemsHtml}
                </ul>
            </nav>
        `;
    }

    /**
     * attributeChangedCallback is called when an observed attribute
     * has been added, removed, or updated. We simply re-render.
     */
    attributeChangedCallback(name, oldValue, newValue) {
        if (oldValue !== newValue) {
            this.render();
        }
    }

    disconnectedCallback() {
        console.log('CmpHeader disconnected from DOM');
    }

    adoptedCallback() {
        console.log('CmpHeader adopted to new document');
    }
}

// Define the custom element with its tag name.
customElements.define('cmp-header', CmpHeader);