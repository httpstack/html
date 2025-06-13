const React = window.React;
const { Icons } = window;
const { Monitor, Code, Briefcase } = Icons;

const AboutSection = () => {
  return (
    <section id="about" className="py-20 md:py-28">
      <div className="container">
        <div className="text-center mb-16">
          <h2 className="text-3xl md:text-4xl font-bold">About Me</h2>
          <p className="max-w-3xl mx-auto mt-4 text-muted-foreground">
            A seasoned IT professional with 25 years of hands-on experience in computer and network systems, 
            now expanding into modern web development and coding.
          </p>
        </div>
        
        <div className="grid md:grid-cols-3 gap-8">
          <div className="card text-center p-8 slide-up">
            <div className="flex justify-center mb-4">
              <Monitor className="w-12 h-12 text-primary" />
            </div>
            <h3 className="text-xl font-semibold mb-2">Technical Expertise</h3>
            <p className="text-muted-foreground">
              Deep knowledge in system administration, network infrastructure, 
              and hardware troubleshooting across enterprise environments.
            </p>
          </div>
          
          <div className="card text-center p-8 slide-up" style={{ animationDelay: '200ms' }}>
            <div className="flex justify-center mb-4">
              <Code className="w-12 h-12 text-primary" />
            </div>
            <h3 className="text-xl font-semibold mb-2">Development Skills</h3>
            <p className="text-muted-foreground">
              Actively learning modern web technologies including PHP, JavaScript, 
              and React to transition into full-stack development.
            </p>
          </div>
          
          <div className="card text-center p-8 slide-up" style={{ animationDelay: '400ms' }}>
            <div className="flex justify-center mb-4">
              <Briefcase className="w-12 h-12 text-primary" />
            </div>
            <h3 className="text-xl font-semibold mb-2">Career Goals</h3>
            <p className="text-muted-foreground">
              Seeking opportunities in software development and technical support 
              roles where I can leverage my extensive IT background.
            </p>
          </div>
        </div>
      </div>
    </section>
  );
};
window.AboutSection = AboutSection;