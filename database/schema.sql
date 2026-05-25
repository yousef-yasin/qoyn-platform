-- utbn-backend/schema.sql
CREATE DATABASE IF NOT EXISTS utbn_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE utbn_db;

-- Users (matches your current table structure)
CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  phone VARCHAR(30) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Student profile (student side MVP)
CREATE TABLE IF NOT EXISTS student_profiles (
  user_id INT UNSIGNED PRIMARY KEY,
  major_id INT UNSIGNED NOT NULL,
  level INT NOT NULL DEFAULT 1,
  coins_total INT NOT NULL DEFAULT 0,
  CONSTRAINT fk_sp_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Majors + courses + mapping (study plan)
CREATE TABLE IF NOT EXISTS majors (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS courses (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(30) NOT NULL,
  name VARCHAR(160) NOT NULL,
  description TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS major_courses (
  major_id INT UNSIGNED NOT NULL,
  course_id INT UNSIGNED NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  PRIMARY KEY (major_id, course_id),
  CONSTRAINT fk_mc_major FOREIGN KEY (major_id) REFERENCES majors(id) ON DELETE CASCADE,
  CONSTRAINT fk_mc_course FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Trainings (per course)
CREATE TABLE IF NOT EXISTS trainings (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  course_id INT UNSIGNED NOT NULL,
  title VARCHAR(180) NOT NULL,
  description TEXT NULL,
  coin_reward INT NOT NULL DEFAULT 0,
  sort_order INT NOT NULL DEFAULT 0,
  CONSTRAINT fk_tr_course FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Videos (free/paid)
CREATE TABLE IF NOT EXISTS videos (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  training_id INT UNSIGNED NOT NULL,
  title VARCHAR(180) NOT NULL,
  video_url TEXT NOT NULL,
  is_paid TINYINT(1) NOT NULL DEFAULT 0,
  sort_order INT NOT NULL DEFAULT 0,
  CONSTRAINT fk_vid_training FOREIGN KEY (training_id) REFERENCES trainings(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Video progress
CREATE TABLE IF NOT EXISTS video_progress (
  user_id INT UNSIGNED NOT NULL,
  video_id INT UNSIGNED NOT NULL,
  watched TINYINT(1) NOT NULL DEFAULT 0,
  updated_at DATETIME NOT NULL,
  PRIMARY KEY (user_id, video_id),
  CONSTRAINT fk_vp_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_vp_video FOREIGN KEY (video_id) REFERENCES videos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Quizzes
CREATE TABLE IF NOT EXISTS quizzes (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  training_id INT UNSIGNED NOT NULL,
  title VARCHAR(180) NOT NULL,
  pass_score INT NOT NULL DEFAULT 60,
  CONSTRAINT fk_q_training FOREIGN KEY (training_id) REFERENCES trainings(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS quiz_questions (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  quiz_id INT UNSIGNED NOT NULL,
  question_text TEXT NOT NULL,
  correct_option_id INT UNSIGNED NULL,
  sort_order INT NOT NULL DEFAULT 0,
  CONSTRAINT fk_qq_quiz FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS quiz_options (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  question_id INT UNSIGNED NOT NULL,
  option_text TEXT NOT NULL,
  CONSTRAINT fk_qo_question FOREIGN KEY (question_id) REFERENCES quiz_questions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Quiz submissions
CREATE TABLE IF NOT EXISTS quiz_submissions (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  quiz_id INT UNSIGNED NOT NULL,
  score INT NOT NULL,
  passed TINYINT(1) NOT NULL DEFAULT 0,
  submitted_at DATETIME NOT NULL,
  CONSTRAINT fk_qs_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_qs_quiz FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Subscription (monthly/yearly) - MVP manual start
CREATE TABLE IF NOT EXISTS subscriptions (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  plan ENUM('monthly','yearly') NOT NULL,
  status ENUM('active','inactive','cancelled') NOT NULL DEFAULT 'active',
  start_at DATETIME NOT NULL,
  end_at DATETIME NULL,
  CONSTRAINT fk_sub_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Coins ledger
CREATE TABLE IF NOT EXISTS coins_ledger (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  amount INT NOT NULL,
  reason VARCHAR(50) NOT NULL,
  ref_type VARCHAR(50) NULL,
  ref_id INT NULL,
  created_at DATETIME NOT NULL,
  CONSTRAINT fk_cl_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Training rewards (ensure coins awarded only once per training)
CREATE TABLE IF NOT EXISTS training_rewards (
  user_id INT UNSIGNED NOT NULL,
  training_id INT UNSIGNED NOT NULL,
  coins_awarded INT NOT NULL,
  awarded_at DATETIME NOT NULL,
  PRIMARY KEY (user_id, training_id),
  CONSTRAINT fk_trw_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_trw_training FOREIGN KEY (training_id) REFERENCES trainings(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Certificates
CREATE TABLE IF NOT EXISTS certificates (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  title VARCHAR(200) NOT NULL,
  issued_at DATETIME NOT NULL,
  CONSTRAINT fk_cert_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
ALTER TABLE users ENGINE=InnoDB;

DROP TABLE IF EXISTS student_attachments;

CREATE TABLE student_attachments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  type ENUM('plan','experience') NOT NULL,
  title VARCHAR(200) DEFAULT NULL,
  file_path VARCHAR(255) NOT NULL,
  original_name VARCHAR(255) NOT NULL,
  mime_type VARCHAR(100) NOT NULL,
  file_size INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX (user_id),
  CONSTRAINT fk_student_attach_user
    FOREIGN KEY (user_id)
    REFERENCES users(id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
