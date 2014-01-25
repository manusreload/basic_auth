CREATE TABLE IF NOT EXISTS `users` (
      `email` varchar(64) COLLATE utf8_bin NOT NULL,
      `hash` varchar(32) COLLATE utf8_bin NOT NULL,
      `token` varchar(64) COLLATE utf8_bin NOT NULL,
      `name` varchar(16) COLLATE utf8_bin NOT NULL,
      `surname` varchar(32) COLLATE utf8_bin NOT NULL,
      `verificaton_code` varchar(64) COLLATE utf8_bin NOT NULL,
      `verificated` int(11) NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
      ALTER TABLE  `users` ADD UNIQUE (
      `email`
      );