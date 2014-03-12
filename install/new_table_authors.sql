USE cs446_project;

CREATE TABLE `authors` (
 `isbn_13` char(13) NOT NULL,
 `name` varchar(255) NOT NULL,
 PRIMARY KEY (`isbn_13`, `name`),
 FOREIGN KEY (`isbn_13`) REFERENCES books(`isbn_13`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
