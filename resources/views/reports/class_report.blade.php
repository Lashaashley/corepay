<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Class Performance Report</title>
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
        }

        .report-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 11px;
        }

        .report-info div {
            flex: 1;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 9px;
        }

        th, td {
            border: 1px solid #333;
            padding: 4px 2px;
            text-align: center;
            word-wrap: break-word;
        }

        th {
            background-color: #e0e0e0;
            font-weight: bold;
            font-size: 9px;
        }

        .rank-col {
            width: 35px;
        }

        .admno-col {
            width: 50px;
        }

        .name-col {
            width: 120px;
            text-align: left;
            padding-left: 5px;
        }

        .stream-col {
            width: 60px;
        }

        .marks-col {
            width: 45px;
        }

        .average-col {
            width: 50px;
        }

        .grade-col {
            width: 35px;
        }

        .subject-col {
            width: 50px;
        }

        .join-col {
            width: 45px;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 9px;
            border-top: 1px solid #333;
            padding-top: 10px;
        }

        .rotate-text {
            writing-mode: vertical-rl;
            text-orientation: mixed;
            padding: 5px 2px;
        }

        tbody tr:nth-child(odd) {
            background-color: #f9f9f9;
        }

        tbody tr:hover {
            background-color: #f0f0f0;
        }

        .subject-header {
            min-width: 45px;
            max-width: 50px;
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
        <div><strong>Total Students:</strong> {{ count($studentsData) }}</div>
        <div><strong>Generated:</strong> {{ $generatedDate->format('d/m/Y H:i') }}</div>
    </div>

    <!-- Performance Table -->
    <table>
        <thead>
            <tr>
                <th class="rank-col">Class<br>Rank</th>
                <th class="rank-col">Stream<br>Rank</th>
                <th class="marks-col">Total<br>Marks</th>
                <th class="average-col">Average<br>Marks</th>
                <th class="grade-col">Grade</th>
                <th class="admno-col">Adm No</th>
                <th class="name-col">Student Name</th>
                <th class="stream-col">Stream</th>
                
                <!-- Subject Columns -->
                @foreach($subjects as $subject)
                    <th class="subject-header">{{ $subject->scode ?? $subject->sname }}</th>
                @endforeach
                
                <!-- Join Rank Columns -->
                <th class="join-col">Join<br>Marks</th>
                <th class="rank-col">Join<br>Rank</th>
            </tr>
        </thead>
        <tbody>
            @forelse($studentsData as $student)
                <tr>
                    <td class="rank-col">{{ $student['class_rank'] > 0 ? $student['class_rank'] : '-' }}</td>
                    <td class="rank-col">{{ $student['stream_rank'] > 0 ? $student['stream_rank'] : '-' }}</td>
                    <td class="marks-col">{{ $student['total_marks'] > 0 ? $student['total_marks'] : '-' }}</td>
                    <td class="average-col">{{ $student['average_marks'] > 0 ? number_format($student['average_marks'], 1) : '-' }}</td>
                    <td class="grade-col">{{ $student['average_grade'] ?: '-' }}</td>
                    <td class="admno-col">{{ $student['admno'] }}</td>
                    <td class="name-col">{{ $student['student_name'] }}</td>
                    <td class="stream-col">{{ $student['stream_name'] }}</td>
                    
                    <!-- Subject Marks -->
                    @foreach($subjects as $subject)
                        <td class="subject-col">
                            @if(isset($student['subjects'][$subject->ID]))
                                {{ $student['subjects'][$subject->ID]['display'] }}
                            @else
                                -
                            @endif
                        </td>
                    @endforeach
                    
                    <!-- Join Rank Data -->
                    <td class="join-col">{{ $student['join_marks'] ?? '-' }}</td>
                    <td class="rank-col">{{ $student['join_rank'] ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ 8 + count($subjects) + 2 }}" style="text-align: center; padding: 20px;">
                        No student data available
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <div style="margin-bottom: 5px;">
            <strong>Legend:</strong> Subject columns show marks followed by grade (e.g., "85 A")
        </div>
        <div>
            Generated on {{ $generatedDate->format('l, F j, Y \a\t g:i A') }}
        </div>
    </div>
</body>
</html>