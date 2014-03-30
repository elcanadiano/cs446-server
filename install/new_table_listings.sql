USE cs446_project;

CREATE TABLE `listings` (
 `id` integer NOT NULL AUTO_INCREMENT,
 `isbn_13` char(13) NOT NULL,
 `listing_price` decimal(10, 2) NOT NULL,
 `course_id` varchar(8),
 `comments` text,
 `condition` integer,
 `is_active` boolean NOT NULL DEFAULT FALSE,
 PRIMARY KEY (`id`),
 FOREIGN KEY (`isbn_13`) REFERENCES books(`isbn_13`),
 FOREIGN KEY (`course_id`) REFERENCES courses(`course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
