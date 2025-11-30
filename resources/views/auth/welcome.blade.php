@extends('layouts.guest')

@section('title', 'Selamat Datang')

@section('content')
    <a href="{{ route('public.landing') }}" class="back-link" title="Kembali ke Beranda">
        <i class="bi bi-arrow-left"></i>
    </a>

    <div class="auth-screen" id="welcome-screen">
        <div class="auth-content">
            <i class="bi bi-flower1 logo"></i> 
            
            <h1>WELCOME</h1>
            <p class="subtitle">Smart Masjid Al-Jabbar Bandung</p>
            
            </div>
        
        <a href="{{ route('login') }}" class="btn btn-primary" style="margin-bottom: 0.5rem;">Sign In With Email</a>
        
        <footer class="auth-footer">
            <p>Don't have an account? <a href="{{ route('register') }}" class="link">Sign Up</a></p>
        </footer>
    </div>
@endsection