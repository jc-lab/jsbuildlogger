
CREATE TABLE `buildlogs` (
`idx` int(10) unsigned NOT NULL,
  `time` datetime NOT NULL,
  `archivefile` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `buildlog_hashs` (
`fidx` int(10) unsigned NOT NULL,
  `hash` binary(32) NOT NULL,
  `build_idx` int(10) unsigned NOT NULL,
  `filename` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `buildlogs`
 ADD PRIMARY KEY (`idx`);

ALTER TABLE `buildlog_hashs`
 ADD PRIMARY KEY (`fidx`), ADD KEY `hash` (`hash`);


ALTER TABLE `buildlogs`
MODIFY `idx` int(10) unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE `buildlog_hashs`
MODIFY `fidx` int(10) unsigned NOT NULL AUTO_INCREMENT;