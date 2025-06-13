const React = window.React;
const { Button } = window;

const HeroSection = ({ handleNavClick }) => {
  return (
    <section id="home" className="py-24 md:py-32 lg:py-40 text-center bg-secondary">
      <div className="container">
        <div className="fade-in">
          <h1 className="text-4xl md:text-5xl lg:text-6xl font-bold tracking-tighter">
            Computer & Network Technician
          </h1>
          <p className="max-w-2xl mx-auto mt-4 text-lg md:text-xl text-muted-foreground">
            25 Years of Technical Excellence | Transitioning to Full-Stack Development
          </p>
          <div className="mt-8 flex justify-center gap-4 flex-wrap">
            <Button 
              size="lg"
              onClick={() => handleNavClick('projects')}
            >
              View My Work
            </Button>
            <Button 
              size="lg"
              variant="outline"
              onClick={() => handleNavClick('contact')}
            >
              Get In Touch
            </Button>
          </div>
        </div>
      </div>
    </section>
  );
};
window.HeroSection = HeroSection;