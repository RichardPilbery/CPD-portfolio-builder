---
title: "Audit Log from {{\Carbon\Carbon::parse($start)->format('d/m/Y')}} to {{\Carbon\Carbon::parse($end)->format('d/m/Y')}}"
author: {{ $user->name }}
date: "Printed on: {{ now()->format('d/m/Y') }}"
subject: "Audit Log for {{$user->name}}"
keywords: []
lang: "en"
table-use-row-colors: true
@if(count($airways) > 0 && count($vasculars) > 0)
toc: true
toc-own-page: true
@endif
...

@if(count($airways) > 0)
# Airway log

| Inc. date time | Inc. No. | Sim | Age | Sex | Airway Type | Success | Grade | Size | Bougie | CO2 | Notes |
|:---------:|:--------|:---|:--------:|:----:|:--------------:|:----:|:-----:|:---:|:----:|:-------------:|:------------------------------:|
@foreach($airways as $a)
|{{\Carbon\Carbon::parse($a['incdatetime'])->format('d/m/y H:i')}}|{{ $a['incnumber'] }}|{{ $a['simulation'] == 1 ? 'yes' : 'no' }}|{{ $a['age'] }}|{{ $a['sex'] }}|{{ $airway_types[$a['airwaytype_id']] }}|{{ $a['success'] == 1 ? 'yes' : 'no'}}|{{ $a['grade'] }}|{{ $a['size'] }}|{{ $a['bougie'] == 1 ? 'yes' : 'no' }}|{{ $a['capnography_id'] != null ? $cap_types[$a['capnography_id']] : '' }}|{{ str_replace(array('#','##','###','*'), '',$a['notes']) }}|
@endforeach
@endif


@if(count($vasculars) > 0)
# Vascular access log

| Inc. date time | Inc. No. | Sim | Age | Sex | Vascular Access Type | Success | Size | Site |
|:---------:|:--------|:---|:--------:|:----:|:--------------:|:----:|:-----:|:--------------:|
@foreach($vasculars as $v)
|{{\Carbon\Carbon::parse($v['incdatetime'])->format('d/m/y H:i')}}|{{ $v['incnumber'] }}|{{ $v['simulation'] == 1 ? 'yes' : 'no' }}|{{ $v['age'] }}|{{ $v['sex'] }}|{{ $v['ivtype'] }}|{{ $v['success'] == 1 ? 'yes' : 'no'}}|{{ $v['size'] }}|{{ $v['ivsite'] }}|
@endforeach
@endif


# Audit log

| Inc. date time | Inc. No. | Sim | Age | Sex | Call type | Outcome | Audit items |
|:---------:|:--------|:---|:--------:|:----:|:-----:|:----:|:--------------------------|
@foreach($print_audit as $a)
|{{\Carbon\Carbon::parse($a['incdatetime'])->format('d/m/y H:i')}}|{{ $a['incnumber'] }}|{{ $a['simulation'] == 1 ? 'yes' : 'no' }}|{{ $a['age'] }}|{{ $a['sex'] }}|{{$a['call_type']}}|{{$a['outcome']}}|{{$a['audititems']}}|
@endforeach