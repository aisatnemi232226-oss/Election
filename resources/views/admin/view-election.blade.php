
@extends('layouts.admin')

@section('title', 'Manage Elections')
@section('dashboard-title', 'Election Details')

@section('content')
    @forelse($elections as $election)
   <section class="text-center my-8">
    <h2 class="text-4xl font-bold text-gray-800 mb-4">
        {{ $election->name }}
    </h2>
    <p class="text-gray-600 text-lg">
        {{ $election->description }}   
    </p>
    <img alt="Banner image for the Election 2023 showing a diverse group of students voting" class="w-full h-64 object-cover rounded-lg my-4" height="400" src="https://storage.googleapis.com/a1aa/image/n0pRcSvzmx51IRpeEAlMvqKrTHf16QDNph5r8J2WdHSvwfOoA.jpg" width="1200"/>
   </section>
   <section class="my-8">
    <div class="bg-white p-6 rounded-lg shadow-lg">
     <h3 class="text-3xl font-bold text-gray-800 mb-4">
      Election Details
     </h3>
     <p class="text-gray-600 mb-4">
        <span class="font-bold">Start Date:</span> 
            {{ \Carbon\Carbon::parse($election->start_date)->format('Y-m-d') }}
     </p>

     <p class="text-gray-600 mb-4">
      <strong>
       End Date:
      </strong>
      {{ \Carbon\Carbon::parse($election->end_date)->format('Y-m-d') }}
     </p>
     <p class="text-gray-600 mb-4">
      <strong>
       Registration Period:
      </strong>
      {{ \Carbon\Carbon::parse($election->reg_start)->format('Y-m-d') }} to {{ \Carbon\Carbon::parse($election->reg_end)->format('Y-m-d') }}
     </p>
     <p class="text-gray-600 mb-4">
      <strong>
       Campaign Period:
      </strong>
      {{ \Carbon\Carbon::parse($election->campaign_start)->format('Y-m-d') }} to {{ \Carbon\Carbon::parse($election->campaign_end)->format('Y-m-d') }}
     </p>
     <p class="text-gray-600 mb-4">
      <strong>
       Election Day:
      </strong>
      {{ \Carbon\Carbon::parse($election->start_date)->format('Y-m-d') }}
     </p>
     <p class="text-gray-600 mb-4">
      <strong>
       Results Announcement:
      </strong>
      {{ \Carbon\Carbon::parse($election->result_date)->format('Y-m-d') }}
     </p>
     <h3 class="text-3xl font-bold text-gray-800 mb-4">
      Candidates
     </h3>
     <div class="mb-8">
      <h4 class="text-2xl font-bold text-gray-800 mb-4">
       Presidential Candidates
      </h4>
      <p class="text-gray-600 mb-4">
       <strong>
        Maximum 1
       </strong>
      </p>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
       <div class="bg-gray-100 p-4 rounded-lg shadow-lg">
        <img alt="Portrait of Presidential Candidate 1" class="w-full h-48 object-cover rounded-lg mb-4" height="300" src="https://storage.googleapis.com/a1aa/image/KDV6CxMu81q1Jlr2WCf36nmVvUI9ehSeGCBVNbLNgCF8CfdQB.jpg" width="300"/>
        <h4 class="text-2xl font-bold text-gray-800 mb-2">
         Candidate 1
        </h4>
        <p class="text-gray-600">
         Brief description of Candidate 1's background and platform.
        </p>
       </div>
       <div class="bg-gray-100 p-4 rounded-lg shadow-lg">
        <img alt="Portrait of Presidential Candidate 2" class="w-full h-48 object-cover rounded-lg mb-4" height="300" src="https://storage.googleapis.com/a1aa/image/mLtdHw0n2VreB6R4fsMtI1dLXirN0ycxB5YpFUab6O9ghfOoA.jpg" width="300"/>
        <h4 class="text-2xl font-bold text-gray-800 mb-2">
         Candidate 2
        </h4>
        <p class="text-gray-600">
         Brief description of Candidate 2's background and platform.
        </p>
       </div>
      </div>
     </div>
     <div class="mb-8">
      <h4 class="text-2xl font-bold text-gray-800 mb-4">
       Vice Presidential Candidates
      </h4>
      <p class="text-gray-600 mb-4">
       <strong>
        Maximum 1
       </strong>
      </p>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
       <div class="bg-gray-100 p-4 rounded-lg shadow-lg">
        <img alt="Portrait of Vice Presidential Candidate 1" class="w-full h-48 object-cover rounded-lg mb-4" height="300" src="https://storage.googleapis.com/a1aa/image/VP5XksAc5SooHlsNSODY1tL1RVoShkIfW3ZUKrjfbCUdhfOoA.jpg" width="300"/>
        <h4 class="text-2xl font-bold text-gray-800 mb-2">
         Candidate 3
        </h4>
        <p class="text-gray-600">
         Brief description of Candidate 3's background and platform.
        </p>
       </div>
       <div class="bg-gray-100 p-4 rounded-lg shadow-lg">
        <img alt="Portrait of Vice Presidential Candidate 2" class="w-full h-48 object-cover rounded-lg mb-4" height="300" src="https://storage.googleapis.com/a1aa/image/ehEx0Lf1yghq3E7uULgnduXqdhyL6YK6848V3mpvoAeO9edQB.jpg" width="300"/>
        <h4 class="text-2xl font-bold text-gray-800 mb-2">
         Candidate 4
        </h4>
        <p class="text-gray-600">
         Brief description of Candidate 4's background and platform.
        </p>
       </div>
      </div>
     </div>
     <div class="mb-8">
      <h4 class="text-2xl font-bold text-gray-800 mb-4">
       Representatives
      </h4>
      <p class="text-gray-600 mb-4">
       <strong>
        Maximum 3
       </strong>
      </p>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
       <div class="bg-gray-100 p-4 rounded-lg shadow-lg">
        <img alt="Portrait of Representative Candidate 1" class="w-full h-48 object-cover rounded-lg mb-4" height="300" src="https://storage.googleapis.com/a1aa/image/3jqQgsbMSq5eUqrAwwXeKncRxV7iKIepaXMy7d2lhJtR9edQB.jpg" width="300"/>
        <h4 class="text-2xl font-bold text-gray-800 mb-2">
         Candidate 5
        </h4>
        <p class="text-gray-600">
         Brief description of Candidate 5's background and platform.
        </p>
       </div>
       <div class="bg-gray-100 p-4 rounded-lg shadow-lg">
        <img alt="Portrait of Representative Candidate 2" class="w-full h-48 object-cover rounded-lg mb-4" height="300" src="https://storage.googleapis.com/a1aa/image/CXU70IdAP0YWGF2o0eIm8edH6xhFZi4ri0pmsLFnpdyqeedQB.jpg" width="300"/>
        <h4 class="text-2xl font-bold text-gray-800 mb-2">
         Candidate 6
        </h4>
        <p class="text-gray-600">
         Brief description of Candidate 6's background and platform.
        </p>
       </div>
      </div>
     </div>
     <div class="mb-8">
      <h4 class="text-2xl font-bold text-gray-800 mb-4">
       Election Parties
      </h4>
      <p class="text-gray-600 mb-4">
       <strong>
        Maximum 1
       </strong>
      </p>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
       <div class="bg-gray-100 p-4 rounded-lg shadow-lg">
        <img alt="Logo of Election Party 1" class="w-full h-48 object-cover rounded-lg mb-4" height="300" src="https://storage.googleapis.com/a1aa/image/GL9xieZsPEQGTSA2JCtqYv9zgjiyZOfjpAgEGl8QYEZ7jfOoA.jpg" width="300"/>
        <h4 class="text-2xl font-bold text-gray-800 mb-2">
         Party 1
        </h4>
        <p class="text-gray-600">
         Brief description of Election Party 1's platform and goals.
        </p>
       </div>
       <div class="bg-gray-100 p-4 rounded-lg shadow-lg">
        <img alt="Logo of Election Party 2" class="w-full h-48 object-cover rounded-lg mb-4" height="300" src="https://storage.googleapis.com/a1aa/image/JBme9vwvypzzCyZHhRcKjoNuKRrOSUoW7fnfFntIMFG5HfdQB.jpg" width="300"/>
        <h4 class="text-2xl font-bold text-gray-800 mb-2">
         Party 2
        </h4>
        <p class="text-gray-600">
         Brief description of Election Party 2's platform and goals.
        </p>
       </div>
      </div>
     </div>
     <div class="mb-8">
      <h4 class="text-2xl font-bold text-gray-800 mb-4">
       Valid Voters
      </h4>
      <ul class="list-disc list-inside text-gray-600">
       <li>
        Enrolled in 1st semester 2024-2025
       </li>
       <li>
        Enrolled in 2nd semester 2024-2025
       </li>
       <li>
        Enrolled in School year 2024-2025
       </li>
      </ul>
     </div>
    </div>
   </section>
   @empty
    <p>No elections found.</p>
   @endforelse
@endsection