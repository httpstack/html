const React = window.React;
const { useToast, Button, Icons } = window;
const { Download } = Icons;

const ResumeSection = ({ resumeFormats }) => {
  const { toast } = useToast();

  const handleResumeDownload = (format) => {
    toast({
      title: "🚧 Download Feature",
      description: "Resume download isn't implemented yet—but don't worry! You can request it in your next prompt! 🚀"
    });
  };

  return (
    <section id="resume" className="py-20 md:py-28 bg-secondary">
      <div className="container">
        <div className="text-center mb-16">
          <h2 className="text-3xl md:text-4xl font-bold">Resume Downloads</h2>
          <p className="max-w-3xl mx-auto mt-4 text-muted-foreground">
            Download my resume in your preferred format for easy application submission.
          </p>
        </div>
        
        <div className="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto">
          {resumeFormats.map((format, index) => (
            <div key={index} className="download-card card text-center p-8">
              <div className="mx-auto w-12 h-12 mb-4 bg-primary text-primary-foreground rounded-full flex items-center justify-center">
                <format.icon className="w-6 h-6" />
              </div>
              <h4 className="font-semibold text-lg mb-2">{format.type} Format</h4>
              <p className="text-sm text-muted-foreground mb-4">{format.description}</p>
              <Button 
                size="sm"
                onClick={() => handleResumeDownload(format)}
                className="w-full"
              >
                <Download className="w-4 h-4 mr-2" />
                Download {format.type}
              </Button>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
};
window.ResumeSection = ResumeSection;