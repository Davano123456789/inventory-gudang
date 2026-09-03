<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Inventory Gudang</title>
    <!-- Inter/Outfit Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            blue: '#0a235c', // Dark blue from the design
                            light: '#1e3c87'
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="m-0 font-sans antialiased bg-slate-100 min-h-screen flex items-center justify-center p-4 md:p-6 lg:p-8">

    <!-- Main Container -->
    <div class="w-full max-w-5xl bg-white rounded-3xl shadow-[0_20px_50px_rgba(15,23,42,0.1)] overflow-hidden flex flex-col md:flex-row min-h-[600px]">
        
        <!-- Left Column (Branding & Illustration) -->
        <div class="md:w-1/2 bg-brand-blue relative flex flex-col pt-12 overflow-hidden">
            
            <!-- Text Content Wrapper -->
            <div class="px-10 md:px-14 relative z-10">
                <!-- Top Logo -->
                <div class="flex items-center gap-3 mb-12 lg:mb-16">
                    <h1 class="text-white text-xl font-bold tracking-wide">Inventory Gudang</h1>
                </div>

                <!-- Text Content -->
                <div>
                    <h2 class="text-3xl md:text-4xl font-extrabold text-white leading-tight mb-4">
                        Kelola Gudang Lebih<br>Teratur
                    </h2>
                    <p class="text-blue-100 text-sm md:text-base leading-relaxed opacity-90 max-w-sm">
                        Pantau stok, barang masuk, dan barang keluar dalam satu sistem terintegrasi.
                    </p>
                </div>
            </div>

            <!-- Illustration -->
            <div class="mt-8 flex-grow relative w-full">
                <img src="{{ asset('images/gambar-gudang.png') }}" alt="Warehouse Illustration" class="absolute inset-0 w-full h-full object-cover object-bottom mix-blend-luminosity opacity-90" style="mix-blend-mode: normal; opacity: 1;">
            </div>
        </div>

        <!-- Right Column (Form) -->
        <div class="md:w-1/2 p-10 md:p-14 lg:p-16 flex flex-col justify-center bg-white relative">
            <div class="w-full max-w-sm mx-auto">
                
                <!-- Heading -->
                <div class="mb-10">
                    <h2 class="text-3xl font-extrabold text-slate-800 tracking-tight mb-2">Masuk</h2>
                    <p class="text-sm text-slate-500 font-medium">Masuk untuk mengelola inventori gudang.</p>
                </div>

                <!-- Session Alert Success -->
                @if(session('success'))
                    <div class="mb-6 p-4 text-xs text-emerald-700 bg-emerald-50 border border-emerald-100 rounded-xl flex items-start gap-2.5">
                        <i class="fa fa-circle-check text-emerald-500 mt-0.5 text-sm"></i>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                @endif

                <!-- Validation Errors -->
                @if($errors->any())
                    <div class="mb-6 p-4 text-xs text-rose-700 bg-rose-50 border border-rose-100 rounded-xl flex flex-col gap-1.5">
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
                    
                    <!-- Username Input -->
                    <div class="space-y-1.5">
                        <label for="username" class="block text-xs font-bold text-slate-700">Username</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="fa fa-user text-slate-400 text-sm"></i>
                            </div>
                            <input type="text" name="username" id="username" value="{{ old('username') }}" required placeholder="Masukkan username" class="block w-full pl-10 pr-4 py-3 text-sm text-slate-700 placeholder-slate-400 bg-white border border-slate-300 rounded-lg focus:border-brand-light focus:ring-4 focus:ring-brand-light/10 focus:outline-none transition-all font-medium">
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div class="space-y-1.5">
                        <label for="password" class="block text-xs font-bold text-slate-700">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="fa fa-lock text-slate-400 text-sm"></i>
                            </div>
                            <input type="password" name="password" id="password" required placeholder="Masukkan password" class="block w-full pl-10 pr-10 py-3 text-sm text-slate-700 placeholder-slate-400 bg-white border border-slate-300 rounded-lg focus:border-brand-light focus:ring-4 focus:ring-brand-light/10 focus:outline-none transition-all font-medium">
                            <button type="button" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                                <i id="passwordIcon" class="fa fa-eye-slash text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center pt-1">
                        <input id="remember" type="checkbox" name="remember" class="w-4 h-4 text-brand-blue bg-gray-100 border-gray-300 rounded focus:ring-brand-light focus:ring-2 cursor-pointer">
                        <label for="remember" class="ml-2 text-xs font-medium text-slate-700 cursor-pointer">Ingat saya</label>
                    </div>

                    <!-- Submit Button -->
                    <div class="">
                        <button type="submit" class="w-full py-3.5 px-6 font-bold text-center text-white bg-brand-blue hover:bg-brand-light rounded-lg shadow-md hover:shadow-lg transition-all duration-200 active:scale-[0.98] text-sm tracking-wide">
                            Masuk
                        </button>
                    </div>
                </form>
                
                <!-- Footer -->
                <div class="absolute bottom-6 left-0 right-0 text-center">
                    <p class="text-xs text-slate-400 font-medium">Inventory Gudang &bull; Sistem Manajemen Inventori</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Password visibility toggle script -->
    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('passwordIcon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('fa-eye-slash');
                passwordIcon.classList.add('fa-eye');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('fa-eye');
                passwordIcon.classList.add('fa-eye-slash');
            }
        }
    </script>

</body>
</html>
