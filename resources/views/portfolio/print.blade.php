---
title: "CPD Portfolio Builder"
author: [{{ $user->name }}]
date: "Printed on: {{ now()->format('d/m/Y') }}"
subject: "Portfolio Entry"
keywords: []
lang: "en"
table-use-row-colors: false
header-includes:
    - \usepackage[absolute,showboxes, overlay]{textpos}
    - \TPGrid[25mm,40mm]{10}{5}
    - \TPMargin{2mm}
    - \usepackage{graphicx}
    - \usepackage[space]{grffile}
    - \usepackage{pdfpages}
    - \definecolor{mygray}{gray}{0.95}
...

# {{ $portfolio->title }}
**Activity date:** {{ $portfolio->actdate->format('d/m/Y') }}

**Description**

{{ $portfolio->description  }}

**Benefits to self and service users:**

{{ $portfolio->benefit  }}

@if(count($comps))
## Competencies achieved
@endif
@forelse($comps as $c)
+ {{ $c }}
@empty
@endforelse

@if(isset($swot->strength))
\newpage

# SWOT analysis

## Strengths
{{ $swot->strength }}

## Weaknesses
{{ $swot->weakness }}

## Opportunities
{{ $swot->opportunity }}

## Threats
{{ $swot->threat }}

@endif

@forelse ($documents as $d)

\includepdf[pages=-,scale=0.75,pagecommand={\pagestyle{fancy}}]{@php echo $d; @endphp}

@empty

@endforelse



