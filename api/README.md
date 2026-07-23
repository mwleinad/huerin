# API v1 — descarga de archivos por empresa

Expone el inventario de **documentos, archivos y requerimientos** de una empresa
(tabla `contract`), agrupado por tipo y con las N versiones de cada uno, mas la
descarga individual de cada version.

Todo endpoint salvo la emision del token exige `Authorization: Bearer <token>`.

---

## Instalacion

1. Aplicar la migracion:

   ```
   mysql -uroot -h127.0.0.1 huerin < changes_db/update_20260722_api.sql
   ```

   Crea `api_client`, `api_token` y `api_log`. No toca ninguna tabla existente.

2. Generar la credencial del consumidor:

   ```
   php api/tools/clientes.php crear "Portal Contable"
   ```

   Imprime `client_id` y `client_secret`. **El secret se muestra una sola vez**;
   en la BD queda unicamente su `password_hash()`.

---

## Endpoints

### `POST /api/v1/token.php`

Emite un token con vigencia de **8 horas**. Acepta JSON o formulario.

```bash
curl -s -X POST http://<host>/api/v1/token.php \
  -H "Content-Type: application/json" \
  -d '{"client_id":"<id>","client_secret":"<secret>"}'
```

```json
{
  "access_token": "e3b0c44298fc1c14...",
  "token_type": "Bearer",
  "expires_in": 28800,
  "expires_at": "2026-07-22T21:30:00-06:00"
}
```

Cuando vence, el resto de endpoints responde `401 token_expired`: el consumidor
vuelve a pedir token con la misma llamada. No hay refresh token a proposito —
con credenciales de larga vida, un refresh solo agrega superficie sin ganar nada.

`DELETE /api/v1/token.php` con el token en el encabezado lo revoca de inmediato.

Tras **10 intentos fallidos desde una IP en 15 minutos** se responde `429`
durante el resto de la ventana.

### `GET /api/v1/empresas.php`

Localiza el `contractId`. Filtros combinables: `contract_id`, `rfc`, `q`
(texto libre sobre razon social / nombre comercial / RFC), `activo` (`Si`/`No`),
`page`, `per_page` (1..200, default 50).

```bash
curl -s "http://<host>/api/v1/empresas.php?rfc=ITB190308MW8" \
  -H "Authorization: Bearer <token>"
```

### `GET /api/v1/manifiesto.php?contract_id=2663`

Inventario completo. En vez de `contract_id` se puede pasar `rfc=<RFC>` como
atajo para saltarse `empresas.php`; si el RFC pertenece a mas de una empresa
responde `409 rfc_ambiguo` con la lista de `candidatas` para reintentar con
`contract_id`. Opcionales: `grupo` (`documentos|archivos|requerimientos`) y
`solo_existentes=1` para omitir registros cuyo archivo ya no esta en disco.

```bash
curl -s "http://<host>/api/v1/manifiesto.php?rfc=ITB190308MW8" \
  -H "Authorization: Bearer <token>"
```

```json
{
  "empresa": { "contractId": 2663, "rfc": "ITB190308MW8", "razonSocial": "ITBAF" },
  "resumen": { "registros": 81, "faltantes": 0, "bytes": 44210233 },
  "contenido": {
    "documentos": [
      {
        "tipoId": 16,
        "tipo": "Comunicado",
        "totalVersiones": 69,
        "versiones": [
          {
            "id": 19776,
            "nombre": "comunicado-ce2404171123300009904.zip",
            "existeEnDisco": true,
            "bytes": 51233,
            "mimeType": "application/zip",
            "urlDescarga": "http://<host>/api/v1/descargar.php?tipo=documento&id=19776",
            "fechaVencimiento": null,
            "version": 1,
            "esUltima": false
          }
        ]
      }
    ],
    "archivos": [],
    "requerimientos": []
  }
}
```

`version` va de 1 (la mas antigua) a N (la vigente, marcada con `esUltima`).

### `GET /api/v1/descargar.php?tipo=documento&id=19776`

Devuelve el binario. `tipo` es lista blanca (`documento`, `archivo`,
`requerimiento`); `id` es entero. Se sirve siempre como
`application/octet-stream` con `Content-Disposition: attachment`, de modo que un
HTML o SVG almacenado no se ejecute en el dominio del sistema.

Si el registro existe pero el archivo se perdio, responde `410 file_missing`.

---

## Codigos de error

| HTTP | `error` | Cuando |
|---|---|---|
| 400 | `bad_request` | Falta un parametro o tiene formato invalido |
| 401 | `unauthorized` | Sin token, mal formado o desconocido |
| 401 | `token_expired` | Vencieron las 8 horas — pedir uno nuevo |
| 401 | `token_revoked` | Revocado antes de vencer |
| 401 | `invalid_client` | client_id o client_secret incorrectos |
| 403 | `client_inactive` | La credencial fue dada de baja |
| 404 | `not_found` | La empresa o el registro no existe |
| 405 | `method_not_allowed` | Metodo HTTP equivocado |
| 410 | `file_missing` | Registro en BD sin archivo en disco |
| 429 | `too_many_attempts` | Limite de intentos por IP |

---

## Decisiones de seguridad

**El token no se emite por "existe la empresa".** Un RFC es publico y hay 3,681
empresas: cualquiera podria enumerarlas y obtener token. La empresa es un
*parametro* de la peticion; la credencial es lo que autentica.

**Credencial de integracion, no cuenta de usuario.** `user.passwd` es MD5 sin sal
y el login de `classes/user.class.php:104` concatena SQL. Reusar esa contraseña
habria puesto el acceso al panel completo en el `.env` del integrador. `api_client`
usa `password_hash()` y se revoca sin afectar a ningun humano.

**El cliente nunca manda rutas.** `api_resource_path()` arma la ruta desde la BD,
aplica `basename()` y verifica con `realpath()` que el resultado quede dentro de
la carpeta esperada.

**Tokens revocables.** Se guarda `sha256` del token, no el token. `api_token`
permite cortar el acceso al instante en lugar de esperar 8 horas.

**Auditoria.** `api_log` registra autenticaciones, manifiestos y descargas con
cliente, empresa, recurso, bytes e IP.

---

## Pendientes que quedan fuera de este cambio

1. **La API corre sobre HTTP.** `config.php:2` arma `WEB_ROOT` como `http://`, o
   sea que el sitio no tiene TLS. Un Bearer token en claro se puede interceptar.
   Hay un interruptor listo: `API_REQUIRE_HTTPS` en `api/v1/bootstrap.php`, hoy
   en `false` porque activarlo sin certificado deja la API inservible. **Ponerlo
   en `true` en cuanto haya HTTPS.**

2. **`download.php` de la raiz sigue siendo un LFI abierto.** No valida sesion ni
   sanitiza `$_GET['file']`; `download.php?file=config.php` entrega las
   credenciales SMTP y de BD. La API nueva no lo usa, pero el agujero sigue ahi.

3. **`util/download.php`** tiene el mismo problema.

4. **Limpieza de tokens vencidos.** Conviene un cron:
   `DELETE FROM api_token WHERE expiresAt < DATE_SUB(NOW(), INTERVAL 30 DAY)`.
