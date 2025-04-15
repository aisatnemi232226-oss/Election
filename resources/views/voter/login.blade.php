<html>
<head>
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
        <h2 class="text-3xl font-bold text-gray-800 mb-6 text-center">University Online Election System</h2>
        <form action="{{route('login')}}" method="POST">
            @csrf
            <div class="mb-4 flex items-center">
                <div class="flex-grow">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="username">Username</label>
                    <div class="relative">
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="username" name="email" type="text" placeholder="Username">
                        <button class="absolute inset-y-0 right-0 flex items-center bg-green-600 text-white p-2 rounded-r-lg hover:bg-green-700" type="button" id="scan-qr-button">
                            <i class="fas fa-qrcode"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">Password</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="password" name="password" type="password" placeholder="Password">
            </div>
            <div class="flex items-center justify-between mb-4">
                <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700" type="submit">Login</button>
                <a class="inline-block align-baseline font-bold text-sm text-blue-600 hover:text-blue-800" href="{{route('password.request') }}">Forgot Password?</a>
            </div>
            <div class="text-center">
                <a class="inline-block align-baseline font-bold text-sm text-blue-600 hover:text-blue-800" href="{{url('/register') }}">Create Account</a>
            </div>
        </form>
        <div id="qr-reader" class="mt-4" style="display: none;"></div>
    </div>
    <script src="https://unpkg.com/html5-qrcode@latest/html5-qrcode.min.js"></script>
    <script>
        document.getElementById('scan-qr-button').addEventListener('click', function() {
            const qrReader = document.getElementById('qr-reader');
            qrReader.style.display = 'block';
            const html5QrCode = new Html5Qrcode("qr-reader");
            html5QrCode.start(
                { facingMode: "environment" },
                {
                    fps: 10,
                    qrbox: 250
                },
                qrCodeMessage => {
                    alert(`QR Code detected: ${qrCodeMessage}`);
                    html5QrCode.stop().then(ignore => {
                        qrReader.style.display = 'none';
                    }).catch(err => {
                        console.error(err);
                    });
                },
                errorMessage => {
                    console.warn(`QR Code no longer in front of camera.`);
                }
            ).catch(err => {
                console.error(err);
            });
        });
    </script>
</body>
</html>