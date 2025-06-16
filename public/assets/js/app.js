const root = document.getElementById('root');

const navLinks = [
    { href: '#about', text: 'About' },
    { href: '#projects', text: 'Projects' },
    { href: '#contact', text: 'Contact' },
];

const socialLinks = [
    { href: 'https://www.linkedin.com', text: 'LinkedIn', icon: 'linkedin' },
    { href: 'https://github.com', text: 'GitHub', icon: 'github' },
    // Add more social links as needed
];

const App = () => {
    return (
        <div>
            <Header />
            <Navbar links={navLinks} />
            <main>
                <section id="projects">
                    <ProjectCard title="Project 1" description="Lorem ipsum..." />
                    <ProjectCard title="Project 2" description="Lorem ipsum..." />
                </section>
            </main>
            <Footer socialLinks={socialLinks} />
        </div>
    );
};

ReactDOM.render(<App />, root);