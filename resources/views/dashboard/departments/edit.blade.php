@extends('dashboard.layouts.app')

@section('content')
    @include('components.alert')


    <form action="{{ route('departments.update', $department->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="">
            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-header d-flex justify-content-between align-items-center py-3">
                    <h6 class="mb-0 fw-semibold"><i class="fas fa-user-shield me-2"></i> {{ $department->name }}</h6>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light btn-sm d-flex align-items-center" id="globalSelectAllBtn"
                            data-bs-toggle="tooltip" data-bs-placement="top" title="Select all tabs">
                            <i class="fas fa-check-circle me-1"></i> Select All
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm d-flex align-items-center"
                            id="globalUnselectAllBtn" data-bs-toggle="tooltip" data-bs-placement="top"
                            title="Unselect all tabs">
                            <i class="fas fa-times-circle me-1"></i> Unselect All
                        </button>
                    </div>
                </div>

                <div class="card-body p-3">
                    <div class="mb-3">
                        <label class="form-label fw-bold fs-5">Assign Tabs</label>
                        <div class="accordion" id="tabsAccordion">
                            @foreach ($tabs->where('children', '!=', null)->filter(fn($t) => $t->children->count() > 0) as $parent)
                                <div class="accordion-item section-group border mb-3 rounded-3">
                                    <div class="accordion-header" id="heading-{{ $parent->id }}">
                                        
                                        <div class="d-flex align-items-center gap-2">
                                            {{-- Parent Tab --}}
                                            <input  class="form-check-input tab-checkbox"  type="checkbox"  name="tabs[]"  value="{{ $parent->id }}"  id="tab-{{ $parent->id }}"  aria-label="Select {{ $parent->label }} (Parent)" {{ $department->tabs->contains($parent->id) ? 'checked' : '' }} >
                                            <span class="badge bg-secondary ms-2 rounded-pill">{{ $parent->children->count() + 1 }}</span>
                                            <button class="accordion-button collapsed fw-medium" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $parent->id }}" aria-expanded="false" aria-controls="collapse-{{ $parent->id }}">
                                                {{ $parent->label }}
                                            </button>
                                        </div>
                                    </div>
                                    <div id="collapse-{{ $parent->id }}" class="accordion-collapse collapse" aria-labelledby="heading-{{ $parent->id }}" data-bs-parent="#tabsAccordion">
                                        <div class="accordion-body">
                                            <div class="d-flex justify-content-end mb-3 gap-2">
                                                <button type="button" class="btn btn-light btn-sm d-flex align-items-center section-select-all" data-bs-toggle="tooltip" data-bs-placement="top" title="Select all in this section">
                                                    Select All
                                                </button>
                                                <button type="button" class="btn btn-secondary btn-sm d-flex align-items-center section-unselect-all" data-bs-toggle="tooltip" data-bs-placement="top" title="Unselect all in this section">
                                                    Unselect All
                                                </button>
                                            </div>
                                            <div class="row row-cols-4 row-cols-sm-4 g-3">
                                                
                                                {{-- Children Tabs --}}
                                                @foreach ($parent->children as $child)
                                                    <div class="col">
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input tab-checkbox" 
                                                            type="checkbox" 
                                                            name="tabs[]" 
                                                            value="{{ $child->id }}" 
                                                            id="tab-{{ $child->id }}" 
                                                            aria-label="Select {{ $child->label }}"
                                                            {{ $department->tabs->contains($child->id) ? 'checked' : '' }}
                                                            >
                                                            <label class="form-check-label" for="tab-{{ $child->id }}">
                                                                {{ $child->label }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            @php
                                $noChildrenTabs = $tabs->filter(fn($t) => $t->children->count() == 0);
                            @endphp

                            @if ($noChildrenTabs->count() > 0)
                                <div class="accordion-item section-group border mb-3 rounded-3">
                                    <div class="accordion-header" id="heading-no-children">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-secondary ms-2 rounded-pill">{{ $noChildrenTabs->count() }}</span>
                                            <button class="accordion-button collapsed fw-medium" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-no-children" aria-expanded="false" aria-controls="collapse-no-children">
                                                Other Tabs
                                            </button>
                                        </div>
                                    </div>
                                    <div id="collapse-no-children" class="accordion-collapse collapse" aria-labelledby="heading-no-children" data-bs-parent="#tabsAccordion">
                                        <div class="accordion-body">
                                            <div class="d-flex justify-content-end mb-3 gap-2">
                                                <button type="button" class="btn btn-light btn-sm d-flex align-items-center section-select-all" data-bs-toggle="tooltip" data-bs-placement="top" title="Select all in this section">
                                                    Select All
                                                </button>
                                                <button type="button" class="btn btn-secondary btn-sm d-flex align-items-center section-unselect-all" data-bs-toggle="tooltip" data-bs-placement="top" title="Unselect all in this section">
                                                    Unselect All
                                                </button>
                                            </div>
                                            <div class="row row-cols-4 row-cols-sm-4 g-3">
                                                @foreach ($noChildrenTabs as $tab)
                                                    <div class="col">
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input tab-checkbox" 
                                                            type="checkbox" 
                                                            name="tabs[]" 
                                                            value="{{ $tab->id }}" 
                                                            id="tab-{{ $tab->id }}" 
                                                            aria-label="Select {{ $tab->label }}"
                                                            {{ $department->tabs->contains($tab->id) ? 'checked' : '' }}
                                                            >
                                                            <label class="form-check-label fw-semibold" for="tab-{{ $tab->id }}">
                                                                {{ $tab->label }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-end mt-3">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-1"></i> Save Changes
        </button>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(
                tooltipTriggerEl));

            $("#globalSelectAllBtn").on("click", function() {
                $(".tab-checkbox").prop("checked", true).trigger("change");
                $(this).addClass("animate__animated animate__pulse").one("animationend", function() {
                    $(this).removeClass("animate__animated animate__pulse");
                });
            });

            $("#globalUnselectAllBtn").on("click", function() {
                $(".tab-checkbox").prop("checked", false).trigger("change");
                $(this).addClass("animate__animated animate__pulse").one("animationend", function() {
                    $(this).removeClass("animate__animated animate__pulse");
                });
            });

            $(".section-select-all").on("click", function() {
                $(this).closest(".section-group").find(".tab-checkbox").prop("checked", true).trigger(
                    "change");
                $(this).addClass("animate__animated animate__pulse").one("animationend", function() {
                    $(this).removeClass("animate__animated animate__pulse");
                });
            });

            $(".section-unselect-all").on("click", function() {
                $(this).closest(".section-group").find(".tab-checkbox").prop("checked", false).trigger(
                    "change");
                $(this).addClass("animate__animated animate__pulse").one("animationend", function() {
                    $(this).removeClass("animate__animated animate__pulse");
                });
            });

            $(".tab-checkbox").on("change", function() {
                const label = $(this).next("label");
                if ($(this).is(":checked")) {
                    label.addClass("text-primary fw-bold").removeClass("text-muted");
                } else {
                    label.addClass("text-muted").removeClass("text-primary fw-bold");
                }
            });
        });
    </script>
@endpush
