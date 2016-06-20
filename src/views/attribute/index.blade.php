@extends('layouts.default')

@section('header')
      
      <span class="mdl-layout-title">{{Lang::get("attributes.Attributes")}}</span>

@stop

@include('attribute.filter')

@section('content')

<div class="mdl-grid demo-content">

	@include('includes.gridview', [
    	'registers' => $attributes,
    	'gridview' => [
    		'pageActive' => 'attribute',
         	'sortFilters' => [
                ["class" => "mdl-cell--hide-phone mdl-cell--hide-tablet mdl-cell--8-col", "name" => "entity-key", "lang" => "attributes.Entity_key"], 
                ["class" => "mdl-cell--2-col", "name" => "description", "lang" => "attributes.Description"], 
    		] 
    	]
    ])
     
</div>

@stop