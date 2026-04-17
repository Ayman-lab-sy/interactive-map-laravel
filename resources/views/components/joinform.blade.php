<form method="POST" action="{{route('join.post')}}" class="two_third first">
    @csrf
    <h6 class="heading">{{ __('home.joinus.line') }}</h6>
    <div class="group btmspace-50">
        <div class="one_half first">
            <label for="first_name">{{ __('form.first_name') }} *</label>
            <input class="{{$errors->has('first_name') ? 'error-input' : ''}}" type="text" name="first_name" value="{{old('first_name') ?? ''}}" required value="{{old('first_name')}}"/>
            @if ($errors->has('first_name'))
                <span class="text-danger">{{ $errors->first('first_name') }}</span>
            @endif
        </div>
        <div class="one_half">
            <label for="last_name">{{ __('form.last_name') }} *</label>
            <input class="{{$errors->has('last_name') ? 'error-input' : ''}}" type="text" name="last_name" value="{{old('last_name') ?? ''}}" required value="{{old('last_name')}}"/>
            @if ($errors->has('last_name'))
                <span class="text-danger">{{ $errors->first('last_name') }}</span>
            @endif
        </div>
    </div>
    <div class="group btmspace-50">
        <div class="one_half first">
            <label for="birth_date">{{ __('form.birth_date') }} *</label>
            <input class="{{$errors->has('birth_date') ? 'error-input' : ''}}" type="date" name="birth_date" required value="{{old('birth_date')}}"/>
            @if ($errors->has('birth_date'))
                <span class="text-danger">{{ $errors->first('birth_date') }}</span>
            @endif
        </div>
        <div class="one_half">
            <label for="gender">{{ __('form.gender') }}</label>
            <select class="{{$errors->has('gender') ? 'error-input' : ''}}" name="gender" required value="{{old('gender') ?? ''}}">
                <option value="none" selected>--</option>
                <option value="male">{{ __('form.male') }}</option>
                <option value="female">{{ __('form.female') }}</option>
            </select>
            @if ($errors->has('gender'))
                <span class="text-danger">{{ $errors->first('gender') }}</span>
            @endif
        </div>
    </div>
    <div class="group btmspace-50">
        <div class="one_half first">
            <label for="street">{{ __('form.street') }} *</label>
            <textarea class="{{$errors->has('street') ? 'error-input' : ''}}" name="street" id="street" cols="30" rows="10" required>{{old('street')}}</textarea>
            @if ($errors->has('street'))
                <span class="text-danger">{{ $errors->first('street') }}</span>
            @endif
        </div>
        <div class="one_half">
            <label for="postcode">{{ __('form.postcode') }} *</label>
            <input class="{{$errors->has('postcode') ? 'error-input' : ''}}" type="text" name="postcode" required value="{{old('postcode')}}"/>
            @if ($errors->has('postcode'))
                <span class="text-danger">{{ $errors->first('postcode') }}</span>
            @endif
        </div>
    </div>
    <div class="group btmspace-50">
        <div class="one_half first">
            <label for="location">{{ __('form.location') }} *</label>
            <input class="{{$errors->has('location') ? 'error-input' : ''}}" type="text" name="location" required value="{{old('location')}}"/>
            @if ($errors->has('location'))
                <span class="text-danger">{{ $errors->first('location') }}</span>
            @endif
        </div>
        <div class="one_half">
            <label for="phone">{{ __('form.phone') }}</label>
            <input class="{{$errors->has('phone') ? 'error-input' : ''}}" type="tel" name="phone" value="{{old('phone')}}"/>
            @if ($errors->has('phone'))
                <span class="text-danger">{{ $errors->first('phone') }}</span>
            @endif
        </div>
    </div>
    <div class="group btmspace-50">
        <div class="one_half first">
            <label for="email">{{ __('form.email') }} *</label>
            <input class="{{$errors->has('email') ? 'error-input' : ''}}" type="email" name="email" required value="{{old('email')}}"/>
            @if ($errors->has('email'))
                <span class="text-danger">{{ $errors->first('email') }}</span>
            @endif
        </div>
    </div>
    <div class="group btmspace-50">
        <div class="one_half first">
            @if ($errors->has('aggrement_1'))
                <span class="text-danger">{{ $errors->first('aggrement_1') }}</span>
            @endif
            <label for="aggrement_1">
                <input type="checkbox" value="1" name="aggrement_1" id="aggrement_1" style="display: inline-block;width: fit-content;" required  />
                {{ __('form.aggrement_1') }} *
            </label>
        </div>
        <div class="one_half">
            @if ($errors->has('aggreement_2'))
                <span class="text-danger">{{ $errors->first('aggreement_2') }}</span>
            @endif
            <label for="aggreement_2">
                <input type="checkbox" value="1" name="aggreement_2" id="aggreement_2" style="display: inline-block;width: fit-content;" required/>
                {{ __('form.aggreement_2') }} *
            </label>
        </div>
    </div>
    <button class="btn" type="submit">{{__('form.submit')}}</button>
</form>
