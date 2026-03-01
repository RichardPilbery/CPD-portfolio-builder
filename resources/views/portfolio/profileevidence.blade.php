---
title: "Evidence for CPD profile"
author: [@if($hcpcaudit == 1) "" @else {{ $user->name }} @endif]
date: "Printed on: {{ now()->format('d/m/Y') }}"
subject: "CPD profile: Evidence"
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


