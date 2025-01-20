@extends('layouts.template')

@section('content')
  <div class="card card-outline card-primary">
    <div class="card-header">
      <h3 class="card-title">Edit Menu</h3>
    </div>
    <div class="card-body">
      <form action="{{ url('/menu/' . $menu->menus_id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
          <label for="name">Nama Menu</label>
          <input type="text" class="form-control" name="name" id="name" value="{{ $menu->name }}" required>
        </div>
        <div class="form-group">
          <label for="slug">Slug</label>
          <input type="text" class="form-control" name="slug" id="slug" value="{{ $menu->slug }}" required>
        </div>
        <div class="form-group">
          <label for="parent_id">Induk Menu</label>
          <select class="form-control" name="parent_id" id="parent_id">
            <option value="">- Pilih Menu Induk -</option>
            @foreach($parents as $parent)
              <option value="{{ $parent->menus_id }}" {{ $menu->parent_id == $parent->menus_id ? 'selected' : '' }}>
                {{ $parent->name }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label for="order_number">Urutan</label>
          <input type="number" class="form-control" name="order_number" id="order_number" value="{{ $menu->order_number }}" required>
        </div>
        <div class="form-group">
          <label for="content">Konten</label>
          <textarea class="form-control" name="content" id="content">{{ $menu->content->body ?? '' }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary">Perbarui</button>
      </form>
    </div>
  </div>
@endsection
