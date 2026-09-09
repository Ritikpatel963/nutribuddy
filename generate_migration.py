import json
import re

file_path = r"C:\Users\Ritik\Downloads\final_master_food_data_clean.json"
migration_file = r"e:\website-project\Nutribuddy\database\migrations\2026_09_05_114652_create_foods_table.php"

with open(file_path, "r", encoding="utf-8") as f:
    data = json.load(f)

foods = data['foods']
from collections import defaultdict
field_stats = defaultdict(lambda: {'types': set(), 'max_len': 0})

for item in foods:
    for k, v in item.items():
        if v is not None:
            t = type(v).__name__
            field_stats[k]['types'].add(t)
            if t == 'str':
                field_stats[k]['max_len'] = max(field_stats[k]['max_len'], len(v))

lines = []
for k, stats in field_stats.items():
    types = stats['types']
    if 'str' in types:
        max_len = stats['max_len']
        if max_len > 255:
            lines.append(f"$table->text('{k}')->nullable();")
        else:
            # Add a buffer of 20% to max_len, min 10
            length = max(10, int(max_len * 1.2))
            if k == 'dish_id':
                lines.append(f"$table->string('{k}', 20)->unique();")
            else:
                lines.append(f"$table->string('{k}', {length})->nullable();")
    elif 'float' in types:
        lines.append(f"$table->double('{k}')->nullable();")
    elif 'int' in types:
        lines.append(f"$table->integer('{k}')->nullable();")
    else:
        # fallback if all null
        lines.append(f"$table->string('{k}')->nullable();")

# Add standard indexes for admin panel performance
indexes = [
    "dish_name", "food_group", "slot", "diet_type", "is_veg", "region"
]

lines.append("")
for idx in indexes:
    lines.append(f"$table->index('{idx}');")

schema_str = "\n            ".join(lines)

with open(migration_file, "r", encoding="utf-8") as f:
    content = f.read()

new_content = re.sub(
    r"\$table->id\(\);\n(.*?)\$table->timestamps\(\);",
    f"$table->id();\n            {schema_str}\n            $table->timestamps();",
    content,
    flags=re.DOTALL
)

with open(migration_file, "w", encoding="utf-8") as f:
    f.write(new_content)

print("Migration updated successfully!")
