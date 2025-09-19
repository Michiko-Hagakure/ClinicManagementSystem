<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $transaction['receipt_number'] ?? 'N/A' }}</title>
    <meta name="robots" content="noindex, nofollow">
    <style>
        /* Reset and Receipt Styling - Traditional Format */
        * {
            box-sizing: border-box;
        }
        
        html, body {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            height: 100% !important;
            font-family: 'Courier New', monospace !important;
            background: white !important;
            overflow-x: hidden !important;
        }
        
        body {
            padding: 20px !important;
            background: #f5f5f5 !important;
        }
        
        /* Force hide any application elements */
        .sidebar, .nav, .navbar, .header, .menu, .app-sidebar, .main-sidebar, 
        .content-wrapper, .wrapper, .layout, .app-layout, #app, .app,
        aside, nav, header, .main-header, .navbar-nav, .sidebar-wrapper {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            position: absolute !important;
            left: -9999px !important;
        }
        
        /* Ensure receipt container takes full space */
        .receipt-container {
            position: relative !important;
            z-index: 99999 !important;
        }
        
        .receipt-container {
            max-width: 320px;
            margin: 0 auto;
            background: white;
            padding: 0;
            border: 1px solid #ddd;
            position: relative;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        /* Perforated edges effect */
        .receipt-container:before,
        .receipt-container:after {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            height: 10px;
            background-image: radial-gradient(circle at 10px center, transparent 5px, white 5px);
            background-size: 20px 10px;
            background-repeat: repeat-x;
        }
        
        .receipt-container:before {
            top: -5px;
        }
        
        .receipt-container:after {
            bottom: -5px;
        }
        
        .receipt-content {
            padding: 20px 15px;
            font-size: 12px;
            line-height: 1.3;
        }
        
        .receipt-header {
            text-align: center;
            margin-bottom: 15px;
        }
        
        .receipt-header h2 {
            font-size: 18px;
            font-weight: bold;
            margin: 0 0 10px 0;
            letter-spacing: 2px;
        }
        
        .clinic-info {
            text-align: center;
            margin-bottom: 10px;
        }
        
        .clinic-name {
            font-weight: bold;
            font-size: 13px;
            margin-bottom: 3px;
        }
        
        .clinic-address,
        .clinic-contact {
            font-size: 11px;
            margin-bottom: 2px;
        }
        
        .dotted-line {
            border-top: 1px dashed #000;
            margin: 12px 0;
            width: 100%;
        }
        
        .transaction-info,
        .services-section,
        .medicines-section,
        .total-section {
            margin-bottom: 8px;
        }
        
        .receipt-line {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
            align-items: flex-start;
        }
        
        .receipt-line span:first-child {
            flex: 1;
            padding-right: 10px;
        }
        
        .receipt-line span:last-child {
            text-align: right;
            white-space: nowrap;
        }
        
        .item-name {
            font-size: 11px;
            line-height: 1.2;
        }
        
        .item-price {
            font-weight: bold;
        }
        
        .total-line {
            font-size: 14px;
            font-weight: bold;
            border-top: 1px solid #000;
            padding-top: 5px;
            margin-top: 5px;
        }
        
        .thank-you {
            text-align: center;
            margin: 15px 0;
            font-size: 11px;
        }
        
        .thank-you div {
            margin-bottom: 3px;
        }
        
        .small-text {
            font-size: 9px !important;
            font-style: italic;
        }
        
        .doctor-section {
            margin: 8px 0;
        }
        
        .center-text {
            text-align: center;
        }
        
        .section-header {
            text-align: center;
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .barcode-section {
            text-align: center;
            margin-top: 15px;
            padding-top: 10px;
        }
        
        .barcode {
            font-family: 'Courier New', monospace;
            font-size: 20px;
            letter-spacing: 1px;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .barcode-number {
            font-size: 10px;
            margin-top: 3px;
        }
        
        /* Print styles */
        @media print {
            html, body {
                margin: 0;
                padding: 0;
                background: white !important;
                width: 100% !important;
                height: auto !important;
            }
            
            .receipt-container {
                box-shadow: none !important;
                border: none !important;
                max-width: none !important;
                margin: 0 !important;
                padding: 10px !important;
            }
            
            /* Hide any potential navigation or sidebar elements */
            nav, .sidebar, .header, .navbar, .menu {
                display: none !important;
            }
        }
        
        @page {
            size: 80mm auto;
            margin: 5mm;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="receipt-content">
            <!-- Receipt Header -->
            <div class="receipt-header">
                <h2>RECEIPT</h2>
            </div>
            
            <!-- Dotted separator -->
            <div class="dotted-line"></div>
            
            <!-- Clinic Information -->
            <div class="clinic-info">
                <div class="clinic-name">MARY ANGELS DIAGNOSTIC CLINIC</div>
                <div class="clinic-address">Sample Address, City, Philippines</div>
                <div class="clinic-contact">Tel: (02) 123-4567</div>
            </div>
            
            <!-- Dotted separator -->
            <div class="dotted-line"></div>
            
            <!-- Transaction Details -->
            <div class="transaction-info">
                <div class="receipt-line">
                    <span>Receipt #:</span>
                    <span>{{ $transaction['receipt_number'] ?? 'N/A' }}</span>
                </div>
                <div class="receipt-line">
                    <span>Date:</span>
                    <span>{{ isset($transaction['created_at']) ? date('m/d/Y H:i', strtotime($transaction['created_at'])) : date('m/d/Y H:i') }}</span>
                </div>
                <div class="receipt-line">
                    <span>Transaction:</span>
                    <span>{{ $transaction['transaction_id'] ?? 'N/A' }}</span>
                </div>
                <div class="receipt-line">
                    <span>Patient:</span>
                    <span>{{ $transaction['patient_name'] ?? 'Walk-in' }}</span>
                </div>
                <div class="receipt-line">
                    <span>Patient ID:</span>
                    <span>{{ $transaction['patient_id'] ?? 'N/A' }}</span>
                </div>
                <div class="receipt-line">
                    <span>Cashier:</span>
                    <span>{{ $transaction['cashier'] ?? 'Cashier' }}</span>
                </div>
            </div>
            
            <!-- Dotted separator -->
            <div class="dotted-line"></div>
            
            <!-- Services Section -->
            @if(!empty($transaction['services']))
            <div class="services-section">
                @foreach($transaction['services'] as $service)
                <div class="receipt-line">
                    <span class="item-name">{{ $service['name'] }}</span>
                    <span class="item-price">₱{{ number_format($service['price'], 2) }}</span>
                </div>
                @endforeach
            </div>
            @endif
            
            <!-- Medicine Section -->
            @if(!empty($transaction['medicines']))
            <div class="medicines-section">
                @foreach($transaction['medicines'] as $medicine)
                <div class="receipt-line">
                    <span class="item-name">{{ $medicine['name'] }} ({{ $medicine['quantity'] ?? 1 }}x)</span>
                    <span class="item-price">₱{{ number_format($medicine['total'] ?? $medicine['subtotal'] ?? 0, 2) }}</span>
                </div>
                @endforeach
            </div>
            @endif
            
            <!-- Dotted separator -->
            <div class="dotted-line"></div>
            
            <!-- Total Section -->
            <div class="total-section">
                <div class="receipt-line total-line">
                    <span><strong>Total:</strong></span>
                    <span><strong>₱{{ number_format($transaction['total_amount'] ?? 0, 2) }}</strong></span>
                </div>
                <div class="receipt-line">
                    <span>Payment:</span>
                    <span>{{ ucfirst($transaction['payment_method'] ?? 'Cash') }}</span>
                </div>
                <div class="receipt-line">
                    <span>Amount Paid:</span>
                    <span>₱{{ number_format($transaction['amount_paid'] ?? 0, 2) }}</span>
                </div>
                @if(($transaction['change_amount'] ?? 0) > 0)
                <div class="receipt-line">
                    <span>Change:</span>
                    <span>₱{{ number_format($transaction['change_amount'], 2) }}</span>
                </div>
                @endif
            </div>
            
            <!-- Doctor Assignment Section -->
            <div class="dotted-line"></div>
            <div class="doctor-section">
                <div class="section-header">DOCTOR ASSIGNMENT</div>
                <div class="receipt-line">
                    <span>Doctor:</span>
                    <span>{{ $transaction['assigned_doctor'] ?? 'TEST DOCTOR' }}</span>
                </div>
                <div class="receipt-line">
                    <span>Appointment:</span>
                    <span>{{ $transaction['appointment_time'] ?? 'TEST TIME' }}</span>
                </div>
                <div class="receipt-line">
                    <span>Room:</span>
                    <span>{{ $transaction['room_number'] ?? 'TEST ROOM' }}</span>
                </div>
                <div class="small-text center-text">Please proceed to the assigned doctor</div>
                
                <!-- Debug Info -->
                <div style="margin-top: 10px; font-size: 8px; color: red;">
                    DEBUG: Doctor = {{ $transaction['assigned_doctor'] ?? 'NULL' }}<br>
                    Time = {{ $transaction['appointment_time'] ?? 'NULL' }}<br>
                    Room = {{ $transaction['room_number'] ?? 'NULL' }}
                </div>
            </div>
            
            <!-- Dotted separator -->
            <div class="dotted-line"></div>
            
            <!-- Thank you message -->
            <div class="thank-you">
                <div>Thank you for choosing</div>
                <div><strong>Mary Angels Diagnostic Clinic</strong></div>
                <div class="small-text">This serves as your official receipt</div>
            </div>
            
            <!-- Barcode simulation -->
            <div class="barcode-section">
                <div class="barcode">|||| || ||| | |||| | ||| ||||</div>
                <div class="barcode-number">{{ $transaction['receipt_number'] ?? 'RCP-0001' }}</div>
            </div>
        </div>
    </div>

    <script>
        // Force clean the page and auto-print when page loads
        window.onload = function() {
            // Remove any unwanted elements
            const unwantedSelectors = [
                '.sidebar', '.nav', '.navbar', '.header', '.menu', '.app-sidebar', 
                '.main-sidebar', '.content-wrapper', '.wrapper', '.layout', 
                '.app-layout', '#app', '.app', 'aside', 'nav', 'header', 
                '.main-header', '.navbar-nav', '.sidebar-wrapper'
            ];
            
            unwantedSelectors.forEach(selector => {
                const elements = document.querySelectorAll(selector);
                elements.forEach(el => el.remove());
            });
            
            // Ensure body only contains receipt
            const receiptContainer = document.querySelector('.receipt-container');
            if (receiptContainer) {
                // Clone the receipt container
                const clone = receiptContainer.cloneNode(true);
                // Clear body
                document.body.innerHTML = '';
                // Add back only the receipt
                document.body.appendChild(clone);
                // Apply receipt styles to body
                document.body.style.cssText = 'margin:0!important;padding:20px!important;font-family:"Courier New",monospace!important;background:#f5f5f5!important;';
            }
            
            // Auto-print
            setTimeout(function() {
                window.print();
            }, 500);
            
            // Close window after printing (optional)
            // setTimeout(function() {
            //     window.close();
            // }, 2000);
        }
    </script>
</body>
</html>
