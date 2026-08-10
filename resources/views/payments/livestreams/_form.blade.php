@csrf
@if(isset($livestream)) @method('PUT') @endif

<div class="mb-3">
    <label for="platform" class="form-label">Platform</label>
    <select name="platform" id="platform" class="form-select @error('platform') is-invalid @enderror">
        <option value="">-- Select platform --</option>
        @foreach (['youtube' => 'YouTube', 'twitch' => 'Twitch', 'facebook' => 'Facebook'] as $value => $label)
            <option value="{{ $value }}" {{ old('platform', $livestream->platform ?? '') == $value ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>
    @error('platform')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label for="url" class="form-label">Stream URL</label>
    <input type="url" name="url" id="url" class="form-control @error('url') is-invalid @enderror"
           value="{{ old('url', $livestream->url ?? '') }}" placeholder="https://...">
    @error('url')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label for="label" class="form-label">Label (optional)</label>
    <input type="text" name="label" id="label" class="form-control" value="{{ old('label', $livestream->label ?? '') }}" placeholder="e.g. Main Stream">
</div>

<button type="submit" class="btn btn-primary">Save</button>