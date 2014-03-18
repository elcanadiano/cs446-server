USE cs446_project;

CREATE TABLE `courses` (
	`subject` varchar(8) NOT NULL,
	`catalog_number` varchar(5) NOT NULL
	CONSTRAINT course PRIMARY_KEY(subject, catalog_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
		
