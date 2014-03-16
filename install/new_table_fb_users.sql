USE cs446_project;

CREATE TABLE `fb_users` (
 `user_id` int NOT NULL,
 `access_token` varchar(255) NOT NULL,
 `full_name` varchar(255),
 `email` varchar(255),
 `pic_square` varchar(255),
 `pic_small` varchar(255),
 `phone_number` varchar(31),
 `is_verified` boolean NOT NULL DEFAULT FALSE,
 PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
