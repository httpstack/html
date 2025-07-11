        function LoginForm(props) {
            const [strUsername, setUsername] = React.useState('');
            const [strPassword, setPassword] = React.useState('');

            const handleLogin = (e) => {
                e.preventDefault();
                alert(`Logging in with: ${strUsername}`);
            };

            return (
                <form onSubmit={handleLogin} style={{ border: '1px solid blue', padding: '1em', margin: '1em 0' }}>
                    <h3>Login</h3>
                    <input 
                        type="text" 
                        placeholder="Username" 
                        value={strUsername} 
                        onChange={e => setUsername(e.target.value)} 
                    />
                    <br />
                    <input 
                        type="password" 
                        placeholder="Password" 
                        value={strPassword} 
                        onChange={e => setPassword(e.target.value)} 
                    />
                    <br />
                    <button type="submit">Log In</button>
                </form>
            );
        }

        function Header(props) {
            const { strTitle } = props;
            return <header><h1>{strTitle || 'Default Title'}</h1></header>;
        }