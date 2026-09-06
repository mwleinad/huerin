# API v1 — descarga de archivos por empresa y de expedientes del personal

Expone el inventario de **documentos, archivos y requerimientos** de una empresa
(tabla `contract`), agrupado por tipo y con las N versiones de cada uno, mas la
descarga individual de cada version.

Expone tambien el **expediente de cada empleado** (tabla `personalExpedientes`):
que documentos le corresponden, cuales ya tiene cargados y la descarga de cada
uno, consultables **por nombre** del empleado y del documento.

Todo endpoint salvo la emision del token exige `Authorization: Bearer <token>`.

---

## Instalacion

1. Aplicar las migraciones:

   ```
   mysql -uroot -h127.0.0.1 huerin < changes_db/update_20260722_api.sql
   mysql -uroot -h127.0.0.1 huerin < changes_db/update_20260722_api_urlfirmada.sql
   ```

   La primera crea `api_client`, `api_token` y `api_log`. La segunda crea
   `api_setting`, donde se guarda el secreto de las URLs firmadas (se genera
   solo la primera vez; no hay que editar `config.php`). Ninguna toca tablas
   existentes.

2. Generar la credencial del consumidor:

   ```
   php api/tools/clientes.php crear "Portal Contable"
   ```

   Imprime `client_id` y `client_secret`. **El secret se muestra una sola vez**;
   en la BD queda unicamente su `password_hash()`.

Los expedientes del personal no necesitan migracion: leen las tablas que ya usa
el modulo de personal (`personal`, `expedientes`, `personalExpedientes`) y los
archivos que ya estan en `/expedientes/<personalId>/`.

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

Cada `urlDescarga` viene **ya firmada** y lista para abrirse en el navegador
(ver abajo). No hay que armar la URL a mano ni adjuntar el token.

### `GET /api/v1/empleados.php`

Localiza el `personalId` que consume `expedientes.php`, y de paso dice cuantos
documentos lleva cargados cada empleado. Filtros combinables: `personal_id`,
`q` (texto libre sobre nombre / puesto / email), `nombre` (solo el nombre),
`departamento_id`, `activo` (`1`/`0`), `con_archivos=1` (solo quien ya tiene al
menos un expediente cargado), `page`, `per_page` (1..200, default 50).

```bash
curl -s "http://<host>/api/v1/empleados.php?nombre=Rogelio" \
  -H "Authorization: Bearer <token>"
```

```json
{
  "paginacion": { "page": 1, "perPage": 50, "total": 1, "paginas": 1 },
  "empleados": [
    {
      "personalId": 32,
      "nombre": "FINGER1 Rogelio Isaac Zetina Olazagasti",
      "puesto": "Coordinador",
      "email": "rzetina@braunhuerin.com.mx",
      "departamento": "Finanzas",
      "tipoPersonal": "Coordinador",
      "fechaIngreso": "2007-07-09",
      "activo": true,
      "expedientes": { "asignados": 14, "conArchivo": 13, "pendientes": 1 }
    }
  ]
}
```

Los conteos solo consideran tipos **vigentes** del catalogo: los dados de baja
siguen en `personalExpedientes` pero ya no se piden, y contarlos inventaria
faltantes que no existen. Cuentan lo registrado en la BD, sin tocar disco —para
saber si el archivo sigue ahi esta `existeEnDisco` en `expedientes.php`—, porque
comprobar cada archivo de cada empleado en un listado paginado seria lentisimo.

### `GET /api/v1/expedientes.php?personal_id=32`

Expediente de un empleado: los tipos de documento asignados, cuales tienen
archivo y la URL firmada de cada uno.

En vez de `personal_id` se puede pasar `empleado=<nombre o parte del nombre>`.
La busqueda es parcial porque el sistema guarda claves internas dentro del
nombre (`"CIGER3 Miguel Angel Brito Garcia"`); si coincide con mas de un
empleado responde `409 empleado_ambiguo` con los `candidatos` para reintentar
con `personal_id`.

Opcionales: `expediente=<nombre>` o `expediente_id=<id>` para un solo documento
(un nombre ambiguo da `409 expediente_ambiguo`), `solo_existentes=1` para omitir
los que aun no se cargan, `incluir_baja=1` para incluir tipos dados de baja del
catalogo que todavia conservan archivo, y `catalogo=1` para devolver unicamente
el catalogo de tipos de expediente, sin empleado.

