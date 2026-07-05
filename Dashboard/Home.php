<?php
require_once __DIR__ . '/../includes/init.php';
if (!isset($conn) || !$conn instanceof mysqli) {
    throw new RuntimeException('Database connection not initialized');
}
require_login();

$user_id = (int) $_SESSION['user_id'];
$user_name = htmlspecialchars($_SESSION['full_name'] ?? 'User');
refresh_user_session($conn, $user_id);

$total_income = get_total_income($conn, $user_id);
$total_expenses = get_total_expenses($conn, $user_id);
$total_balance = get_balance($conn, $user_id);

$monthly = get_monthly_income_expenses($conn, $user_id);
$category_data = get_expenses_by_category($conn, $user_id);
$recent_tx = get_recent_transactions($conn, $user_id, 5);

$budget_total = get_total_income($conn, $user_id);
$needs = $budget_total * 0.5;
$wants = $budget_total * 0.3;
$savings_alloc = $budget_total * 0.2;

$page_title = 'Dashboard';
$active_page = 'home';
$include_chart = true;
$include_advisor = true;
$dash_path = '';
$user_path = '../user/';

include __DIR__ . '/../includes/head.php';
include __DIR__ . '/../includes/user_sidebar.php';
include __DIR__ . '/../includes/user_navbar.php';
?>

<div class="p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold mb-2 card-title">Hi, <?php echo $user_name; ?></h1>
        <p class="card-text">Welcome to your Smart Finance Dashboard. Here is your financial overview.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="stat-card card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 gradient-green rounded-xl flex items-center justify-center"><i class="fas fa-wallet text-white text-xl"></i></div>
            </div>
            <p class="text-sm mb-1 card-text">Total Balance</p>
            <p class="text-2xl font-bold card-title"><?php echo format_tsh($total_balance); ?></p>
        </div>
        <div class="stat-card card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 gradient-green rounded-xl flex items-center justify-center"><i class="fas fa-arrow-trend-up text-white text-xl"></i></div>
            </div>
            <p class="text-sm mb-1 card-text">Total Income</p>
            <p class="text-2xl font-bold card-title"><?php echo format_tsh($total_income); ?></p>
        </div>
        <div class="stat-card card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 gradient-green rounded-xl flex items-center justify-center"><i class="fas fa-arrow-trend-down text-white text-xl"></i></div>
            </div>
            <p class="text-sm mb-1 card-text">Total Expenses</p>
            <p class="text-2xl font-bold card-title"><?php echo format_tsh($total_expenses); ?></p>
        </div>
        
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="card rounded-2xl p-6">
            <h3 class="text-lg font-semibold mb-4 card-title">Income vs Expenses</h3>
            <div class="chart-container"><canvas id="incomeExpenseChart"></canvas></div>
        </div>
        <div class="card rounded-2xl p-6">
            <h3 class="text-lg font-semibold mb-4 card-title">Expenses by Category</h3>
            <div class="chart-container"><canvas id="categoryChart"></canvas></div>
        </div>
    </div>

    <!-- <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="card rounded-2xl p-6 lg:col-span-2">
            <h3 class="text-lg font-semibold mb-4 card-title">Monthly Spending</h3>
            <div class="chart-container"><canvas id="monthlySpendingChart"></canvas></div>
        </div>
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold card-title">Recent Transactions</h3>
                <a href="transactions.php" class="text-blue-500 text-sm hover:underline">View All</a>
            </div>
            <div class="space-y-4">
                <?php if (empty($recent_tx)): ?>
                <p class="text-sm card-text">No transactions yet. Add income or expenses to get started.</p>
                <?php else: foreach ($recent_tx as $tx):
                    $isIncome = str_contains($tx['type'], 'income') || str_contains($tx['type'], 'deposit');
                ?>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center <?php echo $isIncome ? 'bg-green-500/20' : 'bg-red-500/20'; ?>">
                        <i class="fas <?php echo $isIncome ? 'fa-arrow-down text-green-500' : 'fa-arrow-up text-red-500'; ?>"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate card-title"><?php echo htmlspecialchars($tx['label'] ?: 'Transaction'); ?></p>
                        <p class="text-xs card-text"><?php echo date('M j, Y', strtotime($tx['tx_date'])); ?></p>
                    </div>
                    <p class="font-medium <?php echo $isIncome ? 'text-green-500' : 'text-red-500'; ?>"><?php echo ($isIncome ? '+' : '-') . format_tsh((float)$tx['amount']); ?></p>
                </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </div> -->

    <?php if ($budget_total > 0): ?>
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold card-title">Budget Overview</h3>
            <a href="Budget.php" class="text-blue-500 text-sm hover:underline">Manage Budget</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="sub-card rounded-xl p-4">
                <div class="flex items-center justify-between mb-2"><span class="text-sm card-text">Needs (50%)</span><span class="font-medium card-title"><?php echo format_tsh($needs); ?></span></div>
                <div class="w-full bg-gray-300 rounded-full h-2 dark:bg-slate-700"><div class="bg-blue-500 h-2 rounded-full" style="width:50%"></div></div>
            </div>
            <div class="sub-card rounded-xl p-4">
                <div class="flex items-center justify-between mb-2"><span class="text-sm card-text">Wants (30%)</span><span class="font-medium card-title"><?php echo format_tsh($wants); ?></span></div>
                <div class="w-full bg-gray-300 rounded-full h-2 dark:bg-slate-700"><div class="bg-purple-500 h-2 rounded-full" style="width:30%"></div></div>
            </div>
            <div class="sub-card rounded-xl p-4">
                <div class="flex items-center justify-between mb-2"><span class="text-sm card-text">Savings (20%)</span><span class="font-medium card-title"><?php echo format_tsh($savings_alloc); ?></span></div>
                <div class="w-full bg-gray-300 rounded-full h-2 dark:bg-slate-700"><div class="bg-green-500 h-2 rounded-full" style="width:20%"></div></div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php
