-- utbn-backend/seed.sql
USE utbn_db;

INSERT IGNORE INTO majors (id, name) VALUES
(1, 'Computer Science'),
(2, 'Information Technology');

INSERT IGNORE INTO courses (id, code, name, description) VALUES
(1, 'CS101', 'Introduction to Programming', 'Basics of programming, variables, conditions, loops.'),
(2, 'CS102', 'Data Structures', 'Arrays, lists, stacks, queues, trees.'),
(3, 'CS201', 'Databases', 'SQL basics, design, normalization.');

INSERT IGNORE INTO major_courses (major_id, course_id, sort_order) VALUES
(1, 1, 1),
(1, 2, 2),
(1, 3, 3);

INSERT IGNORE INTO trainings (id, course_id, title, description, coin_reward, sort_order) VALUES
(1, 1, 'Variables & Conditions', 'Learn variables and if/else.', 200, 1),
(2, 1, 'Loops & Functions', 'Learn loops and functions.', 300, 2),
(3, 2, 'Arrays & Linked Lists', 'Core data structures.', 400, 1),
(4, 3, 'SQL Fundamentals', 'SELECT, WHERE, JOIN.', 500, 1);

-- Example videos (YouTube embed URLs)
INSERT IGNORE INTO videos (id, training_id, title, video_url, is_paid, sort_order) VALUES
(1, 1, 'Intro: Variables', 'https://www.youtube.com/embed/zOjov-2OZ0E', 0, 1),
(2, 1, 'Conditions Deep Dive (Paid)', 'https://www.youtube.com/embed/1d2C5Jm4qVg', 1, 2),
(3, 2, 'Loops Basics', 'https://www.youtube.com/embed/6iF8Xb7Z3wQ', 0, 1),
(4, 2, 'Functions (Paid)', 'https://www.youtube.com/embed/9Os0o3wzS_I', 1, 2),
(5, 4, 'SQL SELECT', 'https://www.youtube.com/embed/7S_tz1z_5bA', 0, 1),
(6, 4, 'SQL JOIN (Paid)', 'https://www.youtube.com/embed/9Pzj7Aj25lw', 1, 2);

-- Quizzes
INSERT IGNORE INTO quizzes (id, training_id, title, pass_score) VALUES
(1, 1, 'Quiz: Variables & Conditions', 60),
(2, 4, 'Quiz: SQL Fundamentals', 60);

-- Quiz 1 questions
INSERT IGNORE INTO quiz_questions (id, quiz_id, question_text, sort_order) VALUES
(1, 1, 'Which one is a valid variable name in most languages?', 1),
(2, 1, 'What does an if-statement do?', 2);

INSERT IGNORE INTO quiz_options (id, question_id, option_text) VALUES
(1, 1, 'my_var'),
(2, 1, '1name'),
(3, 1, 'my var'),
(4, 1, '@test'),
(5, 2, 'It repeats code multiple times'),
(6, 2, 'It checks a condition and chooses a path'),
(7, 2, 'It defines a class'),
(8, 2, 'It connects to the database');

UPDATE quiz_questions SET correct_option_id = 1 WHERE id = 1;
UPDATE quiz_questions SET correct_option_id = 6 WHERE id = 2;

-- Quiz 2 questions
INSERT IGNORE INTO quiz_questions (id, quiz_id, question_text, sort_order) VALUES
(3, 2, 'Which SQL keyword is used to fetch data from a table?', 1),
(4, 2, 'Which clause is used to filter rows?', 2);

INSERT IGNORE INTO quiz_options (id, question_id, option_text) VALUES
(9, 3, 'SELECT'),
(10, 3, 'INSERT'),
(11, 3, 'DELETE'),
(12, 3, 'UPDATE'),
(13, 4, 'WHERE'),
(14, 4, 'GROUP BY'),
(15, 4, 'ORDER BY'),
(16, 4, 'LIMIT');

UPDATE quiz_questions SET correct_option_id = 9 WHERE id = 3;
UPDATE quiz_questions SET correct_option_id = 13 WHERE id = 4;
