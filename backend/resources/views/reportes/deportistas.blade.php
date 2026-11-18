<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte de deportistas</title>
    <style>
        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 12px;
            color: #1f2937;
        }

        h1 {
            font-size: 18px;
            margin-bottom: 4px;
        }

        .meta {
            color: #6b7280;
            margin-bottom: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #d1d5db;
            padding: 6px 8px;
            text-align: left;
        }

        th {
            background-color: #e5e7eb;
        }

        tr:nth-child(even) td {
            background-color: #f9fafb;
        }
    </style>
</head>
<body>
    <h1>Reporte de deportistas</h1>
    <p class="meta">Generado el {{ now()->format('d/m/Y H:i') }} · Total: {{ count($deportistas) }}</p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nombre completo</th>
                <th>Fecha de nacimiento</th>
                <th>Modalidad</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($deportistas as $deportista)
                <tr>
                    <td>{{ $deportista->id }}</td>
                    <td>{{ $deportista->nombreCompleto() }}</td>
                    <td>{{ $deportista->fechaNacimiento->format('d/m/Y') }}</td>
                    <td>{{ $deportista->modalidad?->nombre ?? 'Sin modalidad' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No hay deportistas registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
