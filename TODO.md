# TODO - Smart Finance dashboard backend fixes

- [x] Step 1: Update `Includes/functions.php`

  - Add currency formatter `format_tsh()` and remove/stop using `format_usd()` across code.
  - Unify accounting helpers so totals obey:
    - Total income = incomes + (completed deposit savings_transactions)
    - Total expenses = expenses + (withdrawal savings_transactions)
    - Balance = income - expenses
  - Add helpers for monthly totals using unified income/expense.
  - Ensure reports/net income uses unified totals.

- [ ] Step 2: Fix budget remaining + report consistency
  - Implement budget spent calculation consistently.
  - If using `budget_items.spent_amount`, update it when adding/deleting expenses.
  - Update `Dashboard/Budget.php` remaining to match budget report logic.

- [ ] Step 3: Fix auto-dismissing success message
  - Replace persistent success divs with JS toast that auto-hides (e.g., 3s).

- [ ] Step 4: Ensure charts render on user actions
  - Ensure POST handlers redirect-after-success so charts update on reload.
  - Any chart canvases must only init when element exists.

- [ ] Step 5: Currency UI labels
  - Replace all "USD" labels in Income/Expenses/Budget/transactions with "Tsh".

- [ ] Step 6: QA via quick local run
  - Add income -> verify unified totals and toast.
  - Add expense -> verify expenses totals, budget remaining, and report net income.
  - Add savings deposit/withdraw -> verify totals match rules.

