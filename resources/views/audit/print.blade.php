---
title: "CPD Portfolio Builder"
author: [{{ $user->name }}]
date: "Printed on: {{ now()->format('d/m/Y') }}"
subject: "Audit Entry"
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

# Incident number: {{ $audit->incnumber}}\
**Incident date:** {{ Carbon\Carbon::parse($audit->incdatetime)->format('d/m/Y H:i') }}\
**Patient information:** {{ucfirst($audit->sex)}} {{$audit->age}} {{$audit->ageunit}}\
@if(isset($audit->provdiag))
**Working impression**: {{$audit->provdiag}}\
@endif
@if(isset($ampds))
**AMPDS code:** {{$ampds}}\
@endif
**Call type:** {{ $call_type }}\
**Outcome:** {{$outcome}}\
**Notes:** {{ !empty($audit->note) ? $audit->note : "No notes" }}


@if(!$audit->airways->isEmpty())
# Airway management

| Device   | Outcome   |  Additional Information | Notes                 |
|:---------|:----------|-------------------|---------------------------|
@foreach ($audit->airways as $a)
|{{ $airway_types[$a->airwaytype_id] }}|{{ $a->success? "Successful" : "Unsuccessful" }}| @if(in_array($a->airwaytype_id, $int_codes)) Tube size: {{ $a->size }}; Grade: {{ $a->grade }}; @else @if(in_array($a->airwaytype_id, $dev_codes)) Device size: {{ !empty($a->size)? $a->size : "Not entered"}}; @endif @endif @if(in_array($a->airwaytype_id, $int_codes)) Bougie used: {{ $a->bougie? "Yes" : "No" }};  @endif @if(in_array($a->airwaytype_id, $cap_codes)) Capnography: {{ !empty($a->capnography_id)? $cap_types[$a->capnography_id] : "Not entered" }}; @endif
|{{ $a->notes }}|
@endforeach
@endif



@if(!$ai->isEmpty())
# Skills and interventions\
@php
$oldheading = "";
$newheading = "";
@endphp
@foreach($ai as $a)
@php
if($a->name != $oldheading) {
// This will work except for the first loop
$newheading = $a->name;
$oldheading = $newheading;
if($loop->first) {
    $oldheading = $a->name;
}
echo "
**$newheading**\
";

}

echo "$a->name2\
";
@endphp
@endforeach
@endif

@if(!$audit->vasculars->isEmpty())
# Vascular access

| Device   | Outcome   |  Additional Information | Notes                 |
|:---------|:----------|-------------------|---------------------------|
@foreach ($audit->vasculars as $v)
|{{ $iv_types[$v->ivtype_id] }}|{{ $v->success? "Successful" : "Unsuccessful" }}| Size: {{ !empty($v->size) ? $v->size : "Not entered" }}; Site: {{ !empty($v->ivsite_id) ? $iv_sites[$v->ivsite_id] : "Not entered"}}|{{ $a->notes }}|
@endforeach
@endif


@forelse ($documents as $d)

\includepdf[pages=-,scale=0.75,pagecommand={\pagestyle{fancy}}]{@php $new_file_name = pathinfo($d->filepath, PATHINFO_BASENAME); echo $new_file_name; @endphp}

@empty

@endforelse



