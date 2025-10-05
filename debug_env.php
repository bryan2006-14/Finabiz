<?php
echo "<pre>";
echo "GOOGLE_CLIENT_ID: " . ($_ENV['GOOGLE_CLIENT_ID'] ?? getenv('GOOGLE_CLIENT_ID')) . "\n";
echo "GOOGLE_CLIENT_SECRET: " . ($_ENV['GOOGLE_CLIENT_SECRET'] ?? getenv('GOOGLE_CLIENT_SECRET')) . "\n";
echo "GOOGLE_REDIRECT_URI: " . ($_ENV['GOOGLE_REDIRECT_URI'] ?? getenv('GOOGLE_REDIRECT_URI')) . "\n";
echo "</pre>";
