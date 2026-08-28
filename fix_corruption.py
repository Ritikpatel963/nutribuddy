# -*- coding: utf-8 -*-
html_path = 'e:/website-project/Nutribuddy/resources/views/pages/diet-chart.blade.php'

with open(html_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Remove all bullet characters added by the empty string replace mistake
content = content.replace('•', '')

with open(html_path, 'w', encoding='utf-8') as f:
    f.write(content)

print("Fixed the bullet corruption!")
