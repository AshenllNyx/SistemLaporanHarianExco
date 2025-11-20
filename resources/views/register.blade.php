<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<title>Register</title>
	<style>
		:root{--card-bg:#ffffff;--muted:#6b7280;--accent:#2563eb}
		*{box-sizing:border-box}
		html,body{height:100%}
		body{margin:0;font-family:Inter, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial;color:#0f172a;background:linear-gradient(135deg,#eef2ff 0%, #eef2ff 100%);display:flex;align-items:center;justify-content:center;padding:24px}
		/* Default: wide screens show promo + form side-by-side */
		.wrap{width:100%;max-width:960px;display:grid;grid-template-columns:1fr 420px;gap:32px;align-items:center}
		.promo{display:flex;flex-direction:column;gap:18px;padding:32px}
		.promo h2{margin:0;font-size:28px;color:#0f172a}
		.promo p{margin:0;color:var(--muted);font-size:15px}
		.card{background:var(--card-bg);border-radius:14px;padding:28px;box-shadow:0 10px 30px rgba(2,6,23,0.08);border:1px solid rgba(15,23,42,0.04);width:100%}
		.brand{display:flex;gap:12px;align-items:center;margin-bottom:14px}
		.logo{width:48px;height:48px;border-radius:10px;background:linear-gradient(135deg,var(--accent),#4f46e5);display:flex;align-items:center;justify-content:center;color:white;font-weight:700}
		h1{margin:0;font-size:18px;color:#0f172a}
		.desc{color:var(--muted);font-size:13px;margin-top:4px}
		.field{margin-bottom:14px}
		label{display:block;font-size:13px;color:#0f172a;margin-bottom:8px;font-weight:600}
		input[type="text"],input[type="email"],input[type="password"]{width:100%;padding:12px 14px;border-radius:10px;border:1px solid #e6e9ef;background:#fbfdff;font-size:14px}
		input:focus{outline:none;box-shadow:0 6px 18px rgba(37,99,235,0.12);border-color:var(--accent)}
		.row{display:flex;align-items:center;justify-content:space-between;gap:12px}
		.remember{display:flex;align-items:center;gap:8px;color:var(--muted);font-size:14px}
		.btn{background:linear-gradient(90deg,var(--accent),#4f46e5);color:white;padding:10px 16px;border-radius:10px;border:none;font-weight:600;cursor:pointer}
		.btn:active{transform:translateY(1px)}
		.error{background:#fff1f2;color:#7f1d1d;padding:10px;border-radius:8px;margin-bottom:12px;border:1px solid rgba(185,28,28,0.08)}
		.help{margin-top:12px;display:flex;justify-content:space-between;font-size:13px;color:var(--muted)}
		/* Small screens: center the card and hide the promo */
		@media (max-width:900px){
			.wrap{width:100%;max-width:420px;display:flex;flex-direction:column;gap:0;align-items:center;justify-content:center}
			.promo{display:none}
			.card{width:100%}	
		}
		select {
	width:100%;
	padding:12px 14px;
	border-radius:10px;
	border:1px solid #e6e9ef;
	background:#fbfdff;
	font-size:14px;
	color:#0f172a;
	cursor:pointer;
	appearance:none;         /* remove default browser arrow */
	-webkit-appearance:none; /* Safari */
	-moz-appearance:none;    /* Firefox */
	background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
	background-repeat:no-repeat;
	background-position:right 12px center;
	background-size:16px;
}

select:focus {
	outline:none;
	box-shadow:0 6px 18px rgba(37,99,235,0.12);
	border-color:var(--accent);
}
/* Styling untuk placeholder */
input::placeholder,
select::placeholder {
	color: #9ca3af;   /* warna kelabu lembut */
	font-size: 13px;
	opacity: 1;       /* pastikan warna tidak terlalu pudar */
}

	</style>
</head>
<body>
	<div class="wrap">
		<div class="promo">
			<h2>Anda adalah user baru?</h2>
			<p>Buat akaun untuk mula menghantar dan mengurus laporan harian.</p>
		</div>

		<section class="card" aria-labelledby="register-title">
			<div class="brand">
				<div class="logo">Ex</div>
				<div>
					<h1 id="register-title">Create an account</h1>
					<div class="desc">Fill the form below to create a new account.</div>
				</div>
			</div>

			@if($errors->any())
				<div class="error">{{ $errors->first() }}</div>
			@endif

			<form method="POST" action="{{ route('register.attempt') }}">
				@csrf

				<div class="field">
					<label for="no_ic">No. IC (Tanpa "-")</label>
					<input id="no_ic" type="text" name="no_ic" value="{{ old('no_ic') }}" required />
					@error('no_ic') <div class="error">{{ $message }}</div> @enderror
				</div>


				<div class="field">
					<label for="name">Full name</label>
					<input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus />
					@error('name') <div class="error">{{ $message }}</div> @enderror
				</div>

				<div class="field">
					<label for="user_name">Username</label>
					<input id="user_name" type="text" name="user_name" value="{{ old('user_name') }}" required />
					@error('user_name') <div class="error">{{ $message }}</div> @enderror
				</div>

				<div class="field">
					<label for="jantina">Jantina</label>
					<select id="jantina" name="jantina" required>
						<option value="" disabled selected>Select your gender</option>
						<option value="Lelaki" >Lelaki</option>
						<option value="Perempuan" >Perempuan</option>
					</select>	
					@error('jantina') <div class="error">{{ $message }}</div> @enderror
				</div>

				<div class="field">
					<label for="email">Email</label>
					<input id="email" type="email" name="email" value="{{ old('email') }}" required />
					@error('email') <div class="error">{{ $message }}</div> @enderror
				</div>

				<div class="field">
					<label for="password">Password</label>
					<input id="password" type="password" name="password" required required placeholder="Sekurang-kurangnya 8 aksara" />
					@error('password') <div class="error">{{ $message }}</div> @enderror
				</div>

				<div class="field">
					<label for="password_confirmation">Confirm password</label>
					<input id="password_confirmation" type="password" name="password_confirmation" required />
				</div>

				<div style="display:flex;justify-content:flex-end;margin-top:16px">
					<button type="submit" class="btn">Create account</button>
				</div>

				<div class="help">
					<span>Already have an account? <a href="{{ route('login') }}">Sign in</a></span>
				</div>
			</form>
		</section>
	</div>
</body>
</html>