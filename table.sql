CREATE TABLE `user_info` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4


CREATE TABLE university_info (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    major VARCHAR(100) NOT NULL,
    faculty VARCHAR(100) NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    campus VARCHAR(255) NOT NULL,
    info_id VARCHAR(20) NOT NULL,
    id_photo VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user_info(id)
);