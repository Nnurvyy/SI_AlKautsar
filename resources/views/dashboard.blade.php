<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        body { font-family: sans-serif; padding: 2rem; }
        .logout-btn {
            background-color: #dc2626;
            color: white;
            padding: 0.5rem 1rem;
            text-decoration: none;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <h1>Selamat Datang di Dashboard!</h1>
    
    <p>Halo, <strong>{{ Auth::user()->name }}</strong> ({{ Auth::user()->email }})</p>

    <p>Ini adalah halaman yang dilindungi. Hanya user yang sudah login yang bisa mengakses ini.</p>
    <br>

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="logout-btn">Logout</button>
    </form>

</body>
</html>