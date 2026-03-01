---
title: "Summary table of CPD activity over the past 2 years"
author: [@if($hcpcaudit == 1) "" @else {{ $user->name }} @endif]
date: "Printed on: {{ now()->format('d/m/Y') }}"
subject: "CPD profile: Summary table"
keywords: []
lang: "en"
table-use-row-colors: true
...

# 4. Summary of supporting evidence

@php
    $counter = 1;
    $evidence = 2;
@endphp

| Evidence no. | Brief description | Pages or format | CPD standards met |
|:------------:|:------------------|:----------------|:-----------------:|
| 1 | Summary table of previous 2 years activities | 1 table | 1 and 2 |
@foreach($result as $entry)
@if(($entry['docs'] != null && count($entry['docs']) > 0) || isset($entry['swot']))
@if(isset($entry['swot']))
    | {{ trim($evidence) }} | SWOT analysis | 1 page | 3 and 4 |
@else
    | {{ trim($evidence) }} | {{ preg_replace("/\r|\n/", " ", trim($entry['docs'][0]['title'])) }} | {{ preg_replace( "/\r|\n/", " ", trim($entry['docs'][0]['format'])) }} | 3 and 4 |
@endif
@php
    $evidence++;
    $counter++;
@endphp
@endif
@endforeach
