<?php
require_once __DIR__ . "/db.php";
require_login();

$user_id = (int)$_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] === "GET") {
  $term_credits = 15;
  $q = $conn->prepare("SELECT term_credits FROM user_settings WHERE user_id=? LIMIT 1");
  $q->bind_param("i",$user_id);
  $q->execute();
  $r = $q->get_result();
  if ($row = $r->fetch_assoc()) $term_credits = (int)$row["term_credits"];
  $q->close();
  json_out(["ok"=>true,"term_credits"=>$term_credits]);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $body = json_decode(file_get_contents("php://input"), true);
  $term_credits = (int)($body["term_credits"] ?? 15);
  if ($term_credits < 9) $term_credits = 9;
  if ($term_credits > 21) $term_credits = 21;

  $q = $conn->prepare("INSERT INTO user_settings (user_id, term_credits)
    VALUES (?,?)
    ON DUPLICATE KEY UPDATE term_credits=VALUES(term_credits)");
  $q->bind_param("ii",$user_id,$term_credits);
  $q->execute();
  $q->close();
  json_out(["ok"=>true,"term_credits"=>$term_credits]);
}

json_out(["error"=>"METHOD_NOT_ALLOWED"], 405);
