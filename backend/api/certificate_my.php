<?php
require __DIR__ . "/db.php";
require_login();

$user_id = (int)$_SESSION["user_id"];

$stmt = $conn->prepare("SELECT id, title, issued_at FROM certificates WHERE user_id=? ORDER BY issued_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();

$items = [];
while ($row = $res->fetch_assoc()) {
  $id = (int)($row["id"] ?? 0);
  $row["view_url"] = "/utbn-backend/api/certificate_view.php?id=" . $id;
  $row["download_url"] = "/utbn-backend/api/certificate_download.php?id=" . $id;
  $items[] = $row;
}

json_out(["certificates"=>$items]);
