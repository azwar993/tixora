@extends('layouts.eo', ['title' => 'Aktivitas'])

@section('content')
<div class="eo-page eo-activity-page">
    <section class="eo-events-intro">
        <div>
            <span class="eo-eyebrow">TIXORA CREATOR</span>
            <h2>Aktivitas</h2>
            <p>Aktivitas terbaru dari event, ticket type, dan transaksi milikmu.</p>
        </div>
    </section>

    @if ($activities->isEmpty())
        <section class="eo-panel">
            <div class="eo-empty-state">
                <span class="eo-empty-icon"><i class="fa-solid fa-bolt"></i></span>
                <h3>Belum ada aktivitas</h3>
                <p>Aktivitas event dan transaksi milikmu akan muncul di sini.</p>
                <a class="eo-button eo-button-primary" href="{{ route('eo.events.create') }}">
                    <i class="fa-solid fa-plus"></i><span>Buat Event</span>
                </a>
            </div>
        </section>
    @else
        <section class="eo-panel eo-events-panel" aria-label="Aktivitas terbaru">
            <div class="eo-event-list">
                @foreach ($activities as $activity)
                    <article class="eo-event-card">
                        <div class="eo-event-content">
                            <div class="eo-event-heading">
                                <div class="eo-event-title-group">
                                    <h3>{{ $activity['type'] }}</h3>
                                </div>
                            </div>
                            <div class="eo-event-meta">
                                <span>{{ $activity['detail'] }}</span>
                                <span><i class="fa-regular fa-clock"></i>
                                    <time datetime="{{ $activity['created_at']->toIso8601String() }}">{{ $activity['created_at']->format('d M Y H:i') }}</time>
                                </span>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
