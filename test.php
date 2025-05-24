<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (extension_loaded('zip')) {
    echo "Zip extension is loaded!\n";
} else {
    echo "Zip extension is NOT loaded.\n";
    // Try to load it manually
    if (dl('php_zip.dll')) {
        echo "Zip extension loaded manually!\n";
    } else {
        echo "Failed to load Zip extension manually.\n";
    }
}
phpinfo();