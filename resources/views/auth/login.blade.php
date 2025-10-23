<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Informasi Keuangan</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }
        .login-card {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 2.5rem;
            width: 100%;
            max-width: 400px; 
            text-align: center;
        }
        .login-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            background-color: #e0e7ff; 
            border-radius: 50%; 
            color: #4338ca; 
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 1rem;
        }
        .login-card h1 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #111827;
            margin: 0 0 0.5rem 0;
        }
        .login-card p {
            font-size: 0.9rem;
            color: #6b7280;
            margin-bottom: 2rem;
        }
        .form-group {
            text-align: left;
            margin-bottom: 1.25rem;
        }
        .form-group label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.5rem;
        }
        .form-group input {
            width: 100%;
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            box-sizing: border-box; 
        }
        .btn-login {
            width: 100%;
            padding: 0.8rem 1rem;
            font-size: 0.9rem;
            font-weight: 600;
            color: #ffffff;
            background-color: #2563eb; 
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .btn-login:hover {
            background-color: #1d4ed8;
        }
        .error-message {
            color: #dc2626; 
            font-size: 0.875rem;
            text-align: left;
            margin-top: 0.5rem;
        }
        .password-wrapper {
            position: relative;
        }
        #password {
             padding-right: 3rem; 
        }
        .password-toggle-icon {
            position: absolute;
            top: 50%;
            right: 1rem;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6b7280; 
            width: 20px;
            height: 20px;
        }
    </style>
</head>
<body>
    
    <div class="login-card">
        <div class="login-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 24px; height: 24px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
            </svg>              
        </div>
        
        <h1>Sistem Informasi Keuangan</h1>
        <p>Pesantren Al Kautsar 561</p>

        <form action="{{ route('login.process') }}" method="POST">
            @csrf 

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                
                <div class="password-wrapper">
                    <input type="password" id="password" name="password" required>
                    
                    <span id="password-toggle" class="password-toggle-icon">
                        
                        <svg id="icon-eye" style="display: none;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        
                        <svg id="icon-eye-slash" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>                          
                    </span>
                </div>

            </div>

            <button type="submit" class="btn-login">Masuk</button>
        </form>
    </div>


    <script>
        const toggleButton = document.getElementById('password-toggle');
        const passwordInput = document.getElementById('password');
        const iconEye = document.getElementById('icon-eye');
        const iconEyeSlash = document.getElementById('icon-eye-slash');

        toggleButton.addEventListener('click', function() {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                iconEye.style.display = 'block';
                iconEyeSlash.style.display = 'none';
            } else {
                passwordInput.type = 'password';
                iconEye.style.display = 'none';
                iconEyeSlash.style.display = 'block';
            }
        });
    </script>

</body>
</html>