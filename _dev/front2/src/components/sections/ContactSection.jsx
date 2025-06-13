const React = window.React;
const { useToast, Button, Icons } = window;
const { Mail, Phone, MapPin } = Icons;

const ContactSection = ({ navigationData }) => {
  const { toast } = useToast();

  const handleContactSubmit = (e) => {
    e.preventDefault();
    toast({
      title: "🚧 Contact Form",
      description: "Contact form isn't implemented yet—but don't worry! You can request it in your next prompt! 🚀"
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
    <section id="contact" className="py-20 md:py-28">
      <div className="container">
        <div className="text-center mb-16">
          <h2 className="text-3xl md:text-4xl font-bold">Get In Touch</h2>
          <p className="max-w-3xl mx-auto mt-4 text-muted-foreground">
            Ready to discuss opportunities or have questions about my experience? Let's connect!
          </p>
        </div>
        
        <div className="grid md:grid-cols-2 gap-12 max-w-5xl mx-auto">
          <div className="card p-8">
            <h3 className="text-xl font-semibold mb-6">Contact Information</h3>
            <div className="space-y-4">
              <div className="flex items-center gap-3">
                <Mail className="w-5 h-5 text-primary" />
                <span>your.email@example.com</span>
              </div>
              <div className="flex items-center gap-3">
                <Phone className="w-5 h-5 text-primary" />
                <span>(555) 123-4567</span>
              </div>
              <div className="flex items-center gap-3">
                <MapPin className="w-5 h-5 text-primary" />
                <span>Your City, State</span>
              </div>
            </div>
            
            <div className="flex space-x-4 mt-8">
              {navigationData.social.map((social, index) => (
                <a 
                  key={index}
                  href={social.href}
                  className="social-link"
                  aria-label={social.label}
                  onClick={handleSocialLinkClick}
                >
                  <social.icon className="w-5 h-5" />
                </a>
              ))}
            </div>
          </div>
          
          <div className="card p-8">
            <h3 className="text-xl font-semibold mb-6">Send a Message</h3>
            <form onSubmit={handleContactSubmit} className="space-y-4">
              <div>
                <label className="block text-sm font-medium mb-2" htmlFor="name">Name</label>
                <input id="name" type="text" className="w-full p-3 border rounded-md bg-background focus:ring-2 focus:ring-primary focus:border-transparent" required />
              </div>
              <div>
                <label className="block text-sm font-medium mb-2" htmlFor="email">Email</label>
                <input id="email" type="email" className="w-full p-3 border rounded-md bg-background focus:ring-2 focus:ring-primary focus:border-transparent" required />
              </div>
              <div>
                <label className="block text-sm font-medium mb-2" htmlFor="message">Message</label>
                <textarea id="message" rows="4" className="w-full p-3 border rounded-md bg-background focus:ring-2 focus:ring-primary focus:border-transparent" required></textarea>
              </div>
              <Button type="submit" className="w-full">
                Send Message
              </Button>
            </form>
          </div>
        </div>
      </div>
    </section>
  );
};
window.ContactSection = ContactSection;