$catLabels = array_column($category_data, 'category_name');
$catValues = array_map(fn($r) => (float)$r['total'], $category_data);
$page_scripts = '<script>
const monthlyLabels = ' . json_encode($monthly['labels']) . ';
const monthlyIncome = ' . json_encode($monthly['income']) . ';
const monthlyExpenses = ' . json_encode($monthly['expenses']) . ';
const catLabels = ' . json_encode($catLabels) . ';
const catValues = ' . json_encode($catValues) . ';

const c1 = new Chart(document.getElementById("incomeExpenseChart"), {
    type: "line",
    data: {
        labels: monthlyLabels,
        datasets: [
            { label: "Income", data: monthlyIncome, borderColor: "#10b981", backgroundColor: "rgba(16,185,129,0.15)", fill: true, tension: 0.4 },
            { label: "Expenses", data: monthlyExpenses, borderColor: "#ef4444", backgroundColor: "rgba(239,68,68,0.15)", fill: true, tension: 0.4 }
        ]
    },
    options: sfChartOptions()
});
sfRegisterChart(c1);

const c2 = new Chart(document.getElementById("categoryChart"), {
    type: "doughnut",
    data: {
        labels: catLabels.length ? catLabels : ["No data"],
        datasets: [{ data: catValues.length ? catValues : [1], backgroundColor: ["#3b82f6","#10b981","#f59e0b","#ef4444","#8b5cf6","#06b6d4"] }]
    },
    options: sfChartOptions({ plugins: { legend: { position: "bottom" } }, scales: {} })
});
sfRegisterChart(c2);

const c3 = new Chart(document.getElementById("monthlySpendingChart"), {
    type: "bar",
    data: { labels: monthlyLabels, datasets: [{ label: "Spending", data: monthlyExpenses, backgroundColor: "#3b82f6", borderRadius: 8 }] },
    options: sfChartOptions()
});
sfRegisterChart(c3);
</script>';
include __DIR__ . '/../includes/layout_end.php';
