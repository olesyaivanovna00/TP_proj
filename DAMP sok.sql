CREATE TABLE `administrator_sites` (
  `id_administrator_sites` int NOT NULL,
  `login` varchar(120) NOT NULL,
  `password` varchar(120) NOT NULL,
  `mail` varchar(120) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `id_city` int NOT NULL
);

CREATE TABLE `area` (
  `id_area` int NOT NULL,
  `id_administrator_sites` int NOT NULL,
  `title` varchar(120) NOT NULL,
  `id_city` int NOT NULL,
  `address` varchar(250) NOT NULL,
  `img_map` varchar(120) DEFAULT NULL
);

CREATE TABLE `auction_product` (
  `id_auction_product` int NOT NULL,
  `title` varchar(120) NOT NULL,
  `id_users` int DEFAULT NULL,
  `status` varchar(50) NOT NULL,
  `price` int NOT NULL,
  `time_start` int NOT NULL,
  `bet_time` int NOT NULL,
  `img_product` int NOT NULL,
  `description_product` int NOT NULL
);

CREATE TABLE `auction_start` (
  `id_auction_start` int NOT NULL,
  `id_types_places` int NOT NULL,
  `tickets_left` int DEFAULT NULL,
  `days_left` int DEFAULT NULL,
  `increment_step` int NOT NULL
);

CREATE TABLE `auction_ticket` (
  `id_auction_ticket` int NOT NULL,
  `id_auction_start` int NOT NULL,
  `id_users` int DEFAULT NULL,
  `id_ticket` int NOT NULL,
  `status` varchar(50) NOT NULL,
  `price` int NOT NULL,
  `time_start` int NOT NULL,
  `bet_time` int NOT NULL
);

CREATE TABLE `city` (
  `id_city` int NOT NULL,
  `title` varchar(50) NOT NULL
);

CREATE TABLE `concert` (
  `id_concert` int NOT NULL,
  `id_organizer` int NOT NULL,
  `date_concert` int NOT NULL,
  `time_start_sale` int NOT NULL,
  `time_end_sale` int NOT NULL,
  `age_restriction` int NOT NULL,
  `id_genre` int NOT NULL,
  `id_area` int NOT NULL,
  `broadcast` varchar(150) DEFAULT NULL,
  `img_promo` varchar(120) DEFAULT NULL,
  `description_promo` varchar(1000) DEFAULT NULL
);

CREATE TABLE `concert_participants` (
  `id_concert` int NOT NULL,
  `id_executor` int NOT NULL
);

CREATE TABLE `executor` (
  `id_executor` int NOT NULL,
  `title` varchar(150) NOT NULL
);

CREATE TABLE `genre` (
  `id_genre` int NOT NULL,
  `title` varchar(150) NOT NULL
);

CREATE TABLE `organizer` (
  `id_organizer` int NOT NULL,
  `title` varchar(120) NOT NULL,
  `login` varchar(120) NOT NULL,
  `password` varchar(120) NOT NULL,
  `mail` varchar(120) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `payment_card` varchar(50) DEFAULT NULL,
  `id_city` int NOT NULL
);

CREATE TABLE `place_hall` (
  `id_place_hall` int NOT NULL,
  `id_area` int NOT NULL,
  `id_types_places` int NOT NULL,
  `row` int DEFAULT NULL,
  `place` int NOT NULL,
  `x_map` int DEFAULT NULL,
  `y_map` int DEFAULT NULL
);

CREATE TABLE `ticket` (
  `id_ticket` int NOT NULL,
  `id_concert` int NOT NULL,
  `id_place_hall` int NOT NULL,
  `status` varchar(50) NOT NULL,
  `id_users` int DEFAULT NULL,
  `mail` varchar(50) DEFAULT NULL,
  `price` int NOT NULL
);

CREATE TABLE `types_places` (
  `id_types_places` int NOT NULL,
  `title` varchar(120) NOT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `units` varchar(15) NOT NULL,
  `id_area` int NOT NULL
);

CREATE TABLE `users` (
  `id_users` int NOT NULL,
  `name` varchar(120) NOT NULL,
  `mail` varchar(120) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `password` varchar(120) NOT NULL,
  `payment_card` varchar(50) DEFAULT NULL
);


ALTER TABLE `administrator_sites`
  ADD PRIMARY KEY (`id_administrator_sites`),
  ADD UNIQUE KEY `login` (`login`),
  ADD UNIQUE KEY `mail` (`mail`),
  ADD KEY `id_city` (`id_city`);

ALTER TABLE `area`
  ADD PRIMARY KEY (`id_area`),
  ADD KEY `id_administrator_sites` (`id_administrator_sites`),
  ADD KEY `id_city` (`id_city`);

ALTER TABLE `auction_product`
  ADD PRIMARY KEY (`id_auction_product`),
  ADD KEY `id_users` (`id_users`);

ALTER TABLE `auction_start`
  ADD PRIMARY KEY (`id_auction_start`),
  ADD KEY `id_types_places` (`id_types_places`);

ALTER TABLE `auction_ticket`
  ADD PRIMARY KEY (`id_auction_ticket`),
  ADD KEY `id_auction_start` (`id_auction_start`),
  ADD KEY `id_users` (`id_users`),
  ADD KEY `id_ticket` (`id_ticket`);

