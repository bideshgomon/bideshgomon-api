<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>@yield('title', config('app.name', 'BideshGomon'))</title>

    <link rel="icon" type="image/png" href="{{ asset('img/Fev Icon.png') }}">

    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
</head>
<body style="display: flex; flex-direction: column; min-height: 100vh;">

    <header class="navbar">
        <div class="container" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
            
            <a href="/">
                <img src="{{ asset('img/bideshgomonlogo.png') }}" alt="BideshGomon Logo" class="navbar-logo-img">
            </a>

            <nav>
                @guest
                    <a href="{{ route('login') }}" class="btn btn-outline" style="margin-left: 1rem;">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-primary" style="margin-left: 0.5rem;">Get Started</a>
                @endguest
                
                @auth
                    <a href="#">Dashboard</a>
                    
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-outline" style="margin-left: 1rem;">
                        Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                @endauth
            </nav>
        </div>
    </header>

    <main style="flex-grow: 1;">
        @yield('content')
    </main>

    <footer class="footer">
        <div class="footer-container">
            
            <div class="footer-section">
                <a href="/">
                    <img src="{{ asset('img/bideshgomonlogo.png') }}" alt="BideshGomon Logo" class="footer-logo-img">
                </a>
                <p>
                    Simplifying your journey for migration, overseas education, and travel with AI-driven guidance.
                </p>
            </div>

            <div class="footer-section">
                <h4>Core Services</h4>
                <ul>
                    <li><a href="#">Work Visa</a></li>
                    <li><a href="#">Student Visa</a></li>
                    <li><a href="#">Tourist Visa</a></li>
                    <li><a href="#">AI Profile Analysis</a></li>
                    <li><a href="#">CV Builder</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h4>Company</h4>
                <ul>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Contact Us</a></li>
                    <li><a href="#">Our Agencies</a></li>
                    <li><a href="#">Careers</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h4>Legal</h4>
                <ul>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Terms of Service</a></li>
                </ul>
                
                <h4 style="margin-top: 1.5rem;">Follow Us</h4>
                <div class="footer-socials">
                    <a href="#">FB</a>
                    <a href="#">IN</a>
                    <a href="#">TW</a>
                    <a href="#">YT</a>
                </div>
            </div>

        </div>
        
        <div class="footer-bottom">
            © {{ date('Y') }} {{ config('app.name', 'BideshGomon') }}. All rights reserved.
        </div>
    </footer>

</body>
</html>