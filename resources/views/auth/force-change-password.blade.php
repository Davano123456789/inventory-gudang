<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keamanan Akun - Inventory Gudang</title>
    <!-- Inter Font -->
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
                            blue: '#0f172a', // Dark navy blue matching sidebar
                            light: '#1e293b'
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
        
        <!-- Left Column (Branding & Info with Background Image & Overlay) -->
        <div class="md:w-1/2 relative flex flex-col p-10 md:p-14 overflow-hidden bg-[#0f172a]">
            <!-- Background Image -->
            <img src="{{ asset('images/bg-inventory.jpg') }}" alt="Warehouse Background" class="absolute inset-0 w-full h-full object-cover z-0">
            
            <!-- Dark Navy Semi-transparent Overlay -->
            <div class="absolute inset-0 bg-[#0f172a]/90 z-0"></div>

            <!-- Top Logo Header -->
            <div class="flex items-center gap-3 mb-10 relative z-10">
                <img src="{{ asset('images/logo-pt.png') }}" class="h-10 w-10 object-contain" alt="PT. Bintang Cakra Kencana Logo">
                <div class="flex flex-col leading-tight font-normal">
                    <span class="text-xs text-white tracking-tight uppercase">PT. BINTANG CAKRA</span>
                    <span class="text-[11px] text-white tracking-wider uppercase">KENCANA</span>
                </div>
            </div>

            <!-- Text Content -->
            <div class="relative z-10 mt-10 md:mt-14">
                <h2 class="text-3xl md:text-4xl font-extrabold text-white leading-tight mb-4 drop-shadow-sm">
                    Kelola Gudang Lebih<br>Teratur
                </h2>
                <p class="text-blue-100 text-sm md:text-base leading-relaxed opacity-90 max-w-sm mb-6">
                    Pantau stok, barang masuk, dan barang keluar dalam satu sistem terintegrasi.
                </p>
                <div class="w-32 h-[2px] bg-white rounded-full"></div>
            </div>
        </div>

        <!-- Right Column (Form) -->
        <div class="md:w-1/2 p-8 md:p-12 lg:p-14 flex flex-col justify-center bg-white relative">
            <div class="w-full max-w-sm mx-auto my-auto">
                
                <!-- Heading -->
                <div class="mb-6">
                    <h2 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight mb-2">Keamanan Akun</h2>
                    <p class="text-xs md:text-sm text-slate-500 font-medium leading-relaxed">
                        Ini adalah login pertama Anda. Silakan ganti password <em>default</em> Anda untuk melanjutkan.
                    </p>
                </div>

                <!-- Session Alert Success -->
                @if(session('success'))
                    <div class="mb-4 p-4 text-xs text-emerald-700 bg-emerald-50 border border-emerald-100 rounded-xl flex items-start gap-2.5">
                        <i class="fa fa-circle-check text-emerald-500 mt-0.5 text-sm"></i>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                @endif

                <!-- Validation Errors -->
                @if($errors->any())
                    <div class="mb-4 p-4 text-xs text-rose-700 bg-rose-50 border border-rose-100 rounded-xl flex flex-col gap-1.5">
                        <div class="flex items-center gap-2 font-semibold">
                            <i class="fa fa-circle-exclamation text-rose-500 text-sm"></i>
                            <span>Gagal Memperbarui Password!</span>
                        </div>
                        <ul class="list-disc pl-4 text-rose-600/90 font-medium">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Form -->
                <form action="{{ route('password.force-change.update') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <!-- Name Input (Disabled) -->
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-slate-700">Nama Lengkap</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="fa fa-user text-slate-400 text-sm"></i>
                            </div>
                            <input type="text" value="{{ auth()->user()->name }}" disabled class="block w-full pl-10 pr-4 py-2.5 text-sm text-slate-500 bg-slate-100 border border-slate-200 rounded-lg cursor-not-allowed font-medium">
                        </div>
                    </div>

                    <!-- Username Input (Disabled) -->
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-slate-700">Username</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="fa fa-at text-slate-400 text-sm"></i>
                            </div>
                            <input type="text" value="{{ auth()->user()->username }}" disabled class="block w-full pl-10 pr-4 py-2.5 text-sm text-slate-500 bg-slate-100 border border-slate-200 rounded-lg cursor-not-allowed font-medium">
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div class="space-y-1">
                        <label for="password" class="block text-xs font-bold text-slate-700">Password Baru <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="fa fa-lock text-slate-400 text-sm"></i>
                            </div>
                            <input type="password" name="password" id="password" required placeholder="Minimal 6 karakter" class="block w-full pl-10 pr-10 py-2.5 text-sm text-slate-700 placeholder-slate-400 bg-white border border-slate-300 rounded-lg focus:border-brand-light focus:ring-4 focus:ring-brand-light/10 focus:outline-none transition-all font-medium">
                            <button type="button" onclick="togglePasswordVisibility('password', 'passwordIcon')" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                                <i id="passwordIcon" class="fa fa-eye-slash text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Password Confirm Input -->
                    <div class="space-y-1">
                        <label for="password_confirmation" class="block text-xs font-bold text-slate-700">Konfirmasi Password Baru <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="fa fa-check-double text-slate-400 text-sm"></i>
                            </div>
                            <input type="password" name="password_confirmation" id="password_confirmation" required placeholder="Ulangi password baru" class="block w-full pl-10 pr-10 py-2.5 text-sm text-slate-700 placeholder-slate-400 bg-white border border-slate-300 rounded-lg focus:border-brand-light focus:ring-4 focus:ring-brand-light/10 focus:outline-none transition-all font-medium">
                            <button type="button" onclick="togglePasswordVisibility('password_confirmation', 'passwordConfirmIcon')" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                                <i id="passwordConfirmIcon" class="fa fa-eye-slash text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button type="submit" class="w-full py-3 px-6 font-bold text-center text-white bg-brand-blue hover:bg-brand-light rounded-lg transition-colors text-sm tracking-wide">
                            Simpan & Lanjutkan <i class="fa fa-arrow-right-long ml-1.5"></i>
                        </button>
                    </div>
                </form>
                
                <!-- Logout option -->
                <div class="mt-4 text-center">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-xs font-semibold text-slate-400 hover:text-rose-600 transition-colors">
                            Bukan Anda? Logout
                        </button>
                    </form>
                </div>

                <!-- Footer -->
                <div class="mt-6 text-center">
                    <p class="text-xs text-slate-400 font-medium">Inventory Gudang &bull; Sistem Manajemen Inventori</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Password visibility toggle script -->
    <script>
        function togglePasswordVisibility(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const passwordIcon = document.getElementById(iconId);
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
