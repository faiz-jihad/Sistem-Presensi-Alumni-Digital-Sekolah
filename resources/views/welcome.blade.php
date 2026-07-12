<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sistem Presensi Alumni Digital</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
<link
  rel="stylesheet"
  href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"
/>
<style>
body{
    font-family:'Plus Jakarta Sans',sans-serif;
    background:linear-gradient(135deg,#eef6ff,#fff);
}

.swiper{
    width:100%;
    height:280px;
    border-radius:24px;
    overflow:hidden;
}

.swiper-slide img{
    width:100%;
    height:100%;
    object-fit:cover;
}
</style>
</head>
<body>
<div class="max-w-7xl mx-auto px-6 py-10">
<h1 class="text-center text-5xl font-extrabold text-slate-800">Sistem Presensi Alumni Digital</h1>
<p class="text-center text-slate-500 mt-3">Kelola presensi dan alumni secara modern.</p>
<div class="grid lg:grid-cols-2 md:grid-cols-2 gap-6 mt-12">
<div class="swiper murid-slider rounded-3xl shadow-xl">
    <div class="swiper-wrapper">
        <div class="swiper-slide">
            <img src="{{ asset('murid/murid1.png') }}" class="w-full h-[280px] object-cover">
        </div>
        <div class="swiper-slide">
            <img src="{{ asset('murid/murid2.png') }}" class="w-full h-[280px] object-cover">
        </div>
        <div class="swiper-slide">
            <img src="{{ asset('murid/murid3.png') }}" class="w-full h-[280px] object-cover">
        </div>
    </div>
</div>
<div class="swiper guru-slider rounded-3xl shadow-xl">
    <div class="swiper-wrapper">
        <div class="swiper-slide">
            <img src="{{ asset('guru/guru1.png') }}" class="w-full h-[280px] object-cover">
        </div>
        <div class="swiper-slide">
            <img src="{{ asset('guru/guru2.png') }}" class="w-full h-[280px] object-cover">
        </div>
        <div class="swiper-slide">
            <img src="{{ asset('guru/guru3.png') }}" class="w-full h-[280px] object-cover">
        </div>
    </div>
</div>
<div class="swiper alumni-slider rounded-3xl shadow-xl">
    <div class="swiper-wrapper">
        <div class="swiper-slide">
            <img src="{{ asset('alumni/alumni1.png') }}" class="w-full h-[280px] object-cover">
        </div>
        <div class="swiper-slide">
            <img src="{{ asset('alumni/alumni2.png') }}" class="w-full h-[280px] object-cover">
        </div>
        <div class="swiper-slide">
            <img src="{{ asset('alumni/alumni3.png') }}" class="w-full h-[280px] object-cover">
        </div>
    </div>
</div>
<div class="swiper ortu-slider rounded-3xl shadow-xl">
    <div class="swiper-wrapper">
        <div class="swiper-slide">
            <img src="{{ asset('ortu/ortu1.png') }}" class="w-full h-[280px] object-cover">
        </div>
        <div class="swiper-slide">
            <img src="{{ asset('ortu/ortu2.png') }}" class="w-full h-[280px] object-cover">
        </div>
        <div class="swiper-slide">
            <img src="{{ asset('ortu/ortu3.png') }}" class="w-full h-[280px] object-cover">
        </div>
    </div>
</div>

</div>

<div class="text-center mt-10 mb-10">
<a href="/admin/login" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-2xl font-bold">Cobain Sekarang</a>
</div>

</div>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
document.querySelectorAll('.swiper').forEach((slider,index)=>{

    new Swiper(slider,{
        loop:true,
        speed:800,

        autoplay:{
            delay:3500 + index*500,
            disableOnInteraction:false,
        },

        allowTouchMove:false,

        observer:true,
        observeParents:true,
    });

});
</script>
</body>
</html>