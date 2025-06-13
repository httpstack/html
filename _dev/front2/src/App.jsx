const React = window.React;
const { Routes, Route, useNavigate, useLocation } = window.ReactRouterDOM;
const {
  ToastProvider,
  Toaster,
  useScrollspy,
  navigationData,
  Header,
  Footer,
  HomePage,
  LoginPage
} = window;

const App = () => {
  const sectionIds = navigationData.main.map(item => item.id);
  const activeSection = useScrollspy(sectionIds, 150);
  const navigate = useNavigate();
  const location = useLocation();

  const handleNavClick = (sectionId) => {
    if (location.pathname !== '/') {
      navigate('/');
      setTimeout(() => {
        const element = document.getElementById(sectionId);
        if (element) {
          element.scrollIntoView({ behavior: 'smooth' });
        }
      }, 100);
    } else {
      const element = document.getElementById(sectionId);
      if (element) {
        element.scrollIntoView({ behavior: 'smooth' });
      }
    }
  };

  return (
    <ToastProvider>
      <div className="min-h-screen flex flex-col bg-background">
        <Header
          navigationData={navigationData}
          activeSection={activeSection}
          handleNavClick={handleNavClick}
        />
        
        <main className="flex-grow">
          <Routes>
            <Route path="/" element={<HomePage handleNavClick={handleNavClick} />} />
            <Route path="/login" element={<LoginPage />} />
          </Routes>
        </main>

        <Footer navigationData={navigationData} handleNavClick={handleNavClick} />
        <Toaster />
      </div>
    </ToastProvider>
  );
};
window.App = App;