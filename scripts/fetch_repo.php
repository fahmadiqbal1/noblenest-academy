<?php
// PHP CLI script to fetch a GitHub repository into a local directory
// Usage:
//   php scripts/fetch_repo.php [--owner=OWNER] [--repo=REPO] [--branch=BRANCH] [--dest=DIR] [--dry-run]
// Defaults: owner=fahmadiqbal1, repo=noblenest-academy, dest=./noblenest-academy

ini_set('display_errors', '1');
error_reporting(E_ALL);

function stderr($msg) { fwrite(STDERR, $msg . PHP_EOL); }
function stdout($msg) { fwrite(STDOUT, $msg . PHP_EOL); }

function parseArg($name, $default = null) {
    global $argv;
    foreach ($argv as $arg) {
        if (strpos($arg, "--$name=") === 0) {
            return substr($arg, strlen($name) + 3);
        }
        if ($arg === "--$name") {
            return true; // boolean flag
        }
    }
    return $default;
}

function ensureExtension($ext, $hint) {
    if (!extension_loaded($ext)) {
        throw new RuntimeException("Required PHP extension '$ext' is not loaded. $hint");
    }
}

function httpRequest($url, $headers = [], $writeToFile = null) {
    $useCurl = function_exists('curl_init');
    $ua = 'NobleNestFetcher/1.0 (+https://github.com)';

    // Ensure we always send a UA, and only add a default Accept if caller hasn't provided one
    $hasAccept = false;
    foreach ($headers as $h) {
        if (stripos($h, 'Accept:') === 0) { $hasAccept = true; break; }
    }
    $baseHeaders = ["User-Agent: $ua"];
    if (!$hasAccept) { $baseHeaders[] = 'Accept: application/vnd.github+json'; }
    $headers = array_merge($baseHeaders, $headers);

    if ($useCurl) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        if ($writeToFile) {
            // Capture the response in memory and write to file ourselves to avoid leaking binary to STDOUT
            curl_setopt($ch, CURLOPT_NOBODY, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
        } else {
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, true);
        }
        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        if ($writeToFile) {
            if ($response === false || $status < 200 || $status >= 300) {
                $err = curl_error($ch);
                curl_close($ch);
                @unlink($writeToFile);
                throw new RuntimeException("Download request failed with HTTP $status" . ($err ? ": $err" : ''));
            }
            $bytes = file_put_contents($writeToFile, $response);
            curl_close($ch);
            if ($bytes === false || $bytes === 0) {
                @unlink($writeToFile);
                throw new RuntimeException("Failed to write downloaded content to file: $writeToFile");
            }
            return [null, $status, []];
        } else {
            if ($response === false) {
                $err = curl_error($ch);
                $code = curl_errno($ch);
                curl_close($ch);
                throw new RuntimeException("HTTP request failed: $err ($code)");
            }
            $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $header = substr($response, 0, $headerSize);
            $body = substr($response, $headerSize);
            curl_close($ch);
            return [$body, $status, parseHeaders($header)];
        }
    } else {
        // Fallback to file_get_contents
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => implode("\r\n", $headers),
                'ignore_errors' => true,
                'timeout' => 300,
            ]
        ]);
        if ($writeToFile) {
            $data = file_get_contents($url, false, $context);
            if ($data === false) throw new RuntimeException("HTTP request failed: $url");
            $status = parseHttpStatusFromHeaders($http_response_header ?? []);
            if ($status < 200 || $status >= 300) {
                throw new RuntimeException("HTTP $status when requesting $url");
            }
            $ok = file_put_contents($writeToFile, $data);
            if ($ok === false) throw new RuntimeException("Failed to write to $writeToFile");
            return [null, $status, $http_response_header ?? []];
        } else {
            $data = file_get_contents($url, false, $context);
            if ($data === false) throw new RuntimeException("HTTP request failed: $url");
            $status = parseHttpStatusFromHeaders($http_response_header ?? []);
            return [$data, $status, $http_response_header ?? []];
        }
    }
}

function httpHead($url, $headers = []) {
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);
        return $status;
    } else {
        $context = stream_context_create([
            'http' => [
                'method' => 'HEAD',
                'header' => implode("\r\n", $headers),
                'ignore_errors' => true,
                'timeout' => 60,
            ]
        ]);
        @file_get_contents($url, false, $context);
        return parseHttpStatusFromHeaders($http_response_header ?? []);
    }
}

function parseHeaders($raw) {
    $lines = preg_split('/\r\n|\n|\r/', trim($raw));
    $headers = [];
    foreach ($lines as $line) {
        if (strpos($line, ':') !== false) {
            [$k, $v] = explode(':', $line, 2);
            $headers[trim($k)] = trim($v);
        }
    }
    return $headers;
}

function parseHttpStatusFromHeaders($headers) {
    foreach ($headers as $h) {
        if (preg_match('#^HTTP/\d+\.\d+\s+(\d{3})#', $h, $m)) {
            return (int)$m[1];
        }
    }
    return 0;
}

function rrmdir($dir) {
    if (!is_dir($dir)) return;
    $items = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($items as $file) {
        if ($file->isDir()) {
            @rmdir($file->getPathname());
        } else {
            @chmod($file->getPathname(), 0666);
            @unlink($file->getPathname());
        }
    }
    @rmdir($dir);
}

