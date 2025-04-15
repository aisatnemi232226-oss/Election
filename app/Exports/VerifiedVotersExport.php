<?php 

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VerifiedVotersExport implements FromCollection, WithHeadings
{
    protected $verifiedVoters;

    public function __construct($verifiedVoters)
    {
        $this->verifiedVoters = $verifiedVoters;
    }

    public function collection()
    {
        return collect($this->verifiedVoters)->map(function ($voter) {
            return [
                'Name' => optional($voter->users)->name ?? 'N/A',
                'ID Number' => optional($voter->users)->idnumber ?? 'N/A',
                'Academic Year' => optional($voter->acadyr)->name ?? 'N/A',
                'Semester' => optional($voter->semesters)->name ?? 'N/A',
            ];
        });
    }

    public function headings(): array
    {
        return ['Name', 'ID Number', 'Academic Year', 'Semester'];
    }
}
