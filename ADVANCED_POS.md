# OSPOS Advanced POS — Enhancement Report

তারিখ: ২১–২২ অক্টোবর ২০২৬

এই ডকুমেন্টে আপনার POS-এ যোগ করা নতুন ফিচার, নিরাপত্তা ও পারফরম্যান্স পরিবর্তন এবং সেগুলো ব্যবহারের নিয়ম লেখা আছে।

---

## ১. নতুন অ্যানালিটিক্স ড্যাশবোর্ড (হোম পেজ)

`http://localhost/public/home` এখন একটি পূর্ণাঙ্গ ড্যাশবোর্ড:

| সেকশন | কী দেখায় |
|---|---|
| KPI কার্ড | আজকের বিক্রি (OMR), লেনদেন সংখ্যা, গড় টিকিট, বিক্রিত পণ্য — গতকালের তুলনায় % পরিবর্তনসহ |
| Revenue trend | শেষ ১৪ দিনের দৈনিক বিক্রির চার্ট (Chartist) |
| Payment mix | আজকের পেমেন্ট মাধ্যম অনুযায়ী ভাগ (Cash / Card / Due ইত্যাদি) |
| Low stock | রি-অর্ডার লেভেলে পৌঁছানো পণ্যের তালিকা ও স্টক বার |
| Top items | শেষ ৩০ দিনের সর্বাধিক বিক্রিত ৫টি পণ্য |
| Recent sales | সর্বশেষ ৬টি বিক্রি — রসিদ খোলার লিংকসহ |
| Quick actions | New Sale, Receiving, Cash Up, Reports — এক ক্লিকে |

নতুন ফাইল:
- `app/Models/Dashboard.php` — সব অ্যাগ্রিগেট কুয়েরি
- `app/Controllers/Home.php` — ড্যাশবোর্ড ডেটা প্রস্তুত করে
- `app/Views/home/home.php` — নতুন UI
- `app/Language/en/Dashboard.php` — নতুন লেবেল

> কোনো কুয়েরি ব্যর্থ হলেও হোম পেজ খুলবে (try/catch + লগ), ব্যবহারকারী আটকে যাবেন না।

> **Revenue Trend চার্ট ফিক্স:** চার্টের নিচের অক্ষে আগে শুধু দিনের সংখ্যা দেখা যেত; এখন পূর্ণ তারিখ (যেমন `09 Oct`) দেখায়। ডেস্কটপে প্রতি দ্বিতীয় টিক এবং মোবাইলে প্রতি চতুর্থ টিকে লেবেল দেখানো হয়, তাই লেবেল ভিড় হয় না। সংশ্লিষ্ট ফাইল `app/Views/home/home.php` ও `public/css/pro-ui.css`; CSS ভার্সন `20261021h`।

---

## ২. নিরাপত্তা উন্নতি

- **এনক্রিপশন কী পরিষ্কার** — `.env`-এ ডুপ্লিকেট `encryption.key` সরানো হয়েছে। আগের কী `encryption.previousKeys`-এ রাখা হয়েছে, তাই পুরনো এনক্রিপ্টেড ডেটাও পড়া যাবে।
- **ডুপ্লিকেট সেটিংস বাদ** — `security.*` ব্লক একবারই আছে।
- **Uploads সুরক্ষিত** — `public/uploads/.htaccess` যোগ করা হয়েছে; আপলোড ফোল্ডারে PHP/স্ক্রিপ্ট চালানো যাবে না।
- **Hidden ফাইল ব্লক** — `.env`, `.git`, `.htaccess` ইত্যাদি ওয়েব থেকে সরাসরি পড়া যাবে না (root ও public `.htaccess`)।
- **Debug toolbar** — প্রোডাকশনে স্বয়ংক্রিয়ভাবে বন্ধ হয়।
- **প্রোডাকশন মোড চালু** — `.env`-এ এখন `CI_ENVIRONMENT = production` ও `CI_DEBUG = false`; বিস্তারিত এরর/ডিবাগ বার ওয়েবে দেখাবে না (এরর `writable/logs`-এ যাবে)।
- **অপ্রয়োজনীয় ডিবাগ লগ সরানো** — `app/Models/Sale.php`-এ প্রতিটি রিপোর্ট কুয়েরি ভুলভাবে ERROR হিসেবে লগ হচ্ছিল; সেটি বন্ধ করা হয়েছে।
- **Taxes মডিউল গ্রান্ট** — admin ইউজারের `taxes` পারমিশন যোগ করা হয়েছে (আগে Taxes পেজে "no access" আসত)।
- **প্রোডাকশন টেমপ্লেট** — `.env.production.example` দেখুন (HTTPS, secure cookie, HSTS, আলাদা DB ইউজার)।

> বর্তমানে সাইটটি ইতিমধ্যে production মোডে চলছে। ডিবাগ তথ্য দেখতে হলে `.env`-এ সাময়িকভাবে `CI_ENVIRONMENT = development` ও `CI_DEBUG = true` করে Apache রিস্টার্ট করুন; কাজ শেষে আবার production-এ ফিরিয়ে নিন।

### প্রোডাকশনে যাওয়ার চেকলিস্ট
1. `.env.production.example` থেকে মানগুলো `.env`-এ বসান (নতুন শক্ত পাসওয়ার্ড ও এনক্রিপশন কী দিয়ে)।
2. `app/Config/Cookie.php`-এ `$secure = true` করুন (HTTPS থাকলে)।
3. `public/.htaccess`-এ HSTS লাইনটি আনকমেন্ট করুন।
4. Apache-এর DocumentRoot `public/` ফোল্ডারে রাখুন — পুরো প্রজেক্ট রুট নয়।
5. `CI_ENVIRONMENT = production` এবং `CI_DEBUG = false` নিশ্চিত করুন।

---

## ৩. স্বয়ংক্রিয় ব্যাকআপ ও রিস্টোর

