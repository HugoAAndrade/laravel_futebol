<!DOCTYPE html>
<html>

<head>
    <title>Laravel</title>
    @vite('resources/css/app.css')
</head>

<body>
    <h1>Welcome to the Futebol Application</h1>
    <a href="{{ route('players.index') }}">View Players</a>
</body>

</html>
