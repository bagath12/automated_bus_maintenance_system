# Automated Bus Maintenance Scheduling and Reminder System
PHP + MySQL + TagoIO + Brevo.

## Setup (XAMPP / any PHP 8 + MySQL host)
1. Copy this folder to `htdocs/bus-maintenance`.
2. Import `schema.sql` in phpMyAdmin.
3. Edit `config.php`: DB login, Brevo API key, sender and receiver email.
4. Create the admin: `php create_admin.php admin YourPassword`
5. Open `http://localhost/bus-maintenance/` and log in.

## TagoIO simulator
1. In TagoIO, create a Device for each bus (or use the Bus Simulator) that pushes a variable named `odometer` (km). Change `TAGO_VARIABLE` in `config.php` if yours differs.
2. Copy the device token into the bus record (Buses page).

## Brevo
Create an API key, verify your sender email, put both in `config.php`.

## Using it
1. Buses: add bus numbers and tokens. Items: engine oil, coolant, filters, brakes.
2. Schedules: record the previous service (km + date) and the repeat rule (km, days, or both). Next due is calculated.
3. Dashboard: "Fetch odometer and check reminders" pulls km from TagoIO, flags Due or approaching (500 km / 7 days, adjustable), and emails the reminder once.
4. Automatic mode: schedule `php sync.php` every 15 minutes (Windows Task Scheduler or cron).
5. After servicing choose status Completed: it is logged in History and the next due is reset.
