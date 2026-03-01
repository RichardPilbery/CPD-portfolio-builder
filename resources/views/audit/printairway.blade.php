---
title: "Airway Log from {{\Carbon\Carbon::parse($start)->format('d/m/Y')}} to {{\Carbon\Carbon::parse($end)->format('d/m/Y')}}"
author: {{ $user->name }}
date: "Printed on: {{ now()->format('d/m/Y') }}"
subject: "Airway Log for {{$user->name}}"
keywords: []
lang: "en"
table-use-row-colors: true
...


| Inc. date time | Inc. No. | Sim | Age | Sex | Airway Type | Success | Grade | Size | Bougie | CO2 | Notes |
|:---------:|:--------|:---|:--------:|:----:|:--------------:|:----:|:-----:|:---:|:----:|:-------------:|:------------------------------:|
@foreach($airways as $a)
|{{\Carbon\Carbon::parse($a['incdatetime'])->format('d/m/y H:i')}}|{{ $a['incnumber'] }}|{{ $a['simulation'] == 1 ? 'yes' : 'no' }}|{{ $a['age'] }}|{{ $a['sex'] }}|{{ $airway_types[$a['airwaytype_id']] }}|{{ $a['success'] == 1 ? 'yes' : 'no'}}|{{ $a['grade'] }}|{{ $a['size'] }}|{{ $a['bougie'] == 1 ? 'yes' : 'no' }}|{{ $a['capnography_id'] != null ? $cap_types[$a['capnography_id']] : '' }}|{{ str_replace(array('#','##','###','*'), '',$a['notes']) }}|
@endforeach