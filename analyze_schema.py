import json
import os
from collections import defaultdict

file_path = r"C:\Users\Ritik\Downloads\final_master_food_data_clean.json"

with open(file_path, "r", encoding="utf-8") as f:
    data = json.load(f)

foods = data['foods']

field_stats = defaultdict(lambda: {'types': set(), 'max_len': 0, 'has_null': False})

for item in foods:
    for k, v in item.items():
        if v is None:
            field_stats[k]['has_null'] = True
        else:
            t = type(v).__name__
            field_stats[k]['types'].add(t)
            if t == 'str':
                field_stats[k]['max_len'] = max(field_stats[k]['max_len'], len(v))

print("Field Summary:")
for k, stats in field_stats.items():
    types_str = ", ".join(list(stats['types']))
    null_str = " (nullable)" if stats['has_null'] else ""
    len_str = f" [max_len: {stats['max_len']}]" if 'str' in stats['types'] else ""
    print(f"{k}: {types_str}{len_str}{null_str}")
