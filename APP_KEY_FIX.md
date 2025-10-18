# APP_KEY Synchronization Fix

## Problem
Pharmacist (LeBron James, Employee ID: 871183) could not log in to the Inventory/Pharmacy system. After entering credentials, the login would succeed in the Auth service but then redirect back to the login page in an infinite loop.

## Root Cause
**All Laravel services MUST use the same `APP_KEY` to share encrypted session cookies across different ports.**

Laravel uses `APP_KEY` to encrypt and decrypt cookies, including session cookies. When services have different keys, they cannot decrypt each other's cookies, breaking the shared session mechanism.

### Before the Fix:
- **Auth Service**: `APP_KEY=` (empty/missing) ❌
- **EMR Service**: `APP_KEY=base64:c96l8+afTYe5orl/6HSHOkOyStprdsY/WsQHCm6+0hg=` ✓
- **POS Service**: `APP_KEY=base64:c96l8+afTYe5orl/6HSHOkOyStprdsY/WsQHCm6+0hg=` ✓
- **Inventory Service**: `APP_KEY=base64:dPv9VTEyUcNMbwsmHVlrCR9/HiXcDIwpDNFyZOdErEk=` ❌

## Solution
Synchronized `APP_KEY` across all services to use the same encryption key.

### After the Fix:
- **Auth Service**: `APP_KEY=base64:c96l8+afTYe5orl/6HSHOkOyStprdsY/WsQHCm6+0hg=` ✓
- **EMR Service**: `APP_KEY=base64:c96l8+afTYe5orl/6HSHOkOyStprdsY/WsQHCm6+0hg=` ✓
- **POS Service**: `APP_KEY=base64:c96l8+afTYe5orl/6HSHOkOyStprdsY/WsQHCm6+0hg=` ✓
- **Inventory Service**: `APP_KEY=base64:c96l8+afTYe5orl/6HSHOkOyStprdsY/WsQHCm6+0hg=` ✓

## Changes Made

### 1. Updated `.env` Files
```bash
# Auth Service
APP_KEY=base64:c96l8+afTYe5orl/6HSHOkOyStprdsY/WsQHCm6+0hg=

# Inventory Service  
APP_KEY=base64:c96l8+afTYe5orl/6HSHOkOyStprdsY/WsQHCm6+0hg=
```

### 2. Simplified AuthService (`inventory-service/app/Services/AuthService.php`)
- Removed complex session payload decoding logic
- Now relies on Laravel's built-in session handling
- With matching APP_KEY, Laravel automatically decrypts the shared session cookie
- Simplified `checkAuth()` method:
  1. Check local session first
  2. If not found, check for `user_id` in shared session (Laravel handles decryption)
  3. Fetch full user details from Auth API
  4. Store in local session for faster subsequent requests

## How It Works Now

1. **User logs in** at Auth service (port 8000)
   - Session created with `user_id` stored
   - Encrypted session cookie `clinic_session` set
   - Cookie uses `APP_KEY` for encryption

2. **User redirected to Inventory service** (port 8003)
   - Browser sends encrypted `clinic_session` cookie
   - Inventory service uses **same APP_KEY** to decrypt cookie
   - Reads `user_id` from decrypted session
   - Fetches user details from Auth API
   - Grants access if role is `pharmacist`, `pharmacy_staff`, or `owner`

## Testing

1. **Logout** from current session: http://127.0.0.1:8000/logout
2. **Log in** as LeBron James:
   - Employee ID: `871183`
   - Password: (set by admin)
3. ✅ Should be redirected directly to Pharmacy Dashboard at http://127.0.0.1:8003/pharmacy/dashboard
4. ✅ No login loop, no authentication errors

## Why This Was Critical

Without matching APP_KEY:
- Auth service encrypts cookie with Key A (or no key)
- Inventory service tries to decrypt cookie with Key B
- Decryption fails → session appears empty
- Inventory service thinks user is not authenticated
- Redirects back to login
- **Infinite loop** 🔄

With matching APP_KEY:
- Auth service encrypts cookie with Key X
- Inventory service decrypts cookie with Key X
- Session data successfully retrieved
- User authenticated
- **Access granted** ✅

## Important Notes

⚠️ **Never change APP_KEY after deployment** - it will invalidate all existing sessions and encrypted data.

⚠️ **All services sharing sessions must use the same APP_KEY** - this is not optional, it's required for Laravel's encryption to work.

✅ **Keep APP_KEY secret** - never commit it to public repositories without using environment variables.

