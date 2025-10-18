# Dynamic Doctor List Feature

## Overview
The doctor dropdown in the POS Transaction form now **dynamically fetches** the list of doctors from the Auth service in real-time. This ensures that any doctors added or removed in the System Admin panel will automatically appear or disappear in the POS system.

## How It Works

### 1. Auth Service API Endpoint
- **Endpoint**: `GET http://127.0.0.1:8000/api/doctors`
- **Location**: `auth-service/routes/web.php`
- **Function**: Returns a list of all active users with the role "doctor"
- **Response Format**:
```json
[
  {
    "id": 17,
    "name": "Patrick Mahomes",
    "employee_id": "230310",
    "department": "Radiology"
  },
  {
    "id": 15,
    "name": "Shohei Ohtani",
    "employee_id": "148573",
    "department": "Laboratory"
  }
]
```

### 2. POS Transaction Form
- **Location**: `pos-service/resources/views/transactions/create.blade.php`
- **Behavior**: 
  - When the page loads, it automatically calls the Auth service API
  - Populates the doctor dropdown with the current list of active doctors
  - Shows doctor name and department (e.g., "Patrick Mahomes - Radiology")
  - If no doctors are found, displays "No doctors available"
  - If API fails, displays "Error loading doctors"

## Benefits

✅ **Automatic Synchronization**: No need to manually update doctor lists in multiple places
✅ **Real-Time Updates**: Add a doctor in Admin panel → Immediately available in POS
✅ **Centralized Management**: All doctor data managed from one location (Auth service)
✅ **Error Handling**: Gracefully handles API failures or empty doctor lists

## Testing

### Current Doctors in System:
- Patrick Mahomes - Radiology (Employee ID: 230310)
- Shohei Ohtani - Laboratory (Employee ID: 148573)

### To Test:
1. Go to System Admin → Users → Create User
2. Create a new user with role "Doctor"
3. Go to POS → New Transaction
4. The new doctor should automatically appear in the dropdown
5. If you deactivate or delete a doctor, they will disappear from the dropdown

## Technical Details

- The API only returns **active** doctors (where `is_active = true`)
- Doctors are sorted alphabetically by name
- The dropdown shows both name and department for clarity
- Uses JavaScript Fetch API for asynchronous loading
- No page refresh required - loads on page render

