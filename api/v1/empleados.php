<?php
/**
 * GET /api/v1/empleados.php
 *
 * Localiza empleados (tabla personal) para obtener su personal_id, que es lo
 * que consume /expedientes.php. Tambien sirve para resolver un nombre a un
 * unico empleado antes de pedir su expediente.
 *
 * Parametros (todos opcionales, se combinan):
 *   personal_id      Entero. Devuelve ese empleado.
 *   q                Texto libre sobre nombre / puesto / email.
 *   nombre           Coincidencia parcial solo sobre el nombre.
 *   departamento_id  Entero. Filtra por departamento.
 *   activo           "1" | "0". Filtra por personal.active.
 *   con_archivos     "1" para listar solo empleados que ya tienen al menos
 *                    un expediente cargado.
 *   page             Pagina, base 1. Default 1.
 *   per_page         1..200. Default 50.
 *
 * Requiere: Authorization: Bearer <token>
 */

require_once(dirname(__FILE__) . '/bootstrap.php');

api_require_method('GET');

$auth = api_require_token();
$db   = api_db();

// Conteos de expedientes del empleado. Solo cuentan los tipos vigentes del
// catalogo: los dados de baja siguen en personalExpedientes pero ya no se
// piden, y contarlos daria un "faltan documentos" falso.
$subAsignados = "(SELECT COUNT(*)
                    FROM personalExpedientes pe
                    INNER JOIN expedientes e ON e.expedienteId = pe.expedienteId
                   WHERE pe.personalId = p.personalId
                     AND e.status = 'activo')";

$subConArchivo = "(SELECT COUNT(*)
                     FROM personalExpedientes pe
                     INNER JOIN expedientes e ON e.expedienteId = pe.expedienteId
                    WHERE pe.personalId = p.personalId
                      AND e.status = 'activo'
                      AND pe.path IS NOT NULL
                      AND pe.path <> '')";

$where = array();

if (isset($_GET['personal_id']) && $_GET['personal_id'] !== '') {
    $where[] = 'p.personalId = ' . (int)$_GET['personal_id'];
}

if (isset($_GET['q']) && trim($_GET['q']) !== '') {
    $q = api_escape(trim($_GET['q']));
    $where[] = "(p.name LIKE '%" . $q . "%'
              OR p.puesto LIKE '%" . $q . "%'
              OR p.email LIKE '%" . $q . "%')";
}

if (isset($_GET['nombre']) && trim($_GET['nombre']) !== '') {
    $where[] = "p.name LIKE '%" . api_escape(trim($_GET['nombre'])) . "%'";
}

if (isset($_GET['departamento_id']) && $_GET['departamento_id'] !== '') {
    $where[] = 'p.departamentoId = ' . (int)$_GET['departamento_id'];
}

if (isset($_GET['activo']) && in_array($_GET['activo'], array('0', '1'), true)) {
    $where[] = "p.active = '" . $_GET['activo'] . "'";
}

if (isset($_GET['con_archivos']) && $_GET['con_archivos'] === '1') {
    $where[] = $subConArchivo . ' > 0';
}

$sqlFrom = ' FROM personal p
             LEFT JOIN departamentos d ON d.departamentoId = p.departamentoId';

$sqlWhere = $where ? ' WHERE ' . implode(' AND ', $where) : '';

$perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 50;
$perPage = max(1, min(200, $perPage));
$page    = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page    = max(1, $page);
$offset  = ($page - 1) * $perPage;

$db->setQuery('SELECT COUNT(*)' . $sqlFrom . $sqlWhere);
$total = (int)$db->GetSingle();

$db->setQuery('SELECT p.personalId, p.name, p.puesto, p.email, p.active,
                      p.tipoPersonal, p.fechaIngreso, p.departamentoId, d.departamento,
                      ' . $subAsignados . ' AS totalAsignados,
                      ' . $subConArchivo . ' AS totalConArchivo'
             . $sqlFrom
             . $sqlWhere
             . ' ORDER BY p.name ASC
                 LIMIT ' . $perPage . ' OFFSET ' . $offset);

$rows      = $db->GetResult();
$empleados = array();

foreach ($rows as $row) {
    $empleado = api_empleado_publico($row);

    $asignados   = (int)$row['totalAsignados'];
    $conArchivo  = (int)$row['totalConArchivo'];

    $empleado['expedientes'] = array(
        'asignados'   => $asignados,
        'conArchivo'  => $conArchivo,
        'pendientes'  => $asignados - $conArchivo,
    );

    $empleados[] = $empleado;
}

api_json(array(
    'paginacion' => array(
        'page'    => $page,
        'perPage' => $perPage,
        'total'   => $total,
        'paginas' => (int)ceil($total / $perPage),
    ),
    'empleados' => $empleados,
));
