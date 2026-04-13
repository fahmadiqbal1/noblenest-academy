<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daily Learning Summary</title>
  <style>
    body { font-family: 'Segoe UI', Helvetica, Arial, sans-serif; background: #f8f9fa; margin: 0; padding: 0; color: #1a1a2e; }
    .wrapper { max-width: 580px; margin: 32px auto; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.06); }
    .header { background: linear-gradient(135deg, #7C3AED, #A78BFA); padding: 32px 40px; text-align: center; }
    .header h1 { color: #fff; font-size: 22px; margin: 0 0 6px; font-weight: 700; }
    .header p { color: rgba(255,255,255,0.85); margin: 0; font-size: 14px; }
    .body { padding: 32px 40px; }
    .greeting { font-size: 16px; color: #374151; margin-bottom: 24px; }
    .child-card { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 20px; margin-bottom: 20px; }
    .child-card .child-name { font-size: 17px; font-weight: 700; color: #065f46; margin-bottom: 4px; }
    .child-card .streak { font-size: 13px; color: #6b7280; margin-bottom: 12px; }
    .activity-pill { display: inline-block; background: #fff; border: 1px solid #d1fae5; border-radius: 8px; padding: 6px 12px; margin: 4px 4px 4px 0; font-size: 13px; color: #374151; }
    .stats-bar { display: flex; gap: 16px; margin: 24px 0; }
    .stat { flex: 1; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 10px; padding: 14px; text-align: center; }
    .stat .num { font-size: 28px; font-weight: 800; color: #7C3AED; }
    .stat .label { font-size: 12px; color: #9ca3af; margin-top: 2px; }
    .cta { display: block; width: fit-content; margin: 28px auto 0; background: #7C3AED; color: #fff; text-decoration: none; padding: 14px 32px; border-radius: 50px; font-size: 15px; font-weight: 600; text-align: center; }
    .footer { background: #f9fafb; border-top: 1px solid #e5e7eb; padding: 20px 40px; text-align: center; font-size: 12px; color: #9ca3af; }
    .footer a { color: #6b7280; text-decoration: underline; }
  </style>
</head>
<body>
  <div class="wrapper">
    <div class="header">
      <h1>🌟 Yesterday's Learning Recap</h1>
      <p>{{ now()->subDay()->format('l, F j, Y') }}</p>
    </div>

    <div class="body">
      <p class="greeting">Hi {{ $parent->name }},</p>

      @if(!empty($digest['summaries']))
        <p style="color:#374151; margin-bottom:20px;">Here's what your {{ count($digest['summaries']) > 1 ? 'children' : 'child' }} learned yesterday:</p>

        <div class="stats-bar">
          <div class="stat">
            <div class="num">{{ $digest['total_completions'] }}</div>
            <div class="label">Activities Done</div>
          </div>
          <div class="stat">
            <div class="num">🔥 {{ $digest['streak_max'] }}</div>
            <div class="label">Day Streak</div>
          </div>
        </div>

        @foreach($digest['summaries'] as $summary)
          <div class="child-card">
            <div class="child-name">{{ $summary['child_name'] }} · {{ $summary['age_display'] }}</div>
            @if($summary['streak'] > 1)
              <div class="streak">🔥 {{ $summary['streak'] }}-day streak – keep it up!</div>
            @endif
            <div>
              @foreach($summary['activities'] as $activity)
                <span class="activity-pill">{{ $activity->emoji ?? '📚' }} {{ $activity->title }}</span>
              @endforeach
            </div>
          </div>
        @endforeach

        <a href="{{ url('/parent/dashboard') }}" class="cta">View Full Dashboard →</a>
      @else
        <p style="color:#374151;">No activities yesterday — that's okay! Consistency builds over time. Try logging in today and starting with just one activity.</p>
        <a href="{{ url('/activities') }}" class="cta">Explore Today's Activities →</a>
      @endif
    </div>

    <div class="footer">
      <p>You're receiving this because you're a NobleNest Academy parent.</p>
      <p><a href="{{ url('/settings') }}">Manage email preferences</a> · <a href="{{ url('/privacy') }}">Privacy Policy</a></p>
    </div>
  </div>
</body>
</html>
