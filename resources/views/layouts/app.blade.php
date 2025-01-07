<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wallet App</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.js"></script>
    <style>
        #sidebar {
            transition: all 0.3s;
        }
        #sidebar.collapsed {
            width: 0;
            overflow: hidden;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('wallet.show') }}">Charles Uwaje WalletApp Test Project</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @auth
                        <li class="nav-item"><span class="nav-link">Hi, {{ auth()->user()->name }}</span></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('wallet.show') }}">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('wallet.withdraw.form') }}">Withdraw</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('wallet.transfer.form') }}">Transfer</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('chat.index') }}">Customer Care</a></li>
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="btn btn-link nav-link" type="submit">Logout</button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Register</a></li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content with Sidebar -->
    <div class="container-fluid mt-4">
        <div class="row">
            <!-- Sidebar -->
            <div id="sidebar" class="col-md-3 col-lg-2 bg-light p-3 border-end">
                <button id="toggleSidebar" class="btn btn-primary mb-3">
                    <i class="bi bi-list"></i> Toggle Sidebar
                </button>
                <h5 class="mb-3">Features</h5>
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link" href="{{ route('wallet.show') }}"><i class="bi bi-house"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('wallet.withdraw.form') }}"><i class="bi bi-wallet"></i> Withdraw</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('wallet.transfer.form') }}"><i class="bi bi-arrow-left-right"></i> Transfer</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('chat.index') }}"><i class="bi bi-chat-dots"></i> Customer Care</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                </ul>
            </div>

            <!-- Main Content -->
            <div id="mainContent" class="col-md-9 col-lg-10">
                @yield('content')
            </div>
        </div>
    </div>

    <script>
        document.getElementById('toggleSidebar').addEventListener('click', function () {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            sidebar.classList.toggle('collapsed');
        });
    </script>
</body>
</html>
