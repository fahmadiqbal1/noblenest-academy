<div class="mb-3">
    <label for="question_text" class="form-label">Question Text <span class="text-danger">*</span></label>
    <textarea name="question_text" id="question_text" class="form-control" required maxlength="500">{{ old('question_text', $question->question_text ?? '') }}</textarea>
    <div class="form-text">You can use up to 500 characters. Supports basic HTML for math or emphasis.</div>
</div>
<div class="mb-3">
    <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
    <select name="type" id="type" class="form-select" required>
        @foreach(['single'=>'Single Choice','multiple'=>'Multiple Choice','short'=>'Short Answer','long'=>'Long Answer'] as $val=>$label)
            <option value="{{ $val }}" @if(old('type', $question->type ?? 'single')==$val) selected @endif>{{ $label }}</option>
        @endforeach
    </select>
    <div class="form-text">Single/Multiple Choice: add options below. Short/Long Answer: user types a response.</div>
</div>
<div class="mb-3">
    <label for="order" class="form-label">Order</label>
    <input type="number" name="order" id="order" class="form-control" min="0" value="{{ old('order', $question->order ?? 0) }}">
</div>
<div id="options-section" class="mb-3" @if(isset($question) && !in_array($question->type ?? 'single', ['single','multiple'])) style="display:none" @endif>
    <label class="form-label">Options <span class="text-danger" id="option-required" style="display:none">*</span></label>
    <div id="options-list">
        @php $opts = old('options', isset($question) ? $question->options->toArray() : [['option_text'=>'','is_correct'=>false]]); @endphp
        @foreach($opts as $i=>$opt)
        <div class="input-group mb-2 option-row" draggable="true">
            <input type="text" name="options[{{ $i }}][option_text]" class="form-control" placeholder="Option text" value="{{ $opt['option_text'] ?? '' }}" required>
            <input type="hidden" name="options[{{ $i }}][id]" value="{{ $opt['id'] ?? '' }}">
            <div class="input-group-text">
                <input type="checkbox" name="options[{{ $i }}][is_correct]" value="1" @if(!empty($opt['is_correct'])) checked @endif> Correct
            </div>
            <button type="button" class="btn btn-outline-danger btn-remove-option" title="Remove option">&times;</button>
            <span class="input-group-text drag-handle" title="Drag to reorder" style="cursor:move">&#x2630;</span>
        </div>
        @endforeach
    </div>
    <button type="button" class="btn btn-outline-secondary" id="add-option">Add Option</button>
    <div class="form-text">Mark at least one correct answer for auto-grading. Drag to reorder options.</div>
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
    row.innerHTML = `<input type="text" name="options[${idx}][option_text]" class="form-control" placeholder="Option text" required>
        <input type="hidden" name="options[${idx}][id]">
        <div class="input-group-text"><input type="checkbox" name="options[${idx}][is_correct]" value="1"> Correct</div>
        <button type="button" class="btn btn-outline-danger btn-remove-option" title="Remove option">&times;</button>
        <span class="input-group-text drag-handle" title="Drag to reorder" style="cursor:move">&#x2630;</span>`;
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
