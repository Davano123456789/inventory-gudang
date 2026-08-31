<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ganti Password Wajib - Inventory Gudang</title>
    <!-- Outfit Font from Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
    </style>
</head>
<body class="m-0 font-sans antialiased bg-gradient-to-br from-indigo-100 via-white to-blue-50 min-h-screen flex items-center justify-center p-4">

    <!-- Login Container -->
    <div class="glass-card max-w-md w-full p-8 rounded-[2rem] border border-white/60 shadow-[0_20px_50px_rgba(15,23,42,0.08)] transition-all duration-300 relative overflow-hidden">
        
        <!-- Decorative bg blob -->
        <div class="absolute -top-12 -right-12 w-40 h-40 bg-blue-500/10 rounded-full blur-2xl pointer-events-none"></div>

        <!-- Header / Logo -->
        <div class="text-center mb-8 relative z-10">
            <div class="mx-auto w-14 h-14 bg-amber-100 rounded-full flex items-center justify-center mb-4">
                <i class="fa fa-shield-alt text-amber-500 text-2xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Keamanan Akun</h2>
            <p class="text-sm text-slate-500 mt-2 font-medium px-4">Ini adalah login pertama Anda. Silakan ganti password *default* Anda untuk melanjutkan.</p>
        </div>

        <!-- Validation Errors -->
        @if($errors->any())
            <div class="mb-5 p-4 text-xs text-rose-700 bg-rose-50/70 border border-rose-100 rounded-2xl flex flex-col gap-1.5 relative z-10">
                <div class="flex items-center gap-2 font-semibold">
                    <i class="fa fa-circle-exclamation text-rose-500 text-sm"></i>
                    <span>Gagal</span>
                </div>
                <ul class="list-disc pl-4 text-rose-600/90 font-medium">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form -->
        <form action="{{ route('password.force-change.update') }}" method="POST" class="space-y-5 relative z-10">
            @csrf
            
            <!-- Name Input (Disabled) -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider pl-1">Nama Lengkap</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i class="fa fa-user text-slate-400 text-sm"></i>
                    </div>
                    <input type="text" value="{{ auth()->user()->name }}" disabled class="block w-full pl-11 pr-3 py-3 text-sm text-slate-500 bg-gray-100 border border-slate-200 rounded-2xl cursor-not-allowed font-medium shadow-inner">
                </div>
            </div>

            <!-- Email Input (Disabled) -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider pl-1">Alamat Email</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i class="fa fa-envelope text-slate-400 text-sm"></i>
                    </div>
                    <input type="email" value="{{ auth()->user()->email }}" disabled class="block w-full pl-11 pr-3 py-3 text-sm text-slate-500 bg-gray-100 border border-slate-200 rounded-2xl cursor-not-allowed font-medium shadow-inner">
                </div>
            </div>

            <!-- Password Input -->
            <div class="space-y-1.5">
                <label for="password" class="block text-xs font-bold text-slate-600 uppercase tracking-wider pl-1">Password Baru <span class="text-red-500">*</span></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i class="fa fa-lock text-slate-400 text-sm"></i>
                    </div>
                    <input type="password" name="password" id="password" required placeholder="Minimal 6 karakter" class="block w-full pl-11 pr-11 py-3 text-sm text-slate-700 placeholder-slate-400 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 focus:outline-none transition-all font-medium">
                    <button type="button" onclick="togglePasswordVisibility('password', 'passwordIcon')" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                        <i id="passwordIcon" class="fa fa-eye text-sm"></i>
                    </button>
                </div>
            </div>

            <!-- Password Confirm Input -->
            <div class="space-y-1.5">
                <label for="password_confirmation" class="block text-xs font-bold text-slate-600 uppercase tracking-wider pl-1">Konfirmasi Password Baru <span class="text-red-500">*</span></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i class="fa fa-check-double text-slate-400 text-sm"></i>
                    </div>
                    <input type="password" name="password_confirmation" id="password_confirmation" required placeholder="Ulangi password baru" class="block w-full pl-11 pr-11 py-3 text-sm text-slate-700 placeholder-slate-400 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 focus:outline-none transition-all font-medium">
                    <button type="button" onclick="togglePasswordVisibility('password_confirmation', 'passwordConfirmIcon')" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                        <i id="passwordConfirmIcon" class="fa fa-eye text-sm"></i>
                    </button>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 flex flex-col gap-3">
                <button type="submit" class="w-full py-3.5 px-6 font-bold text-center text-white bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 rounded-2xl shadow-[0_8px_20px_rgba(16,185,129,0.2)] hover:shadow-[0_8px_25px_rgba(16,185,129,0.3)] transition-all duration-200 active:scale-[0.98] text-sm tracking-wide">
                    Simpan & Lanjutkan <i class="fa fa-arrow-right-long ml-1.5"></i>
                </button>
            </div>
        </form>
        
        <!-- Logout option -->
        <div class="mt-6 text-center relative z-10">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-xs font-semibold text-slate-400 hover:text-rose-500 transition-colors">
                    Bukan Anda? Logout
                </button>
            </form>
        </div>

    </div>

    <!-- Password visibility toggle script -->
    <script>
        function togglePasswordVisibility(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const passwordIcon = document.getElementById(iconId);
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('fa-eye');
                passwordIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('fa-eye-slash');
                passwordIcon.classList.add('fa-eye');
            }
        }
    </script>

</body>
</html>
