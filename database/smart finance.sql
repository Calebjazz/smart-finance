SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Fresh install: uncomment next line to reset database
-- DROP DATABASE IF EXISTS financedb;

CREATE DATABASE IF NOT EXISTS financedb;
USE financedb;

-- USER
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    role ENUM('user','admin') DEFAULT 'user',
    status ENUM('active','blocked') DEFAULT 'active',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- INCOME CATEGORIES
CREATE TABLE IF NOT EXISTS income_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- INCOMES
CREATE TABLE IF NOT EXISTS incomes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    description TEXT,
    title VARCHAR(150),
    income_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES income_categories(id)
);

-- EXPENSE CATEGORIES
CREATE TABLE IF NOT EXISTS expense_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- EXPENSES
CREATE TABLE IF NOT EXISTS expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    description TEXT,
    expense_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES expense_categories(id)
);

-- BUDGETS
CREATE TABLE IF NOT EXISTS budgets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    budget_amount DECIMAL(12,2) NOT NULL,
    month VARCHAR(20) NOT NULL,
    year YEAR NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES expense_categories(id)
);

-- BUDGET ITEMS
CREATE TABLE IF NOT EXISTS budget_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    budget_id INT NOT NULL,
    category_id INT NOT NULL,
    allocated_amount DECIMAL(12,2) NOT NULL,
    spent_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (budget_id) REFERENCES budgets(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES expense_categories(id)
);

-- -- SAVINGS & GOALS
-- CREATE TABLE IF NOT EXISTS savings_goals (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     user_id INT NOT NULL,
--     goal_name VARCHAR(150) NOT NULL,
--     target_amount DECIMAL(12,2) NOT NULL,
--     current_amount DECIMAL(12,2) DEFAULT 0,
--     target_date DATE,
--     status ENUM('active','completed') DEFAULT 'active',
--     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--     FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
-- );

