@props(['name', 'label'])

<div x-data="{ preview: null }">
    <label class="block text-sm text-gray-600">{{ $label }}</label>
    <label class="mt-1 flex h-32 w-full cursor-pointer items-center justify-center rounded border-2 border-dashed border-gray-300 bg-gray-50 text-gray-400 hover:border-accent">
        <template x-if="preview"><img :src="preview" alt="" class="h-full w-full rounded object-contain p-2"></template>
        <template x-if="!preview"><span class="text-sm">Tap to upload</span></template>
        <input type="file" accept="image/*" class="hidden" @change="
            const file = $event.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = () => preview = reader.result;
            reader.readAsDataURL(file);
        ">
    </label>
    <input type="hidden" name="{{ $name }}" :value="preview">
</div>
