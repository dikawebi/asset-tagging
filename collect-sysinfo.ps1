# ============================================================
# collect-sysinfo.ps1 - Kumpulkan sysinfo PC untuk staging registrasi aset
# Flow Kasus 2: stiker QR belum tertempel, assign berurutan ke dummy.
#
# Output CSV ramping (hanya data perangkat):
#   serial_number,model,brand
#
# Lokasi, departemen, kategori, dan pemegang diisi MANUAL per batch
# di form staging (satu upload = satu kelompok).
#
# Pemakaian:
#   .\collect-sysinfo.ps1
#   .\collect-sysinfo.ps1 -OutFile ".\sysinfo-bua.csv"
#
# Untuk banyak PC: ulangi dengan -Append ke file yang sama
# (satu baris per PC), pastikan header hanya satu di baris pertama:
#   .\collect-sysinfo.ps1 -OutFile ".\sysinfo-bua.csv"
#   .\collect-sysinfo.ps1 -OutFile ".\sysinfo-bua.csv" -Append   # di PC berikutnya
#
# Catatan: jalankan dengan akun yang bisa akses WMI/CIM di PC target.
# ============================================================
param(
    [string]$OutFile = ".\sysinfo.csv",
    [switch]$Append
)

$ErrorActionPreference = "Stop"

$cs = Get-CimInstance -ClassName Win32_ComputerSystem
$bios = Get-CimInstance -ClassName Win32_BIOS

$serial = ($bios.SerialNumber | ForEach-Object { "$_".Trim() }) -join " "
$model = "$($cs.Manufacturer)".Trim() + " " + "$($cs.Model)".Trim()
$brand = "$($cs.Manufacturer)".Trim()

$row = [pscustomobject]@{
    serial_number = $serial.Trim()
    model         = $model.Trim()
    brand         = $brand
}

if (-not $Append -or -not (Test-Path $OutFile)) {
    $row | Export-Csv -Path $OutFile -NoTypeInformation -Encoding UTF8
} else {
    $row | Export-Csv -Path $OutFile -NoTypeInformation -Encoding UTF8 -Append
}

Write-Host "Tersimpan: $OutFile" -ForegroundColor Green
$row | Format-List
