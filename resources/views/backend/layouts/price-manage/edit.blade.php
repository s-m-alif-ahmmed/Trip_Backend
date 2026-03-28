@extends('backend.app')

@section('title', 'Price Manage Edit')

@section('content')
    {{-- PAGE-HEADER --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Price Manage Form</h1>
        </div>
        <div class="ms-auto pageheader-btn">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0);">User</a></li>
                <li class="breadcrumb-item active" aria-current="page">Price Manage</li>
            </ol>
        </div>
    </div>
    {{-- PAGE-HEADER --}}


    <div class="row">
        <div class="col-lg-12 col-xl-12 col-md-12 col-sm-12">
            <div class="card box-shadow-0">
                <div class="card-body">
                    <form method="post" action="{{ route('price-manage.update', ['id' => $data->id]) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <div class="form-group">
                            <label for="pickup_fee" class="form-label">Pickup Fee:</label>
                            <input  type="number" step="0.01" min="0" class="form-control @error('pickup_fee') is-invalid @enderror"
                                   name="pickup_fee" placeholder="pickup_fee" id="pickup_fee" value="{{ $data->pickup_fee ?? old('pickup_fee') }}">
                            @error('pickup_fee')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="service_fee" class="form-label">Service Fee:</label>
                            <input type="number" step="0.01" min="0" class="form-control @error('service_fee') is-invalid @enderror"
                                   name="service_fee" placeholder="service_fee" id="service_fee" value="{{ $data->service_fee ?? old('service_fee') }}">
                            @error('service_fee')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="weight_per_kg_price" class="form-label">Weight Per Kg Price:</label>
                            <input type="number" step="0.01" min="0" class="form-control @error('weight_per_kg_price') is-invalid @enderror"
                                   name="weight_per_kg_price" placeholder="weight_per_kg_price" id="weight_per_kg_price" value="{{ $data->weight_per_kg_price ?? old('weight_per_kg_price') }}">
                            @error('weight_per_kg_price')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button class="btn btn-primary" type="submit">Submit</button>
                            <a href="{{ route('user.index') }}" class="btn btn-danger me-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
