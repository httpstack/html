const React = window.React;
const { 
  HeroSection, 
  AboutSection, 
  SkillsSection, 
  ProjectsSection, 
  ResumeSection, 
  ContactSection,
  skillsData,
  projectsData,
  resumeFormats,
  navigationData
} = window;

const HomePage = ({ handleNavClick }) => {
  return (
    <React.Fragment>
      <HeroSection handleNavClick={handleNavClick} />
      <AboutSection />
      <SkillsSection skillsData={skillsData} />
      <ProjectsSection projectsData={projectsData} />
      <ResumeSection resumeFormats={resumeFormats} />
      <ContactSection navigationData={navigationData} />
    </React.Fragment>
  );
};
window.HomePage = HomePage;