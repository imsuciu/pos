<?php require_once __DIR__.'/../../src/bootstrap.php'; require_role('technician');
$uid=current_user()['id'];
if($_SERVER['REQUEST_METHOD']==='POST'){
  $wo=(int)$_POST['work_order_id']; $action=$_POST['action'];
  if($action==='start'){ db()->prepare("UPDATE work_orders SET status='in_progress',started_at=IFNULL(started_at,NOW()) WHERE id=? AND technician_id=? AND status IN ('new','assigned','pause')")->execute([$wo,$uid]); db()->prepare('INSERT INTO work_sessions(work_order_id,technician_id,started_at) VALUES(?,?,NOW())')->execute([$wo,$uid]); }
  if($action==='pause'){ db()->prepare("UPDATE work_orders SET status='pause' WHERE id=? AND technician_id=?")->execute([$wo,$uid]); db()->prepare('UPDATE work_sessions SET paused_at=NOW() WHERE work_order_id=? AND technician_id=? AND ended_at IS NULL ORDER BY id DESC LIMIT 1')->execute([$wo,$uid]); }
  if($action==='resume'){ db()->prepare("UPDATE work_orders SET status='in_progress' WHERE id=? AND technician_id=?")->execute([$wo,$uid]); db()->prepare('INSERT INTO work_sessions(work_order_id,technician_id,resumed_at) VALUES(?,?,NOW())')->execute([$wo,$uid]); }
  if($action==='op_done'){ db()->prepare("UPDATE work_order_operations SET status='done',tech_note=? WHERE id=?")->execute([$_POST['tech_note']??'',$_POST['op_id']]); }
  if($action==='finish'){
    $pending=db()->prepare("SELECT COUNT(*) FROM work_order_operations WHERE work_order_id=? AND status<>'done'");$pending->execute([$wo]);$cnt=$pending->fetchColumn();
    if($cnt>0 && empty($_POST['finish_reason'])) flash('err','Finalizare blocata: completeaza operatiunile sau adauga motiv');
    else { db()->prepare("UPDATE work_orders SET status='completed',completed_at=NOW(),finish_reason=? WHERE id=? AND technician_id=?")->execute([$_POST['finish_reason']??null,$wo,$uid]); db()->prepare('UPDATE work_sessions SET ended_at=NOW() WHERE work_order_id=? AND technician_id=? AND ended_at IS NULL')->execute([$wo,$uid]); }
  }
  header('Location:/technician/tasks.php'); exit;
}
$st=db()->prepare('SELECT wo.*,c.name client,v.plate_no,v.vin,CONCAT(v.make," ",v.model," ",v.year) car,w.name station FROM work_orders wo JOIN clients c ON c.id=wo.client_id JOIN vehicles v ON v.id=wo.vehicle_id LEFT JOIN workstations w ON w.id=wo.workstation_id WHERE wo.technician_id=? AND wo.status<>"cancelled" ORDER BY FIELD(wo.status,"in_progress","assigned","new","pause","completed"), wo.id DESC');$st->execute([$uid]); $rows=$st->fetchAll(PDO::FETCH_ASSOC);
?><!doctype html><html><head><meta name='viewport' content='width=device-width,initial-scale=1'><link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet'></head><body class='bg-light'><div class='container py-3'><a href='/logout.php' class='btn btn-outline-secondary float-end'>Logout</a><h3>Task Board</h3><?php if($e=flash('err')):?><div class='alert alert-danger'><?=e($e)?></div><?php endif; ?>
<?php foreach($rows as $r): $ops=db()->prepare('SELECT woo.*,ot.name FROM work_order_operations woo JOIN operation_templates ot ON ot.id=woo.operation_template_id WHERE woo.work_order_id=?'); $ops->execute([$r['id']]); $secs=total_worked_seconds($r['id']); ?>
<div class='card my-3'><div class='card-body'><h5>#<?=$r['id']?> <?=e($r['client'])?> - <?=e($r['plate_no'])?> <span class='badge bg-primary'><?=e($r['status'])?></span></h5>
<div>VIN: <?=e($r['vin'])?> | <?=e($r['car'])?> | Post: <?=e($r['station'])?> | Timer: <?=gmdate('H:i:s',$secs)?></div>
<form method='post' class='my-2 d-flex gap-2'><input type='hidden' name='work_order_id' value='<?=$r['id']?>'>
<button name='action' value='start' class='btn btn-success btn-lg'>Incepe sarcina</button><button name='action' value='pause' class='btn btn-warning'>Pauza</button><button name='action' value='resume' class='btn btn-info'>Reia sarcina</button></form>
<ul class='list-group'><?php foreach($ops as $o):?><li class='list-group-item'><form method='post' class='d-flex gap-2 align-items-center'><input type='hidden' name='work_order_id' value='<?=$r['id'] ?>'><input type='hidden' name='op_id' value='<?=$o['id']?>'><input class='form-check-input' type='checkbox' <?= $o['status']==='done'?'checked':'' ?> disabled> <?=e($o['name'])?><input name='tech_note' class='form-control' placeholder='Observatie operatie'><button name='action' value='op_done' class='btn btn-sm btn-outline-primary'>Bifeaza</button></form></li><?php endforeach;?></ul>
<form method='post' class='mt-2'><input type='hidden' name='work_order_id' value='<?=$r['id']?>'><input name='finish_reason' class='form-control mb-2' placeholder='Motiv finalizare partiala (optional)'><button name='action' value='finish' class='btn btn-danger btn-lg'>Termina sarcina</button></form>
</div></div><?php endforeach;?></div></body></html>