ALTER TABLE `city`
  ADD PRIMARY KEY (`id_city`);

ALTER TABLE `concert`
  ADD PRIMARY KEY (`id_concert`),
  ADD KEY `id_area` (`id_area`),
  ADD KEY `id_genre` (`id_genre`);

ALTER TABLE `concert_participants`
  ADD KEY `id_concert` (`id_concert`),
  ADD KEY `id_executor` (`id_executor`);

ALTER TABLE `executor`
  ADD PRIMARY KEY (`id_executor`);

ALTER TABLE `genre`
  ADD PRIMARY KEY (`id_genre`);

ALTER TABLE `organizer`
  ADD PRIMARY KEY (`id_organizer`),
  ADD UNIQUE KEY `login` (`login`),
  ADD UNIQUE KEY `mail` (`mail`),
  ADD KEY `id_city` (`id_city`);

ALTER TABLE `place_hall`
  ADD PRIMARY KEY (`id_place_hall`),
  ADD KEY `id_area` (`id_area`),
  ADD KEY `id_types_places` (`id_types_places`);

ALTER TABLE `ticket`
  ADD PRIMARY KEY (`id_ticket`),
  ADD KEY `id_concert` (`id_concert`),
  ADD KEY `id_place_hall` (`id_place_hall`),
  ADD KEY `id_users` (`id_users`);

ALTER TABLE `types_places`
  ADD PRIMARY KEY (`id_types_places`),
  ADD KEY `id_area` (`id_area`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id_users`),
  ADD UNIQUE KEY `mail` (`mail`);


ALTER TABLE `administrator_sites`
  MODIFY `id_administrator_sites` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `area`
  MODIFY `id_area` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `auction_product`
  MODIFY `id_auction_product` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `auction_start`
  MODIFY `id_auction_start` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `auction_ticket`
  MODIFY `id_auction_ticket` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `city`
  MODIFY `id_city` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `concert`
  MODIFY `id_concert` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `executor`
  MODIFY `id_executor` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `genre`
  MODIFY `id_genre` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `organizer`
  MODIFY `id_organizer` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `place_hall`
  MODIFY `id_place_hall` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `ticket`
  MODIFY `id_ticket` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `types_places`
  MODIFY `id_types_places` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `users`
  MODIFY `id_users` int NOT NULL AUTO_INCREMENT;


ALTER TABLE `administrator_sites`
  ADD CONSTRAINT `administrator_sites_ibfk_1` FOREIGN KEY (`id_city`) REFERENCES `city` (`id_city`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `area`
  ADD CONSTRAINT `area_ibfk_1` FOREIGN KEY (`id_administrator_sites`) REFERENCES `administrator_sites` (`id_administrator_sites`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `area_ibfk_2` FOREIGN KEY (`id_city`) REFERENCES `city` (`id_city`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `auction_product`
  ADD CONSTRAINT `auction_product_ibfk_1` FOREIGN KEY (`id_users`) REFERENCES `users` (`id_users`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `auction_start`
  ADD CONSTRAINT `auction_start_ibfk_1` FOREIGN KEY (`id_types_places`) REFERENCES `types_places` (`id_types_places`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `auction_ticket`
  ADD CONSTRAINT `auction_ticket_ibfk_1` FOREIGN KEY (`id_auction_start`) REFERENCES `auction_start` (`id_auction_start`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `auction_ticket_ibfk_2` FOREIGN KEY (`id_users`) REFERENCES `users` (`id_users`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `auction_ticket_ibfk_3` FOREIGN KEY (`id_ticket`) REFERENCES `ticket` (`id_ticket`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `concert`
  ADD CONSTRAINT `concert_ibfk_1` FOREIGN KEY (`id_concert`) REFERENCES `organizer` (`id_organizer`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `concert_ibfk_2` FOREIGN KEY (`id_area`) REFERENCES `area` (`id_area`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `concert_ibfk_3` FOREIGN KEY (`id_genre`) REFERENCES `genre` (`id_genre`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `concert_participants`
  ADD CONSTRAINT `concert_participants_ibfk_1` FOREIGN KEY (`id_concert`) REFERENCES `concert` (`id_concert`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `concert_participants_ibfk_2` FOREIGN KEY (`id_executor`) REFERENCES `executor` (`id_executor`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `organizer`
  ADD CONSTRAINT `organizer_ibfk_1` FOREIGN KEY (`id_city`) REFERENCES `city` (`id_city`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `place_hall`
  ADD CONSTRAINT `place_hall_ibfk_1` FOREIGN KEY (`id_area`) REFERENCES `area` (`id_area`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `place_hall_ibfk_2` FOREIGN KEY (`id_types_places`) REFERENCES `types_places` (`id_types_places`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `ticket`
  ADD CONSTRAINT `ticket_ibfk_1` FOREIGN KEY (`id_concert`) REFERENCES `concert` (`id_concert`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `ticket_ibfk_2` FOREIGN KEY (`id_place_hall`) REFERENCES `place_hall` (`id_place_hall`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `ticket_ibfk_3` FOREIGN KEY (`id_users`) REFERENCES `users` (`id_users`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `types_places`
  ADD CONSTRAINT `types_places_ibfk_1` FOREIGN KEY (`id_area`) REFERENCES `area` (`id_area`) ON DELETE RESTRICT ON UPDATE RESTRICT;