-- NOTIFICATIONS
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    notification_type ENUM('email','whatsapp','system') DEFAULT 'system',
    status ENUM('pending','sent','failed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- AI CONVERSATIONS
CREATE TABLE IF NOT EXISTS ai_consultations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    user_question TEXT NOT NULL,
    ai_response LONGTEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- REPORTS
CREATE TABLE IF NOT EXISTS reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    report_type ENUM('income','expense','budget','savings','financial_summary') NOT NULL,
    generated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- AUTOMATION LOGS
CREATE TABLE IF NOT EXISTS automation_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    event_type VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('success','failure') DEFAULT 'success',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- SAVINGS TRANSACTIONS
CREATE TABLE IF NOT EXISTS savings_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    status ENUM('active','completed') NOT NULL DEFAULT 'active',
    type ENUM('deposit','withdrawal') NOT NULL,
    REFERENCE VARCHAR(255) NOT NULL,
    transaction_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- =========================================
-- INSERT DATA
-- =========================================

INSERT IGNORE INTO users (full_name, email, phone, password, role, status) VALUES
('System Admin', 'admin@smartfinance.com', '0717902752', '$2y$10$2BTr4lWwsmmGBLV.85rxIOWDy.tbrZRTy.2nk0TPGkc5VDQDyD3e6', 'admin', 'active'),


-- Default password for all seeded accounts: admin123

INSERT IGNORE INTO income_categories (category_name, description) VALUES
('Salary', 'Regular employment income'),
('Freelance', 'Contract and freelance work'),
('Investment', 'Dividends and investment returns'),
('Rental', 'Property rental income'),
('Other', 'Miscellaneous income');

INSERT IGNORE INTO expense_categories (category_name, description) VALUES
('Housing', 'Rent, mortgage, utilities related to home'),
('Food', 'Groceries and dining'),
('Transport', 'Fuel, public transport, car maintenance'),
('Utilities', 'Electricity, water, internet'),
('Entertainment', 'Movies, subscriptions, leisure'),
('Healthcare', 'Medical and health expenses'),
('Education', 'Courses and learning materials'),
('Other', 'Miscellaneous expenses');

INSERT INTO incomes (user_id, category_id, amount, description, title, income_date)
SELECT u.id, 1, 5000.00, 'Monthly salary', 'Salary', CURDATE() FROM users u WHERE u.email='jane@example.com' LIMIT 1;

INSERT INTO incomes (user_id, category_id, amount, description, title, income_date)
SELECT u.id, 2, 1200.00, 'Web project', 'Freelance', DATE_SUB(CURDATE(), INTERVAL 5 DAY) FROM users u WHERE u.email='jane@example.com' LIMIT 1;

INSERT INTO incomes (user_id, category_id, amount, description, title, income_date)
SELECT u.id, 1, 4500.00, 'Monthly salary', 'Salary', CURDATE() FROM users u WHERE u.email='john@example.com' LIMIT 1;

INSERT INTO expenses (user_id, category_id, amount, description, expense_date)
SELECT u.id, 2, 350.00, 'Weekly groceries', DATE_SUB(CURDATE(), INTERVAL 2 DAY) FROM users u WHERE u.email='jane@example.com' LIMIT 1;

INSERT INTO expenses (user_id, category_id, amount, description, expense_date)
SELECT u.id, 4, 85.00, 'Electricity bill', DATE_SUB(CURDATE(), INTERVAL 3 DAY) FROM users u WHERE u.email='jane@example.com' LIMIT 1;

INSERT INTO expenses (user_id, category_id, amount, description, expense_date)
SELECT u.id, 3, 60.00, 'Fuel', DATE_SUB(CURDATE(), INTERVAL 1 DAY) FROM users u WHERE u.email='jane@example.com' LIMIT 1;

INSERT INTO expenses (user_id, category_id, amount, description, expense_date)
SELECT u.id, 1, 1200.00, 'Rent payment', CURDATE() FROM users u WHERE u.email='john@example.com' LIMIT 1;

INSERT INTO budgets (user_id, category_id, budget_amount, month, year)
SELECT u.id, 2, 500.00, DATE_FORMAT(CURDATE(), '%M'), YEAR(CURDATE()) FROM users u WHERE u.email='jane@example.com' LIMIT 1;

INSERT INTO budgets (user_id, category_id, budget_amount, month, year)
SELECT u.id, 3, 200.00, DATE_FORMAT(CURDATE(), '%M'), YEAR(CURDATE()) FROM users u WHERE u.email='jane@example.com' LIMIT 1;

INSERT INTO budget_items (budget_id, category_id, allocated_amount, spent_amount)
SELECT b.id, 2, 500.00, 350.00 FROM budgets b JOIN users u ON u.id=b.user_id WHERE u.email='jane@example.com' ORDER BY b.id DESC LIMIT 1;

INSERT INTO budget_items (budget_id, category_id, allocated_amount, spent_amount)
SELECT b.id, 3, 200.00, 60.00 FROM budgets b JOIN users u ON u.id=b.user_id WHERE u.email='jane@example.com' ORDER BY b.id ASC LIMIT 1;

-- INSERT INTO savings_goals (user_id, goal_name, target_amount, current_amount, target_date, status)
-- SELECT u.id, 'Emergency Fund', 10000.00, 2500.00, DATE_ADD(CURDATE(), INTERVAL 6 MONTH), 'active' FROM users u WHERE u.email='jane@example.com' LIMIT 1;

-- INSERT INTO savings_goals (user_id, goal_name, target_                                                                                                    amount, current_amount, target_date, status)
-- SELECT u.id, 'Vacation', 3000.00, 800.00, DATE_ADD(CURDATE(), INTERVAL 3 MONTH), 'active' FROM users u WHERE u.email='jane@example.com' LIMIT 1;

-- INSERT INTO savings_goals (user_id, goal_name, target_amount, current_amount, target_date, status)
-- SELECT u.id, 'New Laptop', 1500.00, 400.00, NULL, 'active' FROM users u WHERE u.email='john@example.com' LIMIT 1;

INSERT INTO savings_transactions (user_id, amount, status, type, REFERENCE, transaction_date)
SELECT u.id, 500.00, 'active', 'deposit', 'EMERG-001', DATE_SUB(CURDATE(), INTERVAL 10 DAY) FROM users u WHERE u.email='jane@example.com' LIMIT 1;

INSERT INTO savings_transactions (user_id, amount, status, type, REFERENCE, transaction_date)
SELECT u.id, 200.00, 'completed', 'deposit', 'VAC-001', DATE_SUB(CURDATE(), INTERVAL 5 DAY) FROM users u WHERE u.email='jane@example.com' LIMIT 1;

INSERT INTO savings_transactions (user_id, amount, status, type, REFERENCE, transaction_date)
SELECT u.id, 400.00, 'active', 'deposit', 'LAP-001', CURDATE() FROM users u WHERE u.email='john@example.com' LIMIT 1;

INSERT INTO notifications (user_id, title, message, notification_type, status)
SELECT u.id, 'Welcome to Smart Finance', 'Your account is ready. Start tracking your finances in USD.', 'system', 'pending' FROM users u WHERE u.email='jane@example.com' LIMIT 1;

INSERT INTO notifications (user_id, title, message, notification_type, status)
SELECT u.id, 'Budget tip', 'You are within your food budget this month.', 'system', 'pending' FROM users u WHERE u.email='jane@example.com' LIMIT 1;

INSERT INTO notifications (user_id, title, message, notification_type, status)
SELECT u.id, 'Welcome to Smart Finance', 'Set your first savings goal today.', 'system', 'pending' FROM users u WHERE u.email='john@example.com' LIMIT 1;

INSERT INTO ai_consultations (user_id, user_question, ai_response)
SELECT u.id, 'How can I save more?', 'Try the 50/30/20 rule: 50% needs, 30% wants, 20% savings.' FROM users u WHERE u.email='jane@example.com' LIMIT 1;

INSERT INTO reports (user_id, report_type)
SELECT u.id, 'financial_summary' FROM users u WHERE u.email='jane@example.com' LIMIT 1;

INSERT INTO automation_logs (user_id, event_type, message, status) VALUES
(NULL, 'system_startup', 'Smart Finance database initialized', 'success'),
(2, 'budget_check', 'Monthly budget review completed', 'success');

COMMIT;
