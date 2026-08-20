@extends('admin_layout.master')
<style>
        /* .dropdown-wrap {
            position: relative !important;
        } */

        .dropdown-menu-custom {
            position: fixed !important;
            z-index: 99999 !important;
            width:10%;
        }

    /* 17 march */
    @media screen and (max-width:767px) {
        .table-card.new-inner-table {
            overflow-x: auto;
        }

        .new-inner-table .data-table th,
        .new-inner-table .data-table td {
            white-space: nowrap;
        }
    }
</style>

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/parties_section/index.css') }}">

    <div class="parties-page">
        <div class="page-header">
            <div>
                <h2>Parties Templates</h2>
                <p>Manage party templates for contract generation</p>
            </div>
            <a href="{{ route('admin.parties-templates.create') }}" class="btn-add">
                + Add New Template
            </a>
        </div>

        {{-- Toast Popups --}}
        {{-- @if(session('success'))
        <div id="successPopup" style="position:fixed;top:30px;right:30px;z-index:99999;
                         background:#fff;border-radius:12px;padding:18px 28px;
                         box-shadow:0 4px 24px rgba(0,0,0,0.13);border-left:5px solid #28a745;
                         display:flex;align-items:center;gap:12px;min-width:280px;">
            <i class="fas fa-check-circle" style="color:#28a745;font-size:20px;"></i>
            <span style="font-size:14px;color:#333;font-weight:500;">{{ session('success') }}</span>
            <button onclick="document.getElementById('successPopup').style.display='none'"
                style="margin-left:auto;background:none;border:none;font-size:18px;cursor:pointer;color:#999;">&times;</button>
        </div>
        @endif --}}

        @if(session('error'))
            <div id="errorPopup" style="position:fixed;top:30px;right:30px;z-index:99999;
                                     background:#fff;border-radius:12px;padding:18px 28px;
                                     box-shadow:0 4px 24px rgba(0,0,0,0.13);border-left:5px solid #dc3545;
                                     display:flex;align-items:center;gap:12px;min-width:280px;">
                <i class="fas fa-times-circle" style="color:#dc3545;font-size:20px;"></i>
                <span style="font-size:14px;color:#333;font-weight:500;">{{ session('error') }}</span>
                <button onclick="document.getElementById('errorPopup').style.display='none'"
                    style="margin-left:auto;background:none;border:none;font-size:18px;cursor:pointer;color:#999;">&times;</button>
            </div>
        @endif

        <div class="table-card new-inner-table">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Side A</th>
                        <th>Side B</th>
                        <th>Party Section Text</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($templates as $template)
                        <tr>
                            <td class="td-id">{{ $loop->iteration }}</td>
                            <td class="td-name">{{ $template->name }}</td>
                            <td>{{ $template->parties_type }}</td>
                            <td>{{ $template->party_a_count }}</td>
                            <td>{{ $template->party_b_count }}</td>
                            <td class="td-desc">
                                {{-- @if(!empty($template->parties_section_text) && trim($template->parties_section_text) !== '')
                                    Yes
                                @else
                                    No
                                @endif --}}
                                @if(!empty($template->parties_section_text))
    Yes
@else
    No
