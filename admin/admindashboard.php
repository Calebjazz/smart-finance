<?php
require_once __DIR__ . '/../includes/init.php';
require_admin();

global $conn;

$admin_name = htmlspecialchars($_SESSION['full_name'] ?? 'Admin');
$stats = get_admin_stats($conn);
$activities = get_admin_recent_activity($conn);

$labels = [];
$user_growth = [];
for ($i = 5; $i >= 0; $i--) {
    $d = new DateTime("first day of -{$i} months");
    $y = $d->format('Y');
    $m = $d->format('m');
    $labels[] = $d->format('M');
    $r = mysqli_query($conn, "SELECT COUNT(*) AS c FROM users WHERE YEAR(created_at)=$y AND MONTH(created_at)=$m");
    $user_growth[] = (int) mysqli_fetch_assoc($r)['c'];
}

$tx_volume = [];
for ($i = 5; $i >= 0; $i--) {
    $d = new DateTime("first day of -{$i} months");
    $y = $d->format('Y');
    $m = $d->format('m');
    $c = 0;
    foreach (['incomes', 'expenses', 'savings_transactions'] as $tbl) {
        $dateCol = $tbl === 'savings_transactions' ? 'transaction_date' : ($tbl === 'incomes' ? 'income_date' : 'expense_date');
        $r = mysqli_query($conn, "SELECT COUNT(*) AS c FROM $tbl WHERE YEAR($dateCol)=$y AND MONTH($dateCol)=$m");
        $c += (int) mysqli_fetch_assoc($r)['c'];
    }
    $tx_volume[] = $c;
}

$page_title = 'Admin Dashboard';
$active_page = 'dashboard';
$include_chart = true;
$asset_path = '../assets';

include __DIR__ . '/../includes/head.php';
include __DIR__ . '/../includes/admin_sidebar.php';
include __DIR__ . '/../includes/admin_navbar.php';
?>

<div class="p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold mb-2 card-title">Admin Dashboard</h1>
        <p class="card-text">System overview and management</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="card rounded-2xl p-6"><p class="text-sm card-text">Total Users</p><p class="text-2xl font-bold card-title"><?php echo $stats['users']; ?></p></div>
        <div class="card rounded-2xl p-6"><p class="text-sm card-text">Total Transactions</p><p class="text-2xl font-bold card-title"><?php echo $stats['transactions']; ?></p></div>
        <div class="card rounded-2xl p-6"><p class="text-sm card-text">AI Requests</p><p class="text-2xl font-bold card-title"><?php echo $stats['ai_requests']; ?></p></div>
        <div class="card rounded-2xl p-6"><p class="text-sm card-text">Automation Logs</p><p class="text-2xl font-bold card-title"><?php echo $stats['automations']; ?></p></div>
    </div>

    <!-- <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="card rounded-2xl p-6">
            <h3 class="text-lg font-semibold mb-4 card-title">User Growth</h3>
            <div class="chart-container"><canvas id="userGrowthChart"></canvas></div>
        </div>
        <div class="card rounded-2xl p-6">
            <h3 class="text-lg font-semibold mb-4 card-title">Transaction Volume</h3>
            <div class="chart-container"><canvas id="transactionChart"></canvas></div>
        </div>
    </div> -->

    <div class="card rounded-2xl p-6">
        <h3 class="text-lg font-semibold mb-6 card-title">Recent Activity</h3>
        <div class="space-y-4">
            <?php if (empty($activities)): ?>
            <p class="card-text">No recent activity.</p>
            <?php else: foreach ($activities as $a):
                $icons = ['user' => 'fa-user-plus text-blue-500', 'ai' => 'fa-robot text-green-500', 'automation' => 'fa-cog text-yellow-500'];
                $icon = $icons[$a['type']] ?? 'fa-info text-gray-500';
            ?>
            <div class="flex items-center gap-4 p-4 sub-card rounded-xl">
                <div class="w-10 h-10 rounded-full bg-blue-500/20 flex items-center justify-center"><i class="fas <?php echo $icon; ?>"></i></div>
                <div class="flex-1"><p class="font-medium card-title"><?php echo htmlspecialchars($a['title']); ?></p><p class="text-sm card-text"><?php echo htmlspecialchars($a['message']); ?></p></div>
                <span class="text-sm card-text"><?php echo time_ago($a['time']); ?></span>
            </div>
            <?php endforeach; endif; ?>
        </div>
    </div>
</div>

<?php
$page_scripts = '<script>
sfRegisterChart(new Chart(document.getElementById("userGrowthChart"), { type:"line", data:{ labels:' . json_encode($labels) . ', datasets:[{label:"New Users",data:' . json_encode($user_growth) . ',borderColor:"#3b82f6",fill:true,tension:0.4}]}, options:sfChartOptions() }));
sfRegisterChart(new Chart(document.getElementById("transactionChart"), { type:"bar", data:{ labels:' . json_encode($labels) . ', datasets:[{label:"Transactions",data:' . json_encode($tx_volume) . ',backgroundColor:"#10b981"}]}, options:sfChartOptions() }));
</script>';

