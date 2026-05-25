<?php
// utbn-backend/api/certificate_view.php
// ✅ HTML certificate renderer (Arabic-friendly). Users can Print/Save as PDF.

require __DIR__ . "/db.php";
require_login();

$user_id = (int)($_SESSION["user_id"] ?? 0);
$id = (int)($_GET["id"] ?? 0);
if (!$id) { http_response_code(400); echo "Missing id"; exit; }

$stmt = $conn->prepare(
  "SELECT id, title, issued_at,
          COALESCE(student_name,'') AS student_name,
          COALESCE(major_name,'') AS major_name
   FROM certificates
   WHERE id=? AND user_id=?
   LIMIT 1"
);
if (!$stmt) { http_response_code(500); echo "DB error"; exit; }
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$row) { http_response_code(404); echo "Not found"; exit; }

$title  = htmlspecialchars($row["title"], ENT_QUOTES, "UTF-8");
$name   = htmlspecialchars($row["student_name"], ENT_QUOTES, "UTF-8");
$major  = htmlspecialchars($row["major_name"], ENT_QUOTES, "UTF-8");
$issued = htmlspecialchars($row["issued_at"], ENT_QUOTES, "UTF-8");
$template_rel = "/utbn-backend/assets/cert_templates/phase1.jpeg";
$template_fs  = dirname(__DIR__) . "/assets/cert_templates/phase1.jpeg";
$has_template = file_exists($template_fs);

$template_rel = "/utbn-backend/assets/cert_templates/phase1.jpeg";
$template_abs = dirname(__DIR__) . "/assets/cert_templates/phase1.jpeg";
$has_template = is_file($template_abs);

header("Content-Type: text/html; charset=utf-8");
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>شهادة</title>
  <style>
@media print {
  body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
}
</style>

  <style>
    :root{--navy:#0A2E5D;--line:rgba(0,0,0,.18)}
    body{margin:0;font-family:system-ui,-apple-system,"Segoe UI",Tahoma,Arial;background:#f4f6fb}
    .wrap{max-width:1100px;margin:24px auto;padding:16px}
    .toolbar{display:flex;gap:10px;justify-content:flex-start;margin-bottom:12px;flex-wrap:wrap}
    .btn{border:1px solid var(--line);background:white;border-radius:12px;padding:10px 14px;cursor:pointer;text-decoration:none;color:#111;display:inline-block}
    .btn.primary{background:var(--navy);color:#fff;border-color:transparent}

    /* ----- template mode ----- */
    .paper{
      position:relative;
      width:100%;
      max-width:1000px;
      margin:auto;
      border-radius:18px;
      overflow:hidden;
      box-shadow:0 20px 60px rgba(0,0,0,.08);
      border:1px solid rgba(0,0,0,.06);
      background:#fff;
      /* A4 landscape ratio ~ 1.414 */
      aspect-ratio: 1.414 / 1;
    }
    .paper img{width:100%;height:100%;object-fit:cover;display:block}

    .t{
      position:absolute;
      left:0; right:0;
      text-align:center;
      color:#0b0b0b;
      font-weight:800;
      text-shadow:0 1px 0 rgba(255,255,255,.35);
    }

    /* ✅ عدّل القيم هاي حسب قالبك (بالنسبة المئوية) */
    .t.name{top:45%;font-size:42px;color:var(--navy)}
    .t.major{top:58%;font-size:28px}
   .t.issued{
  top:80%;   /* نزّلناه لتحت */
  font-size:18px;
  font-weight:700;
  opacity:.85;
}
.ai-text{
  margin-right:8px;
  font-weight:900;
  color:#000;
}
    /* ----- fallback (no template) ----- */
    .cert{background:white;border-radius:24px;padding:40px 36px;box-shadow:0 20px 60px rgba(0,0,0,.08);border:1px solid rgba(0,0,0,.06)}
    .top{display:flex;align-items:center;justify-content:space-between;gap:12px}
    .logo{font-weight:800;letter-spacing:.5px;color:var(--navy)}
    .badge{border:1px solid rgba(10,46,93,.2);color:var(--navy);padding:6px 10px;border-radius:999px;font-size:13px}
    h1{margin:18px 0 8px;font-size:34px;color:#111}
    .sub{margin:0 0 22px;color:#444;line-height:1.7}
    .name{font-size:30px;font-weight:800;margin:10px 0;color:var(--navy)}
    .meta{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:22px}
    .box{border:1px solid rgba(0,0,0,.08);border-radius:16px;padding:12px 14px;background:rgba(0,0,0,.02)}
    .k{font-size:12px;opacity:.75;margin-bottom:6px}
    .v{font-size:16px;font-weight:700}
    .footer{margin-top:26px;display:flex;justify-content:space-between;align-items:flex-end;gap:12px}
    .sig{border-top:1px solid rgba(0,0,0,.2);width:220px;padding-top:8px;text-align:center;font-weight:700}

    /* -------- Template mode (background image exported from your PDF) -------- */
    .tplWrap{position:relative; width:100%; aspect-ratio: 1.414/1; background:#fff; border-radius:24px; overflow:hidden; border:1px solid rgba(0,0,0,.06); box-shadow:0 20px 60px rgba(0,0,0,.08)}
    .tplBg{position:absolute; inset:0; background-size:cover; background-position:center; background-repeat:no-repeat}
    .tplText{position:absolute; left:0; right:0; text-align:center; color:#0A2E5D; font-weight:800}
    /* ✅ عدّل القيم التالية لو بدك تحرّك مكان الاسم/التخصص على القالب */
    .tplName{top:45%; font-size:38px}
    .tplMajor{top:55%; font-size:22px; font-weight:700; color:#111}
    .tplIssued{top:68%; font-size:16px; font-weight:700; color:#111}

    @media print{
      body{background:#fff}
      .toolbar{display:none}
      .wrap{margin:0;max-width:none;padding:0}
      .cert{box-shadow:none;border:none;border-radius:0}
      .paper{box-shadow:none;border:none;border-radius:0;max-width:none}
    }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="toolbar">
      <button class="btn primary" onclick="window.print()">طباعة / حفظ كـ PDF</button>
      <a class="btn" href="certificate_download.php?id=<?php echo (int)$row['id']; ?>" target="_blank" rel="noopener">تحميل PDF (بسيط)</a>
    </div>

    <?php if ($has_template): ?>
      <div class="paper">
        <img src="<?php echo htmlspecialchars($template_rel, ENT_QUOTES, 'UTF-8'); ?>" alt="certificate template"/>
        <div class="t name"><?php echo $name ?: '—'; ?></div>
<div class="t issued">
  <?php echo $issued; ?>
</div>
      </div>
    <?php else: ?>
      <div class="cert">
        <div class="top">
          <div class="logo">UTBN</div>
          <div class="badge"><?php echo $title; ?></div>
        </div>

        <h1>شهادة إتمام</h1>
        <p class="sub">تشهد منصة UTBN بأن الطالب/ـة التالي قد أتم المتطلبات بنجاح.</p>

        <div class="name"><?php echo $name ?: "—"; ?></div>

        <div class="meta">
          <div class="box">
            <div class="k">التخصص</div>
            <div class="v"><?php echo $major ?: "—"; ?></div>
          </div>
          <div class="box">
            <div class="k">تاريخ الإصدار</div>
            <div class="v"><?php echo $issued; ?></div>
          </div>
        </div>

        <div class="footer">
          <div style="opacity:.8">رقم الشهادة: #<?php echo (int)$row['id']; ?></div>
          <div class="sig">التوقيع</div>
        </div>
      </div>
    <?php endif; ?>

  </div>
</body>
</html>
