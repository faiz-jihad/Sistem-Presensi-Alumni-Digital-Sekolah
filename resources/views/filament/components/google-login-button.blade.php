<div class="mt-4 flex flex-col items-center custom-auth-google-container" style="width: 100%;">
    <div class="relative flex py-2 items-center w-full custom-auth-divider">
        <div class="flex-grow border-t border-gray-300 dark:border-slate-700 custom-auth-divider-line"></div>
        <span class="flex-shrink mx-4 text-gray-400 dark:text-gray-500 text-xs uppercase tracking-wider font-bold custom-auth-divider-text">atau masuk dengan</span>
        <div class="flex-grow border-t border-gray-300 dark:border-slate-700 custom-auth-divider-line"></div>
    </div>

    <a
        href="{{ route('google.redirect') }}"
        id="google-login-btn"
        style="
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            width: 100%;
            padding: 0.625rem 1rem;
            margin-top: 0.5rem;
            border: 1px solid #d1d5db;
            border-radius: 0.75rem;
            background-color: #ffffff;
            color: #374151;
            font-weight: 600;
            font-size: 0.875rem;
            text-decoration: none;
            box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05);
            cursor: pointer;
            transition: background-color 0.2s ease, border-color 0.2s ease;
            font-family: 'Plus Jakarta Sans', sans-serif;
        "
        onmouseover="this.style.backgroundColor='#f9fafb';this.style.borderColor='#9ca3af';"
        onmouseout="this.style.backgroundColor='#ffffff';this.style.borderColor='#d1d5db';"
    >
        <svg style="width:1.25rem;height:1.25rem;flex-shrink:0;" viewBox="0 0 24 24">
            <path fill="#EA4335" d="M12 5.04c1.66 0 3.2.57 4.38 1.69l3.27-3.27C17.68 1.54 14.98 1 12 1 7.35 1 3.37 3.67 1.39 7.56l3.89 3.02C6.22 7.78 8.89 5.04 12 5.04z"/>
            <path fill="#4285F4" d="M23.49 12.27c0-.81-.07-1.59-.2-2.36H12v4.51h6.46c-.29 1.48-1.14 2.73-2.4 3.58l3.72 2.88c2.18-2 3.71-4.96 3.71-8.61z"/>
            <path fill="#FBBC05" d="M5.28 14.78c-.24-.72-.38-1.49-.38-2.28s.14-1.56.38-2.28L1.39 7.2C.5 8.99 0 10.99 0 13s.5 4.01 1.39 5.8l3.89-3.02z"/>
            <path fill="#34A853" d="M12 23c3.24 0 5.97-1.07 7.96-2.91l-3.72-2.88c-1.04.7-2.38 1.12-4.24 1.12-3.11 0-5.78-2.74-6.72-5.54l-3.89 3.02C3.37 20.33 7.35 23 12 23z"/>
        </svg>
        <span>Masuk dengan Google</span>
    </a>
</div>

@if(session()->has('errors') && session('errors')->has('google'))
<div style="
    margin-top: 0.75rem;
    padding: 0.75rem 1rem;
    background-color: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 0.5rem;
    color: #dc2626;
    font-size: 0.875rem;
    text-align: center;
">
    {{ session('errors')->first('google') }}
</div>
@endif
