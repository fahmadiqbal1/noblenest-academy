#!/usr/bin/env pwsh
<#
.SYNOPSIS
    Noble Nest Academy — Full Stress-Test Runner
    Runs smoke → load → stress → spike and opens HTML reports.

.PARAMETER Phase
    Which test(s) to run: smoke | load | stress | spike | all
    Default: all

.PARAMETER BaseUrl
    The target URL. Default: http://127.0.0.1:8000

.PARAMETER StartServer
    If set, starts 'php artisan serve' automatically (local dev only).

.EXAMPLE
    .\run-stress-tests.ps1 -Phase smoke -StartServer
    .\run-stress-tests.ps1 -Phase all -BaseUrl https://staging.noblenest.academy
#>
param(
    [ValidateSet('smoke','load','stress','spike','all')]
    [string]$Phase = 'all',

    [string]$BaseUrl = 'http://127.0.0.1:8000',

    [switch]$StartServer
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

# ── Paths ──────────────────────────────────────────────────────────────────
$ProjectRoot = Split-Path -Parent (Split-Path -Parent $PSScriptRoot)
$LoadDir     = Join-Path $PSScriptRoot ""   # this script lives in tests/load
$ReportsDir  = Join-Path $LoadDir "reports"

# ── Ensure reports directory ───────────────────────────────────────────────
if (-not (Test-Path $ReportsDir)) { New-Item -ItemType Directory $ReportsDir | Out-Null }

# ── Start artisan serve if requested ──────────────────────────────────────
$serverJob = $null
if ($StartServer) {
    Write-Host "`n[runner] Starting php artisan serve on 127.0.0.1:8000 ..." -ForegroundColor Cyan
    $serverJob = Start-Process -FilePath "php" `
        -ArgumentList "artisan","serve","--port=8000" `
        -WorkingDirectory $ProjectRoot `
        -PassThru -NoNewWindow
    Start-Sleep -Seconds 4
    Write-Host "[runner] Server PID: $($serverJob.Id)" -ForegroundColor Cyan
}

# ── Run stress:audit (PHP audit) ──────────────────────────────────────────
Write-Host "`n[runner] Running artisan stress:audit ..." -ForegroundColor Cyan
Push-Location $ProjectRoot
php artisan stress:audit
Pop-Location

# ── Install Artillery deps ────────────────────────────────────────────────
Push-Location $LoadDir
if (-not (Test-Path "node_modules")) {
    Write-Host "`n[runner] Installing Artillery dependencies ..." -ForegroundColor Cyan
    npm install --silent
}

# ── Helper: run one Artillery phase ──────────────────────────────────────
function Invoke-Artillery {
    param([string]$Name)

    $ymlFile    = "$Name.yml"
    $reportJson = "reports/$Name.json"
    $reportHtml = "reports/$Name.html"

    Write-Host "`n$('─' * 60)" -ForegroundColor DarkGray
    Write-Host "[runner] Phase: $($Name.ToUpper())" -ForegroundColor Yellow
    Write-Host "$('─' * 60)" -ForegroundColor DarkGray

    $env:BASE_URL = $BaseUrl

    # Run test → JSON  (pass --target to override the YAML default)
    artillery run $ymlFile --target $BaseUrl --output $reportJson
    $exitCode = $LASTEXITCODE

    # Generate HTML report regardless of exit code
    artillery report $reportJson --output $reportHtml 2>$null

    Write-Host "[runner] Report: $((Resolve-Path $reportHtml).Path)" -ForegroundColor Green

    if ($exitCode -ne 0) {
        Write-Host "[runner] ⚠  Artillery returned exit code $exitCode for phase '$Name'" -ForegroundColor Yellow
    }

    return $exitCode
}

# ── Execute phases ────────────────────────────────────────────────────────
$phases = switch ($Phase) {
    'all'   { @('smoke', 'load', 'stress', 'spike') }
    default { @($Phase) }
}

$results = [ordered]@{}
foreach ($p in $phases) {
    $results[$p] = Invoke-Artillery -Name $p
}

Pop-Location

# ── Stop artisan serve ────────────────────────────────────────────────────
if ($null -ne $serverJob -and -not $serverJob.HasExited) {
    Stop-Process -Id $serverJob.Id -Force
    Write-Host "`n[runner] artisan serve stopped." -ForegroundColor Cyan
}

# ── Final summary ─────────────────────────────────────────────────────────
Write-Host "`n$('═' * 60)" -ForegroundColor Cyan
Write-Host "  STRESS TEST SUMMARY" -ForegroundColor Cyan
Write-Host "$('═' * 60)" -ForegroundColor Cyan
foreach ($p in $results.Keys) {
    $code  = $results[$p]
    $color = if ($code -eq 0) { 'Green' } else { 'Yellow' }
    $icon  = if ($code -eq 0) { '✅' } else { '⚠ ' }
    Write-Host "  $icon  $($p.PadRight(10))  exit=$code   report: tests/load/reports/$p.html" -ForegroundColor $color
}
Write-Host ""
Write-Host "  Open reports with: Start-Process tests/load/reports/stress.html" -ForegroundColor Gray
Write-Host "$('═' * 60)" -ForegroundColor Cyan
