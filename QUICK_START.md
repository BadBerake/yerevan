# راهنمای سریع استفاده

## 🎯 خلاصه راه‌حل مشکل Python Version

شما دو نسخه Python دارید:
- **Python 3.9.6** → Dependencies روی این نصب شده ✅
- **Python 3.14** → Dependencies روی این نصب نیست ❌

### ✅ راه‌حل: استفاده از python3

از دستور `python3` استفاده کنید (که به Python 3.9 اشاره می‌کند):

---

## 🚀 اجرای اسکریپت‌ها

### روش 1: اسکریپت API (نیاز به API Key)

```bash
# 1. ویرایش فایل و وارد کردن API Key در خط 24
nano 2gis_api_scraper.py

# 2. اجرا
python3 2gis_api_scraper.py
```

---

### روش 2: اسکریپت Selenium (بدون API Key)

**مرحله 1: نصب ChromeDriver**

```bash
# نصب ChromeDriver
brew install chromedriver

# اجازه دادن به ChromeDriver (اگر با مشکل امنیتی مواجه شدید)
xattr -d com.apple.quarantine $(which chromedriver)
```

**مرحله 2: اجرای اسکریپت**

```bash
python3 2gis_selenium_scraper.py
```

---

## ⚠️ عیب‌یابی

### خطای "chromedriver not found"

```bash
# نصب
brew install chromedriver

# یا دانلود دستی از:
# https://chromedriver.chromium.org/
```

### خطای امنیتی macOS

```bash
# اجازه دادن به ChromeDriver
xattr -d com.apple.quarantine /opt/homebrew/bin/chromedriver
```

### خطای "Session not created"

نسخه ChromeDriver باید با نسخه Chrome شما مطابقت داشته باشد.

```bash
# بررسی نسخه Chrome
/Applications/Google\ Chrome.app/Contents/MacOS/Google\ Chrome --version

# نصب نسخه مناسب ChromeDriver
brew reinstall chromedriver
```

---

## 📊 بررسی نصب

```bash
# بررسی Python
python3 --version

# بررسی Selenium
python3 -c "import selenium; print(selenium.__version__)"

# بررسی ChromeDriver
chromedriver --version
```

---

## 🎬 اجرای سریع (تست)

```bash
# تست منطق استخراج
python3 test_scraper.py

# تولید داده نمونه
python3 generate_sample_data.py
```

---

## 📁 فایل‌های خروجی

بعد از اجرای موفق، فایل‌های زیر ایجاد می‌شوند:

- **API Scraper:**
  - `yerevan_cafes.json`
  - `yerevan_cafes.csv`

- **Selenium Scraper:**
  - `yerevan_cafes_selenium.json`
  - `yerevan_cafes_selenium.csv`

---

## 💡 نکات مهم

1. **استفاده از python3**: همیشه از `python3` استفاده کنید، نه `python3.14`
2. **API Key**: برای اسکریپت API، حتماً API Key را وارد کنید
3. **ChromeDriver**: برای Selenium، ChromeDriver باید نصب باشد
4. **زمان اجرا**: اسکریپت Selenium ممکن است چند دقیقه طول بکشد

---

## 📞 کمک اضافی

برای راهنمای کامل، فایل `README_SCRAPER.md` را مطالعه کنید.
