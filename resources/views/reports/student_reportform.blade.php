<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Report Card - {{ $student->admno }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            background: #fff;
        }
        
        .report-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
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
            font-size: 18px;
            font-weight: bold;
            color: #333;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .student-info {
            display: table;
            width: 100%;
            margin-bottom: 15px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label {
            display: table-cell;
            font-weight: bold;
            padding: 5px 10px 5px 0;
            width: 25%;
            color: #555;
        }
        
        .info-value {
            display: table-cell;
            padding: 5px 0;
            color: #333;
        }
        
        .performance-section {
            margin-bottom: 25px;
        }
        
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #2c5aa0;
            border-bottom: 2px solid #2c5aa0;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        
        .performance-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .performance-table th {
            background: #2c5aa0;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
        }
        
        .performance-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #e9ecef;
            font-size: 11px;
        }
        
        .performance-table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        .performance-table tbody tr:hover {
            background: #e3f2fd;
        }
        
        .marks-cell {
            text-align: center;
            font-weight: bold;
        }
        
        .grade-A { color: #28a745; }
        .grade-B { color: #17a2b8; }
        .grade-C { color: #ffc107; }
        .grade-D { color: #fd7e14; }
        .grade-E { color: #dc3545; }
        .grade-F { color: #6f42c1; }
        
        .summary-section {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .summary-card {
            display: table-cell;
            width: 50%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
            border-radius: 8px;
            margin-right: 10px;
        }
        
        .summary-card:last-child {
            margin-right: 0;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .summary-number {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .summary-label {
            font-size: 11px;
            opacity: 0.9;
        }
        
        
        
        .comments-section {
            margin-top: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }
        
        .comments-title {
            font-size: 14px;
            font-weight: bold;
            color: #2c5aa0;
            margin-bottom: 15px;
        }
        
        .comment-box {
            min-height: 60px;
            border: 1px solid #ddd;
            padding: 10px;
            background: white;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        
        .signatures {
            display: table;
            width: 100%;
            margin-top: 40px;
        }
        
        .signature-box {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 20px 10px;
        }
        
        .signature-line {
            border-top: 1px solid #333;
            margin-bottom: 5px;
            padding-top: 5px;
        }
        
        .signature-label {
            font-size: 10px;
            color: #666;
        }
        
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
        }
        
        .no-data {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 20px;
        }
        
        @media print {
            .report-container {
                padding: 0;
                box-shadow: none;
            }
            
            .performance-table {
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="report-container">
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

        <!-- Student Information -->
        <div class="student-info">
            <div class="info-row">
                <div class="info-label">Student Name:</div>
                <div class="info-value">{{ $studentFullName }}</div>
                <div class="info-label">Admission No:</div>
                <div class="info-value">{{ $student->admno }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Class:</div>
                <div class="info-value">{{ $student->claname ?? 'N/A' }} {{ $student->strmname ? '- ' . $student->strmname : '' }}</div>
                <div class="info-label">Academic Period:</div>
                <div class="info-value">{{ $period->periodname ?? 'Current Period' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Admission Date:</div>
                <div class="info-value">{{ $student->admdate ? \Carbon\Carbon::parse($student->admdate)->format('d/m/Y') : 'N/A' }}</div>
                <div class="info-label">Report Generated:</div>
                <div class="info-value">{{ $generatedDate->format('d/m/Y H:i') }}</div>
            </div>
        </div>

        <!-- Performance Summary -->
        <div class="performance-section">
    <div class="section-title">Overall Performance</div>
    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; font-size: 12px;">
        <strong>Overall Position:</strong> {{ $overallPosition }}/{{ $overallOutOf }}
    </div>
</div>


        <!-- Performance by Exam Type -->
        @foreach($performanceByExam as $examName => $examResults)
        <div class="performance-section">
            <div class="section-title">{{ $examName ?? 'Examination Results' }}</div>
            
            @if($examResults->count() > 0)
<table class="performance-table">
    <thead>
        <tr>
            <th style="width: 25%">Subject</th>
            <th style="width: 12%">Code</th>
            <th style="width: 10%">Marks</th>
            <th style="width: 10%">Grade</th>
            <th style="width: 10%">Points</th>
            <th style="width: 13%">Rank</th>
            <th style="width: 20%">Teacher</th>
        </tr>
    </thead>
    <tbody>
        @foreach($examResults as $record)
        <tr>
            <td>{{ $record->subject_name ?? 'N/A' }}</td>
            <td>{{ $record->subject_code ?? 'N/A' }}</td>
            <td class="marks-cell">{{ $record->marks }}</td>
            <td class="marks-cell grade-{{ $record->grade }}">{{ $record->grade }}</td>
            <td class="marks-cell">{{ $record->points }}</td>
            <td class="marks-cell">
                {{ $record->subject_position }}/{{ $record->subject_out_of }}
            </td>
            <td>{{ trim($record->teacher_name) ?: 'N/A' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

                
                <div style="text-align: right; margin-bottom: 20px; font-weight: bold;">
                    Total Marks: {{ $examResults->sum('marks') }} | 
                    Average: {{ round($examResults->avg('marks'), 2) }}% |
                    Total Points: {{ $examResults->sum('points') }}
                </div>
            @else
                <div class="no-data">No performance data available for {{ $examName }}</div>
            @endif
        </div>
        @endforeach
        @if($classStats)
        <div class="performance-section">
            <div class="section-title">Class Performance Comparison</div>
            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; font-size: 11px;">
                <strong>Class Average:</strong> {{ round($classStats->class_average, 2) }}% | 
                <strong>Highest Mark:</strong> {{ $classStats->highest_mark }}% | 
                <strong>Lowest Mark:</strong> {{ $classStats->lowest_mark }}% | 
                <strong>Total Students:</strong> {{ $classStats->total_students }}
            </div>
        </div>
        @endif

        <!-- Comments Section -->
        <div class="comments-section">
            <div class="comments-title">Teacher's Comments</div>
            <div class="comment-box">
                <!-- Teacher comments can be added here -->
            </div>
            
            <div class="comments-title">Principal's Comments</div>
            <div class="comment-box">
                <!-- Principal comments can be added here -->
            </div>
        </div>

        <!-- Signatures -->
        <div class="signatures">
            <div class="signature-box">
                <div class="signature-line">Class Teacher</div>
                <div class="signature-label">Signature & Date</div>
            </div>
            <div class="signature-box">
                <div class="signature-line">Principal</div>
                <div class="signature-label">Signature & Date</div>
            </div>
            <div class="signature-box">
                <div class="signature-line">Parent/Guardian</div>
                <div class="signature-label">Signature & Date</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            This report was generated electronically on {{ $generatedDate->format('l, F j, Y \a\t g:i A') }}<br>
            © {{ date('Y') }} {{ $school->schname ?? 'School Management System' }}
        </div>
    </div>
</body>
</html>