### ব্যাকআপ নিন
```powershell
powershell -ExecutionPolicy Bypass -File scripts\backup.ps1 -IncludeUploads
```
- DB ডাম্প: `writable\backups\db\ospos_db_YYYYMMDD_HHMMSS.sql.gz`
- আপলোড (পণ্যের ছবি/লোগো): `writable\backups\uploads\uploads_YYYYMMDD_HHMMSS.zip`
- লগ: `writable\logs\backup.log`
- ডিফল্টভাবে সর্বশেষ ৩০টি ব্যাকআপ রাখা হয় (`-Keep 14` দিয়ে বদলানো যায়)।

### প্রতিদিন স্বয়ংক্রিয় ব্যাকআপ
```powershell
powershell -ExecutionPolicy Bypass -File scripts\install-backup-task.ps1 -Time 23:00
```
Windows Task Scheduler-এ "OSPOS Daily Backup" টাস্ক তৈরি হবে (প্রয়োজনে Administrator হিসেবে চালান)।

### রিস্টোর
```powershell
powershell -ExecutionPolicy Bypass -File scripts\restore.ps1 -File "writable\backups\db\ospos_db_20261021_214133.sql.gz"
```
- রিস্টোরের আগে বর্তমান DB-এর একটি safety backup নিজেই তৈরি হয়।
- নিশ্চিত হতে `YES` লিখতে হবে (বা `-Force` ব্যবহার করুন)।

> প্রথম ব্যাকআপটি সফলভাবে পরীক্ষা করা হয়েছে (৬৭ KB আনকম্প্রেসড SQL, সব টেবিল ও ডেটা সহ)।

---

## ৪. পারফরম্যান্স

- **OPcache চালু** — `php.ini`-এ `zend_extension=opcache`, ১৯২ MB, ২০,০০০ ফাইল। Apache রিস্টার্টের পর কার্যকর।
- **Cache: Redis → File** — আগে Redis/Memcached কোনো সার্ভার চলছিল না; এখন CI-এর ফাইল ক্যাশ ব্যবহার হচ্ছে, তাই সেটিংস প্রতি রিকোয়েস্টে DB থেকে পড়তে হয় না।
- **নতুন DB ইনডেক্স** (`database_enhancements.sql`, প্রয়োগ করা হয়েছে):
  - `sales (sale_status, sale_time)`
  - `sales_items (sale_id, item_id)`
  - `sales_payments (payment_type, sale_id)`
  - `customers (person_id, company_name)`
- **GZIP + browser caching** — আগে থেকেই `.htaccess`-এ ছিল, বহাল আছে।
- **Query time limit** — ৫ সেকেন্ড (আগের সেটিং)।

---

## ৫. নতুন রসিদ ডিজাইন

`app/Views/sales/receipt_default.php` সম্পূর্ণ নতুন লেআউট পেয়েছে:

- সেন্টার লোগো + কোম্পানির নাম (বড়, বোল্ড)
- ঠিকানা ও ফোন
- "RECEIPT" ব্যাজ
- বিক্রি নম্বর, তারিখ, ক্যাশিয়ার, কাস্টমার, ইনভয়েস — পরিষ্কার মেটা টেবিল
- পণ্যের তালিকা: নাম, দাম, পরিমাণ, মোট
- ড্যাশড ও সলিড সেপারেটর, বড় করে মোট
- পেমেন্ট ও বাকি/ফেরত
- ধন্যবাদ বার্তা + রিটার্ন পলিসি + বারকোড
- 58mm ও 80mm থার্মাল প্রিন্টারের জন্য উপযুক্ত, প্রিন্ট CSS সহ

নতুন ভাষা কী: `Sales.thank_you_message` (en ও en-GB)।

---

## ৬. প্রফেশনাল UI সিস্টেম

পুরো অ্যাপ্লিকেশনের জন্য একটি কেন্দ্রীয় ডিজাইন সিস্টেম যোগ করা হয়েছে। OSPOS-এর মূল Bootstrap 3 / Bootswatch `cerulean` স্ট্রাকচার অপরিবর্তিত রেখে শুধুমাত্র উপরের স্তরে নতুন স্টাইল প্রয়োগ করা হয়েছে।

### ডিজাইন টোকেন

| টোকেন | মান |
|---|---|
| Primary | `#2563eb` (hover: `#1d4ed8`) |
| Topbar | `#0b1220` |
| Page background | `#f5f7fb` |
| Border | `#e5eaf2` |
| Card radius | `14px` |
| Font | Inter, Segoe UI, system-ui |
| Shadow | নরম, নীল-ধূসর (কম অপাসিটি) |

### কী কী পরিবর্তন হয়েছে

- **গ্লোবাল স্টাইলশিট:** `public/css/pro-ui.css` — থিমের পরে লোড হয়, তাই নিরাপদে সব কম্পোনেন্ট ওভাররাইড করে।
- **টপবার:** ডান-বামে তারিখ, মাঝে কোম্পানি, ডানে ইউজার/লগআউট; মোবাইলে কোম্পানি লুকানো।
- **নেভিগেশন:** সাদা স্টিকি নেভবার, হোভার/অ্যাকটিভ পিল, রেসপন্সিভ ব্রেকপয়েন্ট —
  - `≥1400px`: ১৫টি মডিউলসহ সম্পূর্ণ মেনু
  - `1200–1399px`: কমপ্যাক্ট মেনু
  - `≤1199px`: হ্যামবার্গার কলাপ্স (আগে ৭৬৮–৯৯১px-এ মেনু ভাঙছিল)
