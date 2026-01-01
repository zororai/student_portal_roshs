<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Successful - RSH School</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta http-equiv="refresh" content="5;url=/login">
</head>
<body class="bg-gradient-to-br from-green-50 via-white to-blue-50 min-h-screen flex items-center justify-center">
    <div class="container mx-auto px-4">
        <div class="max-w-lg mx-auto text-center">
            <!-- Success Icon -->
            <div class="mb-8">
                <div class="w-32 h-32 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center mx-auto shadow-2xl animate-pulse">
                    <i class="fas fa-check text-white text-6xl"></i>
                </div>
            </div>

            <!-- Success Message -->
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-6">
                <h1 class="text-3xl font-bold text-gray-800 mb-4">Registration Successful!</h1>
                <p class="text-lg text-gray-600 mb-6">
                    Your parent account has been created successfully. You can now login to the portal using your email and password.
                </p>
                
                <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle text-green-500 mr-3"></i>
                        <p class="text-green-700">
                            You will be redirected to the login page in <span id="countdown" class="font-bold">5</span> seconds...
                        </p>
                    </div>
                </div>

                <a href="/login" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-bold rounded-lg shadow-lg hover:shadow-xl transition duration-200">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Go to Login Now
                </a>
            </div>

            <!-- Footer -->
            <p class="text-sm text-gray-500">© {{ date('Y') }} RSH School. All rights reserved.</p>
        </div>
    </div>

    <script>
        // Countdown timer
        let seconds = 5;
        const countdownEl = document.getElementById('countdown');
        
        const interval = setInterval(function() {
            seconds--;
            countdownEl.textContent = seconds;
            
            if (seconds <= 0) {
                clearInterval(interval);
                window.location.href = '/login';
            }
        }, 1000);
    </script>
</body>
</html>
