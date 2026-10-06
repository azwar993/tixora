@extends('layouts.admin', [
    'title' => 'User Management',
    'activeMenu' => 'users'
])

@section('content')

<section class="admin-section active">

    <div class="section-top">

        <div>
            <span class="topbar-label">
                MANAGEMENT
            </span>

            <h2>
                User Management
            </h2>

            <p>
                Monitor pengguna yang terdaftar di TIXORA.
            </p>
        </div>

    </div>

    @if (session('success'))
        <div class="admin-alert success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="admin-alert error">{{ session('error') }}</div>
    @endif

    @if ($errors->has('role'))
        <div class="admin-alert error">{{ $errors->first('role') }}</div>
    @endif

    <div class="panel">

        <div class="table-wrapper">

            <table>

                <thead>
                    <tr>
                        <th>NO</th>
                        <th>NAME</th>
                        <th>EMAIL</th>
                        <th>ROLE</th>
                        <th>TOTAL ORDER</th>
                        <th>REGISTERED</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($users as $user)

                        <tr>

                            <td>
                                {{ $loop->iteration }}
                            </td>

                            <td>
                                <strong>
                                    {{ $user->name }}
                                </strong>
                            </td>

                            <td>
                                {{ $user->email }}
                            </td>

                            <td>

                                @if ($user->role === 'admin')
                                    <span class="status-pill purple">Admin</span>
                                @elseif (in_array($user->role, ['user', 'eo'], true))
                                    <form method="POST" action="{{ route('admin.users.role', $user) }}" style="display:flex; align-items:center; gap:8px;">
                                        @csrf
                                        @method('PATCH')

                                        <select name="role" aria-label="Role {{ $user->name }}">
                                            <option value="user" @selected($user->role === 'user')>User</option>
                                            <option value="eo" @selected($user->role === 'eo')>EO</option>
                                        </select>

                                        @if ((int) auth()->id() !== (int) $user->getKey())
                                            <button type="submit" class="secondary-button">Simpan</button>
                                        @endif
                                    </form>
                                @else
                                    <span class="status-pill">{{ strtoupper($user->role) }}</span>
                                @endif

                            </td>

                            <td>
                                {{ $user->orders_count }}
                            </td>

                            <td>
                                {{ $user->created_at?->format('d M Y') }}
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="6"
                                style="text-align:center;"
                            >
                                Belum ada user.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</section>

@endsection
