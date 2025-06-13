const React = window.React;
const { useState } = React;
const { useNavigate } = window.ReactRouterDOM;
const { cn, Button, Icons } = window;
const { LogIn } = Icons;

const Header = ({ navigationData, activeSection, handleNavClick }) => {
  const [isMenuOpen, setIsMenuOpen] = useState(false);
  const navigate = useNavigate();

  const toggleMenu = () => setIsMenuOpen(!isMenuOpen);

  const onNavLinkClick = (e, sectionId) => {
    e.preventDefault();
    handleNavClick(sectionId);
    setIsMenuOpen(false);
  };

  const goToLogin = () => {
    navigate('/login');
    setIsMenuOpen(false);
  };

  return (
    <header className="sticky top-0 z-50 w-full border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
      <div className="container flex h-14 items-center">
        <nav className="flex items-center justify-between w-full">
          <a href="#home" className="mr-6 flex items-center space-x-2 text-lg font-bold" onClick={(e) => onNavLinkClick(e, 'home')}>
            TechPro Portfolio
          </a>
          
          <div className={cn("fixed inset-0 top-16 z-50 grid grid-flow-row auto-rows-max overflow-auto p-6 pb-32 shadow-md animate-in slide-in-from-bottom-80 md:relative md:top-0 md:z-auto md:flex md:h-auto md:w-auto md:translate-x-0 md:animate-none md:p-0 md:shadow-none", isMenuOpen ? "grid bg-background" : "hidden")}>
             <ul className="flex flex-col gap-4 md:flex-row md:items-center md:gap-8">
              {navigationData.main.map((item) => (
                <li key={item.id}>
                  <a
                    href={item.href}
                    className={cn(
                      "text-sm font-medium transition-colors hover:text-primary",
                      activeSection === item.id ? "text-primary" : "text-muted-foreground"
                    )}
                    onClick={(e) => onNavLinkClick(e, item.id)}
                  >
                    {item.label}
                  </a>
                </li>
              ))}
              <li className="mt-4 md:mt-0 md:ml-4">
                <Button variant="outline" size="sm" onClick={goToLogin} className="w-full md:w-auto">
                  <LogIn className="w-4 h-4 mr-2" />
                  Login
                </Button>
              </li>
            </ul>
          </div>
          
          <button
            className="md:hidden flex items-center justify-center rounded-md p-2 transition-colors hover:bg-muted"
            onClick={toggleMenu}
            aria-label="Toggle menu"
          >
            {isMenuOpen ? 
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg> : 
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><line x1="4" x2="20" y1="12" y2="12"></line><line x1="4" x2="20" y1="6" y2="6"></line><line x1="4" x2="20" y1="18" y2="18"></line></svg>
            }
          </button>
        </nav>
      </div>
    </header>
  );
};
window.Header = Header;