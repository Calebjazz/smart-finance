<?php
require_once __DIR__ . '/../includes/init.php';
// Ensure $conn is available (some setups store connection in global scope)
$conn = $conn ?? ($GLOBALS['conn'] ?? null);
require_login();

$user_id = (int) $_SESSION['user_id'];
$user_name = htmlspecialchars($_SESSION['full_name'] ?? 'User');
$message = '';
$error = '';

$current_month = (int)date('m');
$current_year = (int)date('Y');

// 1. DYNAMIC PAYCHECK EVALUATION: Calculate Total Budget straight from the incomes table
$income_query = mysqli_prepare($conn, "SELECT COALESCE(SUM(amount), 0) AS total_income FROM incomes WHERE user_id = ? AND MONTH(income_date) = ? AND YEAR(income_date) = ?");
mysqli_stmt_bind_param($income_query, 'iii', $user_id, $current_month, $current_year);
mysqli_stmt_execute($income_query);
$income_res = mysqli_fetch_assoc(mysqli_stmt_get_result($income_query));
$total_budget = (float)($income_res['total_income'] ?? 0);

// 2. AUTOMATIC 50/30/20 ALLOCATION ENGINE: Map your unique database categories
$framework_targets = [
    1 => [
        'name' => 'Needs (Essential Bills | Rent | Food | Health | Transport)', 
        'ratio' => 0.50, 
        'limit' => $total_budget * 0.50,
        'mapped_ids' => [1, 2, 3, 4, 6] // Housing, Food, Transport, Utilities, Healthcare
    ],
    2 => [
        'name' => 'Wants (Entertainment | Leisure | Education | Misc)', 
        'ratio' => 0.30, 
        'limit' => $total_budget * 0.30,
        'mapped_ids' => [5, 7, 8] // Entertainment, Education, Other
    ],
    3 => [
        'name' => 'Savings & Financial Growth Pool', 
        'ratio' => 0.20, 
        'limit' => $total_budget * 0.20,
        'mapped_ids' => [0] // Placeholder (Savings are unspent remaining balances)
    ]
];

// 3. AGGREGATE TOTAL SPENDING: Add up all expenses logged for the current month
$expense_query = mysqli_prepare($conn, "SELECT COALESCE(SUM(amount), 0) AS total_spent FROM expenses WHERE user_id = ? AND MONTH(expense_date) = ? AND YEAR(expense_date) = ?");
mysqli_stmt_bind_param($expense_query, 'iii', $user_id, $current_month, $current_year);
mysqli_stmt_execute($expense_query);
$expense_res = mysqli_fetch_assoc(mysqli_stmt_get_result($expense_query));
$month_expenses = (float)($expense_res['total_spent'] ?? 0);

// 4. BALANCING LOGIC: Deduct expenses from budget to show the true remaining pool
$remaining_balance = $total_budget - $month_expenses;

$page_title = 'Budget Framework';
$active_page = 'budget';
$dash_path = '';
$user_path = '../user/';

include __DIR__ . '/../includes/head.php';
include __DIR__ . '/../includes/user_sidebar.php';
include __DIR__ . '/../includes/user_navbar.php';
?>

