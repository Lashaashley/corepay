<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Subject Analysis Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            padding: 15px;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
        }

        .logo-section img.logo {
            height: 60px;
            width: auto;
        }

        .school-details {
            text-align: center;
        }

        .school-name {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .school-motto {
            font-size: 11px;
            font-style: italic;
            color: #555;
            margin-bottom: 5px;
        }

        .school-contact {
            font-size: 9px;
            color: #666;
        }

        .report-title {
            font-size: 14px;
            font-weight: bold;
            margin: 10px 0;
            color: #333;
        }

        .report-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 9px;
        }

        th, td {
            border: 1px solid #333;
            padding: 5px 3px;
            text-align: center;
        }

        th {
            background-color: #FFFF00;
            font-weight: bold;
            font-size: 9px;
        }

        .subject-header {
            background-color: #FFFF00;
            font-weight: bold;
            text-align: left;
            padding-left: 10px;
            font-size: 10px;
        }

        .stream-col {
            width: 60px;
            text-align: center;
        }

        .entries-col {
            width: 50px;
        }

        .mean-col {
            width: 60px;
        }

        .grade-col {
            width: 35px;
        }

        .teacher-col {
            width: 120px;
            text-align: left;
            padding-left: 5px;
        }

        .total-row {
            background-color: #FFFF00;
            font-weight: bold;
        }

        .total-row td {
            background-color: #FFFF00;
        }

        .grand-total-row {
            background-color: #90EE90;
            font-weight: bold;
            font-size: 10px;
        }

        .grand-total-row td {
            background-color: #90EE90;
        }

        tbody tr:nth-child(even):not(.total-row) {
            background-color: #f9f9f9;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 9px;
            border-top: 1px solid #333;
            padding-top: 10px;
        }

        .subject-title {
            text-align: left;
            padding-left: 5px;
        }

        .number-cell {
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Header Section -->
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
                        <div class="school-name">{{ $school->schname ?? 'School Name' }}</div>
                        
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

    <!-- Report Information -->
    <div class="report-info">
        <div><strong>Class:</strong> {{ $classInfo->claname ?? 'N/A' }}</div>
        <div><strong>Exam:</strong> {{ $examtype ?? 'N/A' }}</div>
        <div><strong>Generated:</strong> {{ $generatedDate->format('d/m/Y H:i') }}</div>
    </div>

    <!-- Analysis Table -->
    <table>
        <thead>
            <tr>
                <th class="subject-col" style="width: 100px;">SUBJECT</th>
                <th class="stream-col">STREAM</th>
                <th class="entries-col">ENT</th>
                <th class="mean-col">MEAN</th>
                
                <!-- Grade columns -->
                @foreach($grades as $grade)
                    <th class="grade-col">{{ $grade->Grade }}</th>
                @endforeach
                
                <th class="teacher-col">TEACHER</th>
            </tr>
        </thead>
        <tbody>
            @foreach($subjectAnalysis as $subjectIndex => $subject)
                <!-- Subject streams -->
                @foreach($subject['streams'] as $streamIndex => $stream)
                    <tr>
                        @if($streamIndex === 0)
                            <td class="subject-title" rowspan="{{ count($subject['streams']) + 1 }}">
                                <strong>{{ strtoupper($subject['subject_name']) }}</strong>
                            </td>
                        @endif
                        <td class="stream-col">{{ $stream['stream_name'] }}</td>
                        <td class="entries-col number-cell">{{ $stream['total_entries'] }}</td>
                        <td class="mean-col number-cell">{{ number_format($stream['mean'], 4) }} {{ $stream['mean_grade'] }}</td>
                        
                        <!-- Grade distribution -->
                        @foreach($grades as $grade)
                            <td class="grade-col number-cell">{{ $stream['grade_distribution'][$grade->Grade] ?? 0 }}</td>
                        @endforeach
                        
                        <td class="teacher-col">{{ strtoupper($stream['teacher_name']) }}</td>
                    </tr>
                @endforeach
                
                <!-- Subject Total Row -->
                <tr class="total-row">
                    <td class="stream-col"><strong>TOTAL</strong></td>
                    <td class="entries-col number-cell"><strong>{{ $subject['totals']['students_count'] }}</strong></td>
                    <td class="mean-col number-cell"><strong>{{ number_format($subject['totals']['mean'], 4) }} {{ $subject['totals']['mean_grade'] }}</strong></td>
                    
                    <!-- Total grade distribution -->
                    @foreach($grades as $grade)
                        <td class="grade-col number-cell"><strong>{{ $subject['totals']['grade_distribution'][$grade->Grade] ?? 0 }}</strong></td>
                    @endforeach
                    
                    <td class="teacher-col"></td>
                </tr>
            @endforeach

            <!-- Grand Total Row -->
            <tr class="grand-total-row">
                <td colspan="2" style="text-align: center;"><strong>GRAND TOTAL</strong></td>
                <td class="entries-col number-cell"><strong>{{ $grandTotals['total_entries'] }}</strong></td>
                <td class="mean-col number-cell"><strong>{{ number_format($grandTotals['mean'], 4) }} {{ $grandTotals['mean_grade'] }}</strong></td>
                
                <!-- Grand total grade distribution -->
                @foreach($grades as $grade)
                    <td class="grade-col number-cell"><strong>{{ $grandTotals['grade_distribution'][$grade->Grade] ?? 0 }}</strong></td>
                @endforeach
                
                <td class="teacher-col"></td>
            </tr>
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <div style="margin-bottom: 5px;">
            <strong>Legend:</strong> ENT = Number of Entries | MEAN = Average Score with Grade
        </div>
        <div>
            Generated on {{ $generatedDate->format('l, F j, Y \a\t g:i A') }}
        </div>
    </div>
</body>
</html>