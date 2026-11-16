PHARMACIA - Pharmacy Database Management System (PHP + MySQL demo)

HOW TO RUN (XAMPP, Windows)
1. Install XAMPP and open the XAMPP Control Panel. Start "Apache" and "MySQL".
2. Copy this whole "pharmacia" folder into  C:\xampp\htdocs\
3. Open http://localhost/phpmyadmin  ->  Import tab  ->  choose database.sql  ->  Go.
   (This creates the "pharmacia" database with tables, sample data, trigger, function and procedures.)
4. Open http://localhost/pharmacia/  and log in:
      admin       / admin123      (full access)
      pharmacist  / pharma123     (no Employees / Suppliers pages)
If MySQL has a root password, edit it at the top of config.php.

DEMO SCRIPT FOR YOUR SIR (maps to your report)
- Login page -> role-based access (admin vs pharmacist).
- Dashboard -> COUNT / SUM / AVG aggregates, low-stock and expiry counts.
- Inventory -> CRUD on the drug table. Try stock = -5: the CHECK constraint (stock >= 0) rejects it.
- Add New Sale -> sells a medicine. The trigger After_Sale_Update_Stock reduces stock automatically.
  The sale runs in a transaction with SELECT ... FOR UPDATE (row-level lock), and rolls back if stock is too low.
- Stock Purchase -> calls stored procedure SafeInsertPurchase (EXIT HANDLER + ROLLBACK). Use a wrong supplier to see the error.
- Customers -> "Total spent" comes from the function GetCustomerTotalSpent().
- Reports -> Soon to expire uses the CURSOR procedure GenerateExpiryReport(); Sales by medicine uses GROUP BY + SUM.
- Sales Invoice Details -> 3-table JOIN (customer, drug, employee).

FILES
database.sql  schema + sample data + trigger/function/procedures
config.php    DB connection, helpers, sidebar layout
index.php (login), dashboard.php, inventory.php, suppliers.php, purchase.php,
employees.php, customers.php, pos.php, sales.php, reports.php, logout.php, style.css
