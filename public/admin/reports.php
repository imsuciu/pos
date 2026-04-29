<?php require_once __DIR__.'/../../src/bootstrap.php'; require_role('admin');
$where='1=1'; $p=[];
if(!empty($_GET['technician_id'])){$where.=' AND wo.technician_id=?';$p[]=$_GET['technician_id'];}
if(!empty($_GET['status'])){$where.=' AND wo.status=?';$p[]=$_GET['status'];}
if(!empty($_GET['date_from'])){$where.=' AND DATE(wo.created_at)>=?';$p[]=$_GET['date_from'];}
if(!empty($_GET['date_to'])){$where.=' AND DATE(wo.created_at)<=?';$p[]=$_GET['date_to'];}
$sql="SELECT wo.id,u.full_name tech,wo.status,wo.estimated_minutes,wo.created_at,wo.completed_at,c.name client,v.vin FROM work_orders wo LEFT JOIN users u ON u.id=wo.technician_id LEFT JOIN clients c ON c.id=wo.client_id LEFT JOIN vehicles v ON v.id=wo.vehicle_id WHERE $where ORDER BY wo.id DESC";
$st=db()->prepare($sql);$st->execute($p);$rows=$st->fetchAll(PDO::FETCH_ASSOC);
if(isset($_GET['export']) && $_GET['export']==='csv'){ header('Content-Type:text/csv'); header('Content-Disposition: attachment; filename=report.csv'); $f=fopen('php://output','w'); fputcsv($f,array_keys($rows[0]??['id'=>''])); foreach($rows as $r) fputcsv($f,$r); exit; }
?><!doctype html><html><head><link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet'></head><body><div class='container p-4'><h3>Rapoarte</h3><form class='row g-2 mb-3'>
<div class='col'><input name='technician_id' class='form-control' placeholder='Technician ID'></div><div class='col'><input name='status' class='form-control' placeholder='status'></div><div class='col'><input type='date' name='date_from' class='form-control'></div><div class='col'><input type='date' name='date_to' class='form-control'></div>
<div class='col'><button class='btn btn-primary'>Filtreaza</button> <button name='export' value='csv' class='btn btn-success'>Export CSV</button></div></form>
<table class='table table-bordered'><tr><th>ID</th><th>Tech</th><th>Status</th><th>Estimat</th><th>Client</th><th>VIN</th></tr><?php foreach($rows as $r):?><tr><td><?=$r['id']?></td><td><?=e($r['tech'])?></td><td><?=e($r['status'])?></td><td><?=$r['estimated_minutes']?></td><td><?=e($r['client'])?></td><td><?=e($r['vin'])?></td></tr><?php endforeach;?></table></div></body></html>
