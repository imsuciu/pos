<?php require_once __DIR__ . '/../src/bootstrap.php';
if (current_user()) { header('Location: '.(current_user()['role']==='admin'?'/admin/dashboard.php':'/technician/tasks.php')); exit; }
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  if(login($_POST['username']??'',$_POST['password']??'')){
    header('Location: '.(current_user()['role']==='admin'?'/admin/dashboard.php':'/technician/tasks.php')); exit;
  }
  $error='Date de autentificare invalide';
}
?><!doctype html><html><head><meta charset='utf-8'><meta name='viewport' content='width=device-width,initial-scale=1'>
<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet'><title>Login</title></head>
<body class='bg-light'><div class='container py-5'><div class='row justify-content-center'><div class='col-md-4'><div class='card shadow'>
<div class='card-body'><h4 class='mb-3'>Auto Service Login</h4><?php if($error):?><div class='alert alert-danger'><?=e($error)?></div><?php endif; ?>
<form method='post'><input class='form-control mb-2' name='username' placeholder='Username' required>
<input class='form-control mb-3' type='password' name='password' placeholder='Password' required>
<button class='btn btn-primary w-100 btn-lg'>Login</button></form></div></div></div></div></div></body></html>
