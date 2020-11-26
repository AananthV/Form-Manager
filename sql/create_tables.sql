CREATE TABLE `users` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`username` varchar(32) NOT NULL UNIQUE,
	`password_hashed` varchar(256) NOT NULL,
	`first_name` varchar(32) NOT NULL,
	`last_name` varchar(32) NOT NULL,
	`forms` INT NOT NULL DEFAULT '0',
	`answers` INT NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
);

CREATE TABLE `forms` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`owner` INT NOT NULL,
	`title` varchar(128) NOT NULL,
	`description` varchar(512) NOT NULL,
	`questions` INT NOT NULL DEFAULT '0',
	`answers` INT NOT NULL DEFAULT '0',
	`active` BINARY NOT NULL DEFAULT true,
	`expires` BINARY NOT NULL DEFAULT false,
	`expiry` DATETIME,
	PRIMARY KEY (`id`)
);

CREATE TABLE `questions` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`parent_form` INT NOT NULL,
	`question` varchar(128) NOT NULL,
	`description` varchar(512) NOT NULL,
	`type` INT NOT NULL,
	`isRequired` BINARY NOT NULL DEFAULT false,
	`hasOther` BOOLEAN NOT NULL DEFAULT false,
	`isValidated` BINARY NOT NULL DEFAULT false,
	PRIMARY KEY (`id`)
);

CREATE TABLE `choices` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`parent_question` INT NOT NULL,
	`choice` varchar(128) NOT NULL,
	`isOther` BOOLEAN NOT NULL DEFAULT false,
	`times_chosen` INT NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
);

CREATE TABLE `answers` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`answered` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`user` INT NOT NULL,
	`form` INT NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `short_text_answers` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`parent_answer` INT NOT NULL,
	`question` INT NOT NULL,
	`value` varchar(128) NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `long_text_answers` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`parent_answer` INT NOT NULL,
	`question` INT NOT NULL,
	`value` varchar(512) NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `choice_answers` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`parent_answer` INT NOT NULL,
	`question` INT NOT NULL,
	`choice` INT NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `validation` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`question` INT NOT NULL,
	`type` VARCHAR(32) NOT NULL,
	`subtype` VARCHAR(32) NOT NULL,
	`left_` FLOAT NOT NULL,
	`right_` FLOAT NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `notifications` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`user` INT NOT NULL,
	`type` INT NOT NULL,
	`title` VARCHAR(128) NOT NULL,
	`text` VARCHAR(256) NOT NULL,
	PRIMARY KEY (`id`)
);

ALTER TABLE `forms` ADD CONSTRAINT `forms_fk0` FOREIGN KEY (`owner`) REFERENCES `users`(`id`);

ALTER TABLE `questions` ADD CONSTRAINT `questions_fk0` FOREIGN KEY (`parent_form`) REFERENCES `forms`(`id`);

ALTER TABLE `choices` ADD CONSTRAINT `choices_fk0` FOREIGN KEY (`parent_question`) REFERENCES `questions`(`id`);

ALTER TABLE `answers` ADD CONSTRAINT `answers_fk0` FOREIGN KEY (`user`) REFERENCES `users`(`id`);

ALTER TABLE `answers` ADD CONSTRAINT `answers_fk1` FOREIGN KEY (`form`) REFERENCES `forms`(`id`);

ALTER TABLE `short_text_answers` ADD CONSTRAINT `short_text_answers_fk0` FOREIGN KEY (`parent_answer`) REFERENCES `answers`(`id`);

ALTER TABLE `short_text_answers` ADD CONSTRAINT `short_text_answers_fk1` FOREIGN KEY (`question`) REFERENCES `questions`(`id`);

ALTER TABLE `long_text_answers` ADD CONSTRAINT `long_text_answers_fk0` FOREIGN KEY (`parent_answer`) REFERENCES `answers`(`id`);

ALTER TABLE `long_text_answers` ADD CONSTRAINT `long_text_answers_fk1` FOREIGN KEY (`question`) REFERENCES `questions`(`id`);

ALTER TABLE `choice_answers` ADD CONSTRAINT `choice_answers_fk0` FOREIGN KEY (`parent_answer`) REFERENCES `answers`(`id`);

ALTER TABLE `choice_answers` ADD CONSTRAINT `choice_answers_fk1` FOREIGN KEY (`question`) REFERENCES `questions`(`id`);

ALTER TABLE `choice_answers` ADD CONSTRAINT `choice_answers_fk2` FOREIGN KEY (`choice`) REFERENCES `choices`(`id`);

ALTER TABLE `validation` ADD CONSTRAINT `validation_fk0` FOREIGN KEY (`question`) REFERENCES `questions`(`id`);

ALTER TABLE `notifications` ADD CONSTRAINT `notifications_fk0` FOREIGN KEY (`user`) REFERENCES `users`(`id`);
