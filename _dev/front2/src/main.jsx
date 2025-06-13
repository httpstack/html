const { StrictMode } = window.React;
const { createRoot } = window.ReactDOM;
const { BrowserRouter } = window.ReactRouterDOM;
const { App } = window;

const rootElement = document.getElementById('root');
const root = createRoot(rootElement);

root.render(
  <StrictMode>
    <BrowserRouter>
      <App />
    </BrowserRouter>
  </StrictMode>
);