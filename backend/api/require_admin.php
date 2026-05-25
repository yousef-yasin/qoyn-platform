<?php
require_once __DIR__ . "/db.php";

if (!isset($_SESSION["user_id"])) {
  json_out(["ok"=>false,"error"=>"NOT_LOGGED_IN"], 401);
}

if (($_SESSION["role"] ?? "") !== "admin") {
  json_out(["ok"=>false,"error"=>"FORBIDDEN_ADMIN_ONLY"], 403);
}
