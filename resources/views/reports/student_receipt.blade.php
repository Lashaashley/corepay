<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Payment Statement</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        
        .header {
            position: relative;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding: 15px 0 20px 0;
            background-color: #f8f9fa;
            min-height: 80px;
        }
        
        .header-content {
            display: table;
            width: 100%;
            table-layout: fixed;
        }
        
        .logo-section {
            display: table-cell;
            width: 100px;
            vertical-align: top;
            padding: 5px 0 0 15px;
        }
        
        .logo {
            width: 100px;
            height: 100px;
            display: block;
            border: 1px solid #007bff;
            border-radius: 8px;
            object-fit: contain;
            background-color: white;
            padding: 3px;
        }
        
        .school-details {
            display: table-cell;
            vertical-align: top;
            padding: 8px 15px 0 20px;
        }
        
        .school-name {
            font-size: 22px;
            font-weight: bold;
            color: #003366;
            margin: 0 0 6px 0;
            line-height: 1.2;
        }
        
        .school-motto {
            font-style: italic;
            color: #666;
            margin: 0 0 8px 0;
            font-size: 12px;
            line-height: 1.3;
        }
        
        .school-contact {
            font-size: 10px;
            color: #555;
            line-height: 1.4;
            margin: 0;
        }
        
        .statement-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 5px 0;
            color: #007bff;
            text-transform: uppercase;
        }
        .content {
    padding: 0 30px; /* Adjust margin size for A4 */
}

        
        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .info-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        
        .info-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            text-align: center;
        }
        
        .info-block {
            margin-bottom: 5px;
        }
        
        .info-label {
            font-weight: bold;
            color: #555;
        }
        
        .info-value {
            color: #333;
        }
        
       
        
        .receipts-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .receipts-table th {
            background-color: #007bff;
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
        }
        
        .receipts-table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            font-size: 11px;
        }
        
        .receipts-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .amount {
            text-align: left;
            font-weight: bold;
        }
        
        .receipt-no {
            font-weight: bold;
            color: #007bff;
        }
        
        .payment-method {
            background-color: #17a2b8;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
        }
        
        /* Total Amount Paid Row (Green) */
.total-row {
    background-color: #28a745 !important;
    color: white;
    font-weight: bold;
}

.total-row td {
    padding: 12px 8px;
    border-bottom: none;
}

/* Balance Row (Custom Color - e.g., Blue for balances) */
.balance-row {
    background-color:rgb(241, 74, 74) !important; /* Blue shade */
    color: white;
    font-weight: bold;
}

.balance-row td {
    padding: 12px 8px;
    border-bottom: none;
}
        
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
            text-align: center;
        }
        
        .no-receipts {
            text-align: center;
            padding: 40px 20px;
            color: #666;
            font-style: italic;
        }
        
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="header-content">
            <!-- Logo Section -->
            <div class="logo-section">
                @if($school && $school->logo)
                    <img src="{{ public_path('storage/' . $school->logo) }}" alt="School Logo" class="logo">
                @endif
            </div>
            
            <!-- School Details Section -->
            <div class="school-details">
                @if($school)
                    <div class="school-name">{{ $school->name ?? 'School Name' }}</div>
                    
                    @if($school->motto)
                        <div class="school-motto">"{{ $school->motto }}"</div>
                    @endif
                    
                    <div class="school-contact">
                @if($school->pobox) P.O. Box {{ $school->pobox }} @endif
                @if($school->physaddres) | {{ $school->physaddres }} @endif
                @if($school->email) | Email: {{ $school->email }} @endif
            </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Statement Title -->
    <div class="statement-title">Payment Receipt</div>

    <!-- Student & Period Information -->
     <div class="content">
    <div class="info-section">
        <div class="info-left">
            <div class="info-block">
                <span class="info-label">Student Name:</span>
                <span class="info-value">
                    {{ trim(($student->fname ?? '') . ' ' . ($student->sirname ?? '') . ' ' . ($student->othername ?? '')) }}
                </span>
            </div>
            
            <div class="info-block">
                <span class="info-label">Admission Number:</span>
                <span class="info-value">{{ $student->admno ?? 'N/A' }}</span>
            </div>
            
            <div class="info-block">
                <span class="info-label">Class:</span>
                <span class="info-value">
                    {{ ($student->claname ?? 'N/A') . (isset($student->streamname) ? ' - ' . $student->streamname : '') }}
                </span>
            </div>
        </div>
        
        <div class="info-right">
            <div class="info-block">
                <span class="info-label">Academic Period:</span>
                <span class="info-value">{{ $period->periodname ?? 'N/A' }}</span>
            </div>
            
            <div class="info-block">
                <span class="info-label">Statement Date:</span>
                <span class="info-value">{{ $generatedDate->format('d/m/Y H:i') }}</span>
            </div>
            
            <div class="info-block">
                <span class="info-label">Status:</span>
                <span class="info-value">{{ $period->pstatus ?? 'N/A' }}</span>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    

    <!-- Receipts Table -->
    @if($receipts && $receipts->count() > 0)
        <table class="receipts-table">
            <thead>
                <tr>
                    <th style="width: 8%">#</th>
                    <th style="width: 18%">Receipt No.</th>
                    <th style="width: 15%">Date</th>
                    <th style="width: 18%">Amount</th>
                    <th style="width: 18%">Payment Method</th>
                    <th style="width: 23%">Reference</th>
                </tr>
            </thead>
            <tbody>
                @foreach($receipts as $index => $receipt)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="receipt-no">{{ $receipt->receiptno }}</td>
                        <td>{{ $receipt->receiptdate}}</td>
                        <td class="amount">KSh {{ number_format($receipt->amount, 2) }}</td>
                        <td>
                            <span class="payment-method">
                                {{ $receipt->payment_method ?? 'N/A' }}
                            </span>
                        </td>
                        <td style="font-size: 10px;">
                            @if($receipt->tcode)
                                Code: {{ $receipt->tcode }}
                            @elseif($receipt->chequeno)
                                Cheque: {{ $receipt->chequeno }}
                                @if($receipt->bankn) ({{ $receipt->bankn }}) @endif
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endforeach
                
                <!-- Total Row -->
                <!-- Total Amount Paid Row -->
<tr class="total-row">
    <td colspan="3"><strong>TOTAL AMOUNT PAID</strong></td>
    <td class="amount"><strong>KSh {{ number_format($totalAmount, 2) }}</strong></td>
    <td colspan="2"></td>
</tr>

<!-- Balance As of Receipt Row (New) -->
<tr class="balance-row">
    <td colspan="3"><strong>BALANCE AS OF THIS RECEIPT</strong></td>
    <td class="amount"><strong>KSh {{ number_format($receipt->balanceasof, 2) }}</strong></td>
    <td colspan="2"></td>
</tr>
            </tbody>
        </table>
    @else
        <div class="no-receipts">
            <p>No payment receipts found for this student in the current academic period.</p>
        </div>
    @endif
     </div>

    <!-- Footer -->
    <div class="footer">
        <p>This is a computer-generated statement. For any queries, please contact the school administration.</p>
        <p>Generated on {{ $generatedDate->format('l, F j, Y \a\t g:i A') }}</p>
    </div>
</body>
</html>