- **পেজ হেডার:** নতুন `app/Views/partial/page_header.php` — সব ম্যানেজমেন্ট পেজে একই রকম `H1 + subtitle`।
- **টেবিল:** কার্ডের ভিতরে টুলবার, ফিল্টার, সার্চ; হেডার হালকা ধূসর, রো হোভার, মোবাইলে অনুভূমিক স্ক্রল।
- **ফর্ম ও বাটন:** একই উচ্চতা, বর্ডার, ফোকাস রিং, প্রাইমারি গ্রেডিয়েন্ট, সেকেন্ডারি নিউট্রাল।
- **ট্যাব:** ট্যাবের নিচে সাদা কার্ড (Taxes, Configuration পেজ)।
- **মোডাল:** সাদা হেডার + গাঢ় টাইটেল (আগে `type-primary` মোডালে সাদার উপর সাদা টাইটেল ঢেকে যাচ্ছিল), ৬৪০px প্রস্থ, নরম শ্যাডো।
- **রিপোর্ট পেজ:** কার্ড হেডারের শিরোনাম দৃশ্যমান করা হয়েছে (থিমের উচ্চ-স্পেসিফিসিটি ঠিক করে), লিস্ট-গ্রুপ হোভার, ৩-কলাম লেআউট।
- **Messages:** জাম্বোট্রন বাদ দিয়ে কার্ড-ভিত্তিক SMS ফর্ম + পেজ হেডার।
- **Office:** পেজ হেডার + রেসপন্সিভ মডিউল কার্ড গ্রিড।
- **সেলস রেজিস্টার:** রঙের প্যালেট গ্লোবাল প্রাইমারির সাথে মিলিয়ে, খালি কার্টে নিউট্রাল ড্যাশড বর্ডার।
- **লগইন ও ড্যাশবোর্ড:** প্রাইমারি রঙ `#2563eb`-এ একীভূত।
- **স্টিকি ফুটার:** ছোট পেজেও ফুটার ভিউপোর্টের নিচে থাকে; মোবাইলে `.container`-এর প্রস্থ ১০০% নিশ্চিত করা হয়েছে।

### যাচাই

- সব পরিবর্তিত PHP ফাইলে `php -l` — কোনো সিনট্যাক্স এরর নেই।
- ১৯টি প্রধান পেজ (Home, Items, Attributes, Cashups, Expenses, Expenses Categories, Gift Cards, Item Kits, Customers, Employees, Suppliers, Sales, Sales Manage, Receivings, Reports, Taxes, Configuration, Messages, Office) — সব `HTTP 200`।
- CSS `HTTP 200`, পেজ টাইটেল ও `pro-ui.css` লোড নিশ্চিত।
- মোবাইল (৫৯৮px) ও ডেস্কটপ (১২৯৬px) স্ক্রিনশটে যাচাই করা হয়েছে।
- Production মোড ও `.env` অপরিবর্তিত; নতুন কোনো PHP/DB এরর লগে নেই।

> ক্যাশ-বাস্টিং: `app/Views/partial/header.php`-এ `pro-ui.css?v=20261021h` ব্যবহার করা হয়েছে। ভবিষ্যতে CSS বদলালে শেষ অক্ষরটি বাড়িয়ে দিন।

---

## ৭. Cashups — পূর্ণ ক্যাশ রিকনসিলিয়েশন

Cashups মডিউলটি এখন শুধু একটি সংখ্যা সংরক্ষণ না করে প্রতিদিনের ক্যাশ ও অন্য পেমেন্টের পূর্ণ মিলন (reconciliation) করে।

### হিসাবের নিয়ম

| খাত | কীভাবে হিসাব হয় |
|---|---|
| Counted Cash (`closed_amount_cash`) | ব্যবহারকারীর দেওয়া শারীরিক ক্যাশ |
| Expected Cash (`expected_amount_cash`) | Opening Cash + Cash In/Out + Net Cash Sales − Cash Expenses |
| Cash Difference (`cash_difference`) | Counted Cash − Expected Cash |
| Cash Sales | Completed sales-এর Cash payment − Cash refund |
| Card / Check / Gift Card / Other | Completed sales-এর সংশ্লিষ্ট payment_type |
| Due Sales (`closed_amount_due`) | Completed sales-এর Due payment; এটি হাতে আসা ক্যাশ নয়, তাই Expected Cash-এ যোগ হয় না |
| Total Sales (`total_sales`) | Period-এর Net Sales (returns/refunds বাদ দেওয়ার পরে) |
| Total Expenses (`total_expenses`) | Period-এর সব expense |
| Net Total (`closed_amount_total`) | Total Sales − Total Expenses |

> Cash Difference ধনাত্মক (+) হলে **Over**, ঋণাত্মক (−) হলে **Short** — তালিকায় রঙিন ব্যাজ হিসেবে দেখায়।

### নতুন সুবিধা

- **Exact timestamp:** খোলা ও বন্ধের সময় সেকেন্ডসহ ব্যবহৃত হয়; শুধু তারিখ নয়। রাত ১২টার পরে শেষ হওয়া শিফটও সঠিক period-এ পড়ে।
- **Auto opening:** নতুন cashup খুললে সর্বশেষ **closed** cashup-এর close time + ১ সেকেন্ড থেকে open time, এবং সেই cashup-এর counted cash থেকে opening cash বসে।
- **Live summary:** form-এ opening cash, in/out cash বা counted cash বদলালেই AJAX (`cashups/summary`) দিয়ে expected cash, difference, card, due ও totals সাথে সাথে আপডেট হয়।
- **Use Expected:** এক ক্লিকে expected amount counted cash-এ বসানোর সুবিধা; expected cash ঋণাত্মক হলে বাটনটি নিষ্ক্রিয় থাকে, যাতে ভুল করে negative cash save না হয়।
- **Server-side recalc:** Save করার সময় সার্ভারই ডাটাবেস থেকে সব payment ও expense আবার হিসাব করে; ব্রাউজারের পাঠানো totals-এর উপর ভরসা করা হয় না।
- **List filter:** Cashup তালিকার তারিখ ফিল্টার এখন `close_date` ব্যবহার করে; close না হলে `open_date`। ফলে গত রাতে খুলে আজ বন্ধ হওয়া shift আজকের তালিকায় দেখা যায়।
- **Read-only tender:** Card, Check, Gift Card, Other ও Due — এগুলো সম্পাদনযোগ্য নয়; payment data থেকে স্বয়ংক্রিয়ভাবে আসে।

### ডাটাবেসে নতুন কলাম (`ospos_cash_up`)

`expected_amount_cash`, `cash_difference`, `closed_amount_giftcard`, `closed_amount_other`, `total_sales`, `total_expenses`, `cash_sales`, `cash_refunds`, `cash_expenses`, `total_returns`

### ফাইল

