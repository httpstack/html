
import React, { useEffect } from 'react';
import { Helmet } from 'react-helmet';
import { Toaster } from '@/components/ui/toaster';
import '@/components/CustomNavBar';
import '@/components/CustomLoginForm';
import '@/components/CustomSignupForm';

function App() {
  useEffect(() => {
    // Set initial theme
    document.documentElement.classList.add('dark');
  }, []);

  return (
    <>
      <Helmet>
        <title>Custom Web Components</title>
        <meta name="description" content="Custom web components with login, signup forms and navigation bar" />
      </Helmet>
      
      <div className="min-h-screen bg-gradient-to-br from-indigo-900 via-purple-900 to-slate-900 dark:from-slate-900 dark:via-gray-900 dark:to-black">
        <custom-nav-bar></custom-nav-bar>
        
        <main className="container mx-auto px-4 py-8">
          <div className="text-center mb-12">
            <h1 className="text-4xl md:text-6xl font-bold bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent mb-4">
              Custom Web Components
            </h1>
            <p className="text-xl text-gray-300 dark:text-gray-400 max-w-2xl mx-auto">
              Experience the power of native web components with modern styling and functionality
            </p>
          </div>

          <div className="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
            <div className="space-y-6">
              <h2 className="text-2xl font-semibold text-white dark:text-gray-200 text-center">Login</h2>
              <custom-login-form></custom-login-form>
            </div>
            
            <div className="space-y-6">
              <h2 className="text-2xl font-semibold text-white dark:text-gray-200 text-center">Sign Up</h2>
              <custom-signup-form></custom-signup-form>
            </div>
          </div>
        </main>
      </div>
      
      <Toaster />
    </>
  );
}

export default App;
