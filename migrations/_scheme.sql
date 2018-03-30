CREATE TABLE `RbacAuthRule`
(
  `name`      VARCHAR(64) NOT NULL,
  `data`      BLOB,
  `createdAt` TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` TIMESTAMP   NULL     DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`name`)
)
  ENGINE InnoDB;

CREATE TABLE `RbacAuthItem`
(
  `name`        VARCHAR(64) NOT NULL,
  `type`        SMALLINT    NOT NULL,
  `description` TEXT,
  `ruleName`    VARCHAR(64),
  `data`        BLOB,
  `createdAt`   TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt`   TIMESTAMP   NULL     DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`name`),
  FOREIGN KEY (`ruleName`) REFERENCES `RbacAuthRule` (`name`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  KEY `type` (`type`)
)
  ENGINE InnoDB;

CREATE TABLE `RbacAuthItemChild`
(
  `parent` VARCHAR(64) NOT NULL,
  `child`  VARCHAR(64) NOT NULL,
  PRIMARY KEY (`parent`, `child`),
  FOREIGN KEY (`parent`) REFERENCES `RbacAuthItem` (`name`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  FOREIGN KEY (`child`) REFERENCES `RbacAuthItem` (`name`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
)
  ENGINE InnoDB;

CREATE TABLE `RbacAuthAssignment`
(
  `itemName`  VARCHAR(64) NOT NULL,
  `userId`    VARCHAR(64) NOT NULL,
  `createdAt` TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`itemName`, `userId`),
  FOREIGN KEY (`itemName`) REFERENCES `RbacAuthItem` (`name`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  KEY `authAssignmentUser` (`userId`)
)
  ENGINE InnoDB;
