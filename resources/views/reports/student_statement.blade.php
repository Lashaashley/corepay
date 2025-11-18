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
            margin-bottom: 5px;
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
            margin: 2% 0;
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
            text-align: left;
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
        .payment-details {
    vertical-align: top; /* Align content to the top of the cell */
}
        
        .payment-method {
            background-color: #17a2b8;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
        }
        .payment-details small {
    color: #666;         /* Gray out secondary details */
    font-size: 10px;     /* Match original small font */
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
        .text-danger { color: #dc3545; }  /* Red for negative */
.text-success { color:rgb(250, 250, 250); } /* Green for positive */
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
    <div class="statement-title">Fee Statement</div>

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
                <span class="info-label">Admission Date:</span><
                <span class="info-value">{{ $student->admdate}}</span>
            </div>
            <div class="info-block">
                <span class="info-label">Academic Period:</span><
                <span class="info-value">{{ $period->periodname ?? 'N/A' }}</span>
            </div>
            
            <div class="info-block">
                <span class="info-label">Statement Date:</span>
                <span class="info-value">{{ $generatedDate->format('d/m/Y H:i') }}</span>
            </div>
            
            
        </div>
    </div>

    <!-- Summary Cards -->
    @if($invoinces && $invoinces->count() > 0)
        <table class="receipts-table">
            <thead>
                <tr>
                    <th style="width: 8%">#</th>
                    <th style="width: 18%">Date.</th>
                    <th style="width: 10%">Ref</th>
                    <th style="width: 25%">Description</th>
                    <th style="width: 18%">Amount(Ksh)</th>
                    <th style="width: 18%">Paid(Ksh)</th>
                    <th style="width: 18%">Balance(Ksh)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoinces as $index => $invoince)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($invoince->created_at)->format('d/M/Y') }}</td>
                        <td class="receipt-no">{{ $invoince->ID }}</td>
                        <td>{{ $invoince->feedesc}}</td>
                       
                        <td class="amount">{{ number_format($invoince->amount, 2) }}</td>
                        <td class="amount">{{ number_format($invoince->paid, 2) }}</td>
                        <td class="amount">{{ number_format($invoince->balance, 2) }}</td>
                    </tr>
                @endforeach
                
                <!-- Total Row -->
                <!-- Total Amount Paid Row -->
<tr class="total-row">
    <td colspan="4"><strong>TOTALS</strong></td>
    <td class="amount"><strong>{{ number_format($totalfee, 2) }}</strong></td>
     <td class="amount"><strong>{{ number_format($totalpaid, 2) }}</strong></td>
      <td class="amount"><strong>{{ number_format($totalbal, 2) }}</strong></td>
   
</tr>


            </tbody>
        </table>
    @else
        <div class="no-receipts">
            <p>No payment receipts found for this student in the current academic period.</p>
        </div>
    @endif
     </div>

    <!-- Receipts Table -->
    @if($receipts && $receipts->count() > 0)
        <table class="receipts-table">
            <thead>
                <tr>
                    <th style="width: 5%">#</th>
                    <th style="width: 18%">Date.</th>
                    <th style="width: 20%">Ref</th>
                    <th style="width: 25%">Description</th>
                    <th style="width: 15%">Debit(Ksh)</th>
                    <th style="width: 15%">Credit(Ksh)</th>
                    <th style="width: 15%">Balance(Ksh)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                        <td>{{ $generatedDate->format('d/M/Y') }}</td>
                        <td class="receipt-no">{{ ($student->admno ?? 'N/A') . ' - ' . ($period->periodname ?? 'N/A') }}</td>
                        <td>STANDARD INVOICE</td>
                        <td class="amount">{{ number_format($totalfee, 2) }}</td>
                        <td class="amount">0.00</td>
                        <td class="amount">0.00</td>
                    </tr>
                @foreach($receipts as $index => $receipt)

                
                    <tr>
                        <td>{{ $index + 2 }}</td>
                        <td>{{ $receipt->receiptdate}}</td>
                        <td class="receipt-no">{{ $receipt->receiptno }}</td>
                        <td class="payment-details">
                            <span class="payment-method">
                                {{ $receipt->payment_method ?? 'N/A' }}
                            </span>
                            @if($receipt->tcode)
                            <br><small>Code: {{ $receipt->tcode }}</small>
                            @elseif($receipt->chequeno)
                            <br><small>
                                Cheque: {{ $receipt->chequeno }}
                                @if($receipt->bankn) ({{ $receipt->bankn }}) @endif
                            </small>
                            @endif
                        </td>
                       
                        
                        <td class="amount">0.00</td>
                        <td class="amount">{{ number_format($receipt->amount, 2) }}</td>
                        <td class="amount">{{ number_format($receipt->balanceasof, 2) }}</td>
                        
                    </tr>
                @endforeach
                
                <!-- Total Row -->
                <!-- Total Amount Paid Row -->
<tr class="total-row">
    <td colspan="4"><strong>TOTALS</strong></td>
    <td class="amount"><strong>{{ number_format($totalfee, 2) }}</strong></td>
    <td class="amount"><strong>{{ number_format($totalAmount, 2) }}</strong></td>
    <td class="amount {{ ($totalfee - $totalAmount < 0) ? 'text-danger' : 'text-success' }}">
    <strong>{{ number_format($totalfee - $totalAmount, 2) }}</strong>
</td>
    
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