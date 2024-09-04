#!/usr/bin/php
<?php
// تعیین مسیر فایل صوتی
$soundFile = '/var/lib/asterisk/sounds/pr/welcome';

// لیست داخلی‌ها
$internalNumbers = ['200', '666', '100']; // لیست داخلی‌ها

// بررسی ورودی
if (empty($internalNumbers)) {
    exit('Internal numbers are required.');
}

// تنظیمات
$directory = '/var/spool/asterisk/outgoing/';
$date = date('YmdHis'); // تاریخ و زمان کنونی
$callerID = '000-Attention'; // کالر آیدی
$waitTime = 5; // زمان انتظار

foreach ($internalNumbers as $internalNumber) {
    $fileName = $directory . $date . '-' . $internalNumber . '.call'; // نام فایل
    $channel = 'SIP/' . $internalNumber;
    $application = 'Playback';
    $data = $soundFile; // مسیر فایل صوتی

    // محتوای فایل
    $fileContent = "Channel: $channel\n";
    $fileContent .= "CallerID: $callerID\n";
    $fileContent .= "MaxRetries: 2\n"; // تلاش‌های مجدد 
    $fileContent .= "RetryTime: 30\n"; // زمان انتظار بین تلاش‌ها
    $fileContent .= "WaitTime: $waitTime\n"; // زمان انتظار قبل از شروع تماس
    $fileContent .= "Context: default\n"; // کانتکست مورد نظر
    $fileContent .= "Extension: s\n"; // داخلی مورد نظر
    $fileContent .= "Priority: 1\n"; // اولویت اجرای تماس
    $fileContent .= "Application: $application\n";
    $fileContent .= "Data: $data\n";

    // نوشتن به فایل
    if (file_put_contents($fileName, $fileContent) === false) {
        echo "Failed to create the file for extension $internalNumber.\n";
        continue;
    }

    echo "File created successfully for extension $internalNumber.\n";
}

exit('All files created successfully.');

?>
