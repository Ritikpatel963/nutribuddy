import re

html_path = 'kidznourish_website.html'
blade_path = 'resources/views/pages/diet-chart.blade.php'
css_path = 'public/assets/css/frontendstyle.css'

with open(html_path, 'r', encoding='utf-8') as f:
    lines = f.readlines()

# Extract Stats
stats = []
in_stats = False
for line in lines:
    if '<!-- STATS -->' in line:
        in_stats = True
    if in_stats:
        stats.append(line)
        if '</div>' in line and len(stats) > 7:
            break

# Extract How it works
how = []
in_how = False
for line in lines:
    if '<!-- HOW IT WORKS -->' in line:
        in_how = True
    if in_how:
        if '<div class="step-arrow">?</div>' not in line:
            how.append(line)
        if '</section>' in line:
            break

# Clean how
how_cleaned = []
for line in how:
    if 'class="step-num"' in line:
        if 'var(--mn)' in line: line = line.replace('style="background:#e8f5e9;color:var(--mn)"', 'class="step-num-1"')
        if 'var(--or)' in line: line = line.replace('style="background:#fff3e0;color:var(--or)"', 'class="step-num-2"')
        if 'var(--pu)' in line: line = line.replace('style="background:#f3e5f5;color:var(--pu)"', 'class="step-num-3"')
        if 'var(--sk)' in line: line = line.replace('style="background:#e3f2fd;color:var(--sk)"', 'class="step-num-4"')
    if 'font-size:2.5rem' in line:
        line = line.replace('style="font-size:2.5rem;margin-bottom:12px"', 'class="step-icon"')
    how_cleaned.append(line)


# Extract Features
features = []
in_features = False
for line in lines:
    if '<!-- UNIQUE FEATURES -->' in line:
        in_features = True
    if in_features:
        features.append(line)
        if '</section>' in line:
            break

feat_cleaned = []
fc_idx = 1
for line in features:
    if '<div class="feature-card"' in line:
        line = re.sub(r'style="[^"]*"', f'', line)
        line = line.replace('class="feature-card"', f'class="feature-card fc-{fc_idx}"')
        feat_cleaned.append(line)
    elif '<span class="tag"' in line:
        line = re.sub(r'style="[^"]*"', '', line)
        feat_cleaned.append(line)
        fc_idx += 1
    else:
        feat_cleaned.append(line)

# Extract Zones
zones = []
in_zones = False
for line in lines:
    if '<!-- INDIA ZONES -->' in line:
        in_zones = True
    if in_zones:
        zones.append(line)
        if '</section>' in line:
            break

zones_cleaned = []
zc_idx = 1
for line in zones:
    if '<div class="zone-card"' in line:
        line = re.sub(r'style="[^"]*"', '', line)
        line = line.replace('class="zone-card"', f'class="zone-card zc-{zc_idx}"')
        zones_cleaned.append(line)
        zc_idx += 1
    else:
        zones_cleaned.append(line)


all_sections = "".join(stats) + "".join(how_cleaned) + "".join(feat_cleaned) + "".join(zones_cleaned)

with open(blade_path, 'r', encoding='utf-8') as f:
    blade_content = f.read()

# Insert before diet-chart section
parts = blade_content.split('<section class="diet-section" id="diet-chart">')
new_blade = parts[0] + all_sections + '<section class="diet-section" id="diet-chart">' + parts[1]

with open(blade_path, 'w', encoding='utf-8') as f:
    f.write(new_blade)

