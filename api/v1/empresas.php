<?php
/**
 * GET /api/v1/empresas.php
 *
 * Localiza empresas (tabla contract) para obtener su contract_id, que es lo
 * que consumen /manifiesto.php y /descargar.php.
 *
 * Parametros (todos opcionales, se combinan):
 *   contract_id  Entero. Devuelve esa empresa.
 *   rfc          RFC exacto.
 *   q            Texto libre sobre razon social / nombre comercial / RFC.
 *   activo       "Si" | "No". Filtra por contract.activo.
 *   page         Pagina, base 1. Default 1.
 *   per_page     1..200. Default 50.
 *
 * Requiere: Authorization: Bearer <token>
 */

require_once(dirname(__FILE__) . '/bootstrap.php');

api_require_method('GET');

$auth = api_require_token();
$db   = api_db();

$where = array();

if (isset($_GET['contract_id']) && $_GET['contract_id'] !== '') {
    $where[] = 'c.contractId = ' . (int)$_GET['contract_id'];
}

if (isset($_GET['rfc']) && $_GET['rfc'] !== '') {
    $where[] = "c.rfc = '" . api_escape(trim($_GET['rfc'])) . "'";
}

if (isset($_GET['q']) && trim($_GET['q']) !== '') {
    $q = api_escape(trim($_GET['q']));
    $where[] = "(c.name LIKE '%" . $q . "%'
              OR c.nombreComercial LIKE '%" . $q . "%'
              OR c.rfc LIKE '%" . $q . "%')";
}

if (isset($_GET['activo']) && in_array($_GET['activo'], array('Si', 'No'), true)) {
    $where[] = "c.activo = '" . $_GET['activo'] . "'";
}

$sqlWhere = $where ? ' WHERE ' . implode(' AND ', $where) : '';

$perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 50;
$perPage = max(1, min(200, $perPage));
$page    = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page    = max(1, $page);
$offset  = ($page - 1) * $perPage;

$db->setQuery('SELECT COUNT(*) FROM contract c' . $sqlWhere);
$total = (int)$db->GetSingle();

$db->setQuery('SELECT c.contractId, c.rfc, c.name, c.nombreComercial, c.activo,
                      (SELECT COUNT(*) FROM documento d     WHERE d.contractId = c.contractId) AS totalDocumentos,
                      (SELECT COUNT(*) FROM archivo a       WHERE a.contractId = c.contractId) AS totalArchivos,
                      (SELECT COUNT(*) FROM requerimiento r WHERE r.contractId = c.contractId) AS totalRequerimientos
                 FROM contract c'
             . $sqlWhere
             . ' ORDER BY c.name ASC
                 LIMIT ' . $perPage . ' OFFSET ' . $offset);

$rows      = $db->GetResult();
$empresas  = array();

foreach ($rows as $row) {
    $empresas[] = array(
        'contractId'      => (int)$row['contractId'],
        'rfc'             => $row['rfc'],
        'razonSocial'     => $row['name'],
        'nombreComercial' => $row['nombreComercial'],
        'activo'          => $row['activo'],
        'totales'         => array(
            'documentos'     => (int)$row['totalDocumentos'],
            'archivos'       => (int)$row['totalArchivos'],
            'requerimientos' => (int)$row['totalRequerimientos'],
        ),
    );
}

api_json(array(
    'paginacion' => array(
        'page'     => $page,
        'perPage'  => $perPage,
        'total'    => $total,
        'paginas'  => (int)ceil($total / $perPage),
    ),
    'empresas' => $empresas,
));
