# Yerevango Project Structure

## 📁 Directory Layout

```
Yerevango/
├── scripts/              # All Python/PHP scripts
│   ├── scrapers/        # 2GIS data extraction
│   │   ├── 2gis_api_scraper.py
│   │   ├── 2gis_selenium_scraper.py
│   │   └── fast_image_extractor.py
│   ├── importers/       # Database import scripts
│   │   ├── import_cafes_v2.py
│   │   ├── import_routes_v2.py
│   │   ├── import_tours.py
│   │   └── ...
│   └── utilities/       # Helper & debug scripts
│       ├── check_*.py
│       └── debug_*.py
├── tests/               # Testing scripts
├── docs/                # Documentation
├── data/                # Data files
│   ├── samples/        # JSON/CSV exports
│   ├── tours/          # Tour configurations
│   └── debug/          # Debug artifacts
├── database/            # SQL schema
├── src/                 # PHP application core
├── templates/           # View templates
└── public/              # Web root

## 🚀 Quick Start

### Import Tours
```bash
python3 scripts/importers/import_routes_v2.py
```

### Import Cafes
```bash
python3 scripts/importers/import_cafes_v2.py
```

### Run Web Server
```bash
./serve.sh
```

## 📝 Notes

- All scripts must be run from project root
- Data files referenced by importers use relative paths from root
- PHP server runs from `public/` directory
