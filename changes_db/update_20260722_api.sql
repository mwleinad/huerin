-- ---------------------------------------------------------------------------
-- API de descarga de archivos por empresa (contract)
-- Fecha: 2026-07-22
--
-- Crea las 3 tablas de soporte de la API v1:
--   api_client -> credenciales de integracion (client_id + secret hasheado)
--   api_token  -> tokens emitidos, con vigencia de 8h y revocacion
--   api_log    -> auditoria de autenticaciones y descargas
--
-- No modifica ninguna tabla existente.
-- ---------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `api_client` (
  `apiClientId` INT NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(150) NOT NULL COMMENT 'Nombre del consumidor, ej: Portal Contable',
  `clientId`    CHAR(32) NOT NULL COMMENT 'Identificador publico, hex',
  `secretHash`  VARCHAR(255) NOT NULL COMMENT 'password_hash() del secret. El secret en claro no se guarda',
  `active`      ENUM('0','1') NOT NULL DEFAULT '1',
  `createdAt`   DATETIME NOT NULL,
  `revokedAt`   DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`apiClientId`),
  UNIQUE KEY `uq_api_client_clientId` (`clientId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `api_token` (
  `apiTokenId`  INT NOT NULL AUTO_INCREMENT,
  `apiClientId` INT NOT NULL,
  `tokenHash`   CHAR(64) NOT NULL COMMENT 'sha256 del token. El token en claro no se guarda',
  `issuedAt`    DATETIME NOT NULL,
  `expiresAt`   DATETIME NOT NULL,
  `revokedAt`   DATETIME NULL DEFAULT NULL,
  `ip`          VARCHAR(45) NOT NULL,
  PRIMARY KEY (`apiTokenId`),
  UNIQUE KEY `uq_api_token_hash` (`tokenHash`),
  KEY `ix_api_token_client` (`apiClientId`),
  KEY `ix_api_token_expires` (`expiresAt`),
  CONSTRAINT `fk_api_token_client`
    FOREIGN KEY (`apiClientId`) REFERENCES `api_client` (`apiClientId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `api_log` (
  `apiLogId`     BIGINT NOT NULL AUTO_INCREMENT,
  `event`        VARCHAR(30) NOT NULL COMMENT 'auth_ok|auth_fail|auth_locked|manifest|download|denied',
  `apiClientId`  INT NULL DEFAULT NULL,
  `apiTokenId`   INT NULL DEFAULT NULL,
  `contractId`   INT NULL DEFAULT NULL,
  `resourceType` VARCHAR(20) NULL DEFAULT NULL COMMENT 'documento|archivo|requerimiento',
  `resourceId`   INT NULL DEFAULT NULL,
  `bytes`        BIGINT NULL DEFAULT NULL,
  `ip`           VARCHAR(45) NOT NULL,
  `detail`       VARCHAR(255) NULL DEFAULT NULL,
  `createdAt`    DATETIME NOT NULL,
  PRIMARY KEY (`apiLogId`),
  KEY `ix_api_log_ip_event` (`ip`, `event`, `createdAt`),
  KEY `ix_api_log_client` (`apiClientId`, `createdAt`),
  KEY `ix_api_log_contract` (`contractId`, `createdAt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
