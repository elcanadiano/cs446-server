USE cs446_project;

/**
 * NOTES:
 *	- ISBN-10 can be converted into ISBN-13 by adding 9780000000000
 *	- A 13-digit is larger than 32 bits, may change to char(13)
 */

CREATE TABLE `books` (
 `isbn_13` bigint NOT NULL,
 `title` varchar(256) NOT NULL,
 `author` varchar(256) NOT NULL,
 `edition` int,
 `msrp` decimal(10, 2),
 `year` year(4),
 PRIMARY KEY (`isbn_13`),
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
