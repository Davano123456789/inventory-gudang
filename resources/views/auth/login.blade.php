<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Inventory Gudang</title>
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
<body class="m-0 font-sans antialiased bg-gradient-to-tr from-slate-100 via-zinc-50 to-blue-50 min-h-screen flex items-center justify-center p-4">

    <!-- Login Container -->
    <div class="glass-card max-w-md w-full p-8 rounded-[2rem] border border-white/60 shadow-[0_20px_50px_rgba(15,23,42,0.08)] transition-all duration-300">
        
        <!-- Header / Logo -->
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Selamat Datang Kembali</h2>
            <p class="text-sm text-slate-400 mt-1 font-medium">Sistem Manajemen Inventory & Gudang</p>
        </div>

        <!-- Session Alert Success -->
        @if(session('success'))
            <div class="mb-5 p-4 text-xs text-emerald-700 bg-emerald-50/70 border border-emerald-100 rounded-2xl flex items-start gap-2.5">
                <i class="fa fa-circle-check text-emerald-500 mt-0.5 text-sm"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Validation Errors -->
        @if($errors->any())
            <div class="mb-5 p-4 text-xs text-rose-700 bg-rose-50/70 border border-rose-100 rounded-2xl flex flex-col gap-1.5">
                <div class="flex items-center gap-2 font-semibold">
                    <i class="fa fa-circle-exclamation text-rose-500 text-sm"></i>
                    <span>Login Gagal!</span>
                </div>
                <ul class="list-disc pl-4 text-rose-600/90 font-medium">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form -->
        <form action="{{ route('login') }}" method="POST" class="space-y-5">
            @csrf
            
            <!-- Email Input -->
            <div class="space-y-1.5">
                <label for="email" class="block text-xs font-bold text-slate-600 uppercase tracking-wider pl-1">Email</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i class="fa fa-envelope text-slate-400 text-sm"></i>
                    </div>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder="nama@email.com" class="block w-full pl-11 pr-4 py-3 text-sm text-slate-700 placeholder-slate-400 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 focus:outline-none transition-all font-medium">
                </div>
            </div>

            <!-- Password Input -->
            <div class="space-y-1.5">
                <label for="password" class="block text-xs font-bold text-slate-600 uppercase tracking-wider pl-1">Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i class="fa fa-lock text-slate-400 text-sm"></i>
                    </div>
                    <input type="password" name="password" id="password" required placeholder="••••••" class="block w-full pl-11 pr-11 py-3 text-sm text-slate-700 placeholder-slate-400 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 focus:outline-none transition-all font-medium">
                    <button type="button" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                        <i id="passwordIcon" class="fa fa-eye text-sm"></i>
                    </button>
                </div>
            </div>



            <!-- Submit Button -->
            <div class="pt-2">
                <button type="submit" class="w-full py-3.5 px-6 font-bold text-center text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 rounded-2xl shadow-[0_8px_20px_rgba(37,99,235,0.15)] hover:shadow-[0_8px_25px_rgba(37,99,235,0.25)] transition-all duration-200 active:scale-[0.98] text-sm tracking-wide">
                    Masuk ke Dashboard <i class="fa fa-arrow-right-long ml-1.5"></i>
                </button>
            </div>
        </form>



    </div>

    <!-- Password visibility toggle script -->
    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('passwordIcon');
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
