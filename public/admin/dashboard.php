<?php require_once __DIR__.'/../../src/bootstrap.php'; require_role('admin');
$kpi=[];
$kpi['total_today']=db()->query("SELECT COUNT(*) FROM work_orders WHERE DATE(created_at)=CURDATE()")->fetchColumn();
$kpi['in_progress']=db()->query("SELECT COUNT(*) FROM work_orders WHERE status='in_progress'")->fetchColumn();
$kpi['done_today']=db()->query("SELECT COUNT(*) FROM work_orders WHERE status='completed' AND DATE(completed_at)=CURDATE()")->fetchColumn();
$kpi['active_tech']=db()->query("SELECT COUNT(DISTINCT technician_id) FROM work_orders WHERE status='in_progress'")->fetchColumn();
?><!doctype html><html><head><meta charset='utf-8'><meta name='viewport' content='width=device-width,initial-scale=1'><link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet'></head><body>
<div class='container-fluid p-4'><a href='/logout.php' class='btn btn-outline-secondary float-end'>Logout</a><h3>Dashboard Admin</h3><div class='row g-3'><?php foreach($kpi as $k=>$v):?><div class='col-md-3'><div class='card'><div class='card-body'><div><?=e($k)?></div><h2><?=e($v)?></h2></div></div></div><?php endforeach;?></div>
<a class='btn btn-primary mt-3' href='/admin/work_orders.php'>Lucrari</a> <a class='btn btn-dark mt-3' href='/admin/reports.php'>Rapoarte</a></div></body></html>