@endif
                            </td>
                            <td>
                                @if($template->is_active)
                                    <span class="badge-active">Active</span>
                                @else
                                    <span class="badge-inactive">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown-wrap" id="dd-{{ $template->id }}">
                                    <button class="btn-dots" onclick="toggleDropdown('dd-{{ $template->id }}')" title="Actions">
                                        &#8942;
                                    </button>
                                    <div class="dropdown-menu-custom">
                                        <a href="{{ route('admin.parties_templates.edit', $template) }}">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                            </svg>
                                            Edit
                                        </a>
                                        <div class="dropdown-divider"></div>

                                        {{-- Hidden delete form - no onsubmit confirm --}}
                                        <form action="{{ route('admin.parties-templates.destroy', $template) }}" method="POST"
                                            id="deleteForm-{{ $template->id }}">
                                            @csrf
                                            @method('DELETE')
                                        </form>

                                        <button type="button" class="action-delete"
                                            onclick="openDeleteModal('deleteForm-{{ $template->id }}')">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <polyline points="3 6 5 6 21 6" />
                                                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
                                                <path d="M10 11v6M14 11v6" />
                                                <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" />
                                            </svg>
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">No templates yet. Create one to get started.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrap">
            {{ $templates->links() }}
        </div>

        {{-- Custom Delete Confirmation Modal --}}
        <div id="deleteModal" style="display:none; position:fixed; inset:0; z-index:99998;
                     background:rgba(0,0,0,0.45); align-items:center; justify-content:center;">
            <div style="background:#fff; border-radius:0.25em; padding:36px 32px; max-width:400px;
                                width:90%; box-shadow:0 8px 40px rgba(0,0,0,0.18); text-align:center;">
                <div style="width:56px;height:56px;background:#fff0f0;border-radius:50%;
                                    display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <svg width="26" height="26" fill="none" stroke="#dc3545" stroke-width="2" viewBox="0 0 24 24">
                        <polyline points="3 6 5 6 21 6" />
                        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
                        <path d="M10 11v6M14 11v6" />
                        <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" />
                    </svg>
                </div>
                <h5 style="font-weight:700;font-size:18px;margin-bottom:8px;color:#364a63;">Delete Template?</h5>
                <p style="color:#666;font-size:14px;margin-bottom:28px;">
                    This action cannot be undone. The template will be permanently removed.
                </p>
                <div style="display:flex;gap:12px;justify-content:center;">
                    <button onclick="closeDeleteModal()" style="padding:10px 28px;border-radius:0.25em;border:1.5px solid #ddd;
                                           background:#012555;color:#fff;font-weight:600;font-size:14px;cursor:pointer;">
                        Cancel
                    </button>
                    <button onclick="confirmDelete()" style="padding:10px 28px;border-radius:0.25em;border:none;
                                           background:#FD5602;color:#fff;font-weight:600;font-size:14px;cursor:pointer;">
                        Delete
                    </button>
                </div>
            </div>
        </div>

    </div>

    <script>
        //  Dropdown toggle 
        // function toggleDropdown(id) {
        //     const el = document.getElementById(id);
        //     const isOpen = el.classList.contains('open');
        //     document.querySelectorAll('.dropdown-wrap.open').forEach(d => d.classList.remove('open'));
        //     if (!isOpen) el.classList.add('open');
        // }

        function toggleDropdown(id) {
            const el = document.getElementById(id);
            const isOpen = el.classList.contains('open');
            document.querySelectorAll('.dropdown-wrap.open').forEach(d => d.classList.remove('open'));
            if (!isOpen) {
                el.classList.add('open');
                const btn = el.querySelector('.btn-dots');
                const menu = el.querySelector('.dropdown-menu-custom');
                const rect = btn.getBoundingClientRect();
                menu.style.top = (rect.bottom + 4) + 'px';
                menu.style.left = (rect.right - menu.offsetWidth || rect.left) + 'px';
            }
        }

        // Close dropdown on outside click
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.dropdown-wrap')) {
                document.querySelectorAll('.dropdown-wrap.open').forEach(d => d.classList.remove('open'));
            }
        });

        // ── Delete Modal ──────────────────────────────────────────────────────
        var _deleteFormId = null;

        function openDeleteModal(formId) {
            _deleteFormId = formId;
            var modal = document.getElementById('deleteModal');
            modal.style.display = 'flex';
            // close any open dropdown
            document.querySelectorAll('.dropdown-wrap.open').forEach(function (d) {
                d.classList.remove('open');
            });
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
            _deleteFormId = null;
        }

        function confirmDelete() {
            if (_deleteFormId) {
                document.getElementById(_deleteFormId).submit();
            }
        }

        // Close modal on backdrop click
        // document.getElementById('deleteModal').addEventListener('click', function(e) {
        //     if (e.target === this) closeDeleteModal();
        // });

        //  Toast auto-dismiss after 4 seconds
        // ['successPopup', 'errorPopup'].forEach(function(id) {
        //     var el = document.getElementById(id);
        //     if (el) setTimeout(function() { el.style.display = 'none'; }, 4000);
        // });
    </script>

@endsection