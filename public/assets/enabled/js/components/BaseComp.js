 class GoogleButton extends HTMLElement {
            constructor() {
                super(); // Always call super() first in the constructor of a custom element    
                this.attachShadow({ mode: 'open' });
            }

            /**
             * connectedCallback is called when the custom element is inserted into the DOM.
             * This is the ideal place to render the component's initial structure
             * and attach event listeners, as the element is now part of the document.
             */
            connectedCallback() {
                this.render(); // First, render the HTML content into the shadow DOM
                this.setupEventListeners(); // Then, set up event listeners on the rendered elements
            }

            /**
             * Renders the HTML content and styles into the shadow DOM.
             * This method creates the button element.
             */
            render() {
                this.shadowRoot.innerHTML = `
                    <style>
                        /* Styles scoped specifically to this custom component's shadow DOM */
                        :host {
                            /* :host targets the custom element itself (e.g., <google-button>) */
                            display: block; /* Makes the custom element behave like a block-level element */
                            width: 100%;
                            max-width: 300px; /* Limit width for better appearance */
                            background-color: #2d3748; /* Slightly lighter dark background */
                            padding: 20px;
                            border-radius: 12px; /* Rounded corners for the component container */
                            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3); /* Soft shadow */
                            text-align: center;
                        }

                        .google-button {
                            /* Styling for the actual button inside the component */
                            background: linear-gradient(135deg, #4285F4, #34A853); /* Google-themed gradient */
                            color: white;
                            border: none;
                            padding: 15px 30px;
                            border-radius: 8px; /* Rounded corners for the button */
                            font-size: 1.1em;
                            font-weight: 600;
                            cursor: pointer;
                            transition: all 0.3s ease; /* Smooth transitions for hover/active states */
                            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); /* Button shadow */
                            width: 100%; /* Make button fill its container */
                            box-sizing: border-box; /* Include padding in width */
                        }

                        .google-button:hover {
                            background: linear-gradient(135deg, #34A853, #FBBC05); /* Change gradient on hover */
                            transform: translateY(-3px); /* Slight lift effect */
                            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3); /* Enhanced shadow on hover */
                        }

                        .google-button:active {
                            transform: translateY(0); /* Press down effect */
                            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2); /* Reduced shadow on active */
                        }
                    </style>
                    <button class="google-button" id="myGoogleButton">Go to Google</button>
                `;
            }

            /**
             * Sets up event listeners for elements within the shadow DOM.
             * This is called after the render method has created the elements.
             */
            setupEventListeners() {
                // Get a reference to the button using its ID within the shadow DOM.
                // querySelector is used here as it's a single element.
                const button = this.shadowRoot.querySelector('#myGoogleButton');

                // Check if the button was found to prevent errors.
                if (button) {
                    // Attach a click event listener to the button.
                    // An arrow function is used as the event handler to ensure 'this'
                    // inside the handler correctly refers to the GoogleButton instance.
                    button.addEventListener('click', () => {
                        this.goToGoogle(); // Call the method to perform the redirection
                    });
                } else {
                    console.error("Error: Button with ID 'myGoogleButton' not found in shadow DOM.");
                }
            }

            /**
             * Method to handle the button click and redirect to Google.
             */
            goToGoogle() {
                console.log("Redirecting to Google...");
                window.location.href = "https://www.google.com"; // Change the browser's URL
            }

            // Optional lifecycle callbacks (good practice to include, even if empty)
            disconnectedCallback() {
                console.log('GoogleButton disconnected from DOM');
                // Good place to remove event listeners if they were not set up with arrow functions
                // or if you need to clean up other resources.
            }

            adoptedCallback() {
                console.log('GoogleButton adopted to new document');
            }

            attributeChangedCallback(name, oldValue, newValue) {
                console.log(`Attribute ${name} changed from ${oldValue} to ${newValue}`);
            }

            // static get observedAttributes() { return ['some-attribute']; }
        }

        // Define the custom element with its tag name.
        // This makes <google-button> available for use in your HTML.
        customElements.define('google-button', GoogleButton);