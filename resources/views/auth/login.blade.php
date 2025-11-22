<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            height: 100vh;
            background-color: #f5f5f5;
        }
        .container {
            display: flex;
            width: 100%;
            max-width: 800px;
            margin: auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        .left {
    flex: 1;
    background-image: url('/images/logo.png'); /* Correct image path */
    background-size: cover; /* Ensures the image covers the entire div */
    background-position: center; /* Centers the image */
    background-repeat: no-repeat; /* Prevents repeating */
    color: white;
    padding: 40px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
   
}
        .right {
            flex: 1;
            padding: 40px;
        }
        h2 {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            font-size: 16px;
        }
        button:hover {
            background-color: #45a049;
        }
        .footer {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            font-size: 14px;
        }
        .social-icons {
            margin-top: 15px;
        }
        .social-icons a {
            margin: 0 5px;
            text-decoration: none;
            color: #4CAF50;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="left">
    
   
    </div>
    <div class="right">
        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="emailaddress">
                    Email Address <span class="text-red-500">*</span>
                </label>
                <input class="shadow appearance-none border @error('email') border-red-500 @enderror rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline" 
                    type="email" name="email" id="emailaddress" placeholder="email@example.com">
                @error('email')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                    Password <span class="text-red-500">*</span>
                </label>
                <input class="shadow appearance-none border @error('password') border-red-500 @enderror rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline" 
                    type="password" name="password" id="password" placeholder="******************">
                @error('password')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit">Login</button>
            <div class="footer">
                <div class="flex items-center justify-between mb-4">
                    <label class="flex items-center text-gray-500 font-bold">
                        <input class="mr-2 leading-tight" type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span class="text-sm">Remember Me</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="text-blue-500">Forgot Password?</a>
                </div>
            </div>
        
        </form>
    </div>
</div>

</body>
</html>