<h2>Monthly Curriculum Analytics Report</h2>
<p>Total Skills: <b>{{ $totalSkills }}</b></p>
<p>Total Activities: <b>{{ $totalActivities }}</b></p>
<h3>Coverage by Skill</h3>
<table border="1" cellpadding="6" cellspacing="0" style="border-collapse:collapse;">
    <thead>
        <tr>
            <th>Skill</th>
            <th>Activities</th>
            <th>Age Range</th>
        </tr>
    </thead>
    <tbody>
        @foreach($coverage as $row)
        <tr>
            <td>{{ $row['skill'] }}</td>
            <td>{{ $row['count'] }}</td>
            <td>{{ $row['age_min'] }}–{{ $row['age_max'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<h3 style="margin-top:24px;">Most Liked Activities</h3>
<table border="1" cellpadding="6" cellspacing="0" style="border-collapse:collapse;">
    <thead>
        <tr>
            <th>Activity</th>
            <th>Skill</th>
            <th>Likes</th>
            <th>Age Range</th>
        </tr>
    </thead>
    <tbody>
        @foreach($topLiked as $activity)
        <tr>
            <td>{{ $activity->title }}</td>
            <td>{{ $activity->skill }}</td>
            <td>{{ $activity->likes_count }}</td>
            <td>{{ $activity->age_min }}–{{ $activity->age_max }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<h3 style="margin-top:24px;">Pedagogical Influences & Global Activities</h3>
<table border="1" cellpadding="6" cellspacing="0" style="border-collapse:collapse;">
    <thead>
        <tr>
            <th>Pedagogy</th>
            <th>Age Bracket</th>
            <th>Sample Activities</th>
            <th>Learning Content</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Japanese</td>
            <td>2–4 years</td>
            <td>
                Group empathy games,<br>
                Songs/dances (Japan),<br>
                Martial-arts-inspired movement,<br>
                Role-play: respect for elders/nature
            </td>
            <td>
                Cross-cultural stories, group music/dance videos, emotional coaching modules, discipline exercises
            </td>
        </tr>
        <tr>
            <td>Chinese</td>
            <td>2–5 years</td>
            <td>
                Art projects (ink painting, calligraphy),<br>
                Songs/dances (China),<br>
                Tracing Chinese characters,<br>
                Pinyin introduction,<br>
                Chinese puzzle games
            </td>
            <td>
                Art/drawing modules, tracing activities, group singing/dance, logic puzzles
            </td>
        </tr>
        <tr>
            <td>Scandinavian</td>
            <td>3–6 years</td>
            <td>
                Collaborative art projects,<br>
                Mindfulness exercises,<br>
                Animated scenarios (fairness),<br>
                Weaving/nature crafts,<br>
                Family tree/cultural scrapbook
            </td>
            <td>
                Mindfulness modules, collaborative games, scenario-based videos, creative/nature-based art
            </td>
        </tr>
    </tbody>
</table>
<p style="margin-top:24px;">For more details, visit your <a href="{{ url('/admin/activities/analytics') }}">Curriculum Analytics Dashboard</a>.</p>
