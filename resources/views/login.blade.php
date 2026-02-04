<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<title>Login</title>
	<style>
		:root{--card-bg:#ffffff;--muted:#6b7280;--accent:#2563eb;--warden:#dc2626;--exco:#059669}
		*{box-sizing:border-box}
		html,body{height:100%}
		body{margin:0;font-family:Inter, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial;color:#0f172a;background:url("{{ asset('images/bg.jpeg') }}") center center / cover no-repeat fixed;display:flex;align-items:center;justify-content:center;padding:24px}
		.wrap{width:100%;max-width:380px;display:flex;align-items:center;flex-direction:column;margin-top:70px}
		.promo{display:none}
		.card{display:flex;align-items:center;flex-direction:column;background:var(--card-bg);border-radius:12px;padding:24px 20px;box-shadow:0 8px 24px rgba(2,6,23,0.1);border:1px solid rgba(255, 255, 255, 0.04);width:100%}
		.brand{display:flex;gap:10px;align-items:center;margin-bottom:12px}
		.logo{width:40px;height:40px;border-radius:8px;background:linear-gradient(135deg,var(--accent),#4f46e5);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:18px}
		h1{margin:0;font-size:16px;color:#0f172a}
		.desc{color:var(--muted);font-size:12px;margin-top:3px;text-align:center}
		.field{margin-bottom:12px;width:100%}
		label{display:block;font-size:12px;color:#0f172a;margin-bottom:6px;font-weight:600}
		input[type="text"],input[type="password"]{width:100%;padding:10px 12px;border-radius:8px;border:1px solid #e6e9ef;background:#fbfdff;font-size:13px}
		input:focus{outline:none;box-shadow:0 4px 12px rgba(37,99,235,0.1);border-color:var(--accent)}
		.btn{background:linear-gradient(90deg,var(--accent),#4f46e5);color:white;padding:10px 16px;border-radius:8px;border:none;font-weight:600;cursor:pointer;transition:all 0.3s;font-size:14px;width:100%}
		.btn:hover{transform:translateY(-1px);box-shadow:0 6px 16px rgba(37,99,235,0.3)}
		.btn:active{transform:translateY(0)}
		.user-type-btn{padding:14px;font-size:14px;margin:6px 0;display:flex;align-items:center;justify-content:center;gap:8px;position:relative;overflow:hidden}
		.user-type-btn::before{content:'';position:absolute;top:0;left:-100%;width:100%;height:100%;background:rgba(255,255,255,0.2);transition:left 0.3s}
		.user-type-btn:hover::before{left:100%}
		.user-type-btn svg{width:20px;height:20px}
		.btn-warden{background:linear-gradient(135deg,#dc2626,#b91c1c)}
		.btn-warden:hover{box-shadow:0 6px 16px rgba(220,38,38,0.4)}
		.btn-exco{background:linear-gradient(135deg,#059669,#047857)}
		.btn-exco:hover{box-shadow:0 6px 16px rgba(5,150,105,0.4)}
		.error{background:#fff1f2;color:#7f1d1d;padding:8px 10px;border-radius:6px;margin-bottom:10px;border:1px solid rgba(185,28,28,0.08);width:100%;font-size:12px}
		.help{margin-top:10px;display:flex;justify-content:space-between;font-size:12px;color:var(--muted);width:100%}
		.welcome-banner{position:fixed;top:0;left:0;right:0;background:linear-gradient(90deg, rgba(37,99,235,0.95), rgba(79,70,229,0.95));color:#fff;padding:12px 20px;z-index:10;box-shadow:0 4px 20px rgba(0,0,0,0.15);overflow:hidden}
		.welcome-track{display:flex;white-space:nowrap;animation:scroll-left 20s linear infinite}
		.welcome-banner span{margin:0 50px;font-size:18px;font-weight:700}
		@keyframes scroll-left{0%{transform:translateX(0)}100%{transform:translateX(-50%)}}
		.kvdsazi-logo{display:block;margin:0 auto 16px;max-width:140px;max-height:80px;width:auto;height:auto;object-fit:contain}
		
		/* Step transitions */
		.step{display:none;width:100%;animation:fadeIn 0.4s ease-in}
		.step.active{display:block}
		@keyframes fadeIn{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}
		
		.user-type-selection{display:flex;flex-direction:column;gap:10px;width:100%;margin-top:14px}
		.login-form-container{width:100%}
		
		.back-btn{background:transparent;color:var(--muted);border:1px solid #e5e7eb;padding:6px 12px;font-size:12px;margin-bottom:12px}
		.back-btn:hover{background:#f9fafb;transform:none;box-shadow:none}
		
		.selected-type{background:#f0f9ff;border:1px solid #bfdbfe;padding:10px;border-radius:6px;margin-bottom:12px;display:flex;align-items:center;justify-content:space-between}
		.selected-type-label{color:var(--accent);font-weight:600;font-size:13px}
		
		@media (max-width:900px){
            .wrap{max-width:340px}.welcome-banner span{font-size:16px}.welcome-track{animation-duration:15s}.kvdsazi-logo{max-width:120px;max-height:70px;margin-bottom:14px}
            .help{flex-direction:column;gap:8px;align-items:center}
        }
	</style>
</head>
<body>
	<div class="welcome-banner">
		<div class="welcome-track">
			<span>Selamat Datang ke Sistem Laporan Harian Exco Aspura (e-LHEAspura)</span>
			<span>Selamat Datang ke Sistem Laporan Harian Exco Aspura (e-LHEAspura)</span>
		</div>
	</div>

	<div class="wrap">
		<section class="card" aria-labelledby="login-title">
			<img class="kvdsazi-logo" src="{{ asset('images/Logo KVDSAZI (transparent) HD.png') }}" alt="Logo KVDSAZI">
			<div class="brand">
				<img class="logo" src="{{ asset("images/logo.jpeg") }}" alt="">
				<div>
					<h1 id="login-title">Log masuk ke akaun anda</h1>
					<div class="desc" id="step-desc">Pilih jenis akaun anda</div>
				</div>
			</div>

			@if($errors->any())
				<div class="error">{{ $errors->first() }}</div>
			@endif

			<!-- Step 1: User Type Selection -->
			<div id="step1" class="step active">
				<div class="user-type-selection">
					<button type="button" class="btn user-type-btn btn-warden" onclick="selectUserType('admin', 'Warden')">
						<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><polyline points="17 11 19 13 23 9"></polyline></svg>
						Warden
					</button>
					<button type="button" class="btn user-type-btn btn-exco" onclick="selectUserType('user', 'Exco')">
						<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
						Exco
					</button>
				</div>
			</div>

			<!-- Step 2: Login Form -->
			<div id="step2" class="step">
				<button type="button" class="btn back-btn" onclick="backToUserTypeSelection()">
					← Tukar Jenis Akaun
				</button>

				<div class="selected-type">
					<span class="selected-type-label">Log masuk sebagai: <strong id="selected-type-name"></strong></span>
				</div>

				<form method="POST" action="{{ route('login.attempt') }}" class="login-form-container">
					@csrf
					<input type="hidden" name="login_type" id="login_type_input" value="">

					<div class="field">
						<label for="user_name">Username</label>
						<input id="user_name" type="text" name="user_name" value="{{ old('user_name') }}" required />
						@error('user_name') <div class="error">{{ $message }}</div> @enderror
					</div>

					<div class="field">
						<label for="password">Kata Laluan</label>
						<input id="password" type="password" name="password" required />
						@error('password') <div class="error">{{ $message }}</div> @enderror
					</div>

					<button type="submit" class="btn" style="margin-top:16px">
						Sign In
					</button>

					<div class="help">
						<span>Perlukan Akaun Baru? <a href="{{route('register')}}">Daftar Pengguna</a></span>
						<a href="#">Lupa Kata Laluan?</a>
					</div>
				</form>
			</div>
		</section>
	</div>

	<script>
		function selectUserType(type, typeName) {
			// Hide step 1, show step 2
			document.getElementById('step1').classList.remove('active');
			document.getElementById('step2').classList.add('active');
			
			// Update description
			document.getElementById('step-desc').textContent = 'Masukkan nama pengguna dan kata laluan anda';
			
			// Set the login type
			document.getElementById('login_type_input').value = type;
			document.getElementById('selected-type-name').textContent = typeName;
			
			// Focus on username field
			setTimeout(() => {
				document.getElementById('user_name').focus();
			}, 100);
		}

		function backToUserTypeSelection() {
			// Show step 1, hide step 2
			document.getElementById('step2').classList.remove('active');
			document.getElementById('step1').classList.add('active');
			
			// Update description
			document.getElementById('step-desc').textContent = 'Pilih jenis akaun anda';
			
			// Clear form
			document.getElementById('user_name').value = '';
			document.getElementById('password').value = '';
		}
	</script>
</body>
</html>
