3. Personal statement

{{ $summary->standard1 }}

{{ $summary->standard2 }}

A summary of the previous 2 years' CPD activity is listed in the summary sheet (Evidence 1).

@php
    $counter = 0;
    $evidence = 2;
@endphp

@foreach($result as $entry)
@php
    $counter++;
@endphp

Example {{ $counter }} @if(($entry['docs'] != null && count($entry['docs']) > 0) || isset($entry['swot'])) (Evidence {{ $evidence }}) @php $evidence++;@endphp @endif

    Activity type: {{ $entry['activity'] }}

    Title: {{ $entry['portfolio']['title'] }}

    Description: {{ $entry['portfolio']['description'] }}

    Benefit: {{ $entry['portfolio']['benefit'] }}
@endforeach