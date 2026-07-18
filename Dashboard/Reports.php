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

// Budget remaining for the active reporting period.
// Aligned with the Budget page's 50/30/20 framework logic.
$total_budget_amount = $total_income;
$total_budget_spent = $total_expenses;
$total_budget_remaining = $total_income - $total_expenses;

$framework_targets = [
    [
        'category_name' => 'Needs (50%)',
        'budget_amount' => $total_budget_amount * 0.50,
        'mapped_ids' => [1, 2, 3, 4, 6]
    ],
    [
        'category_name' => 'Wants (30%)',
        'budget_amount' => $total_budget_amount * 0.30,
        'mapped_ids' => [5, 7, 8]
    ],
    [
        'category_name' => 'Savings (20%)',
        'budget_amount' => $total_budget_amount * 0.20,
        'mapped_ids' => [0]
    ]
];

$budgets_remaining = [];
foreach ($framework_targets as $index => $data) {
    if ($index === 2) { // Savings
        $spent = $total_budget_remaining < 0 ? 0 : $total_budget_remaining;
    } else {
        $ids_list = implode(',', $data['mapped_ids']);
        $spent_query = mysqli_prepare($conn, "SELECT COALESCE(SUM(amount), 0) AS cat_spent FROM expenses WHERE user_id = ? AND category_id IN ($ids_list) AND expense_date >= ? AND expense_date <= ?");
        mysqli_stmt_bind_param($spent_query, 'iss', $user_id, $from, $to);
        mysqli_stmt_execute($spent_query);
        $spent_res = mysqli_fetch_assoc(mysqli_stmt_get_result($spent_query));
        $spent = (float)($spent_res['cat_spent'] ?? 0);
    }
    
    $remaining = $data['budget_amount'] - $spent;
    $budgets_remaining[] = [
        'category_name' => $data['category_name'],
        'budget_amount' => $data['budget_amount'],
        'spent' => $spent,
        'remaining' => $remaining,
        'month' => date('F', strtotime($from)),
        'year' => date('Y', strtotime($from))
    ];
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
        fputcsv($out, ['Budget Remaining', $total_budget_remaining]);
        fputcsv($out, ['Savings Rate %', $savings_rate]);
        fputcsv($out, []);
        fputcsv($out, ['Category', 'Amount']);
        foreach ($category_data as $c) {
            fputcsv($out, [$c['category_name'], $c['total']]);
        }
        fputcsv($out, []);
        fputcsv($out, ['Budget Category', 'Allocated', 'Spent', 'Remaining']);
        foreach ($budgets_remaining as $br) {
            fputcsv($out, [$br['category_name'], $br['budget_amount'], $br['spent'], $br['remaining']]);
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
        <div class="card rounded-2xl p-6"><p class="text-sm card-text">Net Income</p><p class="text-2xl font-bold card-title"><?php echo format_tsh($net); ?></p></div>
        <div class="card rounded-2xl p-6"><p class="text-sm card-text">Total Expenses</p><p class="text-2xl font-bold card-title"><?php echo format_tsh($total_expenses); ?></p></div>
        <div class="card rounded-2xl p-6">
            <p class="text-sm card-text">Budget Remaining</p>
            <p class="text-2xl font-bold card-title"><?php echo format_tsh($total_budget_remaining); ?></p>
            <p class="text-xs card-text mt-1">of <?php echo format_tsh($total_budget_amount); ?> allocated · <?php echo format_tsh($total_budget_spent); ?> spent</p>
        </div>
        <div class="card rounded-2xl p-6"><p class="text-sm card-text">Savings Rate</p><p class="text-2xl font-bold card-title"><?php echo $savings_rate; ?>%</p></div>
        <div class="card rounded-2xl p-6"><p class="text-sm card-text">Avg Daily Spend</p><p class="text-2xl font-bold card-title"><?php echo format_tsh($avg_daily); ?></p></div>
    </div>

    <?php if (!empty($budgets_remaining)): ?>
    <div class="card rounded-2xl p-6 mb-8">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold card-title">Budget Remaining by Category</h3>
            <a href="Budget.php" class="text-blue-500 text-sm hover:underline">Manage Budgets</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($budgets_remaining as $br):
                $pct = $br['budget_amount'] > 0
                    ? round(((float) $br['spent'] / (float) $br['budget_amount']) * 100)
                    : 0;
                $pct = max(0, min(100, $pct));
                $remainingClass = ((float) $br['remaining']) < 0
                    ? 'text-red-500'
                    : (((float) $br['remaining']) < ((float) $br['budget_amount']) * 0.2
                        ? 'text-yellow-500'
                        : 'text-green-500');
            ?>
            <div class="sub-card rounded-xl p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="font-medium card-title"><?php echo htmlspecialchars($br['category_name']); ?></span>
                    <span class="text-xs card-text"><?php echo htmlspecialchars($br['month'] . ' ' . $br['year']); ?></span>
                </div>
                <p class="text-xl font-bold <?php echo $remainingClass; ?>"><?php echo format_tsh((float) $br['remaining']); ?></p>
                <p class="text-xs card-text mt-1">of <?php echo format_tsh((float) $br['budget_amount']); ?> budget</p>
                <div class="w-full bg-gray-300 rounded-full h-2 mt-2 dark:bg-slate-700">
                    <div class="<?php echo ((float)$br['remaining'] < 0) ? 'bg-red-500' : 'bg-blue-500'; ?> h-2 rounded-full" style="width:<?php echo $pct; ?>%"></div>
                </div>
                <p class="text-xs card-text mt-1"><?php echo $pct; ?>% spent</p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
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
    </div>
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

const el1 = document.getElementById("incomeExpenseChart");
if (el1) {
    sfRegisterChart(new Chart(el1, { type:"line", data:{ labels:monthlyLabels, datasets:[{label:"Income",data:monthlyIncome,borderColor:"#10b981",fill:true,tension:0.4},{label:"Expenses",data:monthlyExpenses,borderColor:"#ef4444",fill:true,tension:0.4}]}, options:sfChartOptions() }));
}
const el2 = document.getElementById("expenseBreakdownChart");
if (el2) {
    sfRegisterChart(new Chart(el2, { type:"doughnut", data:{ labels:catLabels.length?catLabels:["No data"], datasets:[{data:catValues.length?catValues:[1],backgroundColor:["#3b82f6","#10b981","#f59e0b","#ef4444","#8b5cf6"]}]}, options:sfChartOptions({plugins:{legend:{position:"bottom"}}}) }));
}
const el3 = document.getElementById("monthlyComparisonChart");
if (el3) {
    sfRegisterChart(new Chart(el3, { type:"bar", data:{ labels:monthlyLabels, datasets:[{label:"Savings",data:savingsMonthly,backgroundColor:"#10b981"}]}, options:sfChartOptions() }));
}
</script>';

// CRITICAL FIX: Prints the page scripts layout framework setup
include __DIR__ . '/../includes/layout_end.php';
