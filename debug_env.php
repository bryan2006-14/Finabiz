<?php
echo "<pre>";
echo "GOOGLE_CLIENT_ID: " . var_export($_ENV['GOOGLE_CLIENT_ID'] ?? getenv('GOOGLE_CLIENT_ID'), true) . "\n";
echo "GOOGLE_CLIENT_SECRET: " . (isset($_ENV['GOOGLE_CLIENT_SECRET']) || getenv('GOOGLE_CLIENT_SECRET') ? "[SET]" : "[NOT SET]") . "\n";
echo "GOOGLE_REDIRECT_URI: " . var_export($_ENV['GOOGLE_REDIRECT_URI'] ?? getenv('GOOGLE_REDIRECT_URI'), true) . "\n";
