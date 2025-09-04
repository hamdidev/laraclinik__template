<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin dashboard</title>
</head>

<body>
    <h1>admin dashboard</h1>
    <form method="POST" action="{{ route('admin.logout') }}">
        @csrf


        <button
            onclick="event.preventDefault();
                                                    this.closest('form').submit();"
            style="cursor: pointer;">logout</button>
    </form>

</body>

</html>
