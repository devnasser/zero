# Inventory & Orders 360

## Scope
- Products, Stock entries, Suppliers, Purchase orders, Sales orders, Shipments
- Reorder alerts, invoice matching, basic profitability

## Entities
- Product(sku, name, category, price, reorder_level)
- StockEntry(product_id, type[in|out|adjust], quantity)
- Supplier(name, email, phone)
- PurchaseOrder(supplier_id, status)
- SalesOrder(customer_name, status)
- Shipment(sales_order_id, carrier, tracking)

## Workflows
- On StockEntry.in -> recalc stock for product
- On Product.updated when stock <= reorder_level -> notify procurement

## Views
- Tables for Products and StockEntries with filters
- Forms for Product and StockEntry

## API
- Publish CRUD for all entities with Sanctum auth