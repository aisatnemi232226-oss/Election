<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ElectionEventSetting extends Model
{
    protected $table = 'election_event_settings';

    protected $fillable = [
        'label',
        'label_image',
        'start_date',
        'end_date',
        'status',
        'event_1_title',
        'event_1_date',
        'event_1_description',
        'event_1_image',
        'event_2_title',
        'event_2_date',
        'event_2_description',
        'event_2_image',
        'event_3_title',
        'event_3_date',
        'event_3_description',
        'event_3_image',
    ];
}
