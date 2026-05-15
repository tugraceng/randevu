<!DOCTYPE html>
<html lang="tr"><head><meta charset="UTF-8"><title>Kayıt</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="d-flex align-items-center justify-content-center min-vh-100 bg-light">
<div class="card p-4 shadow" style="width:420px;border-radius:16px">
<h4 class="text-center fw-bold">Kayıt Ol</h4>
<form method="post" action="<?= customer_url('?route=register') ?>"><?= csrf_field() ?>
<div class="row g-2"><div class="col-6"><input class="form-control" name="first_name" placeholder="Ad" required></div>
<div class="col-6"><input class="form-control" name="last_name" placeholder="Soyad" required></div></div>
<input class="form-control mt-2" name="email" type="email" placeholder="E-posta" required>
<input class="form-control mt-2" name="phone" placeholder="Telefon">
<input class="form-control mt-2" name="password" type="password" placeholder="Şifre" required>
<div class="form-check mt-2"><input class="form-check-input" type="checkbox" name="sms_permission" value="1"><label class="form-check-label small">SMS bildirimleri</label></div>
<div class="form-check"><input class="form-check-input" type="checkbox" name="whatsapp_permission" value="1"><label class="form-check-label small">WhatsApp bildirimleri</label></div>
<button class="btn btn-primary w-100 mt-3">Kayıt Ol</button>
</form>
<p class="text-center mt-3 small"><a href="<?= customer_url('?route=login') ?>">Giriş yap</a></p>
</div></body></html>