function rcopy($src, $dst) {
    $src = rtrim($src, DIRECTORY_SEPARATOR);
    if (!is_dir($src)) throw new RuntimeException("Source directory does not exist: $src");
    if (!is_dir($dst)) {
        if (!mkdir($dst, 0777, true) && !is_dir($dst)) {
            throw new RuntimeException("Failed to create destination directory: $dst");
        }
    }
    $items = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($src, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($items as $item) {
        $rel = substr($item->getPathname(), strlen($src) + 1);
        $target = $dst . DIRECTORY_SEPARATOR . $rel;
        if ($item->isDir()) {
            if (!is_dir($target) && !mkdir($target, 0777, true) && !is_dir($target)) {
                throw new RuntimeException("Failed to create directory: $target");
            }
        } else {
            if (!is_dir(dirname($target))) {
                if (!mkdir(dirname($target), 0777, true) && !is_dir(dirname($target))) {
                    throw new RuntimeException("Failed to create directory: " . dirname($target));
                }
            }
            if (!copy($item->getPathname(), $target)) {
                throw new RuntimeException("Failed to copy file to: $target");
            }
        }
    }
}

function findTopLevelDir($dir) {
    $entries = array_values(array_filter(scandir($dir), function ($e) { return $e !== '.' && $e !== '..'; }));
    $dirs = array_values(array_filter($entries, function ($e) use ($dir) { return is_dir($dir . DIRECTORY_SEPARATOR . $e); }));
    if (count($dirs) === 1) {
        return $dir . DIRECTORY_SEPARATOR . $dirs[0];
    }
    return $dir; // fall back to given dir
}

function main() {
    $owner = parseArg('owner', getenv('REPO_OWNER') ?: 'fahmadiqbal1');
    $repo  = parseArg('repo', getenv('REPO_NAME') ?: 'noblenest-academy');
    $dest  = parseArg('dest', 'noblenest-academy');
    $branchOverride = parseArg('branch', getenv('REPO_BRANCH') ?: null);
    $dryRun = parseArg('dry-run', false) === true;

    $token = getenv('GITHUB_TOKEN') ?: '';
    if ($token) {
        $authHeader = 'Authorization: token ' . $token;
    } else {
        $authHeader = null;
        stdout('Warning: GITHUB_TOKEN not set. Proceeding unauthenticated (may hit rate limits or fail for private repos).');
    }

    $headers = $authHeader ? [$authHeader] : [];

    $repoApi = "https://api.github.com/repos/$owner/$repo";
    stdout("Fetching repository metadata: $repoApi");
    [$body, $status] = httpRequest($repoApi, $headers);
    if ($status < 200 || $status >= 300) {
        throw new RuntimeException("Failed to fetch repo metadata ($status). Ensure repo exists and token has access.");
    }
    $meta = json_decode($body, true);
    if (!is_array($meta)) {
        throw new RuntimeException('Failed to parse repo metadata JSON.');
    }
    $defaultBranch = $meta['default_branch'] ?? 'main';
    $ref = $branchOverride ?: $defaultBranch;
    stdout("Using ref: $ref");

    if ($dryRun) {
        stdout('Dry run mode: metadata fetched successfully, not downloading.');
        return 0;
    }

    ensureExtension('zip', 'Install/enable the zip extension for PHP (e.g., sudo apt-get install php-zip).');

    $zipUrl = "https://api.github.com/repos/$owner/$repo/zipball/$ref";
    stdout("Downloading zipball: $zipUrl");

    $tmpDir = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'repo_fetch_' . uniqid();
    if (!mkdir($tmpDir, 0777, true) && !is_dir($tmpDir)) {
        throw new RuntimeException("Failed to create temp dir: $tmpDir");
    }
    $zipFile = $tmpDir . DIRECTORY_SEPARATOR . 'repo.zip';

    httpRequest($zipUrl, $headers, $zipFile);

    if (!file_exists($zipFile) || filesize($zipFile) === 0) {
        throw new RuntimeException('Downloaded zip file is empty.');
    }

    $zip = new ZipArchive();
    if ($zip->open($zipFile) !== true) {
        throw new RuntimeException('Failed to open downloaded zip.');
    }
    $extractDir = $tmpDir . DIRECTORY_SEPARATOR . 'extracted';
    if (!mkdir($extractDir, 0777, true) && !is_dir($extractDir)) {
        throw new RuntimeException("Failed to create extraction directory: $extractDir");
    }
    if (!$zip->extractTo($extractDir)) {
        $zip->close();
        throw new RuntimeException('Failed to extract zip.');
    }
    $zip->close();

    $top = findTopLevelDir($extractDir);

    // Prepare destination
    $destPath = getcwd() . DIRECTORY_SEPARATOR . $dest;
    if (is_dir($destPath)) {
        stdout("Cleaning existing destination: $destPath");
        rrmdir($destPath);
    }
    if (!mkdir($destPath, 0777, true) && !is_dir($destPath)) {
        throw new RuntimeException("Failed to create destination: $destPath");
    }

    stdout("Copying files to destination: $destPath");
    rcopy($top, $destPath);

    // Cleanup temp
    rrmdir($tmpDir);

    stdout('Fetch completed successfully.');
    return 0;
}

try {
    exit(main());
} catch (Throwable $e) {
    stderr('Error: ' . $e->getMessage());
    exit(1);
}
