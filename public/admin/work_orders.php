<?php require_once __DIR__.'/../../src/bootstrap.php'; require_role('admin');
if($_SERVER['REQUEST_METHOD']==='POST'){
  $st=db()->prepare('INSERT INTO work_orders(client_id,vehicle_id,technician_id,workstation_id,priority,status,admin_notes,created_at,estimated_minutes) VALUES(?,?,?,?,?,?,?,NOW(),?)');
  $st->execute([$_POST['client_id'],$_POST['vehicle_id'],$_POST['technician_id'],$_POST['workstation_id'],$_POST['priority'],$_POST['status'],$_POST['admin_notes'],(int)$_POST['estimated_minutes']]);
  $woid=db()->lastInsertId();
  foreach(($_POST['operation_template_id']??[]) as $op){ db()->prepare('INSERT INTO work_order_operations(work_order_id,operation_template_id,status) VALUES(?,? ,"pending")')->execute([$woid,$op]); }
  audit_log('create','work_order',$woid,$_POST); flash('ok','Lucrare creata'); header('Location:/admin/work_orders.php'); exit;
}
$rows=db()->query('SELECT wo.*,c.name client,v.plate_no,u.full_name tech,w.name station FROM work_orders wo LEFT JOIN clients c ON c.id=wo.client_id LEFT JOIN vehicles v ON v.id=wo.vehicle_id LEFT JOIN users u ON u.id=wo.technician_id LEFT JOIN workstations w ON w.id=wo.workstation_id ORDER BY wo.id DESC')->fetchAll(PDO::FETCH_ASSOC);
$clients=db()->query('SELECT id,name FROM clients')->fetchAll(PDO::FETCH_ASSOC); $vehicles=db()->query('SELECT id,plate_no FROM vehicles')->fetchAll(PDO::FETCH_ASSOC); $techs=db()->query("SELECT id,full_name FROM users WHERE role='technician'")->fetchAll(PDO::FETCH_ASSOC); $stations=db()->query('SELECT id,name FROM workstations WHERE is_active=1')->fetchAll(PDO::FETCH_ASSOC); $ops=db()->query('SELECT id,name FROM operation_templates WHERE is_active=1')->fetchAll(PDO::FETCH_ASSOC);
?><!doctype html><html><head><meta charset='utf-8'><meta name='viewport' content='width=device-width,initial-scale=1'><link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet'></head><body><div class='container-fluid p-4'><a href='/admin/dashboard.php'>Dashboard</a><h3>Lucrari</h3><?php if($m=flash('ok')):?><div class='alert alert-success'><?=e($m)?></div><?php endif; ?>
<form method='post' class='row g-2 mb-4'>
<div class='col-md-2'><select name='client_id' class='form-select' required><?php foreach($clients as $r) echo "<option value='{$r['id']}'>".e($r['name'])."</option>";?></select></div>
<div class='col-md-2'><select name='vehicle_id' class='form-select' required><?php foreach($vehicles as $r) echo "<option value='{$r['id']}'>".e($r['plate_no'])."</option>";?></select></div>
<div class='col-md-2'><select name='technician_id' class='form-select' required><?php foreach($techs as $r) echo "<option value='{$r['id']}'>".e($r['full_name'])."</option>";?></select></div>
<div class='col-md-2'><select name='workstation_id' class='form-select' required><?php foreach($stations as $r) echo "<option value='{$r['id']}'>".e($r['name'])."</option>";?></select></div>
<div class='col-md-1'><select name='priority' class='form-select'><option>normal</option><option>urgent</option></select></div><div class='col-md-1'><input name='estimated_minutes' type='number' class='form-control' placeholder='Min' required></div>
<div class='col-md-2'><select name='status' class='form-select'><option value='new'>new</option><option value='assigned'>assigned</option></select></div>
<div class='col-12'><textarea name='admin_notes' class='form-control' placeholder='Observatii'></textarea></div>
<div class='col-12'><?php foreach($ops as $o) echo "<label class='me-3'><input type='checkbox' name='operation_template_id[]' value='{$o['id']}'> ".e($o['name'])."</label>"; ?></div>
<div class='col-12'><button class='btn btn-primary'>Creeaza lucrare</button></div></form>
<table class='table table-striped'><tr><th>ID</th><th>Client</th><th>Masina</th><th>Tehnician</th><th>Status</th><th>Prioritate</th></tr><?php foreach($rows as $r):?><tr><td><?=$r['id']?></td><td><?=e($r['client'])?></td><td><?=e($r['plate_no'])?></td><td><?=e($r['tech'])?></td><td><?=e($r['status'])?></td><td><?=e($r['priority'])?></td></tr><?php endforeach;?></table>
</div></body></html>