<div class="p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold mb-2 card-title">Budget</h1>
        <p class="card-text">Automated 50/30/20 category tracking derived from your real monthly income</p>
    </div>

    <!-- Macro Metrics Panels -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="card rounded-2xl p-6 bg-white shadow-sm border border-gray-100">
            <p class="text-sm font-medium text-gray-400">Total Budget (From Income)</p>
            <p class="text-2xl font-bold text-gray-800 mt-1"><?php echo format_tsh($total_budget); ?></p>
        </div>
        <div class="card rounded-2xl p-6 bg-white shadow-sm border border-gray-100">
            <p class="text-sm font-medium text-gray-400">Spent This Month</p>
            <p class="text-2xl font-bold text-rose-500 mt-1">- <?php echo format_tsh($month_expenses); ?></p>
        </div>
        <div class="card rounded-2xl p-6 bg-white shadow-sm border border-gray-100">
            <p class="text-sm font-medium text-gray-400">Remaining Framework Balance</p>
            <p class="text-2xl font-bold <?php echo $remaining_balance >= 0 ? 'text-green-500' : 'text-rose-600'; ?> mt-1">
                <?php echo format_tsh($remaining_balance); ?>
            </p>
        </div>
    </div>

    <!-- Smart Categories Breakdown Tables -->
    <div class="card rounded-2xl p-6 bg-white shadow-sm border border-gray-100">
        <h3 class="text-lg font-semibold mb-4 text-gray-800">Your Real-Time Target Ratios</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-sm border-b border-gray-200 text-gray-400 font-medium">
                        <th class="pb-3">Budget Class</th>
                        <th class="pb-3">Allocation Framework</th>
                        <th class="pb-3">Target Ceiling</th>
                        <th class="pb-3">Current Consumption Progress</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    <?php if ($total_budget <= 0): ?>
                        <tr><td colspan="4" class="py-6 text-center text-gray-400">No active tracking matrices compiled yet. Add an income record to generate your budget layout automatically!</td></tr>
                    <?php else: foreach ($framework_targets as $cat_group_id => $data):
                        // If it's the Savings target row, the amount spent is the remaining allocation math
                        if ($cat_group_id === 3) {
                            $cat_spent = $remaining_balance < 0 ? 0 : $remaining_balance;
                        } else {
                            // Extract mapped IDs array into comma string: "1,2,3,4,6"
                            $ids_list = implode(',', $data['mapped_ids']);
                            
                            $spent_query = mysqli_prepare($conn, "SELECT COALESCE(SUM(amount), 0) AS cat_spent FROM expenses WHERE user_id = ? AND category_id IN ($ids_list) AND MONTH(expense_date) = ? AND YEAR(expense_date) = ?");
                            mysqli_stmt_bind_param($spent_query, 'iii', $user_id, $current_month, $current_year);
                            mysqli_stmt_execute($spent_query);
                            $spent_res = mysqli_fetch_assoc(mysqli_stmt_get_result($spent_query));
                            $cat_spent = (float)($spent_res['cat_spent'] ?? 0);
                        }
                        
                        $limit_amt = $data['limit'];
                        $pct = $limit_amt > 0 ? min(100, round(($cat_spent / $limit_amt) * 100)) : 0;
                        
                        // Color layout configuration rules (Green safe, Yellow 75%, Orange 90%, Red 100%+)
                        $bar_color = 'bg-green-500';
                        if ($cat_group_id !== 3) {
                            if (($cat_spent / $limit_amt) >= 1.0) $bar_color = 'bg-rose-600';
                            elseif (($cat_spent / $limit_amt) >= 0.90) $bar_color = 'bg-amber-500';
                            elseif (($cat_spent / $limit_amt) >= 0.75) $bar_color = 'bg-yellow-400';
                        } else {
                            $bar_color = 'bg-emerald-500'; // Keep savings a steady growth green
                        }
                    ?>
                    <tr class="align-middle">
                        <td class="py-4 font-semibold text-gray-800"><?php echo htmlspecialchars($data['name']); ?></td>
                        <td class="py-4 font-medium text-gray-400"><?php echo ($data['ratio'] * 100); ?>% of Earnings</td>
                        <td class="py-4 font-bold text-gray-700"><?php echo format_tsh($limit_amt); ?></td>
                        <td class="py-4 w-1/3 min-w-[200px]">
                            <div class="flex items-center justify-between text-xs font-semibold mb-1 text-gray-500">
                                <span><?php echo $cat_group_id === 3 ? 'Saved: ' : 'Used: '; ?><?php echo format_tsh($cat_spent); ?></span>
                                <span><?php echo $pct; ?>%</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="<?php echo $bar_color; ?> h-2 rounded-full transition-all duration-500" style="width: <?php echo $pct; ?>%"></div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/layout_end.php'; ?>