# Append CSS
css_content = '''
/* --- KidzNourish Features Section --- */
#features {
  padding: 80px 20px;
  background: var(--wh);
  max-width: 1200px;
  margin: 0 auto;
}
#features .text-center { text-align: center; margin-bottom: 50px; }
#features .section-label { color: var(--mn); font-weight: 700; text-transform: uppercase; letter-spacing: 2px; font-size: 0.85rem; margin-bottom: 10px; }
#features .section-title { font-family: 'Fredoka One', cursive; font-size: 2.8rem; color: var(--dk); margin-bottom: 15px; }
#features .section-sub { color: #666; font-size: 1.1rem; max-width: 600px; margin: 0 auto; }
.features-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 25px; }
.feature-card { background: var(--wh); border: 2px solid; border-radius: 16px; padding: 30px 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); transition: transform 0.3s ease, box-shadow 0.3s ease; position: relative; display: flex; flex-direction: column; border-top-width: 4px; }
.feature-card:hover { transform: translateY(-5px); box-shadow: 0 15px 40px rgba(0,0,0,0.08); }
.feature-icon { font-size: 2.5rem; margin-bottom: 15px; }
.feature-card h3 { font-family: 'Fredoka One', cursive; font-size: 1.25rem; color: var(--dk); margin-bottom: 12px; }
.feature-card p { color: #666; font-size: 0.95rem; line-height: 1.5; margin-bottom: 20px; flex-grow: 1; }
.feature-card .tag { align-self: flex-start; font-size: 0.75rem; font-weight: 700; padding: 6px 12px; border-radius: 20px; }
.fc-1 { border-color: var(--mn); } .fc-1 .tag { background: var(--mnl); color: var(--mn); }
.fc-2 { border-color: var(--sk); } .fc-2 .tag { background: var(--skl); color: var(--sk); }
.fc-3 { border-color: var(--pu); } .fc-3 .tag { background: var(--pul); color: var(--pu); }
.fc-4 { border-color: var(--or); } .fc-4 .tag { background: var(--orl); color: var(--or); }
.fc-5 { border-color: var(--pkd); } .fc-5 .tag { background: var(--pkl); color: var(--pkd); }
.fc-6 { border-color: var(--pk); } .fc-6 .tag { background: var(--pkl); color: var(--pk); }
.fc-7 { border-color: #00bcd4; } .fc-7 .tag { background: #e0f7fa; color: #00838f; }
.fc-8 { border-color: #8bc34a; } .fc-8 .tag { background: #f1f8e9; color: #558b2f; }

/* --- India Zones Section --- */
.zones-section { padding: 80px 20px; background: #f9fdfa; max-width: 1200px; margin: 0 auto; }
.zones-section .text-center { text-align: center; margin-bottom: 50px; }
.zones-section .section-label { color: var(--mn); font-weight: 700; text-transform: uppercase; letter-spacing: 2px; font-size: 0.85rem; margin-bottom: 10px; }
.zones-section .section-title { font-family: 'Fredoka One', cursive; font-size: 2.8rem; color: var(--dk); margin-bottom: 15px; }
.zones-section .section-sub { color: #666; font-size: 1.1rem; max-width: 600px; margin: 0 auto; }
.zones-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 24px; }
.zone-card { border-radius: 20px; padding: 32px 24px; color: #fff; position: relative; overflow: hidden; box-shadow: 0 12px 32px rgba(0,0,0,0.06); transition: transform 0.3s ease; }
.zone-card:hover { transform: translateY(-5px); }
.zone-card::after { content: ''; position: absolute; bottom: -30px; right: -30px; width: 120px; height: 120px; background: rgba(255,255,255,0.1); border-radius: 50%; }
.zc-1 { background: linear-gradient(135deg, var(--or), var(--ord)); }
.zc-2 { background: linear-gradient(135deg, var(--mn), var(--mnd)); }
.zc-3 { background: linear-gradient(135deg, var(--sk), var(--skd)); }
.zc-4 { background: linear-gradient(135deg, var(--pu), var(--pud)); }
.zone-flag { font-size: 2.5rem; margin-bottom: 16px; }
.zone-name { font-family: 'Fredoka One', cursive; font-size: 1.5rem; margin-bottom: 8px; }
.zone-states { font-size: 0.85rem; opacity: 0.9; margin-bottom: 24px; line-height: 1.4; border-bottom: 1px solid rgba(255,255,255,0.2); padding-bottom: 16px; }
.zone-foods { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 24px; }
.food-chip { background: rgba(255,255,255,0.2); padding: 6px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; backdrop-filter: blur(4px); }
.zone-insight { font-size: 0.9rem; line-height: 1.5; opacity: 0.95; background: rgba(0,0,0,0.1); padding: 16px; border-radius: 12px; }

'''

with open(css_path, 'a', encoding='utf-8') as f:
    f.write(css_content)

print("Done")
