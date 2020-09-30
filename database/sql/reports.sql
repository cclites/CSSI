CREATE TABLE report(
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  check_id int(11) unsigned NOT NULL,
  report longtext NOT NULL,
  PRIMARY KEY (id),
  KEY `check_id` (`check_id`),
  FOREIGN KEY (`check_id`) REFERENCES `checks` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;