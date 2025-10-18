# Multi-User Simultaneous Login Fix

## Problem Solved
**Issue**: When testing different roles, logging in as a new user would log out the previous user, making it impossible to test multiple roles simultaneously from the same browser.

**Root Cause**: All services were using the SAME session cookie name (`clinic_session`), so logging in as a different user would overwrite the previous session.

## Solution Implemented
**Port-Specific Session Cookies** - Each service now uses its own unique session cookie:

- **Auth Service (Port 8000)**: `clinic_session_8000`
- **EMR Service (Port 8001)**: `clinic_session_8001`
- **POS Service (Port 8002)**: `clinic_session_8002`
- **Inventory Service (Port 8003)**: `clinic_session_8003`

## How It Works

### For Testing/Development:
You can now have **multiple users logged in simultaneously**:

1. **Tab 1**: Login as **System Administrator** at `http://127.0.0.1:8000`
   - Creates `clinic_session_8000` cookie
   - Stays logged in as Admin

2. **Tab 2**: Login as **Medical Staff** at `http://127.0.0.1:8001`
   - Creates `clinic_session_8001` cookie
   - Stays logged in as Medical Staff
   - **Tab 1 (Admin) remains logged in!** ✅

3. **Tab 3**: Login as **Cashier** at `http://127.0.0.1:8002`
   - Creates `clinic_session_8002` cookie
   - All previous tabs still logged in! ✅

### For Production:
In real-world usage:
- Each employee logs in with **their own credentials**
- They access the appropriate service based on their role
- Single-user experience (one person = one login)

## Configuration Changes

### `.env` Files Updated:

**auth-service/.env**:
```env
SESSION_COOKIE=clinic_session_8000
```

**emr-service/.env**:
```env
SESSION_COOKIE=clinic_session_8001
```

**pos-service/.env**:
```env
SESSION_COOKIE=clinic_session_8002
```

**inventory-service/.env**:
```env
SESSION_COOKIE=clinic_session_8003
```

## Testing Instructions

### Step 1: Restart ALL Servers
**Important**: Stop all servers (`Ctrl+C`) and restart them to load the new configuration.

**Terminal 1 - Auth Service**:
```bash
cd C:\laragon\www\clinic\auth-service
php artisan serve --port=8000
```

**Terminal 2 - EMR Service**:
```bash
cd C:\laragon\www\clinic\emr-service
php artisan serve --port=8001
```

**Terminal 3 - POS Service**:
```bash
cd C:\laragon\www\clinic\pos-service
php artisan serve --port=8002
```

**Terminal 4 - Inventory Service**:
```bash
cd C:\laragon\www\clinic\inventory-service
php artisan serve --port=8003
```

### Step 2: Clear Browser Cookies
`Ctrl + Shift + Delete` → Select "Cookies and other site data" → Click "Clear data"

### Step 3: Test Multi-User Login

**Test Case 1: Admin + Medical Staff**

1. **Open Tab 1**: Go to `http://127.0.0.1:8000/login`
   - Login as: Employee ID `131734`, Password `admin123`
   - Should redirect to Admin dashboard
   - ✅ **Leave this tab open**

2. **Open Tab 2**: Go to `http://127.0.0.1:8001/login`  
   - Login as: Employee ID `413889`, Password `#Llaneta8080`
   - Should redirect to EMR dashboard
   - ✅ **Both tabs stay logged in!**

3. **Switch back to Tab 1**:
   - Refresh the page
   - ✅ **Should STILL be logged in as Admin!**

**Test Case 2: Three Users Simultaneously**

1. **Tab 1**: Admin at port 8000
2. **Tab 2**: Medical Staff at port 8001  
3. **Tab 3**: Cashier at port 8002

All should remain logged in simultaneously! ✅

## How Browser Cookies Work

**Key Insight**: Browsers send ALL cookies for a domain, regardless of port.

When you visit `http://127.0.0.1:8001`:
- Browser sends: `clinic_session_8000`, `clinic_session_8001`, `clinic_session_8002`, `clinic_session_8003`
- The application reads ONLY its configured cookie (e.g., EMR reads `clinic_session_8001`)
- Other cookies are ignored

This allows:
- ✅ **Independent sessions** per service/port
- ✅ **No interference** between different user logins
- ✅ **Simultaneous multi-user testing**

## Troubleshooting

### Problem: User still gets logged out when logging in as different user

**Solution**:
1. Verify `.env` files have port-specific cookie names (run this command):
   ```powershell
   Get-Content auth-service/.env | Select-String "SESSION_COOKIE"
   ```
   Should show: `SESSION_COOKIE=clinic_session_8000`

2. Restart all servers (cookies only update on server restart)

3. Clear ALL browser cookies completely

4. Test again

### Problem: "The provided credentials do not match our records"

**Solution**:
- Make sure you're using the correct credentials for each role
- Check that the user exists in the database
- Verify `employee_id` is set correctly

### Problem: One service redirects to login when accessing it

**Solution**:
- Make sure ALL services are running
- Clear configuration cache: `php artisan config:clear`
- Check logs: `storage/logs/laravel.log`

## Important Notes

1. **Session Isolation**: Each port maintains its own independent session
2. **Role-Based Access**: Each user can only access services their role permits
3. **Cookie Lifetime**: Sessions expire after 120 minutes (configurable in `.env`)
4. **Security**: In production, ensure HTTPS and secure cookie settings

## Files Modified

1. ✅ `auth-service/.env` - Set `SESSION_COOKIE=clinic_session_8000`
2. ✅ `emr-service/.env` - Set `SESSION_COOKIE=clinic_session_8001`
3. ✅ `pos-service/.env` - Set `SESSION_COOKIE=clinic_session_8002`
4. ✅ `inventory-service/.env` - Set `SESSION_COOKIE=clinic_session_8003`
5. ✅ All services - Cleared configuration and application caches
6. ✅ Auth database - Cleared old sessions

## What This Enables

### Development/Testing:
- ✅ Test multiple roles simultaneously
- ✅ Compare different user experiences side-by-side
- ✅ Debug role-specific features easily
- ✅ No need to constantly log out/in

### Production:
- ✅ Each employee logs in once with their credentials
- ✅ Access all services they have permission for
- ✅ Secure, role-based access control
- ✅ Single authentication across all services

---

**Date Fixed**: October 13, 2025  
**Issue**: Cannot test multiple roles simultaneously  
**Resolution**: Implemented port-specific session cookies for independent user sessions per service

