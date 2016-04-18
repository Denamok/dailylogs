CREATE TABLE IF NOT EXISTS `logs` (
`logid` int(10) NOT NULL AUTO_INCREMENT, 
`category` varchar(100) NOT NULL,
`date` date DEFAULT NULL,
`message` varchar(1000) DEFAULT NULL,
`md5` varchar(100) DEFAULT NULL,
`status` int(3) DEFAULT NULL,
`comment` varchar(250) DEFAULT NULL,
`link` varchar(250) DEFAULT NULL,
`score` int(5) DEFAULT NULL,
`cpt` int(5) DEFAULT NULL,
`total` int(5) DEFAULT NULL,
 PRIMARY KEY (`logid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

ALTER TABLE logs ADD CONSTRAINT logs_uniques UNIQUE (category, md5, date);