```bash
curl -s "http://<host>/api/v1/expedientes.php?empleado=Rogelio%20Isaac&expediente=CURP" \
  -H "Authorization: Bearer <token>"
```

```json
{
  "empleado": { "personalId": 32, "nombre": "FINGER1 Rogelio Isaac Zetina Olazagasti" },
  "resumen": { "asignados": 1, "conArchivo": 1, "faltantes": 0, "bytes": 51233 },
  "expedientes": [
    {
      "expedienteId": 7,
      "nombre": "CURP",
      "status": "activo",
      "extensionEsperada": ".pdf",
      "tieneArchivo": true,
      "existeEnDisco": true,
      "nombreArchivo": "CURP - FINGER1 Rogelio Isaac Zetina Olazagasti.pdf",
      "bytes": 51233,
      "mimeType": "application/pdf",
      "fecha": "2019-10-02",
      "urlDescarga": "http://<host>/api/v1/descargar.php?tipo=expediente&id=32-7&exp=...&firma=..."
    }
  ]
}
```

`tieneArchivo` dice que la BD tiene un archivo registrado; `existeEnDisco` dice
que ademas sigue ahi. Cuando no existe, `urlDescarga` viene en `null`.

### `GET /api/v1/descargar.php?tipo=documento&id=19776`

Devuelve el binario. `tipo` es lista blanca (`documento`, `archivo`,
`requerimiento`, `expediente`); `id` es entero. Se sirve siempre como
`application/octet-stream` con `Content-Disposition: attachment`, de modo que un
HTML o SVG almacenado no se ejecute en el dominio del sistema.

Acepta **dos vias de autenticacion**:

1. **URL firmada** — la que emite `manifiesto.php` en cada `urlDescarga`:
   `descargar.php?tipo=..&id=..&exp=<timestamp>&firma=<hmac>`. La firma es un
   HMAC-SHA256 sobre `tipo|id|exp` con un secreto que solo vive en el servidor.
   El navegador la abre directo, **sin cabecera Authorization**. Vence a los
   **15 minutos** (`API_SIGN_TTL`), asi que se usa desde el manifiesto recien
   pedido. Si se altera cualquier campo o vence, responde `403 invalid_signature`.
2. **`Authorization: Bearer <token>`** — para consumidores API que ya manejan el
   token y prefieren no depender de la URL firmada.

Si llega `firma`, se valida esa via; si no, se exige Bearer. Si el registro
existe pero el archivo se perdio, responde `410 file_missing`.

```bash
# via firma: copiar la urlDescarga del manifiesto y abrirla, sin headers
curl -sL "http://<host>/api/v1/descargar.php?tipo=documento&id=19776&exp=1784773576&firma=c919b2..." -o documento.pdf
```

#### `tipo=expediente`: id compuesto y descarga por nombre

`personalExpedientes` tiene llave compuesta, asi que el id del expediente es
`"<personalId>-<expedienteId>"` (`id=32-7`). Va completo dentro de la firma: no
se puede cambiar el empleado ni el documento sin invalidarla.

Con `Authorization: Bearer` se puede **descargar por nombre**, sin pasar antes
por `expedientes.php`:

```bash
curl -sL "http://<host>/api/v1/descargar.php?tipo=expediente&empleado=Rogelio%20Isaac&expediente=CURP" \
  -H "Authorization: Bearer <token>" -o curp.pdf
```

`personal_id` y `expediente_id` sirven igual que sus versiones por nombre, y se
pueden mezclar. Un nombre ambiguo responde `409` con los candidatos, igual que
en `expedientes.php`. Esta via **no funciona con URL firmada** a proposito: la
firma cubre el id ya resuelto, y resolver nombres antes de autenticar
convertiria el endpoint en un buscador de personal abierto.

El archivo se entrega con un nombre legible —`"CURP - Nombre Del Empleado.pdf"`—
en vez del `employe_file327.pdf` con el que se guarda en disco.

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
| 404 | `not_found` | La empresa, el empleado o el registro no existe |
| 405 | `method_not_allowed` | Metodo HTTP equivocado |
| 403 | `invalid_signature` | URL de descarga firmada invalida o vencida |
| 409 | `rfc_ambiguo` | El RFC corresponde a varias empresas — reintentar con `contract_id` |
| 409 | `empleado_ambiguo` | El nombre coincide con varios empleados — reintentar con `personal_id` |
| 409 | `expediente_ambiguo` | El nombre coincide con varios tipos — reintentar con `expediente_id` |
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
la carpeta esperada. `api_expediente_path()` hace lo mismo para
`/expedientes/<personalId>/`.

