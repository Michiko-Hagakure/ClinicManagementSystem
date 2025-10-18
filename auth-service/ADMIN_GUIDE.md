# System Administrator Guide

## Default Admin Credentials

**Access the Admin Panel:**
- URL: `http://127.0.0.1:8000/admin/users`
- Email: `admin@clinic.com`
- Password: `admin123`

⚠️ **IMPORTANT:** Please change the default password after first login!

## Features

### User Management
- Create new user accounts for clinic employees
- Assign roles (Admin, Doctor, Cashier, Pharmacy Staff, etc.)
- Set departments
- Activate/Deactivate accounts
- Edit user information
- Delete user accounts

## Available Roles

1. **System Administrator** - Full access to all system settings and user management
2. **Owner** - Business owner with access to financial reports and settings
3. **Doctor** - Access to EMR, patient consultations, and medical records
4. **Medical Staff** - Nurses and medical assistants
5. **Cashier** - Access to POS and billing system
6. **Pharmacy Staff** - Manage medicine inventory and dispensing
7. **Pharmacist** - Senior pharmacy role with additional permissions
8. **Clinic Staff** - General clinic staff with limited access

## How to Create New Employee Accounts

1. Login to admin panel at http://127.0.0.1:8000/login
2. Navigate to User Management
3. Click "Create New User"
4. Fill in employee details:
   - Full Name
   - Email (will be used as login username)
   - Password (employee will use this to login)
   - Role
   - Department
5. Make sure "Active Account" is checked
6. Click "Create User"

## Security Best Practices

- Change default admin password immediately
- Use strong passwords for all accounts
- Deactivate accounts for employees who leave
- Regularly review user access and roles
- Never share admin credentials

## Support

For technical support or issues, contact your system administrator.

