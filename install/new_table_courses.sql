USE cs446_project;

CREATE TABLE `courses` (
	`course_id` varchar(15),
	`subject` varchar(15) NOT NULL,
	`catalog_number` varchar(7) NOT NULL,
	PRIMARY_KEY(course_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		
