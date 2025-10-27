<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CV - {{ $user->name }}</title>
    {{-- Basic Styling - You MUST expand this significantly --}}
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; line-height: 1.4; color: #333; font-size: 11pt; }
        h1, h2, h3 { margin-bottom: 0.5em; margin-top: 1.2em; color: #000; }
        h1 { font-size: 20pt; text-align: center; margin-bottom: 1em; }
        h2 { font-size: 14pt; border-bottom: 1px solid #ccc; padding-bottom: 0.2em; }
        h3 { font-size: 12pt; font-weight: bold; margin-bottom: 0.2em;}
        p { margin-top: 0; margin-bottom: 0.5em; }
        ul { padding-left: 20px; margin-top: 0.5em; list-style: disc;}
        li { margin-bottom: 0.3em; }
        .contact-info { text-align: center; margin-bottom: 1.5em; font-size: 10pt; color: #555;}
        .section { margin-bottom: 1.5em; }
        .item { margin-bottom: 1em; }
        .item-header { display: block; font-weight: bold; } /* Use block for dates on new line */
        .item-subheader { display: block; font-style: italic; color: #666; font-size: 10pt; margin-bottom: 0.3em; }
        .item-description { font-size: 10pt; margin-left: 15px; } /* Indent description */
        .skills-list span { display: inline-block; background-color: #eee; color: #333; padding: 3px 8px; margin: 2px; border-radius: 4px; font-size: 9pt; }

        /* Add more styles for layout, fonts, margins, etc. */
        /* Consider page breaks: .page-break { page-break-after: always; } */
    </style>
</head>
<body>

    <h1>{{ $user->name }}</h1>

    <div class="contact-info">
        @if($user->profile->phone) Phone: {{ $user->profile->phone }} | @endif
        Email: {{ $user->email }}
        @if($user->profile->address_line_1 || $user->profile->city || $user->profile->country)
            <br>
            Address: {{ implode(', ', array_filter([$user->profile->address_line_1, $user->profile->address_line_2, $user->profile->city, $user->profile->country])) }}
        @endif
        {{-- Add Website/Portfolio Link if available --}}
    </div>

    @if($user->profile->bio)
    <div class="section">
        <h2>Summary / Objective</h2>
        <p>{{ $user->profile->bio }}</p>
    </div>
    @endif

    @if($user->experiences->isNotEmpty())
    <div class="section">
        <h2>Work Experience</h2>
        @foreach($user->experiences->sortByDesc('start_date') as $exp)
            <div class="item">
                <span class="item-header">{{ $exp->designation }} at {{ $exp->company_name }}</span>
                <span class="item-subheader">
                    {{ \Carbon\Carbon::parse($exp->start_date)->format('M Y') }} -
                    {{ $exp->is_current ? 'Present' : ($exp->end_date ? \Carbon\Carbon::parse($exp->end_date)->format('M Y') : 'N/A') }}
                    {{-- Optionally add location: $exp->city, $exp->country?->name --}}
                </span>
                @if($exp->responsibilities)
                    {{-- Render responsibilities, maybe as a list if formatted --}}
                    <div class="item-description">{!! nl2br(e($exp->responsibilities)) !!}</div>
                @endif
            </div>
        @endforeach
    </div>
    @endif

     @if($user->educations->isNotEmpty())
    <div class="section">
        <h2>Education</h2>
        @foreach($user->educations->sortByDesc('start_date') as $edu)
            <div class="item">
                 <span class="item-header">{{ $edu->custom_degree ?? $edu->degree?->name }}</span>
                 <span class="item-subheader">
                    {{ $edu->custom_university ?? $edu->university?->name }} |
                    {{ \Carbon\Carbon::parse($edu->start_date)->format('Y') }} -
                    {{ $edu->is_current ? 'Present' : ($edu->end_date ? \Carbon\Carbon::parse($edu->end_date)->format('Y') : 'N/A') }}
                 </span>
                 @if($edu->result)
                    <div class="item-description">Result/GPA: {{ $edu->result }}</div>
                 @endif
            </div>
        @endforeach
    </div>
    @endif

    @if($user->skillSet->isNotEmpty())
    <div class="section">
        <h2>Skills</h2>
        <div class="skills-list">
            @foreach($user->skillSet->pluck('name') as $skillName)
                <span>{{ $skillName }}</span>
            @endforeach
        </div>
    </div>
    @endif

    @if($user->portfolios->isNotEmpty())
    <div class="section">
        <h2>Portfolio / Projects</h2>
        <ul>
        @foreach($user->portfolios as $portfolio)
            <li>
                <strong>{{ $portfolio->project_title }}:</strong>
                <a href="{{ $portfolio->project_url }}">{{ $portfolio->project_url }}</a>
                @if($portfolio->description)<p style="font-size: 10pt; margin-left: 10px;">{{ $portfolio->description }}</p>@endif
            </li>
        @endforeach
        </ul>
    </div>
    @endif

    {{-- Add other sections: Languages, Licenses, Memberships, Technical Education etc. --}}
    {{-- Example for Languages --}}
    {{-- @if($user->languages->isNotEmpty())
    <div class="section">
        <h2>Languages</h2>
        <ul>
        @foreach($user->languages as $lang)
             <li>{{ $lang->language?->name }} {{-- Add proficiency level if stored --}}</li>
        @endforeach
        </ul>
    </div>
    @endif --}}


</body>
</html>