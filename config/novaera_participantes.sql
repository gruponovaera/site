CREATE TABLE novaera_participantes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id VARCHAR(255) NOT NULL,
  user_name VARCHAR(255) NOT NULL,
  join_time DATETIME NOT NULL,
  start_time DATETIME NOT NULL
);
