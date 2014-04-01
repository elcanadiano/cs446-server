USE cs446_project;

CREATE TABLE `temp_contact_info` (
 `lid` int NOT NULL,
 `first_name` varchar(255),
 `middle_name` varchar(255),
 `last_name` varchar(255),
 `email` varchar(255),
 `phone_number` varchar(31),
 PRIMARY KEY (`lid`),
 FOREIGN KEY (`lid`) REFERENCES listings(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
