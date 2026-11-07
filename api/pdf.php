<?php
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/includes/boot.php';

$docId = (int)($_GET['id'] ?? 0);
$slug  = preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['slug'] ?? '');

$doc = null;

// 1. Public slug access
if ($slug) {
    $doc = Document::getBySlug($slug);
}
// 2. Logged-in user's own document
if (!$doc && Auth::check()) {
    $doc = Document::getForUser($docId, Auth::userId());
}
// 3. Guest / quick-mode document in session
if (!$doc && isset($_SESSION['guest_doc_id']) && (int)$_SESSION['guest_doc_id'] === $docId) {
    $doc = Document::get($docId);
}

if (!$doc) {
    http_response_code(404);
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html><html><head><title>Not Found</title></head><body style="font-family:sans-serif;padding:40px;text-align:center"><h2>Document not found</h2><p>The document may have been deleted or you do not have access.</p><a href="../index.php">Go Home</a></body></html>';
    exit;
}

$template = $doc['data']['styling']['template'] ?? 'modern';
$allowed  = ['modern','classic','biodata','executive','minimal','creative','timeline',
             'elegant','tech','corporate','compact','bold','fresh','indian','marriage_biodata'];
if (!in_array($template, $allowed, true)) $template = 'modern';
$templateFile = ROOT_PATH . '/templates/' . $template . '.php';
if (!file_exists($templateFile)) $templateFile = ROOT_PATH . '/templates/modern.php';

// Capture template HTML
$d = $doc['data'];
ob_start();
include $templateFile;
$html = ob_get_clean();

// Build safe filename from document title
$rawTitle    = $doc['title'] ?? 'document';
$safeTitle   = preg_replace('/[^a-z0-9_\- ]/i', '', $rawTitle);
$safeTitle   = trim(preg_replace('/\s+/', '_', $safeTitle));
$pdfFilename = ($safeTitle ?: 'resume') . '.pdf';

// Try mPDF
$vendorAutoload = ROOT_PATH . '/vendor/autoload.php';
if (file_exists($vendorAutoload)) {
    require_once $vendorAutoload;
    try {
        $mpdf = new \Mpdf\Mpdf([
            'mode'        => 'utf-8',
            'format'      => 'A4',
            'margin_left' => 0, 'margin_right' => 0,
            'margin_top'  => 0, 'margin_bottom' => 0,
            'margin_header' => 0, 'margin_footer' => 0,
            'tempDir'     => sys_get_temp_dir(),
            'allow_charset_conversion' => true,
        ]);
        // SetTitle expects a plain UTF-8 string, not HTML-encoded
        $mpdf->SetTitle(strip_tags($rawTitle));
        $mpdf->SetAuthor(strip_tags($doc['data']['basic']['full_name'] ?? ''));
        $mpdf->WriteHTML($html);
        $mpdf->Output($pdfFilename, 'D');
        exit;
    } catch (\Exception $e) {
        // mPDF failed — fall through to browser print
    }
}

// Fallback: serve print-ready HTML with auto-print trigger
header('Content-Type: text/html; charset=utf-8');
header('Content-Disposition: inline; filename="' . $pdfFilename . '"');
echo $html;
echo '<style>@media screen{body::after{display:block;text-align:center;padding:20px;color:#64748b;font-family:sans-serif;font-size:14px;content:"Use your browser Print dialog (Ctrl+P) to save as PDF"}}</style>';
echo '<script>window.onload = function(){ window.print(); }</script>';
