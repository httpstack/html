const React = window.React;
const { useToast } = window;

const Toaster = () => {
  const { toasts } = useToast();

  return (
    <div className="fixed top-0 right-0 z-[100] p-4">
      <div className="flex flex-col gap-2">
        {toasts.map(({ id, title, description, action }) => (
          <div
            key={id}
            className="w-full max-w-sm p-4 bg-card text-card-foreground border rounded-lg shadow-lg"
          >
            <div className="grid gap-1">
              {title && <p className="font-semibold">{title}</p>}
              {description && (
                <div className="text-sm opacity-90">{description}</div>
              )}
              {action}
            </div>
          </div>
        ))}
      </div>
    </div>
  );
};
window.Toaster = Toaster;