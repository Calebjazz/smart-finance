<?php
session_start();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $full_name = $_POST['full_name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $country_code = $_POST['country_code'] ?? '+255';
    
    
    // In production, you would validate credentials against database
    $_SESSION['user_id'] = 1;
    $_SESSION['full_name'] = $full_name;
    $_SESSION['phone'] = $country_code . $phone;
    
    // Redirect to dashboard
    header('Location: ../../Dashboard/dashboard.php');
    exit();

    /*validation
    if($user['role']=='admin'){
    header("Location: ../admin/dashboard.php");
}else{
    header("Location: ../user/dashboard.php");
}
    */
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        .glassmorphism {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .glassmorphism-dark {
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>

<body class="min-h-screen bg-gradient-to-br from-slate-950 via-blue-950 to-slate-900 flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-white">
                Smart<span class="text-blue-400">Finance</span>
            </h1>
            <p class="text-gray-300 mt-2">Welcome back</p>
        </div>

        <!-- Glassmorphism Form -->
        <div class="glassmorphism-dark rounded-3xl p-8 shadow-2xl">
            <form action="#" method="POST" class="space-y-6">
                
                <!-- Full Name -->
                <div>
                    <label class="block text-white text-sm font-medium mb-2">Full Name</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <input type="text" name="full_name" required
                            class="w-full pl-10 pr-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            placeholder="Enter your full name">
                    </div>
                </div>

                <!-- Phone International feature -->
                <div>
                    <label class="block text-white text-sm font-medium mb-2">Phone Number</label>
                    <div class="flex gap-2">
                        <div class="relative w-24">
                            <select name="country_code" 
                                class="w-full h-full pl-3 pr-2 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition appearance-none cursor-pointer">
                                <option value="+255" class="bg-slate-900">+255</option>
                                <option value="+1" class="bg-slate-900">+1</option>
                                <option value="+91" class="bg-slate-900">+91</option>
                                <option value="+254" class="bg-slate-900">+254</option>
                                <option value="+253" class="bg-slate-900">+253</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </div>
                            <input type="tel" name="phone" required
                                class="w-full pl-10 pr-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                placeholder="Phone number">
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full bg-blue-500 hover:bg-blue-600 transition text-white font-medium py-3 rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                    Sign In
                </button>

            </form>

            <!-- Divider -->
            <div class="flex items-center my-6">
                <div class="flex-1 border-t border-white/20"></div>
                <span class="px-4 text-gray-400 text-sm">or</span>
                <div class="flex-1 border-t border-white/20"></div>
            </div>

            <!-- Sign in with Email -->
            <div>
                <a href="#" class="w-full flex items-center justify-center gap-3 bg-white/10 hover:bg-white/20 transition text-white font-medium py-3 rounded-xl border border-white/20 hover:border-white/30 transition-all duration-200">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                    </svg>
                    Sign in with Email
                </a>
            </div>

            <!-- Google Sign In -->
            <div class="mt-4">
                <a href="#" class="w-full flex items-center justify-center gap-3 bg-white hover:bg-gray-100 transition text-gray-800 font-medium py-3 rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                    <svg class="w-5 h-5" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    Sign in with Google
                </a>
            </div>

            <!-- Register Link -->
            <div class="mt-6 text-center">
                <p class="text-gray-300">
                    Don't have an account? 
                    <a href="register.php" class="text-blue-400 hover:text-blue-300 font-medium transition">
                        Register here
                    </a>
                </p>
            </div>
        </div>

        <!-- Back to Home -->
        <div class="mt-6 text-center">
            <a href="../index.php" class="text-gray-400 hover:text-white transition">
                ← Back to Home
            </a>
        </div>

    </div>

</body>
</html>
