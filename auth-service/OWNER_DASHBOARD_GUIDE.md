# 👔 Owner Dashboard Guide

## 📊 Overview

The **Owner Dashboard** provides a comprehensive, consolidated view of all clinic operations across EMR, POS, and Inventory services. This centralized portal allows the clinic owner to monitor key metrics, analyze trends, and make data-driven decisions.

---

## 🎯 Key Features

### 1. **Consolidated Reports**
- View data from all three services in one place
- Financial reports from POS system
- Patient statistics from EMR system
- Inventory insights from Inventory system

### 2. **Flexible Date Filtering**
Quick preset filters:
- **Today** - Current day operations
- **This Week** - Weekly performance
- **This Month** - Monthly overview
- **This Year** - Annual trends
- **Custom Date Range** - Any specific period (including half-year, quarters, etc.)

### 3. **Real-Time Statistics**
- **Financial Summary**
  - Total revenue
  - Number of transactions
  - Paid vs unpaid bills
  - Payment method breakdown

- **Patient Statistics**
  - Total patients served
  - New patient registrations
  - Consultation count
  - Services rendered

- **Inventory Overview**
  - Total stock value
  - Items in inventory
  - Low stock alerts
  - Expiring items warnings

### 4. **Visual Analytics**
- **Revenue Trend Chart** - Track daily revenue over time
- **Payment Methods Pie Chart** - Distribution of payment types
- **Patient Visits Bar Chart** - Daily patient visit patterns
- **Top Medicines Chart** - Best-selling medications

### 5. **Quick Access Links**
Direct access to all systems:
- EMR System (Patient Records)
- POS System (Billing)
- Inventory System (Medicine Management)

---

## 🚀 How to Access

1. **Login** at `http://127.0.0.1:8000/login`
2. Use an **Owner** account (Employee ID + Password)
3. You'll be automatically redirected to the **Owner Dashboard**

---

## 📱 Dashboard URL

```
http://127.0.0.1:8000/owner/dashboard
```

---

## 🔧 Technical Architecture

### Services Integration

The Owner Dashboard communicates with three backend services via API:

| Service | API Endpoint | Data Provided |
|---------|-------------|---------------|
| **POS Service** | `http://127.0.0.1:8002/api/reports/financial` | Revenue, bills, payment methods |
| **EMR Service** | `http://127.0.0.1:8001/api/reports/patients` | Patient stats, consultations, services |
| **Inventory Service** | `http://127.0.0.1:8003/api/reports/inventory` | Stock levels, top medicines, alerts |

### Files Structure

```
auth-service/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── OwnerController.php          # Main dashboard controller
│   │   └── Middleware/
│   │       └── EnsureUserIsOwner.php        # Owner access middleware
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── owner.blade.php               # Owner dashboard layout
│       └── owner/
│           └── dashboard.blade.php           # Dashboard view
└── routes/
    └── web.php                               # Routes configuration
```

---

## 📈 Report Features

### Date Range Parameters

All API endpoints accept:
- `start_date` - Beginning of period (YYYY-MM-DD)
- `end_date` - End of period (YYYY-MM-DD)

### Example API Response

**Financial Data:**
```json
{
  "total_revenue": 125000.00,
  "total_bills": 45,
  "paid_bills": 40,
  "unpaid_bills": 5,
  "payment_methods": {
    "cash": 80000.00,
    "credit_card": 45000.00
  },
  "daily_revenue": [
    {"date": "Jan 01", "amount": 5000},
    {"date": "Jan 02", "amount": 7500}
  ]
}
```

---

## 🎨 Dashboard Sections

### 1. **Summary Cards** (Top Row)
Three color-coded cards showing key metrics:
- 💚 **Financial** (Green) - Total revenue
- 💙 **Patients** (Blue) - Total patients
- 💛 **Inventory** (Orange) - Stock value

### 2. **Revenue Trend** (Main Chart)
Line graph showing daily revenue trends over the selected period

### 3. **Payment Methods** (Pie Chart)
Visual breakdown of how customers pay (cash, card, online, etc.)

### 4. **Patient Visits** (Bar Chart)
Daily patient visit patterns

### 5. **Top Medicines** (Horizontal Bar Chart)
Best-selling medications by quantity

### 6. **Services Table**
Detailed breakdown of services rendered with counts and revenue

---

## 🔐 Security

- **Role-Based Access Control**: Only users with `owner` role can access
- **Middleware Protection**: `EnsureUserIsOwner` middleware guards routes
- **Session-Based Authentication**: Uses Laravel session authentication

---

## 🎯 Future Enhancements

### Planned Features:
1. **PDF Export** - Download reports as PDF (endpoint ready, PDF generation pending)
2. **Excel Export** - Export data to spreadsheets
3. **Email Reports** - Scheduled email delivery of reports
4. **Comparison Views** - Compare current period vs previous period
5. **Employee Performance** - Track individual staff productivity
6. **Financial Projections** - Revenue forecasting based on trends

---

## 🆘 Troubleshooting

### Dashboard shows zero data?
- **Check date range**: Ensure you've selected a period with actual data
- **Verify services are running**: All three services (EMR, POS, Inventory) must be running on ports 8001, 8002, 8003
- **Check API endpoints**: Test API endpoints directly in browser

### Charts not displaying?
- **Clear browser cache**: Hard refresh (Ctrl+Shift+R)
- **Check console**: Open browser developer tools for JavaScript errors
- **Verify Chart.js**: Ensure Chart.js CDN is accessible

### Can't access owner dashboard?
- **Check user role**: Ensure account has `owner` role
- **Clear Laravel cache**: Run `php artisan config:clear` and `php artisan view:clear`
- **Verify middleware**: Check that `owner` middleware is registered in `bootstrap/app.php`

---

## 📞 Support

For questions or issues with the Owner Dashboard, contact the system administrator.

---

**Built with ❤️ for efficient clinic management**

