-- ---------------------------------------------------------------------------
-- API v1: soporte de URLs de descarga firmadas (HMAC-SHA256)
-- Fecha: 2026-07-22
--
-- Guarda el secreto con el que se firman las URLs. Se genera solo la primera
-- vez que la API lo necesita, asi que no hay que editar config.php en cada
-- entorno ni copiar el secreto a mano.
--
-- El secreto NO debe salir del servidor: quien lo tenga puede firmar
-- descargas de cualquier archivo. Para rotarlo basta con borrar el renglon
-- (las URLs ya emitidas dejan de servir de inmediato):
--   DELETE FROM api_setting WHERE `name` = 'url_secret';
-- ---------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `api_setting` (
  `name`      VARCHAR(50) NOT NULL,
  `value`     VARCHAR(255) NOT NULL,
  `createdAt` DATETIME NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
