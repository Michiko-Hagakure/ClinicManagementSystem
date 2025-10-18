<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic Management Report - {{ $period }}</title>
    <style>
        @media print {
            .no-print { display: none; }
            @page { margin: 1cm; }
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            background: white;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #00A689;
        }
        
        .header h1 {
            color: #00A689;
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .header .period {
            font-size: 16px;
            color: #666;
            font-weight: bold;
        }
        
        .header .generated {
            font-size: 12px;
            color: #999;
            margin-top: 5px;
        }
        
        .section {
            margin-bottom: 40px;
            page-break-inside: avoid;
        }
        
        .section-title {
            background: #00A689;
            color: white;
            padding: 12px 20px;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .metrics {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .metric-card {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            background: #f9f9f9;
        }
        
        .metric-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        
        .metric-value {
            font-size: 24px;
            font-weight: bold;
            color: #00A689;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 13px;
        }
        
        table thead {
            background: #f5f5f5;
        }
        
        table th, table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        
        table th {
            font-weight: bold;
            color: #333;
        }
        
        table tbody tr:nth-child(even) {
            background: #fafafa;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #00A689;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        .print-button:hover {
            background: #008a72;
        }
        
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #e0e0e0;
            text-align: center;
            color: #999;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">Print / Save as PDF</button>
    
    <div class="header">
        <h1>Clinic Management Report</h1>
        <div class="period">Period: {{ $period }}</div>
        <div class="generated">Generated on: {{ now()->format('F d, Y h:i A') }}</div>
    </div>

    <!-- Financial Section -->
    <div class="section">
        <div class="section-title">Financial Summary</div>
        
        <div class="metrics">
            <div class="metric-card">
                <div class="metric-label">Total Revenue</div>
                <div class="metric-value">₱{{ number_format($financialData['total_revenue'] ?? 0, 2) }}</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Total Transactions</div>
                <div class="metric-value">{{ number_format($financialData['total_transactions'] ?? 0) }}</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Paid Bills</div>
                <div class="metric-value">{{ number_format($financialData['paid_bills'] ?? 0) }}</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Unpaid Bills</div>
                <div class="metric-value">{{ number_format($financialData['unpaid_bills'] ?? 0) }}</div>
            </div>
        </div>

        @if(!empty($financialData['payment_methods']) && count($financialData['payment_methods']) > 0)
        <h3 style="margin-top: 25px; margin-bottom: 10px; color: #555;">Payment Methods Breakdown</h3>
        <table>
            <thead>
                <tr>
                    <th>Payment Method</th>
                    <th class="text-center">Transactions</th>
                    <th class="text-right">Total Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($financialData['payment_methods'] as $method)
                <tr>
                    <td>{{ ucfirst($method['payment_method'] ?? 'N/A') }}</td>
                    <td class="text-center">{{ number_format($method['count'] ?? 0) }}</td>
                    <td class="text-right">₱{{ number_format($method['total_amount'] ?? 0, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        @if(!empty($financialData['services_rendered']) && count($financialData['services_rendered']) > 0)
        <h3 style="margin-top: 25px; margin-bottom: 10px; color: #555;">Top Services by Revenue</h3>
        <table>
            <thead>
                <tr>
                    <th>Service Name</th>
                    <th class="text-center">Quantity</th>
                    <th class="text-right">Revenue</th>
                </tr>
            </thead>
            <tbody>
                @foreach(array_slice($financialData['services_rendered'], 0, 10) as $service)
                <tr>
                    <td>{{ $service['service_name'] ?? 'N/A' }}</td>
                    <td class="text-center">{{ number_format($service['count'] ?? 0) }}</td>
                    <td class="text-right">₱{{ number_format($service['revenue'] ?? 0, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    <!-- Patient Section -->
    <div class="section">
        <div class="section-title">Patient Statistics</div>
        
        <div class="metrics">
            <div class="metric-card">
                <div class="metric-label">Total Patients</div>
                <div class="metric-value">{{ number_format($patientData['total_patients'] ?? 0) }}</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">New Patients</div>
                <div class="metric-value">{{ number_format($patientData['new_patients'] ?? 0) }}</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Total Consultations</div>
                <div class="metric-value">{{ number_format($patientData['total_consultations'] ?? 0) }}</div>
            </div>
        </div>

        @if(!empty($patientData['services_rendered']) && count($patientData['services_rendered']) > 0)
        <h3 style="margin-top: 25px; margin-bottom: 10px; color: #555;">Top Chief Complaints</h3>
        <table>
            <thead>
                <tr>
                    <th>Chief Complaint</th>
                    <th class="text-center">Occurrences</th>
                </tr>
            </thead>
            <tbody>
                @foreach(array_slice($patientData['services_rendered'], 0, 10) as $service)
                <tr>
                    <td>{{ $service['service_name'] ?? 'N/A' }}</td>
                    <td class="text-center">{{ number_format($service['count'] ?? 0) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    <!-- Inventory Section -->
    <div class="section">
        <div class="section-title">Inventory Summary</div>
        
        <div class="metrics">
            <div class="metric-card">
                <div class="metric-label">Total Medicines</div>
                <div class="metric-value">{{ number_format($inventoryData['total_medicines'] ?? 0) }}</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Stock Value</div>
                <div class="metric-value">₱{{ number_format($inventoryData['total_stock_value'] ?? 0, 2) }}</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Low Stock Items</div>
                <div class="metric-value">{{ number_format($inventoryData['low_stock_items'] ?? 0) }}</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Expiring Soon</div>
                <div class="metric-value">{{ number_format($inventoryData['expiring_items'] ?? 0) }}</div>
            </div>
        </div>

        @if(!empty($inventoryData['top_selling_medicines']) && count($inventoryData['top_selling_medicines']) > 0)
        <h3 style="margin-top: 25px; margin-bottom: 10px; color: #555;">High-Volume Medicine Sales</h3>
        <table>
            <thead>
                <tr>
                    <th>Medicine Name</th>
                    <th>Description</th>
                    <th class="text-center">Quantity Sold</th>
                    <th class="text-center">Current Stock</th>
                </tr>
            </thead>
            <tbody>
                @foreach(array_slice($inventoryData['top_selling_medicines'], 0, 15) as $medicine)
                <tr>
                    <td>{{ $medicine['medicine']['name'] ?? 'N/A' }}</td>
                    <td>{{ $medicine['medicine']['description'] ?? 'N/A' }}</td>
                    <td class="text-center">{{ number_format($medicine['total_quantity_sold'] ?? 0) }}</td>
                    <td class="text-center">{{ number_format($medicine['medicine']['stock_quantity'] ?? 0) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    <div class="footer">
        <p>This is a system-generated report from Clinic Management System</p>
        <p>Confidential - For authorized personnel only</p>
    </div>

    <script>
        // Auto-print dialog when page loads (optional - can be removed)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>