**Los expedientes son datos personales.** Actas de nacimiento, CURP, contratos y
domicilios del personal quedan expuestos a quien tenga la credencial, asi que
conviene emitir una credencial aparte para el consumidor que los necesite y
revocarla cuando termine la integracion. La API no devuelve sueldo, contraseñas
ni ningun otro campo de `personal` fuera de la identificacion basica. Cada
consulta y cada descarga queda en `api_log` con `resourceType = 'expediente'`,
el `expedienteId` en `resourceId` y el `personalId` en `detail`.

**Tokens revocables.** Se guarda `sha256` del token, no el token. `api_token`
permite cortar el acceso al instante en lugar de esperar 8 horas.

**Auditoria.** `api_log` registra autenticaciones, manifiestos y descargas con
cliente, empresa, recurso, bytes e IP. En las descargas, `detail` distingue si
fue `via token` o `via firma`.

**URLs firmadas de vida corta.** El manifiesto entrega cada descarga como una
URL con HMAC y `exp` a 15 minutos, de modo que el navegador la abre sin exponer
el token en la barra de direcciones ni en el historial. El secreto vive en
`api_setting` (nunca sale del servidor). Para rotarlo — lo que invalida al
instante toda URL ya emitida — basta con borrar el renglon:
`DELETE FROM api_setting WHERE name = 'url_secret';` (se regenera solo).

---

## Legacy en HTTP detras de un consumidor HTTPS (Mixed Content)

Cuando este servidor se sirve por HTTP y quien consume la API corre sobre
HTTPS, el navegador bloquea la descarga: *"Mixed Content: the file ... was
loaded over an insecure connection"*. No es CORS y no se puede desactivar
desde el servidor — lo impone el navegador.

La solucion no requiere HTTPS aqui ni abrir el puerto 443: se hace que la
descarga pase por el mismo proxy HTTPS que ya consume el manifiesto.

```
navegador → https://api.braunhuerin.com/legacy/descargar.php?...   (HTTPS)
                     ↓  proxy inverso, servidor a servidor
            http://braunhuerin.dynalias.net/api/v1/descargar.php?... (HTTP)
```

**En este servidor**, agregar a `config.php`:

```php
define('API_PUBLIC_DOWNLOAD_BASE', 'https://api.braunhuerin.com/legacy');
```

Con eso `manifiesto.php` emite cada `urlDescarga` apuntando al proxy. La
firma HMAC cubre solo `tipo|id|exp` —no el host—, asi que sigue validando
cuando la peticion llega aqui a traves del proxy. Si la constante no se
define, todo sigue igual que antes (URLs a este mismo host).

**En el proxy** (nginx), mapear la ruta:

```nginx
location /legacy/ {
    proxy_pass http://braunhuerin.dynalias.net/api/v1/;
    proxy_set_header Host braunhuerin.dynalias.net;
    proxy_buffering off;          # descargas grandes sin llenar el buffer
}
```

`API_REQUIRE_HTTPS` debe permanecer en `false`: la peticion proxeada llega
a este servidor por HTTP.

---

## Pendientes que quedan fuera de este cambio

1. **La API corre sobre HTTP.** `config.php:2` arma `WEB_ROOT` como `http://`, o
   sea que el sitio no tiene TLS. Un Bearer token en claro se puede interceptar.
   Hay un interruptor listo: `API_REQUIRE_HTTPS` en `api/v1/bootstrap.php`, hoy
   en `false` porque activarlo sin certificado deja la API inservible. **Ponerlo
   en `true` en cuanto haya HTTPS.**

2. **Limpieza de tokens vencidos.** Conviene un cron:
   `DELETE FROM api_token WHERE expiresAt < DATE_SUB(NOW(), INTERVAL 30 DAY)`.

---

## Resuelto aparte: LFI en los descargadores por ruta

`download.php` y `util/download.php` servian cualquier ruta sin validar sesion
(`download.php?file=config.php` entregaba las credenciales de BD y SMTP). Ambos
usan ahora `util/download-guard.php`: exigen sesion iniciada y solo sirven
archivos dentro de una lista blanca de carpetas. La API no depende de ellos
—`descargar.php` resuelve la ruta desde la BD—, pero el agujero ya esta cerrado.
