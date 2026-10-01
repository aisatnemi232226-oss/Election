@extends('layouts.voter')

@section('title', 'Philippine Christian University Online Election System - Registration')

@section('content')
<div class="page-content registration-shell registration-shell-registerElection">
<script>
   (function () {
            function loadScript(src, onload, onerror) {
                var s = document.createElement('script');
                s.src = src;
                s.async = true;
                s.onload = onload;
                s.onerror = onerror;
                document.head.appendChild(s);
            }

            loadScript(
                'https://unpkg.com/html5-qrcode/minified/html5-qrcode.min.js',
                function () {},
                function () {
                    loadScript('https://cdn.jsdelivr.net/npm/html5-qrcode/minified/html5-qrcode.min.js');
                }
            );
        })();
  </script>
   <section class="text-center my-8">
    <h2 class="text-4xl font-bold text-gray-800 mb-4">
     Register for Philippine Christian University Online Election System
    </h2>
    <p class="text-gray-600 text-lg">
     Sign up with your student credentials to participate in the election.
    </p>
   </section>
   <section class="my-8">
    <div class="bg-white p-6 rounded-lg shadow-lg">
     @if (session('success'))
      <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-green-800">
       {{ session('success') }}
      </div>
     @endif

     @if ($errors->any())
      <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-red-800">
       <ul class="list-disc pl-5">
        @foreach ($errors->all() as $error)
         <li>{{ $error }}</li>
        @endforeach
       </ul>
      </div>
     @endif

     <form method="POST" action="{{ route('registerElection.store') }}">
      @csrf
      <div class="mb-4">
       <label class="block text-gray-700 font-bold mb-2" for="election-list">
        Election List
       </label>
       <select class="w-full p-3 border border-gray-300 rounded-lg" id="election-list" name="election_id" required="">
       <option value="">Select an Election</option>
        @foreach (($elections ?? []) as $election)
        @php
            $today = now()->toDateString();
            $regStart = $election->reg_start ? $election->reg_start->toDateString() : null;
            $regEnd = $election->reg_end ? $election->reg_end->toDateString() : null;
            $isRegOpen = ($regStart === null || $regStart <= $today) && ($regEnd === null || $regEnd >= $today);
        @endphp
        <option value="{{ $election->id }}" {{ (string) old('election_id', $prefillElectionId ?? '') === (string) $election->id ? 'selected' : '' }} {{ $isRegOpen ? '' : 'disabled' }}>
         {{ $election->name }}{{ $isRegOpen ? '' : ' (Registration closed)' }}
        </option>
        @endforeach
      </select>
     </div>

      <div class="mb-4">
       <label class="block text-gray-700 font-bold mb-2" for="credential">
        Student Number or Email
       </label>
       <div class="relative">
        <input class="w-full p-3 border border-gray-300 rounded-lg pr-12" id="credential" name="credential" type="text" placeholder="Scan QR or type student number/email" value="{{ old('credential', $prefillCredential ?? '') }}" {{ auth()->check() ? 'readonly' : '' }} />
        <button class="absolute inset-y-0 right-0 z-10 flex items-center bg-green-600 text-white px-3 rounded-r-lg hover:bg-green-700 {{ auth()->check() ? 'hidden' : '' }}" type="button" id="scan-qr-button" aria-label="Scan QR">
         Scan QR
        </button>
       </div>
       <p class="text-xs text-gray-500 mt-1">
        If you are already logged in, this field can be left blank.
       </p>
      </div>

      <input type="hidden" id="idnumber" name="idnumber" value="{{ old('idnumber', auth()->check() ? (auth()->user()->idnumber ?? '') : '') }}" />
      <input type="hidden" id="fullname" name="name" value="{{ old('name', auth()->check() ? (auth()->user()->name ?? '') : '') }}" />
      <input type="hidden" id="email" name="email" value="{{ old('email', auth()->check() ? (auth()->user()->email ?? '') : '') }}" />

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
       <div>
        <label class="block text-gray-700 font-bold mb-2" for="display-idnumber">Student Number</label>
        <input class="w-full p-3 border border-gray-300 rounded-lg bg-gray-50" id="display-idnumber" type="text" value="{{ old('idnumber', auth()->check() ? (auth()->user()->idnumber ?? '') : '') }}" readonly />
       </div>
       <div>
        <label class="block text-gray-700 font-bold mb-2" for="display-fullname">Full Name</label>
        <input class="w-full p-3 border border-gray-300 rounded-lg bg-gray-50" id="display-fullname" type="text" value="{{ old('name', auth()->check() ? (auth()->user()->name ?? '') : '') }}" readonly />
       </div>
       <div>
        <label class="block text-gray-700 font-bold mb-2" for="display-email">Email</label>
        <input class="w-full p-3 border border-gray-300 rounded-lg bg-gray-50" id="display-email" type="text" value="{{ old('email', auth()->check() ? (auth()->user()->email ?? '') : '') }}" readonly />
       </div>
      </div>

      <div class="text-center">
       <button class="bg-blue-600 text-white p-3 rounded-lg cursor-pointer hover:bg-blue-700" type="submit">
        Register
       </button>
      </div>
     </form>

     <div id="qr-reader-container" class="mt-4" style="display: none;">
      <div class="flex items-center justify-between mb-2">
       <span class="text-gray-700 text-sm font-bold">Scan QR Code</span>
       <button class="text-gray-600 hover:text-gray-800" type="button" id="close-qr-button" aria-label="Close scanner">
        Close
       </button>
      </div>
      <div id="qr-reader"></div>
     </div>
    </div>
   </section>

    

  <script>
   let html5QrCode = null;
        let isScanning = false;

        function parseQrPayload(raw) {
            const text = (raw || '').trim();
            if (!text) return null;

            if (text.includes(';')) {
                const segs = text.split(';').map(s => s.trim()).filter(Boolean);
                if (segs.length >= 2) {
                    return {
                        idnumber: segs[0] || '',
                        name: segs[1] || '',
                        email: segs[2] || '',
                    };
                }
            }

            if (text[0] === '{') {
                try {
                    const obj = JSON.parse(text);
                    if (obj && typeof obj === 'object') {
                        return obj;
                    }
                } catch (e) {}
            }

            if (text.startsWith('http://') || text.startsWith('https://')) {
                try {
                    const u = new URL(text);
                    return {
                        idnumber: u.searchParams.get('idnumber') || '',
                        name: u.searchParams.get('name') || '',
                        email: u.searchParams.get('email') || '',
                    };
                } catch (e) {}
            }

            const normalized = text.replace(/\r/g, '\n');
            const parts = normalized.split(/[\n,;|]+/).map(p => p.trim()).filter(Boolean);
            const out = {};
            for (const p of parts) {
                const m = p.match(/^\s*(idnumber|student\s*number|studentno|student\s*no|id|email|name|fullname|full\s*name)\s*[:=]\s*(.+)\s*$/i);
                if (!m) continue;
                const key = m[1].toLowerCase().replace(/\s+/g, '');
                const val = (m[2] || '').trim().replace(/^['"]|['"]$/g, '');

                if (key === 'idnumber' || key === 'studentnumber' || key === 'studentno' || key === 'id') {
                    out.idnumber = val;
                } else if (key === 'email') {
                    out.email = val;
                } else if (key === 'name' || key === 'fullname' || key === 'full name') {
                    out.name = val;
                }
            }

            if (Object.keys(out).length > 0) {
                return out;
            }

            return null;
        }

        async function stopScanner() {
            const container = document.getElementById('qr-reader-container');
            if (!html5QrCode || !isScanning) {
                container.style.display = 'none';
                return;
            }

            try {
                await html5QrCode.stop();
            } catch (err) {
                console.error(err);
            } finally {
                container.style.display = 'none';
                isScanning = false;
                html5QrCode = null;
                document.getElementById('qr-reader').innerHTML = '';
            }
        }

        document.getElementById('close-qr-button').addEventListener('click', function() {
            stopScanner();
        });

        document.getElementById('scan-qr-button').addEventListener('click', async function() {
            const container = document.getElementById('qr-reader-container');

            if (typeof Html5Qrcode === 'undefined') {
                alert('QR scanner failed to load. Please refresh the page or check your internet connection.');
                return;
            }

            if (isScanning) {
                await stopScanner();
                return;
            }

            container.style.display = 'block';
            html5QrCode = new Html5Qrcode('qr-reader');
            isScanning = true;

            html5QrCode.start(
                { facingMode: 'environment' },
                {
                    fps: 10,
                    qrbox: 250
                },
                async qrCodeMessage => {
                    try {
                        await stopScanner();
                        const raw = (qrCodeMessage || '').trim();
                        const parsed = parseQrPayload(raw);
                        const idValue = parsed && parsed.idnumber ? String(parsed.idnumber) : '';
                        const emailValue = parsed && parsed.email ? String(parsed.email) : '';
                        const nameValue = parsed && parsed.name ? String(parsed.name) : '';
                        const chosen = emailValue || idValue || raw;
                        document.getElementById('credential').value = chosen;

                        const idClean = (idValue || '').replace(/[^0-9]/g, '');
                        if (idClean) {
                            document.getElementById('idnumber').value = idClean;
                            document.getElementById('display-idnumber').value = idClean;
                        }

                        if (nameValue) {
                            document.getElementById('fullname').value = nameValue;
                            document.getElementById('display-fullname').value = nameValue;
                        }

                        if (emailValue) {
                            document.getElementById('email').value = emailValue;
                            document.getElementById('display-email').value = emailValue;
                        }
                    } catch (e) {
                        console.error(e);
                        alert(e && e.message ? e.message : 'Invalid QR code');
                    }
                },
                errorMessage => {
                    console.warn('QR Code no longer in front of camera.');
                }
            ).catch(async err => {
                console.error(err);
                await stopScanner();
            });
        });
  </script>
</div>
@endsection
