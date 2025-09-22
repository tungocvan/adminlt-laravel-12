<div class="form-group"
     x-data="{
        raw: @entangle($attributes->wire('model')).live,
        tags: [],
        init() {
            if (this.raw) {
                this.tags = this.raw.split(';').map(t => t.trim()).filter(t => t.length > 0);
            }
        },
        updateRaw() {
            this.raw = this.tags.join(';');
        },
        addTag(event) {
            if (event.key === ';') {
                event.preventDefault();
                let value = event.target.value.replace(';', '').trim();
                if (value.length > 0) {
                    this.tags.push(value);
                    this.updateRaw();
                }
                event.target.value = '';
            }
        },
        removeTag(index) {
            this.tags.splice(index, 1);
            this.updateRaw();
        }
     }">

    @if($label ?? false)
        <label>{{ $label }}</label>
    @endif

    <input type="text" class="form-control"
           placeholder="Nhập tag rồi nhấn ;"
           x-on:keydown="addTag($event)">

    <div class="mt-2">
        <template x-for="(tag, index) in tags" :key="index">
            <span class="badge badge-info mr-1">
                <span x-text="tag"></span>
                <button type="button"
                        @click="removeTag(index)"
                        class="ml-1 text-white border-0 bg-transparent">×</button>
            </span>
        </template>
    </div>

    @error($attributes->wire('model')->value()) 
        <small class="text-danger">{{ $message }}</small> 
    @enderror
</div>
