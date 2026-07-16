<?php
session_start();

// Configure secure session cookies before any output
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'secure'   => true,
    'httponly' => true,
    'samesite' => 'Lax',
]);

// Generate / refresh CSRF token for this session
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Rate limiting configuration
const REGISTRATION_MAX_ATTEMPTS = 5;             // max attempts allowed
const REGISTRATION_WINDOW_SECONDS = 60;          // per-minute window
const REGISTRATION_LOCKOUT_SECONDS = 300;        // 5-minute lockout when exceeded

$errors = [];

// Ensure correct path resolution for the database config
require_once __DIR__ . '/../../config/database.php';

// Verify $conn provided by database.php
if (!isset($conn) || !$conn) {
    // Stop execution with a clear message to avoid undefined variable usage
    die('Database connection not found. Please check config/database.php and ensure it defines $conn.');
}

/**
 * Return the client's IP address, preferring REMOTE_ADDR (set by the web server).
 * X-Forwarded-For is intentionally NOT trusted by default; behind a proxy,
 * configure your reverse proxy to overwrite REMOTE_ADDR with a verified value.
 */
function getClientIp(): string
{
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

/**
 * Check the IP-based registration rate limit. Returns null if allowed,
 * or an error message string if the client is over the limit / locked out.
 */
function checkRegistrationRateLimit(string $ip): ?string
{
    $now = time();
    $state = $_SESSION['reg_rate'][$ip] ?? null;

    // Active lockout?
    if (is_array($state) && isset($state['lockout_until']) && $state['lockout_until'] > $now) {
        $remaining = $state['lockout_until'] - $now;
        return "Too many registration attempts. Please try again in {$remaining} seconds.";
    }

    // Initialize / slide the window
    if (!is_array($state) || ($now - ($state['window_start'] ?? 0)) >= REGISTRATION_WINDOW_SECONDS) {
        $state = ['window_start' => $now, 'count' => 0, 'lockout_until' => 0];
    }

    $state['count']++;
    if ($state['count'] > REGISTRATION_MAX_ATTEMPTS) {
        $state['lockout_until'] = $now + REGISTRATION_LOCKOUT_SECONDS;
        $state['count']         = 0;
        $state['window_start']  = $now;
        $_SESSION['reg_rate'][$ip] = $state;
        return "Too many registration attempts. Please try again in " . REGISTRATION_LOCKOUT_SECONDS . " seconds.";
    }

    $_SESSION['reg_rate'][$ip] = $state;
    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Validate CSRF token before doing anything else
    $submittedToken = $_POST['csrf_token'] ?? '';
    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $submittedToken)) {
        $errors[] = "Invalid request. Please reload the page and try again.";
    }

    // Enforce IP-based rate limiting on the registration endpoint
    $clientIp = getClientIp();
    $rateLimitError = checkRegistrationRateLimit($clientIp);
    if ($rateLimitError !== null) {
        $errors[] = $rateLimitError;
    }

    // Get input
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = 'user'; // Set default role

    // Validation
    if (empty($full_name)) {
        $errors[] = "Full name is required";
    }

    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    if (empty($phone)) {
        $errors[] = "Phone number is required";
    } elseif (!preg_match('/^[0-9]{9,15}$/', $phone)) {
        $errors[] = "Invalid phone number format";
    }

    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters";
    } elseif (strlen($password) > 128) {
        $errors[] = "Password must be no more than 128 characters";
    } elseif (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must contain at least one letter and one number";
    }

    if (empty($confirm_password)) {
        $errors[] = "Please confirm your password";
    } elseif (!hash_equals($password, $confirm_password)) {
        $errors[] = "Passwords do not match";
    }

    if (empty($errors)) {

        //Check if user exists (MYSQLi)
        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ? OR phone = ?");
        mysqli_stmt_bind_param($stmt, "ss", $email, $phone);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        $isDuplicate = (bool) mysqli_fetch_assoc($result);

        // Always perform a password_hash, even on the duplicate branch, so the
        // response timing is identical and an attacker cannot enumerate accounts
        // by measuring how long the server takes to respond.
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        if ($isDuplicate) {
            $errors[] = "If that account is new, please check your email to confirm registration.";
        } else {

            //Insert user
            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO users (full_name, email, phone, role, password) VALUES (?, ?, ?, ?, ?)"
            );

            mysqli_stmt_bind_param($stmt, "sssss", $full_name, $email, $phone, $role, $hashedPassword);
            mysqli_stmt_execute($stmt);

            //Get ID
            $user_id = mysqli_insert_id($conn);

            //Login user
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user_id;
            $_SESSION['full_name'] = $full_name;
            $_SESSION['email'] = $email;
            $_SESSION['phone'] = $phone;
            $_SESSION['role'] = 'user';

            //Redirect
            header("Location: ../../Dashboard/Home.php");
            exit;
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

<body class="min-h-screen bg-linear-to-br from-slate-950 via-blue-950 to-slate-900 flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-white">
                <i class="fas fa-wallet text-green-400"></i>
                Smart<span class="text-blue-400">Finance</span>
            </h1>
            <p class="text-gray-300 mt-2">Create your account</p>
        </div>

        <!-- Glassmorphism Form -->
        <div class="glassmorphism-dark rounded-3xl p-8 shadow-2xl">
            <form action="#" method="POST" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

                <!-- Validation Errors -->
                <?php if (!empty($errors)): ?>
                    <div id="form-errors" role="alert" aria-live="polite"
                        class="bg-red-500/20 border border-red-400/40 text-red-100 rounded-xl p-4 space-y-1">
                        <p class="font-semibold text-red-50">Please fix the following:</p>
                        <ul class="list-disc list-inside text-sm">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- Full Name -->
                <div>
                    <label class="block text-white text-sm font-medium mb-2">Full Name</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <input type="text" name="full_name" required value="<?php echo htmlspecialchars($full_name ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                            class="w-full pl-10 pr-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            placeholder="Enter your full name">
                    </div>
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-white text-sm font-medium mb-2">Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                            </svg>
                        </div>
                        <input type="email" name="email" required value="<?php echo htmlspecialchars($email ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                            class="w-full pl-10 pr-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            placeholder="Enter your email">
                    </div>
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-white text-sm font-medium mb-2">Phone</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                        </div>
                        <input type="tel" name="phone" required value="<?php echo htmlspecialchars($phone ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                            class="w-full pl-10 pr-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            placeholder="Enter your phone number">
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-white text-sm font-medium mb-2">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <input type="password" name="password" required
                            class="w-full pl-10 pr-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            placeholder="Create a password">
                    </div>
                </div>
                <div>
                    <label class="block text-white text-sm font-medium mb-2">Confirm Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <input type="password" name="confirm_password" required
                            class="w-full pl-10 pr-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            placeholder="Confirm your password">
                    </div>
                </div>


                <!-- Submit Button -->
                <button type="submit"
                    class="w-full bg-blue-500 hover:bg-blue-600 transition text-white font-medium py-3 rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                    Create Account
                </button>

            </form>

            <!-- Login Link -->
            <div class="mt-6 text-center">
                <p class="text-gray-300">
                    Already have an account?
                    <a href="login.php" class="text-blue-400 hover:text-blue-300 font-medium transition">
                        Login here
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