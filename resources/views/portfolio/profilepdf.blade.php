---
title: "Summary of CPD activity over the past 2 years"
author: [{{ $user->name }}]
date: "Printed on: {{ now()->format('d/m/Y') }}"
subject: "CPD Profile"
logo: 'logo.png'
titlepage: true
keywords: []
lang: "en"
table-use-row-colors: true
header-includes:
    - \usepackage[absolute,showboxes, overlay]{textpos}
    - \TPGrid[25mm,40mm]{10}{5}
    - \TPMargin{2mm}
    - \usepackage{graphicx}
    - \usepackage[space]{grffile}
    - \usepackage{pdfpages}
    - \definecolor{mygray}{gray}{0.95}
...



# 1 Personal details
**1.1 Name:** {{ $user->name }}\
**1.1 Role:** {{ $user->roles->title }}\
**1.2 Registration number:** {{ $user->pin }}

# 2 Summary of recent work / practice

{{ $summary->work_details }}

{{ $summary->service_users }}

Outlined below are the elements of my job description which summarise my professional responsiblities:

+ {{ $summary->job_description }}

# 3. Personal statement

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

# Example {{ $counter }} @if(($entry['docs'] != null && count($entry['docs']) > 0) || isset($entry['swot'])) (Evidence {{ $evidence }}) @php $evidence++;@endphp @endif\
_Activity type: {{ $entry['activity'] }}_

**Title:** {{ $entry['portfolio']['title'] }}

**Description:**\
{{ $entry['portfolio']['description'] }}

**Benefit:**\
{{ $entry['portfolio']['benefit'] }}

@endforeach


\pagebreak

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

\pagebreak


# Evidence 1: Summary of the previous 2 years' CPD activities

| Date      | Title          | Description    |
|:----------|:---------------|:---------------|
@foreach($portfolios as $entry)
    | {{ $entry['actdate']->format('d/m/Y') }} | {{ str_replace(array("\r\n", "\r", "\n"), "", trim($entry['title'])) }} | {{ str_replace(array("\r\n", "\r", "\n"), "", $entry['description']) }} |
@endforeach


@php
    $counter = 1;
    $evidence = 2;
@endphp


@foreach($result as $entry)
@if(($entry['docs'] != null && count($entry['docs']) > 0) || isset($entry['swot']))

@if(isset($entry['swot']))
\newpage

\textblockcolour{mygray}
\begin{textblock}{3}(0,0)
    \begin{LARGE}
        \textbf{Evidence {!! $evidence !!}}
    \end{LARGE}
\end{textblock}

#
# SWOT analysis

## Strengths
{{ $entry['swot']['strength'] }}

## Weaknesses
{{ $entry['swot']['weakness'] }}

## Opportunities
{{ $entry['swot']['opportunity'] }}

## Threats
{{ $entry['swot']['threat'] }}

@else

@foreach($entry['docs'] as $doc)

{{-- https://tex.stackexchange.com/questions/8422/how-to-include-graphics-with-spaces-in-their-path --}}

\newpage


\textblockcolour{mygray}
\begin{textblock}{3}(0,0)
    \begin{LARGE}
        \textbf{Evidence {!! $evidence !!}}
    \end{LARGE}
\end{textblock}


\includepdf[pages=-,scale=0.75,pagecommand={\pagestyle{fancy}}]{@php $new_file_name = pathinfo($doc['filepath'], PATHINFO_BASENAME); echo $new_file_name; @endphp}


@endforeach
@endif
@php $counter++; $evidence++; @endphp
@endif
@endforeach


