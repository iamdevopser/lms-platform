# PowerShell script to remove nested lms-platform folders
# Run this in PowerShell as Administrator if needed

$basePath = "C:\Users\tarik\OneDrive\Masaüstü\lms-platform"

# Remove nested lms-platform folder
$nestedPath = Join-Path $basePath "lms-platform"

if (Test-Path $nestedPath) {
    Write-Host "Removing nested folder: $nestedPath"
    try {
        Remove-Item -Path $nestedPath -Recurse -Force -ErrorAction Stop
        Write-Host "Successfully removed nested folder"
    } catch {
        Write-Host "Error removing folder: $_"
        Write-Host "You may need to close any programs using files in this folder"
        Write-Host "Or run PowerShell as Administrator"
    }
} else {
    Write-Host "Nested folder not found"
}

# Verify removal
if (Test-Path $nestedPath) {
    Write-Host "WARNING: Folder still exists!"
} else {
    Write-Host "SUCCESS: Nested folder removed"
}

