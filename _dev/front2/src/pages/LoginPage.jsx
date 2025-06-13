const React = window.React;
const { Link } = window.ReactRouterDOM;
const { useToast, Button, Icons } = window;
const { Mail, Lock, LogIn } = Icons;

const LoginPage = () => {
    const { toast } = useToast();

    const handleLogin = (e) => {
        e.preventDefault();
        toast({
            title: "🔐 Supabase Authentication Needed",
            description: "To enable login, please connect your Supabase account. I'll guide you through it once you're ready!",
            duration: 8000,
        });
    };

    const handleFeatureRequest = (e) => {
        e.preventDefault();
        toast({
            title: "🚧 Feature Not Implemented",
            description: "This feature isn't implemented yet—but you can request it in your next prompt! 🚀",
        });
    };

    return (
        <div className="w-full flex-grow flex items-center justify-center bg-secondary p-4">
            <div className="w-full max-w-md p-8 space-y-6 bg-card rounded-2xl shadow-lg border">
                <div className="text-center">
                    <h2 className="text-3xl font-bold text-foreground">Welcome Back!</h2>
                    <p className="mt-2 text-muted-foreground">Sign in to access your account.</p>
                </div>
                <form className="mt-8 space-y-6" onSubmit={handleLogin}>
                    <div className="relative">
                        <Mail className="absolute left-3 top-1/2 transform -translate-y-1/2 text-muted-foreground w-5 h-5" />
                        <input
                            id="email-address"
                            name="email"
                            type="email"
                            autoComplete="email"
                            required
                            className="w-full pl-10 p-3 border rounded-md bg-background focus:ring-2 focus:ring-primary focus:border-transparent"
                            placeholder="Email address"
                        />
                    </div>
                    <div className="relative">
                        <Lock className="absolute left-3 top-1/2 transform -translate-y-1/2 text-muted-foreground w-5 h-5" />
                        <input
                            id="password"
                            name="password"
                            type="password"
                            autoComplete="current-password"
                            required
                            className="w-full pl-10 p-3 border rounded-md bg-background focus:ring-2 focus:ring-primary focus:border-transparent"
                            placeholder="Password"
                        />
                    </div>

                    <div className="flex items-center justify-end">
                        <div className="text-sm">
                            <a href="#" onClick={handleFeatureRequest} className="font-medium text-primary hover:text-primary/80">
                                Forgot your password?
                            </a>
                        </div>
                    </div>

                    <div>
                        <Button type="submit" className="w-full">
                            <LogIn className="w-5 h-5 mr-2" />
                            Sign in
                        </Button>
                    </div>
                </form>
                <p className="mt-4 text-center text-sm text-muted-foreground">
                    Not a member?{' '}
                    <a href="#" onClick={handleFeatureRequest} className="font-medium text-primary hover:text-primary/80">
                        Sign up
                    </a>
                </p>
            </div>
        </div>
    );
};
window.LoginPage = LoginPage;