- `app/Controllers/Cashups.php` — summary, save, AJAX endpoint
- `app/Models/Cashup.php` — নতুন কলাম, `get_summary()`, `get_last_cashup()`, close-date filter
- `app/Views/cashups/form.php` — reconciliation UI ও live JS
- `app/Helpers/tabular_helper.php` — তালিকার নতুন কলাম
- `app/Language/en/Cashups.php`, `app/Language/en-GB/Cashups.php` — নতুন লেবেল

---

## ৮. নতুন মডিউল: Stock Alerts

স্টক শেষ হওয়ার আগেই সতর্ক হওয়ার জন্য read-only মডিউল।

- URL: `http://localhost/public/stockalerts`
- Location, status (All / Low / Out) ও item search ফিল্টার
- প্রতি item-এর বর্তমান quantity, reorder level, deficit, location, supplier ও unit cost
- Low = quantity ≤ reorder level (কিন্তু ০-এর বেশি), Out = quantity ≤ 0
- শুধু পড়ার সুবিধা — কোনো stock বা item data বদলায় না
- মডিউল রেজিস্ট্রেশন: `sort = 115`, permission `stockalerts`, admin (person_id 1) ও employee (person_id 3)-কে `home` menu group-এ grant
- ফাইল: `app/Controllers/Stockalerts.php`, `app/Models/Stockalert.php`, `app/Views/stockalerts/manage.php`, language ও `public/images/menubar/stockalerts.svg`

---

## ৯. নতুন মডিউল: Receivables (কাস্টমার বাকি + Due Collection)

`ospos_sales_payments`-এ `Due` হিসেবে থাকা বাকি এক জায়গায় দেখা যায় এবং এখন **সরাসরি Receivables থেকেই বাকি আদায় (collection)** করা যায়।

- URL: `http://localhost/public/receivables`
- Customer অনুযায়ী মোট বাকি, invoice সংখ্যা ও oldest due
- প্রতিটি customer-এর `Details`-এ invoice-ভিত্তিক original due, already collected, remaining due ও receipt link
- প্রতিটি invoice-এর পাশে **Collect** বাটন — আংশিক বা সম্পূর্ণ পেমেন্ট দুটোই করা যায়
- Payment method: Cash, Debit/Credit Card, Check, Bank Transfer ইত্যাদি; `Due` নিজে payment method হিসেবে দেওয়া যায় না
- Card payment-এ reference code বাধ্যতামূলক; optional note যোগ করা যায়
- প্রতিটি collection-এর জন্য আলাদা **printable collection receipt** এবং customer-এর collection history
- Outstanding বের করার নিয়ম: `original Due amount − receivable collection ledger`
- Collection আলাদা টেবিলে রাখা হয়, তাই Sales revenue, Dashboard, Reports বা Cashup-এর `Total Sales` দ্বিগুণ হয় না
- Cashup-এ **Due Collections (Cash)** আলাদা লাইনে দেখায় এবং শুধু expected cash-এ যোগ হয়; sales total-এ যোগ হয় না
- মডিউল রেজিস্ট্রেশন: `sort = 112`, permission `receivables`; collection-ও একই `receivables` grant ব্যবহার করে (আলাদা permission যোগ করা হয়নি)
- ফাইল: `app/Controllers/Receivables.php`, `app/Models/Receivable.php`, `app/Views/receivables/manage.php`, `details.php`, `collect.php`, `receipt.php`, `_flash.php`, language, `public/css/pro-ui.css` ও `public/images/menubar/receivables.svg`

### Due collection ডাটাবেস টেবিল

| টেবিল | কাজ |
|---|---|
| `ospos_receivable_payments` | Due collection ledger: invoice, customer, payment method, amount, time, employee, reference ও note |

- সম্পূর্ণ স্কিমা: `database_enhancements.sql` (শেষে `ospos_receivable_payments` block)
- আলাদা স্কিমা কপি: `writable\backups\due-collection-20261022-095454\receivable_payments_schema.sql`
- Receivables-এর outstanding = `ospos_sales_payments`-এর Due rows − এই table-এর collections
- Cashup expected cash = opening cash + in/out cash + net cash sales + **cash due collections** − cash expenses
- Cashup `total_sales`, `net_total`, Sales list, Dashboard revenue ও payment reports অপরিবর্তিত থাকে

---

## ১০. নতুন মডিউল: Rentals (ভাড়া)

মাদুর, শাল, কাঞ্জার, সিন্দুক ইত্যাদি ভাড়ায় দেওয়া সরঞ্জামের জন্য সম্পূর্ণ আলাদা মডিউল। এটি নিজস্ব ক্যাটালগ, ইনভয়েস, লাইন ও রিটার্ন ইভেন্ট ব্যবহার করে; rent ও late fee completed Sale হিসেবে OSPOS Sales/Cashup-এ যায়, কিন্তু rental deposit Sales revenue-এর বাইরে থাকে।

- URL: `http://localhost/public/rentals`
- আইটেম ক্যাটালগ: `http://localhost/public/rentals/items`
- নতুন রেন্টাল ইনভয়েস নম্বর: `RNT-YYYYMMDD-####` (যেমন `RNT-20261021-0001`)
- কাস্টমার সরাসরি বিদ্যমান `ospos_customers` / `ospos_people` থেকে নির্বাচন করা হয়
- আলাদা রেন্টাল স্ট্যাটাস: `Out`, `Partial`, `Returned`, `Cancelled`; **Overdue** ও **Due Today** আলাদা স্ট্যাটাস নয়, `due_date` থেকে স্বয়ংক্রিয়ভাবে নির্ধারিত হয়
- ইনভয়েস তৈরির সময় প্রতিটি লাইনে **tiered pricing** স্বয়ংক্রিয় হিসাব হয়:
  - প্রথম `base_days` দিনের জন্য `base_fee`
  - এরপর প্রতিটি অতিরিক্ত দিনে `extra_daily_rate`
  - সূত্র: `quantity × (base_fee + max(0, days - base_days) × extra_daily_rate)`
  - উদাহরণ: `base_days=4`, `base_fee=5.00`, `extra_daily_rate=3.00` হলে ৪ দিনে `5.00`; ৬ দিনে `5.00 + 2 × 3.00 = 11.00`
