<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'NutriBuddy')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=Fredoka+One&family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/frontendstyle.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/frontendresponsive.css') }}">
    @stack('styles')
</head>
<body>
    <!-- ══ GLOBAL TOAST SYSTEM ══ -->
    <div id="nb-toast-wrap" style="position:fixed;top:20px;right:20px;z-index:99999;display:flex;flex-direction:column;gap:10px;pointer-events:none;"></div>
    <style>
      .nb-toast{pointer-events:all;display:flex;align-items:flex-start;gap:12px;background:#fff;border-radius:14px;padding:14px 18px;min-width:280px;max-width:360px;box-shadow:0 8px 32px rgba(0,0,0,.14);border-left:4px solid #ccc;font-family:'Nunito',sans-serif;animation:nbSlideIn .35s cubic-bezier(.34,1.56,.64,1) forwards;transition:opacity .3s,transform .3s}
      .nb-toast.hiding{opacity:0;transform:translateX(20px)}
      .nb-toast-icon{font-size:1.3rem;flex-shrink:0;margin-top:1px}
      .nb-toast-body{flex:1}
      .nb-toast-title{font-weight:900;font-size:.88rem;color:#1a0040;margin-bottom:2px}
      .nb-toast-msg{font-size:.8rem;color:#666;line-height:1.4}
      .nb-toast-close{background:none;border:none;cursor:pointer;font-size:1rem;color:#aaa;padding:0;line-height:1;flex-shrink:0}
      .nb-toast-close:hover{color:#333}
      .nb-toast.success{border-color:#00c47a}.nb-toast.error{border-color:#ff4d4d}.nb-toast.warning{border-color:#f5a623}.nb-toast.info{border-color:#7c3aed}
      @keyframes nbSlideIn{from{opacity:0;transform:translateX(60px)}to{opacity:1;transform:translateX(0)}}
      /* Confirm Dialog */
      .nb-confirm-overlay{position:fixed;inset:0;z-index:99998;background:rgba(13,0,32,.55);backdrop-filter:blur(6px);display:flex;align-items:center;justify-content:center;animation:nbFadeIn .2s ease}
      .nb-confirm-box{background:#fff;border-radius:20px;padding:28px 28px 22px;max-width:360px;width:90%;box-shadow:0 24px 60px rgba(0,0,0,.22);border:2px solid #f0e8ff;animation:nbPopUp .3s cubic-bezier(.34,1.56,.64,1)}
      .nb-confirm-icon{font-size:2rem;margin-bottom:10px}
      .nb-confirm-title{font-family:'Nunito',sans-serif;font-weight:900;font-size:1.05rem;color:#1a0040;margin-bottom:6px}
      .nb-confirm-msg{font-size:.84rem;color:#666;margin-bottom:20px;line-height:1.5}
      .nb-confirm-btns{display:flex;gap:10px;justify-content:flex-end}
      .nb-confirm-cancel{border:2px solid #e0e0e0;background:#fff;border-radius:10px;padding:9px 18px;font-family:'Nunito',sans-serif;font-weight:800;font-size:.85rem;cursor:pointer;color:#666;transition:all .2s}
      .nb-confirm-cancel:hover{border-color:#ccc;background:#f5f5f5}
      .nb-confirm-ok{border:none;background:linear-gradient(135deg,#ff4d4d,#c0392b);border-radius:10px;padding:9px 18px;font-family:'Nunito',sans-serif;font-weight:900;font-size:.85rem;cursor:pointer;color:#fff;transition:all .2s}
      .nb-confirm-ok:hover{transform:translateY(-1px);box-shadow:0 4px 12px rgba(255,77,77,.4)}
      @keyframes nbFadeIn{from{opacity:0}to{opacity:1}}
      @keyframes nbPopUp{from{opacity:0;transform:scale(.8)}to{opacity:1;transform:scale(1)}}
    </style>
    <script>
      function nbToast(msg, type='success', title=''){
        const icons={success:'✅',error:'❌',warning:'⚠️',info:'ℹ️'};
        const titles={success:'Success',error:'Error',warning:'Warning',info:'Info'};
        const wrap=document.getElementById('nb-toast-wrap');
        const t=document.createElement('div');
        t.className=`nb-toast ${type}`;
        t.innerHTML=`<div class="nb-toast-icon">${icons[type]||'ℹ️'}</div><div class="nb-toast-body"><div class="nb-toast-title">${title||titles[type]||'Notice'}</div><div class="nb-toast-msg">${msg}</div></div><button class="nb-toast-close" onclick="this.closest('.nb-toast').remove()">✕</button>`;
        wrap.appendChild(t);
        setTimeout(()=>{t.classList.add('hiding');setTimeout(()=>t.remove(),350);},3500);
      }
      function nbConfirm(msg, onOk, opts={}){
        const overlay=document.createElement('div');
        overlay.className='nb-confirm-overlay';
        overlay.innerHTML=`<div class="nb-confirm-box"><div class="nb-confirm-icon">${opts.icon||'🗑️'}</div><div class="nb-confirm-title">${opts.title||'Are you sure?'}</div><div class="nb-confirm-msg">${msg}</div><div class="nb-confirm-btns"><button class="nb-confirm-cancel">Cancel</button><button class="nb-confirm-ok">${opts.okText||'Yes, Delete'}</button></div></div>`;
        document.body.appendChild(overlay);
        overlay.querySelector('.nb-confirm-cancel').onclick=()=>overlay.remove();
        overlay.querySelector('.nb-confirm-ok').onclick=()=>{overlay.remove();onOk();};
        overlay.addEventListener('click',e=>{if(e.target===overlay)overlay.remove();});
      }
    </script>
    <div id="cur"></div>
    <div id="cur-ring"></div>

    @include('partials.header')

    <main>
        @yield('content')
    </main>

    @include('partials.footer')

    @guest
        @include('components.login-modal')
    @endguest

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
    <script src="{{ asset('assets/js/frontendscript.js') }}" defer></script>
    @stack('scripts')
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('login')) {
                if (typeof openLoginModal === 'function') {
                    openLoginModal();
                }
            }
        });
    </script>
</body>
</html>
