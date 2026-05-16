<div class="mb-3">
    <label for="question_text" class="block text-sm font-medium text-gray-700 mb-1">Question Text <span class="text-red-600">*</span></label>
    <textarea name="question_text" id="question_text" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" required maxlength="500">{{ old('question_text', $question->question_text ?? '') }}</textarea>
    <div class="mt-1 text-sm text-[var(--color-text-muted)]">You can use up to 500 characters. Supports basic HTML for math or emphasis.</div>
</div>
<div class="mb-3">
    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type <span class="text-red-600">*</span></label>
    <select name="type" id="type" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" required>
        @foreach(['single'=>'Single Choice','multiple'=>'Multiple Choice','short'=>'Short Answer','long'=>'Long Answer'] as $val=>$label)
            <option value="{{ $val }}" @if(old('type', $question->type ?? 'single')==$val) selected @endif>{{ $label }}</option>
        @endforeach
    </select>
    <div class="mt-1 text-sm text-[var(--color-text-muted)]">Single/Multiple Choice: add options below. Short/Long Answer: user types a response.</div>
</div>
<div class="mb-3">
    <label for="order" class="block text-sm font-medium text-gray-700 mb-1">Order</label>
    <input type="number" name="order" id="order" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" min="0" value="{{ old('order', $question->order ?? 0) }}">
</div>
<div id="options-section" class="mb-3" @if(isset($question) && !in_array($question->type ?? 'single', ['single','multiple'])) style="display:none" @endif>
    <label class="block text-sm font-medium text-gray-700 mb-1">Options <span class="text-red-600" id="option-required" style="display:none">*</span></label>
    <div id="options-list">
        @php $opts = old('options', isset($question) ? $question->options->toArray() : [['option_text'=>'','is_correct'=>false]]); @endphp
        @foreach($opts as $i=>$opt)
        <div class="flex w-full items-stretch mb-2 option-row" draggable="true">
            <input type="text" name="options[{{ $i }}][option_text]" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" placeholder="Option text" value="{{ $opt['option_text'] ?? '' }}" required>
            <input type="hidden" name="options[{{ $i }}][id]" value="{{ $opt['id'] ?? '' }}">
            <div class="inline-flex items-center px-3 bg-gray-50 border border-gray-300">
                <input type="checkbox" name="options[{{ $i }}][is_correct]" value="1" @if(!empty($opt['is_correct'])) checked @endif> Correct
            </div>
            <button type="button" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-red-600 text-red-600 hover:bg-red-600 hover:text-white btn-remove-option" title="Remove option">&times;</button>
            <span class="inline-flex items-center px-3 bg-gray-50 border border-gray-300 drag-handle" title="Drag to reorder" style="cursor:move">&#x2630;</span>
        </div>
        @endforeach
    </div>
    <button type="button" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-gray-300 text-gray-700 hover:bg-gray-100" id="add-option">Add Option</button>
    <div class="mt-1 text-sm text-[var(--color-text-muted)]">Mark at least one correct answer for auto-grading. Drag to reorder options.</div>
</div>
@section('scripts')
<script>
function updateOptionsVisibility() {
    const type = document.getElementById('type').value;
    const section = document.getElementById('options-section');
    section.style.display = (type==='single'||type==='multiple') ? '' : 'none';
    document.getElementById('option-required').style.display = (type==='single'||type==='multiple') ? '' : 'none';
}
document.getElementById('type').onchange = updateOptionsVisibility;
document.getElementById('add-option').onclick = function() {
    const idx = document.querySelectorAll('.option-row').length;
    const row = document.createElement('div');
    row.className = 'input-group mb-2 option-row';
    row.innerHTML = `<input type="text" name="options[${idx}][option_text]" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" placeholder="Option text" required>
        <input type="hidden" name="options[${idx}][id]">
        <div class="inline-flex items-center px-3 bg-gray-50 border border-gray-300"><input type="checkbox" name="options[${idx}][is_correct]" value="1"> Correct</div>
        <button type="button" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-red-600 text-red-600 hover:bg-red-600 hover:text-white btn-remove-option" title="Remove option">&times;</button>
        <span class="inline-flex items-center px-3 bg-gray-50 border border-gray-300 drag-handle" title="Drag to reorder" style="cursor:move">&#x2630;</span>`;
    document.getElementById('options-list').appendChild(row);
    row.querySelector('.btn-remove-option').onclick = function() { row.remove(); };
};
document.querySelectorAll('.btn-remove-option').forEach(btn => btn.onclick = function() { btn.closest('.option-row').remove(); });
updateOptionsVisibility();
// Enhanced drag-and-drop for option reordering
let dragSrc = null;
document.querySelectorAll('.option-row').forEach(row => {
    row.draggable = true;
    row.ondragstart = e => { dragSrc = row; row.style.opacity = '0.5'; };
    row.ondragend = e => { dragSrc = null; row.style.opacity = '1'; };
    row.ondragover = e => e.preventDefault();
    row.ondrop = e => {
        e.preventDefault();
        if (dragSrc && dragSrc !== row) {
            row.parentNode.insertBefore(dragSrc, row.nextSibling);
        }
    };
});
</script>
@endsection
