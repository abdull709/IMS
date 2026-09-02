# Manual QA Checklist

| Test ID | Module | Scenario | Steps | Expected Result | Actual Result | Status |
|---|---|---|---|---|---|---|
| T-001 | Authentication | Valid admin login | Log in with `admin` / `password` | Admin reaches dashboard | Pending local run | Pending |
| T-002 | Authentication | Invalid login | Enter wrong password | Error message shown, no session created | Pending local run | Pending |
| T-003 | Authentication | Logout | Click Logout | Session ends and login page appears | Pending local run | Pending |
| T-004 | Authorization | Staff blocked from users | Log in as staff and open `?route=users` | Access denied page appears | Pending local run | Pending |
| T-005 | Categories | Create category | Add `Beverages` | Category appears in list | Pending local run | Pending |
| T-006 | Categories | Duplicate category | Add another `Beverages` | Duplicate error is shown | Pending local run | Pending |
| T-007 | Products | Create product | Add `Malt Drink`, `PRD-0001`, quantity `20`, reorder `5` | Product is saved with opening stock movement | Pending local run | Pending |
| T-008 | Products | Duplicate product code | Add another product with `PRD-0001` | Duplicate error is shown | Pending local run | Pending |
| T-009 | Inventory | Add stock | Add `20` units to Malt Drink | Quantity increases and stock movement is recorded | Pending local run | Pending |
| T-010 | Inventory | Decrease too far | Decrease more than available stock | Request is rejected, quantity unchanged | Pending local run | Pending |
| T-011 | Sales | Single-product sale | Sell `3` Malt Drink | Sale saves, stock becomes `17`, receipt opens | Pending local run | Pending |
| T-012 | Sales | Multi-product sale | Add two products to one sale | One invoice contains multiple items | Pending local run | Pending |
| T-013 | Sales | Exact-stock sale | Sell all available units of a product | Stock becomes `0`, status shows out of stock | Pending local run | Pending |
| T-014 | Sales | Insufficient stock | Try selling `6` when only `5` exist | Sale rejected and stock remains `5` | Pending local run | Pending |
| T-015 | Reports | Sales report filter | Select date range and filter | Matching sales and summary totals appear | Pending local run | Pending |
| T-016 | Reports | Inventory report | Open inventory report | Stock value equals cost price times quantity | Pending local run | Pending |
| T-017 | Security | CSRF missing | Submit write request without token | Request is rejected | Pending local run | Pending |
| T-018 | Security | SQL injection attempt | Search using `' OR 1=1 --` | Search remains safe and prepared | Pending local run | Pending |
| T-019 | Security | XSS input | Enter `<script>` in description | Output is escaped in views | Pending local run | Pending |
| T-020 | Settings | Business info update | Edit name, phone, currency, footer | Receipts and reports show new values | Pending local run | Pending |

## Required Acceptance Scenario

1. Log in as administrator.
2. Create category `Beverages`.
3. Create product `Malt Drink` with code `PRD-0001`, cost price `500`, selling price `700`, quantity `20`, reorder level `5`.
4. Confirm dashboard inventory values.
5. Record sale of `3 x Malt Drink`.
6. Confirm remaining quantity is `17`.
7. Confirm stock movement shows sale deduction.
8. Confirm sale appears in sales history.
9. Open and print receipt.
10. Generate sales report.
11. Sell another `12 x Malt Drink`.
12. Confirm quantity becomes `5` and product appears in Low Stock.
13. Attempt to sell `6` units.
14. Confirm transaction is rejected and stock remains `5`.
15. Add `20` units of stock.
16. Confirm quantity becomes `25` and movement history is updated.
17. Generate inventory report.
18. Log in as staff.
19. Attempt to access user management URL.
20. Confirm access denied while normal staff functions continue working.

This checklist is ready for execution after XAMPP/PHP/MySQL are available locally.
