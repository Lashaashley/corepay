<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Termly Report - {{ $student->admno }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
            background-color: #fff;
        }

        .report-container {
            max-width: 100%;
            margin: 0 auto;
            padding: 10px;
        }

        /* Header Styles */
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

        /* Report Title */
        .report-title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin: 10px 0;
            text-transform: uppercase;
        }

        /* Student Info */
        .student-info {
            margin-bottom: 15px;
            border: 1px solid #ddd;
            padding: 8px;
            background-color: #f9f9f9;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
            font-size: 10px;
        }

        .info-item {
            flex: 1;
            display: flex;
        }

        .info-label {
            font-weight: bold;
            margin-right: 5px;
            min-width: 80px;
        }

        .info-value {
            flex: 1;
        }

        /* Performance Table */
        .performance-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 9px;
        }

        .performance-table th,
        .performance-table td {
            border: 1px solid #333;
            padding: 3px;
            text-align: center;
            vertical-align: middle;
        }

        .performance-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 8px;
        }

        .performance-table td.subject-name {
            text-align: left;
            padding-left: 5px;
        }

        .performance-table td.teacher-name {
            text-align: left;
            padding-left: 3px;
            font-size: 8px;
        }

        /* Grade Colors */
        .grade-A { background-color: #d4edda; color: #155724; }
        .grade-B { background-color: #d1ecf1; color: #0c5460; }
        .grade-C { background-color: #fff3cd; color: #856404; }
        .grade-D { background-color: #f8d7da; color: #721c24; }
        .grade-E { background-color: #f8d7da; color: #721c24; }
        .grade-F { background-color: #f5c6cb; color: #721c24; }

        /* Summary Section */
        .summary-section {
            margin-top: 10px;
            border: 1px solid #333;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }

        .summary-table th,
        .summary-table td {
            border: 1px solid #333;
            padding: 4px;
            text-align: center;
        }

        .summary-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        /* Position Summary */
        .position-summary {
            background-color: #e9ecef;
            padding: 8px;
            margin: 10px 0;
            text-align: center;
            font-weight: bold;
            font-size: 11px;
        }

        /* Comments Section */
        .comments-section {
            margin-top: 20px;
        }

        .comment-box {
            border: 1px solid #333;
            height: 40px;
            margin-bottom: 10px;
            padding: 5px;
        }

        .comment-title {
            font-weight: bold;
            margin-bottom: 3px;
            font-size: 10px;
        }

        /* Footer */
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        /* Performance Chart Placeholder */
        .chart-section {
            height: 100px;
            border: 1px solid #ddd;
            margin: 10px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
            font-style: italic;
            color: #666;
        }

        @media print {
            .report-container {
                padding: 5px;
            }
            
            body {
                font-size: 9px;
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

        <!-- Report Title -->
        <div class="report-title">
            YEAR: {{ date('Y') }} TERM: {{ $period->periodname ?? 'Current Term' }} EXAM: REPORT FORM
        </div>

        <!-- Student Information -->
        <div class="student-info">
            <div class="info-row">
                <div class="info-item">
                    <span class="info-label">ADMNO:</span>
                    <span class="info-value">{{ $student->admno }} | UPI:</span>
                </div>
                <div class="info-item" style="text-align: center;">
                    <span class="info-label">NAME:</span>
                    <span class="info-value">{{ strtoupper($studentFullName) }}</span>
                </div>
                <div class="info-item" style="text-align: right;">
                    <span class="info-label">CLASS:</span>
                    <span class="info-value">{{ $student->claname ?? 'N/A' }} | KCPE MRKS:</span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-item">
                    <span class="info-label">TOTAL MARKS</span>
                </div>
                <div class="info-item" style="text-align: center;">
                    <span class="info-label">MEAN GRADE</span>
                </div>
                <div class="info-item" style="text-align: right;">
                    <span class="info-label">TOTAL POINTS</span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-item">
                    <strong>{{ $termTotalMarks }}/{{ count($performanceByExam) * 100 * $termSubjectsCount / count($performanceByExam) }} DEV [{{ $termTotalMarks > 0 ? '+' : '' }}{{ $termTotalMarks - 500 }}]</strong>
                </div>
                <div class="info-item" style="text-align: center;">
                    @php
                        $overallGrade = '';
                        if($termAverageMarks >= 80) $overallGrade = 'A';
                        elseif($termAverageMarks >= 70) $overallGrade = 'B';
                        elseif($termAverageMarks >= 60) $overallGrade = 'C';
                        elseif($termAverageMarks >= 50) $overallGrade = 'D';
                        elseif($termAverageMarks >= 40) $overallGrade = 'E';
                        else $overallGrade = 'F';
                    @endphp
                    <strong>GRADE: {{ $overallGrade }} - PTS: {{ round($termAverageMarks/10, 1) }} MRKS: {{ $termAverageMarks }}%</strong>
                </div>
                <div class="info-item" style="text-align: right;">
                    @php
                        $totalPoints = 0;
                        foreach($performanceByExam as $examRecords) {
                            $totalPoints += $examRecords->sum('points');
                        }
                    @endphp
                    <strong>{{ $totalPoints }}/{{ count($performanceByExam) * 12 * $termSubjectsCount / count($performanceByExam) }} DEV [{{ $totalPoints > 0 ? '+' : '' }}{{ $totalPoints - 84 }}]</strong>
                </div>
            </div>
            <div class="info-row">
                <div class="info-item">
                    <span class="info-label">CLASS POSITION</span>
                </div>
                <div class="info-item" style="text-align: center;">
                    <span class="info-label">STREAM POSITION</span>
                </div>
                <div class="info-item" style="text-align: right;">
                    <span class="info-label">KCPE POSITION</span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-item">
                    <strong>{{ $termPosition['position'] ?? 'N/A' }} OUT OF {{ $termPosition['out_of'] ?? 'N/A' }}</strong>
                </div>
                <div class="info-item" style="text-align: center;">
                    <strong>{{ $termPosition['position'] ?? 'N/A' }} OUT OF {{ $termPosition['out_of'] ?? 'N/A' }}</strong>
                </div>
                <div class="info-item" style="text-align: right;">
                    <strong>{{ $termPosition['out_of'] ?? 'N/A' }} OUT OF {{ $termPosition['out_of'] ?? 'N/A' }}</strong>
                </div>
            </div>
        </div>

        <!-- Main Performance Table -->
        @php
            // Get all unique subjects
            $allSubjects = collect();
            foreach($performanceByExam as $examRecords) {
                $allSubjects = $allSubjects->merge($examRecords);
            }
            $uniqueSubjects = $allSubjects->groupBy('subid');
            
            // Get exam types for headers
            $examTypes = [];
            foreach($performanceByExam as $examcount => $examRecords) {
                $examTypes[$examcount] = $examRecords->first()->exam_name ?? "Exam $examcount";
            }
        @endphp

        <table class="performance-table">
            <thead>
                <tr>
                    <th style="width: 8%;">CODE</th>
                    <th style="width: 20%;">SUBJECT</th>
                    @foreach($examTypes as $examcount => $examName)
                        <th style="width: 10%;">{{ strtoupper(str_replace(['CAT ', 'EXAM'], ['CAT', ''], $examName)) }}</th>
                    @endforeach
                    <th style="width: 8%;">TOTAL</th>
                    <th style="width: 8%;">GRADE</th>
                    <th style="width: 6%;">DEV</th>
                    <th style="width: 10%;">RANK</th>
                    <th style="width: 10%;">REMARKS</th>
                    <th style="width: 20%;">TEACHER</th>
                </tr>
            </thead>
            <tbody>
                @foreach($uniqueSubjects as $subjectId => $subjectRecords)
                    @php
                        $firstRecord = $subjectRecords->first();
                        $subjectTotal = 0;
                        $subjectExams = [];
                        
                        // Organize marks by exam count
                        foreach($subjectRecords as $record) {
                            $subjectExams[$record->examcount] = $record;
                            $subjectTotal += $record->marks;
                        }
                        
                        $subjectAverage = count($subjectExams) > 0 ? round($subjectTotal / count($subjectExams), 0) : 0;
                        $subjectGrade = '';
                        if($subjectAverage >= 80) $subjectGrade = 'A';
                        elseif($subjectAverage >= 70) $subjectGrade = 'B';
                        elseif($subjectAverage >= 60) $subjectGrade = 'C';
                        elseif($subjectAverage >= 50) $subjectGrade = 'D';
                        elseif($subjectAverage >= 40) $subjectGrade = 'E';
                        else $subjectGrade = 'F';
                        
                        $deviation = $subjectAverage - 50; // Assuming 50% is the baseline
                        $rank = $subjectRecords->first()->subject_position ?? 'N/A';
                        $outOf = $subjectRecords->first()->subject_out_of ?? 'N/A';
                    @endphp
                    <tr>
                        <td>{{ $firstRecord->subject_code ?? 'N/A' }}</td>
                        <td class="subject-name">{{ strtoupper($firstRecord->subject_name ?? 'N/A') }}</td>
                        
                        @foreach($examTypes as $examcount => $examName)
                            <td class="{{ isset($subjectExams[$examcount]) ? 'grade-' . $subjectExams[$examcount]->grade : '' }}">
                                @if(isset($subjectExams[$examcount]))
                                    {{ $subjectExams[$examcount]->marks }}{{ $subjectExams[$examcount]->grade }}
                                @else
                                    -
                                @endif
                            </td>
                        @endforeach
                        
                        <td><strong>{{ $subjectAverage }}%</strong></td>
                        <td class="grade-{{ $subjectGrade }}"><strong>{{ $subjectGrade }}</strong></td>
                        <td><strong>{{ $deviation > 0 ? '+' : '' }}{{ $deviation }}</strong></td>
                        <td>{{ $rank }}/{{ $outOf }}</td>
                        <td>
                            @if($subjectGrade == 'A')
                                Excellent
                            @elseif($subjectGrade == 'B')
                                Very Good
                            @elseif($subjectGrade == 'C')
                                Good
                            @elseif($subjectGrade == 'D')
                                Satisfactory
                            @else
                                Needs Improvement
                            @endif
                        </td>
                        <td class="teacher-name">{{ $firstRecord->teacher_name ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary Statistics -->
        <div class="summary-section">
            <table class="summary-table">
                <thead>
                    <tr>
                        <th>EXAM</th>
                        @foreach($examTypes as $examcount => $examName)
                            <th>{{ strtoupper($examName) }}</th>
                        @endforeach
                        <th style="background-color: #d4edda;">OVERALL</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="font-weight: bold;">MARKS</td>
                        @foreach($examTypes as $examcount => $examName)
                            @php
                                $examTotal = isset($examPositions[$examcount]) ? $examPositions[$examcount]['total_marks'] : 0;
                                $maxPossible = isset($performanceByExam[$examcount]) ? $performanceByExam[$examcount]->count() * 100 : 100;
                                $percentage = $maxPossible > 0 ? round(($examTotal / $maxPossible) * 100) : 0;
                            @endphp
                            <td>{{ $examTotal }}/{{ $maxPossible }}({{ $percentage }}%)</td>
                        @endforeach
                        <td style="background-color: #d4edda; font-weight: bold;">
                            {{ $termTotalMarks }}/{{ count($examTypes) * $termSubjectsCount * 100 / count($examTypes) }}({{ round($termAverageMarks) }}%)
                        </td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">POINTS</td>
                        @foreach($examTypes as $examcount => $examName)
                            @php
                                $examPoints = isset($performanceByExam[$examcount]) ? $performanceByExam[$examcount]->sum('points') : 0;
                                $maxPoints = isset($performanceByExam[$examcount]) ? $performanceByExam[$examcount]->count() * 12 : 12;
                                $pointGrade = '';
                                if($examPoints >= $maxPoints * 0.9) $pointGrade = 'A';
                                elseif($examPoints >= $maxPoints * 0.7) $pointGrade = 'B+';
                                elseif($examPoints >= $maxPoints * 0.6) $pointGrade = 'B';
                                else $pointGrade = 'B-';
                            @endphp
                            <td>{{ round($examPoints, 1) }}{{ $pointGrade }}</td>
                        @endforeach
                        <td style="background-color: #d4edda; font-weight: bold;">{{ $overallGrade }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">POSITION</td>
                        @foreach($examTypes as $examcount => $examName)
                            <td>
                                {{ isset($examPositions[$examcount]) ? $examPositions[$examcount]['position'] : 'N/A' }}/{{ isset($examPositions[$examcount]) ? $examPositions[$examcount]['out_of'] : 'N/A' }}
                            </td>
                        @endforeach
                        <td style="background-color: #d4edda; font-weight: bold;">
                            {{ $termPosition['position'] ?? 'N/A' }}/{{ $termPosition['out_of'] ?? 'N/A' }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Performance Chart Placeholder -->
        <div class="chart-section">
            [Performance Trend Chart - Can be implemented with Chart.js or similar]
        </div>

        <!-- Comments Section -->
        <div class="comments-section">
            <div class="comment-title">Class Teacher's Comments:</div>
            <div class="comment-box">
                @php
                    $comment = '';
                    if($termAverageMarks >= 80) {
                        $comment = 'Excellent performance. Keep up the outstanding work!';
                    } elseif($termAverageMarks >= 70) {
                        $comment = 'Very good performance. Continue with the same effort.';
                    } elseif($termAverageMarks >= 60) {
                        $comment = 'Good performance. There is room for improvement.';
                    } elseif($termAverageMarks >= 50) {
                        $comment = 'Satisfactory performance. More effort needed.';
                    } else {
                        $comment = 'Needs significant improvement. Seek additional support.';
                    }
                @endphp
                {{ $comment }}
            </div>
            
            <div class="comment-title">Principal's Comments:</div>
            <div class="comment-box">
                <!-- Principal's comments -->
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            This report was generated electronically on {{ $generatedDate->format('l, F j, Y \a\t g:i A') }}<br>
            © {{ date('Y') }} {{ $school->schname ?? 'School Management System' }} - Student Academic Report
        </div>
    </div>
</body>
</html>