- ক্যাটালগে নাম, কোড, ক্যাটাগরি, বিবরণ, included days, base fee, extra day rate, deposit, মোট পরিমাণ এবং লাইভ অ্যাভেইলেবিলিটি আছে
- একই আইটেম একাধিক লাইনে দিলেও সার্ভার মিলিয়ে মোট চাহিদা যাচাই করে; স্টকের বেশি চাইলে সেভ হয় না
- **Discount**: `none`, `fixed` অথবা `percent`। Discount শুধু rent-এর উপর প্রযোজ্য, deposit-এর উপর নয়। `rent_fee` = gross rent, `discount_amount` = হিসাবকৃত discount, `rental_total` = net rent
- **Payment**: rent payment type বাধ্যতামূলক। Card/Debit/Credit payment-এ reference code বাধ্যতামূলক। Rental-এ `Due` tender নেই
- **Deposit**: ঐচ্ছিক ও refundable; rent ও deposit-এর payment type আলাদা। Deposit Sales revenue বা cashup-এ যোগ হয় না, শুধু rental record-এ liability হিসেবে থাকে
- Rental save করলে OSPOS-এ একটি completed Sale (`ospos_sales`) তৈরি/আপডেট হয়। Sale-এ একটি display line (`RENTAL-CHARGE` hidden item) ও একটি tender payment থাকে; Sale invoice number = rental invoice number
- Rental edit করলে একই Sale in-place update হয়, duplicate Sale তৈরি হয় না
- Return-এ late fee থাকলে আলাদা completed Sale (`L...` invoice) তৈরি হয়, যাতে সঠিক period-এ revenue ও cashup ধরা পড়ে
- Rental cancel করলে rent Sale এবং সংশ্লিষ্ট late-fee Sale(s) `CANCELED` হয়; canceled Sale cashup/report থেকে বাদ পড়ে
- পুরোনো rental (যেমন `RNT-...-0001`) edit না করা পর্যন্ত কোনো historical Sale backfill হয় না; এমন rental-এ paid/balance “Not tracked” হিসেবে দেখায়, ভুল unpaid balance নয়
- **আংশিক রিটার্ন** সমর্থিত: প্রতিটি লাইনে আলাদা পরিমাণ, অবস্থা (`Good` / `Damaged` / `Lost`), প্রয়োজনে সম্পাদনযোগ্য লেট ফি এবং নোট
- রিটার্নের তারিখ ও ডিউ তারিখ তুলনা করে প্রতিটি লাইনে **Return Check** দেখায়:
  - `Returned on time`
  - `Late by X day(s)`
  - `Partially returned`
  - `Not returned`
- Late fee suggestion-এ item-এর `extra_daily_rate` ব্যবহার হয়; না থাকলে `daily_rate` fallback
- তালিকায় ফিল্টার: All, Active, Out, Partial, Due Today, Overdue, Returned, Cancelled
- ইনভয়েস নম্বর, কাস্টমারের নাম/ফোন বা আইটেমের নাম দিয়ে সার্চ
- প্রিন্টেবল ইনভয়েসে কোম্পানির তথ্য, কাস্টমার, রেন্টাল তারিখ/ডিউ তারিখ, রিটার্ন চেক, রিটার্ন হিস্ট্রি, gross rent, discount, net rent, late fee, paid, balance, deposit ও payment details আছে
- `Total Charges` = net rent + late fee; `Amount Paid` = rent Sale + late-fee Sale-এর tender; deposit আলাদা দেখানো হয়
- Deposit refund এখনো manual process; deposit cashup-এ অন্তর্ভুক্ত নয়
- ডিলিট শুধু বাতিল করা (`Cancelled`) রেন্টালে, যার রিটার্ন হিস্ট্রি নেই; চলমান/আংশিক রেন্টাল আগে `Cancel` করুন। রিটার্ন থাকলে অডিটের জন্য রেকর্ড রাখতে হবে
- ভাড়ায় থাকা আইটেম ডিলিট করা যায় না; পরিমাণও বর্তমান আউটস্ট্যান্ডিং সংখ্যার নিচে নামানো যায় না

### ডাটাবেস টেবিল

| টেবিল | কাজ |
|---|---|
| `ospos_rental_items` | রেন্টাল আইটেম ক্যাটালগ, `base_days`, `base_fee`, `extra_daily_rate`, deposit ও মোট পরিমাণ |
| `ospos_rentals` | রেন্টাল ইনভয়েস হেডার, কাস্টমার, তারিখ, স্ট্যাটাস, gross rent, discount, net rent, payment/deposit details, `sale_id`, `late_fee_sale_id` |
| `ospos_rental_lines` | প্রতিটি ইনভয়েসের আইটেম, পরিমাণ, base/extra pricing breakdown, রিটার্ন পরিমাণ ও অবস্থা |
| `ospos_rental_returns` | প্রতিটি রিটার্ন ইভেন্টের তারিখ, পরিমাণ, অবস্থা, লেট ফি, নোট ও কর্মচারী |
| `ospos_sales`, `ospos_sales_items`, `ospos_sales_payments` | Rent ও late-fee-এর জন্য তৈরি completed Sale, display line ও tender payment |
| `ospos_items` (`RENTAL-CHARGE`) | Sales/report-এ পাঠযোগ্য rental charge line-এর জন্য lazy-created hidden item |

সম্পূর্ণ স্কিমা: `rental_pricing_schema.sql`; বিদ্যমান ইনস্টলে একই migration `database_enhancements.sql`-এর শেষে সংযোজিত। আগের Rentals schema: `writable\backups\rentals-20261021-234342\rentals_schema.sql`।

### ফাইল

