<?php
header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_arabic_text($text) {
    return preg_match('/[\x{0600}-\x{06FF}]/u', (string)$text) === 1;
}

function norm_text($text) {
    $text = mb_strtolower((string)$text, 'UTF-8');
    $replace = [
        'أ' => 'ا', 'إ' => 'ا', 'آ' => 'ا', 'ة' => 'ه', 'ى' => 'ي', 'ؤ' => 'و', 'ئ' => 'ي'
    ];
    $text = strtr($text, $replace);
    $text = preg_replace('/[^\p{L}\p{N}\s\.\-_]/u', ' ', $text);
    $text = preg_replace('/\s+/u', ' ', $text);
    return trim($text);
}

function contains_any($haystack, $needles) {
    foreach ((array)$needles as $needle) {
        if ($needle === '') continue;
        if (mb_strpos($haystack, norm_text($needle), 0, 'UTF-8') !== false) {
            return true;
        }
    }
    return false;
}

function score_keywords($haystack, $needles) {
    $score = 0;
    foreach ((array)$needles as $needle) {
        $needle = norm_text($needle);
        if ($needle !== '' && mb_strpos($haystack, $needle, 0, 'UTF-8') !== false) {
            $score += max(1, mb_strlen($needle, 'UTF-8'));
        }
    }
    return $score;
}

function t($value, $lang = 'en') {
    if (is_array($value)) {
        if (isset($value[$lang]) && $value[$lang] !== '') return trim((string)$value[$lang]);
        if (isset($value['en']) && $value['en'] !== '') return trim((string)$value['en']);
        if (isset($value['ar']) && $value['ar'] !== '') return trim((string)$value['ar']);
        return '';
    }
    return trim((string)$value);
}

function lines_to_text($items, $lang = 'en') {
    $lines = [];
    foreach ((array)$items as $item) {
        $text = t($item, $lang);
        if ($text !== '') $lines[] = $text;
    }
    return $lines;
}

