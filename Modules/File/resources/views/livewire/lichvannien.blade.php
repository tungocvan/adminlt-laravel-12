<div class="p-3">

    <table id="header_lich" class="table table-bordered text-center mb-3"></table>
    
    <div class="text-center mb-2">
        <button wire:click="changeMonth(-1)" class="btn btn-success btn-sm">◀</button>
        <span class="font-weight-bold mx-2">Th {{ $month }} - {{ $year }}</span>
        <button wire:click="changeMonth(1)" class="btn btn-success btn-sm">▶</button>
    </div>
    
    <table class="table table-bordered text-center">
        <thead class="bg-success text-white">
            <tr><th>CN</th><th>T2</th><th>T3</th><th>T4</th><th>T5</th><th>T6</th><th>T7</th></tr>
        </thead>
        <tbody>
            @foreach($calendar as $week)
            <tr>
                @foreach($week as $d)
                    @if($d)
                    <td
                        onclick="@this.call('selectDay', {{ $d }});"
                        id="d{{ $d }}"
                        style="cursor:pointer">
                        <div>{{ $d }}</div>
                        <small id="al{{ $d }}"></small>
                    </td>
                    @else <td></td> @endif
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
    
    {{-- ---- JS Lịch Âm ---- --}}
    <script src="/js/lichvannien/amlich.js"></script>
    <script src="/js/lichvannien/canchi.js"></script>
    <script src="/js/lichvannien/napam.js"></script>
    <script src="/js/lichvannien/linhtinh.js"></script>
    <script src="/js/lichvannien/catnhat.js"></script>
    <script src="/js/lichvannien/hungnhat.js"></script>
    <script src="/js/lichvannien/thoithan2.js"></script>
    
    <script>
     // ==== Luôn chạy sau mọi render Livewire ====
    document.addEventListener("livewire:navigated", initCalendar);
    document.addEventListener("livewire:initialized", initCalendar);
    Livewire.hook('message.processed', initCalendar);

    function initCalendar(){
        setTimeout(()=>{
            renderHeader({{ $day }},{{ $month }},{{ $year }});
            fillAm();
            highlight({{ $day }});
        },50);
    }
    
    
    function highlight(d){
        document.querySelectorAll("td[id^='d']").forEach(e=>e.style.background="");
        let el=document.getElementById("d"+d);
        if(el){el.style.background="#28a745"; el.style.color="white";}
    }
    
    function fillAm(){
        for(let d=1;d<=31;d++){
            let el=document.getElementById("al"+d); if(!el) continue;
            try{let am=new LunarDate(d,{{ $month }},{{ $year }}); el.innerText=(am.day+"/"+am.month);}catch{}
        }
    }
    
    function renderHeader(d,m,y){
    try{
        let L=new LunarDate(d,m,y), jd=jdn(d,m,y);
        const TUAN=['CN','T2','T3','T4','T5','T6','T7'];
        const TH=['Một','Hai','Ba','Bốn','Năm','Sáu','Bảy','Tám','Chín','Mười','Mười một','Mười hai'];
        const TIET=['Xuân phân','Thanh minh','Cốc vũ','Lập hạ','Tiểu mãn','Mang chủng','Hạ chí','Tiểu thử','Đại thử','Lập thu','Xử thử','Bạch lộ','Thu phân','Hàn lộ','Sương giáng','Lập đông','Tiểu tuyết','Đại tuyết','Đông chí','Tiểu hàn','Đại hàn','Lập xuân','Vũ thủy','Kinh trập'];
    
        let cY=CanChi(L.year-1900+36);
        let cM=CanChi((y-1900)*12+(m-1)+12); if(d>=L.t) cM=CanChi((y-1900)*12+(m-1)+13);
        let cD=CanChi(L.day);
        let h=getHolodayString(d,m,L.day,L.month);
        let tiet=TIET[getSunLongitude(jd+1,7.0)];
    
        document.getElementById("header_lich").innerHTML=`
        <tr><td colspan=7><b>Tháng ${m} / ${y}</b></td></tr>
        <tr><td colspan=7 style="font-size:28px">${d}<div>${TUAN[(jd+1)%7]}</div></td></tr>
        <tr>
            <td colspan=3><b>Tháng ${TH[L.month-1]}</b><br><span style="font-size:22px;color:red">${L.day}</span><br><b>${cY}</b></td>
            <td colspan=4>Ngày <b>${cD}</b><br>Tháng <b>${cM}</b><br>Tiết <b>${tiet}</b><br>Hoàng đạo: ${getGioHoangDao(jd)}</td>
        </tr>${h?`<tr><td colspan=7 style="background:#dc3545;color:white">${h}</td></tr>`:""}`;
    }catch(e){console.log(e);}
    }
    let day     = @entangle('selectedDay');
let month   = @entangle('month');
let year    = @entangle('year');

// chạy sau mỗi lần Livewire update UI
Livewire.hook('message.processed', () => {
    setTimeout(()=>{
        renderHeader(day, month, year);
        fillAm(month, year);
        highlight(day);
    },30);
});

// chạy lần đầu
document.addEventListener('DOMContentLoaded',()=>{
    renderHeader(day,month,year);
    fillAm(month,year);
    highlight(day);
});

    </script>
    </div>
    