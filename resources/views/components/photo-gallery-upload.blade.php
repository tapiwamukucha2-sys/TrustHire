@props(['max' => 5])

<div x-data="{
    photos: [],
    max: {{ $max }},
    handleFiles(event) {
        const files = Array.from(event.target.files).slice(0, this.max - this.photos.length);
        files.forEach(file => {
            const reader = new FileReader();
            reader.onload = () => this.photos.push(reader.result);
            reader.readAsDataURL(file);
        });
        event.target.value = '';
    },
    removePhoto(i) { this.photos.splice(i, 1); }
}">
    <label class="block text-sm text-gray-600">Photos (up to {{ $max }})</label>
    <div class="mt-2 flex flex-wrap gap-3">
        <template x-for="(src, i) in photos" :key="i">
            <div class="relative h-20 w-20">
                <img :src="src" alt="" class="h-full w-full rounded border border-gray-300 object-cover">
                <button type="button" @click="removePhoto(i)" class="absolute -right-2 -top-2 flex h-5 w-5 items-center justify-center rounded-full bg-primary text-xs text-white">&times;</button>
            </div>
        </template>
        <label x-show="photos.length < max" class="flex h-20 w-20 cursor-pointer items-center justify-center rounded border-2 border-dashed border-gray-300 text-gray-400 hover:border-accent">
            <span class="text-2xl">+</span>
            <input type="file" accept="image/*" multiple @change="handleFiles" class="hidden">
        </label>
    </div>
    <input type="hidden" name="photos_json" :value="JSON.stringify(photos)">
</div>
