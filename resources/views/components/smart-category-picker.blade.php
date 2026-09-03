@props(['categories', 'titlePlaceholder' => ''])

@php
    $keywords = [
        'machinery' => ['forklift', 'excavator', 'generator', 'compressor', 'mixer', 'welder', 'bulldozer', 'crane', 'drill rig', 'scaffold'],
        'electronics' => ['laptop', 'computer', 'tv', 'television', 'speaker', 'projector', 'printer', 'tablet', 'phone', 'monitor', 'console', 'drone'],
        'tools' => ['drill', 'saw', 'hammer', 'wrench', 'sander', 'grinder', 'toolkit', 'ladder', 'ratchet', 'screwdriver'],
        'cameras' => ['camera', 'lens', 'tripod', 'gimbal', 'dslr', 'mirrorless', 'camcorder', 'gopro'],
        'garden-equipment' => ['mower', 'lawnmower', 'trimmer', 'hedge', 'chainsaw', 'wheelbarrow', 'rake', 'leaf blower'],
        'event-gear' => ['tent', 'marquee', 'pa system', 'stage', 'lighting rig', 'chairs', 'tables', 'sound system', 'dj'],
        'vehicles' => ['car', 'truck', 'van', 'bakkie', 'trailer', 'motorbike', 'motorcycle', 'bus', 'scooter'],
        'fashion-luxury' => ['dress', 'suit', 'gown', 'jewelry', 'jewellery', 'handbag', 'watch', 'tuxedo', 'heels'],
        'real-estate-spaces' => ['apartment', 'house', 'venue', 'hall', 'office space', 'studio space', 'cottage', 'cabin'],
        'agricultural-equipment' => ['tractor', 'plough', 'plow', 'harvester', 'irrigation', 'cultivator', 'planter', 'silo'],
        'outdoor-recreational' => ['kayak', 'bicycle', 'bike', 'camping tent', 'fishing rod', 'surfboard', 'skis'],
    ];
@endphp

<div x-data="{
    categoryId: '{{ $categories->first()?->id }}',
    categoryTouched: false,
    suggestedName: null,
    keywords: {{ Js::from($keywords) }},
    slugToId: {{ Js::from($categories->pluck('id', 'slug')) }},
    nameById: {{ Js::from($categories->pluck('name', 'id')) }},
    handleTitle(title) {
        const lower = title.toLowerCase();
        let bestSlug = null, bestScore = 0;
        for (const [slug, words] of Object.entries(this.keywords)) {
            const score = words.filter(w => lower.includes(w)).length;
            if (score > bestScore) { bestScore = score; bestSlug = slug; }
        }
        if (bestSlug && this.slugToId[bestSlug]) {
            this.suggestedName = this.nameById[this.slugToId[bestSlug]];
            if (!this.categoryTouched) this.categoryId = this.slugToId[bestSlug];
        } else {
            this.suggestedName = null;
        }
    }
}">
    <label class="block text-sm text-gray-600">
        Title
        <input type="text" name="title" required placeholder="{{ $titlePlaceholder }}" @input="handleTitle($event.target.value)" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 focus:border-accent focus:outline-none">
    </label>

    <label class="mt-4 block text-sm text-gray-600">
        Category
        <select name="category_id" required x-model="categoryId" @change="categoryTouched = true" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 focus:border-accent focus:outline-none">
            @foreach ($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
        <span x-show="suggestedName && !categoryTouched" x-cloak class="mt-1 block text-xs text-accent">Auto-suggested from your title — change it above if that's not right.</span>
    </label>
</div>
