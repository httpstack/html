const React = window.React;
const { useToast, Button, Icons } = window;
const { ExternalLink } = Icons;

const ProjectsSection = ({ projectsData }) => {
  const { toast } = useToast();

  const handleProjectView = (project) => {
    toast({
      title: "🚧 Project Details",
      description: "Project showcase isn't implemented yet—but don't worry! You can request it in your next prompt! 🚀"
    });
  };

  return (
    <section id="projects" className="py-20 md:py-28">
      <div className="container">
        <div className="text-center mb-16">
          <h2 className="text-3xl md:text-4xl font-bold">Featured Projects</h2>
          <p className="max-w-3xl mx-auto mt-4 text-muted-foreground">
            Showcasing key projects that demonstrate my technical capabilities and problem-solving skills.
          </p>
        </div>
        
        <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
          {projectsData.map((project, index) => (
            <div key={index} className="card flex flex-col">
              <div className="p-6">
                <div className="flex justify-between items-start mb-2">
                  <span className="text-sm font-medium text-primary bg-primary/10 px-2.5 py-1 rounded-full">
                    {project.type}
                  </span>
                  <span className="text-sm text-muted-foreground">{project.year}</span>
                </div>
                <h3 className="text-xl font-semibold mb-2">{project.title}</h3>
                <p className="text-muted-foreground text-sm flex-grow">{project.description}</p>
              </div>
              <div className="p-6 pt-0">
                <div className="flex flex-wrap gap-2 mb-4">
                  {project.technologies.map((tech, techIndex) => (
                    <span 
                      key={techIndex}
                      className="text-xs bg-secondary text-secondary-foreground px-2 py-1 rounded"
                    >
                      {tech}
                    </span>
                  ))}
                </div>
                <Button 
                  variant="outline" 
                  size="sm"
                  onClick={() => handleProjectView(project)}
                  className="w-full"
                >
                  <ExternalLink className="w-4 h-4 mr-2" />
                  View Details
                </Button>
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
};
window.ProjectsSection = ProjectsSection;