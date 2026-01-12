<?php
/**
 * Asset URL Diagnostic Test
 * Access: http://localhost/The%20round%20table/The%20Table/public/test-assets.php
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

echo "<h1>RoundTable Asset Diagnostic</h1>";
echo "<h2>Configuration</h2>";
echo "<p><strong>APP_URL:</strong> " . config('app.url') . "</p>";
echo "<p><strong>Request URL:</strong> " . request()->url() . "</p>";
echo "<p><strong>Request Root:</strong> " . request()->root() . "</p>";

echo "<h2>Generated URLs</h2>";
$testAsset = asset('assets/css/argon-dashboard.min.css');
echo "<p><strong>Asset URL:</strong> <a href='$testAsset'>$testAsset</a></p>";

$testUrl = url('/login');
echo "<p><strong>Login URL:</strong> <a href='$testUrl'>$testUrl</a></p>";

echo "<h2>Asset Loading Tests</h2>";
echo '<link rel="stylesheet" href="' . asset('assets/css/argon-dashboard.min.css') . '">';
echo '<link rel="stylesheet" href="' . asset('assets/css/nucleo-icons.css') . '">';

echo "<p>If you see styled buttons below, CSS is loading correctly:</p>";
echo '<button class="btn btn-primary">Primary Button</button> ';
echo '<button class="btn btn-success">Success Button</button>';

echo "<h2>Direct File Test</h2>";
$localPath = __DIR__ . '/assets/css/argon-dashboard.min.css';
echo "<p><strong>Local file exists:</strong> " . (file_exists($localPath) ? 'YES' : 'NO') . "</p>";

echo "<h2>Request Info</h2>";
echo "<pre>";
echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'Not set') . "\n";
echo "SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'Not set') . "\n";
echo "DOCUMENT_ROOT: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Not set') . "\n";
echo "</pre>";
