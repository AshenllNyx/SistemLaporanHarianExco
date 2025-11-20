<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<title>Login</title>
	<style>
		:root{--card-bg:#ffffff;--muted:#6b7280;--accent:#2563eb}
		*{box-sizing:border-box}
		html,body{height:100%}
		body{margin:0;font-family:Inter, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial;color:#0f172a;background:linear-gradient(135deg,#eef2ff 0%, #eef2ff 100%);display:flex;align-items:center;justify-content:center;padding:24px}
		.wrap{width:100%;max-width:960px;display:grid;grid-template-columns:1fr 420px;gap:32px;align-items:center}
		.promo{display:flex;flex-direction:column;gap:18px;padding:32px}
		.promo h2{margin:0;font-size:28px;color:#0f172a}
		.promo p{margin:0;color:var(--muted);font-size:15px}
		.card{background:var(--card-bg);border-radius:14px;padding:28px;box-shadow:0 10px 30px rgba(2,6,23,0.08);border:1px solid rgba(15,23,42,0.04)}
		.brand{display:flex;gap:12px;align-items:center;margin-bottom:14px}
		.logo{width:48px;height:48px;border-radius:10px;background:linear-gradient(135deg,var(--accent),#4f46e5);display:flex;align-items:center;justify-content:center;color:white;font-weight:700}
		h1{margin:0;font-size:18px;color:#0f172a}
		.desc{color:var(--muted);font-size:13px;margin-top:4px}
		.field{margin-bottom:14px}
		label{display:block;font-size:13px;color:#0f172a;margin-bottom:8px;font-weight:600}
		input[type="text"],input[type="password"]{width:100%;padding:12px 14px;border-radius:10px;border:1px solid #e6e9ef;background:#fbfdff;font-size:14px}
		input:focus{outline:none;box-shadow:0 6px 18px rgba(37,99,235,0.12);border-color:var(--accent)}
		.row{display:flex;align-items:center;justify-content:space-between;gap:12px}
		.remember{display:flex;align-items:center;gap:8px;color:var(--muted);font-size:14px}
		.btn{background:linear-gradient(90deg,var(--accent),#4f46e5);color:white;padding:10px 16px;border-radius:10px;border:none;font-weight:600;cursor:pointer}
		.btn:active{transform:translateY(1px)}
		.error{background:#fff1f2;color:#7f1d1d;padding:10px;border-radius:8px;margin-bottom:12px;border:1px solid rgba(185,28,28,0.08)}
		.help{margin-top:12px;display:flex;justify-content:space-between;font-size:13px;color:var(--muted)}
		@media (max-width:900px){.wrap{grid-template-columns:1fr;max-width:420px}.promo{display:none}}
	</style>
</head>
<body>
	<div class="wrap">
		<div class="promo">
			<h2>Selamat Datang ke Sistem Laporan Harian Exco</h2>
			<p>Log masuk untuk mengurus laporan harian, melihat ringkasan, dan mengakses papan pemuka.</p>
		</div>

		<section class="card" aria-labelledby="login-title">
			<div class="brand">
				<div class="logo">Ex</div>
				<div>
					<h1 id="login-title">Sign in to your account</h1>
					<div class="desc">Enter your username and password to continue.</div>
				</div>
			</div>

			@if($errors->any())
				<div class="error">{{ $errors->first() }}</div>
			@endif

			<form method="POST" action="{{ route('login.attempt') }}">
				@csrf

				<div class="field">
					<label for="user_name">Username</label>
					<input id="user_name" type="text" name="user_name" value="{{ old('user_name') }}" required autofocus />
					@error('user_name') <div class="error">{{ $message }}</div> @enderror
				</div>

				<div class="field">
					<label for="password">Password</label>
					<input id="password" type="password" name="password" required />
					@error('password') <div class="error">{{ $message }}</div> @enderror
				</div>

				<div class="row">
					<label class="remember"><input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember me</label>
					<a href="/" class="help">Back to site</a>
				</div>

				<div style="display:flex;justify-content:flex-end;margin-top:16px">
					<button type="submit" class="btn">Sign in</button>
				</div>

				<div class="help">
					<span>Need an account? <a href="{{route('register')}}">Register</a></span>
					<a href="#">Forgot password?</a>
				</div>
			</form>
		</section>
	</div>
</body>
</html>
