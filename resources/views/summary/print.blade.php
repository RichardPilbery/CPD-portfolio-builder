---
title: "CPD Portfolio Builder"
author: [{{ $user->name }}]
date: "Printed on: {{ now()->format('d/m/Y') }}"
subject: "Summary of Practice"
keywords: []
lang: "en"
table-use-row-colors: true
header-includes:
    - \usepackage[absolute,showboxes, overlay]{textpos}
    - \TPGrid[25mm,40mm]{10}{5}
    - \TPMargin{2mm}
    - \usepackage{graphicx}
    - \usepackage[space]{grffile}
    - \definecolor{mygray}{gray}{0.95}
...

# Summary of practice

{{ $summary->work_details}}

{{ $summary->service_users }}

## Job Description

Outlined below are the elements of my job description which summarise my professional responsiblities:

{{ $summary->job_description }}

@if(!empty($portfolios))
# Summary of {{$duration}} CPD activities

Over the past {{ $duration }} you have undertaken {{ round($total_cpd_time,1) }} hours of CPD.

| Date | Title          | Description    | Duration (hours) |
|:-----|:---------------|:---------------|:--------:|
@foreach($portfolios as $entry)
    | {{ $entry->actdate->format('d/m/Y') }} | {{ str_replace(array("\r\n", "\r", "\n"), "", trim($entry->title)) }} | {{ str_replace(array("\r\n", "\r", "\n"), "", $entry->description)  }} | {{ round(Carbon\Carbon::parse($entry->end)->diffInMinutes(Carbon\Carbon::parse($entry->start))/60,1) }}|
@endforeach
@endif