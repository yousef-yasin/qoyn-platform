<?php
session_start();
header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/db.php";
require_once __DIR__ . "/require_admin.php";

$role_key = trim($_POST["role_key"] ?? "");
if ($role_key === "") {
    http_response_code(400);
    echo json_encode(["ok"=>false,"error"=>"MISSING_ROLE_KEY"]);
    exit;
}

$conn->begin_transaction();

try {

    // 1️⃣ إنشاء المسار
    $title = strtoupper($role_key) . " AI Path";

    $st = $conn->prepare("
        INSERT INTO learning_paths (title, role_key, is_published, created_at)
        VALUES (?, ?, 0, NOW())
    ");
    $st->bind_param("ss", $title, $role_key);
    $st->execute();
    $path_id = $conn->insert_id;
$recommended = $ai_response["recommended_playlists"] ?? [];

$sort = 1;

foreach($recommended as $pl){

  $name = trim($pl["name"] ?? "");
  $subject = trim($pl["subject"] ?? "");

  if($name === "") continue;

  // توليد slug فريد
  $slugBase = preg_replace('/[^a-zA-Z0-9]+/', '-', strtolower($name));
  $slugBase = trim($slugBase, '-');
  $slug = $slugBase . "-p" . $path_id . "-" . $sort;

  // 1️⃣ إنشاء Template داخل partner_playlists
  $st = $conn->prepare("
    INSERT INTO partner_playlists
    (partner_user_id, name, slug, is_template, template_subject, path_id, source_path_id, created_at)
    VALUES
    (0, ?, ?, 1, ?, ?, ?, NOW())
  ");

  if(!$st){
    echo json_encode(["ok"=>false,"error"=>"TPL_PREP_FAILED","details"=>$conn->error]);
    exit;
  }

  $source_path_id = $path_id;

  $st->bind_param("sssii", $name, $slug, $subject, $path_id, $source_path_id);
  $st->execute();

  $template_playlist_id = (int)$conn->insert_id;

  // 2️⃣ ربطها بالمسار
  $st2 = $conn->prepare("
    INSERT INTO learning_path_playlists
    (path_id, template_playlist_id, sort_order)
    VALUES (?, ?, ?)
  ");

  if(!$st2){
    echo json_encode(["ok"=>false,"error"=>"LINK_PREP_FAILED","details"=>$conn->error]);
    exit;
  }

  $st2->bind_param("iii", $path_id, $template_playlist_id, $sort);
  $st2->execute();

  $sort++;
}
    // 2️⃣ جلب playlists المقترحة
    $sql = "
    SELECT DISTINCT p.id
    FROM v_role_skills v
    LEFT JOIN skill_subject_map m
      ON LOWER(v.skill_name) LIKE CONCAT('%', m.skill_keyword, '%')
    LEFT JOIN partner_playlists p
      ON p.is_template = 1
     AND p.template_subject = m.template_subject
    WHERE v.role_key = ?
      AND p.id IS NOT NULL
    ";

    $st2 = $conn->prepare($sql);
    $st2->bind_param("s",$role_key);
    $st2->execute();
    $rs = $st2->get_result();

    $order = 1;

    $insert = $conn->prepare("
        INSERT INTO learning_path_playlists (path_id, template_playlist_id, sort_order)
        VALUES (?, ?, ?)
    ");

    while($row = $rs->fetch_assoc()){
        $tpl_id = $row["id"];
        $insert->bind_param("iii",$path_id,$tpl_id,$order);
        $insert->execute();
        $order++;
    }

    $conn->commit();

    echo json_encode([
        "ok"=>true,
        "path_id"=>$path_id,
        "message"=>"AI Path created successfully"
    ]);

} catch(Exception $e){
    $conn->rollback();
    http_response_code(500);
    echo json_encode(["ok"=>false,"error"=>$e->getMessage()]);
}