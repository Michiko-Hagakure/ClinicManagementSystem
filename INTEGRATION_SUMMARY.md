# Inventory-POS Integration Summary

## Overview
Successfully integrated the **Inventory Service** with the **POS Service** to enable real-time medicine inventory management for pharmacy sales.

## What Was Done

### 1. Created InventoryApiService (`pos-service/app/Services/InventoryApiService.php`)
A new service class that handles all communication between POS and Inventory services:

**Key Methods:**
- `searchMedicines($query, $limit)` - Search medicines by name
- `getAllMedicines($limit)` - Get all available medicines
- `getMedicine($medicineId)` - Get specific medicine details
- `reduceStock($medicineId, $quantity)` - Reduce stock after sale
- `isInventoryServiceAvailable()` - Check service connectivity

**Features:**
- Automatic data mapping between inventory and POS formats
- Comprehensive error logging
- Medicine category detection
- Stock quantity tracking

### 2. Updated PharmacyController (`pos-service/app/Http/Controllers/PharmacyController.php`)
Replaced all mock medicine data with real-time inventory service calls:

**Updated Methods:**
- `sales()` - Now fetches real medicines from inventory-service
- `search($request)` - Now searches actual inventory database
- `sell($request)` - Now validates stock and reduces inventory in real-time
- `apiSearch($request)` - Now returns actual medicine data for AJAX searches

**New Features:**
- Stock validation before sale
- Automatic stock reduction on successful sale
- Real-time price and availability checks
- Better error handling with user-friendly messages

### 3. Service Configuration (`pos-service/config/services.php`)
Added inventory service configuration:

```php
'inventory' => [
    'base_url' => env('INVENTORY_SERVICE_URL', 'http://127.0.0.1:8003'),
    'timeout' => env('INVENTORY_SERVICE_TIMEOUT', 10),
    'api_key' => env('INVENTORY_API_KEY'),
],
```

## How It Works

### Flow Diagram:
```
POS Medicine Sales Page
    ↓
PharmacyController
    ↓
InventoryApiService
    ↓
HTTP Request to http://127.0.0.1:8003/api/v1/medicines/*
    ↓
Inventory Service API
    ↓
Returns Medicine Data (name, dosage, price, stock)
    ↓
POS displays medicines & processes sales
    ↓
On Sale: Stock is reduced in Inventory Service
```

### Medicine Data Flow:

1. **Display Medicines:**
   - POS calls `InventoryApiService::getAllMedicines()`
   - Inventory service returns all medicines with stock > 0
   - Data is mapped to POS format and displayed

2. **Search Medicines:**
   - User types search query
   - POS calls `InventoryApiService::searchMedicines($query)`
   - Returns matching medicines

3. **Process Sale:**
   - User adds medicines to cart
   - On submit, POS validates each medicine:
     - Checks if medicine exists
     - Verifies sufficient stock
     - Calls `InventoryApiService::reduceStock($id, $qty)`
   - If successful, stock is reduced in inventory
   - Transaction is recorded in POS

## Benefits

### Real-Time Synchronization
- ✅ POS always shows current stock levels
- ✅ No overselling - stock validated before each sale
- ✅ Automatic inventory updates when medicines are sold
- ✅ Low stock alerts triggered automatically in inventory service

### Centralized Inventory Management
- ✅ Single source of truth for medicine data
- ✅ Pharmacy staff manage medicines in inventory-service
- ✅ Cashiers use updated data in POS automatically
- ✅ No duplicate data entry

### Data Accuracy
- ✅ Current prices always used
- ✅ Stock quantities synchronized
- ✅ Medicine details (name, dosage) consistent

## Testing the Integration

### 1. Add Medicine in Inventory Service:
```
http://127.0.0.1:8003/medicine/create
```
- Add "Acetylcysteine Sachet, 600mg, Stock: 100, Price: ₱35.00"

### 2. View in POS:
```
http://127.0.0.1:8002/pharmacy/sales
```
- "Acetylcysteine Sachet" should appear in available medicines
- Price: ₱35.00
- Stock: 100

### 3. Process a Sale:
- Add medicine to cart (e.g., quantity: 5)
- Fill patient information
- Process sale
- Check inventory: Stock should now be 95

### 4. Verify Stock Update:
```
http://127.0.0.1:8003/medicine
```
- Acetylcysteine stock should show 95 units

## API Endpoints Used

### Inventory Service API (Port 8003):

**Public API (for inter-service communication):**
- `GET /api/v1/public/medicines/search?q={query}&limit={limit}` - Search medicines
- `GET /api/v1/public/medicines/{id}` - Get medicine details
- `POST /api/v1/public/medicines/{id}/reduce-stock` - Reduce stock quantity
  - Body: `{ "quantity": number }`

**Authenticated API (for web interface):**
- `GET /api/v1/medicines/search?q={query}&limit={limit}` - Search medicines (requires auth)
- `GET /api/v1/medicines/{id}` - Get medicine details (requires auth)
- `POST /api/v1/medicines/{id}/reduce-stock` - Reduce stock quantity (requires auth)
  - Body: `{ "quantity": number }`

Note: The public API routes are used by POS service for server-to-server communication without user authentication.

## Error Handling

The integration includes comprehensive error handling:

1. **Service Unavailable:** Graceful fallback if inventory service is down
2. **Stock Validation:** Prevents overselling
3. **Failed Stock Reduction:** Transaction fails if stock can't be updated
4. **Medicine Not Found:** User-friendly error messages
5. **Logging:** All API calls logged for debugging

## Future Enhancements

Potential improvements:
- [ ] Transaction rollback mechanism for failed stock reductions
- [ ] Real-time stock updates via WebSockets
- [ ] Batch stock reduction for multiple medicines
- [ ] Medicine reservation system for pending transactions
- [ ] API authentication/authorization
- [ ] Caching layer for frequently accessed medicines
- [ ] Stock adjustment history tracking

## Configuration

### Environment Variables (Optional):
Add to `pos-service/.env` if using different URLs:

```env
INVENTORY_SERVICE_URL=http://127.0.0.1:8003
INVENTORY_SERVICE_TIMEOUT=10
```

## Troubleshooting

### Medicines not appearing in POS:
1. Check inventory service is running (http://127.0.0.1:8003)
2. Verify medicines have stock > 0
3. Check logs: `storage/logs/laravel.log`
4. Ensure you're using the public API routes (`/api/v1/public/*`) not authenticated routes
5. Clear caches: `php artisan config:clear && php artisan cache:clear`

### Common Issue: Authentication Error
**Problem:** API returns 401 Unauthorized  
**Cause:** Using authenticated API routes instead of public routes  
**Solution:** POS service uses `/api/v1/public/medicines/*` endpoints for server-to-server communication

### Stock not reducing after sale:
1. Check API endpoint is accessible
2. Verify medicine_id is correct
3. Review logs for API errors

### Performance issues:
1. Consider adding caching
2. Reduce API timeout if service is slow
3. Limit number of medicines displayed

## Summary

The inventory-POS integration enables:
- ✅ Real-time medicine stock management
- ✅ Automatic stock reduction on sales
- ✅ Centralized medicine pricing and information
- ✅ Prevention of overselling
- ✅ Seamless user experience for cashiers

Your POS system now fetches all medicine data directly from the inventory service, ensuring accurate, real-time information for every sale!

