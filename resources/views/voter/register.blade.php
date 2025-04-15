<html>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>University Online Election System - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet"/>
    <script src="https://unpkg.com/html5-qrcode/minified/html5-qrcode.min.js"></script>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
    </style>
</head>
<body class="bg-[url('{{ asset('images/background1.jpg') }}')] bg-bottom bg-cover text-center text-white flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-3xl font-bold text-gray-800 mb-6 text-center">University Online Election System - Registration</h2>
        <form action="{{route('register')}}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="idnumber">Student Number</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="idnumber" name="idnumber" type="text" placeholder="Student Number">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="fullname">Full Name</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="fullname" name="name" type="text" placeholder="Full Name">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="email" name="email" type="email" placeholder="Email">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">Password</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="password" name="password" type="password" placeholder="Password">
            </div>
            <div class="flex items-center justify-between mb-4">
                <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 w-full" type="submit">Register</button>
            </div>
            <div class="text-center">
                <p class="text-gray-700 text-sm">Already have an account? <a class="font-bold text-blue-600 hover:text-blue-800" href="{{ url('/login ') }}">Login</a></p>
            </div>
        </form>
    </div>
</body>

</html>