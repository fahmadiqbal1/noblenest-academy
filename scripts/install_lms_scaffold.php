<?php
// Noble Nest Academy — LMS Scaffolding Installer
// Usage:
//   php scripts/install_lms_scaffold.php [--dest=DIR]
// Default --dest is ./noblenest-academy
// This script copies files from scaffolding/lms into the specified Laravel app directory
// and appends Noble Nest routes into routes/web.php.

ini_set('display_errors', '1');
error_reporting(E_ALL);

function arg($name, $default = null) {
    global $argv;
    foreach ($argv as $a) {
        if (strpos($a, "--$name=") === 0) {
            return substr($a, strlen($name) + 3);
        }
    }
    return $default;
}

function out($msg) { fwrite(STDOUT, $msg . PHP_EOL); }
function err($msg) { fwrite(STDERR, $msg . PHP_EOL); }

$root = __DIR__ . '/../';
$src = realpath($root . 'scaffolding/lms');
$dest = arg('dest', __DIR__ . '/../noblenest-academy');

if (!$src || !is_dir($src)) {
    err('Scaffold source not found: ' . ($src ?: ($root . 'scaffolding/lms')));
    exit(1);
}

$dest = realpath($dest) ?: $dest; // allow non-existing path

if (!is_dir($dest)) {
    err("Destination directory does not exist: $dest\nRun the fetcher first, e.g.:\n  php scripts/fetch_repo.php --dest=noblenest-academy");
    exit(1);
}

// Basic Laravel sanity checks (non-fatal)
$looksLaravel = (file_exists($dest . '/artisan') && file_exists($dest . '/composer.json'));
if (!$looksLaravel) {
    out('Warning: Destination does not look like a Laravel app (artisan/composer.json missing). Proceeding anyway...');
}

function ensureDir($dir) {
    if (!is_dir($dir)) {
        if (!mkdir($dir, 0777, true) && !is_dir($dir)) {
            throw new RuntimeException('Failed to create directory: ' . $dir);
        }
    }
}

function copyFile($srcFile, $destFile) {
    ensureDir(dirname($destFile));
    if (file_exists($destFile)) {
        // Do not overwrite existing files; keep idempotent
        return false;
    }
    $ok = copy($srcFile, $destFile);
    if (!$ok) throw new RuntimeException("Failed to copy $srcFile to $destFile");
    return true;
}

function appendRoutes($destRoutesFile, $appendContent) {
    $markerStart = "// === Noble Nest LMS routes START ===";
    $markerEnd   = "// === Noble Nest LMS routes END ===";

    $existing = file_exists($destRoutesFile) ? file_get_contents($destRoutesFile) : '';
    if (strpos($existing, $markerStart) !== false) {
        return false; // already appended
    }

    $content = $existing;
    if ($content === '') {
        $content = "<?php\n\n"; // new routes file
    } elseif (substr($content, -1) !== "\n") {
        $content .= "\n";
    }
    $content .= "\n$markerStart\n" . trim($appendContent) . "\n$markerEnd\n";
    ensureDir(dirname($destRoutesFile));
    $ok = file_put_contents($destRoutesFile, $content);
    if ($ok === false) throw new RuntimeException('Failed to write routes file: ' . $destRoutesFile);
    return true;
}

// Copy tree except special routes append file
$specialRoutesAppend = $src . '/routes/web.append.php';
$copied = 0; $skipped = 0; $appendedRoutes = false;

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($src, FilesystemIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

foreach ($iterator as $item) {
    $rel = substr($item->getPathname(), strlen($src) + 1);
    if ($item->isDir()) continue;

    // Handle routes/web.append.php specially
    if ($item->getPathname() === $specialRoutesAppend) {
        $appendContent = file_get_contents($specialRoutesAppend);
        $routesDest = rtrim($dest, DIRECTORY_SEPARATOR) . '/routes/web.php';
        $didAppend = appendRoutes($routesDest, $appendContent);
        $appendedRoutes = $appendedRoutes || $didAppend;
        continue;
    }

    $destFile = rtrim($dest, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $rel;
    $ok = copyFile($item->getPathname(), $destFile);
    if ($ok) $copied++; else $skipped++;
}

out("Scaffold copy completed. Files copied: $copied, skipped (already exist): $skipped");
out('Routes: ' . ($appendedRoutes ? 'appended' : 'already present'));

out("\nNext steps:\n  1) cd \"$dest\"\n  2) composer install\n  3) cp .env.example .env && php artisan key:generate\n  4) php artisan migrate\n  5) php artisan serve\n\nThen open http://127.0.0.1:8000 to see the Noble Nest LMS home.\nAdmin sample: /admin/courses\nAI Assistant endpoint: POST /ai/assistant/message\n\nOptional: set AI_ASSIST_PROVIDER and AI_ASSIST_API_KEY in .env to integrate a real AI provider later.");
