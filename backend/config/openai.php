<?php
// config/openai.php
// يقرأ OPENAI_API_KEY من ملف .env الموجود داخل utbn-backend

$envPath = __DIR__ . "/../.env";

$OPENAI_API_KEY = "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";
$OPENAI_MODEL = "gpt-4o-mini";

if (file_exists($envPath)) {
  // parse_ini_file يقرأ format KEY=VALUE
  $env = parse_ini_file($envPath, false, INI_SCANNER_RAW);

  if (is_array($env)) {
    $OPENAI_API_KEY = trim($env["OPENAI_API_KEY"] ?? "");
    $OPENAI_MODEL = trim($env["OPENAI_MODEL"] ?? $OPENAI_MODEL);
  }
}

define("OPENAI_API_KEY", $OPENAI_API_KEY);
define("OPENAI_MODEL", $OPENAI_MODEL);
