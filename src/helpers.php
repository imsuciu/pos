<?php
function e($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function flash($key,$val=null){ if($val!==null){$_SESSION['flash'][$key]=$val;return;} $v=$_SESSION['flash'][$key]??null; unset($_SESSION['flash'][$key]); return $v; }
function audit_log($action,$entity,$entityId,$payload=[]){
  $uid = current_user()['id'] ?? null;
  $st=db()->prepare('INSERT INTO audit_logs(user_id,action,entity,entity_id,payload,created_at) VALUES(?,?,?,?,?,NOW())');
  $st->execute([$uid,$action,$entity,$entityId,json_encode($payload,JSON_UNESCAPED_UNICODE)]);
}
function total_worked_seconds($workOrderId){
  $st=db()->prepare('SELECT started_at,paused_at,resumed_at,ended_at FROM work_sessions WHERE work_order_id=? ORDER BY id');
  $st->execute([$workOrderId]); $secs=0; $now=time();
  foreach($st->fetchAll(PDO::FETCH_ASSOC) as $r){
    $start = $r['resumed_at'] ?: $r['started_at']; if(!$start) continue;
    $end = $r['ended_at'] ?: $r['paused_at']; if(!$end) $end=date('Y-m-d H:i:s',$now);
    $secs += max(0,strtotime($end)-strtotime($start));
  }
  return $secs;
}
