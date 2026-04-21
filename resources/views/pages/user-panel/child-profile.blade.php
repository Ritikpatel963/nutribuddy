@extends('layouts.user-panel')
@section('title', 'Health Scores')



@push('styles')
<style>
:root{
  --pk:#FF4D8F;--pkl:#FFD6E8;--pkd:#C0306F;--pu:#7C3AED;--pul:#EDE9FE;
  --g:#00000;--gdk:#00000;--glt:#ffd0ed;--gbg:#F2FFF9;--ghov:#00BE80;
  --or:#FF6B35;--orl:#FFE8DF;--rd:#E53935;--rdl:#FDE8E8;--ye:#FFD600;--yel:#FFFBE0;
  --dk:#0D0020;--bd:#24163f;--mu:#6B6B80;--br:#EAEAEA;
  --bg:#F7F7F9;--wh:#FFFFFF;
  --fh:'Nunito',sans-serif;--fb:'DM Sans',sans-serif;
  --r:12px;--rl:18px;--rp:999px;
  --sh:0 2px 12px rgba(13,0,32,.06);--shh:0 10px 28px rgba(255,77,143,.16);
  --max:1080px;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html{scroll-behavior:smooth;}
body{font-family:var(--fb);background:var(--bg);color:var(--bd);min-height:100vh;font-size:15px;line-height:1.6;}
button{cursor:pointer;font-family:var(--fb);}
input,select{font-family:var(--fb);}
::-webkit-scrollbar{width:5px;}
::-webkit-scrollbar-thumb{background:var(--br);border-radius:99px;}

/* SHELL */
.nav{display:none;}
.shell{max-width:var(--max);margin:0 auto;padding:32px 24px 80px;}

/* VIEWS */
.view{display:none;}
.view.on{display:block;}

/* ANIMATIONS */
@keyframes fu{from{opacity:0;transform:translateY(18px);}to{opacity:1;transform:translateY(0);}}
.a1{animation:fu .45s .00s both;}.a2{animation:fu .45s .08s both;}
.a3{animation:fu .45s .16s both;}.a4{animation:fu .45s .24s both;}

/* PROFILE HEADER */
.ph{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:6px;}
.ph-title{font-family:var(--fh);font-size:clamp(1.5rem,3.5vw,2rem);font-weight:800;letter-spacing:-.035em;color:var(--dk);}
.ph-meta{font-size:.85rem;color:var(--mu);display:flex;align-items:center;flex-wrap:wrap;gap:6px;margin-bottom:28px;}
.ph-meta-dot{color:var(--br);}
.btn-edit{font-family:var(--fh);font-size:.82rem;font-weight:800;color:var(--pk);background:var(--wh);border:2px solid var(--pkl);border-radius:var(--rp);padding:10px 22px;transition:all .18s;}
.btn-edit:hover{background:var(--pk);border-color:var(--pk);color:#fff;transform:translateY(-1px);box-shadow:0 8px 20px rgba(255,77,143,.2);}

/* STAT TILES */
.tiles{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:28px;}
.tile{background:var(--wh);border:1px solid var(--br);border-radius:var(--rl);padding:20px 16px 16px;text-align:center;box-shadow:var(--sh);transition:transform .2s,box-shadow .2s;}
.tile:hover{transform:translateY(-3px);box-shadow:var(--shh);border-color:var(--pkl);}
.tv{font-family:var(--fh);font-size:clamp(1.7rem,3.5vw,2.4rem);font-weight:900;letter-spacing:-.04em;line-height:1;margin-bottom:6px;}
.tv.cg{color:var(--pk);}.tv.co{color:var(--or);}.tv.cp{color:var(--pu);}.tv.cr{color:var(--gdk);}
.tu{font-size:.5em;letter-spacing:0;font-weight:700;}
.tl{font-size:.78rem;color:var(--mu);font-weight:500;margin-bottom:7px;}
.tb{font-family:var(--fh);font-size:.68rem;font-weight:700;border-radius:var(--rp);padding:3px 10px;display:inline-flex;align-items:center;gap:3px;}
.tb.up{color:var(--gdk);background:var(--glt);}.tb.dn{color:var(--pkd);background:var(--pkl);}

/* TABS */
.tab-bar{display:flex;border-bottom:2px solid var(--br);margin-bottom:24px;overflow-x:auto;scrollbar-width:none;}
.tab-bar::-webkit-scrollbar{display:none;}
.tab-btn{font-family:var(--fb);font-size:.88rem;font-weight:500;color:var(--mu);background:none;border:none;border-bottom:2.5px solid transparent;padding:11px 20px;white-space:nowrap;margin-bottom:-2px;transition:all .18s;}
.tab-btn:hover{color:var(--dk);}
.tab-btn.on{font-weight:800;color:var(--pk);border-bottom-color:var(--pk);}
.tp{display:none;}
.tp.on{display:block;}

/* PROFILE TAB */
.pcols{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
.ic{background:var(--wh);border:1px solid var(--br);border-radius:var(--rl);overflow:hidden;box-shadow:var(--sh);}
.ir{display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-bottom:1px solid var(--br);gap:12px;transition:background .14s;}
.ir:last-child{border-bottom:none;}
.ir:hover{background:#fff8fb;}
.ik{font-size:.84rem;color:var(--mu);flex-shrink:0;}
.iv{font-family:var(--fh);font-size:.86rem;font-weight:600;color:var(--dk);text-align:right;}
.iv.gr{color:var(--g);}
.gap-card{background:var(--wh);border:1px solid var(--br);border-radius:var(--rl);overflow:hidden;box-shadow:var(--sh);}
.gap-head{font-family:var(--fh);font-size:.9rem;font-weight:700;color:var(--dk);padding:16px 20px 14px;border-bottom:1px solid var(--br);}
.gap-row{display:flex;align-items:center;justify-content:space-between;padding:12px 20px;border-bottom:1px solid var(--br);gap:12px;transition:background .14s;}
.gap-row:last-child{border-bottom:none;}
.gap-row:hover{background:#fff8fb;}
.gn{font-size:.86rem;font-weight:500;color:var(--dk);}
.gb{font-family:var(--fh);font-size:.62rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;border-radius:6px;padding:4px 10px;flex-shrink:0;}
.gb.crit{color:var(--rd);background:var(--rdl);border:1.5px solid var(--rd);}
.gb.gap {color:var(--or);background:var(--orl);border:1.5px solid var(--or);}
.gb.part{color:#92600a;background:var(--yel);border:1.5px solid var(--ye);}
.gb.met {color:var(--g);background:var(--gbg);border:1.5px solid var(--g);}
.btn-ci{width:100%;margin-top:12px;font-family:var(--fh);font-size:.92rem;font-weight:900;color:#fff;background:linear-gradient(135deg,var(--pk),var(--pkd));border:none;border-radius:var(--r);padding:15px 24px;display:flex;align-items:center;justify-content:center;gap:8px;box-shadow:0 8px 18px rgba(255,77,143,.2);transition:all .18s;}
.btn-ci:hover{transform:translateY(-2px);box-shadow:0 12px 24px rgba(255,77,143,.24);}
.arr{display:inline-block;transition:transform .2s;}
.btn-ci:hover .arr,.btn-co:hover .arr{transform:translateX(5px);}

/* MILESTONES */
.mg{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
.mc{background:var(--wh);border:1px solid var(--br);border-radius:var(--rl);padding:20px;box-shadow:var(--sh);transition:transform .2s,box-shadow .2s;}
.mc:hover{transform:translateY(-3px);box-shadow:var(--shh);border-color:var(--pkl);}
.mi{font-size:1.6rem;margin-bottom:10px;}
.mn{font-family:var(--fh);font-size:.9rem;font-weight:700;color:var(--dk);margin-bottom:4px;}
.md{font-size:.78rem;color:var(--mu);line-height:1.55;margin-bottom:12px;}
.mbar{height:7px;background:var(--br);border-radius:99px;overflow:hidden;}
.mbf{height:100%;border-radius:99px;animation:gb2 1s cubic-bezier(.4,0,.2,1) .3s both;transform-origin:left;}
@keyframes gb2{from{transform:scaleX(0);}to{transform:scaleX(1);}}
.bg-g{background:var(--g);}.bg-p{background:var(--pu);}.bg-o{background:var(--or);}.bg-r{background:#e91e8c;}

/* FOOD LOG */
.fl{display:flex;flex-direction:column;gap:10px;}
.fc{background:var(--wh);border:1px solid var(--br);border-radius:var(--rl);padding:15px 20px;display:flex;align-items:center;justify-content:space-between;gap:14px;box-shadow:var(--sh);transition:transform .18s,box-shadow .18s;}
.fc:hover{transform:translateY(-2px);box-shadow:var(--shh);border-color:var(--pkl);}
.fl-l{display:flex;align-items:center;gap:12px;}
.fi{width:42px;height:42px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0;}
.fn{font-family:var(--fh);font-size:.86rem;font-weight:700;color:var(--dk);margin-bottom:2px;}
.fd{font-size:.74rem;color:var(--mu);}
.fs{font-family:var(--fh);font-size:.62rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;border-radius:6px;padding:4px 10px;flex-shrink:0;}
.fs.nw{color:var(--g);background:var(--gbg);border:1.5px solid var(--g);}
.fs.rg{color:var(--pu);background:var(--pul);border:1.5px solid var(--pu);}
.fs.rj{color:var(--rd);background:var(--rdl);border:1.5px solid var(--rd);}

/* SEASON */
.sb{background:linear-gradient(135deg,#fff7fb,#fff3e9);border:1.5px solid var(--pkl);border-radius:var(--rl);padding:20px 24px;margin-bottom:16px;display:flex;align-items:center;gap:16px;}
.se{font-size:2.2rem;flex-shrink:0;}
.st{font-family:var(--fh);font-size:1rem;font-weight:900;color:var(--pkd);margin-bottom:3px;}
.ss{font-size:.8rem;color:var(--mu);line-height:1.5;}
.scs{display:flex;flex-direction:column;gap:10px;}
.sc{background:var(--wh);border:1px solid var(--br);border-radius:var(--rl);padding:15px 20px;display:flex;align-items:flex-start;gap:12px;box-shadow:var(--sh);transition:transform .18s,box-shadow .18s;}
.sc:hover{transform:translateY(-2px);box-shadow:var(--shh);border-color:var(--pkl);}
.sd{width:10px;height:10px;border-radius:50%;flex-shrink:0;margin-top:5px;}
.sd.g{background:var(--g);}.sd.o{background:var(--or);}.sd.p{background:var(--pu);}
.sct{font-family:var(--fh);font-size:.86rem;font-weight:700;color:var(--dk);margin-bottom:3px;}
.scd{font-size:.78rem;color:var(--mu);line-height:1.55;}

/* ═══ ONBOARDING ═══ */
.ob-shell{max-width:680px;margin:0 auto;padding:32px 20px 80px;}
@keyframes ou{from{opacity:0;transform:translateY(16px);}to{opacity:1;transform:translateY(0);}}
.os{display:none;}
.os.on{display:block;animation:ou .38s ease both;}

/* Step bar */
.osb{display:flex;align-items:center;gap:10px;margin-bottom:28px;}
.ob-badge{font-family:var(--fh);font-size:.62rem;font-weight:900;letter-spacing:.08em;text-transform:uppercase;color:var(--pkd);background:var(--pkl);border:1.5px solid var(--pk);border-radius:var(--rp);padding:5px 13px;white-space:nowrap;flex-shrink:0;}
.ob-lines{display:flex;gap:5px;flex:1;}
.ol{flex:1;height:3px;border-radius:99px;background:var(--br);transition:background .4s;}
.ol.done{background:var(--pkd);}.ol.active{background:var(--pk);}
.btn-back{font-family:var(--fh);font-size:.82rem;font-weight:800;color:var(--pk);background:var(--wh);border:2px solid var(--pkl);border-radius:var(--rp);padding:9px 15px;display:flex;align-items:center;gap:4px;flex-shrink:0;transition:all .18s;}
.btn-back:hover{border-color:var(--pk);background:#fff5f9;}

.oh1{font-family:var(--fh);font-size:clamp(1.45rem,4vw,1.85rem);font-weight:800;letter-spacing:-.035em;color:var(--dk);margin-bottom:6px;}
.osub{font-size:.86rem;color:var(--mu);margin-bottom:24px;}

/* Field */
.fl-label{font-family:var(--fh);font-size:.62rem;font-weight:700;letter-spacing:.09em;text-transform:uppercase;color:var(--mu);margin-bottom:6px;display:block;}
.nb-i,.nb-s{width:100%;background:var(--wh);border:1.5px solid var(--br);border-radius:var(--r);padding:12px 14px;font-family:var(--fb);font-size:.9rem;font-weight:500;color:var(--dk);outline:none;transition:all .18s;appearance:none;-webkit-appearance:none;}
.nb-i:focus,.nb-s:focus{border-color:var(--pk);box-shadow:0 0 0 3px rgba(255,77,143,.12);}
.nb-i.err,.nb-s.err{border-color:var(--rd)!important;box-shadow:0 0 0 3px rgba(229,57,53,.09)!important;}
.sw{position:relative;}
.sw::after{content:'▾';position:absolute;right:13px;top:50%;transform:translateY(-50%);font-size:.8rem;color:var(--mu);pointer-events:none;}
.fr{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px;}
.fc2{display:flex;flex-direction:column;}
.ff{margin-bottom:14px;}
.emsg{font-size:.76rem;color:var(--rd);font-weight:500;margin-top:4px;display:none;align-items:center;gap:4px;}
.emsg.on{display:flex;}
.sec-err{display:none;background:var(--rdl);border:1.5px solid var(--rd);border-radius:var(--r);padding:12px 16px;margin-bottom:16px;font-size:.84rem;color:var(--rd);font-weight:600;align-items:center;gap:8px;}
.sec-err.on{display:flex;}

/* Gender */
.gr-row{display:flex;gap:12px;margin-bottom:24px;flex-wrap:wrap;}
.gbtn{display:flex;flex-direction:column;align-items:center;gap:6px;background:var(--wh);border:2px solid var(--br);border-radius:14px;padding:14px 20px;min-width:80px;transition:all .18s;}
.gbtn:hover{border-color:var(--pk);}
.gbtn.sel{border-color:var(--pk);background:#fff5f9;}
.gbtn span:first-child{font-size:1.8rem;}
.gl{font-family:var(--fh);font-size:.74rem;font-weight:600;color:var(--dk);}

/* Phase */
.ppr{display:flex;gap:7px;flex-wrap:wrap;margin-bottom:12px;}
.pp{font-family:var(--fh);font-size:.72rem;font-weight:600;background:var(--wh);border:1.5px solid var(--br);border-radius:var(--rp);padding:7px 14px;transition:all .18s;white-space:nowrap;}
.pp span{display:block;font-size:.6rem;font-weight:400;color:var(--mu);margin-top:1px;}
.pp.sel{background:var(--pk);color:#fff;border-color:var(--pk);}
.pp.sel span{color:rgba(255,255,255,.65);}
.pnote{background:#fff5f9;border-left:3px solid var(--pk);border-radius:10px;padding:12px 16px;font-size:.82rem;color:var(--pkd);line-height:1.55;margin-bottom:20px;}
.pnote strong{font-weight:700;}

/* Concern cards */
.ccl{display:flex;flex-direction:column;gap:9px;margin-bottom:22px;}
.cc{display:flex;align-items:center;gap:13px;background:var(--wh);border:1.5px solid var(--br);border-radius:var(--r);padding:14px 18px;transition:all .18s;}
.cc:hover{border-color:var(--pk);box-shadow:0 6px 16px rgba(255,77,143,.12);}
.cc.sel{background:#fff5f9;border-color:var(--pk);}
.ci{font-size:1.45rem;width:34px;text-align:center;flex-shrink:0;}
.cn{font-family:var(--fh);font-size:.88rem;font-weight:700;color:var(--dk);margin-bottom:2px;}
.cde{font-size:.77rem;color:var(--mu);}

/* Diet / picky / life */
.dg{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:18px;}
.dc,.pc,.lc{display:flex;align-items:center;gap:10px;background:var(--wh);border:1.5px solid var(--br);border-radius:var(--r);padding:13px 16px;transition:all .18s;}
.dc:hover,.pc:hover,.lc:hover{border-color:var(--pk);}
.dc.sel,.pc.sel,.lc.sel{background:#fff5f9;border-color:var(--pk);}
.dc span:first-child,.pc span:first-child,.lc span:first-child{font-size:1.25rem;flex-shrink:0;}
.dn2,.pn{font-family:var(--fh);font-size:.85rem;font-weight:600;color:var(--dk);}
.lcn{font-family:var(--fh);font-size:.86rem;font-weight:700;color:var(--dk);margin-bottom:2px;}
.lcd{font-size:.75rem;color:var(--mu);}
.pl{display:flex;flex-direction:column;gap:8px;margin-bottom:18px;}
.ll{display:flex;flex-direction:column;gap:8px;margin-bottom:18px;}
.pt{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:18px;}
.ptl{font-family:var(--fh);font-size:.77rem;font-weight:600;background:var(--wh);border:1.5px solid var(--br);border-radius:var(--rp);padding:7px 16px;transition:all .18s;}
.ptl:hover{border-color:var(--pk);}
.ptl.sel{background:var(--pk);color:#fff;border-color:var(--pk);}

/* Goals */
.gcl{display:flex;flex-direction:column;gap:9px;margin-bottom:20px;}
.gc{display:flex;align-items:center;gap:13px;background:var(--wh);border:1.5px solid var(--br);border-radius:var(--r);padding:14px 18px;transition:all .18s;}
.gc:hover{border-color:var(--pk);}
.gc.sel{background:#fff5f9;border-color:var(--pk);}
.gci{font-size:1.3rem;width:32px;text-align:center;flex-shrink:0;}
.gcn{font-family:var(--fh);font-size:.88rem;font-weight:700;color:var(--dk);margin-bottom:2px;}
.gcd{font-size:.76rem;color:var(--mu);}

/* Generate note */
.gnote{background:#fff7fb;border-left:3px solid var(--pk);border-radius:10px;padding:14px 16px;margin-bottom:22px;}
.gnote-h{font-family:var(--fh);font-size:.64rem;font-weight:900;letter-spacing:.08em;text-transform:uppercase;color:var(--pkd);margin-bottom:5px;display:flex;align-items:center;gap:5px;}
.gnote-b{font-size:.8rem;color:var(--mu);line-height:1.6;}
.sec-l{font-family:var(--fh);font-size:.62rem;font-weight:700;letter-spacing:.09em;text-transform:uppercase;color:var(--mu);margin-bottom:8px;margin-top:4px;display:block;}
.req{color:var(--rd);}

/* Continue btn */
.step-actions{display:flex;flex-direction:column;gap:10px;margin-top:14px;}
.btn-co{width:100%;font-family:var(--fh);font-size:.92rem;font-weight:900;color:#fff;background:linear-gradient(135deg,var(--pk),var(--pkd));border:none;border-radius:var(--r);padding:15px 24px;display:flex;align-items:center;justify-content:center;gap:8px;box-shadow:0 8px 18px rgba(255,77,143,.22);transition:all .18s;margin-top:6px;}
.btn-co:hover{transform:translateY(-2px);box-shadow:0 12px 24px rgba(255,77,143,.26);}
.btn-co.or{background:linear-gradient(135deg,var(--or),#ff935f);box-shadow:0 8px 18px rgba(255,107,53,.22);}
.btn-co.or:hover{box-shadow:0 12px 24px rgba(255,107,53,.28);}
.btn-secondary{width:100%;font-family:var(--fh);font-size:.88rem;font-weight:800;color:var(--pk);background:var(--wh);border:2px solid var(--pkl);border-radius:var(--r);padding:13px 22px;display:flex;align-items:center;justify-content:center;gap:8px;transition:all .18s;}
.btn-secondary:hover{border-color:var(--pk);background:#fff5f9;transform:translateY(-1px);}

/* Plan hero */
.ph2{background:linear-gradient(135deg,var(--pk),var(--pu));border-radius:20px;padding:36px 28px;text-align:center;margin-bottom:20px;}
.ph2-em{font-size:2.4rem;margin-bottom:10px;}
.ph2-t{font-family:var(--fh);font-size:clamp(1.35rem,4vw,1.85rem);font-weight:800;letter-spacing:-.03em;color:#fff;margin-bottom:6px;}
.ph2-s{font-size:.82rem;color:rgba(255,255,255,.72);margin-bottom:14px;}
.ph2-tags{display:flex;align-items:center;justify-content:center;gap:7px;flex-wrap:wrap;}
.ph2-tag{font-family:var(--fh);font-size:.62rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;background:rgba(255,255,255,.14);color:#fff;border:1px solid rgba(255,255,255,.25);border-radius:var(--rp);padding:5px 12px;}
.rda-row{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:22px;}
.rda-c{background:var(--wh);border:1px solid var(--br);border-radius:var(--r);padding:18px 14px;text-align:center;box-shadow:var(--sh);}
.rda-v{font-family:var(--fh);font-size:1.9rem;font-weight:800;letter-spacing:-.04em;margin-bottom:4px;}
.rda-v.cg{color:var(--g);}.rda-v.co{color:var(--or);}.rda-v.cp{color:var(--pu);}
.rda-l{font-size:.74rem;color:var(--mu);}
.plan-unlock{font-family:var(--fh);font-size:1.15rem;font-weight:800;letter-spacing:-.025em;color:var(--dk);margin-bottom:16px;}
.pgrid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:12px;}
.pcard{background:var(--wh);border:1.5px solid var(--br);border-radius:var(--r);padding:18px 16px;display:flex;flex-direction:column;position:relative;box-shadow:var(--sh);transition:transform .2s,box-shadow .2s;}
.pcard:hover{transform:translateY(-3px);box-shadow:var(--shh);}
.pcard.best{border-color:var(--pk);background:#fff7fb;}
.pbest{position:absolute;top:-1px;left:50%;transform:translateX(-50%);font-family:var(--fh);font-size:.6rem;font-weight:800;letter-spacing:.07em;text-transform:uppercase;background:var(--pk);color:#fff;border-radius:0 0 8px 8px;padding:4px 12px;white-space:nowrap;}
.pname{font-family:var(--fh);font-size:.62rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--mu);margin-bottom:8px;margin-top:14px;}
.pcard:not(.best) .pname{margin-top:0;}
.pprice{font-family:var(--fh);font-size:1.65rem;font-weight:800;letter-spacing:-.04em;color:var(--dk);margin-bottom:2px;}
.pprice-sm{font-size:.68rem;font-weight:500;color:var(--mu);}
.pfeat{list-style:none;margin:12px 0 16px;flex:1;}
.pfeat li{font-size:.76rem;color:var(--dk);padding:3px 0;display:flex;align-items:flex-start;gap:6px;}
.pfeat li::before{content:'✓';color:var(--g);font-weight:700;flex-shrink:0;}
.btn-pl{font-family:var(--fh);font-size:.78rem;font-weight:700;border-radius:var(--rp);padding:10px 14px;width:100%;transition:all .18s;}
.btn-pl.out{background:none;border:1.5px solid var(--pk);color:var(--pk);}
.btn-pl.out:hover{background:var(--pk);color:#fff;}
.btn-pl.sol{background:var(--pk);color:#fff;border:none;}
.btn-pl.sol:hover{background:var(--pkd);}
.pnote2{text-align:center;font-size:.74rem;color:var(--mu);margin-top:10px;}
.btn-sp{width:100%;margin-top:20px;font-family:var(--fh);font-size:.9rem;font-weight:700;color:#fff;background:var(--pu);border:none;border-radius:var(--r);padding:15px 24px;display:flex;align-items:center;justify-content:center;gap:8px;box-shadow:0 4px 14px rgba(124,58,237,.25);transition:all .18s;}
.btn-sp:hover{background:#6d28d9;transform:translateY(-2px);}

/* Toast */
.toast{position:fixed;bottom:28px;left:50%;transform:translateX(-50%) translateY(70px);background:var(--gdk);color:#fff;font-family:var(--fh);font-size:.84rem;font-weight:600;border-radius:var(--rp);padding:12px 26px;box-shadow:0 6px 24px rgba(27,67,50,.35);opacity:0;transition:all .34s cubic-bezier(.4,0,.2,1);pointer-events:none;z-index:999;white-space:nowrap;}
.toast.on{opacity:1;transform:translateX(-50%) translateY(0);}

/* RESPONSIVE */
@media(max-width:860px){
  .tiles{grid-template-columns:repeat(2,1fr);}
  .pcols{grid-template-columns:1fr;}
  .mg{grid-template-columns:1fr;}
}
@media(max-width:680px){
  .shell,.ob-shell{padding:20px 14px 60px;}
  .pgrid{grid-template-columns:1fr;}
  .fr{grid-template-columns:1fr;}
  .dg{grid-template-columns:1fr;}
}
@media(max-width:420px){
  .tiles{gap:10px;}
  .tile{padding:16px 10px 12px;}
  .tab-btn{padding:10px 14px;font-size:.82rem;}
  .gr-row{gap:8px;}
  .rda-row{grid-template-columns:1fr;}
}
</style>
@endpush


@section('panel-content')

<!-- NAV -->
<nav class="nav">
  <!-- <a href="#" class="nav-logo">
    <div class="nav-logo-dot">🌿</div>
    NutriBuddy <span class="nav-logo-sub">KIDS</span>
  </a> -->
  <div class="nav-r">
    <button class="btn-nav-back" id="navBack" hidden onclick="backToProfile()">← Back to Profile</button>
  </div>
</nav>

<!-- ═══ VIEW: PROFILE ═══ -->
<div class="view on" id="vProfile">
<main class="shell">

  <div class="a1">
    <div class="ph">
      <h1 class="ph-title" id="profTitle">Arjun's Profile</h1>
      <button class="btn-edit" onclick="openEdit()">✏️ Edit Profile</button>
    </div>
    <p class="ph-meta" id="profMeta">
      5 years <span class="ph-meta-dot">·</span>
      Boy <span class="ph-meta-dot">·</span>
      Pure vegetarian <span class="ph-meta-dot">·</span>
      West India (Gujarat)
    </p>
  </div>

  <div class="tiles a2">
    <div class="tile"><div class="tv cg" id="scoreVal">0</div><div class="tl">Health Score</div><span class="tb up">↑ 21 pts</span></div>
    <div class="tile"><div class="tv co" id="weightTile">18 <span class="tu">kg</span></div><div class="tl">Weight</div><span class="tb up">↑ 1 kg</span></div>
    <div class="tile"><div class="tv cp" id="heightTile">106 <span class="tu">cm</span></div><div class="tl">Height</div><span class="tb up">↑ 2 cm</span></div>
    <div class="tile"><div class="tv cr">1<span class="tu">×</span></div><div class="tl">Sick This Qtr</div><span class="tb dn">↓ from 4×</span></div>
  </div>

  <div class="tab-bar a3">
    <button class="tab-btn on" onclick="switchTab(this,'tProfile')">Profile</button>
    <button class="tab-btn" onclick="switchTab(this,'tMilestone')">Milestones</button>
    <button class="tab-btn" onclick="switchTab(this,'tFood')">Food Log</button>
    <button class="tab-btn" onclick="switchTab(this,'tSeason')">Season Plan</button>
  </div>

  <!-- Profile tab -->
  <div id="tProfile" class="tp on a4">
    <div class="pcols">
      <div class="ic">
        <div class="ir"><span class="ik">Age phase</span><span class="iv" id="ptPhase">Phase 1 — 4–6 yrs</span></div>
        <div class="ir"><span class="ik">Diet type</span><span class="iv" id="ptDiet">Pure vegetarian</span></div>
        <div class="ir"><span class="ik">Picky level</span><span class="iv" id="ptPicky">Slightly picky</span></div>
        <div class="ir"><span class="ik">Allergies</span><span class="iv" id="ptAllergies">None</span></div>
        <div class="ir"><span class="ik">Activity</span><span class="iv" id="ptActivity">Moderately active</span></div>
        <div class="ir"><span class="ik">Region</span><span class="iv" id="ptRegion">West India (Gujarat)</span></div>
        <div class="ir"><span class="ik">Current season</span><span class="iv">🌞 Greeshma (May–June)</span></div>
        <div class="ir"><span class="ik">Plan</span><span class="iv gr" id="ptPlan">Champion ✓</span></div>
      </div>
      <div style="display:flex;flex-direction:column;gap:12px;">
        <div class="gap-card">
          <div class="gap-head">Detected Nutrient Gaps</div>
          <div class="gap-row"><span class="gn">Vitamin D</span><span class="gb crit">Critical</span></div>
          <div class="gap-row"><span class="gn">Omega-3</span><span class="gb gap">Gap</span></div>
          <div class="gap-row"><span class="gn">Iron</span><span class="gb gap">Gap</span></div>
          <div class="gap-row"><span class="gn">Calcium</span><span class="gb part">Partial</span></div>
          <div class="gap-row"><span class="gn">Vitamin C</span><span class="gb met">Met ✓</span></div>
        </div>
        <button class="btn-ci" id="ciBtn" onclick="doCheckin()">Quarterly Check-in <span class="arr">→</span></button>
      </div>
    </div>
  </div>

  <!-- Milestones tab -->
  <div id="tMilestone" class="tp">
    <div class="mg">
      <div class="mc"><div class="mi">🏆</div><div class="mn">Health Score Growth</div><div class="md">Improved from 54 → 75 in 90 days. Target: 85 by next quarter.</div><div class="mbar"><div class="mbf bg-g" style="width:75%"></div></div></div>
      <div class="mc"><div class="mi">📏</div><div class="mn">Height Milestone</div><div class="md">Grew 2 cm in 60 days — 104 → 106 cm. On track for 50th percentile.</div><div class="mbar"><div class="mbf bg-p" style="width:68%"></div></div></div>
      <div class="mc"><div class="mi">🍽️</div><div class="mn">New Foods Tried</div><div class="md">7 new foods accepted this quarter. Target: 9 foods by end of term.</div><div class="mbar"><div class="mbf bg-o" style="width:78%"></div></div></div>
      <div class="mc"><div class="mi">💪</div><div class="mn">Immunity Streak</div><div class="md">Sick episodes reduced: 4 → 1 this quarter. Immunity score: 87.</div><div class="mbar"><div class="mbf bg-r" style="width:87%"></div></div></div>
    </div>
  </div>

  <!-- Food tab -->
  <div id="tFood" class="tp">
    <div class="fl">
      <div class="fc"><div class="fl-l"><div class="fi" style="background:#d0fff2">🥦</div><div><div class="fn">Broccoli</div><div class="fd">Introduced May 12 · Accepted ✓</div></div></div><span class="fs nw">New</span></div>
      <div class="fc"><div class="fl-l"><div class="fi" style="background:#fff0dc">🥕</div><div><div class="fn">Carrots (cooked)</div><div class="fd">Regular since March · 3× / week</div></div></div><span class="fs rg">Regular</span></div>
      <div class="fc"><div class="fl-l"><div class="fi" style="background:#fde8e8">🍅</div><div><div class="fn">Tomatoes (raw)</div><div class="fd">Tried May 20 · Rejected this time</div></div></div><span class="fs rj">Rejected</span></div>
      <div class="fc"><div class="fl-l"><div class="fi" style="background:#fffbe0">🌽</div><div><div class="fn">Sweet Corn</div><div class="fd">Introduced Apr 28 · Accepted ✓</div></div></div><span class="fs nw">New</span></div>
      <div class="fc"><div class="fl-l"><div class="fi" style="background:#ede9fe">🫘</div><div><div class="fn">Rajma (Kidney Beans)</div><div class="fd">Regular since Feb · 4× / week</div></div></div><span class="fs rg">Regular</span></div>
    </div>
  </div>

  <!-- Season tab -->
  <div id="tSeason" class="tp">
    <div class="sb"><div class="se">🌞</div><div><div class="st">Greeshma Season — May to June</div><div class="ss">Hot &amp; dry. Focus on hydration, cooling foods, and iron absorption.</div></div></div>
    <div class="scs">
      <div class="sc"><div class="sd g"></div><div><div class="sct">Hydration Priority</div><div class="scd">Offer coconut water, buttermilk (chaas), and fresh lime water 3× daily. Target: 1.2L fluids/day.</div></div></div>
      <div class="sc"><div class="sd o"></div><div><div class="sct">Cooling Foods to Include</div><div class="scd">Cucumber, watermelon, curd, sattu — add to lunch &amp; dinner. Avoid spicy food in afternoon heat.</div></div></div>
      <div class="sc"><div class="sd p"></div><div><div class="sct">Vitamin D Window</div><div class="scd">Safe sun: 7–8 AM only (15 min). Always pair D3 gummy with ghee-based breakfast.</div></div></div>
      <div class="sc"><div class="sd g"></div><div><div class="sct">Iron Absorption Boost</div><div class="scd">Pair rajma &amp; palak with amla juice or lemon. Raw mango (kachha aam) = natural Vit C source.</div></div></div>
    </div>
  </div>

</main>
</div>

<!-- ═══ VIEW: EDIT/ONBOARDING ═══ -->
<div class="view" id="vEdit">
<main class="ob-shell">

  <!-- STEP 1 -->
  <div class="os on" id="s1">
    <div class="osb">
      <span class="ob-badge">Step 1 of 5</span>
      <div class="ob-lines"><div class="ol active"></div><div class="ol"></div><div class="ol"></div><div class="ol"></div><div class="ol"></div></div>
    </div>
    <h1 class="oh1">Tell us about your child</h1>
    <p class="osub">We'll personalise everything to their exact profile</p>
    <div class="sec-err" id="e1">⚠️ Please fill in all required fields before continuing.</div>

    <div class="gr-row">
      <button class="gbtn sel" data-g="boy" onclick="selG(this)"><span>👦</span><span class="gl">Boy</span></button>
      <button class="gbtn" data-g="girl" onclick="selG(this)"><span>👧</span><span class="gl">Girl</span></button>
      <button class="gbtn" data-g="other" onclick="selG(this)"><span>🧒</span><span class="gl">Other</span></button>
    </div>

    <div class="ff">
      <label class="fl-label">Child's Name <span class="req">*</span></label>
      <input class="nb-i" id="oName" type="text" placeholder="e.g. Arjun" value="Arjun" oninput="clrE(this,'eName')"/>
      <div class="emsg" id="eName">⚠ Name is required.</div>
    </div>

    <div class="fr">
      <div class="fc2">
        <label class="fl-label">Age <span class="req">*</span></label>
        <div class="sw"><select class="nb-s" id="oAge" onchange="autoPhase();clrE(this,'eAge')">
          <option value="">Select age</option>
          <option value="4">4 years</option><option value="5" selected>5 years</option>
          <option value="6">6 years</option><option value="7">7 years</option>
          <option value="8">8 years</option><option value="9">9 years</option>
          <option value="10">10 years</option><option value="11">11 years</option>
          <option value="12">12 years</option><option value="13">13 years</option>
          <option value="14">14 years</option><option value="15">15 years</option>
          <option value="16">16 years</option><option value="17">17 years</option>
        </select></div>
        <div class="emsg" id="eAge">⚠ Please select an age.</div>
      </div>
      <div class="fc2">
        <label class="fl-label">Birth Month</label>
        <div class="sw"><select class="nb-s" id="oMonth">
          <option>January</option><option>February</option><option>March</option>
          <option>April</option><option>May</option><option>June</option>
          <option>July</option><option>August</option><option>September</option>
          <option>October</option><option>November</option><option selected>December</option>
        </select></div>
      </div>
    </div>

    <div class="fr">
      <div class="fc2">
        <label class="fl-label">Height (cm) <span class="req">*</span></label>
        <input class="nb-i" id="oHeight" type="number" placeholder="106" value="106" min="50" max="220" oninput="clrE(this,'eHeight')"/>
        <div class="emsg" id="eHeight">⚠ Enter valid height (50–220 cm).</div>
      </div>
      <div class="fc2">
        <label class="fl-label">Weight (kg) <span class="req">*</span></label>
        <input class="nb-i" id="oWeight" type="number" placeholder="18" value="18" min="5" max="150" oninput="clrE(this,'eWeight')"/>
        <div class="emsg" id="eWeight">⚠ Enter valid weight (5–150 kg).</div>
      </div>
    </div>

    <div class="fr">
      <div class="fc2">
        <label class="fl-label">City</label>
        <input class="nb-i" id="oCity" type="text" placeholder="Ahmedabad" value="Ahmedabad"/>
      </div>
      <div class="fc2">
        <label class="fl-label">Region <span class="req">*</span></label>
        <div class="sw"><select class="nb-s" id="oRegion" onchange="clrE(this,'eRegion')">
          <option value="">Select region</option>
          <option selected>Gujarat / West India</option>
          <option>Maharashtra / West India</option>
          <option>Punjab / North India</option>
          <option>Tamil Nadu / South India</option>
          <option>Karnataka / South India</option>
          <option>West Bengal / East India</option>
          <option>Rajasthan / North India</option>
          <option>Uttar Pradesh / North India</option>
          <option>Madhya Pradesh / Central India</option>
        </select></div>
        <div class="emsg" id="eRegion">⚠ Please select a region.</div>
      </div>
    </div>

    <div class="ppr">
      <button class="pp sel" data-p="1" onclick="selP(this)">Phase 1<span>4–6 yrs</span></button>
      <button class="pp" data-p="2" onclick="selP(this)">Phase 2<span>7–9 yrs</span></button>
      <button class="pp" data-p="3" onclick="selP(this)">Phase 3<span>10–12 yrs</span></button>
      <button class="pp" data-p="4" onclick="selP(this)">Phase 4<span>13–15 yrs</span></button>
      <button class="pp" data-p="5" onclick="selP(this)">Phase 5<span>16–17 yrs</span></button>
    </div>
    <div class="pnote" id="pNote"><strong>Phase 1 — Early childhood (4–6 yrs)</strong><br>Calcium bone foundation · Iron for brain · Probiotics for gut immunity · Soft textures, mild flavours</div>

    <div class="step-actions">
      <button class="btn-co" onclick="goTo(2)">Continue <span class="arr">→</span></button>
      <button class="btn-secondary" type="button" onclick="backToProfile()">← Back to Profile</button>
    </div>
  </div>

  <!-- STEP 2 -->
  <div class="os" id="s2">
    <div class="osb">
      <button class="btn-back" onclick="goTo(1)">← Back</button>
      <span class="ob-badge">Step 2 of 5</span>
      <div class="ob-lines" style="flex:1"><div class="ol done"></div><div class="ol active"></div><div class="ol"></div><div class="ol"></div><div class="ol"></div></div>
    </div>
    <h1 class="oh1">What worries you most?</h1>
    <p class="osub">Select all that apply</p>
    <div class="sec-err" id="e2">⚠️ Please select at least one concern to continue.</div>
    <div class="ccl">
      <div class="cc sel" onclick="togCC(this)"><span class="ci">🤧</span><div><div class="cn">Gets sick too often</div><div class="cde">Frequent colds, cough, fever — low immunity</div></div></div>
      <div class="cc" onclick="togCC(this)"><span class="ci">🧠</span><div><div class="cn">Focus &amp; memory issues</div><div class="cde">Difficulty concentrating, poor academics</div></div></div>
      <div class="cc sel" onclick="togCC(this)"><span class="ci">📏</span><div><div class="cn">Height / weight concerns</div><div class="cde">Not growing as expected for age</div></div></div>
      <div class="cc" onclick="togCC(this)"><span class="ci">🫁</span><div><div class="cn">Digestive issues</div><div class="cde">Constipation, bloating, irregular stomach</div></div></div>
      <div class="cc" onclick="togCC(this)"><span class="ci">⚡</span><div><div class="cn">Low energy / always tired</div><div class="cde">Fatigue, lacks enthusiasm</div></div></div>
      <div class="cc" onclick="togCC(this)"><span class="ci">✨</span><div><div class="cn">Skin &amp; hair issues</div><div class="cde">Dry skin, rashes, eczema</div></div></div>
      <div class="cc" onclick="togCC(this)"><span class="ci">😴</span><div><div class="cn">Sleep problems</div><div class="cde">Trouble falling or staying asleep</div></div></div>
    </div>
    <div class="step-actions">
      <button class="btn-co" onclick="goTo(3)">Continue <span class="arr">→</span></button>
      <button class="btn-secondary" type="button" onclick="backToProfile()">← Back to Profile</button>
    </div>
  </div>

  <!-- STEP 3 -->
  <div class="os" id="s3">
    <div class="osb">
      <button class="btn-back" onclick="goTo(2)">← Back</button>
      <span class="ob-badge">Step 3 of 5</span>
      <div class="ob-lines" style="flex:1"><div class="ol done"></div><div class="ol done"></div><div class="ol active"></div><div class="ol"></div><div class="ol"></div></div>
    </div>
    <h1 class="oh1">What does <span id="nd3">Arjun</span> eat?</h1>
    <div class="sec-err" id="e3">⚠️ Please select a diet type and picky level.</div>
    <span class="sec-l">Diet Type <span class="req">*</span></span>
    <div class="dg">
      <div class="dc sel" onclick="selO(this,'.dc','sel')"><span>🌱</span><span class="dn2">Pure vegetarian</span></div>
      <div class="dc" onclick="selO(this,'.dc','sel')"><span>🥚</span><span class="dn2">Veg + eggs</span></div>
      <div class="dc" onclick="selO(this,'.dc','sel')"><span>🍗</span><span class="dn2">Non-vegetarian</span></div>
      <div class="dc" onclick="selO(this,'.dc','sel')"><span>🙏</span><span class="dn2">Jain diet</span></div>
    </div>
    <span class="sec-l">Picky Level <span class="req">*</span></span>
    <div class="pl">
      <div class="pc" onclick="selO(this,'.pc','sel')"><span>😋</span><span class="pn">Eats most things</span></div>
      <div class="pc sel" onclick="selO(this,'.pc','sel')"><span>🤔</span><span class="pn">Slightly picky</span></div>
      <div class="pc" onclick="selO(this,'.pc','sel')"><span>😤</span><span class="pn">Very picky eater</span></div>
    </div>
    <span class="sec-l">Allergies</span>
    <div class="pt">
      <button class="ptl sel" onclick="togP(this)">None</button>
      <button class="ptl" onclick="togP(this)">Dairy</button>
      <button class="ptl" onclick="togP(this)">Gluten</button>
      <button class="ptl" onclick="togP(this)">Nuts</button>
      <button class="ptl" onclick="togP(this)">Soy</button>
      <button class="ptl" onclick="togP(this)">Eggs</button>
    </div>
    <span class="sec-l">Home Cooking Frequency</span>
    <div class="ff"><div class="sw"><select class="nb-s" id="oCook">
      <option selected>Always (100%)</option>
      <option>Almost always (90%+)</option>
      <option>Mostly (70–89%)</option>
      <option>Sometimes (50–69%)</option>
      <option>Rarely (less than 50%)</option>
    </select></div></div>
    <div class="step-actions">
      <button class="btn-co" onclick="goTo(4)">Continue <span class="arr">→</span></button>
      <button class="btn-secondary" type="button" onclick="backToProfile()">← Back to Profile</button>
    </div>
  </div>

  <!-- STEP 4 -->
  <div class="os" id="s4">
    <div class="osb">
      <button class="btn-back" onclick="goTo(3)">← Back</button>
      <span class="ob-badge">Step 4 of 5</span>
      <div class="ob-lines" style="flex:1"><div class="ol done"></div><div class="ol done"></div><div class="ol done"></div><div class="ol active"></div><div class="ol"></div></div>
    </div>
    <h1 class="oh1"><span id="nd4">Arjun</span>'s Daily Lifestyle</h1>
    <div class="sec-err" id="e4">⚠️ Please select an activity level.</div>
    <span class="sec-l">Activity Level <span class="req">*</span></span>
    <div class="ll">
      <div class="lc" onclick="selO(this,'.lc','sel')"><span>🏃</span><div><div class="lcn">Very active</div><div class="lcd">Outdoor sports, runs around a lot</div></div></div>
      <div class="lc sel" onclick="selO(this,'.lc','sel')"><span>🚶</span><div><div class="lcn">Moderately active</div><div class="lcd">Some play, PE at school</div></div></div>
      <div class="lc" onclick="selO(this,'.lc','sel')"><span>📱</span><div><div class="lcn">Mostly sedentary</div><div class="lcd">Screen time, indoor activities</div></div></div>
    </div>
    <span class="sec-l">Current Supplements</span>
    <div class="pt" id="supplRow">
      <button class="ptl sel" onclick="togP(this)">None</button>
      <button class="ptl" onclick="togP(this)">Vitamin D</button>
      <button class="ptl" onclick="togP(this)">Iron</button>
      <button class="ptl" onclick="togP(this)">Calcium</button>
      <button class="ptl" onclick="togP(this)">Chyawanprash</button>
    </div>
    <span class="sec-l">Upcoming Events</span>
    <div class="pt">
      <button class="ptl sel" onclick="togP(this)">Nothing specific</button>
      <button class="ptl" onclick="togP(this)">Exams in 2–3 months</button>
      <button class="ptl" onclick="togP(this)">Sports season</button>
      <button class="ptl" onclick="togP(this)">Illness recovery</button>
    </div>
    <div class="fr" style="margin-top:4px">
      <div class="fc2"><label class="fl-label">School Timing</label><div class="sw"><select class="nb-s"><option>Regular (9–3pm)</option><option>Morning (7–1pm)</option><option>Afternoon (12–6pm)</option><option>Home schooled</option></select></div></div>
      <div class="fc2"><label class="fl-label">Sun Exposure</label><div class="sw"><select class="nb-s"><option>Occasional outdoor</option><option>Daily 30+ min outdoor</option><option>Mostly indoors</option><option>School outdoor only</option></select></div></div>
    </div>
    <div class="step-actions">
      <button class="btn-co" onclick="goTo(5)" style="margin-top:16px">Continue <span class="arr">→</span></button>
      <button class="btn-secondary" type="button" onclick="backToProfile()">← Back to Profile</button>
    </div>
  </div>

  <!-- STEP 5 -->
  <div class="os" id="s5">
    <div class="osb">
      <button class="btn-back" onclick="goTo(4)">← Back</button>
      <span class="ob-badge">Step 5 of 5</span>
      <div class="ob-lines" style="flex:1"><div class="ol done"></div><div class="ol done"></div><div class="ol done"></div><div class="ol done"></div><div class="ol active"></div></div>
    </div>
    <h1 class="oh1">3 months from now…</h1>
    <p class="osub">What does success look like for <span id="nd5">Arjun</span>?</p>
    <div class="sec-err" id="e5">⚠️ Please select at least one goal.</div>
    <div class="gcl">
      <div class="gc sel" onclick="togG(this)"><span class="gci">🛡️</span><div><div class="gcn">Falls sick less often</div><div class="gcd">Build strong lasting immunity</div></div></div>
      <div class="gc" onclick="togG(this)"><span class="gci">🧠</span><div><div class="gcn">Better focus and school performance</div><div class="gcd">Sharper memory, calm sustained energy</div></div></div>
      <div class="gc" onclick="togG(this)"><span class="gci">📏</span><div><div class="gcn">Healthy weight gain and growth</div><div class="gcd">Reach target height, stronger bones</div></div></div>
      <div class="gc" onclick="togG(this)"><span class="gci">⚡</span><div><div class="gcn">More energy and stamina</div><div class="gcd">Active, enthusiastic, less tired</div></div></div>
      <div class="gc" onclick="togG(this)"><span class="gci">🌿</span><div><div class="gcn">Balanced overall nutrition</div><div class="gcd">Fill all gaps, build lifelong food foundation</div></div></div>
    </div>
    <div class="gnote">
      <div class="gnote-h">🌿 What NutriBuddy will generate</div>
      <div class="gnote-b">A 7-day meal plan tailored to <span id="nd5b">Arjun</span>'s region, season, age, and concerns — with Ayurvedic wisdom, step-by-step recipes, supplement schedule, and expert nutritionist chat.</div>
    </div>
    <div class="step-actions">
      <button class="btn-co or" id="genBtn" onclick="goTo(6)">Generate <span id="nd5c">Arjun</span>'s Plan <span class="arr">→</span></button>
      <button class="btn-secondary" type="button" onclick="backToProfile()">← Back to Profile</button>
    </div>
  </div>

  <!-- STEP 6 -->
  <div class="os" id="s6">
    <div class="ph2">
      <div class="ph2-em">🎉</div>
      <div class="ph2-t"><span id="nd6">Arjun</span>'s plan is ready!</div>
      <div class="ph2-s">3 concerns detected — immunity, growth &amp; focus mapped to Ayurvedic foods</div>
      <div class="ph2-tags">
        <span class="ph2-tag">🛡️ Immunity</span><span class="ph2-tag">📏 Growth</span>
        <span class="ph2-tag">🌿 Ayurvedic</span><span class="ph2-tag">💬 Expert Chat</span>
      </div>
    </div>
    <div class="rda-row">
      <div class="rda-c"><div class="rda-v cg" id="rda1">0%</div><div class="rda-l">Immunity RDA</div></div>
      <div class="rda-c"><div class="rda-v co" id="rda2">0%</div><div class="rda-l">Growth RDA</div></div>
      <div class="rda-c"><div class="rda-v cp" id="rda3">0%</div><div class="rda-l">Brain RDA</div></div>
    </div>
    <div class="plan-unlock">Unlock Your Full Plan</div>
    <div class="pgrid">
      <div class="pcard">
        <div class="pname">Seedling</div>
        <div class="pprice">₹199<span class="pprice-sm"> /mo</span></div>
        <ul class="pfeat"><li>7-day meal plan</li><li>50+ recipes</li><li>Tiffin planner</li><li>Forum access</li></ul>
        <button class="btn-pl out" onclick="pickPlan(this,'Seedling')">Choose Seedling</button>
      </div>
      <div class="pcard best">
        <div class="pbest">Best for <span class="cname">Arjun</span></div>
        <div class="pname">Champion</div>
        <div class="pprice">₹499<span class="pprice-sm"> /mo</span></div>
        <ul class="pfeat"><li>Everything in Seedling</li><li>30 expert chat msgs/mo</li><li>Nutritionist Q&amp;A</li><li>Community leaderboard</li><li>Health score dashboard</li></ul>
        <button class="btn-pl sol" onclick="pickPlan(this,'Champion')">Start 7-day trial →</button>
      </div>
      <div class="pcard">
        <div class="pname">SuperKid</div>
        <div class="pprice">₹999<span class="pprice-sm"> /mo</span></div>
        <ul class="pfeat"><li>Unlimited expert chat</li><li>1:1 video nutritionist</li><li>Growth tracking</li><li>Leaderboard boosts</li><li>15% product discount</li></ul>
        <button class="btn-pl out" onclick="pickPlan(this,'SuperKid')">Choose SuperKid</button>
      </div>
    </div>
    <div class="pnote2">Cancel anytime · 7-day free trial · No card required</div>
    <button class="btn-sp" onclick="saveReturn()">✓ Save &amp; View Updated Profile →</button>
  </div>

</main>
</div>

<div class="toast" id="toast"></div>
  @push('scripts')
<script>
/* ── VIEW SWITCHING ── */
function openEdit(){
  document.getElementById('vProfile').classList.remove('on');
  document.getElementById('vEdit').classList.add('on');
  goTo(1); window.scrollTo({top:0,behavior:'smooth'});
}
function backToProfile(){
  document.getElementById('vEdit').classList.remove('on');
  document.getElementById('vProfile').classList.add('on');
  window.scrollTo({top:0,behavior:'smooth'});
}

/* ── VALIDATION ── */
function v1(){
  let ok=true;
  const n=document.getElementById('oName').value.trim();
  if(!n){markE('oName','eName');ok=false;}else clrE(document.getElementById('oName'),'eName');
  const a=document.getElementById('oAge').value;
  if(!a){markE('oAge','eAge');ok=false;}else clrE(document.getElementById('oAge'),'eAge');
  const h=parseFloat(document.getElementById('oHeight').value);
  if(!h||h<50||h>220){markE('oHeight','eHeight');ok=false;}else clrE(document.getElementById('oHeight'),'eHeight');
  const w=parseFloat(document.getElementById('oWeight').value);
  if(!w||w<5||w>150){markE('oWeight','eWeight');ok=false;}else clrE(document.getElementById('oWeight'),'eWeight');
  const r=document.getElementById('oRegion').value;
  if(!r){markE('oRegion','eRegion');ok=false;}else clrE(document.getElementById('oRegion'),'eRegion');
  document.getElementById('e1').classList.toggle('on',!ok);
  if(!ok)document.getElementById('e1').scrollIntoView({behavior:'smooth',block:'nearest'});
  return ok;
}
function v2(){const ok=!!document.querySelector('#s2 .cc.sel');document.getElementById('e2').classList.toggle('on',!ok);return ok;}
function v3(){const ok=!!document.querySelector('.dc.sel')&&!!document.querySelector('.pc.sel');document.getElementById('e3').classList.toggle('on',!ok);return ok;}
function v4(){const ok=!!document.querySelector('.lc.sel');document.getElementById('e4').classList.toggle('on',!ok);return ok;}
function v5(){const ok=!!document.querySelector('#s5 .gc.sel');document.getElementById('e5').classList.toggle('on',!ok);return ok;}
function markE(iId,eId){document.getElementById(iId).classList.add('err');document.getElementById(eId).classList.add('on');}
function clrE(el,eId){if(el.classList)el.classList.remove('err');const e=document.getElementById(eId);if(e)e.classList.remove('on');}

/* ── STEP NAV ── */
const vmap={1:v1,2:v2,3:v3,4:v4,5:v5};
function goTo(n){
  const cur=parseInt(document.querySelector('.os.on')?.id.slice(1));
  if(n>cur&&vmap[cur]&&!vmap[cur]())return;
  const name=(document.getElementById('oName').value.trim())||'your child';
  ['nd3','nd4','nd5','nd5b','nd5c','nd6'].forEach(id=>{const el=document.getElementById(id);if(el)el.textContent=name;});
  document.querySelectorAll('.cname').forEach(el=>el.textContent=name);
  document.getElementById('genBtn').innerHTML=`Generate ${name}'s Plan <span class="arr">→</span>`;
  document.querySelectorAll('.os').forEach(s=>s.classList.remove('on'));
  const next=document.getElementById('s'+n);
  if(next){next.classList.add('on');window.scrollTo({top:0,behavior:'smooth'});}
  if(n===6){setTimeout(()=>{cu('rda1',87,'%',1200);cu('rda2',79,'%',1400);cu('rda3',72,'%',1100);},350);}
}

/* ── SAVE & RETURN ── */
function saveReturn(){
  const name=(document.getElementById('oName').value.trim())||'Arjun';
  const age=document.getElementById('oAge').value||'5';
  const h=document.getElementById('oHeight').value||'106';
  const w=document.getElementById('oWeight').value||'18';
  const region=document.getElementById('oRegion').value||'West India (Gujarat)';
  const g=document.querySelector('.gbtn.sel')?.dataset.g||'boy';
  const gl={boy:'Boy',girl:'Girl',other:'Other'}[g];
  const phase=document.querySelector('.pp.sel')?.dataset.p||'1';
  const pm={'1':'Phase 1 — 4–6 yrs','2':'Phase 2 — 7–9 yrs','3':'Phase 3 — 10–12 yrs','4':'Phase 4 — 13–15 yrs','5':'Phase 5 — 16–17 yrs'};
  const diet=document.querySelector('.dc.sel .dn2')?.textContent||'Pure vegetarian';
  const picky=document.querySelector('.pc.sel .pn')?.textContent||'Slightly picky';
  const act=document.querySelector('.lc.sel .lcn')?.textContent||'Moderately active';
  const alg=[...document.querySelectorAll('#s3 .pt .ptl.sel')].map(p=>p.textContent);
  const allergies=alg.length?alg.join(', '):'None';

  document.getElementById('profTitle').textContent=name+"'s Profile";
  document.getElementById('profMeta').innerHTML=`${age} years <span class="ph-meta-dot">·</span> ${gl} <span class="ph-meta-dot">·</span> ${diet} <span class="ph-meta-dot">·</span> ${region}`;
  document.getElementById('ptPhase').textContent=pm[phase];
  document.getElementById('ptDiet').textContent=diet;
  document.getElementById('ptPicky').textContent=picky;
  document.getElementById('ptAllergies').textContent=allergies;
  document.getElementById('ptActivity').textContent=act;
  document.getElementById('ptRegion').textContent=region;
  document.getElementById('heightTile').innerHTML=h+' <span class="tu">cm</span>';
  document.getElementById('weightTile').innerHTML=w+' <span class="tu">kg</span>';

  backToProfile();
  const sv=document.getElementById('scoreVal');sv.textContent='0';
  setTimeout(()=>cu('scoreVal',75,'',1300),400);
  showToast('✓ '+name+"'s profile updated successfully!");
}

/* ── PROFILE HELPERS ── */
function switchTab(btn,id){
  document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('on'));
  document.querySelectorAll('.tp').forEach(p=>p.classList.remove('on'));
  btn.classList.add('on');
  const p=document.getElementById(id);p.classList.add('on');
  p.style.animation='none';p.offsetHeight;p.style.animation='fu .36s ease both';
}
function doCheckin(){
  const b=document.getElementById('ciBtn');
  b.innerHTML='⏳ Initiating check-in…';b.style.background='var(--pu)';b.disabled=true;
  setTimeout(()=>{b.innerHTML='✓ Check-in Complete! Scores updated';b.style.background='var(--g)';
  setTimeout(()=>{b.innerHTML='Quarterly Check-in <span class="arr">→</span>';b.style.background='';b.disabled=false;},2800);},1400);
}

/* ── ONBOARDING HELPERS ── */
const pDesc={'1':'<strong>Phase 1 — Early childhood (4–6 yrs)</strong><br>Calcium bone foundation · Iron for brain · Probiotics for gut immunity · Soft textures, mild flavours','2':'<strong>Phase 2 — Middle childhood (7–9 yrs)</strong><br>Omega-3 for brain growth · Zinc for immunity · Complex carbs for energy · Variety building','3':'<strong>Phase 3 — Late childhood (10–12 yrs)</strong><br>Iron for girls · Protein for growth spurts · Calcium peak window · Sports nutrition','4':'<strong>Phase 4 — Early teen (13–15 yrs)</strong><br>Hormonal support · B-vitamins for energy · Protein for muscle · Iron replacement','5':'<strong>Phase 5 — Late teen (16–17 yrs)</strong><br>Bone density final window · Stress & focus support · Heart health foundations'};
function selG(b){document.querySelectorAll('.gbtn').forEach(x=>x.classList.remove('sel'));b.classList.add('sel');}
function selP(b){document.querySelectorAll('.pp').forEach(x=>x.classList.remove('sel'));b.classList.add('sel');document.getElementById('pNote').innerHTML=pDesc[b.dataset.p]||'';}
function autoPhase(){const a=parseInt(document.getElementById('oAge').value);const p=a<=6?'1':a<=9?'2':a<=12?'3':a<=15?'4':'5';const pill=document.querySelector(`.pp[data-p="${p}"]`);if(pill)selP(pill);}
function selO(el,sel,cls){document.querySelectorAll(sel).forEach(c=>c.classList.remove(cls));el.classList.add(cls);}
function togCC(el){el.classList.toggle('sel');}
function togG(el){el.classList.toggle('sel');}
function togP(el){el.classList.toggle('sel');}
function pickPlan(b,plan){b.textContent='✓ Selected!';b.style.background='var(--g)';b.style.color='#fff';b.style.borderColor='var(--g)';setTimeout(()=>{b.textContent=plan==='Champion'?'Start 7-day trial →':'Choose '+plan;b.style.background='';b.style.color='';b.style.borderColor='';},2000);}

/* ── UTILS ── */
function cu(id,target,suffix,dur){const el=document.getElementById(id);if(!el)return;let c=0;const step=target/(dur/16);const t=()=>{c=Math.min(c+step,target);el.textContent=Math.round(c)+suffix;if(c<target)requestAnimationFrame(t);};requestAnimationFrame(t);}
function showToast(msg){const t=document.getElementById('toast');t.textContent=msg;t.classList.add('on');setTimeout(()=>t.classList.remove('on'),3400);}

window.addEventListener('DOMContentLoaded',()=>{setTimeout(()=>cu('scoreVal',75,'',1300),300);});
</script>




    @endpush
@endsection
