<?php require APP_PATH . '/Views/customer/partials/header.php'; $c = $customer; ?>
<form method="post" action="<?= customer_url('?route=profile') ?>"><?= csrf_field() ?>
<div class="row g-3"><div class="col-md-6"><label>Ad</label><input class="form-control" name="first_name" value="<?= e($c['first_name']) ?>"></div>
<div class="col-md-6"><label>Soyad</label><input class="form-control" name="last_name" value="<?= e($c['last_name']) ?>"></div>
<div class="col-md-6"><label>Telefon</label><input class="form-control" name="phone" value="<?= e($c['phone'] ?? '') ?>"></div>
<div class="col-12"><div class="form-check"><input type="checkbox" name="sms_permission" value="1" <?= $c['sms_permission']?'checked':'' ?>><label>SMS izni</label></div>
<div class="form-check"><input type="checkbox" name="whatsapp_permission" value="1" <?= $c['whatsapp_permission']?'checked':'' ?>><label>WhatsApp izni</label></div>
<div class="form-check"><input type="checkbox" name="marketing_permission" value="1" <?= $c['marketing_permission']?'checked':'' ?>><label>Pazarlama izni</label></div></div>
<button class="btn btn-primary">Kaydet</button></div></form>
<hr>
<form method="post" action="<?= customer_url('?route=profile/password') ?>"><?= csrf_field() ?>
<h6>Şifre Değiştir</h6>
<input type="password" name="current_password" class="form-control mb-2" placeholder="Mevcut şifre" required>
<input type="password" name="password" class="form-control mb-2" placeholder="Yeni şifre" required>
<button class="btn btn-outline-primary">Şifreyi Güncelle</button></form>
<?php require APP_PATH . '/Views/customer/partials/footer.php'; ?>
