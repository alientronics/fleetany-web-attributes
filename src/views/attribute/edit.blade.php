@extends('layouts.default')

@section('header')
	@if ($attribute->id)
	{{--*/ $operation = 'update' /*--}}
	<span class="mdl-layout-title">{{$attribute->description}}</span>
	@else
	{{--*/ $operation = 'create' /*--}}
	<span class="mdl-layout-title">{{Lang::get("attributes.Attribute")}}</span>
	@endif
@stop

@section('content')

@permission($operation.'.attribute')

<div class="">
	<section class="demo-section demo-section--textfield demo-page--textfield mdl-upgraded">
		<div class="demo-preview-block">

@if (!$attribute->id)
{!! Form::open(['route' => 'attribute.store']) !!}
@else
{!! Form::model('$attribute', [
        'method'=>'PUT',
        'route' => ['attribute.update',$attribute->id]
    ]) !!}
@endif
		    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label @if ($errors->has('entity_key')) is-invalid is-dirty @endif"">
                {!!Form::select('entity_key', $entity_key, $attribute->entity_key, ['class' => 'mdl-textfield__input'])!!}
            	{!!Form::label('entity_key', Lang::get('attributes.Entity_key'), ['class' => 'mdl-color-text--primary-contrast mdl-textfield__label is-dirty'])!!}
            	<span class="mdl-textfield__error">{{ $errors->first('entity_key') }}</span>
            </div>
         
			<div class="mdl-textfield mdl-js-textfield is-upgraded is-focused mdl-textfield--floating-label @if ($errors->has('description')) is-invalid is-dirty @endif"" data-upgraded="eP">
         		{!!Form::text('description', $attribute->description, ['class' => 'mdl-textfield__input'])!!}
				{!!Form::label('description', Lang::get('attributes.Description'), ['class' => 'mdl-color-text--primary-contrast mdl-textfield__label is-dirty'])!!}
				<span class="mdl-textfield__error">{{ $errors->first('description') }}</span>
			</div>
			
		    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label @if ($errors->has('type')) is-invalid is-dirty @endif"">
                {!!Form::select('type', $type, $attribute->type, ['class' => 'mdl-textfield__input'])!!}
            	{!!Form::label('type', Lang::get('attributes.Type'), ['class' => 'mdl-color-text--primary-contrast mdl-textfield__label is-dirty'])!!}
            	<span class="mdl-textfield__error">{{ $errors->first('type') }}</span>
            </div>

			<div class="mdl-textfield mdl-js-textfield is-upgraded is-focused mdl-textfield--floating-label @if ($errors->has('options')) is-invalid is-dirty @endif"" data-upgraded="eP">
         		{!!Form::text('options', $attribute->options, ['class' => 'mdl-textfield__input'])!!}
				{!!Form::label('options', Lang::get('attributes.Options'), ['class' => 'mdl-color-text--primary-contrast mdl-textfield__label is-dirty'])!!}
				<span class="mdl-textfield__error">{{ $errors->first('options') }}</span>
			</div>

			<div class="mdl-card__actions">
				<button type="submit" class="mdl-button mdl-color--primary mdl-color-text--accent-contrast mdl-js-button mdl-button--raised mdl-button--colored">
                  {{ Lang::get('general.Send') }} 
                </button>
			</div>
	
{!! Form::close() !!}

		</div>
	</section>
</div>

@else
<div class="alert alert-info">
	{{Lang::get("general.accessdenied")}}
</div>
@endpermission

@stop