import os

css_path = 'e:/website-project/Nutribuddy/public/assets/css/frontendstyle.css'

css_content = '''
/* --- Stats Bar --- */
.stats-bar { display: flex; justify-content: center; align-items: center; gap: 40px; padding: 40px 20px; background: var(--wh); border-bottom: 1px solid #f0f0f0; flex-wrap: wrap; }
.stat { text-align: center; }
.stat-num { font-family: 'Fredoka One', cursive; font-size: 2.2rem; color: var(--dk); }
.stat-num span { color: var(--pk); }
.stat-label { font-size: 0.85rem; color: #666; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; margin-top: 4px; }
@media (max-width: 768px) { .stats-bar { gap: 20px; padding: 20px; } .stat { width: 40%; } }

/* --- How KidzNourish Works Section --- */
.how { padding: 80px 20px; background: #fdfbfd; }
.how .text-center { text-align: center; margin-bottom: 50px; }
.how .section-label { color: var(--mn); font-weight: 700; text-transform: uppercase; letter-spacing: 2px; font-size: 0.85rem; margin-bottom: 10px; }
.how .section-title { font-family: 'Fredoka One', cursive; font-size: 2.8rem; color: var(--dk); margin-bottom: 15px; }
.how .section-sub { color: #666; font-size: 1.1rem; max-width: 600px; margin: 0 auto; }
.steps { display: flex; align-items: flex-start; justify-content: space-between; gap: 20px; max-width: 1100px; margin: 0 auto; }
.step { flex: 1; text-align: center; padding: 20px; position: relative; }
.step-num { width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-family: 'Fredoka One', cursive; font-size: 1.5rem; margin: 0 auto 20px; }
.step h3 { font-family: 'Fredoka One', cursive; font-size: 1.2rem; color: var(--dk); margin-bottom: 10px; }
.step p { color: #666; font-size: 0.95rem; line-height: 1.5; }
.step-icon { font-size: 2.5rem; margin-bottom: 12px; }
.step-num-1 { background: var(--mnl); color: var(--mn); }
.step-num-2 { background: var(--orl); color: var(--or); }
.step-num-3 { background: var(--pul); color: var(--pu); }
.step-num-4 { background: var(--skl); color: var(--sk); }
.how .step:not(:last-child)::after { content: '?'; position: absolute; right: -20px; top: 30px; font-size: 2rem; color: #ccc; }
@media (max-width: 768px) { .steps { flex-direction: column; align-items: center; } .how .step:not(:last-child)::after { display: none; } }

/* --- KidzNourish Features Section --- */
#features { padding: 80px 20px; background: var(--wh); max-width: 1200px; margin: 0 auto; }
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
.zc-1 { background: linear-gradient(135deg, #f39c12, #e67e22); }
.zc-2 { background: linear-gradient(135deg, #2ecc71, #1abc9c); }
.zc-3 { background: linear-gradient(135deg, #3498db, #2980b9); }
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
