<?php
require_once __DIR__ . '/../includes/init.php';
require_login();

global $conn;

$user_id = (int) $_SESSION['user_id'];
$user_name = htmlspecialchars($_SESSION['full_name'] ?? 'User');

$from = $_GET['from'] ?? date('Y-m-01');
$to = $_GET['to'] ?? date('Y-m-d');

$total_income = get_total_income($conn, $user_id, $from, $to);
$total_expenses = get_total_expenses($conn, $user_id, $from, $to);
$net = $total_income - $total_expenses;
$savings_rate = $total_income > 0 ? round(($net / $total_income) * 100, 1) : 0;
$days = max(1, (strtotime($to) - strtotime($from)) / 86400 + 1);
$avg_daily = $total_expenses / $days;

// Budget remaining for the active reporting period. Uses the shared
// get_budget_remaining() helper so the dashboard, reports and budget
// pages all show the exact same number.
$budgets_remaining = get_budget_remaining($conn, $user_id);
$total_budget_remaining = 0.0;
$total_budget_amount    = 0.0;
$total_budget_spent     = 0.0;
foreach ($budgets_remaining as $br) {
    $total_budget_remaining += (float) $br['remaining'];
    $total_budget_amount    += (float) $br['budget_amount'];
    $total_budget_spent     += (float) $br['spent'];
}

$monthly = get_monthly_income_expenses($conn, $user_id);
$category_data = get_expenses_by_category($conn, $user_id);

if (isset($_GET['export'])) {
    save_report_record($conn, $user_id, 'financial_summary');
    if ($_GET['export'] === 'csv') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="financial_report_' . date('Y-m-d') . '.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['Smart Finance Report', $from, 'to', $to]);
        fputcsv($out, ['Total Income', $total_income]);
        fputcsv($out, ['Total Expenses', $total_expenses]);
        fputcsv($out, ['Net Income', $net]);
        fputcsv($out, ['Savings Rate %', $savings_rate]);
        fputcsv($out, []);
        fputcsv($out, ['Category', 'Amount']);
        foreach ($category_data as $c) {
            fputcsv($out, [$c['category_name'], $c['total']]);
        }
        fclose($out);
        exit;
    }
}

$page_title = 'Reports';
$active_page = 'reports';
$include_chart = true;
$dash_path = '';
$user_path = '../user/';

include __DIR__ . '/../includes/head.php';
include __DIR__ . '/../includes/user_sidebar.php';
include __DIR__ . '/../includes/user_navbar.php';
?>

<div class="p-6" id="report-content">
    <div class="mb-8 flex flex-wrap justify-between items-start gap-4">
        <div>
            <h1 class="text-3xl font-bold mb-2 card-title">Financial Reports</h1>
            <p class="card-text">Analytics and insights for your financial health</p>
        </div>
        <div class="flex gap-2">
            <a href="?from=<?php echo urlencode($from); ?>&to=<?php echo urlencode($to); ?>&export=csv" class="gradient-blue text-white px-4 py-2 rounded-xl text-sm font-medium"><i class="fas fa-file-csv mr-1"></i> Export CSV</a>
            <button type="button" onclick="window.print()" class="nav-btn px-4 py-2 rounded-xl text-sm font-medium"><i class="fas fa-file-pdf mr-1"></i> Print / PDF</button>
        </div>
    </div>

    <div class="card rounded-2xl p-4 mb-8">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="text-sm card-text block mb-1">From</label><input type="date" name="from" value="<?php echo htmlspecialchars($from); ?>" class="form-input rounded-lg px-4 py-2"></div>
                 <div><label class="text-sm card-text block mb-1">To</label><input type="date" name="to" value="<?php echo htmlspecialchars($to); ?>" class="form-input rounded-lg px-4 py-2"></div>
            </div>
            <button type="submit" class="gradient-blue text-white px-6 py-2 rounded-xl font-medium"><i class="fas fa-filter mr-1"></i> Apply</button>
        </form>
    </div>

    <div class="grid grid-cols-4 md:grid-cols-2 gap-6 mb-8">
        <div class="card rounded-2xl p-6"><p class="text-sm card-text">Net Income</p><p class="text-2xl font-bold card-title"><?php echo format_usd($net); ?></p></div>
        <div class="card rounded-2xl p-6"><p class="text-sm card-text">Total Expenses</p><p class="text-2xl font-bold card-title"><?php echo format_usd($total_expenses); ?></p></div>
        <div class="card rounded-2xl p-6"><p class="text-sm card-text">Savings Rate</p><p class="text-2xl font-bold card-title"><?php echo $savings_rate; ?>%</p></div>
        <div class="card rounded-2xl p-6"><p class="text-sm card-text">Avg Daily Spend</p><p class="text-2xl font-bold card-title"><?php echo format_usd($avg_daily); ?></p></div>
    </div>

    <!-- <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="card rounded-2xl p-6">
            <h3 class="text-lg font-semibold mb-4 card-title">Income vs Expenses Trend</h3>
            <div class="chart-container"><canvas id="incomeExpenseChart"></canvas></div>
        </div>
        <div class="card rounded-2xl p-6">
            <h3 class="text-lg font-semibold mb-4 card-title">Expense Breakdown</h3>
            <div class="chart-container"><canvas id="expenseBreakdownChart"></canvas></div>
        </div>
    </div>

    <div class="card rounded-2xl p-6">
        <h3 class="text-lg font-semibold mb-4 card-title">Monthly Savings</h3>
        <div class="chart-container" style="height:200px"><canvas id="monthlyComparisonChart"></canvas></div>
    </div> -->
</div>

<?php
$savingsMonthly = [];
for ($i = 0; $i < count($monthly['income']); $i++) {
    $savingsMonthly[] = $monthly['income'][$i] - $monthly['expenses'][$i];
}
$catLabels = array_column($category_data, 'category_name');
$catValues = array_map(fn($r) => (float)$r['total'], $category_data);
$page_scripts = '<script>
const monthlyLabels = ' . json_encode($monthly['labels']) . ';
const monthlyIncome = ' . json_encode($monthly['income']) . ';
const monthlyExpenses = ' . json_encode($monthly['expenses']) . ';
const savingsMonthly = ' . json_encode($savingsMonthly) . ';
const catLabels = ' . json_encode($catLabels) . ';
const catValues = ' . json_encode($catValues) . ';
sfRegisterChart(new Chart(document.getElementById("incomeExpenseChart"), { type:"line", data:{ labels:monthlyLabels, datasets:[{label:"Income",data:monthlyIncome,borderColor:"#10b981",fill:true,tension:0.4},{label:"Expenses",data:monthlyExpenses,borderColor:"#ef4444",fill:true,tension:0.4}]}, options:sfChartOptions() }));
sfRegisterChart(new Chart(document.getElementById("expenseBreakdownChart"), { type:"doughnut", data:{ labels:catLabels.length?catLabels:["No data"], datasets:[{data:catValues.length?catValues:[1],backgroundColor:["#3b82f6","#10b981","#f59e0b","#ef4444","#8b5cf6"]}]}, options:sfChartOptions({plugins:{legend:{position:"bottom"}},scales:{}}) }));
sfRegisterChart(new Chart(document.getElementById("monthlyComparisonChart"), { type:"bar", data:{ labels:monthlyLabels, datasets:[{label:"Savings",data:savingsMonthly,backgroundColor:"#10b981"}]}, options:sfChartOptions() }));
</script>';
include __DIR__ . '/../includes/layout_end.php';
