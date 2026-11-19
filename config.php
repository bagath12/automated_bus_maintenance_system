<?php
define('DB_HOST','localhost'); define('DB_NAME','bus_maintenance'); define('DB_USER','root'); define('DB_PASS','');
define('BREVO_API_KEY','YOUR_BREVO_API_KEY');          // Brevo > SMTP & API > API keys
define('MAIL_FROM','you@yourdomain.com'); define('MAIL_FROM_NAME','Lion Transport Maintenance');
define('MAIL_TO','admin@yourdomain.com');              // who receives reminders
define('SOON_KM',500); define('SOON_DAYS',7);          // "approaching" thresholds
define('TAGO_VARIABLE','odometer');                    // variable name in your TagoIO simulator
