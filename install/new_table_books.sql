USE cs446_project;

CREATE TABLE `books` (
 `isbn_13` char(13) NOT NULL,
 `title` varchar(256) NOT NULL,
 `author` varchar(256),
 `publisher` varchar(256),
 `edition` varchar(64),
 `msrp` decimal(10, 2),
 `year` year(4),
 `amazon_detail_url` varchar(1024),
 `amazon_small_image` varchar(1024),
 `amazon_medium_image` varchar(1024),
 `amazon_large_image` varchar(1024), 
 PRIMARY KEY (`isbn_13`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
