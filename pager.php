#!/usr/bin/php
<?php
$soundFile = '/var/lib/asterisk/sounds/pr/welcome';
$internalNumbers = ['200', '666', '100', '201', '202', '203', '204', '205', '300', '301']; 

if (empty($internalNumbers)) {
    exit('Internal numbers are required.');
}

$directory = '/var/spool/asterisk/outgoing/';
$callerID = '000-Attention';
$waitTime = 5;
$delayBetweenGroups = 10; 
$groups = array_chunk($internalNumbers, 5);

foreach ($groups as $groupIndex => $group) {
    $datePrefix = date('YmdHis');

    foreach ($group as $index => $internalNumber) {
        $fileName = $directory . $datePrefix . '-' . $groupIndex . '-' . $internalNumber . '.call';
        $channel = 'SIP/' . $internalNumber;
        $application = 'Playback';
        $data = $soundFile;

        $fileContent = "Channel: $channel\n";
        $fileContent .= "CallerID: $callerID\n";
        $fileContent .= "MaxRetries: 2\n";
        $fileContent .= "RetryTime: 30\n";
        $fileContent .= "WaitTime: $waitTime\n";
        $fileContent .= "Context: default\n";
        $fileContent .= "Extension: s\n";
        $fileContent .= "Priority: 1\n";
        $fileContent .= "Application: $application\n";
        $fileContent .= "Data: $data\n";

        if (file_put_contents($fileName, $fileContent) === false) {
            echo "Failed to create the file for extension $internalNumber.\n";
            continue;
        }

        echo "File created successfully for extension $internalNumber.\n";
    }
    if ($groupIndex < count($groups) - 1) {
        echo "Waiting $delayBetweenGroups seconds before processing the next group...\n";
        sleep($delayBetweenGroups);
    }
}

exit('All files created successfully.');
?>
