---
title: "Personal Development Plan"
author: [{{ $user->name }}]
date: "Printed on: {{ now()->format('d/m/Y') }}"
subject: "CPD Portfolio Builder: Personal development plan"
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


# Personal Development Plan

| Objective | Activity | Measure | Support | Barriers | Planned Completion Date | Completed |
|:--------------|:------------|:--------|:--------|:---------|:----:|:-------:|
@foreach($format_pdp as $p)
| {{ $p['objective'] }} | {{ $p['activity'] }} | {{ $p['measure'] }} | {{ $p['support'] }} | {{ $p['barriers'] }} | {{ \Carbon\Carbon::parse($p['finishdate'])->format('d/m/y') }} | {{ $p['completed'] ? "Yes" : "No" }} |
@endforeach
