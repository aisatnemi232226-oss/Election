@foreach($voters as $voter)
    <tr>
        <td class="py-2 px-4 border-b border-gray-300">{{ $voter->name }}</td>
        <td class="py-2 px-4 border-b border-gray-300">{{ $voter->idnumber ?? 'N/A' }}</td>
        <td class="py-2 px-4 border-b border-gray-300">{{ $voter->course ?? 'N/A' }}</td>
        <td class="py-2 px-4 border-b border-gray-300">{{ $voter->email }}</td>
        <td class="py-2 px-4 border-b border-gray-300">—</td>
    </tr>
@endforeach
