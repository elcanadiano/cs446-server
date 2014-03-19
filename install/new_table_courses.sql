USE cs446_project;

CREATE TABLE `courses` (
	`course_id` varchar(15) NOT NULL,
	`subject` varchar(15) NOT NULL,
	`catalog_number` varchar(7) NOT NULL,
	`title` varchar(255) NOT NULL,
	PRIMARY KEY (course_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
