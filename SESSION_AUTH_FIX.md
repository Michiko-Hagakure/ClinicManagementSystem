# Session Authentication Fix for Inventory Service

## Problem
When users navigated from the POS System to the Inventory/Pharmacy System by clicking "Manage Inventory", they were redirected to the login page instead of accessing the pharmacy dashboard. This happened even though they were already authenticated.

## Root Cause
The Inventory service was trying to validate authentication by making an API call to the Auth service's `/api/auth/check` endpoint with forwarded cookies. However, this approach failed because:

1. The Auth service's `/api/auth/check` endpoint was protected by `auth:sanctum` middleware, which expected API tokens
2. Laravel's cookie encryption and session handling across different services (running on different ports) was causing issues
3. The forwarded cookies weren't being properly recognized by the Auth service's middleware

## Solution
Changed the Inventory service's authentication strategy to **check the shared session database directly** instead of relying on API calls:

### Changes Made:

1. **Auth Service (`auth-service/routes/web.php`):**
   - Changed `/api/auth/check` and `/api/auth/token` routes from `auth:sanctum` middleware to `web` middleware
   - This allows session-based authentication instead of requiring API tokens

2. **Inventory Service (`inventory-service/app/Services/AuthService.php`):**
   - Modified `checkAuth()` method to:
     - First check if user data exists in the local session
     - If not, directly query the shared session database (`auth_session` connection)
     - Extract `user_id` from the session payload
     - Fetch full user details from Auth service's `/api/users/{id}` endpoint
     - Store user data in local session for future requests

### Why This Works:

✅ **Shared Session Database**: All services (Auth, EMR, POS, Inventory) use the same SQLite database for sessions (`auth-service/database/auth.sqlite`)

✅ **Same Cookie Name**: All services use `clinic_session` as the session cookie name

✅ **Same Domain**: All services use `127.0.0.1` as the cookie domain

✅ **Direct Database Access**: Instead of relying on HTTP requests and cookie forwarding, the Inventory service directly reads the session data from the shared database

## How It Works Now:

1. **User logs in** at Auth service (port 8000)
   - Session created in shared database
   - Cookie `clinic_session` set for domain `127.0.0.1`

2. **User navigates to POS** (port 8002)
   - Browser sends `clinic_session` cookie
   - POS reads session from shared database
   - User is authenticated

3. **User clicks "Manage Inventory"** → navigates to Inventory service (port 8003)
   - Browser sends same `clinic_session` cookie
   - Inventory service:
     - Gets session ID from cookie
     - Queries shared session database
     - Extracts `user_id` from session payload
     - Fetches user details from Auth API
     - Verifies user has `pharmacist`, `pharmacy_staff`, or `owner` role
     - Grants access to pharmacy dashboard

## Testing:

To verify the fix works:
1. Log in as a pharmacist (e.g., LeBron James)
2. Should be redirected to Pharmacy Dashboard
3. Navigate to POS System
4. Click "Manage Inventory"
5. Should successfully load Pharmacy Dashboard without login redirect

## Technical Details:

- **Session Driver**: `database`
- **Session Connection**: `auth_session` (shared SQLite database)
- **Session Table**: `sessions`
- **Session Payload**: Base64-encoded serialized PHP data
- **Cookie Encryption**: All services use the same `APP_KEY` for cookie encryption/decryption

