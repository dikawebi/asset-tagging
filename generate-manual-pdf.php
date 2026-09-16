<?php

/**
 * Generate ulang MANUAL-PENGGUNA.pdf dari MANUAL-PENGGUNA.md.
 *
 * Cara pakai: php generate-manual-pdf.php
 * Membutuhkan: dompdf/dompdf + league/commonmark (sudah di composer.json).
 */

require __DIR__ . '/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;
use League\CommonMark\CommonMarkConverter;

$md = file_get_contents(__DIR__ . '/MANUAL-PENGGUNA.md');
// Buang baris judul H1 (jadi cover) agar tidak duplikat
$md = preg_replace('/^# Manual Pengguna.*\n/', '', $md, 1);

$converter = new CommonMarkConverter();
$body = $converter->convertToHtml($md);

$date = date('d F Y');
$html = <<<HTML
<!DOCTYPE html>
<html><head><meta charset="utf-8">
<style>
  @page { margin: 60px 50px 70px 50px; }
  body { font-family: DejaVu Sans, sans-serif; font-size: 10.5pt; line-height: 1.6; color: #1a1a1a; }
  .cover { text-align: center; padding-top: 160px; page-break-after: always; }
  .cover .brand { display: inline-block; background: #0066ff; color: #ffffff; font-size: 13pt; font-weight: bold; letter-spacing: 3px; padding: 8px 26px; border-radius: 20px; margin-bottom: 30px; }
  .cover h1 { font-size: 30pt; color: #0f172a; margin: 0 0 12px 0; line-height: 1.25; }
  .cover .sub { font-size: 13pt; color: #475569; margin-bottom: 40px; }
  .cover .meta { font-size: 10pt; color: #64748b; }
  .cover .bar { width: 120px; height: 5px; background: #0066ff; margin: 30px auto; }
  h2 { font-size: 16pt; color: #0f172a; border-bottom: 3px solid #0066ff; padding-bottom: 6px; margin-top: 28px; page-break-after: avoid; }
  h3 { font-size: 12.5pt; color: #1d4ed8; margin-top: 20px; page-break-after: avoid; }
  table { width: 100%; border-collapse: collapse; margin: 12px 0; font-size: 10pt; }
  th { background: #0066ff; color: #ffffff; text-align: left; padding: 7px 9px; }
  td { border: 1px solid #cbd5e1; padding: 6px 9px; vertical-align: top; }
  tr:nth-child(even) td { background: #f1f5f9; }
  blockquote { border-left: 4px solid #f59e0b; background: #fffbeb; margin: 12px 0; padding: 8px 14px; font-size: 10pt; }
  code { background: #f1f5f9; padding: 1px 5px; border-radius: 4px; font-size: 9.5pt; }
  ul, ol { margin: 8px 0; padding-left: 24px; }
  li { margin-bottom: 4px; }
  hr { border: none; border-top: 1px solid #e2e8f0; margin: 20px 0; }
  a { color: #0066ff; text-decoration: none; }
</style></head><body>
<div class="cover">
  <div class="brand">PT BORNEO PRIMA</div>
  <h1>Manual Pengguna<br>Asset Tagging</h1>
  <div class="sub">Sistem Inventaris Aset Berbasis QR</div>
  <div class="bar"></div>
  <div class="meta">Dokumen versi 1.1 &nbsp;•&nbsp; {$date}<br>Panduan operasional sistem inventaris aset</div>
</div>
{$body}
</body></html>
HTML;

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('defaultFont', 'DejaVu Sans');
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html, 'UTF-8');
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$canvas = $dompdf->getCanvas();
$canvas->page_text(50, 800, 'Manual Pengguna — PT Borneo Prima Asset Tagging', null, 8, [0.4, 0.45, 0.55]);
$canvas->page_text(545, 800, 'Hal. {PAGE_NUM} / {PAGE_COUNT}', null, 8, [0.4, 0.45, 0.55]);

$out = __DIR__ . '/MANUAL-PENGGUNA.pdf';
file_put_contents($out, $dompdf->output());
echo 'OK: ' . filesize($out) . " bytes -> $out\n";