if (!isset($_SESSION['user_id'])) {
    $lang = is_arabic_text(file_get_contents('php://input')) ? 'ar' : 'en';
    http_response_code(401);
    echo json_encode([
        'ok' => false,
        'answer' => $lang === 'ar'
            ? 'انتهت الجلسة الخاصة بك. سجّل الدخول مرة ثانية ثم اسألني من جديد.'
            : 'Your session has expired. Please sign in again and ask me once more.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
$message = trim((string)($data['message'] ?? ''));
$currentPage = basename((string)($data['current_page'] ?? 'student-dashboard.php'));
$lang = is_arabic_text($message) ? 'ar' : 'en';

if ($message === '') {
    echo json_encode([
        'ok' => false,
        'answer' => $lang === 'ar' ? 'اكتب سؤالك أولًا.' : 'Please write a question first.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$knowledgeFile = __DIR__ . '/../assets/data/qoyne_knowledge.json';
if (!is_file($knowledgeFile)) {
    echo json_encode([
        'ok' => false,
        'answer' => $lang === 'ar' ? 'ملف قاعدة المعرفة غير موجود.' : 'Knowledge base file is missing.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$knowledge = json_decode(file_get_contents($knowledgeFile), true);
if (!is_array($knowledge)) {
    echo json_encode([
        'ok' => false,
        'answer' => $lang === 'ar' ? 'قاعدة المعرفة غير صالحة.' : 'Knowledge base is invalid.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$q = norm_text($message);
$answerParts = [];
$matchedFiles = [];

$greetings = $lang === 'ar'
    ? ['مرحبا', 'اهلا', 'السلام', 'هاي', 'هلا']
    : ['hi', 'hello', 'hey'];
if (contains_any($q, $greetings)) {
    $answerParts[] = $lang === 'ar'
        ? 'مرحبًا، أنا ' . t($knowledge['assistant_name'] ?? 'QOYNE', $lang) . '. أقدر أشرح لك صفحات الموقع والأزرار والنتائج والرفع والمراحل وكيف تستخدم المنصة خطوة بخطوة.'
        : 'Hello, I am ' . t($knowledge['assistant_name'] ?? 'QOYNE', $lang) . '. I can explain the website pages, buttons, results, uploads, phases, and how to use the platform step by step.';
}

$currentPageInfo = null;
foreach (($knowledge['pages'] ?? []) as $page) {
    if (($page['file'] ?? '') === $currentPage) {
        $currentPageInfo = $page;
        break;
    }
}

$wantsCurrentPage = contains_any($q, [
    'this page', 'current page', 'here', 'what does this page do', 'explain this page',
    'هاي الصفحه', 'هذه الصفحه', 'الصفحه هاي', 'شو بتعمل هاي الصفحه', 'اشرح هاي الصفحه', 'وين انا'
]);

if ($wantsCurrentPage && $currentPageInfo) {
    $matchedFiles[] = $currentPageInfo['file'];
    if ($lang === 'ar') {
        $answerParts[] = 'أنت الآن داخل صفحة ' . $currentPageInfo['file'] . ' (' . t($currentPageInfo['title'] ?? '', $lang) . '). ' . t($currentPageInfo['purpose'] ?? '', $lang);
        $details = lines_to_text($currentPageInfo['details'] ?? [], $lang);
        $actions = lines_to_text($currentPageInfo['actions'] ?? [], $lang);
        $flow = lines_to_text($currentPageInfo['flow'] ?? [], $lang);
        if (!empty($details)) $answerParts[] = 'تفاصيل مهمة: ' . implode(' ', $details);
        if (!empty($actions)) $answerParts[] = 'الأشياء التي يمكنك عملها هنا: ' . implode(' ', $actions);
        if (!empty($flow)) $answerParts[] = 'التسلسل المعتاد داخل هذه الصفحة: ' . implode(' ', $flow);
    } else {
        $answerParts[] = 'You are currently on ' . $currentPageInfo['file'] . ' (' . t($currentPageInfo['title'] ?? '', $lang) . '). ' . t($currentPageInfo['purpose'] ?? '', $lang);
        $details = lines_to_text($currentPageInfo['details'] ?? [], $lang);
        $actions = lines_to_text($currentPageInfo['actions'] ?? [], $lang);
        $flow = lines_to_text($currentPageInfo['flow'] ?? [], $lang);
        if (!empty($details)) $answerParts[] = 'Important details: ' . implode(' ', $details);
        if (!empty($actions)) $answerParts[] = 'What you can do here: ' . implode(' ', $actions);
        if (!empty($flow)) $answerParts[] = 'Typical flow on this page: ' . implode(' ', $flow);
    }
}

$bestPage = null;
$bestPageScore = 0;
foreach (($knowledge['pages'] ?? []) as $page) {
    $keywords = array_merge((array)($page['keywords'] ?? []), [($page['file'] ?? ''), t($page['title'] ?? '', $lang), t($page['title'] ?? '', $lang === 'ar' ? 'en' : 'ar')]);
    $score = score_keywords($q, $keywords);
    if ($score > $bestPageScore) {
        $bestPageScore = $score;
        $bestPage = $page;
    }
}
if ($bestPage && $bestPageScore > 0) {
    $matchedFiles[] = $bestPage['file'];
    $details = lines_to_text($bestPage['details'] ?? [], $lang);
    $actions = lines_to_text($bestPage['actions'] ?? [], $lang);
    $flow = lines_to_text($bestPage['flow'] ?? [], $lang);
    if ($lang === 'ar') {
        $segment = 'الصفحة الأقرب لسؤالك هي ' . $bestPage['file'] . ' (' . t($bestPage['title'] ?? '', $lang) . '). ' . t($bestPage['purpose'] ?? '', $lang);
        if (!empty($details)) $segment .= ' ' . implode(' ', $details);
        if (!empty($actions)) $segment .= ' الإجراءات المتاحة: ' . implode(' ', $actions);
        if (!empty($flow)) $segment .= ' المسار المعتاد: ' . implode(' ', $flow);
    } else {
        $segment = 'The page most related to your question is ' . $bestPage['file'] . ' (' . t($bestPage['title'] ?? '', $lang) . '). ' . t($bestPage['purpose'] ?? '', $lang);
        if (!empty($details)) $segment .= ' ' . implode(' ', $details);
        if (!empty($actions)) $segment .= ' Available actions: ' . implode(' ', $actions);
        if (!empty($flow)) $segment .= ' Usual flow: ' . implode(' ', $flow);
    }
    $answerParts[] = $segment;
}

$bestFeature = null;
$bestFeatureScore = 0;
foreach (($knowledge['features'] ?? []) as $feature) {
    $score = score_keywords($q, $feature['keywords'] ?? []);
    if ($score > $bestFeatureScore) {
        $bestFeatureScore = $score;
        $bestFeature = $feature;
    }
}
if ($bestFeature && $bestFeatureScore > 0) {
    $answerParts[] = t($bestFeature['answer'] ?? '', $lang);
}

$bestFaq = null;
$bestFaqScore = 0;
foreach (($knowledge['faq'] ?? []) as $faq) {
    $score = score_keywords($q, $faq['keywords'] ?? []);
    if ($score > $bestFaqScore) {
        $bestFaqScore = $score;
        $bestFaq = $faq;
    }
}
if ($bestFaq && $bestFaqScore > 0) {
    $answerParts[] = t($bestFaq['answer'] ?? '', $lang);
}

if (contains_any($q, ['all website', 'what does the site do', 'about website', 'site overview', 'اشرح الموقع', 'عن الموقع', 'شو بعمل الموقع', 'الموقع كامل', 'كل الموقع'])) {
    $answerParts[] = t($knowledge['project_summary'] ?? '', $lang);
    $areas = lines_to_text($knowledge['student_areas'] ?? [], $lang);
    if (!empty($areas)) {
        $answerParts[] = ($lang === 'ar' ? 'أهم الأجزاء للطالب: ' : 'Main student areas: ') . implode(' ', $areas);
    }
}

if (contains_any($q, ['buttons', 'features', 'sections', 'what can i do', 'شو الاشياء الموجودة', 'الازرار', 'شو بقدر اعمل', 'اقسام الموقع'])) {
    $sections = lines_to_text($knowledge['student_areas'] ?? [], $lang);
    if (!empty($sections)) {
        $answerParts[] = ($lang === 'ar' ? 'الموقع يحتوي على هذه الأقسام الأساسية: ' : 'The website contains these core sections: ') . implode(' ', $sections);
    }
}

if (empty($answerParts)) {
    $fallback = [];
    if ($currentPageInfo) {
        $fallback[] = $lang === 'ar'
            ? 'أنت الآن داخل ' . $currentPageInfo['file'] . ' (' . t($currentPageInfo['title'] ?? '', $lang) . ').'
            : 'You are currently on ' . $currentPageInfo['file'] . ' (' . t($currentPageInfo['title'] ?? '', $lang) . ').';
    }
    $fallback[] = t($knowledge['fallback_help'] ?? '', $lang);
    $fallback[] = $lang === 'ar'
        ? 'جرّب أسئلة مثل: وين نتيجتي؟ كيف أفتح الملف الشخصي؟ شو بتعمل هاي الصفحة؟ ليش رجعني لتسجيل الدخول؟ كيف أرفع ملف؟'
        : 'Try questions like: Where are my results? How do I open my profile? What does this page do? Why was I redirected to login? How do I upload a file?';
    $answerParts = $fallback;
}

$answer = implode("\n\n", array_values(array_unique(array_filter($answerParts))));

echo json_encode([
    'ok' => true,
    'answer' => $answer,
    'lang' => $lang,
    'matched_files' => array_values(array_unique($matchedFiles)),
    'current_page' => $currentPage
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
