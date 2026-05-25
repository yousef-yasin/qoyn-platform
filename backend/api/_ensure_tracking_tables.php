<?php
// utbn-backend/api/_ensure_tracking_tables.php
// Creates tracking tables if missing (safe add-on).

if (!function_exists("ensure_tracking_tables")) {
  function ensure_tracking_tables(mysqli $conn) {

    // 1) student_performance (detailed attempts)
    $conn->query("
      CREATE TABLE IF NOT EXISTS student_performance (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id INT UNSIGNED NOT NULL,
        video_id VARCHAR(32) NOT NULL,
        quiz_type ENUM('quick','deep') NOT NULL DEFAULT 'quick',
        attempt_no INT UNSIGNED NOT NULL DEFAULT 1,

        score INT NOT NULL DEFAULT 0,
        total INT NOT NULL DEFAULT 0,
        score_percent INT NOT NULL DEFAULT 0,

        time_spent_seconds INT NOT NULL DEFAULT 0,
        watched_percent DECIMAL(6,4) NOT NULL DEFAULT 0.0000,  -- store 0..1
        difficulty TINYINT UNSIGNED NOT NULL DEFAULT 3,         -- 1..5

        meta_json MEDIUMTEXT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

        INDEX idx_user (user_id),
        INDEX idx_video (video_id),
        INDEX idx_user_created (user_id, created_at),
        INDEX idx_user_video (user_id, video_id)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    // 2) user_behavior (event log)
    $conn->query("
      CREATE TABLE IF NOT EXISTS user_behavior (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id INT UNSIGNED NOT NULL,
        event_type VARCHAR(40) NOT NULL,     -- e.g. watch_progress, quiz_submit
        video_id VARCHAR(32) NULL,

        value_int INT NULL,
        value_float DOUBLE NULL,
        meta_json MEDIUMTEXT NULL,

        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

        INDEX idx_user (user_id),
        INDEX idx_user_created (user_id, created_at),
        INDEX idx_event (event_type),
        INDEX idx_video (video_id)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    // 3) user_level_predictions (cache latest prediction)
    $conn->query("
      CREATE TABLE IF NOT EXISTS user_level_predictions (
        user_id INT UNSIGNED NOT NULL PRIMARY KEY,
        level_label ENUM('beginner','intermediate','advanced') NOT NULL DEFAULT 'beginner',
        phase_ready TINYINT(1) NOT NULL DEFAULT 0,
        model_version VARCHAR(50) NOT NULL DEFAULT 'rule-v1',
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    // 4) video_rewards (coins per video/student)
    $conn->query("
      CREATE TABLE IF NOT EXISTS video_rewards (
        user_id INT UNSIGNED NOT NULL,
        video_id VARCHAR(32) NOT NULL,
        coins_awarded INT NOT NULL DEFAULT 0,
        score INT NOT NULL DEFAULT 0,
        total INT NOT NULL DEFAULT 0,
        score_percent INT NOT NULL DEFAULT 0,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (user_id, video_id),
        KEY idx_video (video_id)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

  }
}
