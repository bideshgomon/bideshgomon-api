{{-- resources/views/pdf/cv_template.blade.php --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $user->name }}'s CV</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            font-size: 12px;
            line-height: 1.6;
        }
        .container {
            width: 100%;
            margin: 0 auto;
        }
        h1 {
            font-size: 28px;
            margin-bottom: 0;
            color: #222;
        }
        h2 {
            font-size: 18px;
            border-bottom: 2px solid #3B82F6; /* Your primary color */
            padding-bottom: 5px;
            margin-top: 20px;
            margin-bottom: 10px;
            color: #3B82F6;
        }
        .header p {
            font-size: 14px;
            margin: 2px 0;
        }
        .section {
            margin-bottom: 20px;
        }
        .item {
            margin-bottom: 15px;
        }
        .item-header {
            font-size: 14px;
            font-weight: bold;
            color: #000;
        }
        .item-sub {
            font-size: 12px;
            font-style: italic;
            color: #555;
            margin: 2px 0;
        }
        .item-dates {
            float: right;
            font-style: italic;
            color: #555;
        }
        .skills-list {
            padding-left: 0;
            list-style-type: none;
            margin: 0;
        }
        .skills-list li {
            display: inline-block;
            background-color: #E0E7FF; /* Light blue */
            color: #3B82F6; /* Primary color */
            padding: 5px 10px;
            border-radius: 15px;
            margin: 3px;
            font-size: 12px;
        }
        .clearfix {
            clear: both;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header section">
            <h1>{{ $user->name }}</h1>
            <p>{{ $user->email }}</p>
            @if($user->profile)
                <p>{{ $user->profile->phone ?? '' }}</p>
                <p>{{ $user->profile->address ?? '' }}</t>
            @endif
        </div>

        @if($user->profile && $user->profile->bio)
        <div class="section">
            <h2>Summary</h2>
            <p>{{ $user->profile->bio }}</p>
        </div>
        @endif

        @if($user->experiences->isNotEmpty())
        <div class="section">
            <h2>Work Experience</h2>
            @foreach($user->experiences as $exp)
                <div class="item">
                    <span class="item-dates">
                        {{ \Carbon\Carbon::parse($exp->start_date)->format('M Y') }} - 
                        {{ $exp->is_current ? 'Present' : \Carbon\Carbon::parse($exp->end_date)->format('M Y') }}
                    </span>
                    <div class="item-header">{{ $exp->title }}</div>
                    <div class="item-sub">{{ $exp->company }} | {{ $exp->location }}</div>
                    <p>{{ $exp->description }}</p>
                </div>
            @endforeach
        </div>
        @endif

        @if($user->educations->isNotEmpty())
        <div class="section">
            <h2>Education</h2>
            @foreach($user->educations as $edu)
                <div class="item">
                    <span class="item-dates">
                        {{ \Carbon\Carbon::parse($edu->start_date)->format('M Y') }} - 
                        {{ $exp->is_current ? 'Present' : \Carbon\Carbon::parse($edu->end_date)->format('M Y') }}
                    </span>
                    <div class="item-header">{{ $edu->degree }}</div>
                    <div class="item-sub">{{ $edu->school }} | {{ $edu->location }}</div>
                </div>
            @endforeach
        </div>
        @endif

        @if($user->skills->isNotEmpty())
        <div class="section">
            <h2>Skills</h2>
            <ul class="skills-list">
                @foreach($user->skills as $skill)
                    <li>{{ $skill->name }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        
        <div class="clearfix"></div>
    </div>
</body>
</html>