@if (isset($create))
    @if (isset($create_type))
        @switch($create_type)
            @case('principal')
                <button type="button" class="btn btn-success btn-rounded btn-icon" id="btn_create">
                    <i class="bx bx-plus"></i>
                </button>
                @break

            @case('inverse')
                <button type="button" class="btn btn-inverse-success btn-rounded btn-icon" id="btn_create_inverse">
                    <i class="bx bx-plus"></i>
                </button>
                @break
                
            @case('outline')
                <button type="button" class="btn btn-outline-success btn-rounded btn-icon" id="btn_create_outline">
                    <i class="bx bx-plus"></i>
                </button>
                @break
                
            @default
                <button type="button" class="btn btn-success btn-rounded btn-icon" id="btn_create">
                    <i class="bx bx-plus"></i>
                </button>
                @break

        @endswitch
    @else
        <button type="button" class="btn btn-success btn-rounded btn-icon" id="btn_create">
            <i class="bx bx-plus"></i>
        </button>
    @endif
@endif

@if (isset($edit))
    @if (isset($edit_type))
        @switch($edit_type)
            @case('principal')
                <button type="button" class="btn btn-warning btn-rounded btn-icon" id="btn_edit">
                    <i class="bx bxs-edit-alt"></i>
                </button>
                @break

            @case('inverse')
                <button type="button" class="btn btn-inverse-warning btn-rounded btn-icon" id="btn_edit_inverse">
                    <i class="bx bxs-edit-alt"></i>
                </button>
                @break
                
            @case('outline')
                <button type="button" class="btn btn-outline-warning btn-rounded btn-icon" id="btn_edit_outline">
                    <i class="bx bxs-edit-alt"></i>
                </button>
                @break
                
            @default
                <button type="button" class="btn btn-warning btn-rounded btn-icon" id="btn_edit">
                    <i class="bx bxs-edit-alt"></i>
                </button>
                @break

        @endswitch
    @else
        <button type="button" class="btn btn-warning btn-rounded btn-icon" id="btn_edit">
            <i class="bx bxs-edit-alt"></i>
        </button>
    @endif
@endif

@if (isset($delete))
    @if (isset($delete_type))
        @switch($delete_type)
            @case('principal')
                <button type="button" class="btn btn-danger btn-rounded btn-icon" id="btn_delete">
                    <i class="bx bx-trash"></i>
                </button>
                @break

            @case('inverse')
                <button type="button" class="btn btn-inverse-danger btn-rounded btn-icon" id="btn_delete_inverse">
                    <i class="bx bx-trash"></i>
                </button>
                @break
                
            @case('outline')
                <button type="button" class="btn btn-outline-danger btn-rounded btn-icon" id="btn_delete_outline">
                    <i class="bx bx-trash"></i>
                </button>
                @break
                
            @default
                <button type="button" class="btn btn-danger btn-rounded btn-icon" id="btn_delete">
                    <i class="bx bx-trash"></i>
                </button>
                @break

        @endswitch
    @else
        <button type="button" class="btn btn-danger btn-rounded btn-icon" id="btn_delete">
            <i class="bx bx-trash"></i>
        </button>
    @endif
@endif

@if (isset($show))
    @if (isset($show_type))
        @switch($show_type)
            @case('principal')
                <button type="button" class="btn btn-secondary btn-rounded btn-icon" id="btn_show">
                    <i class="bx bxs-show"></i>
                </button>
                @break

            @case('inverse')
                <button type="button" class="btn btn-inverse-secondary btn-rounded btn-icon" id="btn_show_inverse">
                    <i class="bx bxs-show"></i>
                </button>
                @break
                
            @case('outline')
                <button type="button" class="btn btn-outline-secondary btn-rounded btn-icon" id="btn_show_outline">
                    <i class="bx bxs-show"></i>
                </button>
                @break
                
            @default
                <button type="button" class="btn btn-secondary btn-rounded btn-icon" id="btn_show">
                    <i class="bx bxs-show"></i>
                </button>
                @break

        @endswitch
    @else
        <button type="button" class="btn btn-secondary btn-rounded btn-icon" id="btn_show">
            <i class="bx bxs-show"></i>
        </button>
    @endif
@endif

@if (isset($detail))
    @if (isset($detail_type))
        @switch($detail_type)
            @case('principal')
                <button type="button" class="btn btn-info btn-rounded btn-icon" id="btn_detail">
                    <i class="bx bxs-detail"></i>
                </button>
                @break

            @case('inverse')
                <button type="button" class="btn btn-inverse-info btn-rounded btn-icon" id="btn_detail_inverse">
                    <i class="bx bxs-detail"></i>
                </button>
                @break
                
            @case('outline')
                <button type="button" class="btn btn-outline-info btn-rounded btn-icon" id="btn_detail_outline">
                    <i class="bx bxs-detail"></i>
                </button>
                @break
                
            @default
                <button type="button" class="btn btn-info btn-rounded btn-icon" id="btn_detail">
                    <i class="bx bxs-detail"></i>
                </button>
                @break

        @endswitch
    @else
        <button type="button" class="btn btn-info btn-rounded btn-icon" id="btn_detail">
            <i class="bx bxs-detail"></i>
        </button>
    @endif
@endif