- `app/Controllers/Rentals.php` — list, create/edit, tiered pricing, discount/payment validation, view, return, invoice, cancel, delete, item catalog
- `app/Models/Rental.php` — রেন্টাল, লাইন, রিটার্ন, সারাংশ, availability और rent/late-fee Sale sync
- `app/Models/Rentalitem.php` — রেন্টাল আইটেম ক্যাটালগ, tiered pricing ও লাইভ অ্যাভেইলেবিলিটি
- `app/Helpers/rental_helper.php` — স্ট্যাটাস, তারিখ, দিন ও Return Check হেল্পার
- `app/Views/rentals/` — `manage.php`, `form.php`, `view.php`, `return.php`, `invoice.php`, `items.php`, `item_form.php`, `_flash.php`
- `app/Language/en/Rentals.php`, `app/Language/en-GB/Rentals.php`
- `app/Language/en/Module.php`, `app/Language/en-GB/Module.php` — `rentals` / `rentals_desc` লেবেল
- `rental_pricing_schema.sql`, `database_enhancements.sql` — pricing/payment/Sale migration
- `public/images/menubar/rentals.svg`
- `public/css/pro-ui.css` — rental UI, status badge, responsive ও print invoice স্টাইল

> মডিউল রেজিস্ট্রেশন: `sort = 111`, permission `rentals`, admin (person_id 1) ও employee (person_id 3)-কে `home` menu group-এ grant। পরীক্ষামূলক সব রেন্টাল/আইটেম ডেটা মুছে ফেলা হয়েছে; টেবিলগুলো ব্যবহারকারীর ডেটার জন্য প্রস্তুত।

### Rentals মডিউলের DB রেজিস্ট্রেশন (ইতিমধ্যে প্রয়োগ করা)

```sql
INSERT INTO ospos_modules (module_id, name_lang_key, desc_lang_key, sort) VALUES
('rentals', 'module_rentals', 'module_rentals_desc', 111);

INSERT INTO ospos_permissions (permission_id, module_id) VALUES
('rentals', 'rentals');

INSERT INTO ospos_grants (permission_id, person_id, menu_group) VALUES
('rentals', 1, 'home'), ('rentals', 3, 'home');
```

---

## ১১. Barcode fix

আগে ১৩-ডিজিটের EAN barcode কিন্তু global setting `UPCA` হলে Picqer library exception ছুঁড়ে দিত এবং barcode sheet-এ stack trace ছাপা হতো।

- ১৩ ডিজিট + `UPCA` → স্বয়ংক্রিয়ভাবে `EAN13`
- ১২ ডিজিট + `EAN13` → `UPCA`
- ৮ ডিজিট + `UPCA`/`EAN13` → `EAN8`
- অন্য invalid value হলে barcode sheet-এ সংক্ষিপ্ত friendly message; stack trace আর কখনো পেজে ছাপে না, শুধু server log-এ যায়
- ফাইল: `app/Libraries/Barcode_lib.php`, `app/Language/en/Items.php`, `app/Language/en-GB/Items.php`

---

## ১২. পরিবর্তিত ফাইলের তালিকা

| ফাইল | ধরন |
|---|---|
| `app/Models/Dashboard.php` | নতুন |
| `app/Controllers/Home.php` | পরিবর্তিত |
| `app/Views/home/home.php` | নতুন ড্যাশবোর্ড UI |
| `app/Views/sales/receipt_default.php` | নতুন রসিদ ডিজাইন |
| `app/Language/en/Dashboard.php` | নতুন |
| `app/Language/en/Sales.php`, `en-GB/Sales.php` | নতুন কী |
| `app/Models/Sale.php` | ডিবাগ লগ সরানো |
| `.env` | পরিষ্কার + cache file |
| `php.ini` | OPcache চালু |
| `public/.htaccess`, `public/uploads/.htaccess`, `.htaccess` | নিরাপত্তা |
| `scripts/backup.ps1`, `restore.ps1`, `backup.bat`, `install-backup-task.ps1` | নতুন |
| `database_enhancements.sql` | নতুন ইনডেক্স |
| `.env.production.example` | নতুন টেমপ্লেট |
| `public/css/pro-ui.css` | নতুন গ্লোবাল UI ডিজাইন সিস্টেম |
| `app/Views/partial/page_header.php` | নতুন শেয়ার্ড পেজ হেডার |
| `app/Views/partial/header.php` | টপবার, নেভ, ফুটার লেআউট ও CSS লোড |
| `app/Views/items/manage.php`, `attributes/manage.php`, `cashups/manage.php`, `expenses/manage.php`, `expenses_categories/manage.php`, `giftcards/manage.php`, `item_kits/manage.php`, `people/manage.php`, `sales/manage.php` | পেজ হেডার / UI |
| `app/Views/taxes/manage.php`, `configs/manage.php`, `reports/listing.php` | পেজ হেডার + ট্যাব/রিপোর্ট কার্ড |
| `app/Views/messages/sms.php` | কার্ড-ভিত্তিক SMS ফর্ম |
| `app/Views/home/office.php` | মডিউল কার্ড গ্রিড |
| `app/Views/home/home.php` | ড্যাশবোর্ড প্রাইমারি গ্রেডিয়েন্ট |
| `app/Views/sales/register.php` | রেজিস্টার প্যালেট ও খালি কার্ট |
| `app/Views/login.php` | লগইন প্যালেট |
| `app/Controllers/Cashups.php`, `app/Models/Cashup.php` | Cashup reconciliation হিসাব ও নতুন কলাম |
| `app/Views/cashups/form.php` | নতুন cash reconciliation UI |
| `app/Helpers/tabular_helper.php` | Cashup তালিকার নতুন কলাম |
| `app/Controllers/Stockalerts.php`, `app/Models/Stockalert.php`, `app/Views/stockalerts/manage.php` | নতুন Stock Alerts মডিউল |
| `app/Controllers/Receivables.php`, `app/Models/Receivable.php`, `app/Views/receivables/manage.php`, `details.php`, `collect.php`, `receipt.php`, `_flash.php` | Receivables মডিউল ও Due Collection |
| `app/Models/Cashup.php`, `app/Controllers/Cashups.php`, `app/Views/cashups/form.php` | Due collection cash reconciliation (sales total অপরিবর্তিত) |
| `app/Controllers/Rentals.php`, `app/Models/Rental.php`, `app/Models/Rentalitem.php` | Rentals মডিউল, tiered pricing, discount/payment validation ও Sale sync |
| `app/Views/rentals/manage.php`, `form.php`, `view.php`, `return.php`, `invoice.php`, `items.php`, `item_form.php`, `_flash.php` | Rentals UI ও pricing/payment breakdown |
| `rental_pricing_schema.sql`, `database_enhancements.sql` | Rentals pricing/payment/Sale migration |
| `app/Helpers/rental_helper.php` | Rentals স্ট্যাটাস/তারিখ/Return Check হেল্পার |
| `app/Language/en/Rentals.php`, `en-GB/Rentals.php` | নতুন ভাষা কী |
| `app/Language/en/Module.php`, `en-GB/Module.php` | Rentals নেভ লেবেল |
| `public/images/menubar/rentals.svg` | নতুন নেভ আইকন |
| `app/Language/en/Cashups.php`, `Stockalerts.php`, `Receivables.php` এবং `en-GB` | নতুন ভাষা কী |
| `app/Libraries/Barcode_lib.php` | Barcode type auto-detect ও নিরাপদ error |
| `public/images/menubar/stockalerts.svg`, `receivables.svg`, `rentals.svg` | নতুন নেভ আইকন |
| `public/css/pro-ui.css` | Cashup/module, Rentals UI ও print invoice স্টাইল, responsive nav |
| `ospos_rental_items`, `ospos_rentals`, `ospos_rental_lines`, `ospos_rental_returns` (DB) | নতুন Rentals টেবিল |
| `app/Controllers/Supplierledger.php`, `app/Models/SupplierledgerModel.php` | Supplier Ledger মডিউল, বাকি হিসাব ও পেমেন্ট রেকর্ডিং |
| `app/Views/supplierledger/` | Supplier Ledger তালিকা, বিস্তারিত, পেমেন্ট ফর্ম ও receipt |
| `app/Language/en/Supplierledger.php`, `en-GB/Supplierledger.php` | Supplier Ledger ভাষা কী |
| `app/Language/en/Module.php`, `en-GB/Module.php` | Supplier Ledger নেভ লেবেল |
| `public/images/menubar/supplierledger.svg` | নতুন নেভ আইকন |
| `supplier_ledger_schema.sql`, `database_enhancements.sql` | Supplier payment টেবিল ও মডিউল migration |
| `ospos_supplier_payments` (DB) | সাপ্লাইয়ার পেমেন্ট ledger |
| `ospos_receivable_payments` (DB) | Due collection ledger |
| `ospos_cash_up` (DB) | নতুন reconciliation কলাম |
| `ospos_modules`, `ospos_permissions`, `ospos_grants` (DB) | নতুন মডিউল রেজিস্ট্রেশন |
| `ADVANCED_POS.md` | এই ডকুমেন্টেশন |

