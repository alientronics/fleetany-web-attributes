	@foreach($attributes as $attribute)
	
		@if($attribute->type == 'string')
            <div class="mdl-textfield mdl-js-textfield is-upgraded is-focused mdl-textfield--floating-label @if ($errors->has('attribute'.$attribute->attribute_id)) is-invalid is-dirty @endif"" data-upgraded="eP">
            	{!!Form::text('attribute'.$attribute->attribute_id, $attribute->value, ['id' => 'attribute'.$attribute->attribute_id, 'class' => 'mdl-textfield__input'])!!}
            	{!!Form::label('attribute'.$attribute->attribute_id, $attribute->description, ['class' => 'mdl-color-text--primary-contrast mdl-textfield__label is-dirty'])!!}
            	<span class="mdl-textfield__error">{{ $errors->first('attribute'.$attribute->attribute_id) }}</span>
            </div>
		@elseif($attribute->type == 'numeric')
            <div class="mdl-textfield mdl-js-textfield is-upgraded is-focused mdl-textfield--floating-label @if ($errors->has('attribute'.$attribute->attribute_id)) is-invalid is-dirty @endif"" data-upgraded="eP">
            	{!!Form::number('attribute'.$attribute->attribute_id, $attribute->value, ['id' => 'attribute'.$attribute->attribute_id, 'class' => 'mdl-textfield__input'])!!}
            	{!!Form::label('attribute'.$attribute->attribute_id, $attribute->description, ['class' => 'mdl-color-text--primary-contrast mdl-textfield__label is-dirty'])!!}
            	<span class="mdl-textfield__error">{{ $errors->first('attribute'.$attribute->attribute_id) }}</span>
            </div> 
		@elseif($attribute->type == 'select')
            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label @if ($errors->has('attribute'.$attribute->attribute_id)) is-invalid is-dirty @endif"">
                {!!Form::select('attribute'.$attribute->attribute_id, $attribute->options, $attribute->value, ['class' => 'mdl-textfield__input'])!!}
            	{!!Form::label('attribute'.$attribute->attribute_id, $attribute->description, ['class' => 'mdl-color-text--primary-contrast mdl-textfield__label is-dirty'])!!}
            	<span class="mdl-textfield__error">{{ $errors->first('attribute'.$attribute->attribute_id) }}</span>
            </div>
		@elseif($attribute->type == 'checkbox')
			<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label @if ($errors->has('attribute'.$attribute->attribute_id)) is-invalid is-dirty @endif"">
                {!!Form::checkbox('attribute'.$attribute->attribute_id, $attribute->value, ['class' => 'mdl-textfield__input'])!!}
            	{!!Form::label('attribute'.$attribute->attribute_id, $attribute->description, ['class' => 'mdl-color-text--primary-contrast mdl-textfield__label is-dirty'])!!}
            	<span class="mdl-textfield__error">{{ $errors->first('attribute'.$attribute->attribute_id) }}</span>
            </div>
		@elseif($attribute->type == 'file')
		@endif
    
	@endforeach

    <div class="mdl-card__actions">
    	<button type="submit" class="mdl-button mdl-color--primary mdl-color-text--accent-contrast mdl-js-button mdl-button--raised mdl-button--colored">
          {{ Lang::get('general.Send') }} 
        </button>
    </div>