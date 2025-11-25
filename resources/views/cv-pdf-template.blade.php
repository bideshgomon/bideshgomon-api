<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $cv->title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #333;
        }
        
        .container {
            padding: 20px;
        }
        
        .header {
            border-bottom: 3px solid {{ $template->color_scheme['primary'] ?? '#059669' }};
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .name {
            font-size: 24pt;
            font-weight: bold;
            color: {{ $template->color_scheme['primary'] ?? '#059669' }};
            margin-bottom: 5px;
        }
        
        .contact-info {
            font-size: 9pt;
            color: #666;
        }
        
        .contact-info span {
            margin-right: 15px;
        }
        
        .section {
            margin-bottom: 20px;
        }
        
        .section-title {
            font-size: 14pt;
            font-weight: bold;
            color: {{ $template->color_scheme['primary'] ?? '#059669' }};
            border-bottom: 2px solid {{ $template->color_scheme['secondary'] ?? '#10b981' }};
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        
        .summary {
            text-align: justify;
            line-height: 1.6;
        }
        
        .entry {
            margin-bottom: 15px;
            padding-left: 10px;
            border-left: 3px solid {{ $template->color_scheme['accent'] ?? '#34d399' }};
        }
        
        .entry-header {
            font-weight: bold;
            font-size: 11pt;
            color: #000;
        }
        
        .entry-subheader {
            color: #666;
            font-size: 10pt;
            margin-bottom: 5px;
        }
        
        .entry-dates {
            color: #999;
            font-size: 9pt;
            font-style: italic;
        }
        
        .entry-description {
            margin-top: 5px;
            text-align: justify;
        }
        
        .skills-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        
        .skill-tag {
            display: inline-block;
            padding: 5px 12px;
            background-color: {{ $template->color_scheme['primary'] ?? '#059669' }};
            color: white;
            border-radius: 15px;
            font-size: 9pt;
            margin-right: 5px;
            margin-bottom: 5px;
        }
        
        .languages-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .language-item {
            flex: 1 1 45%;
            min-width: 150px;
        }
        
        .language-name {
            font-weight: bold;
        }
        
        .language-proficiency {
            color: #666;
            font-size: 9pt;
        }
        
        .certification-item {
            margin-bottom: 10px;
        }
        
        .cert-name {
            font-weight: bold;
        }
        
        .cert-org {
            color: #666;
            font-size: 10pt;
        }
        
        .cert-dates {
            color: #999;
            font-size: 9pt;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="name">{{ $cv->full_name }}</div>
            <div class="contact-info">
                <span>📧 {{ $cv->email }}</span>
                <span>📱 {{ $cv->phone }}</span>
                @if($cv->city)
                    <span>📍 {{ $cv->city }}@if($cv->country), {{ $cv->country->name }}@endif</span>
                @endif
            </div>
            @if($cv->linkedin_url || $cv->website_url)
                <div class="contact-info" style="margin-top: 5px;">
                    @if($cv->linkedin_url)
                        <span>🔗 {{ $cv->linkedin_url }}</span>
                    @endif
                    @if($cv->website_url)
                        <span>🌐 {{ $cv->website_url }}</span>
                    @endif
                </div>
            @endif
        </div>

        <!-- Professional Summary -->
        @if($cv->professional_summary)
        <div class="section">
            <div class="section-title">Professional Summary</div>
            <div class="summary">{{ $cv->professional_summary }}</div>
        </div>
        @endif

        <!-- Work Experience -->
        @if($cv->experience && count($cv->experience) > 0)
        <div class="section">
            <div class="section-title">Work Experience</div>
            @foreach($cv->experience as $exp)
                <div class="entry">
                    <div class="entry-header">{{ $exp['job_title'] }}</div>
                    <div class="entry-subheader">
                        {{ $exp['company'] }}@if(!empty($exp['location'])) • {{ $exp['location'] }}@endif
                    </div>
                    <div class="entry-dates">
                        {{ date('M Y', strtotime($exp['start_date'])) }} - 
                        {{ $exp['is_current'] ? 'Present' : date('M Y', strtotime($exp['end_date'])) }}
                    </div>
                    @if(!empty($exp['description']))
                        <div class="entry-description">{{ $exp['description'] }}</div>
                    @endif
                </div>
            @endforeach
        </div>
        @endif

        <!-- Education -->
        @if($cv->education && count($cv->education) > 0)
        <div class="section">
            <div class="section-title">Education</div>
            @foreach($cv->education as $edu)
                <div class="entry">
                    <div class="entry-header">{{ $edu['degree'] }}@if(!empty($edu['field_of_study'])) in {{ $edu['field_of_study'] }}@endif</div>
                    <div class="entry-subheader">{{ $edu['institution'] }}</div>
                    <div class="entry-dates">
                        {{ date('M Y', strtotime($edu['start_date'])) }} - {{ date('M Y', strtotime($edu['end_date'])) }}
                        @if(!empty($edu['grade']))
                            • Grade: {{ $edu['grade'] }}
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        @endif

        <!-- Skills -->
        @if($cv->skills && count($cv->skills) > 0)
        <div class="section">
            <div class="section-title">Skills</div>
            <div class="skills-grid">
                @foreach($cv->skills as $skill)
                    <span class="skill-tag">{{ $skill['name'] ?? $skill['skill'] ?? '' }} @if(!empty($skill['level']) || !empty($skill['proficiency']))({{ ucfirst($skill['level'] ?? $skill['proficiency']) }})@endif</span>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Languages -->
        @if($cv->languages && count($cv->languages) > 0)
        <div class="section">
            <div class="section-title">Languages</div>
            <div class="languages-grid">
                @foreach($cv->languages as $lang)
                    <div class="language-item">
                        <span class="language-name">{{ $lang['language'] }}</span>
                        <span class="language-proficiency">• {{ ucfirst($lang['proficiency']) }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Certifications -->
        @if($cv->certifications && count($cv->certifications) > 0)
        <div class="section">
            <div class="section-title">Certifications</div>
            @foreach($cv->certifications as $cert)
                <div class="certification-item">
                    <div class="cert-name">{{ $cert['name'] }}</div>
                    <div class="cert-org">{{ $cert['issuing_organization'] ?? $cert['organization'] ?? '' }}</div>
                    <div class="cert-dates">
                        Issued: {{ date('M Y', strtotime($cert['issue_date'])) }}
                        @if(!empty($cert['expiry_date']))
                            • Expires: {{ date('M Y', strtotime($cert['expiry_date'])) }}
                        @endif
                        @if(!empty($cert['credential_id']))
                            • ID: {{ $cert['credential_id'] }}
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        @endif
    </div>
</body>
</html>
