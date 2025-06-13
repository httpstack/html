const { createContext, useContext } = window.React;

const ToastContext = createContext(null);
window.ToastContext = ToastContext;

const useToast = () => {
  const context = useContext(ToastContext);

  if (context === null) {
    throw new Error('useToast must be used within a ToastProvider');
  }

  return context;
};
window.useToast = useToast;