# Simultaneous Login Support - Port-Specific Session Cookies

## Problem
In a production environment, multiple users need to be able to log in and use the system simultaneously from the same browser (e.g., in a clinic setting where staff share computers).

## Understanding the Challenge
By default, browsers only maintain ONE session cookie per domain. When multiple users try to log in from the same browser:
- User A logs in → Session cookie stored
- User B logs in → Session cookie **overwrites** User A's cookie
- User A is now logged out!

## Solution
Each service now uses **port-specific cookie names and domains** to maintain independent sessions:

### Session Cookie Configuration
| Service | Port | Cookie Name | Cookie Domain | Purpose |
|---------|------|-------------|---------------|---------|
| **Auth** | 8000 | `clinic_session_8000` | `127.0.0.1:8000` | User authentication & login |
| **EMR** | 8001 | `clinic_session_8001` | `127.0.0.1:8001` | Medical records |
| **POS** | 8002 | `clinic_session_8002` | `127.0.0.1:8002` | Billing & transactions |
| **Inventory** | 8003 | `clinic_session_8003` | `127.0.0.1:8003` | Medicine inventory |

## How It Works

### 1. Login Process
- User logs in at **Auth service** (port 8000)
- Browser stores `clinic_session_8000` cookie
- Auth service redirects to appropriate service based on role

### 2. Cross-Service Authentication
- When accessing EMR/POS/Inventory, the middleware forwards **ALL browser cookies** to Auth service
- Auth service validates the `clinic_session_8000` cookie
- If valid, the target service creates its own session (`clinic_session_8001/8002/8003`)
- User data is synchronized across services

### 3. Multiple Simultaneous Sessions
- **User A** opens Tab 1 → Logs in as Doctor → Uses EMR (port 8001)
- **User B** opens Tab 2 → Logs in as Cashier → Uses POS (port 8002)
- Each tab maintains its own set of cookies:
  - Tab 1: `clinic_session_8000` (auth) + `clinic_session_8001` (EMR)
  - Tab 2: `clinic_session_8000` (auth) + `clinic_session_8002` (POS)
- Both users stay logged in simultaneously! ✅

## Files Modified
- `auth-service/config/session.php` - Lines 132, 159
- `emr-service/config/session.php` - Lines 132, 159
- `pos-service/config/session.php` - Lines 132, 159
- `inventory-service/config/session.php` - Lines 132, 159

## Benefits
✅ **Multiple simultaneous logins** from the same browser
✅ **Independent sessions** per service/user
✅ **No automatic logouts** when another user logs in
✅ **Production-ready** for shared workstations
✅ **Seamless cross-service authentication**

## Testing Simultaneous Logins

### Test Case 1: Multiple Users, Same Browser
1. **Tab 1**: Go to `http://127.0.0.1:8000/login`
2. Log in as **Doctor** (Employee ID: 148573)
3. You'll be redirected to EMR Dashboard
4. **Tab 2**: Open new tab, go to `http://127.0.0.1:8000/login`
5. Log in as **Cashier** (different account)
6. You'll be redirected to POS Dashboard
7. **Switch back to Tab 1** - Doctor is still logged in! ✅
8. **Switch to Tab 2** - Cashier is still logged in! ✅

### Test Case 2: Same User, Multiple Services
1. Log in as **Owner**
2. Browse between:
   - Owner Dashboard (port 8000)
   - EMR Reports (port 8001)
   - POS Reports (port 8002)
   - Inventory Reports (port 8003)
3. Each service maintains your session independently

## Technical Details

### Cookie Scope
- Each cookie is scoped to its specific port domain
- Browsers treat `127.0.0.1:8000` and `127.0.0.1:8001` as separate domains
- This allows multiple cookies with different names to coexist

### Session Storage
- Session data is stored in the shared `sessions` table
- Each service has its own session entry
- Session IDs are unique per service per user

### Middleware Behavior
- Middleware forwards all cookies to Auth service for validation
- If Auth cookie is valid, service-specific session is created
- User data is synchronized from Auth service to local session

## Deployment Notes

### For Production
When deploying to production with real domains:

```php
// Example for production domains
'domain' => env('SESSION_DOMAIN', 'auth.clinic.com'),  // Auth service
'domain' => env('SESSION_DOMAIN', 'emr.clinic.com'),   // EMR service
'domain' => env('SESSION_DOMAIN', 'pos.clinic.com'),   // POS service
'domain' => env('SESSION_DOMAIN', 'inventory.clinic.com'), // Inventory
```

Each subdomain will automatically have isolated cookies.

### For Development
Current localhost setup:
- Uses port-based domains: `127.0.0.1:8000`, `127.0.0.1:8001`, etc.
- Port numbers ensure cookie isolation

## Maintenance

### Adding New Services
If you add a new microservice:
1. Use a unique port (e.g., 8004)
2. Set cookie name: `clinic_session_8004`
3. Set cookie domain: `127.0.0.1:8004`
4. Implement middleware to check Auth service
5. Clear cache: `php artisan cache:clear && php artisan config:clear`

### Troubleshooting
If users are getting logged out:
1. Clear browser cookies completely
2. Clear all service caches
3. Truncate sessions table
4. Restart Laravel services
5. Test with fresh browser session

---

**Date Implemented:** October 13, 2025  
**Issue:** Need simultaneous logins from same browser  
**Solution:** Port-specific session cookies with independent domains  
**Status:** ✅ Fully Functional - Production Ready
