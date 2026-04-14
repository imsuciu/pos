# RestaurantFlow POS - Phase 3

Modern PHP/MySQL restaurant POS for LAMP/Cloudron, with dynamic base path detection, Orderbird-inspired table view, CRUD for tables/products/users, receipt printing, kitchen board stub, and architecture ready for Oblio integration.

## Requirements
- PHP 8.1+
- MySQL 8+
- Apache with mod_rewrite

## Install
1. Upload contents to your app folder (works in `/` or `/bixi`).
2. Import `database/schema.sql`.
3. Set DB credentials in `config/config.php`.
4. Ensure Apache points to project root and `AllowOverride All` is enabled.

## Demo credentials
- admin@restaurant.local / admin123

## Phase 3 included
- Orderbird-style table board
- CRUD: tables, products, users
- Roles: admin, manager, waiter, cashier
- Live order screen
- Printable receipt
- Sales history
- KDS placeholder service + screen stub
- Oblio integration service stub with logging points

## Notes
This is a practical foundation, not a final fiscal/commercial release. Fiscal printers, full split bill engine, live KDS websocket updates and real Oblio sync remain future work.
