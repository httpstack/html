const React = window.React;
const { useToast } = window;

const Footer = ({ navigationData, handleNavClick }) => {
  const { toast } = useToast();

  const handleFooterLinkClick = (e) => {
    e.preventDefault();
    toast({
      title: "🚧 Legal Pages",
      description: "Legal pages aren't implemented yet—but don't worry! You can request them in your next prompt! 🚀"
    });
  };

  const handleSocialLinkClick = (e) => {
    e.preventDefault();
    toast({
      title: "🚧 Social Links",
      description: "Social media integration isn't implemented yet—but don't worry! You can request it in your next prompt! 🚀"
    });
  };

  return (
    <footer className="bg-secondary border-t">
      <div className="container py-12">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
          <div className="footer-section">
            <h4 className="font-semibold text-foreground mb-4">Quick Links</h4>
            <ul className="space-y-2">
              {navigationData.main.slice(0, 4).map((item, index) => (
                <li key={index}>
                  <a 
                    href={item.href}
                    onClick={(e) => {
                      e.preventDefault();
                      handleNavClick(item.id);
                    }}
                    className="text-sm text-muted-foreground hover:text-primary"
                  >
                    {item.label}
                  </a>
                </li>
              ))}
            </ul>
          </div>
          
          <div className="footer-section">
            <h4 className="font-semibold text-foreground mb-4">Services</h4>
            <ul className="space-y-2">
              <li><a href="#" className="text-sm text-muted-foreground hover:text-primary">Network Administration</a></li>
              <li><a href="#" className="text-sm text-muted-foreground hover:text-primary">System Troubleshooting</a></li>
              <li><a href="#" className="text-sm text-muted-foreground hover:text-primary">Web Development</a></li>
              <li><a href="#" className="text-sm text-muted-foreground hover:text-primary">Technical Support</a></li>
            </ul>
          </div>
          
          <div className="footer-section">
            <h4 className="font-semibold text-foreground mb-4">Legal</h4>
            <ul className="space-y-2">
              {navigationData.footer.map((item, index) => (
                <li key={index}>
                  <a href={item.href} onClick={handleFooterLinkClick} className="text-sm text-muted-foreground hover:text-primary">
                    {item.label}
                  </a>
                </li>
              ))}
            </ul>
          </div>
          
          <div className="footer-section">
            <h4 className="font-semibold text-foreground mb-4">Connect</h4>
            <p className="text-sm text-muted-foreground mb-4">
              Let's discuss how my 25 years of IT experience can benefit your organization.
            </p>
            <div className="flex space-x-4">
              {navigationData.social.map((social, index) => (
                <a 
                  key={index}
                  href={social.href}
                  className="social-link"
                  aria-label={social.label}
                  onClick={handleSocialLinkClick}
                >
                  <social.icon className="w-4 h-4" />
                </a>
              ))}
            </div>
          </div>
        </div>
        
        <div className="mt-12 pt-8 border-t text-center text-sm text-muted-foreground">
          <p>&copy; {new Date().getFullYear()} TechPro Portfolio. All rights reserved. Built with modern web technologies.</p>
        </div>
      </div>
    </footer>
  );
};
window.Footer = Footer;