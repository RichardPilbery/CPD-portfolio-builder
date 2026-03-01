---
title: "Summary table of Clinical Leadership Competency Framework"
author: [{{ $user->name }}]
date: "Printed on: {{ now()->format('d/m/Y') }}"
subject: "CPD profile: Clinical Leadership Competency Framework"
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


# Summary table of Clinical Leadership Competency Framework

| Dimension    | Portfolio Entries       |
|:-------------|:------------------------|
@foreach($portfolio_clfs as $key => $value)
| {{ $key }}: {{ $value['element'] }} | @if(isset($value['portfolio'])) @foreach($value['portfolio'] as $k => $e) {{\Carbon\Carbon::parse($e['actdate'])->format('d/m/Y') }}: {{ $e['title'] }} @if($k !== array_key_last($value['portfolio'])) \newline @endif @endforeach @endif |
@endforeach
