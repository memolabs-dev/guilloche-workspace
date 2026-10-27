from pathlib import Path
import json

def luminance(color):
    channels = [int(color.lstrip("#")[i:i+2], 16)/255 for i in (0,2,4)]
    linear = [v/12.92 if v <= .04045 else ((v+.055)/1.055)**2.4 for v in channels]
    return sum(a*b for a,b in zip(linear, [.2126,.7152,.0722]))

pairs = [
    ("body", "#1a1a2e", "#ffffff", 4.5),
    ("secondary", "#6b7280", "#ffffff", 4.5),
    ("secondary background", "#6b7280", "#f8f9fa", 4.5),
    ("primary", "#ffffff", "#376b44", 4.5),
    ("hover", "#ffffff", "#2d5a38", 4.5),
    ("action", "#92400e", "#fff5e1", 4.5),
    ("progress", "#0369a1", "#edf5fc", 4.5),
    ("done", "#166534", "#edf7ef", 4.5),
    ("selected title", "#1a1a2e", "#e8f5e9", 4.5),
    ("selected marker", "#376b44", "#e8f5e9", 4.5),
    ("danger", "#b91c1c", "#fef2f2", 4.5),
    ("focus on reader", "#376b44", "#f8f9fa", 3),
    ("input", "#819087", "#ffffff", 3),
]
report = []
for name, fg, bg, minimum in pairs:
    a,b = sorted([luminance(fg),luminance(bg)])
    ratio = (b+.05)/(a+.05)
    report.append({"name":name,"ratio":round(ratio,2),"minimum":minimum,"pass":ratio>=minimum})
    print(name, round(ratio,2), "PASS" if ratio>=minimum else "FAIL")
assert all(p["pass"] for p in report)
Path(".agent/project/contrast.json").write_text(json.dumps(report,indent=2)+"\n")
