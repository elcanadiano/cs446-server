USE cs446_project;

CREATE TABLE `books` (
 `isbn_13` char(13) NOT NULL,
 `title` varchar(256) NOT NULL,
 `author` varchar(256),
 `publisher` varchar(256),
 `edition` int,
 `msrp` decimal(10, 2),
 `year` year(4),
 PRIMARY KEY (`isbn_13`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
