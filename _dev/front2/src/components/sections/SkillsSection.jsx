const React = window.React;
const { Icons } = window;
const { Server, Code } = Icons;

const SkillsSection = ({ skillsData }) => {
  return (
    <section id="skills" className="py-20 md:py-28 bg-secondary">
      <div className="container">
        <div className="text-center mb-16">
          <h2 className="text-3xl md:text-4xl font-bold">Technical Skills</h2>
          <p className="max-w-3xl mx-auto mt-4 text-muted-foreground">
            A comprehensive overview of my technical competencies built over 25 years in IT.
          </p>
        </div>
        
        <div className="grid md:grid-cols-2 gap-12">
          <div>
            <h3 className="text-2xl font-semibold mb-6 flex items-center gap-3">
              <Server className="w-6 h-6 text-primary" />
              Infrastructure & Systems
            </h3>
            <div className="grid grid-cols-2 gap-4">
              {skillsData.technical.map((skill, index) => (
                <div key={index} className="skill-item bg-card p-4 rounded-lg border">
                  <div className="font-semibold text-foreground">{skill.name}</div>
                  <div className="text-sm text-muted-foreground">{skill.level}</div>
                  <div className="text-xs text-muted-foreground mt-1">{skill.years} years</div>
                </div>
              ))}
            </div>
          </div>
          
          <div>
            <h3 className="text-2xl font-semibold mb-6 flex items-center gap-3">
              <Code className="w-6 h-6 text-primary" />
              Programming & Development
            </h3>
            <div className="grid grid-cols-2 gap-4">
              {skillsData.programming.map((skill, index) => (
                <div key={index} className="skill-item bg-card p-4 rounded-lg border">
                  <div className="font-semibold text-foreground">{skill.name}</div>
                  <div className="text-sm text-muted-foreground">{skill.level}</div>
                  <div className="text-xs text-muted-foreground mt-1">{skill.years} years</div>
                </div>
              ))}
            </div>
          </div>
        </div>
      </div>
    </section>
  );
};
window.SkillsSection = SkillsSection;