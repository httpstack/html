const React = window.React;
const { useState, useCallback } = React;
const { ToastContext } = window;

const ToastProvider = ({ children }) => {
  const [toasts, setToasts] = useState([]);

  const toast = useCallback(({ title, description, ...props }) => {
    const id = Date.now().toString();
    const newToast = { id, title, description, ...props };
    setToasts((prevToasts) => [newToast, ...prevToasts]);

    setTimeout(() => {
      setToasts((prevToasts) =>
        prevToasts.filter((t) => t.id !== id)
      );
    }, props.duration || 5000);
  }, []);

  return (
    <ToastContext.Provider value={{ toasts, toast }}>
      {children}
    </ToastContext.Provider>
  );
};
window.ToastProvider = ToastProvider;