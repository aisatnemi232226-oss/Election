@extends('layouts.admin')

@section('content')
    <main class="container mx-auto p-4">
        <section class="text-center my-8">
            <h2 class="text-4xl font-bold text-gray-800 mb-4">Admin Dashboard</h2>
            <p class="text-gray-600 text-lg">Welcome{{ auth()->check() ? ', ' . auth()->user()->name : '' }} to Philippine Christian University Online Election System admin dashboard. Here you can manage elections, candidates, voters, and view reports.</p>
        </section>
        
        <!-- Floating Widget: Live Time + Election Countdown (draggable & resizable) -->
        <div id="floating-widget" class="bg-white rounded-lg shadow-lg" style="position:fixed;left:20px;top:80px;width:420px;min-width:240px;min-height:110px;z-index:9999;overflow:hidden;max-width:90vw;max-height:80vh;">
            <div class="fw-header p-4 border-b cursor-move bg-gray-50 rounded-t-lg">
                <div class="flex items-center justify-between">
                    <strong class="text-gray-700">Dashboard Widget</strong>
                    <button id="fw-close" class="text-gray-500 hover:text-gray-800">✕</button>
                </div>
            </div>
            <div class="p-4 grid grid-cols-2 gap-4">
                <div class="p-4 bg-white rounded-md border">
                    <div class="text-xs text-gray-500">Live Time</div>
                    <div id="fw-live-time" class="text-xl font-bold text-gray-800 mt-1">--:--:--</div>
                    <div id="fw-live-date" class="text-sm text-gray-500">----</div>
                </div>
                <div class="p-4 bg-white rounded-md border">
                    <div class="text-xs text-gray-500">Election Countdown</div>
                    <select id="fw-election-select" class="w-full mt-2 p-2 border rounded">
                        <option>Vice-President</option>
                        <option>President</option>
                        <option>Secretary</option>
                    </select>
                    <div class="mt-3 font-bold text-gray-800" id="fw-countdown">Ends in<br><span id="fw-countdown-timer">00d 00:00:00</span></div>
                </div>
            </div>
            <div class="fw-resize-handle" title="Resize" style="position:absolute;right:8px;bottom:8px;width:18px;height:18px;cursor:se-resize;border-radius:4px;display:flex;align-items:center;justify-content:center;color:rgba(0,0,0,0.35);">▾</div>
        </div>

        <!-- Minimized alarm icon -->
        <div id="fw-minimized" title="Show dashboard widget" style="position:fixed;left:20px;bottom:20px;width:48px;height:48px;display:none;align-items:center;justify-content:center;border-radius:9999px;background:#ffffff;border:1px solid rgba(0,0,0,0.06);box-shadow:0 8px 20px rgba(2,6,23,0.08);cursor:pointer;z-index:9999;font-size:20px;">⏰</div>

        
        <section class="my-8 grid grid-cols-1 md:grid-cols-4 gap-6">
            <a href="{{ route('admin.elections.index') }}" class="stat-card block bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-shadow" data-accent="blue" aria-label="Total Elections">
                <span class="accent-strip" aria-hidden="true"></span>
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-1">Total Elections</h3>
                        <div class="text-sm text-gray-500">Overview</div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="stat-icon" aria-hidden="true"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3 13h18v2H3z" fill="currentColor" opacity="0.9"/><path d="M7 7h10v2H7z" fill="currentColor" opacity="0.9"/></svg></span>
                        <p class="stat-value text-4xl font-bold text-blue-600" data-target="{{ $totalElections ?? 0 }}" aria-live="polite">0</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.candidates.index') }}" class="stat-card block bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-shadow" data-accent="green" aria-label="Total Candidates">
                <span class="accent-strip" aria-hidden="true"></span>
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-1">Total Candidates</h3>
                        <div class="text-sm text-gray-500">Registered</div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="stat-icon" aria-hidden="true"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 12a4 4 0 100-8 4 4 0 000 8z" fill="currentColor"/><path d="M4 20a8 8 0 0116 0H4z" fill="currentColor" opacity="0.9"/></svg></span>
                        <p class="stat-value text-4xl font-bold text-green-600" data-target="{{ $totalCandidates ?? 0 }}" aria-live="polite">0</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.verified-voters.index') }}" class="stat-card block bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-shadow" data-accent="red" aria-label="Total Voters">
                <span class="accent-strip" aria-hidden="true"></span>
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-1">Total Voters</h3>
                        <div class="text-sm text-gray-500">Verified</div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="stat-icon" aria-hidden="true"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 12a3 3 0 100-6 3 3 0 000 6z" fill="currentColor"/><path d="M4 21v-1a7 7 0 0114 0v1H4z" fill="currentColor" opacity="0.9"/></svg></span>
                        <p class="stat-value text-4xl font-bold text-red-600" data-target="{{ $totalVoters ?? 0 }}" aria-live="polite">0</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.users.index') }}" class="stat-card block bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-shadow" data-accent="purple" aria-label="Total Enrolled Students">
                <span class="accent-strip" aria-hidden="true"></span>
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-1">Total Enrolled Students</h3>
                        <div class="text-sm text-gray-500">Active</div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="stat-icon" aria-hidden="true"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 3l9 4v6c0 5-4 8-9 8s-9-3-9-8V7l9-4z" fill="currentColor" opacity="0.95"/></svg></span>
                        <p class="stat-value text-4xl font-bold text-purple-600" data-target="{{ $totalEnrolledStudents ?? 0 }}" aria-live="polite">0</p>
                    </div>
                </div>
            </a>
        </section>

        <section class="my-8 bg-white p-6 rounded-lg shadow-lg">
            <h3 class="text-3xl font-bold text-gray-800 mb-4">Recent Elections</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Election Name</th>
                            <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Date</th>
                            <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Status</th>
                            <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Voter Turnout</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(empty($recentElections) || $recentElections->isEmpty())
                            <tr>
                                <td colspan="4" class="py-4 px-4 text-center text-gray-500">No recent elections found.</td>
                            </tr>
                        @else
                            @foreach($recentElections as $election)
                                <tr>
                                    <td class="py-2 px-4 border-b border-gray-200 text-gray-700">{{ $election->name }}</td>
                                    <td class="py-2 px-4 border-b border-gray-200 text-gray-700">
                                        {{ optional($election->start_date)->format('M d, Y') }}
                                    </td>
                                    <td class="py-2 px-4 border-b border-gray-200 text-gray-700">{{ $election->dashboard_status ?? '' }}</td>
                                    <td class="py-2 px-4 border-b border-gray-200 text-gray-700">{{ $election->dashboard_turnout ?? '—' }}</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </section>
    </main>
    <style>
        /* Stats card entrance and count styling */
        :root{ --card-bg:#ffffff; --muted:#6b7280; }
        .stat-card { position:relative; overflow:visible; transform: translateY(12px) scale(0.98); opacity: 0; will-change: transform, opacity; border-left: 1px solid rgba(0,0,0,0.03); }
        .stat-card.pop-in { animation: popIn 560ms cubic-bezier(.2,.9,.2,1) forwards; }
        .stat-card:hover { transform: translateY(-6px) scale(1.02); transition: transform 220ms ease, box-shadow 220ms ease; box-shadow: 0 18px 40px rgba(6,12,34,0.12); }
        .stat-value { display:inline-block; transform-origin:center; min-width:70px; text-align:right; }

        .accent-strip { position:absolute; left:0; top:12px; bottom:12px; width:8px; border-radius:8px; box-shadow:0 6px 18px rgba(2,6,23,0.06) inset; }
        .stat-card[data-accent="blue"] .accent-strip{ background: linear-gradient(180deg,#3b82f6,#60a5fa); }
        .stat-card[data-accent="green"] .accent-strip{ background: linear-gradient(180deg,#10b981,#34d399); }
        .stat-card[data-accent="red"] .accent-strip{ background: linear-gradient(180deg,#ef4444,#f97373); }
        .stat-card[data-accent="purple"] .accent-strip{ background: linear-gradient(180deg,#8b5cf6,#a78bfa); }

        .stat-icon{ width:40px; height:40px; display:inline-flex; align-items:center; justify-content:center; border-radius:10px; color:rgba(0,0,0,0.75); background:linear-gradient(180deg, rgba(255,255,255,0.9), rgba(255,255,255,0.6)); box-shadow: 0 6px 18px rgba(2,6,23,0.04); transition: transform 260ms cubic-bezier(.2,.9,.2,1), box-shadow 260ms ease; }
        .stat-icon svg{ display:block; width:20px; height:20px; color:inherit; }

        /* small pop for the icon when value animates */
        .icon-pop{ animation: iconPop 560ms cubic-bezier(.2,.9,.2,1) forwards; }
        @keyframes iconPop { 0%{ transform: translateY(0) scale(1); } 50%{ transform: translateY(-6px) scale(1.12);} 100%{ transform: translateY(0) scale(1); } }

        @keyframes popIn {
            0% { opacity: 0; transform: translateY(12px) scale(0.98); }
            60% { opacity: 1; transform: translateY(-6px) scale(1.02); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }
        /* Floating widget visuals */
        #floating-widget .fw-header { -webkit-user-select:none; -moz-user-select:none; user-select:none; }
        #floating-widget .fw-header .fw-title { cursor:move }
        #floating-widget{ box-shadow:0 10px 30px rgba(2,6,23,0.08); }
        /* Make text inside the widget inherit a single base font-size so we can scale it */
        #floating-widget { font-size:14px; }
        #floating-widget * { font-size: inherit !important; }
        /* Prevent internal scrollbars and allow content to reflow */
        #floating-widget { overflow:visible; }
        #floating-widget .p-4 { box-sizing:border-box; }
        /* Responsive adjustments */
        @media (max-width:640px){
            #floating-widget{ left:5vw !important; width:90vw !important; top:20px !important; right:auto !important; }
            #fw-minimized{ left:10px !important; bottom:10px !important; }
            #floating-widget{ resize:horizontal; }
        }
        /* smooth font changes */
        #floating-widget{ transition: font-size 140ms linear, left 80ms ease, top 80ms ease; }
        #floating-widget:active { transition: none; }
        #floating-widget .fw-resize-handle { width:18px;height:18px;background:transparent;position:absolute;right:8px;bottom:8px;cursor:se-resize;display:flex;align-items:center;justify-content:center }
        #fw-minimized{ display:flex }
    </style>

    <script>
        (function(){
            const widget = document.getElementById('floating-widget');
            const header = widget.querySelector('.fw-header');
            const closeBtn = document.getElementById('fw-close');
            const liveTimeEl = document.getElementById('fw-live-time');
            const liveDateEl = document.getElementById('fw-live-date');
            const countdownTimer = document.getElementById('fw-countdown-timer');
            const electionSelect = document.getElementById('fw-election-select');

            const minimizedBtn = document.getElementById('fw-minimized');

            // initial max size: use the widget's default width/height as the maximum allowed size
            const initialStyleWidth = widget.style.width || '420px';
            const initialStyleHeight = widget.style.height || '';
            let initialMaxW = parseInt(initialStyleWidth, 10) || 420;
            let initialMaxH = 0; // 0 means no explicit max for height unless measured after center

            // restore saved state
            try{
                const saved = localStorage.getItem('fw-state');
                if(saved){
                    const s = JSON.parse(saved);
                    if(s.left) widget.style.left = s.left;
                    if(s.top) widget.style.top = s.top;
                    if(s.width) widget.style.width = s.width;
                    if(s.height) widget.style.height = s.height;
                    const wasMin = s.minimized || s.closed || false;
                    // if saved size exceeds our initial max, minimize instead
                    const parsedW = s.width && s.width !== 'auto' ? parseInt(s.width,10) : null;
                    const parsedH = s.height && s.height !== 'auto' ? parseInt(s.height,10) : null;
                    if(parsedW && parsedW > initialMaxW){ widget.style.display = 'none'; minimizedBtn.style.display = 'flex'; }
                    else if(parsedH && initialMaxH && parsedH > initialMaxH){ widget.style.display = 'none'; minimizedBtn.style.display = 'flex'; }
                    else if(wasMin){ widget.style.display = 'none'; minimizedBtn.style.display = 'flex'; }
                }
            }catch(e){}

            function clampPosition(){
                const vw = window.innerWidth || document.documentElement.clientWidth;
                const vh = window.innerHeight || document.documentElement.clientHeight;
                const rect = widget.getBoundingClientRect();
                let left = rect.left, top = rect.top;
                if(left + rect.width > vw - 8) left = Math.max(8, vw - rect.width - 8);
                if(top + rect.height > vh - 8) top = Math.max(8, vh - rect.height - 8);
                if(left < 8) left = 8;
                if(top < 8) top = 8;
                widget.style.left = left + 'px';
                widget.style.top = top + 'px';
            }

            function saveState(){
                try{
                    clampPosition();
                    // store 'auto' for height when set to auto
                    const heightVal = widget.style.height || (getComputedStyle(widget).height ? 'auto' : '');
                    const s = { left: widget.style.left, top: widget.style.top, width: widget.style.width, height: heightVal, minimized: widget.style.display === 'none' };
                    localStorage.setItem('fw-state', JSON.stringify(s));
                }catch(e){}
            }

            // Dragging
            let isDragging = false, startX=0, startY=0, startLeft=0, startTop=0;
            header.addEventListener('mousedown', onDragStart);
            document.addEventListener('mouseup', onDragEnd);
            function onDragStart(e){
                isDragging = true;
                startX = e.clientX;
                startY = e.clientY;
                startLeft = parseInt(widget.style.left) || widget.getBoundingClientRect().left;
                startTop = parseInt(widget.style.top) || widget.getBoundingClientRect().top;
                document.addEventListener('mousemove', onDragMove);
                e.preventDefault();
            }
            function onDragMove(e){
                if(!isDragging) return;
                const dx = e.clientX - startX;
                const dy = e.clientY - startY;
                widget.style.left = Math.max(0, startLeft + dx) + 'px';
                widget.style.top = Math.max(0, startTop + dy) + 'px';
            }
            function onDragEnd(){
                if(!isDragging) return;
                isDragging = false;
                document.removeEventListener('mousemove', onDragMove);
                // ensure it stays in viewport
                clampPosition();
                saveState();
            }

            // touch support
            header.addEventListener('touchstart', function(e){
                const t = e.touches[0];
                isDragging = true; startX = t.clientX; startY = t.clientY; startLeft = parseInt(widget.style.left) || widget.getBoundingClientRect().left; startTop = parseInt(widget.style.top) || widget.getBoundingClientRect().top;
            });
            document.addEventListener('touchmove', function(e){ if(!isDragging) return; const t = e.touches[0]; widget.style.left = Math.max(0, startLeft + (t.clientX - startX)) + 'px'; widget.style.top = Math.max(0, startTop + (t.clientY - startY)) + 'px'; });
            document.addEventListener('touchend', function(){ if(isDragging){ isDragging=false; saveState(); } });

            // Minimize (close) button -> show minimized alarm icon
            closeBtn.addEventListener('click', function(){ widget.style.display='none'; minimizedBtn.style.display='flex'; saveState(); });

            // Restore from minimized icon
            function centerWidget(){
                try{
                    const vw = window.innerWidth || document.documentElement.clientWidth;
                    const vh = window.innerHeight || document.documentElement.clientHeight;
                    // ensure widget is visible to measure
                    widget.style.display = 'block';
                    widget.style.visibility = 'hidden';
                    // let browser render
                    requestAnimationFrame(function(){
                        const rect = widget.getBoundingClientRect();
                        const left = Math.max(8, Math.round((vw - rect.width) / 2));
                        const top = Math.max(8, Math.round((vh - rect.height) / 2));
                        widget.style.left = left + 'px';
                        widget.style.top = top + 'px';
                        // set measured initial max height now that widget rendered centered
                        initialMaxH = Math.round(rect.height);
                        initialMaxW = Math.round(rect.width);
                        widget.style.visibility = 'visible';
                    });
                }catch(e){}
            }

            minimizedBtn.addEventListener('click', function(){
                // hide minimized icon
                minimizedBtn.style.display = 'none';
                // clear saved position so we don't restore an old top-left position
                try{ localStorage.removeItem('fw-state'); }catch(e){}

                // show widget and immediately center visually using transform
                widget.style.display = 'block';
                widget.style.visibility = 'hidden';
                widget.style.left = '50%';
                widget.style.top = '50%';
                widget.style.transform = 'translate(-50%, -50%)';
                widget.style.visibility = 'visible';

                // After render, compute explicit px left/top and remove transform
                requestAnimationFrame(function(){
                    const rect = widget.getBoundingClientRect();
                    const vw = window.innerWidth || document.documentElement.clientWidth;
                    const vh = window.innerHeight || document.documentElement.clientHeight;
                    const left = Math.max(8, Math.round((vw - rect.width) / 2));
                    const top = Math.max(8, Math.round((vh - rect.height) / 2));
                    widget.style.transform = '';
                    widget.style.left = left + 'px';
                    widget.style.top = top + 'px';
                    initialMaxH = Math.round(rect.height);
                    initialMaxW = Math.round(rect.width);
                    adjustFontSize(); clampPosition(); saveState();
                });
            });

            // Adjust base font-size based on widget size so text scales when resized
            function adjustFontSize(){
                try{
                    if(!widget || widget.style.display === 'none') return;
                    const rect = widget.getBoundingClientRect();
                    const w = rect.width;
                    const h = rect.height;
                    // base size from smaller dimension so text scales both ways
                    const candidate = Math.min(w * 0.035, h * 0.08);
                    const size = Math.round(Math.max(10, Math.min(22, candidate)));
                    widget.style.fontSize = size + 'px';
                }catch(e){}
            }

            // Custom resize handle (replaces CSS resize). Keeps overflow hidden so no internal scrollbars appear.
            const handle = widget.querySelector('.fw-resize-handle');
            let isResizing = false, rsW = 0, rsH = 0, rsX = 0, rsY = 0;
            function startResize(e){
                isResizing = true;
                const t = e.touches ? e.touches[0] : e;
                rsX = t.clientX; rsY = t.clientY;
                const rect = widget.getBoundingClientRect();
                rsW = rect.width; rsH = rect.height;
                document.addEventListener('mousemove', onResizeMove);
                document.addEventListener('mouseup', endResize);
                document.addEventListener('touchmove', onResizeMove, {passive:false});
                document.addEventListener('touchend', endResize);
                e.preventDefault();
            }
            function onResizeMove(e){
                if(!isResizing) return;
                const t = e.touches ? e.touches[0] : e;
                const dx = t.clientX - rsX; const dy = t.clientY - rsY;
                let newW = Math.max(240, Math.min(window.innerWidth - 16, rsW + dx));
                let newH = Math.max(110, Math.min(window.innerHeight - 16, rsH + dy));
                // if the user makes the widget larger than the initial centered maximum -> auto-minimize
                if(initialMaxW && newW > initialMaxW + 1){
                    // auto minimize
                    widget.style.display = 'none';
                    minimizedBtn.style.display = 'flex';
                    saveState();
                    endResize();
                    return;
                }
                if(initialMaxH && newH > initialMaxH + 1){
                    widget.style.display = 'none';
                    minimizedBtn.style.display = 'flex';
                    saveState();
                    endResize();
                    return;
                }
                widget.style.width = Math.round(newW) + 'px';
                // If vertical drag is small, let height auto-adjust to content
                if(Math.abs(dy) < 12){
                    widget.style.height = 'auto';
                }else{
                    widget.style.height = Math.round(newH) + 'px';
                }
                adjustFontSize();
                clampPosition();
                e.preventDefault();
            }
            function endResize(){
                if(!isResizing) return; isResizing = false;
                document.removeEventListener('mousemove', onResizeMove);
                document.removeEventListener('mouseup', endResize);
                document.removeEventListener('touchmove', onResizeMove);
                document.removeEventListener('touchend', endResize);
                saveState();
            }
            if(handle){ handle.addEventListener('mousedown', startResize); handle.addEventListener('touchstart', startResize, {passive:false}); }

            // Use ResizeObserver mainly to catch programmatic style changes if any
            const ro = new ResizeObserver(entries => { adjustFontSize(); clampPosition(); saveState(); });
            try{ ro.observe(widget); }catch(e){ }

            // ensure initial sizing after restoring state and keep inside viewport
            adjustFontSize(); clampPosition();
            window.addEventListener('resize', function(){ adjustFontSize(); clampPosition(); });

            // Live time
            function updateTime(){
                const now = new Date();
                liveTimeEl.textContent = now.toLocaleTimeString([], {hour:'2-digit', minute:'2-digit', second:'2-digit'});
                liveDateEl.textContent = now.toLocaleString([], {weekday:'long', month:'short', day:'numeric', year:'numeric'});
            }
            updateTime(); setInterval(updateTime, 1000);

            // Simple countdown demo: choose durations per selection
            const offsets = { 'Vice-President': 8*24*60*60, 'President': 10*24*60*60, 'Secretary': 5*24*60*60 };
            let target = Date.now() + offsets[electionSelect.value]*1000;
            function setTargetForSelection(){ target = Date.now() + (offsets[electionSelect.value]||(8*24*60*60))*1000; }
            electionSelect.addEventListener('change', function(){ setTargetForSelection(); });

            function updateCountdown(){
                const now = Date.now(); const diff = Math.max(0, target - now);
                const days = Math.floor(diff / (24*60*60*1000));
                const hrs = Math.floor((diff % (24*60*60*1000)) / (60*60*1000));
                const mins = Math.floor((diff % (60*60*1000)) / (60*1000));
                const secs = Math.floor((diff % (60*1000)) / 1000);
                countdownTimer.textContent = String(days).padStart(2,'0') + 'd ' + String(hrs).padStart(2,'0') + ':' + String(mins).padStart(2,'0') + ':' + String(secs).padStart(2,'0');
            }
            setTargetForSelection(); updateCountdown(); setInterval(updateCountdown, 1000);
        })();
    </script>

    <script>
        // Count-up animation for stat values and trigger pop-in when visible
        (function(){
            const values = Array.from(document.querySelectorAll('.stat-value'));
            if(!values.length) return;

            function animateCount(el, target){
                target = Number(target) || 0;
                const duration = Math.min(1400, 400 + Math.abs(target) * 8);
                const start = performance.now();
                const from = 0;
                // highlight effect
                el.classList.add('counting');
                const icon = el.closest('.stat-card') ? el.closest('.stat-card').querySelector('.stat-icon') : null;
                if(icon){ icon.classList.add('icon-pop'); setTimeout(()=> icon.classList.remove('icon-pop'), 700); }
                function step(now){
                    const t = Math.min(1, (now - start) / duration);
                    const eased = t < 0.5 ? 2*t*t : -1 + (4 - 2*t)*t; // simple ease
                    const cur = Math.round(from + (target - from) * eased);
                    el.textContent = cur.toLocaleString();
                    if(t < 1) requestAnimationFrame(step);
                    else setTimeout(()=> el.classList.remove('counting'), 220);
                }
                requestAnimationFrame(step);
            }

            const observer = new IntersectionObserver((entries, obs) => {
                entries.forEach(entry => {
                    if(!entry.isIntersecting) return;
                    const card = entry.target.closest('.stat-card') || entry.target;
                    if(card && !card.classList.contains('pop-in')) card.classList.add('pop-in');
                    const el = entry.target;
                    const target = el.getAttribute('data-target') || el.textContent || '0';
                    animateCount(el, target);
                    obs.unobserve(entry.target);
                });
            }, { threshold: 0.3 });

            values.forEach(v => observer.observe(v));
        })();
    </script>
@endsection
