
CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `portal_id` varchar(20) NOT NULL,
  `username` varchar(50) NOT NULL,
  `msg` varchar(500) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp()
);

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `portal_id` varchar(20) NOT NULL,
  `host_id` varchar(50) NOT NULL,
  `title` varchar(50) NOT NULL,
  `msg` varchar(1000) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `visibility` int(11) NOT NULL
);

CREATE TABLE `participants` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `portal_id` varchar(20) NOT NULL,
  `user_details` varchar(1000) NOT NULL,
  `joined_date` datetime NOT NULL DEFAULT current_timestamp(),
  `status` varchar(10) NOT NULL
);

CREATE TABLE `portals` (
  `id` int(11) NOT NULL,
  `portal_id` varchar(20) NOT NULL,
  `host_id` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `type` varchar(10) NOT NULL,
  `mode` varchar(10) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `max_reach` int(11) NOT NULL,
  `portal_desc` varchar(1000) NOT NULL,
  `list` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '[]' CHECK (json_valid(`list`)),
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '\'[]\'',
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '\'\\\'{"status":0, "participantsTabVisibility":1, "commentsTabVisibility":0}\\\'\''
);

CREATE TABLE `users` (
  `uid` int(50) NOT NULL,
  `username` varbinary(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(20) NOT NULL
);

ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `participants`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `portals`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `portal_id` (`portal_id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`uid`),
  ADD UNIQUE KEY `username` (`username`);

ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

ALTER TABLE `participants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

ALTER TABLE `portals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `users`
  MODIFY `uid` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;