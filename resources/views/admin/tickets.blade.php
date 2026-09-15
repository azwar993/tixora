<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Ticket Management | TIXORA</title>

    @vite([
        'resources/css/admin.css',
        'resources/js/admin.js'
    ])
</head>

<body>

    <main class="main-content">

        <section class="admin-section active">

            <div class="section-top">

                <div>
                    <span class="topbar-label">
                        TRANSACTION
                    </span>

                    <h2>
                        Ticket Management
                    </h2>

                    <p>
                        Kelola tiket untuk setiap event TIXORA.
                    </p>
                </div>

            </div>

            <div class="panel">

                <div class="table-wrapper">

                    <table>

                        <thead>
                            <tr>
                                <th>TICKET</th>
                                <th>EVENT</th>
                                <th>PRICE</th>
                                <th>QUOTA</th>
                                <th>SOLD</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($tickets as $ticket)

                                <tr>

                                    <td>
                                        <strong>
                                            {{ $ticket->name }}
                                        </strong>
                                    </td>

                                    <td>
                                        {{ $ticket->event->name }}
                                    </td>

                                    <td>
                                        Rp{{ number_format($ticket->price, 0, ',', '.') }}
                                    </td>

                                    <td>
                                        {{ $ticket->quota }}
                                    </td>

                                    <td>
                                        {{ $ticket->sold }}
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="5" style="text-align: center;">
                                        Belum ada ticket.
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </section>

    </main>

</body>
</html>