**ব্যাকআপ:** পরিবর্তনের আগের ফাইলগুলো `writable\backups\code-20261021-213547\`-এ রাখা আছে। UI পরিবর্তনের স্ন্যাপশট `writable\backups\ui-20261021-2255\`-এ সংরক্ষিত। Cashups ও নতুন মডিউলের আগের অবস্থা `writable\backups\cashups-modules-20261021-230517\`-এ (DB dump সহ), এবং barcode fix-এর আগের ফাইল `writable\backups\barcode-fix-20261021-232228\`-এ আছে। পূর্ববর্তী পূর্ণ স্ন্যাপশট `writable\backups\final-20261021-233523\`-এ সংরক্ষিত। Rentals মডিউলের আগের অবস্থা `writable\backups\rentals-20261021-234342\`-এ (app, CSS, menubar icon, DB dump ও `rentals_schema.sql`); কাজ শেষের পূর্ণ স্ন্যাপশট `writable\backups\rentals-final-20261022-094606\`-এ (app, CSS, icon, fresh DB dump, schema ও `MANIFEST.txt`) সংরক্ষিত। Due collection কাজ শুরুর আগের পূর্ণ স্ন্যাপশট `writable\backups\due-collection-20261022-095454\`-এ (app, CSS, DB dump, `database_enhancements.sql`, পুরোনো `ADVANCED_POS.md` ও `receivable_payments_schema.sql`) সংরক্ষিত। কাজ শেষের পূর্ণ স্ন্যাপশট `writable\backups\due-collection-final-20261022-101544\`-এ (app, CSS, fresh DB dump, `database_enhancements.sql`, `receivable_payments_schema.sql`, `ADVANCED_POS.md` ও `MANIFEST.txt`) সংরক্ষিত। Rental pricing/payment/Sale কাজ শুরুর আগের পূর্ণ স্ন্যাপশট `writable\backups\rentals-pricing-20261022-120516\`-এ; টেস্ট ডেটা পরিষ্কারের আগের safety dump `writable\backups\rentals-test-cleanup-20261022-124405\`-এ; কাজ শেষের পূর্ণ স্ন্যাপশট `writable\backups\rentals-pricing-final-20261022-124648\`-এ (app, CSS, `rental_pricing_schema.sql`, `database_enhancements.sql`, `ADVANCED_POS.md`, fresh DB dump ও `MANIFEST.txt`) সংরক্ষিত। Supplier Ledger কাজ শেষের পূর্ণ স্ন্যাপশট `writable\backups\supplier-ledger-final-20261022-173127\`-এ (app, icon, `supplier_ledger_schema.sql`, `database_enhancements.sql`, `ADVANCED_POS.md`, fresh DB dump ও `MANIFEST.txt`) সংরক্ষিত।

### নতুন মডিউলের DB রেজিস্ট্রেশন (ইতিমধ্যে প্রয়োগ করা)

নতুন ইনস্টলেশনে নিচের SQL ব্যবহার করে মডিউলগুলো যোগ করা যাবে। `person_id` নিজের DB-এর admin/employee ID অনুযায়ী বদলান।

```sql
INSERT INTO ospos_modules (module_id, name_lang_key, desc_lang_key, sort) VALUES
('receivables', 'module_receivables', 'module_receivables_desc', 112),
('supplierledger', 'module_supplierledger', 'module_supplierledger_desc', 113),
('stockalerts', 'module_stockalerts', 'module_stockalerts_desc', 115);

