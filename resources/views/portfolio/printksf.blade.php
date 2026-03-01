---
title: "Summary table of KSF compentencies"
author: [{{ $user->name }}]
date: "Printed on: {{ now()->format('d/m/Y') }}"
subject: "CPD profile: KSF compentencies"
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


# Summary table of KSF compentencies

| Dimension    | Portfolio Entries       |
|:-------------|:------------------------|
@foreach($portfolio_ksfs as $key => $value)
| {{ $key }}: {{ $value['description'] }} | @if(isset($value['portfolio'])) @foreach($value['portfolio'] as $k => $e) @if($e['actdate'] != "00-00-00") {{\Carbon\Carbon::parse($e['actdate'])->format('d/m/Y') }}: @endif {{ $e['title'] }} @if($k !== array_key_last($value['portfolio'])) \newline @endif @endforeach @endif |
@endforeach