INSERT INTO ospos_permissions (permission_id, module_id) VALUES
('receivables', 'receivables'),
('supplierledger', 'supplierledger'),
('stockalerts', 'stockalerts');

INSERT INTO ospos_grants (permission_id, person_id, menu_group) VALUES
('receivables', 1, 'home'), ('receivables', 3, 'home'),
('supplierledger', 1, 'home'), ('supplierledger', 3, 'home'),
('stockalerts', 1, 'home'), ('stockalerts', 3, 'home');
```

---

## ১৩. নতুন মডিউল: Supplier Ledger (সাপ্লাইয়ার বাকি ও পেমেন্ট)

কোন সাপ্লাইয়ার থেকে কী কী কিনেছি, মোট কত টাকার কিনেছি, কত বাকি আছে এবং কবে কত টাকা পরিশোধ করেছি—সব এক জায়গায় দেখার জন্য নতুন **Supplier Ledger** মডিউল যোগ করা হয়েছে।

- URL: `http://localhost/public/supplierledger`
- সাপ্লাইয়ার তালিকা: মোট ক্রয়, মোট পরিশোধ, বাকি, সর্বশেষ ক্রয়/পেমেন্ট ও ক্রয় সংখ্যা
- সাপ্লাইয়ার বিস্তারিত: প্রতিটি Receiving, আইটেম ও পরিমাণের সারাংশ, ক্রয়ের মোট, ক্রয়ের সময় পরিশোধ, পেমেন্ট হিস্ট্রি ও Receipt
- পেমেন্ট: সম্পূর্ণ বা আংশিক পরিশোধ; `Due` ও cash adjustment বাদে অন্য payment method ব্যবহার করা যায়; Card/Debit/Credit-এ reference code বাধ্যতামূলক
- ঐচ্ছিক “Purchase Reference” শুধু রেকর্ডের জন্য; পেমেন্টটি সাপ্লাইয়ারের মোট বাকির বিরুদ্ধে হিসাব হয়, নির্দিষ্ট invoice-এ allocate হয় না
- বাকি হিসাব: `মোট ক্রয় − ক্রয়ের সময় পরিশোধ − পরবর্তী পেমেন্ট`; ফলাফল ঋণাত্মক হলে সেটি Advance হিসেবে দেখায়

### হিসাবের নিয়ম

| উপাদান | উৎস |
|---|---|
| মোট ক্রয় | `ospos_receivings_items`-এর quantity × unit price − discount |
| ক্রয়ের সময় পরিশোধ | Receiving-এর `payment_type` `Due`/খালি না হলে পুরো ক্রয় পরিশোধিত ধরা হয় |
| পরবর্তী পেমেন্ট | `ospos_supplier_payments` টেবিলের যোগফল |
| বাকি | মোট ক্রয় − ক্রয়ের সময় পরিশোধ − পরবর্তী পেমেন্ট |

### ডাটাবেস টেবিল

| টেবিল | কাজ |
|---|---|
| `ospos_supplier_payments` | সাপ্লাইয়ার পেমেন্টের তারিখ, মাধ্যম, পরিমাণ, ঐচ্ছিক purchase reference, reference code, note ও employee |

স্কিমা: `supplier_ledger_schema.sql`; বিদ্যমান ইনস্টলে একই migration `database_enhancements.sql`-এর শেষে সংযোজিত। মডিউল রেজিস্ট্রেশন: `sort = 113`, permission `supplierledger`, admin (person_id 1) ও employee (person_id 3)-কে `home` menu group-এ grant।

### ফাইল

- `app/Controllers/Supplierledger.php` — তালিকা, বিস্তারিত, পেমেন্ট ফর্ম/ভ্যালিডেশন ও receipt
- `app/Models/SupplierledgerModel.php` — ক্রয়ের মোট, বাকি, পেমেন্ট হিস্ট্রি ও পেমেন্ট রেকর্ডিং
- `app/Views/supplierledger/` — `manage.php`, `details.php`, `pay.php`, `receipt.php`, `_flash.php`
- `app/Language/en/Supplierledger.php`, `app/Language/en-GB/Supplierledger.php`
- `app/Language/en/Module.php`, `app/Language/en-GB/Module.php` — `supplierledger` লেবেল
- `public/images/menubar/supplierledger.svg` — নতুন নেভ আইকন
- `supplier_ledger_schema.sql`, `database_enhancements.sql` — পেমেন্ট টেবিল ও মডিউল migration

> পেমেন্ট রেকর্ডিং transaction-এ হয় এবং সাপ্লাইয়ার row lock করা হয়, তাই একই সময়ে দুটি পেমেন্ট হলেও বাকি ঋণাত্মক হবে না। বর্তমানে supplier payment Cashup-এর expected cash হিসাবের বাইরে রাখা হয়েছে; চাইলে পরে cash outflow হিসেবে যোগ করা যাবে।

**ব্যাকআপ:** এই কাজ শুরুর আগের স্ন্যাপশট `writable\backups\supplier-ledger-20261022-131032\`-এ (DB dump, `database_enhancements.sql`, Module language ফাইল ও `MANIFEST.txt`) সংরক্ষিত। কাজ শেষের পূর্ণ স্ন্যাপশট `writable\backups\supplier-ledger-final-20261022-173127\`-এ (app, icon, `supplier_ledger_schema.sql`, `database_enhancements.sql`, `ADVANCED_POS.md`, fresh DB dump ও `MANIFEST.txt`) সংরক্ষিত।

---

## ১৪. পরবর্তী ধাপে যা করা যেতে পারে

- বাংলা ভাষা প্যাক (২৪টি ফাইল, ১,৬৮০ লাইন) — চাইলে যোগ করা যাবে।
- Due collection receipt-এ কাস্টমারকে SMS/email পাঠানো (বর্তমানে শুধু প্রিন্ট/PDF ব্রাউজার থেকে)।
- লয়্যালটি/পয়েন্ট ও SMS নোটিফিকেশন।
- SSL সার্টিফিকেট + HTTPS-এ সম্পূর্ণ স্থানান্তর।
- Git ভার্সন কন্ট্রোল চালু করা (এখন রিপো নেই)।
